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

        // Eager Load ข้อมูลสินค้าเพื่อลด N+1 Query
        $productIds = $items->pluck('id')->toArray();
        $products = ProductSalepage::with('images')
            ->whereIn('pd_sp_id', $productIds)
            ->get()
            ->keyBy('pd_sp_id');

        $applicablePromotions = $this->getApplicablePromotions($items);
        $freebieLimit = $this->calculateFreebieLimit($items, $applicablePromotions);

        // ดึงรายชื่อของแถมที่ได้รับสิทธิ์
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
     * ดึงโปรโมชั่นที่เกี่ยวข้องกับสินค้าชิ้นเดียว
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
     * ดึงข้อมูลตะกร้า (Auto-Restore จาก DB)
     */
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

    // Alias สำหรับการเรียกใช้งานแบบเก่า
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
            $imgPath = asset('storage/'.ltrim(str_replace('storage/', '', $imgPath), '/'));
        }

        return (object) [
            'id' => $product->pd_sp_id,
            'name' => $product->pd_sp_name,
            'price' => $price,
            'original_price' => (float) $product->pd_sp_price,
            'discount' => (float) $product->pd_sp_discount,
            'image' => $imgPath,
            'pd_code' => $product->pd_code,
            'stock' => $product->pd_sp_stock ?? 0,
        ];
    }

    private function checkStockAndGetProduct(int $productId, int $quantity)
    {
        $product = ProductSalepage::find($productId);
        if (! $product) {
            throw new Exception("ไม่พบสินค้า ID: {$productId}");
        }

        $userId = $this->getUserId();
        $cart = Cart::session($userId);
        $currentQty = $cart->has($productId) ? $cart->get($productId)->quantity : 0;

        if (($currentQty + $quantity) > $product->pd_sp_stock) {
            throw new Exception("สินค้า '{$product->pd_sp_name}' มีไม่เพียงพอ");
        }

        return $product;
    }

    /**
     * เพิ่มหรืออัปเดตสินค้าปกติ
     */
    public function addOrUpdate(int $productId, int $quantity): void
    {
        $product = $this->checkStockAndGetProduct($productId, $quantity);
        $details = $this->getProductDetails($productId);

        if ($details) {
            $userId = $this->getUserId();
            $cart = Cart::session($userId);

            $existingItem = $cart->get($productId);

            $newAttributes = [
                'image' => $details->image,
                'original_price' => $details->original_price,
                'discount' => $details->discount,
                'pd_code' => $details->pd_code,
            ];

            // เก็บค่า Attributes เดิมถ้ามี
            if ($existingItem) {
                $promoAttributes = [
                    'promo_group_id', 'is_condition_item', 'item_type', 'is_freebie',
                ];
                foreach ($promoAttributes as $attr) {
                    if (isset($existingItem->attributes[$attr])) {
                        $newAttributes[$attr] = $existingItem->attributes[$attr];
                    }
                }
            }

            $cart->add([
                'id' => $details->id,
                'name' => $details->name,
                'price' => $details->price,
                'quantity' => $quantity,
                'attributes' => $newAttributes,
                'associatedModel' => $product,
            ]);

            if (Auth::check()) {
                $this->saveCartToDatabase($userId, $cart->getContent());
            }
        }
    }

    /**
     * เพิ่มสินค้าพร้อมของแถม (สำหรับโปรโมชั่นซื้อครบ/แถม)
     */
    public function addWithGifts(int $productId, int $quantity, array $giftIds): void
    {
        $userId = $this->getUserId();
        $cart = Cart::session($userId);

        $product = $this->checkStockAndGetProduct($productId, $quantity);
        foreach (array_count_values($giftIds) as $gId => $count) {
            $this->checkStockAndGetProduct($gId, $count);
        }

        $promoGroupId = 'promo_'.Str::uuid();

        // เพิ่มสินค้าหลัก
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
                    'is_condition_item' => true,
                ],
                'associatedModel' => $product,
            ]);
        }

        // เพิ่มของแถม
        foreach ($giftIds as $giftId) {
            $giftDetails = $this->getProductDetails($giftId);
            $giftProduct = ProductSalepage::find($giftId);

            if ($giftDetails && $giftProduct) {
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
                        'is_freebie' => true,
                        'promo_group_id' => $promoGroupId,
                    ],
                    'associatedModel' => $giftProduct, // ✅ Fix associatedModel
                ]);
            }
        }

        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    /**
     * เพิ่มสินค้า Bundle (ซื้อคู่)
     */
    public function addBundle(int $mainProductId, int $secondaryProductId, array $giftIds = []): void
    {
        $userId = $this->getUserId();
        $cart = Cart::session($userId);
        $promoGroupId = 'bundle_'.Str::uuid();

        // 1. Main Product
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
                    'is_condition_item' => true,
                    'item_type' => 'main',
                ],
                'associatedModel' => ProductSalepage::find($mainProductId),
            ]);
        }

        // 2. Secondary Product
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
                    'is_condition_item' => true,
                    'item_type' => 'secondary',
                ],
                'associatedModel' => ProductSalepage::find($secondaryProductId),
            ]);
        }

        // 3. Freebies
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
                    'associatedModel' => $giftProduct, // ✅ Fix associatedModel
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
     * ลบสินค้าออกจากตะกร้า (ถ้าเป็นกลุ่ม Bundle จะลบออกทั้งหมด)
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

        // ถ้าสินค้าเป็นเงื่อนไขโปรฯ (ไม่ใช่ของแถม) ให้ลบทั้งกลุ่ม
        if ($promoGroupId && ! $isFreebie) {
            $itemsInGroup = $cart->getContent()->filter(function ($cartItem) use ($promoGroupId) {
                return ($cartItem->attributes['promo_group_id'] ?? null) === $promoGroupId;
            });

            foreach ($itemsInGroup as $groupItem) {
                $cart->remove($groupItem->id);
            }
        } else {
            // กรณีลบของแถม หรือ ลบสินค้าปกติ
            $cart->remove($productId);
        }

        $this->validateFreebieConsistency($userId);

        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    /**
     * แลกของแถม (Redeem Points etc.)
     */
    public function addFreebies(array $freebieIds): void
    {
        $userId = $this->getUserId();
        $this->getCartContents();
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
                    'associatedModel' => $product, // ✅ Fix associatedModel
                ]);
            }
        }

        $this->validateFreebieConsistency($userId);

        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
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

        foreach ($guestItems as $guestItem) {
            try {
                $this->checkStockAndGetProduct($guestItem->id, $guestItem->quantity);

                if ($userCart->has($guestItem->id)) {
                    $userCart->update($guestItem->id, ['quantity' => $guestItem->quantity]);
                } else {
                    $details = $this->getProductDetails($guestItem->id);
                    if ($details) {
                        $userCart->add([
                            'id' => $details->id,
                            'name' => $details->name,
                            'price' => $details->price,
                            'quantity' => $guestItem->quantity,
                            'attributes' => [
                                'image' => $details->image,
                                'original_price' => $details->original_price,
                                'discount' => $details->discount,
                                'pd_code' => $details->pd_code,
                            ],
                            'associatedModel' => ProductSalepage::find($guestItem->id),
                        ]);
                    }
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

        $freebies = $items->filter(function ($item) {
            return isset($item->attributes['is_freebie']) && $item->attributes['is_freebie'];
        })->sort();

        $currentFreebieQty = $freebies->sum('quantity');

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

    private function getApplicablePromotions(Collection $cartItems): Collection
    {
        if ($cartItems->isEmpty()) {
            return collect();
        }
        $cartProductIds = $cartItems->pluck('id')->toArray();
        $cartQuantities = $cartItems->pluck('quantity', 'id');

        $potentialPromotionIds = PromotionRule::where(function ($q) use ($cartProductIds) {
            foreach ($cartProductIds as $id) {
                $q->orWhereJsonContains('rules->product_id', (int) $id)
                    ->orWhereJsonContains('rules->product_id', (string) $id);
            }
        })->pluck('promotion_id')->unique();

        return Promotion::with(['rules', 'actions'])
            ->whereIn('id', $potentialPromotionIds)
            ->where('is_active', true)
            ->get()
            ->filter(function ($promo) use ($cartQuantities) {
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

    /**
     * ดึงข้อมูลจาก DB และ Auto-Heal (ซ่อมแซม associatedModel) ให้ทันที
     */
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
                            // 🔥 เพิ่มการดึง Model มาแปะใหม่ทุกครั้ง เพื่อแก้ปัญหาข้อมูลเก่าพัง
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
