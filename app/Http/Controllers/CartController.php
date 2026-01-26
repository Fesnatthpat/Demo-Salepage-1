<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $quantity = (int) $request->input('quantity', 1);

        // Determine if gifts are required for this product and quantity
        $promotions = $this->cartService->getPromotionsForProduct((int) $productId);
        $giftsPerItem = $promotions->sum(function ($promo) {
            return $promo->actions->sum(fn($action) => (int)($action->actions['quantity_to_get'] ?? 0));
        });
        $expectedGiftCount = $quantity * $giftsPerItem;

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'selected_gift_ids' => [
                // If gifts are expected, this field is required and must be an array.
                Rule::requiredIf($expectedGiftCount > 0),
                'nullable',
                'array',
                // If it is an array, its size must exactly match the expected count.
                function ($attribute, $value, $fail) use ($expectedGiftCount) {
                    if ($expectedGiftCount > 0 && is_array($value) && count($value) !== $expectedGiftCount) {
                        $fail("กรุณาเลือกของแถมให้ครบจำนวน {$expectedGiftCount} ชิ้น");
                    }
                },
            ],
            'selected_gift_ids.*' => 'integer|exists:product_salepage,pd_sp_id',
        ]);

        try {
            $giftIds = $request->input('selected_gift_ids');

            if (is_array($giftIds) && !empty($giftIds)) {
                $this->cartService->addWithGifts((int) $productId, $quantity, $giftIds);
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
        // Server-side validation to ensure the user does not submit more freebies than they are entitled to.
        $freebieLimit = $this->cartService->calculateFreebieLimit();

        $request->validate([
            'selected_freebies'   => 'required|array|max:'.$freebieLimit,
            'selected_freebies.*' => 'integer|exists:product_salepage,pd_sp_id',
        ], [
            'selected_freebies.max' => "คุณสามารถเลือกของแถมได้สูงสุด {$freebieLimit} ชิ้น",
        ]);

        try {
            $this->cartService->addFreebies($request->input('selected_freebies'));

            return redirect()->route('cart.index')->with('success', 'เพิ่มของแถมเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
