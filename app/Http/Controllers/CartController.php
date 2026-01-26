<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index()
    {
        $data = $this->cartService->getCartDataForView();

        return view('cart', $data);
    }

    public function addToCart(Request $request, $productId)
    {
        $request->validate(['quantity' => 'integer|min:1']);

        try {
            $quantity = (int) $request->input('quantity', 1);
            $this->cartService->addOrUpdate((int) $productId, $quantity);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'เพิ่มสินค้าเรียบร้อยแล้ว',
                    'cartCount' => $this->cartService->getTotalQuantity(),
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'เพิ่มสินค้าแล้ว');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function updateQuantity($productId, $action)
    {
        $this->cartService->updateQuantity((int) $productId, $action);

        return back();
    }

    public function removeItem($productId)
    {
        $this->cartService->removeItem((int) $productId);

        return back()->with('success', 'ลบสินค้าแล้ว');
    }

    public function addFreebiesToCart(Request $request)
    {
        $request->validate([
            'selected_freebies' => 'required|array',
            'selected_freebies.*' => 'integer|exists:product_salepage,pd_sp_id',
        ]);

        try {
            $this->cartService->addFreebies($request->input('selected_freebies'));

            return redirect()->route('cart.index')->with('success', 'เพิ่มของแถมเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
