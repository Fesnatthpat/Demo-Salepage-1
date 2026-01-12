<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductSalepage;

class ProductController extends Controller
{
    public function show($id)
    {
        // Prioritize finding the product in product_salepage
        $salePageProduct = ProductSalepage::with('images')->find($id);

        if ($salePageProduct) {
            // If found, create a product-like object to pass to the view
            $product = (object) [
                'pd_id' => $salePageProduct->pd_sp_id,
                'id' => $salePageProduct->pd_sp_id,
                'pd_name' => $salePageProduct->pd_sp_name,
                'pd_price' => $salePageProduct->pd_sp_price,
                'pd_sp_discount' => $salePageProduct->pd_sp_discount,
                'pd_details' => $salePageProduct->pd_sp_details,
                'pd_sp_details' => $salePageProduct->pd_sp_details,
                'images' => $salePageProduct->images,
                'brand' => null,
                'brand_name' => null,
                'pd_code' => $salePageProduct->pd_code,
                'quantity' => 99, // Default stock
                'pd_img' => $salePageProduct->images->first()->image_path ?? null,
            ];
        } else {
            // Fallback to the original logic if not in sale page
            $product = Product::with(['brand', 'images'])->find($id);

            if (! $product) {
                return redirect('/')->with('error', 'ไม่พบสินค้านี้');
            }
             // Add pd_img for consistency
            $product->pd_img = $product->images->first()->image_path ?? $product->pd_img;
        }

        return view('product', compact('product'));
    }
}
