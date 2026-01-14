<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductSalepage;

class ProductController extends Controller
{
    public function show($id)
    {
        // Eager load everything needed for the page
        $salePageProduct = ProductSalepage::with([
            'images', 
            'options.images', 
            'bogoFreeOptions.images'
        ])->find($id);

        if ($salePageProduct) {
            $primaryImage = $salePageProduct->images->where('is_primary', true)->first();
            $pd_img = $primaryImage ? $primaryImage->image_path : ($salePageProduct->images->first()->image_path ?? null);
            
            $product = (object) [
                'pd_id' => $salePageProduct->pd_sp_id,
                'id' => $salePageProduct->pd_sp_id,
                'pd_name' => $salePageProduct->pd_sp_name,
                'pd_price' => $salePageProduct->pd_sp_price,
                'pd_sp_discount' => $salePageProduct->pd_sp_discount,
                'pd_details' => $salePageProduct->pd_sp_details,
                'pd_sp_details' => $salePageProduct->pd_sp_details,
                'images' => $salePageProduct->images,
                'options' => $salePageProduct->options,
                'is_bogo_active' => $salePageProduct->is_bogo_active,
                'bogoFreeOptions' => $salePageProduct->bogoFreeOptions,
                'brand' => null,
                'brand_name' => null,
                'pd_code' => $salePageProduct->pd_code,
                'quantity' => 99, // Default stock
                'pd_img' => $pd_img,
            ];
        } else {
            // Fallback logic for original products, if any.
            // These will not have BOGO promotions by design.
            $product = Product::with(['brand', 'images'])->find($id);

            if (! $product) {
                return redirect('/')->with('error', 'ไม่พบสินค้านี้');
            }
            
            $primaryImage = $product->images->where('is_primary', true)->first();
            $product->pd_img = $primaryImage ? $primaryImage->image_path : ($product->images->first()->image_path ?? $product->pd_img);
            
            // Add the missing properties to avoid errors in the view
            $product->options = collect();
            $product->is_bogo_active = false;
            $product->bogoFreeOptions = collect();
            $product->pd_sp_discount = 0;
        }

        return view('product', compact('product'));
    }
}
