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
     * ตัวแปรเช็คสถานะการโหลด เพื่อไม่ให้โหลดซ้ำใน Request เดียวกัน
     */
    protected bool $cartLoadedFromDb = false;

    /**
     * ดึงข้อมูลทั้งหมดสำหรับแสดงผลหน้าตะกร้า (Cart View)
     */
    public function getCartDataForView(): array
    {
        $items = $this->getCartContents();
        $total = $this->getTotal();

        // ดึงข้อมูลสินค้า (Eager Load)
        $productIds = $items->pluck('id')->toArray();
        $products = ProductSalepage::with('images')
            ->whereIn('pd_sp_id', $productIds)
            ->get()
            ->keyBy('pd_sp_id');

        // คำนวณโปรโมชั่น
        $applicablePromotions = $this->getApplicablePromotions($items);

        // คำนวณ Limit ของแถม (ส่ง $applicablePromotions ไปด้วย ลด Query)
        $freebieLimit = $this->calculateFreebieLimit($items, $applicablePromotions);

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
    public function calculateFreebieLimit(?Collection $cartItems = null, ?Collection $applicablePromotions = null): int
    {
        $items = $cartItems ?? $this->getCartContents();
        $promos = $applicablePromotions ?? $this->getApplicablePromotions($items);

        if ($promos->isEmpty()) {
            return 0;
        }

        // Logic ใหม่: คูณด้วย Multiplier (จำนวนชุดที่ได้รับสิทธิ์)
        return $promos->sum(function ($promo) {
            $multiplier = $promo->multiplier ?? 1;

            return $promo->actions->sum(function ($action) use ($multiplier) {
                $qtyPerSet = (int) ($action->actions['quantity_to_get'] ?? 0);

                return $qtyPerSet * $multiplier;
            });
        });
    }

    public function getUserId(): string|int
    {
        return Auth::check() ? Auth::id() : '_guest_'.session()->getId();
    }

    /**
     * ดึงข้อมูลตะกร้า (แก้ไขให้โหลดจาก DB แค่ครั้งเดียว)
     */
    public function getCartContents(): Collection
    {
        $userId = $this->getUserId();

        if (Auth::check() && ! $this->cartLoadedFromDb) {
            $sessionCart = Cart::session($userId)->getContent();
            // โหลดใหม่เฉพาะถ้า Session ว่างเปล่า
            if ($sessionCart->isEmpty()) {
                $this->restoreCartFromDatabase($userId);
            }
            $this->cartLoadedFromDb = true;
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

    /**
     * ฟังก์ชันเช็คสต็อก (Helper)
     */
    private function checkStockAndGetProduct(int $productId, int $quantity)
    {
        $product = ProductSalepage::find($productId);
        if (! $product) {
            throw new \Exception("ไม่พบสินค้า ID: {$productId}");
        }

        $userId = $this->getUserId();
        $cart = Cart::session($userId);
        $currentQty = $cart->has($productId) ? $cart->get($productId)->quantity : 0;

        if (($currentQty + $quantity) > $product->pd_sp_stock) {
            throw new \Exception("สินค้า '{$product->pd_sp_name}' มีไม่เพียงพอ (เหลือ {$product->pd_sp_stock})");
        }

        return $product;
    }

    public function addOrUpdate(int $productId, int $quantity): void
    {
        $product = $this->checkStockAndGetProduct($productId, $quantity);
        $details = $this->getProductDetails($productId);

        if ($details) {
            $userId = $this->getUserId();
            $cart = Cart::session($userId);

            // --- START NEW LOGIC ---
            $existingItem = $cart->get($productId);
            $newAttributes = [
                'image' => $details->image,
                'original_price' => $details->original_price,
                'discount' => $details->discount,
                'pd_code' => $details->pd_code,
            ];

            if ($existingItem) {
                // Preserve existing promotional attributes
                $promoAttributes = [
                    'promo_group_id',
                    'is_condition_item',
                    'item_type',
                    'is_freebie',
                ];
                foreach ($promoAttributes as $attr) {
                    if (isset($existingItem->attributes[$attr])) {
                        $newAttributes[$attr] = $existingItem->attributes[$attr];
                    }
                }
            }
            // --- END NEW LOGIC ---

            $cart->add([
                'id' => $details->id,
                'name' => $details->name,
                'price' => $details->price,
                'quantity' => $quantity,
                'attributes' => $newAttributes, // Use the merged attributes
                'associatedModel' => $product,
            ]);

            if (Auth::check()) {
                $this->saveCartToDatabase($userId, $cart->getContent());
            }
        }
    }

    /**
     * เพิ่มสินค้าแบบซื้อคู่ (Bundle) A+B แถม C
     */
    public function addBundle(int $mainProductId, int $secondaryProductId, array $giftIds = []): void
    {
        $userId = $this->getUserId();
        $cart = Cart::session($userId);
        $promoGroupId = 'bundle_'.Str::uuid(); // Group ID เดียวกันทั้งชุด

        // 1. Add Main
        $this->checkStockAndGetProduct($mainProductId, 1);
        $mainDetails = $this->getProductDetails($mainProductId);
        if ($mainDetails) {
            $cart->add([
                'id' => $mainDetails->id,
                'name' => $mainDetails->name,
                'price' => $mainDetails->price,
                'quantity' => 1,
                'attributes' => [
                    'image' => $mainDetails->image,
                    'pd_code' => $mainDetails->pd_code,
                    'promo_group_id' => $promoGroupId,
                    'is_condition_item' => true, // ✅ เป็นสินค้าเงื่อนไข
                    'item_type' => 'main',
                ],
                'associatedModel' => ProductSalepage::find($mainProductId),
            ]);
        }

        // 2. Add Secondary
        $this->checkStockAndGetProduct($secondaryProductId, 1);
        $secDetails = $this->getProductDetails($secondaryProductId);
        if ($secDetails) {
            $cart->add([
                'id' => $secDetails->id,
                'name' => $secDetails->name,
                'price' => $secDetails->price,
                'quantity' => 1,
                'attributes' => [
                    'image' => $secDetails->image,
                    'pd_code' => $secDetails->pd_code,
                    'promo_group_id' => $promoGroupId,
                    'is_condition_item' => true, // ✅ เป็นสินค้าเงื่อนไข
                    'item_type' => 'secondary',
                ],
                'associatedModel' => ProductSalepage::find($secondaryProductId),
            ]);
        }

        // 3. Add Freebies
        foreach ($giftIds as $giftId) {
            $this->checkStockAndGetProduct($giftId, 1);
            $giftDetails = $this->getProductDetails($giftId);
            if ($giftDetails) {
                $cart->add([
                    'id' => $giftDetails->id,
                    'name' => $giftDetails->name.' (ของแถม)',
                    'price' => 0,
                    'quantity' => 1,
                    'attributes' => [
                        'image' => $giftDetails->image,
                        'pd_code' => $giftDetails->pd_code,
                        'is_freebie' => true, // ✅ เป็นของแถม
                        'promo_group_id' => $promoGroupId,
                    ],
                ]);
            }
        }

        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    /**
     * เพิ่มสินค้าพร้อมของแถม (Logic เดิม ปรับปรุง Attributes)
     */
    public function addWithGifts(int $productId, int $quantity, array $giftIds): void
    {
        $userId = $this->getUserId();
        $cart = Cart::session($userId);

        // Check Stocks
        $product = $this->checkStockAndGetProduct($productId, $quantity);
        foreach (array_count_values($giftIds) as $gId => $count) {
            $this->checkStockAndGetProduct($gId, $count);
        }

        $promoGroupId = 'promo_'.Str::uuid();

        // Add Main Product
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
                    'is_condition_item' => true, // ✅ เป็นสินค้าเงื่อนไข
                ],
                'associatedModel' => $product,
            ]);
        }

        // Add Gifts
        foreach ($giftIds as $giftId) {
            $giftDetails = $this->getProductDetails($giftId);
            if ($giftDetails) {
                $cart->add([
                    'id' => $giftDetails->id,
                    'name' => $giftDetails->name.' (ของแถม)',
                    'price' => 0,
                    'quantity' => 1,
                    'attributes' => [
                        'image' => $giftDetails->image,
                        'original_price' => $giftDetails->original_price,
                        'discount' => $giftDetails->original_price,
                        'pd_code' => $giftDetails->pd_code,
                        'is_freebie' => true, // ✅ เป็นของแถม
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

    /**
     * ⚠️ Logic การลบสินค้า:
     * - ถ้าสินค้าที่ถูกลบเป็นส่วนหนึ่งของโปรโมชั่น (เช่น สินค้าที่ต้องซื้อเพื่อรับของแถม)
     *   ระบบจะทำการลบสินค้าอื่นๆ ทั้งหมดที่อยู่ในกลุ่มโปรโมชั่นเดียวกันออกไปด้วย (ทั้งของแถมและสินค้าเงื่อนไขอื่นๆ)
     * - ถ้าสินค้าที่ถูกลบเป็นของแถม หรือเป็นสินค้าเดี่ยวๆ ที่ไม่เกี่ยวกับโปรโมชั่น ระบบจะลบแค่สินค้านั้นๆ
     */
    public function removeItem(int $productId): void
    {
        $userId = $this->getUserId();
        $cart = Cart::session($userId);
        $item = $cart->get($productId);

        if (! $item) {
            return;
        }

        $promoGroupId = $item->attributes['promo_group_id'] ?? null;
        $isFreebie = $item->attributes['is_freebie'] ?? false;

        // If the item is part of a promotion group, AND it's NOT a freebie
        // (meaning it must be a condition item, either main or secondary)
        if ($promoGroupId && ! $isFreebie) {
            // Remove the entire group associated with this promotion
            $itemsInGroup = $cart->getContent()->filter(function ($cartItem) use ($promoGroupId) {
                return ($cartItem->attributes['promo_group_id'] ?? null) === $promoGroupId;
            });

            foreach ($itemsInGroup as $groupItem) {
                $cart->remove($groupItem->id);
            }
        } else {
            // Otherwise, it's a standalone item OR a freebie. Just remove the item itself.
            $cart->remove($productId);
        }

        // ตรวจสอบความถูกต้องของแถมอื่นๆ ในตะกร้า
        $this->validateFreebieConsistency($userId);

        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    /**
     * ฟังก์ชันตรวจสอบสิทธิ์ของแถม (ใช้ Multiplier Logic ใหม่)
     */
    private function validateFreebieConsistency(int|string $userId): void
    {
        $cart = Cart::session($userId);
        $items = $cart->getContent();

        // คำนวณ Limit ที่ถูกต้อง (รวม Multiplier แล้ว)
        $limit = $this->calculateFreebieLimit($items);

        // ของแถมที่มีอยู่ในตะกร้า
        $freebies = $items->filter(function ($item) {
            return $item->attributes->is_freebie ?? false;
        })->sort();

        $currentFreebieQty = $freebies->sum('quantity');

        // ถ้าของแถมเกินสิทธิ์ ให้ลบออก
        if ($currentFreebieQty > $limit) {
            $diff = $currentFreebieQty - $limit;

            foreach ($freebies->reverse() as $freebie) {
                if ($diff <= 0) {
                    break;
                }

                $qtyToRemove = min($diff, $freebie->quantity);

                if ($qtyToRemove >= $freebie->quantity) {
                    $cart->remove($freebie->id);
                } else {
                    $cart->update($freebie->id, ['quantity' => -$qtyToRemove]);
                }

                $diff -= $qtyToRemove;
            }
        }
    }

    public function addFreebies(array $freebieIds): void
    {
        $userId = $this->getUserId();
        $this->getCartContents(); // Ensure cart loaded
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
                        'is_freebie' => true, // ✅
                    ],
                ]);
            }
        }

        $this->validateFreebieConsistency($userId);

        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    /**
     * คำนวณโปรโมชั่น และหา "ตัวคูณ" (Multiplier)
     */
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
            $promoMultipliers = [];

            foreach ($promo->rules as $rule) {
                $pids = $rule->rules['product_id'] ?? [];
                if (! is_array($pids)) {
                    $pids = [$pids];
                }
                $reqQty = (int) ($rule->rules['quantity_to_buy'] ?? 1);

                $totalQtyMatched = 0;
                foreach ($pids as $pid) {
                    if ($cartQuantities->has((int) $pid)) {
                        $totalQtyMatched += $cartQuantities->get((int) $pid);
                    }
                }

                if ($reqQty > 0 && $totalQtyMatched >= $reqQty) {
                    // คำนวณว่าผ่านกี่รอบ (ซื้อ 5 ชุด ได้ 5 สิทธิ์)
                    $promoMultipliers[] = floor($totalQtyMatched / $reqQty);
                } else {
                    $promoMultipliers[] = 0;
                }
            }

            $finalMultiplier = 0;
            if ($conditionType === 'all') {
                $minMultiplier = empty($promoMultipliers) ? 0 : min($promoMultipliers);
                if ($minMultiplier > 0) {
                    $finalMultiplier = $minMultiplier;
                }
            } else {
                $finalMultiplier = array_sum($promoMultipliers);
            }

            if ($finalMultiplier > 0) {
                $promo->multiplier = $finalMultiplier; // ✅ เก็บค่าตัวคูณ

                return true;
            }

            return false;
        });
    }

    private function saveCartToDatabase(int|string $userId, $cartContent): void
    {
        if (is_string($userId)) {
            return;
        }
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
