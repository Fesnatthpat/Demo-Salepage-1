<?php

namespace App\Services;

use App\Models\CartStorage;
use App\Models\ProductSalepage;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CartService
{
    protected bool $cartLoadedFromDb = false;

    public function __construct(protected PromotionService $promotionService) {}

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
            $product = ProductSalepage::with('options')->find($productId);
            if (! $product) {
                throw new Exception("ไม่พบสินค้า ID: {$productId}");
            }

            if ($product->options->isNotEmpty()) {
                throw new Exception("กรุณาเลือกตัวเลือกสำหรับสินค้า '{$product->pd_sp_name}'");
            }

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

        $promoGroupId = 'bundle_'.Str::uuid();

        // 1. จัดการสินค้าหลัก
        if ($mainProductId > 0) {
            $mainProduct = $this->checkStockAndGetProduct($mainProductId, $qty);
            $mainDetails = $this->getProductDetails($mainProductId);
            if ($mainDetails) {
                $cartId = "{$mainDetails->id}-bundle-{$promoGroupId}";
                $cart->add([
                    'id' => $cartId,
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
                        'original_price' => $mainDetails->original_price,
                        'discount' => $mainDetails->discount,
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
                $cartId = "{$secDetails->id}-bundle-{$promoGroupId}";
                $cart->add([
                    'id' => $cartId,
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
                        'original_price' => $secDetails->original_price,
                        'discount' => $secDetails->discount,
                    ],
                    'associatedModel' => $secProduct,
                ]);
            }
        }

        // 3. จัดการของแถม
        $giftProducts = ProductSalepage::whereIn('pd_sp_id', $giftIds)->with(['images', 'stock'])->get()->keyBy('pd_sp_id');

        foreach ($giftIds as $giftId) {
            $giftProduct = $giftProducts->get($giftId);
            if (! $giftProduct) {
                continue;
            }

            // 📦 Stock Check for Gift
            if (($giftProduct->pd_sp_stock ?? 0) <= 0) {
                throw new Exception("ขออภัย! สินค้าของแถม '{$giftProduct->pd_sp_name}' หมดสต็อกแล้ว");
            }

            $imgPath = $giftProduct->images->first()?->img_path;
            if ($imgPath && ! filter_var($imgPath, FILTER_VALIDATE_URL)) {
                $imgPath = asset('storage/'.ltrim(str_replace('storage/', '', $imgPath), '/'));
            }

            $cartId = $giftProduct->pd_sp_id.($isBirthday ? '_birthday' : '_free')."-{$promoGroupId}";

            $cart->add([
                'id' => $cartId,
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

    public function removeItem(string|int $cartKey): void
    {
        $this->removeItems([$cartKey]);
    }

    public function removeItems(array $cartKeys): void
    {
        $userId = $this->getUserId();
        $this->getCartContents();
        $cart = Cart::session($userId);

        $keysToDelete = [];

        foreach ($cartKeys as $key) {
            $item = $cart->get($key);
            if (! $item) {
                continue;
            }

            $promoGroupId = $item->attributes['promo_group_id'] ?? null;
            $isFreebie = $item->attributes['is_freebie'] ?? false;

            if ($promoGroupId && ! $isFreebie) {
                foreach ($cart->getContent() as $k => $cartItem) {
                    if (($cartItem->attributes['promo_group_id'] ?? null) === $promoGroupId) {
                        $keysToDelete[] = $k;
                    }
                }
            } else {
                $keysToDelete[] = $key;
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

    public function updateQuantity(string|int $cartKey, string $action): void
    {
        $userId = $this->getUserId();
        $this->getCartContents();
        $cart = Cart::session($userId);

        $item = $cart->get($cartKey);
        if (! $item) {
            return;
        }

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

            $cart->update($cartKey, ['quantity' => 1]);
        } else {
            if ($item->quantity > 1) {
                $cart->update($cartKey, ['quantity' => -1]);
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

        $orderDiscount = $this->promotionService->calculateTotalDiscount($subTotal, $itemsToCalculate);
        $finalTotal = max(0, $subTotal - $orderDiscount);

        $productIds = $allItems->map(function ($item) {
            return $item->attributes['product_id'] ?? $item->id;
        })->unique()->toArray();

        $products = ProductSalepage::with(['images', 'stock'])->whereIn('pd_sp_id', $productIds)->get()->keyBy('pd_sp_id');

        $allApplicablePromotions = $this->promotionService->getApplicablePromotions($itemsToCalculate);

        $rawFreebieLimit = $this->promotionService->calculateFreebieLimit($itemsToCalculate, $allApplicablePromotions);

        $existingFreebiesCount = $allItems->filter(fn ($item) => $item->attributes->get('is_freebie'))->sum('quantity');
        $freebieLimit = max(0, $rawFreebieLimit - $existingFreebiesCount);

        $giftableProducts = $allApplicablePromotions->flatMap(function ($promo) {
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
            'applicablePromotions' => $allApplicablePromotions,
            'giftableProducts' => $giftableProducts,
            'freebieLimit' => $freebieLimit,
        ];
    }

    public function getPromotionsForProduct(int $productId): Collection
    {
        return $this->promotionService->getPromotionsForProduct($productId);
    }

    public function calculateFreebieLimit(?Collection $cartItems = null, ?Collection $applicablePromotions = null): int
    {
        return $this->promotionService->calculateFreebieLimit($cartItems ?? $this->getCartContents(), $applicablePromotions);
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
        $discount = $this->promotionService->calculateTotalDiscount($subTotal, $this->getCartContents());

        return max(0, $subTotal - $discount);
    }

    public function calculateTotalDiscount(float $subTotal, ?Collection $specificItems = null): float
    {
        return $this->promotionService->calculateTotalDiscount($subTotal, $specificItems ?? $this->getCartContents());
    }

    public function applyPromoCode(string $code): void
    {
        $this->promotionService->applyPromoCode($code);
    }

    public function removePromoCode(): void
    {
        $this->promotionService->removePromoCode();
    }

    public function getAppliedPromoCode(): ?string
    {
        return $this->promotionService->getAppliedPromoCode();
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
            'image' => $imgPath, 'pd_code' => $product->pd_sp_code, 'stock' => $product->pd_sp_stock ?? 0,
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
        // 1. พยายามหาตะกร้าจากหลายแหล่งที่อาจจะเป็นไปได้
        $possibleKeys = [
            $guestSessionKey,
            '_guest_' . $guestSessionKey,
            str_replace('_guest_', '', $guestSessionKey)
        ];

        $guestItems = collect();
        foreach (array_unique($possibleKeys) as $key) {
            $items = Cart::session($key)->getContent();
            if ($items->isNotEmpty()) {
                $guestItems = $items;
                $guestCart = Cart::session($key);
                break;
            }
        }
        
        // จัดการเรื่อง Promo Code
        $guestPromoCode = null;
        foreach (array_unique($possibleKeys) as $key) {
            $code = session("cart_{$key}_promo_code");
            if ($code) {
                $guestPromoCode = $code;
                session()->forget("cart_{$key}_promo_code");
                break;
            }
        }
        
        if ($guestPromoCode) {
            session(["cart_{$userId}_promo_code" => $guestPromoCode]);
        }

        if ($guestItems->isEmpty()) {
            return;
        }

        // 2. เตรียมตะกร้าของ User
        $userCart = Cart::session($userId);
        
        $productIds = $guestItems->map(function($item) {
            return $item->attributes['product_id'] ?? $item->id;
        })->unique()->toArray();
        
        $guestProducts = ProductSalepage::whereIn('pd_sp_id', $productIds)->with('images')->get()->keyBy('pd_sp_id');

        foreach ($guestItems as $guestItem) {
            try {
                $realProductId = $guestItem->attributes['product_id'] ?? $guestItem->id;
                $product = $guestProducts->get($realProductId);
                
                if (!$product) continue;

                $isFreebie = $guestItem->attributes['is_freebie'] ?? false;

                // เพิ่มเข้าตะกร้า User (ถ้ามีอยู่แล้วให้บวกจำนวนเพิ่ม หรือทับไปเลย)
                $userCart->add([
                    'id' => $guestItem->id,
                    'name' => $guestItem->name,
                    'price' => $guestItem->price,
                    'quantity' => $guestItem->quantity,
                    'attributes' => $guestItem->attributes->toArray(),
                    'associatedModel' => $product,
                ]);
                
            } catch (Exception $e) {
                Log::error("Merge Cart Item Error: " . $e->getMessage());
                continue;
            }
        }
        
        // 3. บันทึกลง Database ทันทีเพื่อให้หน้า Checkout ดึงไปใช้ได้ชัวร์ๆ
        $this->saveCartToDatabase($userId, $userCart->getContent());
        
        // 4. ล้างสถานะเพื่อให้ระบบโหลดข้อมูลใหม่จาก DB ในครั้งต่อไปที่เรียกใช้งาน
        $this->cartLoadedFromDb = false;
        $this->restoreCartFromDatabase($userId);
        
        // 5. ล้างตะกร้า Guest ทิ้ง
        if (isset($guestCart)) {
            $guestCart->clear();
        }
    }

    private function validateFreebieConsistency(int|string $userId): void
    {
        $cart = Cart::session($userId);
        $items = $cart->getContent();

        $freebies = $items->filter(fn ($item) => 
            ($item->attributes['is_freebie'] ?? false) && 
            !($item->attributes['is_birthday_gift'] ?? false) &&
            !($item->attributes['promo_group_id'] ?? false)
        );
        
        if ($freebies->isEmpty()) {
            return;
        }

        $limit = $this->promotionService->calculateFreebieLimit($items);
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
        $items = $this->getCartContents();
        $hasBirthdayGift = $items->contains(function ($item) {
            return $item->attributes['is_birthday_gift'] ?? false;
        });

        if ($hasBirthdayGift) {
            return;
        }

        $this->addBundle(0, 0, [$productId], 1, true);
    }

    public function getApplicablePromotions(Collection $cartItems): Collection
    {
        return $this->promotionService->getApplicablePromotions($cartItems);
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
