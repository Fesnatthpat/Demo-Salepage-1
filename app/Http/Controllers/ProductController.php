<?php

namespace App\Http\Controllers;

use App\Models\ProductSalepage;
use App\Models\Promotion;
use App\Models\PromotionRule;

class ProductController extends Controller
{
    public function show($id)
    {
        $salePageProduct = ProductSalepage::with(['images', 'options.images'])
            ->where('pd_sp_id', $id)
            ->firstOrFail();

        $coverImg = $salePageProduct->images->where('img_sort', 1)->first() ?? $salePageProduct->images->first();
        $activeImageUrl = $this->formatUrl($coverImg?->img_path ?? $coverImg?->image_path);

        $productImages = $salePageProduct->images->map(fn ($img) => (object) ['image_url' => $this->formatUrl($img->img_path ?? $img->image_path)]);

        // Promotion Logic
        $now = now();
        $giftableProducts = collect();
        $promotionName = null;

        $relevantPromotionIds = PromotionRule::where(function ($query) use ($id) {
            $query->whereJsonContains('rules->product_id', (string) $id)
                ->orWhereJsonContains('rules->product_id', (int) $id);
        })->pluck('promotion_id')->unique();

        if ($relevantPromotionIds->isNotEmpty()) {
            $activePromotions = Promotion::with(['actions.giftableProducts.images'])
                ->whereIn('id', $relevantPromotionIds)
                ->where('is_active', true)
                ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', $now))
                ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $now))
                ->get();

            if ($activePromotions->isNotEmpty()) {
                $promotionName = $activePromotions->first()->name;
                $giftableProducts = $activePromotions->flatMap(function ($promo) {
                    return $promo->actions->flatMap(function ($action) {
                        $qty = $action->actions['quantity_to_get'] ?? 1;

                        return $action->giftableProducts->map(function ($gift) use ($qty) {
                            $gImg = $gift->images->first();
                            $gift->display_image = $this->formatUrl($gImg?->img_path ?? $gImg?->image_path);
                            $gift->gift_quantity = $qty;

                            return $gift;
                        });
                    });
                })->unique('pd_sp_id');
            }
        }

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

        return view('product', compact('product', 'giftableProducts', 'promotionName'));
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
