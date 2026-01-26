<?php

namespace App\Services;

use App\Models\CartStorage;
use App\Models\ProductSalepage;
use App\Models\Promotion;
use App\Models\PromotionRule;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CartService
{
    /**
     * ดึงข้อมูลทั้งหมดสำหรับแสดงผลหน้าตะกร้า (Cart View)
     */
    public function getCartDataForView(): array
    {
        $items = $this->getCartContents();
        $total = $this->getTotal();

        // ดึงข้อมูลสินค้า (Eager Load) เพื่อป้องกัน N+1 Query
        $productIds = $items->pluck('id')->toArray();
        $products = ProductSalepage::with('images')
            ->whereIn('pd_sp_id', $productIds)
            ->get()
            ->keyBy('pd_sp_id');

        // คำนวณโปรโมชั่นที่ใช้ได้
        $applicablePromotions = $this->getApplicablePromotions($items);

        // คำนวณโควต้าของแถมทั้งหมด
        $freebieLimit = $this->calculateFreebieLimit($items);

        // รวบรวมรายชื่อของแถมที่ได้รับสิทธิ์
        $giftableProducts = $applicablePromotions->flatMap(function ($promo) {
            return $promo->actions->flatMap(function ($action) {
                $gifts = collect();

                // กรณีระบุของแถมเจาะจง
                if ($action->productToGet) {
                    $gifts->push($action->productToGet);
                }

                // กรณีเลือกจากกลุ่มสินค้า (Pool)
                if ($action->giftableProducts->isNotEmpty()) {
                    $gifts = $gifts->merge($action->giftableProducts);
                }

                return $gifts;
            });
        })->unique('pd_sp_id');

        return compact('items', 'total', 'products', 'applicablePromotions', 'giftableProducts', 'freebieLimit');
    }

    /**
     * ดึงโปรโมชั่นที่เกี่ยวข้องกับสินค้าชิ้นเดียว (สำหรับหน้า Product Detail)
     */
    public function getPromotionsForProduct(int $productId): Collection
    {
        $now = now();

        $promotionIds = PromotionRule::where(function ($q) use ($productId) {
            $q->whereJsonContains('rules->product_id', (string) $productId)
                ->orWhereJsonContains('rules->product_id', (int) $productId);
        })
            ->pluck('promotion_id')
            ->unique();

        if ($promotionIds->isEmpty()) {
            return collect();
        }

        return Promotion::with(['rules', 'actions.giftableProducts'])
            ->whereIn('id', $promotionIds)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', $now))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $now))
            ->get();
    }

    /**
     * คำนวณโควต้าของแถมทั้งหมดที่สามารถเลือกได้
     */
    public function calculateFreebieLimit(?Collection $cartItems = null): int
    {
        $items = $cartItems ?? $this->getCartContents();
        $applicablePromotions = $this->getApplicablePromotions($items);

        if ($applicablePromotions->isEmpty()) {
            return 0;
        }

        $limit = $applicablePromotions->sum(function ($promo) {
            return $promo->actions->sum(function ($action) {
                return (int) ($action->actions['quantity_to_get'] ?? 0);
            });
        });

        return $limit;
    }

    public function getUserId(): string|int
    {
        return Auth::check() ? Auth::id() : '_guest_'.session()->getId();
    }

    public function getCartContents(): Collection
    {
        $userId = $this->getUserId();

        if (Auth::check()) {
            $this->restoreCartFromDatabase($userId);
        }

        return Cart::session($userId)->getContent()->sort();
    }

    public function getTotal(): float
    {
        return Cart::session($this->getUserId())->getTotal();
    }

    public function getTotalQuantity(): int
    {
        return Cart::session($this->getUserId())->getTotalQuantity();
    }

    private function getProductDetails(int $productId): ?object
    {
        $product = ProductSalepage::with('images')->find($productId);
        if (! $product) {
            return null;
        }

        $originalPrice = (float) $product->pd_sp_price;
        $discountAmount = (float) $product->pd_sp_discount;
        $finalSellingPrice = max(0, $originalPrice - $discountAmount);

        $img = $product->images->first();
        $imgPath = $img ? ($img->img_path ?? $img->image_path) : null;
        if ($imgPath && ! filter_var($imgPath, FILTER_VALIDATE_URL)) {
            $imgPath = asset('storage/'.ltrim(str_replace('storage/', '', $imgPath), '/'));
        }

        return (object) [
            'id' => $product->pd_sp_id,
            'name' => $product->pd_sp_name,
            'price' => $finalSellingPrice,
            'original_price' => $originalPrice,
            'discount' => $discountAmount,
            'image' => $imgPath,
            'pd_code' => $product->pd_code,
            'stock' => $product->pd_sp_stock ?? 0,
        ];
    }

    public function addOrUpdate(int $productId, int $quantity): void
    {
        $product = ProductSalepage::find($productId);
        if (! $product) {
            return;
        }

        if ($product->pd_sp_stock <= 0) {
            throw new \Exception('ขออภัย สินค้าหมดสต็อก');
        }

        $userId = $this->getUserId();
        $cart = Cart::session($userId);
        $currentQuantity = $cart->has($productId) ? $cart->get($productId)->quantity : 0;

        if (($currentQuantity + $quantity) > $product->pd_sp_stock) {
            throw new \Exception("สินค้ามีไม่เพียงพอ (เหลือ {$product->pd_sp_stock} ชิ้น)");
        }

        $details = $this->getProductDetails($productId);
        if (! $details) {
            return;
        }

        $cart->add([
            'id' => $details->id,
            'name' => $details->name,
            'price' => $details->price,
            'quantity' => $quantity,
            'attributes' => [
                'image' => $details->image,
                'original_price' => $details->original_price,
                'discount' => $details->discount,
                'pd_code' => $details->pd_code,
            ],
            'associatedModel' => $product,
        ]);

        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    /**
     * เพิ่มสินค้าพร้อมของแถม
     */
    public function addWithGifts(int $productId, int $quantity, array $giftIds): void
    {
        $product = ProductSalepage::find($productId);
        if (! $product) {
            throw new \Exception('ไม่พบข้อมูลสินค้า');
        }

        $userId = $this->getUserId();
        $cart = Cart::session($userId);

        // 1. เช็คสต็อกสินค้าหลัก
        $currentProductQty = $cart->has($productId) ? $cart->get($productId)->quantity : 0;
        if (($currentProductQty + $quantity) > $product->pd_sp_stock) {
            throw new \Exception("สินค้า '{$product->pd_sp_name}' มีในสต็อกไม่เพียงพอ");
        }

        // 2. เช็คสต็อกของแถมทั้งหมด
        $giftCounts = array_count_values($giftIds);
        foreach ($giftCounts as $giftId => $count) {
            $giftProduct = ProductSalepage::find($giftId);
            if (! $giftProduct) {
                throw new \Exception("ไม่พบข้อมูลของแถม ID: {$giftId}");
            }
            $currentGiftQty = $cart->has($giftId) ? $cart->get($giftId)->quantity : 0;
            if (($currentGiftQty + $count) > $giftProduct->pd_sp_stock) {
                throw new \Exception("ของแถม '{$giftProduct->pd_sp_name}' มีในสต็อกไม่เพียงพอ");
            }
        }

        // 3. เพิ่มสินค้าหลัก
        $this->addOrUpdate($productId, $quantity);

        // 4. เพิ่มของแถม
        $this->addFreebies($giftIds);
    }

    public function updateQuantity(int $productId, string $action): void
    {
        $userId = $this->getUserId();
        $cart = Cart::session($userId);
        $qty = ($action === 'increase') ? 1 : -1;

        $cart->update($productId, ['quantity' => $qty]);

        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    public function removeItem(int $productId): void
    {
        $userId = $this->getUserId();
        Cart::session($userId)->remove($productId);

        if (Auth::check()) {
            $this->saveCartToDatabase($userId, Cart::session($userId)->getContent());
        }
    }

    public function addFreebies(array $freebieIds): void
    {
        $userId = $this->getUserId();
        if (Auth::check()) {
            $this->restoreCartFromDatabase($userId);
        }

        $cart = Cart::session($userId);

        foreach ($freebieIds as $freebieId) {
            $product = ProductSalepage::find($freebieId);
            if (! $product || $product->pd_sp_stock <= 0) {
                continue;
            }

            $details = $this->getProductDetails($freebieId);
            if ($details) {
                $cart->add([
                    'id' => $details->id,
                    'name' => $details->name.' (ของแถม)',
                    'price' => 0,
                    'quantity' => 1,
                    'attributes' => [
                        'image' => $details->image,
                        'original_price' => $details->original_price,
                        'discount' => $details->original_price,
                        'pd_code' => $details->pd_code,
                        'is_freebie' => true,
                    ],
                ]);
            }
        }

        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    // Logic ตรวจสอบโปรโมชั่นที่เข้าเงื่อนไข (รองรับ AND / OR)
    private function getApplicablePromotions(Collection $cartItems): Collection
    {
        if ($cartItems->isEmpty()) {
            return collect();
        }

        $now = now();
        $cartProductIds = $cartItems->pluck('id')->toArray();
        $cartQuantities = $cartItems->pluck('quantity', 'id');

        // หา ID โปรโมชั่นที่มีกฎเกี่ยวข้อง
        $potentialPromotionIds = PromotionRule::where(function ($q) use ($cartProductIds) {
            foreach ($cartProductIds as $id) {
                $q->orWhereJsonContains('rules->product_id', (string) $id)
                    ->orWhereJsonContains('rules->product_id', (int) $id);
            }
        })->pluck('promotion_id')->unique();

        if ($potentialPromotionIds->isEmpty()) {
            return collect();
        }

        $potentialPromotions = Promotion::with(['rules', 'actions.giftableProducts', 'actions.productToGet'])
            ->whereIn('id', $potentialPromotionIds)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', $now))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $now))
            ->get();

        // ★★★ จุดที่แก้ไข Logic ★★★
        return $potentialPromotions->filter(function ($promo) use ($cartQuantities) {
            if ($promo->rules->isEmpty()) {
                return false;
            }

            // อ่านค่า condition_type (ค่า default เป็น 'any' ถ้าไม่มี)
            $conditionType = $promo->condition_type ?? 'any';
            $totalRules = $promo->rules->count();
            $rulesMetCount = 0;

            foreach ($promo->rules as $rule) {
                $pids = $rule->rules['product_id'] ?? [];
                if (! is_array($pids)) {
                    $pids = [$pids];
                }
                $reqQty = $rule->rules['quantity_to_buy'] ?? 1;

                $isRuleMet = false;
                foreach ($pids as $pid) {
                    if ($cartQuantities->has((int) $pid) && $cartQuantities->get((int) $pid) >= $reqQty) {
                        $isRuleMet = true;
                        break;
                    }
                }

                // ถ้านับข้อที่ผ่าน
                if ($isRuleMet) {
                    $rulesMetCount++;
                }
            }

            // ตัดสินใจตามเงื่อนไข
            if ($conditionType === 'all') {
                // แบบ AND: จำนวนข้อที่ผ่าน ต้องเท่ากับ จำนวนกฎทั้งหมด
                return $rulesMetCount === $totalRules;
            } else {
                // แบบ OR: ผ่านอย่างน้อย 1 ข้อ ก็ได้สิทธิ์เลย
                return $rulesMetCount > 0;
            }
        });
    }

    private function saveCartToDatabase(int $userId, $cartContent): void
    {
        CartStorage::updateOrCreate(['user_id' => $userId], ['cart_data' => $cartContent]);
    }

    private function restoreCartFromDatabase(int $userId): void
    {
        $savedCart = CartStorage::where('user_id', $userId)->first();
        $userCart = Cart::session($userId);
        $userCart->clear();

        if ($savedCart && ! empty($savedCart->cart_data)) {
            $data = $savedCart->cart_data;
            if (is_string($data)) {
                $data = json_decode($data, true);
            }
            if (is_array($data)) {
                $userCart->add($data);
            }
        }
    }
}
