<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cartService)
    {
    }

    public function index()
    {
        $items = $this->cartService->getCartContents();
        $total = $this->cartService->getTotal();

        return view('cart', compact('items', 'total'));
    }

    public function addToCart(Request $request, $productId)
    {
        $quantity = $request->input('quantity', 1);
        $this->cartService->addOrUpdate((int)$productId, (int)$quantity);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'เพิ่มสินค้าเรียบร้อยแล้ว',
                'cartCount' => $this->cartService->getTotalQuantity()
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'เพิ่มสินค้าลงตะกร้าแล้ว');
    }

    public function updateQuantity($productId, $action)
    {
        $this->cartService->updateQuantity((int)$productId, $action);
        return back();
    }

    public function removeItem($productId)
    {
        $this->cartService->removeItem((int)$productId);
        return back()->with('success', 'ลบสินค้าเรียบร้อยแล้ว');
    }
}