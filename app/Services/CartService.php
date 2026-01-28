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
use Exception;
use Illuminate\Support\Facades\Log;

class CartService
{
    protected bool $cartLoadedFromDb = false;

    // -------------------------------------------------------------------------
    //  Helpers (ฟังก์ชันช่วยค้นหา Key จริง)
    // -------------------------------------------------------------------------

    /**
     * ค้นหา "Cart Keys" (รหัสแถวในตะกร้า) ทั้งหมดที่ตรงกับ Product ID
     * (คืนค่าเป็น Array เพราะสินค้าเดียวกันอาจมีหลายแถว)
     */
    private function findCartKeys(int $productId): array
    {
        $userId = $this->getUserId();
        $keys = [];
        // วนลูปเช็คทีละรายการว่า ID ตรงกับที่เราหาไหม
        foreach (Cart::session($userId)->getContent() as $key => $item) {
            if ($item->id == $productId) {
                $keys[] = $key; // เก็บ Key จริงๆ ไว้ (เช่น 8_xxxyyy...)
            }
        }
        return $keys;
    }

    // -------------------------------------------------------------------------
    //  Main Features
    // -------------------------------------------------------------------------

    public function addOrUpdate(int $productId, int $quantity): void
    {
        $product = $this->checkStockAndGetProduct($productId, $quantity);
        $details = $this->getProductDetails($productId);

        if ($details) {
            $userId = $this->getUserId();
            $cart = Cart::session($userId);
            
            // หาของเดิมเพื่อก็อปปี้ Attributes (ถ้ามี)
            $existingKeys = $this->findCartKeys($productId);
            $existingItem = !empty($existingKeys) ? $cart->get($existingKeys[0]) : null;
            
            $newAttributes = [
                'image' => $details->image,
                'original_price' => $details->original_price,
                'discount' => $details->discount,
                'pd_code' => $details->pd_code,
            ];

            // รักษา Group ID เดิมไว้ ถ้าเป็นการเพิ่มสินค้าเดิม
            if ($existingItem) {
                foreach (['promo_group_id', 'is_condition_item', 'item_type', 'is_freebie'] as $attr) {
                    if (isset($existingItem->attributes[$attr])) {
                        $newAttributes[$attr] = $existingItem->attributes[$attr];
                    }
                }
            }

            // Darryldecode จะจัดการบวกจำนวนให้อัตโนมัติถ้า Attributes ตรงกัน
            $cart->add([
                'id' => $details->id,
                'name' => $details->name,
                'price' => $details->price,
                'quantity' => $quantity,
                'attributes' => $newAttributes,
                'associatedModel' => $product,
            ]);

            if (Auth::check()) $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    public function addWithGifts(int $productId, int $quantity, array $giftIds): void
    {
        $this->addBundle($productId, 0, $giftIds, $quantity);
    }

    /**
     * ✅ Add Bundle แบบ Deep Clean: ลบของเก่าด้วย Key จริง ก่อนใส่ใหม่ (แก้เลขเบิ้ล 100%)
     */
    public function addBundle(int $mainProductId, int $secondaryProductId, array $giftIds = [], int $qty = 1): void
    {
        $userId = $this->getUserId();
        $cart = Cart::session($userId);
        
        // 1. รวบรวม ID สินค้าทั้งหมดในโปรโมชั่นนี้
        $allInvolvedIds = [$mainProductId];
        if ($secondaryProductId > 0) $allInvolvedIds[] = $secondaryProductId;
        foreach ($giftIds as $gid) $allInvolvedIds[] = $gid;

        // 2. ลบสินค้าเหล่านี้ออกจากตะกร้าให้เกลี้ยง (โดยใช้ Key จริง)
        foreach ($allInvolvedIds as $pId) {
            $keysToRemove = $this->findCartKeys($pId);
            foreach ($keysToRemove as $k) {
                $cart->remove($k);
            }
        }

        // 3. สร้าง Group ID ใหม่
        $promoGroupId = 'bundle_'.Str::uuid();

        // 4. เพิ่มสินค้าหลัก
        $mainProduct = $this->checkStockAndGetProduct($mainProductId, $qty);
        $mainDetails = $this->getProductDetails($mainProductId);
        if ($mainDetails) {
            $cart->add([
                'id' => $mainDetails->id,
                'name' => $mainDetails->name,
                'price' => $mainDetails->price,
                'quantity' => $qty, // บังคับจำนวน
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

        // 5. เพิ่มสินค้ารอง
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

        // 6. เพิ่มของแถม
        foreach ($giftIds as $giftId) {
            $giftProduct = ProductSalepage::find($giftId);
            $giftDetails = $this->getProductDetails($giftId);
            if ($giftDetails && $giftProduct) {
                $cart->add([
                    'id' => $giftDetails->id,
                    'name' => $giftDetails->name.' (ของแถม)',
                    'price' => 0,
                    'quantity' => 1,
                    'attributes' => [
                        'image' => $giftDetails->image,
                        'pd_code' => $giftDetails->pd_code,
                        'is_freebie' => true,
                        'promo_group_id' => $promoGroupId,
                    ],
                    'associatedModel' => $giftProduct,
                ]);
            }
        }

        if (Auth::check()) $this->saveCartToDatabase($userId, $cart->getContent());
    }

    /**
     * ✅ Remove Item แบบใช้ Key จริง (แก้ปัญหาลบไม่ออก / ลบแล้วหายไม่หมด)
     */
    public function removeItem(int $productId): void
    {
        $userId = $this->getUserId();
        $cart = Cart::session($userId);
        
        // 1. หา Key จริงของสินค้านี้จาก ID
        $targetKeys = $this->findCartKeys($productId);

        if (empty($targetKeys)) return; // ไม่เจอสินค้า

        // หยิบตัวแรกมาเช็ค Group (สมมติว่าถ้ามีหลายตัว ก็ Group เดียวกัน)
        $firstItem = $cart->get($targetKeys[0]);
        $promoGroupId = $firstItem->attributes['promo_group_id'] ?? null;
        $isFreebie = $firstItem->attributes['is_freebie'] ?? false;

        // ถ้าเป็นสินค้า Bundle -> ลบยกแก๊งโดยดู Group ID
        if ($promoGroupId && ! $isFreebie) {
            // วนลูปตะกร้าเพื่อหาเพื่อนร่วมกลุ่ม แล้วลบด้วย Key ของมัน
            foreach ($cart->getContent() as $key => $cartItem) {
                $itemGroupId = $cartItem->attributes['promo_group_id'] ?? null;
                
                // ถ้า Group ID ตรงกัน ให้ลบโดยใช้ Key ของมัน (ไม่ใช่ ID)
                if ($itemGroupId === $promoGroupId) {
                    $cart->remove($key); 
                }
            }
        } else {
            // ถ้าเป็นสินค้าปกติ -> ลบเฉพาะ Key ที่เจอ
            foreach ($targetKeys as $key) {
                $cart->remove($key);
            }
        }

        $this->validateFreebieConsistency($userId);

        if (Auth::check()) $this->saveCartToDatabase($userId, $cart->getContent());
    }

    public function updateQuantity(int $productId, string $action): void
    {
        $userId = $this->getUserId();
        $cart = Cart::session($userId);
        $qty = ($action === 'increase') ? 1 : -1;
        
        // ใช้ Key จริงในการ update
        $keys = $this->findCartKeys($productId);
        foreach ($keys as $key) {
            $cart->update($key, ['quantity' => $qty]);
        }
        
        $this->validateFreebieConsistency($userId);
        if (Auth::check()) $this->saveCartToDatabase($userId, $cart->getContent());
    }

    // -------------------------------------------------------------------------
    //  Standard Getters & Logic (ส่วนนี้เหมือนเดิม)
    // -------------------------------------------------------------------------

    public function getCartDataForView(): array
    {
        $items = $this->getCartContents();
        $total = $this->getTotal();
        $productIds = $items->pluck('id')->toArray();
        $products = ProductSalepage::with('images')->whereIn('pd_sp_id', $productIds)->get()->keyBy('pd_sp_id');
        $applicablePromotions = $this->getApplicablePromotions($items);
        $freebieLimit = $this->calculateFreebieLimit($items, $applicablePromotions);
        $giftableProducts = $applicablePromotions->flatMap(function ($promo) {
            return $promo->actions->flatMap(function ($action) {
                $gifts = collect();
                if ($action->productToGet) $gifts->push($action->productToGet);
                if ($action->giftableProducts->isNotEmpty()) $gifts = $gifts->merge($action->giftableProducts);
                return $gifts;
            });
        })->unique('pd_sp_id');
        return compact('items', 'total', 'products', 'applicablePromotions', 'giftableProducts', 'freebieLimit');
    }

    public function getPromotionsForProduct(int $productId): Collection
    {
        $now = now();
        $promotionIds = PromotionRule::where(function ($q) use ($productId) {
            $q->whereJsonContains('rules->product_id', (string) $productId)
                ->orWhereJsonContains('rules->product_id', (int) $productId);
        })->pluck('promotion_id')->unique();
        if ($promotionIds->isEmpty()) return collect();
        return Promotion::with(['rules', 'actions.giftableProducts'])
            ->whereIn('id', $promotionIds)->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', $now))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $now))->get();
    }

    public function calculateFreebieLimit(?Collection $cartItems = null, ?Collection $applicablePromotions = null): int
    {
        $items = $cartItems ?? $this->getCartContents();
        $promos = $applicablePromotions ?? $this->getApplicablePromotions($items);
        if ($promos->isEmpty()) return 0;
        return $promos->sum(function ($promo) {
            $multiplier = $promo->multiplier ?? 1;
            return $promo->actions->sum(fn($action) => (int) ($action->actions['quantity_to_get'] ?? 0) * $multiplier);
        });
    }

    public function getUserId(): string|int { return Auth::check() ? Auth::id() : '_guest_'.session()->getId(); }

    public function getCartContents(): Collection
    {
        $userId = $this->getUserId();
        if (Auth::check() && ! $this->cartLoadedFromDb) {
            $sessionCart = Cart::session($userId)->getContent();
            if ($sessionCart->isEmpty()) $this->restoreCartFromDatabase($userId);
            $this->cartLoadedFromDb = true;
        }
        return Cart::session($userId)->getContent()->sort();
    }
    
    public function getCartItems(): Collection { return $this->getCartContents(); }
    public function getTotal(): float { return Cart::session($this->getUserId())->getTotal(); }
    public function getTotalQuantity(): int { return Cart::session($this->getUserId())->getTotalQuantity(); }

    private function getProductDetails(int $productId): ?object
    {
        $product = ProductSalepage::with('images')->find($productId);
        if (! $product) return null;
        $price = max(0, (float) $product->pd_sp_price - (float) $product->pd_sp_discount);
        $img = $product->images->first();
        $imgPath = $img ? ($img->img_path ?? $img->image_path) : null;
        if ($imgPath && ! filter_var($imgPath, FILTER_VALIDATE_URL)) {
            $imgPath = asset('storage/'.ltrim(str_replace('storage/', '', $imgPath), '/'));
        }
        return (object) [
            'id' => $product->pd_sp_id, 'name' => $product->pd_sp_name, 'price' => $price,
            'original_price' => (float) $product->pd_sp_price, 'discount' => (float) $product->pd_sp_discount,
            'image' => $imgPath, 'pd_code' => $product->pd_code, 'stock' => $product->pd_sp_stock ?? 0,
        ];
    }

    private function checkStockAndGetProduct(int $productId, int $quantity)
    {
        $product = ProductSalepage::find($productId);
        if (! $product) throw new Exception("ไม่พบสินค้า ID: {$productId}");
        // เช็คสต็อก: สำหรับ Bundle เราจะลบของเก่าออกก่อน ดังนั้นเช็คเทียบกับ quantity ใหม่ได้เลย
        if ($quantity > $product->pd_sp_stock) throw new Exception("สินค้า '{$product->pd_sp_name}' มีไม่เพียงพอ");
        return $product;
    }

    public function addFreebies(array $freebieIds): void
    {
        $this->addBundle(0, 0, $freebieIds); 
    }
    
    public function mergeGuestCart(string $guestSessionKey, int $userId): void
    {
        $guestCartId = '_guest_'.$guestSessionKey;
        $guestCart = Cart::session($guestCartId);
        $guestItems = $guestCart->getContent();
        if ($guestItems->isEmpty()) return;

        $this->restoreCartFromDatabase($userId);
        $userCart = Cart::session($userId);

        foreach ($guestItems as $guestItem) {
            try {
                $this->checkStockAndGetProduct($guestItem->id, $guestItem->quantity);
                if ($userCart->has($guestItem->id)) {
                    $userCart->update($guestItem->id, ['quantity' => $guestItem->quantity]);
                } else {
                    $details = $this->getProductDetails($guestItem->id);
                    if ($details) {
                        $userCart->add([
                            'id' => $details->id, 'name' => $details->name, 'price' => $details->price, 'quantity' => $guestItem->quantity,
                            'attributes' => [
                                'image' => $details->image, 'original_price' => $details->original_price,
                                'discount' => $details->discount, 'pd_code' => $details->pd_code,
                            ],
                            'associatedModel' => ProductSalepage::find($guestItem->id),
                        ]);
                    }
                }
            } catch (Exception $e) { continue; }
        }
        $this->saveCartToDatabase($userId, $userCart->getContent());
        $guestCart->clear();
    }

    private function validateFreebieConsistency(int|string $userId): void
    {
        $cart = Cart::session($userId);
        $items = $cart->getContent();
        $limit = $this->calculateFreebieLimit($items);
        $freebies = $items->filter(fn($item) => $item->attributes['is_freebie'] ?? false)->sort();
        $currentFreebieQty = $freebies->sum('quantity');
        if ($currentFreebieQty > $limit) {
            $diff = $currentFreebieQty - $limit;
            foreach ($freebies->reverse() as $freebie) {
                if ($diff <= 0) break;
                $qtyToRemove = min($diff, $freebie->quantity);
                ($qtyToRemove >= $freebie->quantity) ? $cart->remove($freebie->id) : $cart->update($freebie->id, ['quantity' => -$qtyToRemove]);
                $diff -= $qtyToRemove;
            }
        }
    }

    private function getApplicablePromotions(Collection $cartItems): Collection
    {
        if ($cartItems->isEmpty()) return collect();
        $cartProductIds = $cartItems->pluck('id')->toArray();
        $cartQuantities = $cartItems->pluck('quantity', 'id');
        $potentialPromotionIds = PromotionRule::where(function ($q) use ($cartProductIds) {
            foreach ($cartProductIds as $id) $q->orWhereJsonContains('rules->product_id', (int) $id)->orWhereJsonContains('rules->product_id', (string) $id);
        })->pluck('promotion_id')->unique();
        return Promotion::with(['rules', 'actions'])->whereIn('id', $potentialPromotionIds)->where('is_active', true)->get()->filter(function ($promo) use ($cartQuantities) {
            $promoMultipliers = [];
            foreach ($promo->rules as $rule) {
                $pids = (array) ($rule->rules['product_id'] ?? []);
                $reqQty = (int) ($rule->rules['quantity_to_buy'] ?? 1);
                $totalMatched = 0;
                foreach ($pids as $pid) $totalMatched += $cartQuantities->get((int) $pid, 0);
                $promoMultipliers[] = $reqQty > 0 ? floor($totalMatched / $reqQty) : 0;
            }
            $finalMultiplier = ($promo->condition_type === 'all') ? (empty($promoMultipliers) ? 0 : min($promoMultipliers)) : array_sum($promoMultipliers);
            if ($finalMultiplier > 0) { $promo->multiplier = $finalMultiplier; return true; }
            return false;
        });
    }

    private function saveCartToDatabase(int|string $userId, $cartContent): void
    {
        if (is_numeric($userId)) CartStorage::updateOrCreate(['user_id' => $userId], ['cart_data' => $cartContent->toJson()]);
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
                        } else { Log::warning("Skipping invalid cart item for user {$userId}", ['item' => $item]); }
                    }
                }
            }
        } catch (Exception $e) { Log::error("Failed to restore cart: " . $e->getMessage()); }
    }
}