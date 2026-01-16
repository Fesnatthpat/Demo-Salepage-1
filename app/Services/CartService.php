<?php

namespace App\Services;

use App\Models\CartStorage;
use App\Models\Product;
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
        $cart = Cart::session($userId);

        if (Auth::check() && $cart->isEmpty()) {
            $this->restoreCartFromDatabase($userId);
        }
        
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

        if (!$product) {
            // Silently return if product doesn't exist
            return;
        }

        // 1. Centralized Stock Check
        if ($product->pd_sp_stock <= 0) {
            throw new \Exception('ขออภัย, สินค้าชิ้นนี้หมดสต็อกแล้ว');
        }

        $userId = $this->getUserId();
        $cart = Cart::session($userId);
        $currentQuantity = $cart->has($productId) ? $cart->get($productId)->quantity : 0;

        if (($currentQuantity + $quantity) > $product->pd_sp_stock) {
            throw new \Exception("ไม่สามารถเพิ่มสินค้าได้, มีสินค้าในสต็อกเพียง {$product->pd_sp_stock} ชิ้น");
        }

        // 2. Get Product Details for Cart
        $productDetails = $this->getProductDetails($productId);
        if (!$productDetails) return;

        // 3. Add to Cart (DarrylDecode handles both add & update quantity)
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

        // 4. Persist to DB if logged in
        if (Auth::check()) {
            $this->saveCartToDatabase($userId);
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
            $this->saveCartToDatabase($userId);
        }
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
            $this->saveCartToDatabase($userId);
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
            $this->saveCartToDatabase($userId);
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
        
        $this->saveCartToDatabase($userId);
    }

    /**
     * Persist the cart content to the database for the logged-in user.
     */
    private function saveCartToDatabase(int $userId): void
    {
        $cartContent = Cart::session($userId)->getContent();

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