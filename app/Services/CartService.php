<?php

namespace App\Services;

use App\Models\CartStorage;
use App\Models\ProductSalepage;
use App\Models\Promotion;
use App\Models\PromotionRule;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CartService
{
    protected bool $cartLoadedFromDb = false;

    // -------------------------------------------------------------------------
    //  Helpers
    // -------------------------------------------------------------------------

    private function findCartKeys(string|int $productId): array
    {
        $userId = $this->getUserId();
        $keys = [];
        foreach ($this->getCartContents() as $key => $item) {
            $realProductId = $item->attributes['product_id'] ?? $item->id;
            if ($item->id == $productId || $realProductId == $productId) {
                $keys[] = $key;
            }
        }

        return $keys;
    }

    // -------------------------------------------------------------------------
    //  Main Features
    // -------------------------------------------------------------------------

    public function addOrUpdate(int $productId, int $quantity, ?int $optionId = null): void
    {
        $this->getCartContents();
        $userId = $this->getUserId();
        $cart = Cart::session($userId);

        if ($optionId) {
            $option = \App\Models\ProductOption::with(['product', 'stock'])->find($optionId);

            if (! $option || $option->parent_id !== $productId) {
                throw new Exception('ตัวเลือกสินค้าไม่ถูกต้อง');
            }

            $cartId = "{$productId}-{$optionId}";
            $existingItem = $cart->get($cartId);
            $newQuantity = $existingItem ? $existingItem->quantity + $quantity : $quantity;

            if ($newQuantity > $option->option_stock) {
                throw new Exception("สินค้าตัวเลือก '{$option->option_name}' มีไม่เพียงพอ (เหลือในสต็อก {$option->option_stock} ชิ้น)");
            }

            if ($existingItem) {
                $cart->update($cartId, [
                    'quantity' => $quantity,
                ]);
            } else {
                $details = $this->getProductDetails($productId);
                if (! $details) {
                    throw new Exception("ไม่พบสินค้า ID: {$productId}");
                }

                $product = $this->checkStockAndGetProduct($productId, 0);

                $cart->add([
                    'id' => $cartId,
                    'name' => $details->name.' ('.$option->option_name.')',
                    'price' => $option->final_price,
                    'quantity' => $quantity,
                    'attributes' => [
                        'image' => $details->image,
                        'original_price' => (float) $option->option_price,
                        'discount' => $option->product ? (float) $option->product->pd_sp_discount : 0,
                        'pd_code' => $details->pd_code,
                        'product_id' => $productId,
                        'option_id' => $optionId,
                    ],
                    'associatedModel' => $product,
                ]);
            }

        } else {
            $existingItem = $cart->get($productId);
            $newQuantity = $existingItem ? $existingItem->quantity + $quantity : $quantity;

            $product = $this->checkStockAndGetProduct($productId, $newQuantity);
            $details = $this->getProductDetails($productId);

            if ($details) {
                if ($existingItem) {
                    $cart->update($productId, [
                        'quantity' => $quantity,
                    ]);
                } else {
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
                            'product_id' => $productId,
                        ],
                        'associatedModel' => $product,
                    ]);
                }
            }
        }

        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    public function addWithGifts(int $productId, int $quantity, array $giftIds): void
    {
        $this->addBundle($productId, 0, $giftIds, $quantity);
    }

    public function addBundle(int $mainProductId, int $secondaryProductId, array $giftIds = [], int $qty = 1, bool $isBirthday = false): void
    {
        $userId = $this->getUserId();
        $this->getCartContents();
        $cart = Cart::session($userId);

        $allInvolvedIds = [$mainProductId];
        if ($secondaryProductId > 0) {
            $allInvolvedIds[] = $secondaryProductId;
        }

        $promoGroupId = 'bundle_'.Str::uuid();

        // 1. จัดการสินค้าหลัก
        if ($mainProductId > 0) {
            // ... (keep existing code for main product)
            $mainProduct = $this->checkStockAndGetProduct($mainProductId, $qty);
            $mainDetails = $this->getProductDetails($mainProductId);
            if ($mainDetails) {
                $cart->add([
                    'id' => $mainDetails->id,
                    'name' => $mainDetails->name,
                    'price' => $mainDetails->price,
                    'quantity' => $qty,
                    'attributes' => [
                        'image' => $mainDetails->image,
                        'pd_code' => $mainDetails->pd_code,
                        'promo_group_id' => $promoGroupId,
                        'is_condition_item' => true,
                        'item_type' => 'main',
                        'product_id' => $mainDetails->id,
                    ],
                    'associatedModel' => $mainProduct,
                ]);
            }
        }

        // 2. จัดการสินค้าเงื่อนไขที่สอง (ถ้ามี)
        if ($secondaryProductId > 0) {
            // ... (keep existing code for secondary product)
            $secProduct = $this->checkStockAndGetProduct($secondaryProductId, 1);
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
                        'is_condition_item' => true,
                        'item_type' => 'secondary',
                        'product_id' => $secDetails->id,
                    ],
                    'associatedModel' => $secProduct,
                ]);
            }
        }

        // 3. จัดการของแถม
        $giftProducts = ProductSalepage::whereIn('pd_sp_id', $giftIds)->with('images')->get()->keyBy('pd_sp_id');

        foreach ($giftIds as $giftId) {
            $giftProduct = $giftProducts->get($giftId);
            if (! $giftProduct) {
                continue;
            }

            $imgPath = $giftProduct->images->first()?->img_path;
            if ($imgPath && ! filter_var($imgPath, FILTER_VALIDATE_URL)) {
                $imgPath = asset('storage/'.ltrim(str_replace('storage/', '', $imgPath), '/'));
            }

            $cart->add([
                'id' => $giftProduct->pd_sp_id.($isBirthday ? '_birthday' : '_free'),
                'name' => $giftProduct->pd_sp_name.($isBirthday ? ' (ของขวัญวันเกิด)' : ' (ของแถม)'),
                'price' => 0,
                'quantity' => 1,
                'attributes' => [
                    'image' => $imgPath,
                    'pd_code' => $giftProduct->pd_sp_code,
                    'is_freebie' => true,
                    'is_birthday_gift' => $isBirthday,
                    'promo_group_id' => $promoGroupId,
                    'product_id' => $giftProduct->pd_sp_id,
                ],
                'associatedModel' => $giftProduct,
            ]);
        }

        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    public function removeItem(string|int $productId): void
    {
        $this->removeItems([$productId]);
    }

    public function removeItems(array $productIds): void
    {
        $userId = $this->getUserId();
        $this->getCartContents();
        $cart = Cart::session($userId);

        $keysToDelete = [];

        foreach ($productIds as $productId) {
            $targetKeys = $this->findCartKeys($productId);
            if (empty($targetKeys)) {
                // Try direct lookup by ID if findCartKeys didn't find it (e.g. for freebies)
                if ($cart->get($productId)) {
                    $targetKeys = [$productId];
                } else {
                    continue;
                }
            }

            foreach ($targetKeys as $key) {
                $item = $cart->get($key);
                if (!$item) continue;

                $promoGroupId = $item->attributes['promo_group_id'] ?? null;
                $isFreebie = $item->attributes['is_freebie'] ?? false;

                if ($promoGroupId && !$isFreebie) {
                    foreach ($cart->getContent() as $k => $cartItem) {
                        if (($cartItem->attributes['promo_group_id'] ?? null) === $promoGroupId) {
                            $keysToDelete[] = $k;
                        }
                    }
                } else {
                    $keysToDelete[] = $key;
                }
            }
        }

        foreach (array_unique($keysToDelete) as $k) {
            $cart->remove($k);
        }

        $this->validateFreebieConsistency($userId);
        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    public function updateQuantity(string|int $productId, string $action): void
    {
        $userId = $this->getUserId();
        $this->getCartContents();
        $cart = Cart::session($userId);

        $keys = $this->findCartKeys($productId);
        if (empty($keys)) {
            return;
        }

        foreach ($keys as $key) {
            $item = $cart->get($key);
            if ($action === 'increase') {
                $productIdReal = $item->attributes['product_id'] ?? $item->id;
                $optionId = $item->attributes['option_id'] ?? null;

                if ($optionId) {
                    $option = \App\Models\ProductOption::with('stock')->find($optionId);
                    if ($option && $item->quantity + 1 > $option->option_stock) {
                        throw new Exception("สินค้า '{$item->name}' มีไม่เพียงพอ (สต็อกเหลือ {$option->option_stock})");
                    }
                } else {
                    $product = ProductSalepage::with('stock')->find($productIdReal);
                    if ($product && $item->quantity + 1 > $product->pd_sp_stock) {
                        throw new Exception("สินค้า '{$item->name}' มีไม่เพียงพอ (สต็อกเหลือ {$product->pd_sp_stock})");
                    }
                }

                $cart->update($key, ['quantity' => 1]);
            } else {
                if ($item->quantity > 1) {
                    $cart->update($key, ['quantity' => -1]);
                }
            }
        }

        $this->validateFreebieConsistency($userId);
        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    public function getCartDataForView(?array $selectedIds = null): array
    {
        $allItems = $this->getCartContents();

        $itemsToCalculate = $allItems;
        if ($selectedIds !== null) {
            $itemsToCalculate = $allItems->filter(fn ($item) => in_array($item->id, $selectedIds));
        }

        $subTotal = 0;
        foreach ($itemsToCalculate as $item) {
            $subTotal += ($item->price * $item->quantity);
        }

        $orderDiscount = $this->calculateTotalDiscount($subTotal, $itemsToCalculate);
        $finalTotal = max(0, $subTotal - $orderDiscount);

        $productIds = $allItems->map(function ($item) {
            return $item->attributes['product_id'] ?? $item->id;
        })->unique()->toArray();

        $products = ProductSalepage::with(['images', 'stock'])->whereIn('pd_sp_id', $productIds)->get()->keyBy('pd_sp_id');

        $allApplicablePromotions = $this->getApplicablePromotions($itemsToCalculate);

        $cartApplicablePromotions = $allApplicablePromotions->filter(fn ($p) => $p->condition_type === 'all');

        $rawFreebieLimit = $this->calculateFreebieLimit($itemsToCalculate, $cartApplicablePromotions);

        $existingFreebiesCount = $allItems->filter(fn ($item) => $item->attributes->get('is_freebie'))->sum('quantity');
        $freebieLimit = max(0, $rawFreebieLimit - $existingFreebiesCount);

        $giftableProducts = $cartApplicablePromotions->flatMap(function ($promo) {
            return $promo->actions->flatMap(function ($action) {
                $gifts = collect();
                $productToGetId = $action->actions['product_id_to_get'] ?? null;
                if ($productToGetId) {
                    $p = ProductSalepage::with('images')->find($productToGetId);
                    if ($p) {
                        $gifts->push($p);
                    }
                }
                if ($action->giftableProducts->isNotEmpty()) {
                    $gifts = $gifts->merge($action->giftableProducts);
                }

                return $gifts;
            });
        })->unique('pd_sp_id');

        return [
            'items' => $allItems,
            'subTotal' => $subTotal,
            'totalDiscount' => $orderDiscount,
            'total' => $finalTotal,
            'products' => $products,
            'applicablePromotions' => $cartApplicablePromotions,
            'giftableProducts' => $giftableProducts,
            'freebieLimit' => $freebieLimit,
        ];
    }

    public function getPromotionsForProduct(int $productId): Collection
    {
        $now = now();
        $promotionIds = PromotionRule::where(function ($q) use ($productId) {
            $q->where('rules->product_id', (string) $productId)
                ->orWhere('rules->product_id', (int) $productId)
                ->orWhereJsonContains('rules->product_id', (string) $productId)
                ->orWhereJsonContains('rules->product_id', (int) $productId);
        })->pluck('promotion_id')->unique();
        if ($promotionIds->isEmpty()) {
            return collect();
        }

        return Promotion::with(['rules', 'actions.giftableProducts'])
            ->whereIn('id', $promotionIds)->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', $now))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $now))->get();
    }

    public function calculateFreebieLimit(?Collection $cartItems = null, ?Collection $applicablePromotions = null): int
    {
        $items = $cartItems ?? $this->getCartContents();
        $promos = $applicablePromotions ?? $this->getApplicablePromotions($items);
        if ($promos->isEmpty()) {
            return 0;
        }

        return $promos->sum(function ($promo) {
            $multiplier = $promo->multiplier ?? 1;

            return $promo->actions->sum(fn ($action) => (int) ($action->actions['quantity_to_get'] ?? 0) * $multiplier);
        });
    }

    public function getUserId(): string|int
    {
        return Auth::check() ? Auth::id() : '_guest_'.session()->getId();
    }

    public function getCartContents(): Collection
    {
        $userId = $this->getUserId();
        if (Auth::check() && ! $this->cartLoadedFromDb) {
            $sessionCart = Cart::session($userId)->getContent();
            if ($sessionCart->isEmpty()) {
                $this->restoreCartFromDatabase($userId);
            }
            $this->cartLoadedFromDb = true;
        }

        return Cart::session($userId)->getContent()->sort();
    }

    public function getTotal(): float
    {
        $userId = $this->getUserId();
        $cart = Cart::session($userId);
        $subTotal = (float) $cart->getTotal();
        $discount = $this->calculateTotalDiscount($subTotal);

        return max(0, $subTotal - $discount);
    }

    // ✅ 🌟 แก้ไข: จัดการให้รองรับทั้ง Auto-Discount และ Coupon Code
    public function calculateTotalDiscount(float $subTotal, ?Collection $specificItems = null): float
    {
        $items = $specificItems ?? $this->getCartContents();
        if ($items->isEmpty()) {
            return 0;
        }

        $promos = $this->getApplicablePromotions($items);
        $maxDiscount = 0;
        $appliedCode = $this->getAppliedPromoCode(); 

        foreach ($promos as $promo) {
            // เงื่อนไขที่ 1: เป็นโปรโมชั่นอัตโนมัติ (ไม่ได้ตั้งว่าเป็นโปรที่ต้องใช้รหัส) -> ให้ส่วนลดได้เลย
            $isAutoDiscount = !$promo->is_discount_code;
            
            // เงื่อนไขที่ 2: เป็นโปรโมชั่นที่ต้องใช้รหัส (Coupon Code) -> ต้องกรอกรหัสตรงกันเท่านั้น ถึงจะให้ส่วนลด
            $isMatchingCode = $promo->is_discount_code && !empty($appliedCode) && $promo->code === $appliedCode;

            // ถ้าเข้าเงื่อนไขใดเงื่อนไขหนึ่ง และมีการตั้งค่ามูลค่าส่วนลดไว้
            if ($promo->discount_value > 0 && ($isAutoDiscount || $isMatchingCode)) {
                $currentPromoDiscount = 0;
                if ($promo->discount_type === 'fixed') {
                    $currentPromoDiscount = (float) $promo->discount_value;
                } elseif ($promo->discount_type === 'percentage') {
                    $currentPromoDiscount = ($subTotal * ((float) $promo->discount_value / 100));
                }
                
                // ใช้หลักการ "เลือกส่วนลดที่คุ้มที่สุดให้ลูกค้า" (Best Deal) ไม่เอามาบวกซ้อนกัน
                if ($currentPromoDiscount > $maxDiscount) {
                    $maxDiscount = $currentPromoDiscount;
                }
            }
        }

        return $maxDiscount;
    }

    public function applyPromoCode(string $code): void
    {
        $promo = Promotion::where('code', $code)
            ->where('is_active', true)
            ->where('is_discount_code', true)
            ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', now()))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()))
            ->first();

        if (! $promo) {
            throw new Exception('รหัสส่วนลดไม่ถูกต้อง หรือหมดอายุแล้ว');
        }

        $userId = $this->getUserId();

        // ✅ เพิ่มการตรวจสอบว่า User นี้เคยใช้โค้ดนี้ไปแล้วหรือยัง (เช็คจาก Log การสั่งซื้อจริง)
        // ยกเว้นออเดอร์ที่ถูกยกเลิก (status_id = 5) เพื่อให้ลูกค้านำรหัสกลับมาใช้ใหม่ได้
        if (Auth::check()) {
            $alreadyUsed = \App\Models\PromotionUsageLog::where('promotion_id', $promo->id)
                ->where('user_id', Auth::id())
                ->whereHas('order', function($q) {
                    $q->where('status_id', '!=', 5); // 5 = ยกเลิก (อ้างอิงจาก statusMap ใน view)
                })
                ->exists();
            
            if ($alreadyUsed) {
                throw new Exception('คุณเคยใช้รหัสส่วนลดนี้ไปแล้ว ไม่สามารถใช้ซ้ำได้');
            }
        }

        if ($promo->usage_limit !== null && $promo->used_count >= $promo->usage_limit) {
            throw new Exception('ขออภัย! รหัสส่วนลดนี้ถูกใช้ครบจำนวนสิทธิ์แล้ว');
        }

        session(["cart_{$userId}_promo_code" => $code]);
    }

    public function removePromoCode(): void
    {
        $userId = $this->getUserId();
        session()->forget("cart_{$userId}_promo_code");
    }

    public function getAppliedPromoCode(): ?string
    {
        $userId = $this->getUserId();

        return session("cart_{$userId}_promo_code");
    }

    public function getTotalQuantity(): int
    {
        return Cart::session($this->getUserId())->getTotalQuantity();
    }

    private function getProductDetails(int $productId): ?object
    {
        $product = ProductSalepage::with(['images', 'stock'])->find($productId);
        if (! $product) {
            return null;
        }
        $img = $product->images->first();
        $imgPath = $img ? ($img->img_path ?? $img->image_path) : null;
        if ($imgPath && ! filter_var($imgPath, FILTER_VALIDATE_URL)) {
            $imgPath = asset('storage/'.ltrim($imgPath, '/'));
        }

        return (object) [
            'id' => $product->pd_sp_id, 'name' => $product->pd_sp_name, 'price' => $product->final_price,
            'original_price' => (float) $product->pd_sp_price, 'discount' => (float) $product->pd_sp_discount,
            'image' => $imgPath, 'pd_code' => $product->pd_code, 'stock' => $product->pd_sp_stock ?? 0,
        ];
    }

    private function checkStockAndGetProduct(int $productId, int $quantity)
    {
        $product = ProductSalepage::with('stock')->find($productId);
        if (! $product) {
            throw new Exception("ไม่พบสินค้า ID: {$productId}");
        }
        if ($quantity > $product->pd_sp_stock) {
            throw new Exception("สินค้า '{$product->pd_sp_name}' มีไม่เพียงพอ");
        }

        return $product;
    }

    public function addFreebies(array $freebieIds): void
    {
        $this->addBundle(0, 0, $freebieIds);
    }

    public function mergeGuestCart(string $guestSessionKey, int $userId): void
    {
        $guestCart = Cart::session('_guest_'.$guestSessionKey);
        $guestItems = $guestCart->getContent();
        
        // 1. ย้าย Promo Code จาก Guest Session มาที่ User Session
        $guestPromoCode = session("cart__guest_{$guestSessionKey}_promo_code");
        if ($guestPromoCode) {
            session(["cart_{$userId}_promo_code" => $guestPromoCode]);
            // ลบของเดิมออก
            session()->forget("cart__guest_{$guestSessionKey}_promo_code");
        }

        if ($guestItems->isEmpty()) {
            return;
        }

        $this->restoreCartFromDatabase($userId);
        $userCart = Cart::session($userId);
        
        // รวบรวม ID สินค้าทั้งหมด (รวมทั้งสินค้าปกติและของแถม)
        $productIds = $guestItems->map(function($item) {
            return $item->attributes['product_id'] ?? $item->id;
        })->unique()->toArray();
        
        $guestProducts = ProductSalepage::whereIn('pd_sp_id', $productIds)->with('images')->get()->keyBy('pd_sp_id');

        foreach ($guestItems as $guestItem) {
            try {
                $realProductId = $guestItem->attributes['product_id'] ?? $guestItem->id;
                $product = $guestProducts->get($realProductId);
                
                if (!$product) continue;

                // ตรวจสอบสต็อกเฉพาะสินค้าที่ไม่ใช่ของแถม
                $isFreebie = $guestItem->attributes['is_freebie'] ?? false;
                if (!$isFreebie && $guestItem->quantity > ($product->pd_sp_stock ?? 0)) {
                    continue;
                }

                if ($userCart->has($guestItem->id)) {
                    $userCart->update($guestItem->id, [
                        'quantity' => $guestItem->quantity,
                        'attributes' => $guestItem->attributes->toArray() // Ensure attributes are updated/preserved
                    ]);
                } else {
                    // คัดลอก Attributes ทั้งหมดมา (สำคัญมากสำหรับสถานะ is_freebie และ is_birthday_gift)
                    $attributes = $guestItem->attributes->toArray();
                    
                    $userCart->add([
                        'id' => $guestItem->id,
                        'name' => $guestItem->name,
                        'price' => $guestItem->price,
                        'quantity' => $guestItem->quantity,
                        'attributes' => $attributes,
                        'associatedModel' => $product,
                    ]);
                }
            } catch (Exception $e) {
                Log::error("Merge Cart Item Error: " . $e->getMessage());
                continue;
            }
        }
        
        $this->saveCartToDatabase($userId, $userCart->getContent());
        $guestCart->clear();
    }

    private function validateFreebieConsistency(int|string $userId): void
    {
        $cart = Cart::session($userId);
        $items = $cart->getContent();

        // กรองเอาเฉพาะของแถมที่ไม่ใช่ของขวัญวันเกิด
        $freebies = $items->filter(fn ($item) => ($item->attributes['is_freebie'] ?? false) && !($item->attributes['is_birthday_gift'] ?? false));
        if ($freebies->isEmpty()) {
            return;
        }

        $limit = $this->calculateFreebieLimit($items);
        $currentFreebieQty = $freebies->sum('quantity');

        if ($currentFreebieQty > $limit) {
            $diff = $currentFreebieQty - $limit;
            $keysToRemove = [];

            foreach ($freebies->reverse() as $freebie) {
                if ($diff <= 0) {
                    break;
                }

                if ($freebie->quantity <= $diff) {
                    $keysToRemove[] = $freebie->id;
                    $diff -= $freebie->quantity;
                } else {
                    $cart->update($freebie->id, ['quantity' => -$diff]);
                    $diff = 0;
                }
            }

            foreach ($keysToRemove as $id) {
                $cart->remove($id);
            }
        }
    }

    public function addBirthdayGift(int $productId): void
    {
        // 1. ตรวจสอบว่ามี "ของขวัญวันเกิด" อยู่ในตะกร้าแล้วหรือยัง (ป้องกันการเพิ่มซ้ำหากกดลิงก์ซ้ำ)
        $items = $this->getCartContents();
        $hasBirthdayGift = $items->contains(function ($item) {
            return $item->attributes['is_birthday_gift'] ?? false;
        });

        if ($hasBirthdayGift) {
            return; // ถ้ามีของขวัญวันเกิดในตะกร้าแล้ว ให้ข้ามการเพิ่มใหม่
        }

        // 2. ถ้ายังไม่มี จึงค่อยเพิ่มเข้าตะกร้า
        $this->addBundle(0, 0, [$productId], 1, true);
    }

    public function getApplicablePromotions(Collection $cartItems): Collection
    {
        if ($cartItems->isEmpty()) {
            return collect();
        }

        $now = now();
        $subTotal = 0;
        foreach ($cartItems as $item) {
            if (! ($item->attributes['is_freebie'] ?? false)) {
                $subTotal += ($item->price * $item->quantity);
            }
        }

        $cartQuantities = [];
        foreach ($cartItems as $item) {
            if ($item->attributes['is_freebie'] ?? false) {
                continue;
            }

            $realPid = $item->attributes['product_id'] ?? $item->id;
            if (is_string($realPid) && str_contains($realPid, '_')) {
                $realPid = explode('_', $realPid)[0];
            }
            $realPid = (int) $realPid;
            $cartQuantities[$realPid] = ($cartQuantities[$realPid] ?? 0) + $item->quantity;
        }
        $cartQuantities = collect($cartQuantities);
        $cartProductIds = $cartQuantities->keys()->toArray();

        $potentialPromotionIds = PromotionRule::where(function ($q) use ($cartProductIds) {
            foreach ($cartProductIds as $id) {
                $q->orWhereJsonContains('rules->product_id', (int) $id)
                    ->orWhereJsonContains('rules->product_id', (string) $id);
            }
        })->pluck('promotion_id')->unique();

        // ดึงข้อมูลโปรโมชั่นเบื้องต้นเพื่อแยกประเภท
        $appliedCode = $this->getAppliedPromoCode();
        
        // กรองเอาเฉพาะโปรโมชั่นที่:
        // 1. เป็นโปรโมชั่นอัตโนมัติ (is_discount_code = false)
        // 2. เป็นโปรโมชั่นที่ใช้รหัส และรหัสตรงกับที่ระบุ (is_discount_code = true และ code = appliedCode)
        $validPromotionIds = Promotion::whereIn('id', $potentialPromotionIds)
            ->where('is_active', true)
            ->where(function ($q) use ($appliedCode) {
                $q->where('is_discount_code', false);
                if (!empty($appliedCode)) {
                    $q->orWhere(function($sub) use ($appliedCode) {
                        $sub->where('is_discount_code', true)
                            ->where('code', $appliedCode);
                    });
                }
            })
            ->pluck('id');

        // กรณีโปรโมชั่นที่ไม่มีกฎ (เช่น โปรลดทั้งร้านที่ใช้โค้ด หรือ โปรลดอัตโนมัติทั้งร้าน)
        $noRulePromoIds = Promotion::where('is_active', true)
            ->whereDoesntHave('rules')
            ->where(function ($q) use ($appliedCode) {
                // 1. เป็นโปรโมชั่นอัตโนมัติ (ไม่ต้องใช้โค้ด)
                $q->where('is_discount_code', false);
                // 2. หรือเป็นโปรโมชั่นที่ใช้รหัส และรหัสตรงกับที่ระบุ
                if (!empty($appliedCode)) {
                    $q->orWhere(function($sub) use ($appliedCode) {
                        $sub->where('is_discount_code', true)
                            ->where('code', $appliedCode);
                    });
                }
            })
            ->pluck('id');

        $allPromoIds = $validPromotionIds->merge($noRulePromoIds)->unique();

        return Promotion::with(['rules', 'actions.giftableProducts'])->whereIn('id', $allPromoIds)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', $now))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $now))
            ->get()->filter(function ($promo) use ($cartQuantities, $subTotal) {

                if ($promo->min_order_value > 0 && $subTotal < (float) $promo->min_order_value) {
                    return false;
                }
                if ($promo->rules->isEmpty()) {
                    return true;
                }

                $promoMultipliers = [];
                foreach ($promo->rules as $rule) {
                    $pids = (array) ($rule->rules['product_id'] ?? []);
                    $reqQty = (int) ($rule->rules['quantity_to_buy'] ?? 1);

                    $totalMatched = 0;
                    foreach ($pids as $pid) {
                        $totalMatched += $cartQuantities->get((int) $pid, 0);
                    }

                    $promoMultipliers[] = $reqQty > 0 ? floor($totalMatched / $reqQty) : 0;
                }

                $finalMultiplier = ($promo->condition_type === 'all')
                    ? (empty($promoMultipliers) ? 0 : min($promoMultipliers))
                    : array_sum($promoMultipliers);

                if ($finalMultiplier > 0) {
                    $promo->multiplier = $finalMultiplier;

                    return true;
                }

                return false;
            });
    }

    private function saveCartToDatabase(int|string $userId, $cartContent): void
    {
        if (is_numeric($userId)) {
            CartStorage::updateOrCreate(['user_id' => $userId], ['cart_data' => $cartContent->toJson()]);
        }
    }

    private function restoreCartFromDatabase(int $userId): void
    {
        try {
            $saved = CartStorage::where('user_id', $userId)->first();
            if ($saved && $saved->cart_data) {
                $data = is_string($saved->cart_data) ? json_decode($saved->cart_data, true) : $saved->cart_data;
                if (is_array($data)) {
                    $cart = Cart::session($userId);
                    foreach ($data as $key => $item) {
                        if (is_array($item) && isset($item['id'])) {
                            // ดึง ID จริงมาแทนที่ถ้าเป็นของแถม
                            $realId = $item['attributes']['product_id'] ?? $item['id'];
                            $productModel = ProductSalepage::with('images')->find($realId);
                            if ($productModel) {
                                $item['associatedModel'] = $productModel;
                                $cart->add($item);
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            Log::error('Failed to restore cart: '.$e->getMessage());
        }
    }
}