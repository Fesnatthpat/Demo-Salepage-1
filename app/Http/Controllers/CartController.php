<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index(Request $request)
    {
        $selectedIds = $request->input('selected_items');
        
        // ถ้ามีการส่ง parameter มาแต่เป็นค่าว่าง (เช่น ?selected_items=) 
        // ให้เป็น array ว่างแทนที่จะเป็น null
        if ($selectedIds === '') {
            $selectedIds = [];
        } elseif ($selectedIds && is_string($selectedIds)) {
            $selectedIds = explode(',', $selectedIds);
        }
        
        $data = $this->cartService->getCartDataForView($selectedIds);

        return view('cart', $data);
    }

    /**
     * สำหรับเพิ่มสินค้าปกติ
     */
    public function addToCart(Request $request, $productId)
    {
        // 🔥 ขั้นตอนที่ 1: บังคับแปลงข้อมูลให้เป็นตัวเลขทันที (แก้ปัญหาถาวร)
        $quantity = (int) $request->input('quantity', 1);
        if ($quantity < 1) {
            $quantity = 1;
        }

        $gifts = $request->input('selected_gift_ids', []);
        $optionId = $request->input('selected_option_id') ? (int) $request->input('selected_option_id') : null;

        $request->merge([
            'quantity' => $quantity,
            'selected_gift_ids' => $gifts,
            'selected_option_id' => $optionId,
        ]);



        try {
            // 🔥 ขั้นตอนที่ 2: ตรวจสอบข้อมูล (Validation)
            $request->validate([
                'quantity' => 'integer|min:1',
                'selected_gift_ids' => 'array',
                'selected_gift_ids.*' => 'integer',
                'selected_option_id' => 'nullable|integer|exists:product_options,option_id',
            ], [
                'quantity.min' => 'ต้องสั่งซื้ออย่างน้อย 1 ชิ้น',
                'integer' => 'ข้อมูลต้องเป็นตัวเลขจำนวนเต็ม',
                'array' => 'ข้อมูลไม่ถูกต้อง',
                'selected_option_id.exists' => 'ตัวเลือกสินค้าไม่ถูกต้อง',
            ]);

            if (! empty($gifts)) {
                $this->cartService->addWithGifts((int) $productId, $quantity, $gifts);
            } else {
                $this->cartService->addOrUpdate((int) $productId, $quantity, $optionId);
            }

            // Always return JSON for this endpoint, as it's explicitly called via AJAX
            return response()->json([
                'success' => true,
                'message' => 'เพิ่มสินค้าเรียบร้อยแล้ว',
                'cartCount' => $this->cartService->getTotalQuantity(),
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'ข้อมูลไม่ถูกต้อง',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) { // Modified block
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
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



        try {
            $this->cartService->addBundle($mainId, $secId, $gifts);

            // Always return JSON for this endpoint, as it's explicitly called via AJAX
            return response()->json([
                'success' => true,
                'message' => 'เพิ่มสินค้าชุดโปรโมชั่นเรียบร้อยแล้ว',
                'cartCount' => $this->cartService->getTotalQuantity(),
            ]);

        } catch (\Exception $e) { // Modified block
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function updateQuantity($productId, $action)
    {
        $this->cartService->updateQuantity($productId, $action);

        return back();
    }

    public function removeItem($productId)
    {
        $this->cartService->removeItem($productId);

        return back()->with('success', 'ลบสินค้าแล้ว');
    }
}