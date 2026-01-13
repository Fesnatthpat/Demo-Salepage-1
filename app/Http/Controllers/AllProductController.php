<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AllProductController extends Controller
{
    public function index(Request $request)
    {
        // New logic: Use product_salepage as the source of truth
        $query = DB::table('product_salepage as ps')
            ->select(
                'ps.pd_sp_id as pd_id', // Alias for consistency
                'ps.pd_sp_id',
                'ps.pd_code',
                'ps.pd_sp_name as pd_name',
                'ps.pd_sp_price as pd_price',
                'ps.pd_sp_discount',
                'img.image_path as pd_img'
            )
            ->leftJoin('image_product as img', function ($join) {
                $join->on('ps.pd_sp_id', '=', 'img.product_id')
                     ->where('img.is_primary', '=', 1);
            })
            ->where('ps.pd_sp_active', 1)
            ->where('ps.pd_sp_display_location', 'general'); // Added this line

        // --- Search Logic ---
        if ($request->has('search') && $request->search != '') {
            $query->where('ps.pd_sp_name', 'like', '%' . $request->search . '%');
        }

        // --- Category Logic (if any) ---
        if ($request->has('category') && $request->category != '') {
            // $query->where(...)
        }

        $products = $query->orderBy('ps.pd_sp_id', 'desc')
                          ->paginate(12);

        // Example categories
        $categories = ["Electronics", "Books", "Clothing", "Home & Kitchen"];

        return view('allproducts', compact('products', 'categories'));
    }
}