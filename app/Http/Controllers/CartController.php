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
     * สำหรับเพิ่มสินค้าปกติ
     */
    public function addToCart(Request $request, $productId)
    {
        // 🔥 ขั้นตอนที่ 1: บังคับแปลงข้อมูลให้เป็นตัวเลขทันที (แก้ปัญหาถาวร)
        // ไม่ว่าหน้าบ้านจะส่งอะไรมา เราจะแปลงเป็นตัวเลขก่อนตรวจสอบ
        $quantity = (int) $request->input('quantity', 1);
        if ($quantity < 1) {
            $quantity = 1;
        } // กันเหนียว

        // แปลงของแถมให้เป็น Array เสมอ
        $gifts = $request->input('selected_gift_ids');
        if (! is_array($gifts)) {
            $gifts = [];
        }

        // ยัดข้อมูลที่แปลงแล้วกลับเข้าไปใน Request
        $request->merge([
            'quantity' => $quantity,
            'selected_gift_ids' => $gifts,
        ]);

        // 🔥 ขั้นตอนที่ 2: ตรวจสอบข้อมูล (Validation)
        // ตอนนี้ quantity เป็นตัวเลขแน่นอนแล้ว Error validation.numeric จะไม่มีทางเกิดขึ้น
        $request->validate([
            'quantity' => 'integer|min:1',
            'selected_gift_ids' => 'array',
            'selected_gift_ids.*' => 'integer',
        ], [
            // ใส่ข้อความภาษาไทยกันไว้ (เผื่อกรณีอื่น)
            'quantity.min' => 'ต้องสั่งซื้ออย่างน้อย 1 ชิ้น',
            'integer' => 'ข้อมูลต้องเป็นตัวเลขจำนวนเต็ม',
            'array' => 'ข้อมูลไม่ถูกต้อง',
        ]);

        try {
            // ทำงานต่อได้เลย เพราะเราเตรียมข้อมูลไว้ในตัวแปรข้างบนแล้ว
            if (! empty($gifts)) {
                $this->cartService->addWithGifts((int) $productId, $quantity, $gifts);
            } else {
                $this->cartService->addOrUpdate((int) $productId, $quantity);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'เพิ่มสินค้าเรียบร้อยแล้ว',
                    'cartCount' => $this->cartService->getTotalQuantity(),
                ]);
            }

            return back()->with('success', 'เพิ่มสินค้าเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * สำหรับสินค้าโปรโมชั่น (Bundle)
     */
    public function addBundleToCart(Request $request)
    {
        // 🔥 ขั้นตอนที่ 1: บังคับแปลงข้อมูล Bundle เหมือนกัน
        $mainId = (int) $request->input('main_product_id');
        $secId = (int) $request->input('secondary_product_id');

        $gifts = $request->input('gift_ids');
        if (! is_array($gifts)) {
            $gifts = [];
        }

        $request->merge([
            'main_product_id' => $mainId,
            'secondary_product_id' => $secId,
            'gift_ids' => $gifts,
        ]);

        // 🔥 ขั้นตอนที่ 2: ตรวจสอบ
        $request->validate([
            'main_product_id' => 'required|integer|min:1',
            'secondary_product_id' => 'required|integer|min:1',
            'gift_ids' => 'array',
            'gift_ids.*' => 'integer',
        ], [
            'main_product_id.required' => 'ไม่พบข้อมูลสินค้าหลัก',
            'main_product_id.min' => 'รหัสสินค้าไม่ถูกต้อง',
            'secondary_product_id.required' => 'กรุณาเลือกสินค้าคู่โปรโมชั่น',
            'integer' => 'ข้อมูลต้องเป็นตัวเลข',
        ]);

        try {
            $this->cartService->addBundle($mainId, $secId, $gifts);

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
