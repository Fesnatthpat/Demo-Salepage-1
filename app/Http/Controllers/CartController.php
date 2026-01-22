<?php

namespace App\Http\Controllers;

use App\Models\ProductSalepage;
use App\Models\Promotion;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index()
    {
        $items = $this->cartService->getCartContents();
        $total = $this->cartService->getTotal();

        // Eager load products to prevent N+1 queries in the view
        $productIds = $items->pluck('id')->toArray();
        $products = ProductSalepage::with('images')
            ->whereIn('pd_sp_id', $productIds)
            ->get()
            ->keyBy('pd_sp_id');

        // === New Multi-Condition Promotion Logic ===
        $applicablePromotions = $this->cartService->getApplicablePromotions($items);

        // Consolidate all possible giftable products from all applicable promotions
        $giftableProducts = $applicablePromotions->flatMap(function ($promo) {
            return $promo->actions->flatMap(function ($action) {
                // Combine fixed gifts and pooled gifts
                $gifts = collect();
                if ($action->productToGet) {
                    $gifts->push($action->productToGet);
                }
                if ($action->giftableProducts->isNotEmpty()) {
                    return $gifts->merge($action->giftableProducts);
                }

                return $gifts;
            });
        })->unique('pd_sp_id');
        // === End New Logic ===

        return view('cart', compact('items', 'total', 'products', 'applicablePromotions', 'giftableProducts'));
    }

    public function addToCart(Request $request, $productId)
    {
        try {
            $quantity = $request->input('quantity', 1);
            $this->cartService->addOrUpdate((int) $productId, (int) $quantity);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'เพิ่มสินค้าเรียบร้อยแล้ว',
                    'cartCount' => $this->cartService->getTotalQuantity(),
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'เพิ่มสินค้าลงตะกร้าแล้ว');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422); // 422 Unprocessable Entity is a good status code for this
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

        return back()->with('success', 'ลบสินค้าเรียบร้อยแล้ว');
    }

    public function addPromotion(Request $request) {}
}
