<?php

namespace App\Services;

use App\Models\CartStorage;
use App\Models\Product;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class CartService
{
    /**
     * Get the current user's session ID for the cart.
     * Can be a logged-in user's ID or a guest's session ID.
     */
    public function getUserId(): string|int
    {
        // After login, session ID regenerates. We must use Auth::id() if available.
        return Auth::check() ? Auth::id() : '_guest_' . session()->getId();
    }

    /**
     * Get the content of the current user's cart.
     * This is the primary method for retrieving cart items.
     */
    public function getCartContents(): Collection
    {
        $userId = $this->getUserId();
        $cart = Cart::session($userId);

        // If cart in session is empty for a logged-in user, try restoring from DB once.
        // This prevents hitting the DB on every single page load.
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

    /**
     * Add a product to the cart or update it if it already exists.
     */
    public function addOrUpdate(int $productId, int $quantity): void
    {
        $productModel = Product::findOrFail($productId);
        $userId = $this->getUserId();
        
        $salePrice = $productModel->real_price;
        $quantity = max(1, $quantity);

        $cartDetails = [
            'id' => $productId,
            'name' => $productModel->pd_name,
            'price' => $salePrice,
            'quantity' => $quantity,
            'attributes' => [
                'image' => $productModel->pd_img,
                'original_price' => $productModel->pd_price,
                'discount' => $productModel->discount_amount,
                'pd_code' => $productModel->pd_code,
            ],
            'associatedModel' => $productModel,
        ];

        // Let Darryle's built-in 'update' handle quantity logic.
        // The 'add' method with 'update' set to true also works, but this is explicit.
        if (Cart::session($userId)->has($productId)) {
            Cart::session($userId)->update($productId, [
                'quantity' => [
                    'relative' => false,
                    'value' => $quantity
                ],
                'price' => $salePrice,
                'attributes' => $cartDetails['attributes']
            ]);
        } else {
            Cart::session($userId)->add($cartDetails);
        }

        if (Auth::check()) {
            $this->saveCartToDatabase($userId);
        }
    }

    /**
     * Update the quantity of a cart item relatively.
     */
    public function updateQuantity(int $productId, string $action): void
    {
        $userId = $this->getUserId();
        $quantityValue = ($action === 'increase') ? 1 : -1;
        
        Cart::session($userId)->update($productId, [
            'quantity' => [
                'relative' => true,
                'value' => $quantityValue
            ]
        ]);

        if (Auth::check()) {
            $this->saveCartToDatabase($userId);
        }
    }

    /**
     * Remove an item from the cart.
     */
    public function removeItem(int $productId): void
    {
        $userId = $this->getUserId();
        Cart::session($userId)->remove($productId);
        
        if (Auth::check()) {
            $this->saveCartToDatabase($userId);
        }
    }

    /**
     * [REWRITTEN] Merge the guest cart into the logged-in user's cart.
     * This new logic is safer and avoids direct session manipulation conflicts.
     */
    public function mergeGuestCart(string $guestSessionId, int $userId): void
    {
        // 1. Get guest cart items before doing anything else.
        $guestCartItems = Cart::session($guestSessionId)->getContent();

        // 2. Clear the guest cart instance completely.
        Cart::session($guestSessionId)->clear();

        // 3. Set the active cart session to the logged-in user.
        $userCart = Cart::session($userId);

        // 4. Restore the user's cart from the database to have a base state.
        $this->restoreCartFromDatabase($userId);
        
        // 5. Add items from the guest cart one by one.
        // The `add` method will intelligently update quantities if the item already exists.
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
        
        // 6. Save the final merged cart to the database.
        $this->saveCartToDatabase($userId);
    }

    /**
     * [PRIVATE] Persist the cart content to the database for the logged-in user.
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
     * [PRIVATE & REWRITTEN] Restore the cart from the database.
     */
    private function restoreCartFromDatabase(int $userId): void
    {
        // Find the saved cart from the database.
        $savedCart = CartStorage::where('user_id', $userId)->first();
        
        // Get the cart instance for the user.
        $userCart = Cart::session($userId);
        
        // Always start with a clean slate in the session before restoring.
        $userCart->clear(); 

        // If we have a saved cart with data, add it to the now-empty session cart.
        if ($savedCart && !empty($savedCart->cart_data)) {
            $userCart->add($savedCart->cart_data);
        }
    }
}