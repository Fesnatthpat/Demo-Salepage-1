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
            if ($item->id == $productId) {
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

            // ถ้ามีอยู่แล้วให้ใช้ update แทนการ remove/add เพื่อรักษาพฤติกรรมสะสม
            if ($existingItem) {
                $cart->update($cartId, [
                    'quantity' => $quantity // library ของ Darryldecode/Cart จะบวกเพิ่มให้เองถ้าใช้ update แบบปกติ
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
                        'quantity' => $quantity
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
        foreach ($giftIds as $gid) {
            $allInvolvedIds[] = $gid;
        }

        $keysToRemove = [];
        foreach ($cart->getContent() as $key => $item) {
            if (in_array($item->id, $allInvolvedIds)) {
                $keysToRemove[] = $key;
            }
        }
        foreach ($keysToRemove as $k) {
            $cart->remove($k);
        }

        $promoGroupId = 'bundle_'.Str::uuid();

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
                ],
                'associatedModel' => $mainProduct,
            ]);
        }

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
                    ],
                    'associatedModel' => $secProduct,
                ]);
            }
        }

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
                'id' => $giftProduct->pd_sp_id,
                'name' => $giftProduct->pd_sp_name.' (ของแถม)',
                'price' => 0,
                'quantity' => 1,
                'attributes' => [
                    'image' => $imgPath,
                    'pd_code' => $giftProduct->pd_sp_code,
                    'is_freebie' => true,
                    'promo_group_id' => $promoGroupId,
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
                // เช็คสต็อกก่อนเพิ่ม
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
                // ลดจำนวน (Library จะไม่ลดจนติดลบให้อัตโนมัติถ้าลดเกิน แต่เราใส่เผื่อไว้)
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

    // -------------------------------------------------------------------------
    //  Standard Getters & Logic
    // -------------------------------------------------------------------------

    public function getCartDataForView(): array
    {
        $items = $this->getCartContents();
        $total = $this->getTotal();
        
        $productIds = $items->map(function($item) {
            return $item->attributes['product_id'] ?? $item->id;
        })->unique()->toArray();

        $products = ProductSalepage::with(['images', 'stock'])->whereIn('pd_sp_id', $productIds)->get()->keyBy('pd_sp_id');
        $applicablePromotions = $this->getApplicablePromotions($items);
        $freebieLimit = $this->calculateFreebieLimit($items, $applicablePromotions);
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
        
        // คำนวณส่วนลดจากโปรโมชั่น
        $discount = $this->calculateTotalDiscount($subTotal);
        
        return max(0, $subTotal - $discount);
    }

    /**
     * คำนวณส่วนลดรวมจากโปรโมชั่น (ทั้งอัตโนมัติและรหัส)
     */
    public function calculateTotalDiscount(float $subTotal): float
    {
        $items = $this->getCartContents();
        if ($items->isEmpty()) return 0;

        $promos = $this->getApplicablePromotions($items);
        $totalDiscount = 0;

        foreach ($promos as $promo) {
            // เฉพาะโปรโมชั่นประเภทลดราคา (ไม่ใช่แถมฟรี)
            if ($promo->discount_value > 0) {
                if ($promo->discount_type === 'fixed') {
                    $totalDiscount += (float) $promo->discount_value;
                } elseif ($promo->discount_type === 'percentage') {
                    $totalDiscount += ($subTotal * ((float) $promo->discount_value / 100));
                }
            }
        }

        return $totalDiscount;
    }

    /**
     * ใช้รหัสส่วนลด
     */
    public function applyPromoCode(string $code): void
    {
        $promo = Promotion::where('code', $code)
            ->where('is_active', true)
            ->where('is_discount_code', true)
            ->where(fn($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', now()))
            ->where(fn($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()))
            ->first();

        if (!$promo) {
            throw new Exception("รหัสส่วนลดไม่ถูกต้อง หรือหมดอายุแล้ว");
        }

        if ($promo->usage_limit && $promo->used_count >= $promo->usage_limit) {
            throw new Exception("รหัสส่วนลดนี้ถูกใช้ครบจำนวนสิทธิ์แล้ว");
        }

        // บันทึกรหัสลงใน Session ของตะกร้า
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
        $limit = $this->calculateFreebieLimit($items);
        $freebies = $items->filter(fn ($item) => $item->attributes['is_freebie'] ?? false)->sort();
        $currentFreebieQty = $freebies->sum('quantity');

        if ($currentFreebieQty > $limit) {
            $diff = $currentFreebieQty - $limit;
            $keysToRemove = [];
            foreach ($freebies->reverse() as $freebie) {
                if ($diff <= 0) {
                    break;
                }
                $qtyToRemove = min($diff, $freebie->quantity);
                if ($qtyToRemove >= $freebie->quantity) {
                    $keysToRemove[] = $freebie->id;
                } else {
                    $cart->update($freebie->id, ['quantity' => -$qtyToRemove]);
                }
                $diff -= $qtyToRemove;
            }
            foreach ($keysToRemove as $pid) {
                foreach ($this->findCartKeys($pid) as $k) {
                    $cart->remove($k);
                }
            }
        }
    }

    private function getApplicablePromotions(Collection $cartItems): Collection
    {
        if ($cartItems->isEmpty()) {
            return collect();
        }

        $now = now();
        $subTotal = (float) Cart::session($this->getUserId())->getTotal();
        $cartProductIds = $cartItems->pluck('id')->toArray();
        $cartQuantities = $cartItems->pluck('quantity', 'id');

        // 1. ค้นหาโปรโมชั่นที่อาจจะใช้งานได้
        $potentialPromotionIds = PromotionRule::where(function ($q) use ($cartProductIds) {
            foreach ($cartProductIds as $id) {
                // ค้นหาทั้งแบบ product_id ตรงๆ หรืออยู่ใน Array ของ JSON
                $q->orWhereJsonContains('rules->product_id', (int) $id)
                    ->orWhereJsonContains('rules->product_id', (string) $id);
            }
        })->pluck('promotion_id')->unique();

        // 2. ดึงโปรโมชั่นประเภท Auto-Discount หรือรหัสที่กรอกไว้
        $appliedCode = $this->getAppliedPromoCode();
        $promoCodeIds = Promotion::where('is_active', true)
            ->where(function ($q) use ($appliedCode) {
                $q->where('is_discount_code', false) // Auto-Discount
                  ->orWhere('code', $appliedCode);   // รหัสที่ลูกค้ากรอก
            })
            ->pluck('id');

        $allPromoIds = $potentialPromotionIds->merge($promoCodeIds)->unique();

        return Promotion::with(['rules', 'actions'])->whereIn('id', $allPromoIds)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', $now))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $now))
            ->get()->filter(function ($promo) use ($cartQuantities, $subTotal) {
                
                // ตรวจสอบยอดซื้อขั้นต่ำ
                if ($promo->min_order_value > 0 && $subTotal < (float) $promo->min_order_value) {
                    return false;
                }

                // ถ้าเป็นโปรโมชั่นลดราคาเฉยๆ (ไม่มีกฎการซื้อสินค้าเฉพาะชิ้น) ให้ผ่านเลย
                if ($promo->rules->isEmpty()) {
                    return true;
                }

                $promoMultipliers = [];
                foreach ($promo->rules as $rule) {
                    // product_id ใน rules ตอนนี้เป็น Array ของ IDs (จากที่ผมแก้ไปเมื่อครู่)
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
                            $productModel = ProductSalepage::with('images')->find($item['id']);
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