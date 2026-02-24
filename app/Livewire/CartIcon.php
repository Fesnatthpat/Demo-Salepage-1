<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On; // สำหรับ Livewire 3
use Darryldecode\Cart\Facades\CartFacade as Cart;

class CartIcon extends Component
{
    public $count = 0;

    public function mount()
    {
        // Using the Cart facade from darryldecode/cart package
        if (auth()->check()) {
            $cartSessionId = auth()->id();
        } else {
            $cartSessionId = '_guest_' . session()->getId();
        }
        $this->count = Cart::session($cartSessionId)->getTotalQuantity();
    }

    #[On('cartUpdated')] // รอฟัง Event ชื่อ cartUpdated
    public function updateCartCount()
    {
        if (auth()->check()) {
            $cartSessionId = auth()->id();
        } else {
            $cartSessionId = '_guest_' . session()->getId();
        }
        $this->count = Cart::session($cartSessionId)->getTotalQuantity();
    }

    public function render()
    {
        return view('livewire.cart-icon');
    }
}
