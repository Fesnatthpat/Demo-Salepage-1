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

        // If a sale page entry exists, override the price and discount
        if ($product->salePage) {
            $product->pd_price = $product->salePage->pd_sp_price;
            $product->pd_sp_discount = $product->salePage->pd_sp_discount;
            $product->pd_sp_details = $product->salePage->pd_sp_details;
        } else {
            // Ensure pd_sp_discount is 0 if there is no sale page, for consistency
            $product->pd_sp_discount = 0;
            $product->pd_sp_details = null;
        }


        return view('product', compact('product'));
    }
}
