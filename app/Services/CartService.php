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
    //  Helpers (ฟังก์ชันช่วยค้นหา Key จริง)
    // -------------------------------------------------------------------------

    /**
     * ค้นหา "Cart Keys" ทั้งหมดที่ตรงกับ Product ID
     */
    private function findCartKeys(string|int $productId): array
    {
        $userId = $this->getUserId();
        $keys = [];
        // ใช้ getCartContents() เพื่อมั่นใจว่าโหลดข้อมูลแล้ว
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
        $this->getCartContents(); // Ensure Cart Loaded
        $userId = $this->getUserId();
        $cart = Cart::session($userId);

        if ($optionId) {
            $option = \App\Models\ProductOption::find($optionId);
            if (! $option || $option->parent_id !== $productId) {
                throw new Exception('ตัวเลือกสินค้าไม่ถูกต้อง');
            }
            if ($quantity > $option->option_stock) {
                throw new Exception("สินค้าตัวเลือก '{$option->option_name}' มีไม่เพียงพอ");
            }

            $cartId = "{$productId}-{$optionId}";
            $cart->remove($cartId); // Remove existing to reset quantity

            $details = $this->getProductDetails($productId);
            $product = $this->checkStockAndGetProduct($productId, 0); // Check base product exists

            if ($details) {
                $cart->add([
                    'id' => $cartId, // Use composite ID
                    'name' => $details->name.' ('.$option->option_name.')',
                    'price' => $option->option_price,
                    'quantity' => $quantity,
                    'attributes' => [
                        'image' => $details->image,
                        'original_price' => $option->option_price,
                        'discount' => 0, // Options have final price
                        'pd_code' => $details->pd_code,
                        'product_id' => $productId, // Store original product ID
                        'option_id' => $optionId,
                    ],
                    'associatedModel' => $product,
                ]);
            }
        } else {
            // --- EXISTING LOGIC for products without options ---
            $existingKeys = $this->findCartKeys($productId);
            foreach ($existingKeys as $key) {
                $item = $cart->get($key);
                if (empty($item->attributes['promo_group_id'])) {
                    $cart->remove($key);
                }
            }

            $product = $this->checkStockAndGetProduct($productId, $quantity);
            $details = $this->getProductDetails($productId);

            if ($details) {
                $newAttributes = [
                    'image' => $details->image,
                    'original_price' => $details->original_price,
                    'discount' => $details->discount,
                    'pd_code' => $details->pd_code,
                ];

                $cart->add([
                    'id' => $details->id,
                    'name' => $details->name,
                    'price' => $details->price,
                    'quantity' => $quantity,
                    'attributes' => $newAttributes,
                    'associatedModel' => $product,
                ]);
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

    /**
     * ✅ Add Bundle: แก้ปัญหาเลขเบิ้ลโดยโหลดตะกร้าก่อนลบ
     */
    public function addBundle(int $mainProductId, int $secondaryProductId, array $giftIds = [], int $qty = 1): void
    {
        $userId = $this->getUserId();
        $this->getCartContents(); // ✅ 1. โหลดข้อมูลล่าสุดจาก DB ก่อนเสมอ (สำคัญมาก!)
        $cart = Cart::session($userId);

        // 2. รวบรวม ID สินค้าที่เกี่ยวข้อง
        $allInvolvedIds = [$mainProductId];
        if ($secondaryProductId > 0) {
            $allInvolvedIds[] = $secondaryProductId;
        }
        foreach ($giftIds as $gid) {
            $allInvolvedIds[] = $gid;
        }

        // 3. หา Keys ที่ต้องลบ (Collect Keys First)
        $keysToRemove = [];
        foreach ($cart->getContent() as $key => $item) {
            if (in_array($item->id, $allInvolvedIds)) {
                $keysToRemove[] = $key;
            }
        }

        // 4. ลบจริง (Remove After Collection)
        foreach ($keysToRemove as $k) {
            $cart->remove($k);
        }

        // 5. เพิ่มสินค้าใหม่ (Group ID เดียวกัน)
        $promoGroupId = 'bundle_'.Str::uuid();

        // Main Product
        $mainProduct = $this->checkStockAndGetProduct($mainProductId, $qty);
        $mainDetails = $this->getProductDetails($mainProductId);
        if ($mainDetails) {
            $cart->add([
                'id' => $mainDetails->id,
                'name' => $mainDetails->name,
                'price' => $mainDetails->price,
                'quantity' => [
                    'relative' => false,
                    'value' => $qty,
                ],
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

        // Secondary Product
        if ($secondaryProductId > 0) {
            $secProduct = $this->checkStockAndGetProduct($secondaryProductId, 1);
            $secDetails = $this->getProductDetails($secondaryProductId);
            if ($secDetails) {
                $cart->add([
                    'id' => $secDetails->id,
                    'name' => $secDetails->name,
                    'price' => $secDetails->price,
                    'quantity' => [
                        'relative' => false,
                        'value' => 1,
                    ],
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

        // Freebies
        $giftProducts = ProductSalepage::whereIn('pd_sp_id', $giftIds)->with('images')->get()->keyBy('pd_sp_id');

        foreach ($giftIds as $giftId) {
            $giftProduct = $giftProducts->get($giftId);
            if (! $giftProduct) {
                continue; // Skip if product not found
            }

            $imgPath = $giftProduct->images->first()?->img_path;
            if ($imgPath && ! filter_var($imgPath, FILTER_VALIDATE_URL)) {
                $imgPath = asset('storage/'.ltrim(str_replace('storage/', '', $imgPath), '/'));
            }

            $cart->add([
                'id' => $giftProduct->pd_sp_id,
                'name' => $giftProduct->pd_sp_name.' (ของแถม)',
                'price' => 0,
                'quantity' => [
                    'relative' => false,
                    'value' => 1,
                ],
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

    /**
     * ✅ Remove Item: แก้ปัญหาลบแล้วข้ามบางตัว (ใช้ Keys Collection)
     */
    public function removeItem(string|int $productId): void
    {
        $userId = $this->getUserId();
        $this->getCartContents(); // ✅ Ensure Loaded
        $cart = Cart::session($userId);

        // 1. หา Key ของตัวที่จะลบ
        $targetKeys = $this->findCartKeys($productId);
        if (empty($targetKeys)) {
            return;
        }

        $firstItem = $cart->get($targetKeys[0]);
        $promoGroupId = $firstItem->attributes['promo_group_id'] ?? null;
        $isFreebie = $firstItem->attributes['is_freebie'] ?? false;

        $keysToDelete = [];

        // 2. รวบรวม Key ที่จะลบทั้งหมด (ห้ามลบขณะวนลูป)
        if ($promoGroupId && ! $isFreebie) {
            foreach ($cart->getContent() as $key => $cartItem) {
                $itemGroupId = $cartItem->attributes['promo_group_id'] ?? null;
                if ($itemGroupId === $promoGroupId) {
                    $keysToDelete[] = $key;
                }
            }
        } else {
            // ลบเฉพาะตัวมันเอง
            $keysToDelete = $targetKeys;
        }

        // 3. สั่งลบจริงทีเดียว (หลังจากวนลูปเสร็จแล้ว)
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
        $qty = ($action === 'increase') ? 1 : -1;

        $keys = $this->findCartKeys($productId);
        foreach ($keys as $key) {
            $cart->update($key, ['quantity' => $qty]);
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
        $productIds = $items->pluck('id')->toArray();
        $products = ProductSalepage::with('images')->whereIn('pd_sp_id', $productIds)->get()->keyBy('pd_sp_id');
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
            $q->whereJsonContains('rules->product_id', (string) $productId)
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

    public function getCartItems(): Collection
    {
        return $this->getCartContents();
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
        $price = max(0, (float) $product->pd_sp_price - (float) $product->pd_sp_discount);
        $img = $product->images->first();
        $imgPath = $img ? ($img->img_path ?? $img->image_path) : null;
        if ($imgPath && ! filter_var($imgPath, FILTER_VALIDATE_URL)) {
            $imgPath = asset('storage/'.ltrim($imgPath, '/'));
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
        $guestCartId = '_guest_'.$guestSessionKey;
        $guestCart = Cart::session($guestCartId);
        $guestItems = $guestCart->getContent();
        if ($guestItems->isEmpty()) {
            return;
        }

        $this->restoreCartFromDatabase($userId);
        $userCart = Cart::session($userId);

        // Eager load all guest products to avoid N+1 problem
        $guestProductIds = $guestItems->pluck('id')->toArray();
        $guestProducts = ProductSalepage::whereIn('pd_sp_id', $guestProductIds)->with('images')->get()->keyBy('pd_sp_id');

        foreach ($guestItems as $guestItem) {
            try {
                // Find the product from the pre-loaded collection
                $product = $guestProducts->get($guestItem->id);

                // If product is not found or stock is insufficient, skip it
                if (! $product || $guestItem->quantity > $product->pd_sp_stock) {
                    continue;
                }

                if ($userCart->has($guestItem->id)) {
                    $userCart->update($guestItem->id, ['quantity' => $guestItem->quantity]);
                } else {
                    // Manually construct details similar to getProductDetails but using pre-loaded product
                    $price = max(0, (float) $product->pd_sp_price - (float) $product->pd_sp_discount);
                    $img = $product->images->first();
                    $imgPath = $img ? ($img->img_path ?? $img->image_path) : null;
                    if ($imgPath && ! filter_var($imgPath, FILTER_VALIDATE_URL)) {
                        $imgPath = asset('storage/'.ltrim(str_replace('storage/', '', $imgPath), '/'));
                    }

                    $userCart->add([
                        'id' => $product->pd_sp_id,
                        'name' => $product->pd_sp_name,
                        'price' => $price,
                        'quantity' => $guestItem->quantity,
                        'attributes' => [
                            'image' => $imgPath,
                            'original_price' => (float) $product->pd_sp_price,
                            'discount' => (float) $product->pd_sp_discount,
                            'pd_code' => $product->pd_code,
                        ],
                        'associatedModel' => $product,
                    ]);
                }
            } catch (Exception $e) {
                // Log the exception if needed, but continue merging other items
                Log::warning('Skipping guest cart item due to error: '.$e->getMessage(), ['guestItem' => $guestItem]);
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
                    $keysToRemove[] = $freebie->id; // เก็บ ID เพื่อไปหา Key
                } else {
                    $cart->update($freebie->id, ['quantity' => -$qtyToRemove]);
                }
                $diff -= $qtyToRemove;
            }

            foreach ($keysToRemove as $pid) {
                // หา Key แล้วลบ (ทำแบบ safe remove)
                $keys = $this->findCartKeys($pid);
                foreach ($keys as $k) {
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
        $cartProductIds = $cartItems->pluck('id')->toArray();
        $cartQuantities = $cartItems->pluck('quantity', 'id');
        $potentialPromotionIds = PromotionRule::where(function ($q) use ($cartProductIds) {
            foreach ($cartProductIds as $id) {
                $q->orWhereJsonContains('rules->product_id', (int) $id)->orWhereJsonContains('rules->product_id', (string) $id);
            }
        })->pluck('promotion_id')->unique();

        return Promotion::with(['rules', 'actions'])->whereIn('id', $potentialPromotionIds)->where('is_active', true)->get()->filter(function ($promo) use ($cartQuantities) {
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
            $finalMultiplier = ($promo->condition_type === 'all') ? (empty($promoMultipliers) ? 0 : min($promoMultipliers)) : array_sum($promoMultipliers);
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
                        } else {
                            Log::warning("Skipping invalid cart item for user {$userId}", ['item' => $item]);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            Log::error('Failed to restore cart: '.$e->getMessage());
        }
    }
}
