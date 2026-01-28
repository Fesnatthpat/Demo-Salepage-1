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
        // ★★★ แก้ไข Validation: ไม่บังคับเลือกของแถม (nullable) ★★★
        // เพื่อรองรับกรณีซื้อชิ้นแรกของเงื่อนไข AND (ที่ยังไม่ได้ของแถม)
        $request->validate([
            'quantity' => 'integer|min:1',
            'selected_gift_ids' => 'nullable|array',
            'selected_gift_ids.*' => 'integer',
        ]);

        try {
            $quantity = (int) $request->input('quantity', 1);
            $giftIds = $request->input('selected_gift_ids', []);

            // ★★★ Logic: เช็คว่ามีการส่งของแถมมาด้วยหรือไม่ ★★★
            if (! empty($giftIds)) {
                // กรณีเลือกของแถมมาด้วย (ใช้ฟังก์ชัน addWithGifts ที่เพิ่มใน Service)
                $this->cartService->addWithGifts((int) $productId, $quantity, $giftIds);
            } else {
                // กรณีสินค้าปกติ หรือยังไม่ได้รับสิทธิ์ของแถม
                $this->cartService->addOrUpdate((int) $productId, $quantity);
            }

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
}
