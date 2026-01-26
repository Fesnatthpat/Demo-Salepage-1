<?php

namespace App\Http\Controllers;

use App\Models\ProductSalepage;
use App\Services\CartService;

class ProductController extends Controller
{
    public function __construct(private CartService $cartService)
    {
    }

    public function show($id)
    {
        $salePageProduct = ProductSalepage::with(['images', 'options.images'])
            ->where('pd_sp_id', $id)
            ->firstOrFail();

        $coverImg = $salePageProduct->images->where('img_sort', 1)->first() ?? $salePageProduct->images->first();
        $activeImageUrl = $this->formatUrl($coverImg?->img_path ?? $coverImg?->image_path);

        $productImages = $salePageProduct->images->map(fn ($img) => (object) ['image_url' => $this->formatUrl($img->img_path ?? $img->image_path)]);

        // === Refactored Promotion Logic ===
        $promotions = $this->cartService->getPromotionsForProduct((int) $id);

        $product = (object) [
            'pd_sp_id' => $salePageProduct->pd_sp_id,
            'pd_name' => $salePageProduct->pd_sp_name,
            'pd_code' => $salePageProduct->pd_sp_code,
            'pd_details' => $salePageProduct->pd_sp_description,
            'pd_price' => (float) $salePageProduct->pd_sp_price,
            'pd_sp_discount' => (float) ($salePageProduct->pd_sp_discount ?? 0),
            'pd_sp_stock' => $salePageProduct->pd_sp_stock,
            'cover_image_url' => $activeImageUrl,
            'images' => $productImages,
            'options' => $salePageProduct->options,
        ];

        return view('product', compact('product', 'promotions'));
    }

    private function formatUrl($path)
    {
        if (! $path) {
            return 'https://via.placeholder.com/600x600.png?text=No+Image';
        }
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return asset('storage/'.ltrim(str_replace('storage/', '', $path), '/'));
    }
}
