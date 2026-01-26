<?php

namespace App\Services;

use App\Models\CartStorage;
use App\Models\ProductSalepage;
use App\Models\Promotion;
use App\Models\PromotionRule;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
                if ($action->productToGet) {
                    $gifts->push($action->productToGet);
                }
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
        })->pluck('promotion_id')->unique();

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
        if (! $product || $product->pd_sp_stock <= 0) {
            return;
        }

        $userId = $this->getUserId();
        $cart = Cart::session($userId);
        $currentQuantity = $cart->has($productId) ? $cart->get($productId)->quantity : 0;

        if (($currentQuantity + $quantity) > $product->pd_sp_stock) {
            throw new \Exception("สินค้ามีไม่เพียงพอ (เหลือ {$product->pd_sp_stock} ชิ้น)");
        }

        $details = $this->getProductDetails($productId);
        if ($details) {
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
    }

    public function addWithGifts(int $productId, int $quantity, array $giftIds): void
    {
        $product = ProductSalepage::find($productId);
        if (! $product) {
            throw new \Exception('ไม่พบข้อมูลสินค้า');
        }

        $userId = $this->getUserId();
        $cart = Cart::session($userId);

        // 1. Check stock for main product
        $currentProductQty = $cart->has($productId) ? $cart->get($productId)->quantity : 0;
        if (($currentProductQty + $quantity) > $product->pd_sp_stock) {
            throw new \Exception("สินค้า '{$product->pd_sp_name}' มีในสต็อกไม่เพียงพอ");
        }

        // 2. Check stock for all selected gifts
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

        // 3. Add items with a group ID
        $promoGroupId = 'promo_'.Str::uuid();

        // Add main product
        $productDetails = $this->getProductDetails($productId);
        if ($productDetails) {
            $cart->add([
                'id' => $productDetails->id,
                'name' => $productDetails->name,
                'price' => $productDetails->price,
                'quantity' => $quantity,
                'attributes' => [
                    'image' => $productDetails->image,
                    'original_price' => $productDetails->original_price,
                    'discount' => $productDetails->discount,
                    'pd_code' => $productDetails->pd_code,
                    'promo_group_id' => $promoGroupId,
                ],
                'associatedModel' => $product,
            ]);
        }

        // Add gifts
        foreach ($giftIds as $giftId) {
            $giftDetails = $this->getProductDetails($giftId);
            if ($giftDetails) {
                $cart->add([
                    'id' => $giftDetails->id,
                    'name' => $giftDetails->name.' (ของแถม)',
                    'price' => 0,
                    'quantity' => 1, // Each gift is added with quantity 1
                    'attributes' => [
                        'image' => $giftDetails->image,
                        'original_price' => $giftDetails->original_price,
                        'discount' => $giftDetails->original_price,
                        'pd_code' => $giftDetails->pd_code,
                        'is_freebie' => true,
                        'promo_group_id' => $promoGroupId,
                    ],
                ]);
            }
        }

        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    public function updateQuantity(int $productId, string $action): void
    {
        $userId = $this->getUserId();
        $cart = Cart::session($userId);
        $qty = ($action === 'increase') ? 1 : -1;

        $cart->update($productId, ['quantity' => $qty]);

        $this->validateFreebieConsistency($userId);

        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    public function removeItem(int $productId): void
    {
        $userId = $this->getUserId();
        $cart = Cart::session($userId);
        $item = $cart->get($productId);

        if (! $item) {
            return;
        }

        // If the item is part of a promotion group, remove all items in that group.
        if (isset($item->attributes['promo_group_id'])) {
            $promoGroupId = $item->attributes['promo_group_id'];

            $itemsToRemove = $cart->getContent()->filter(function ($cartItem) use ($promoGroupId) {
                return isset($cartItem->attributes['promo_group_id']) && $cartItem->attributes['promo_group_id'] === $promoGroupId;
            });

            foreach ($itemsToRemove as $itemToRemove) {
                $cart->remove($itemToRemove->id);
            }
        } else {
            // Otherwise, just remove the single item.
            $cart->remove($productId);
        }

        // After removal, it's still good practice to validate consistency for other promos.
        $this->validateFreebieConsistency($userId);

        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    /**
     * ฟังก์ชันหัวใจสำคัญ: ตรวจสอบว่าของแถมเกินสิทธิ์หรือไม่ ถ้าเกินให้ลบออก
     */
    private function validateFreebieConsistency(int $userId): void
    {
        $cart = Cart::session($userId);
        $items = $cart->getContent(); // ดึงตะกร้าปัจจุบัน (หลังลบสินค้าหลักแล้ว)

        // 1. คำนวณสิทธิ์ของแถมที่ควรได้รับจริง ณ ตอนนี้ (จากสินค้าที่เหลืออยู่)
        $limit = $this->calculateFreebieLimit($items);

        // 2. นับจำนวนของแถมที่มีอยู่ในตะกร้าตอนนี้ (ดูจาก attribute is_freebie)
        $freebies = $items->filter(function ($item) {
            return $item->attributes->is_freebie ?? false;
        })->sort();

        $currentFreebieQty = $freebies->sum('quantity');

        // 3. ถ้าของแถมที่มี (current) มากกว่า สิทธิ์ที่ควรได้ (limit) -> ลบส่วนเกินออก
        if ($currentFreebieQty > $limit) {
            $diff = $currentFreebieQty - $limit;

            // วนลูปไล่ลบของแถม (เริ่มจากตัวท้ายๆ)
            foreach ($freebies->reverse() as $freebie) {
                if ($diff <= 0) {
                    break;
                }

                $qtyToRemove = min($diff, $freebie->quantity);

                if ($qtyToRemove >= $freebie->quantity) {
                    $cart->remove($freebie->id); // ลบทั้งรายการถ้าต้องเอาออกหมด
                } else {
                    $cart->update($freebie->id, [
                        'quantity' => -$qtyToRemove, // ลดจำนวนลง
                    ]);
                }

                $diff -= $qtyToRemove;
            }
        }
    }

    // ... (ส่วนอื่นๆ คงเดิม) ...
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
                        'image' => $details->image, 'original_price' => $details->original_price,
                        'discount' => $details->original_price, 'pd_code' => $details->pd_code,
                        'is_freebie' => true,
                    ],
                ]);
            }
        }
        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    private function getApplicablePromotions(Collection $cartItems): Collection
    {
        if ($cartItems->isEmpty()) {
            return collect();
        }
        $now = now();
        $cartProductIds = $cartItems->pluck('id')->toArray();
        $cartQuantities = $cartItems->pluck('quantity', 'id');

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

        return $potentialPromotions->filter(function ($promo) use ($cartQuantities) {
            if ($promo->rules->isEmpty()) {
                return false;
            }

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
                if ($isRuleMet) {
                    $rulesMetCount++;
                }
            }

            if ($conditionType === 'all') {
                return $rulesMetCount === $totalRules;
            } else {
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
