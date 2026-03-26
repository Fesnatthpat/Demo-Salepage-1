<?php

namespace App\Http\Controllers;

use App\Models\ProductSalepage;
use App\Services\CartService;

class ProductController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function show($id)
    {
        // 1. ดึงข้อมูลสินค้าหลัก (เพิ่มเช็คว่าสินค้าเปิดใช้งานอยู่)
        $salePageProduct = ProductSalepage::with(['images', 'options.stock', 'reviewImages', 'stock'])
            ->where('pd_sp_id', $id)
            ->where('pd_sp_active', 1)
            ->firstOrFail();

        $coverImg = $salePageProduct->images->where('img_sort', 0)->first() ?? $salePageProduct->images->sortBy('img_sort')->first();
        $activeImageUrl = $this->formatUrl($coverImg?->img_path ?? $coverImg?->image_path);
        $productImages = $salePageProduct->images->map(fn ($img) => (object) ['image_url' => $this->formatUrl($img->img_path ?? $img->image_path)]);

        // 2. ดึงโปรโมชั่น
        $promotions = $this->cartService->getPromotionsForProduct((int) $id);

        // 🛡️ กรองให้เหลือเฉพาะโปรโมชั่น "ซื้อคู่ (Bundle)" หรือ "ของแถม/1แถม1 (Free Gift/BXGY)"
        $promotions = $promotions->filter(function($promo) {
            // 1. มีของแถม (ของแถม / 1 แถม 1)
            if ($promo->actions->isNotEmpty()) {
                return true;
            }
            // 2. เป็นโปรซื้อคู่ (มีกฎหลายข้อและต้องซื้อร่วมกันครบทุกอัน)
            if ($promo->rules->count() > 1 && ($promo->condition_type ?? 'any') === 'all') {
                return true;
            }
            // โปรอื่นๆ เช่น ลดราคาทั่วไป (Auto Discount), ส่งฟรี หรือรหัสส่วนลด 
            // จะไม่แสดงในกล่องโปรโมชั่นหน้าสินค้า (เพื่อลดความซับซ้อน)
            return false;
        });

        // ดึงตะกร้าผ่าน Service เพื่อความชัวร์
        $cartContent = $this->cartService->getCartContents();

        // หาจำนวนสินค้านี้ที่มีอยู่ในตะกร้าแล้ว
        $currentCartQty = $cartContent->where('id', $id)->first()->quantity ?? 0;

        // 3. Map ข้อมูลโปรโมชั่น + ตรวจสอบเงื่อนไข
        $promotions->map(function ($promo) use ($id, $currentCartQty, $cartContent) {

            $promo->partner_products = collect();

            $myRule = $promo->rules->filter(function ($rule) use ($id) {
                $pids = $rule->rules['product_id'] ?? [];
                if (! is_array($pids)) {
                    $pids = [$pids];
                }

                return in_array((string) $id, array_map('strval', $pids));
            })->first();
            $requiredQty = $myRule ? ($myRule->rules['quantity_to_buy'] ?? 1) : 1;

            $otherRulesMet = true;
            $partnerProductIds = [];

            if (($promo->condition_type ?? 'any') === 'all') {
                $cartQuantities = $cartContent->pluck('quantity', 'id')->toArray();

                foreach ($promo->rules as $rule) {
                    $pids = $rule->rules['product_id'] ?? [];
                    if (! is_array($pids)) {
                        $pids = [$pids];
                    }

                    if (in_array((string) $id, array_map('strval', $pids))) {
                        continue;
                    }

                    foreach ($pids as $pid) {
                        $partnerProductIds[] = (int) $pid;
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

        // 4. เตรียมข้อมูลส่งไปหน้าเว็บ
        $product = (object) [
            'pd_sp_id' => $salePageProduct->pd_sp_id,
            'pd_name' => $salePageProduct->pd_sp_name,
            'pd_code' => $salePageProduct->pd_sp_code,
            'pd_details' => $salePageProduct->pd_sp_description,
            'pd_price' => (float) $salePageProduct->pd_sp_price,
            'pd_price2' => (float) ($salePageProduct->pd_sp_price2 ?? 0),
            'display_price' => $salePageProduct->display_price, // ดึงจาก Accessor
            'pd_sp_discount' => (float) ($salePageProduct->pd_sp_discount ?? 0),
            'pd_sp_stock' => $salePageProduct->pd_sp_stock,     // ดึงจาก Accessor
            'cover_image_url' => $activeImageUrl,
            'images' => $productImages,
            'options' => $salePageProduct->options,
            'reviewImages' => $salePageProduct->reviewImages,
        ];

        $settings = \App\Models\SiteSetting::getAllSettings();

        return view('product', compact('product', 'promotions', 'settings'));
    }

    private function formatUrl($path)
    {
        if (! $path) {
            return 'https://via.placeholder.com/600x600.png?text=No+Image';
        }
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }
}
