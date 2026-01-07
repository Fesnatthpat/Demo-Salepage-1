<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function show($id)
    {
        $product = \App\Models\Product::with(['brand', 'salePage'])->find($id);

        if (! $product) {
            return redirect('/')->with('error', 'ไม่พบสินค้านี้');
        }

        // Manually add the discount to the main object for compatibility with the view
        $product->pd_sp_discount = $product->salePage->pd_sp_discount ?? 0;

        return view('product', compact('product'));
    }
}
