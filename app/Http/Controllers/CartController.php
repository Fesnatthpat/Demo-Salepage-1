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
        
        // ถ้าไม่มีการส่ง parameter มาเลย หรือส่งมาแต่เป็นค่าว่าง ให้เริ่มต้นเป็นอาร์เรย์ว่าง
        if ($selectedIds === null || $selectedIds === '') {
            $selectedIds = [];
        } elseif (is_string($selectedIds)) {
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

            // 🛠️ แก้ไข: เช็คว่าเรียกผ่าน AJAX หรือไม่ ถ้าใช่ส่ง JSON, ถ้าไม่ใช่ให้ Redirect กลับพร้อมข้อความ
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'เพิ่มสินค้าเรียบร้อยแล้ว',
                    'cartCount' => $this->cartService->getTotalQuantity(),
                ]);
            }

            return redirect()->back()->with('success', 'เพิ่มสินค้าลงตะกร้าเรียบร้อยแล้ว');

        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ข้อมูลไม่ถูกต้อง',
                    'errors' => $e->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Cart error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
                'productId' => $productId ?? 'bundle'
            ]);

            if ($request->ajax() || $request->wantsJson()) {
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

            // 🛠️ แก้ไข: รองรับทั้ง AJAX และ การ Redirect
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'เพิ่มสินค้าชุดโปรโมชั่นเรียบร้อยแล้ว',
                    'cartCount' => $this->cartService->getTotalQuantity(),
                ]);
            }

            return redirect()->back()->with('success', 'เพิ่มชุดโปรโมชั่นลงตะกร้าเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Cart error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
                'productId' => $productId ?? 'bundle'
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return redirect()->back()->with('error', $e->getMessage());
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

        return back()->with('success', 'ลบสินค้าเรียบร้อยแล้ว');
    }
}