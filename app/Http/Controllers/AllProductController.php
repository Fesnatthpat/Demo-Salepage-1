<?php

namespace App\Http\Controllers;

use App\Models\ProductSalepage;
use App\Services\CartService;
use Illuminate\Http\Request;

class AllProductController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index(Request $request)
    {
        $query = ProductSalepage::with('images')->where('pd_sp_active', 1);

        // Search Logic
        if ($request->has('search') && $request->search != '') {
            $query->where('pd_sp_name', 'like', '%'.$request->search.'%');
        }

        $products = $query->orderBy('pd_sp_id', 'desc')->paginate(12);

        // Enrich products with promotion data
        $products->getCollection()->transform(function ($product) {
            $promotions = $this->cartService->getPromotionsForProduct($product->pd_sp_id);
            if ($promotions->isNotEmpty()) {
                // Assuming only one applicable promotion for simplicity
                $giftsPerItem = $promotions->first()->actions->sum(fn($a) => (int) ($a->actions['quantity_to_get'] ?? 0));
                $product->gifts_per_item = $giftsPerItem > 0 ? $giftsPerItem : null;
            } else {
                $product->gifts_per_item = null;
            }
            return $product;
        });

        // Categories can be dynamic in the future
        $categories = ['Electronics', 'Books', 'Clothing', 'Home & Kitchen'];

        return view('allproducts', compact('products', 'categories'));
    }
}
