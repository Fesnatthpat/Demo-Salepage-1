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

    /**
     * สำหรับเพิ่มสินค้าปกติ หรือ สินค้าที่มีของแถมแบบ 1 ชิ้น (Buy X Get Y)
     */
    public function addToCart(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'integer|min:1',
            'selected_gift_ids' => 'nullable|array',
            'selected_gift_ids.*' => 'integer',
        ]);

        try {
            $quantity = (int) $request->input('quantity', 1);
            $giftIds = $request->input('selected_gift_ids', []);

            if (! empty($giftIds)) {
                // กรณีมีของแถม (จะถูกผูก Group ID เดียวกัน)
                $this->cartService->addWithGifts((int) $productId, $quantity, $giftIds);
            } else {
                // กรณีสินค้าปกติ
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

    /**
     * ★★★ ฟังก์ชันใหม่: สำหรับเพิ่มสินค้าแบบ Bundle (ซื้อคู่) ★★★
     * ต้องใช้ฟังก์ชันนี้เท่านั้น เพื่อให้สินค้า A และ B ผูกติดกัน เวลาลบจะได้หายพร้อมกัน
     */
    public function addBundleToCart(Request $request)
    {
        $request->validate([
            'main_product_id' => 'required|integer',      // สินค้าหลัก (เช่น ราคา 1000)
            'secondary_product_id' => 'required|integer', // สินค้ารอง (เช่น ราคา 400)
            'gift_ids' => 'nullable|array',               // ของแถม
            'gift_ids.*' => 'integer',
        ]);

        try {
            $mainId = (int) $request->input('main_product_id');
            $secId = (int) $request->input('secondary_product_id');
            $giftIds = $request->input('gift_ids', []);

            // เรียกใช้ addBundle ใน Service (สินค้าทุกชิ้นจะได้ Group ID เดียวกัน)
            $this->cartService->addBundle($mainId, $secId, $giftIds);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'เพิ่มสินค้าชุดโปรโมชั่นเรียบร้อยแล้ว',
                    'cartCount' => $this->cartService->getTotalQuantity(),
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'เพิ่มสินค้าชุดโปรโมชั่นแล้ว');

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
