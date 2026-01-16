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
        try {
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
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422); // 422 Unprocessable Entity is a good status code for this
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
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

    public function addBogo(Request $request)
    {
        try {
            $validated = $request->validate([
                'main_product_id' => 'required|exists:product_salepage,pd_sp_id',
                'free_product_id' => 'required|exists:product_salepage,pd_sp_id',
                'quantity' => 'required|integer|min:1',
            ]);
    
            // Optional TODO: Add logic to verify that free_product_id is a valid free option for main_product_id
            // This adds an extra layer of security on top of the frontend validation.
    
            $this->cartService->addBogoItem(
                (int)$validated['main_product_id'],
                (int)$validated['free_product_id'],
                (int)$validated['quantity']
            );
    
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'เพิ่มสินค้าโปรโมชั่นเรียบร้อยแล้ว',
                    'cartCount' => $this->cartService->getTotalQuantity()
                ]);
            }
    
            return redirect()->route('cart.index')->with('success', 'เพิ่มสินค้าโปรโมชั่นลงตะกร้าแล้ว');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}