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

    public function addBundle(int $mainProductId, int $secondaryProductId, array $giftIds = [], int $qty = 1): void
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
                'id' => $giftProduct->pd_sp_id.'_free',
                'name' => $giftProduct->pd_sp_name.' (ของแถม)',
                'price' => 0,
                'quantity' => 1,
                'attributes' => [
                    'image' => $imgPath,
                    'pd_code' => $giftProduct->pd_sp_code,
                    'is_freebie' => true,
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
        $userId = $this->getUserId();
        $this->getCartContents();
        $cart = Cart::session($userId);

        $targetKeys = $this->findCartKeys($productId);
        if (empty($targetKeys)) {
            return;
        }

        $firstItem = $cart->get($targetKeys[0]);
        $promoGroupId = $firstItem->attributes['promo_group_id'] ?? null;
        $isFreebie = $firstItem->attributes['is_freebie'] ?? false;

        $keysToDelete = [];
        if ($promoGroupId && ! $isFreebie) {
            foreach ($cart->getContent() as $key => $cartItem) {
                if (($cartItem->attributes['promo_group_id'] ?? null) === $promoGroupId) {
                    $keysToDelete[] = $key;
                }
            }
        } else {
            $keysToDelete = $targetKeys;
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
        $totalDiscount = 0;
        $appliedCode = $this->getAppliedPromoCode(); 

        foreach ($promos as $promo) {
            // เงื่อนไขที่ 1: เป็นโปรโมชั่นอัตโนมัติ (ไม่ได้ตั้งว่าเป็นโปรที่ต้องใช้รหัส) -> ให้ส่วนลดได้เลย
            $isAutoDiscount = !$promo->is_discount_code;
            
            // เงื่อนไขที่ 2: เป็นโปรโมชั่นที่ต้องใช้รหัส (Coupon Code) -> ต้องกรอกรหัสตรงกันเท่านั้น ถึงจะให้ส่วนลด
            $isMatchingCode = $promo->is_discount_code && !empty($appliedCode) && $promo->code === $appliedCode;

            // ถ้าเข้าเงื่อนไขใดเงื่อนไขหนึ่ง และมีการตั้งค่ามูลค่าส่วนลดไว้
            if ($promo->discount_value > 0 && ($isAutoDiscount || $isMatchingCode)) {
                if ($promo->discount_type === 'fixed') {
                    $totalDiscount += (float) $promo->discount_value;
                } elseif ($promo->discount_type === 'percentage') {
                    $totalDiscount += ($subTotal * ((float) $promo->discount_value / 100));
                }
            }
        }

        return $totalDiscount;
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
        if ($promo->usage_limit !== null && $promo->used_count >= $promo->usage_limit) {
            throw new Exception('ขออภัย! รหัสส่วนลดนี้ถูกใช้ครบจำนวนสิทธิ์แล้ว');
        }

        $userId = $this->getUserId();
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
        if ($guestItems->isEmpty()) {
            return;
        }

        $this->restoreCartFromDatabase($userId);
        $userCart = Cart::session($userId);
        $guestProducts = ProductSalepage::whereIn('pd_sp_id', $guestItems->pluck('id')->toArray())->with('images')->get()->keyBy('pd_sp_id');

        foreach ($guestItems as $guestItem) {
            try {
                $product = $guestProducts->get($guestItem->id);
                if (! $product || $guestItem->quantity > $product->pd_sp_stock) {
                    continue;
                }

                if ($userCart->has($guestItem->id)) {
                    $userCart->update($guestItem->id, ['quantity' => $guestItem->quantity]);
                } else {
                    $price = max(0, (float) $product->pd_sp_price - (float) $product->pd_sp_discount);
                    $img = $product->images->first();
                    $imgPath = $img ? ($img->img_path ?? $img->image_path) : null;
                    if ($imgPath && ! filter_var($imgPath, FILTER_VALIDATE_URL)) {
                        $imgPath = asset('storage/'.ltrim(str_replace('storage/', '', $imgPath), '/'));
                    }
                    $userCart->add([
                        'id' => $product->pd_sp_id, 'name' => $product->pd_sp_name, 'price' => $price,
                        'quantity' => $guestItem->quantity,
                        'attributes' => [
                            'image' => $imgPath, 'original_price' => (float) $product->pd_sp_price,
                            'discount' => (float) $product->pd_sp_discount, 'pd_code' => $product->pd_code,
                        ],
                        'associatedModel' => $product,
                    ]);
                }
            } catch (Exception $e) {
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

        $freebies = $items->filter(fn ($item) => $item->attributes['is_freebie'] ?? false);
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

        // กรณีโปรโมชั่นที่ไม่มีกฎ (เช่น โปรลดทั้งร้านที่ใช้โค้ด)
        $codeOnlyPromoIds = collect();
        if (!empty($appliedCode)) {
            $codeOnlyPromoIds = Promotion::where('is_active', true)
                ->where('is_discount_code', true)
                ->where('code', $appliedCode)
                ->pluck('id');
        }

        $allPromoIds = $validPromotionIds->merge($codeOnlyPromoIds)->unique();

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