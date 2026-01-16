<?php

namespace App\Http\Controllers;

use App\Models\ProductSalepage;
use Illuminate\Http\Request;

class AllProductController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductSalepage::with('images')->where('pd_sp_active', 1);

        // Search Logic
        if ($request->has('search') && $request->search != '') {
            $query->where('pd_sp_name', 'like', '%'.$request->search.'%');
        }

        $products = $query->orderBy('pd_sp_id', 'desc')->paginate(12);

        // Categories can be dynamic in the future
        $categories = ['Electronics', 'Books', 'Clothing', 'Home & Kitchen'];

        return view('allproducts', compact('products', 'categories'));
    }
}
