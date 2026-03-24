<?php

namespace App\Services;

use App\Models\ProductSalepage;
use App\Models\StockProduct;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    /**
     * Get the total stock for a product.
     * Optimizes performance by using loaded relationships if available.
     */
    public function getStock(ProductSalepage $product): int
    {
        // 1. If 'options' are already loaded, sum their stock (if 'options.stock' is also loaded, it's very fast)
        if ($product->relationLoaded('options')) {
            if ($product->options->isEmpty()) {
                // No options, use main stock if loaded
                if ($product->relationLoaded('stock')) {
                    return (int) ($product->stock ? $product->stock->quantity : 0);
                }
            } else {
                return (int) $product->options->sum(function ($option) {
                    return $option->relationLoaded('stock')
                        ? ($option->stock ? $option->stock->quantity : 0)
                        : $option->option_stock;
                });
            }
        }

        // 2. If no options loaded but 'stock' is loaded (for products without options)
        if ($product->relationLoaded('stock')) {
            return (int) ($product->stock ? $product->stock->quantity : 0);
        }

        // 3. Fallback: Query database (avoid this by eager loading 'options.stock' or 'stock')
        if ($product->options()->exists()) {
            return (int) StockProduct::where('pd_sp_id', $product->pd_sp_id)
                ->whereNotNull('option_id')
                ->sum('quantity');
        }

        return (int) ($product->stock ? $product->stock->quantity : 0);
    }

    /**
     * Get the cover image URL for a product.
     * Minimizes slow disk operations (file_exists).
     */
    public function getCoverImageUrl(ProductSalepage $product): string
    {
        // Use eager-loaded images if available
        $image = $product->relationLoaded('images')
            ? $product->images->sortBy('img_sort')->first()
            : $product->images()->orderBy('img_sort')->first();

        if (! $image || ! $image->img_path) {
            $fallbackId = ($product->pd_sp_id % 8) + 1;
            return asset("images/pd-{$fallbackId}.png");
        }

        $rawPath = $image->img_path;

        if (filter_var($rawPath, FILTER_VALIDATE_URL)) {
            return $rawPath;
        }

        // Optimization: Skip file_exists check in production if you trust your database,
        // or wrap it in a short cache to avoid repetitive disk hits in the same request.
        return asset('storage/'.ltrim($rawPath, '/'));
    }

    /**
     * Get the display price (range or single price).
     */
    public function getDisplayPrice(ProductSalepage $product)
    {
        if ($product->relationLoaded('options')) {
            if ($product->options->isEmpty()) {
                return (int) $product->pd_sp_price;
            }

            $prices = $product->options->pluck('option_price');
            $minPrice = $prices->min();
            $maxPrice = $prices->max();

            if ($minPrice != $maxPrice) {
                return (int)$minPrice . '-' . (int)$maxPrice;
            }

            return (int)$minPrice;
        }

        // Fallback to query if not loaded
        if ($product->options()->exists()) {
            $minPrice = $product->options()->min('option_price');
            $maxPrice = $product->options()->max('option_price');

            if ($minPrice != $maxPrice) {
                return (int)$minPrice . '-' . (int)$maxPrice;
            }

            return (int)$minPrice;
        }

        return (int)$product->pd_sp_price;
    }
}
