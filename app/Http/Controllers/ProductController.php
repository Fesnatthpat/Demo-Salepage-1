<?php

namespace App\Http\Controllers;

use App\Models\ProductSalepage;
use App\Services\CartService;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function show($id)
    {
        // 1. ดึงข้อมูลสินค้าหลัก
        $salePageProduct = ProductSalepage::with(['images', 'options.images'])
            ->where('pd_sp_id', $id)
            ->firstOrFail();

        $coverImg = $salePageProduct->images->where('img_sort', 1)->first() ?? $salePageProduct->images->first();
        $activeImageUrl = $this->formatUrl($coverImg?->img_path ?? $coverImg?->image_path);
        $productImages = $salePageProduct->images->map(fn ($img) => (object) ['image_url' => $this->formatUrl($img->img_path ?? $img->image_path)]);

        // 2. ดึงโปรโมชั่นที่เกี่ยวข้อง
        $promotions = $this->cartService->getPromotionsForProduct((int) $id);

        $userId = Auth::check() ? Auth::id() : '_guest_'.session()->getId();
        $cartContent = Cart::session($userId)->getContent();
        $currentCartQty = $cartContent->where('id', $id)->first()->quantity ?? 0;

        // 3. Map ข้อมูลโปรโมชั่น + หาสินค้าที่ต้องซื้อคู่กัน
        $promotions->map(function ($promo) use ($id, $currentCartQty, $cartContent) {

            // ✅ Fix: สร้าง Collection ว่างไว้ก่อนเสมอ ป้องกัน Error "Call to member function map() on null"
            $promo->partner_products = collect();

            // หาว่าสินค้านี้ต้องซื้อกี่ชิ้นในโปรโมชั่นนี้
            $myRule = $promo->rules->filter(function ($rule) use ($id) {
                $pids = $rule->rules['product_id'] ?? [];
                if (! is_array($pids)) {
                    $pids = [$pids];
                }

                return in_array((string) $id, array_map('strval', $pids));
            })->first();
            $requiredQty = $myRule ? ($myRule->rules['quantity_to_buy'] ?? 1) : 1;

            // เช็คเงื่อนไขสินค้าอื่น (Other Rules)
            $otherRulesMet = true;
            $partnerProductIds = [];

            if (($promo->condition_type ?? 'any') === 'all') {
                $cartQuantities = $cartContent->pluck('quantity', 'id')->toArray();

                foreach ($promo->rules as $rule) {
                    $pids = $rule->rules['product_id'] ?? [];
                    if (! is_array($pids)) {
                        $pids = [$pids];
                    }

                    // เก็บ ID สินค้าอื่นที่เป็น Partner
                    if (! in_array((string) $id, array_map('strval', $pids))) {
                        foreach ($pids as $pid) {
                            $partnerProductIds[] = (int) $pid;
                        }
                    }

                    $reqQ = $rule->rules['quantity_to_buy'] ?? 1;
                    $met = false;
                    foreach ($pids as $pid) {
                        if (($cartQuantities[(int) $pid] ?? 0) >= $reqQ) {
                            $met = true;
                            break;
                        }
                    }
                    if (! $met) {
                        $otherRulesMet = false;
                    }
                }
            }

            // ดึงข้อมูลสินค้า Partner (ถ้ามี)
            if (! empty($partnerProductIds)) {
                $promo->partner_products = ProductSalepage::with('images')
                    ->whereIn('pd_sp_id', array_unique($partnerProductIds))
                    ->get()
                    ->map(function ($p) {
                        $img = $p->images->first();
                        $p->display_image = $this->formatUrl($img?->img_path ?? $img?->image_path);

                        return $p;
                    });
            }

            $promo->frontend_logic = [
                'required_qty' => (int) $requiredQty,
                'cart_qty' => (int) $currentCartQty,
                'other_rules_met' => $otherRulesMet,
                'condition_type' => $promo->condition_type ?? 'any',
            ];

            return $promo;
        });

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
