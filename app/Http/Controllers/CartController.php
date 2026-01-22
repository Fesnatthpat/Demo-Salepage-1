<?php

namespace App\Http\Controllers;

use App\Models\ProductSalepage;
use App\Models\Promotion;
use App\Models\PromotionRule;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

        return view('cart', compact('items', 'total', 'products'));
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

    public function addPromotion(Request $request)
    {
        try {
            $validated = $request->validate([
                'main_product_id' => 'required|exists:product_salepage,pd_sp_id',
                'free_product_ids' => 'required|array|min:1',
                'free_product_ids.*' => 'required|exists:product_salepage,pd_sp_id',
                'quantity' => 'required|integer|min:1',
            ]);

            // --- Security Check: Verify that the freebie IDs are valid for this promotion ---
            $now = now();
            $mainProductId = $validated['main_product_id'];
            $requestedFreebieIds = collect($validated['free_product_ids'])->map(fn($id) => (int)$id)->sort()->values();

            $relevantPromotionIds = PromotionRule::where(function ($query) use ($mainProductId) {
                $query->where('rules->product_id', (string) $mainProductId)
                      ->orWhere('rules->product_id', (int) $mainProductId);
            })
                ->pluck('promotion_id')
                ->unique();
            
            $activePromotions = Promotion::with(['actions.giftableProducts', 'actions.productToGet'])
                ->whereIn('id', $relevantPromotionIds)
                ->where('is_active', true)
                ->where(fn($q) => $q->where('start_date', '<=', $now)->orWhereNull('start_date'))
                ->where(fn($q) => $q->where('end_date', '>=', $now)->orWhereNull('end_date'))
                ->get();

            $validFreebieIds = $activePromotions->flatMap(fn($promo) => 
                $promo->actions->flatMap(fn($action) => 
                    $action->giftableProducts->pluck('pd_sp_id')->merge(
                        $action->productToGet ? [$action->productToGet->pd_sp_id] : []
                    )
                )
            )->unique()->sort()->values();
            
            if ($requestedFreebieIds->diff($validFreebieIds)->isNotEmpty()) {
                throw ValidationException::withMessages(['free_product_ids' => 'Invalid free product selection.']);
            }
            // --- End Security Check ---

            $this->cartService->addPromotion(
                (int) $validated['main_product_id'],
                $validated['free_product_ids'],
                (int) $validated['quantity']
            );

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'เพิ่มสินค้าโปรโมชั่นเรียบร้อยแล้ว',
                    'cartCount' => $this->cartService->getTotalQuantity(),
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'เพิ่มสินค้าโปรโมชั่นลงตะกร้าแล้ว');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                // Use the actual exception message to provide clear feedback
                $message = $e->getMessage();
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
