<?php

namespace App\Services;

use App\Models\CartStorage;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\PromotionRule;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductSalepage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CartService
{
    /**
     * Get the current user's session ID for the cart.
     */
    public function getUserId(): string|int
    {
        return Auth::check() ? Auth::id() : '_guest_' . session()->getId();
    }

    /**
     * Get the content of the current user's cart.
     */
    public function getCartContents(): Collection
    {
        $userId = $this->getUserId();
        
        // For logged-in users, always restore from DB to ensure consistency
        if (Auth::check()) {
            $this->restoreCartFromDatabase($userId);
        }

        $cart = Cart::session($userId);
        return $cart->getContent()->sort();
    }
    
    /**
     * Get the total price of the cart.
     */
    public function getTotal(): float
    {
        $userId = $this->getUserId();
        return Cart::session($userId)->getTotal();
    }

    /**
     * Get the total quantity of items in the cart.
     */
    public function getTotalQuantity(): int
    {
        $userId = $this->getUserId();
        return Cart::session($userId)->getTotalQuantity();
    }

    private function getProductDetails(int $productId): ?object
    {
        $salePageProduct = ProductSalepage::with('images')->find($productId);

        if (!$salePageProduct) {
            return null; // System primarily uses ProductSalepage
        }

        $originalPrice = (float) $salePageProduct->pd_sp_price;
        $discountAmount = (float) $salePageProduct->pd_sp_discount;
        $finalSellingPrice = max(0, $originalPrice - $discountAmount);

        return (object) [
            'id' => $salePageProduct->pd_sp_id,
            'name' => $salePageProduct->pd_sp_name,
            'price' => $finalSellingPrice,
            'original_price' => $originalPrice,
            'discount' => $discountAmount,
            'image' => $salePageProduct->images->first()->image_path ?? null,
            'pd_code' => $salePageProduct->pd_code,
            'stock' => $salePageProduct->pd_sp_stock ?? 0,
        ];
    }

    /**
     * Add a standard product to the cart.
     */
    public function addOrUpdate(int $productId, int $quantity): void
    {
        $product = ProductSalepage::find($productId);
        if (!$product) return;

        if ($product->pd_sp_stock <= 0) {
            throw new \Exception('ขออภัย, สินค้าชิ้นนี้หมดสต็อกแล้ว');
        }

        $userId = $this->getUserId();
        $cart = Cart::session($userId);
        $currentQuantity = $cart->has($productId) ? $cart->get($productId)->quantity : 0;

        if (($currentQuantity + $quantity) > $product->pd_sp_stock) {
            throw new \Exception("ไม่สามารถเพิ่มสินค้าได้, มีสินค้าในสต็อกเพียง {$product->pd_sp_stock} ชิ้น");
        }

        $productDetails = $this->getProductDetails($productId);
        if (!$productDetails) return;

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
            ],
        ]);

        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    /**
     * Finds and returns all promotions for which the user's cart meets ALL conditions.
     */
    public function getApplicablePromotions(Collection $cartItems): Collection
    {
        if ($cartItems->isEmpty()) {
            return collect();
        }

        $now = now();
        $cartProductIds = $cartItems->pluck('id')->toArray();
        $cartQuantities = $cartItems->pluck('quantity', 'id');

        // 1. Find all potential promotions that *could* apply based on items in the cart
        $potentialPromotionIds = PromotionRule::whereIn('rules->product_id', $cartProductIds)
            ->pluck('promotion_id')
            ->unique();
            
        if ($potentialPromotionIds->isEmpty()) {
            return collect();
        }

        // 2. Eager-load these promotions with all their rules and actions
        $potentialPromotions = Promotion::with(['rules', 'actions.giftableProducts', 'actions.productToGet'])
            ->whereIn('id', $potentialPromotionIds)
            ->where('is_active', true)
            ->where(fn($q) => $q->where('start_date', '<=', $now)->orWhereNull('start_date'))
            ->where(fn($q) => $q->where('end_date', '>=', $now)->orWhereNull('end_date'))
            ->get();
        
        // 3. Filter down to only promotions where ALL rules are met by the cart contents
        $applicablePromotions = $potentialPromotions->filter(function ($promo) use ($cartQuantities) {
            // Assume the promotion is valid until a rule fails
            $allRulesMet = true;
            
            if ($promo->rules->isEmpty()) {
                return false;
            }

            foreach ($promo->rules as $rule) {
                // Cast the required product ID to an integer to ensure type-safe comparison
                $requiredProductId = (int)($rule->rules['product_id'] ?? null);
                $requiredQuantity = $rule->rules['quantity_to_buy'] ?? 1;

                // Check if the cart has the product and in the required quantity
                if (
                    !$requiredProductId ||
                    !$cartQuantities->has($requiredProductId) ||
                    $cartQuantities->get($requiredProductId) < $requiredQuantity
                ) {
                    // If any rule is not met, this promotion is not applicable
                    $allRulesMet = false;
                    break; 
                }
            }
            
            return $allRulesMet;
        });

        return $applicablePromotions;
    }

    /**
     * Add a full promotion set (e.g., Buy X, Get Y and Z) to the cart.
     * This links all items under a single promotion group ID.
     */
    public function addPromotion(int $mainProductId, array $freeProductIds, int $quantity): void
    {
        $mainProduct = ProductSalepage::find($mainProductId);
        if (!$mainProduct) return;

        $userId = $this->getUserId();
        $cart = Cart::session($userId);
        $promoGroupId = 'promo_' . Str::uuid();

        // 1. Check stock for the main product
        $mainCurrentQty = $cart->has($mainProductId) ? $cart->get($mainProductId)->quantity : 0;
        if (($mainCurrentQty + $quantity) > $mainProduct->pd_sp_stock) {
            throw new \Exception("ขออภัย, สินค้าหลัก '{$mainProduct->pd_sp_name}' มีในสต็อกไม่เพียงพอ");
        }

        // 2. Check stock for all free products
        foreach ($freeProductIds as $freeId) {
            $freeProduct = ProductSalepage::find($freeId);
            if (!$freeProduct) continue;
            $freeCurrentQty = $cart->has($freeId) ? $cart->get($freeId)->quantity : 0;
            // Assuming 1 freebie per 1 main item purchased in this transaction
            if (($freeCurrentQty + $quantity) > $freeProduct->pd_sp_stock) {
                 throw new \Exception("ขออภัย, ของแถม '{$freeProduct->pd_sp_name}' มีในสต็อกไม่เพียงพอ");
            }
        }

        // 3. Add main product
        $mainProductDetails = $this->getProductDetails($mainProductId);
        if ($mainProductDetails) {
            $cart->add([
                'id' => $mainProductDetails->id,
                'name' => $mainProductDetails->name,
                'price' => $mainProductDetails->price,
                'quantity' => $quantity,
                'attributes' => [
                    'image' => $mainProductDetails->image,
                    'original_price' => $mainProductDetails->original_price,
                    'discount' => $mainProductDetails->discount,
                    'pd_code' => $mainProductDetails->pd_code,
                    'promo_group_id' => $promoGroupId,
                    'is_freebie' => false,
                ],
            ]);
        }

        // 4. Add all free products
        foreach ($freeProductIds as $freeId) {
            $freeProductDetails = $this->getProductDetails($freeId);
            if ($freeProductDetails) {
                $cart->add([
                    'id' => $freeProductDetails->id,
                    'name' => $freeProductDetails->name . ' (Free)',
                    'price' => 0,
                    'quantity' => $quantity, // Assuming 1 freebie per 1 main item
                    'attributes' => [
                        'image' => $freeProductDetails->image,
                        'original_price' => $freeProductDetails->original_price,
                        'discount' => $freeProductDetails->original_price,
                        'pd_code' => $freeProductDetails->pd_code,
                        'promo_group_id' => $promoGroupId,
                        'is_freebie' => true,
                    ],
                ]);
            }
        }

        // 5. Persist cart
        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    /**
     * Add a BOGO (Buy One, Get One) item pair to the cart.
     */
    public function addBogoItem(int $mainProductId, int $freeProductId, int $quantity): void
    {
        $mainProduct = ProductSalepage::find($mainProductId);
        $freeProduct = ProductSalepage::find($freeProductId);

        if (!$mainProduct || !$freeProduct) return;

        $userId = $this->getUserId();
        $cart = Cart::session($userId);

        $mainCurrentQty = $cart->has($mainProductId) ? $cart->get($mainProductId)->quantity : 0;
        $freeCurrentQty = $cart->has($freeProductId) ? $cart->get($freeProductId)->quantity : 0;

        if (($mainCurrentQty + $quantity) > $mainProduct->pd_sp_stock) {
            throw new \Exception("ขออภัย, สินค้าหลัก '{$mainProduct->pd_sp_name}' มีในสต็อกไม่เพียงพอ");
        }

        if (($freeCurrentQty + $quantity) > $freeProduct->pd_sp_stock) {
            throw new \Exception("ขออภัย, ของแถม '{$freeProduct->pd_sp_name}' มีในสต็อกไม่เพียงพอ");
        }
        
        $mainProductDetails = $this->getProductDetails($mainProductId);
        $freeProductDetails = $this->getProductDetails($freeProductId);

        if (!$mainProductDetails || !$freeProductDetails) return;
        
        $bogoId = 'bogo_' . Str::uuid();

        // Add the main product
        $cart->add([
            'id' => $mainProductDetails->id,
            'name' => $mainProductDetails->name,
            'price' => $mainProductDetails->price,
            'quantity' => $quantity,
            'attributes' => [
                'image' => $mainProductDetails->image,
                'original_price' => $mainProductDetails->original_price,
                'discount' => $mainProductDetails->discount,
                'pd_code' => $mainProductDetails->pd_code,
                'bogo_id' => $bogoId,
                'is_bogo_freebie' => false,
            ],
        ]);

        // Add the free product
        $cart->add([
            'id' => $freeProductDetails->id,
            'name' => $freeProductDetails->name . ' (Free)',
            'price' => 0, // It's free!
            'quantity' => $quantity,
            'attributes' => [
                'image' => $freeProductDetails->image,
                'original_price' => $freeProductDetails->original_price,
                'discount' => $freeProductDetails->original_price, // 100% discount
                'pd_code' => $freeProductDetails->pd_code,
                'bogo_id' => $bogoId,
                'is_bogo_freebie' => true,
            ],
        ]);

        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    /**
     * Adds an array of products to the cart as free items and persists to DB.
     */
    public function addFreebies(array $freebieIds): void
    {
        $userId = $this->getUserId();
        if (!$userId || !Auth::check()) return;

        // Ensure we're working with the latest cart state
        $this->restoreCartFromDatabase($userId);
        $cart = Cart::session($userId);

        foreach ($freebieIds as $freebieId) {
            $freeProduct = ProductSalepage::find($freebieId);
            if (!$freeProduct) continue;

            $currentQty = $cart->has($freebieId) ? $cart->get($freebieId)->quantity : 0;
            if (($currentQty + 1) > $freeProduct->pd_sp_stock) {
                throw new \Exception("ขออภัย, ของแถม '{$freeProduct->pd_sp_name}' มีในสต็อกไม่เพียงพอ");
            }

            $freeProductDetails = $this->getProductDetails($freebieId);
            if ($freeProductDetails) {
                 $cart->add([
                    'id' => $freeProductDetails->id,
                    'name' => $freeProductDetails->name . ' (ของแถม)',
                    'price' => 0,
                    'quantity' => 1,
                    'attributes' => [
                        'image' => $freeProductDetails->image,
                        'original_price' => $freeProductDetails->original_price,
                        'discount' => $freeProductDetails->original_price,
                        'pd_code' => $freeProductDetails->pd_code,
                        'is_freebie' => true,
                    ],
                ]);
            }
        }
        
        $this->saveCartToDatabase($userId, $cart->getContent());
    }

    /**
     * Update the quantity of a cart item, handling BOGO links.
     */
    public function updateQuantity(int $productId, string $action): void
    {
        $userId = $this->getUserId();
        $cart = Cart::session($userId);
        $item = $cart->get($productId);

        if (!$item) return;
        
        $quantityValue = ($action === 'increase') ? 1 : -1;
        $newQuantity = $item->quantity + $quantityValue;

        if (isset($item->attributes['bogo_id'])) {
            $bogoId = $item->attributes['bogo_id'];
            $cart->getContent()->each(function ($cartItem) use ($bogoId, $newQuantity, $cart) {
                if (isset($cartItem->attributes['bogo_id']) && $cartItem->attributes['bogo_id'] === $bogoId) {
                    if ($newQuantity > 0) {
                        $cart->update($cartItem->id, ['quantity' => ['relative' => false, 'value' => $newQuantity]]);
                    } else {
                        $cart->remove($cartItem->id);
                    }
                }
            });
        } else {
            $cart->update($productId, ['quantity' => ['relative' => true, 'value' => $quantityValue]]);
        }

        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    /**
     * Remove an item from the cart, handling BOGO links.
     */
    public function removeItem(int $productId): void
    {
        $userId = $this->getUserId();
        $cart = Cart::session($userId);
        $item = $cart->get($productId);

        if (!$item) return;

        if (isset($item->attributes['bogo_id'])) {
            $bogoId = $item->attributes['bogo_id'];
            $cart->getContent()->each(function ($cartItem) use ($bogoId, $cart) {
                if (isset($cartItem->attributes['bogo_id']) && $cartItem->attributes['bogo_id'] === $bogoId) {
                    $cart->remove($cartItem->id);
                }
            });
        } else {
            $cart->remove($productId);
        }
        
        if (Auth::check()) {
            $this->saveCartToDatabase($userId, $cart->getContent());
        }
    }

    /**
     * Merge the guest cart into the logged-in user's cart.
     */
    public function mergeGuestCart(string $guestSessionId, int $userId): void
    {
        $guestCartItems = Cart::session($guestSessionId)->getContent();
        Cart::session($guestSessionId)->clear();
        $userCart = Cart::session($userId);
        $this->restoreCartFromDatabase($userId);
        
        if ($guestCartItems->isNotEmpty()) {
            foreach ($guestCartItems as $item) {
                $userCart->add([
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'attributes' => $item->attributes->toArray(),
                ]);
            }
        }
        
        $this->saveCartToDatabase($userId, $userCart->getContent());
    }

    /**
     * Persist the cart content to the database for the logged-in user.
     */
    private function saveCartToDatabase(int $userId, \Darryldecode\Cart\CartCollection $cartContent): void
    {
        CartStorage::updateOrCreate(
            ['user_id' => $userId],
            ['cart_data' => $cartContent->toArray()]
        );
    }

    /**
     * Restore the cart from the database.
     */
    private function restoreCartFromDatabase(int $userId): void
    {
        $savedCart = CartStorage::where('user_id', $userId)->first();
        $userCart = Cart::session($userId);
        $userCart->clear(); 

        if ($savedCart && !empty($savedCart->cart_data)) {
            $userCart->add($savedCart->cart_data);
        }
    }
}