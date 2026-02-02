<?php

namespace App\Http\Controllers;

use App\Models\ProductSalepage;
use App\Services\CartService;

class ProductController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function show($id)
    {
        // 1. ดึงข้อมูลสินค้าหลัก
        $salePageProduct = ProductSalepage::with(['images', 'options'])
            ->where('pd_sp_id', $id)
            ->firstOrFail();

        $coverImg = $salePageProduct->images->where('img_sort', 1)->first() ?? $salePageProduct->images->first();
        $activeImageUrl = $this->formatUrl($coverImg?->img_path ?? $coverImg?->image_path);
        $productImages = $salePageProduct->images->map(fn ($img) => (object) ['image_url' => $this->formatUrl($img->img_path ?? $img->image_path)]);

        // 2. ดึงโปรโมชั่น
        $promotions = $this->cartService->getPromotionsForProduct((int) $id);

        // ★★★ จุดที่แก้ไข: ดึงตะกร้าผ่าน Service เพื่อความชัวร์ (โหลดจาก DB ถ้าจำเป็น) ★★★
        $cartContent = $this->cartService->getCartContents();

        // หาจำนวนสินค้านี้ที่มีอยู่ในตะกร้าแล้ว
        $currentCartQty = $cartContent->where('id', $id)->first()->quantity ?? 0;

        // 3. Map ข้อมูลโปรโมชั่น + ตรวจสอบเงื่อนไข
        $promotions->map(function ($promo) use ($id, $currentCartQty, $cartContent) {

            // สร้าง Collection ว่างไว้ก่อนเสมอ
            $promo->partner_products = collect();

            // หาว่าสินค้านี้ (ID ปัจจุบัน) ต้องซื้อกี่ชิ้นในโปรโมชั่นนี้
            $myRule = $promo->rules->filter(function ($rule) use ($id) {
                $pids = $rule->rules['product_id'] ?? [];
                if (! is_array($pids)) {
                    $pids = [$pids];
                }

                return in_array((string) $id, array_map('strval', $pids));
            })->first();
            $requiredQty = $myRule ? ($myRule->rules['quantity_to_buy'] ?? 1) : 1;

            // เช็คเงื่อนไขสินค้าอื่น (เช่น ต้องซื้อ A คู่กับ B)
            $otherRulesMet = true;
            $partnerProductIds = [];

            if (($promo->condition_type ?? 'any') === 'all') {
                $cartQuantities = $cartContent->pluck('quantity', 'id')->toArray();

                foreach ($promo->rules as $rule) {
                    $pids = $rule->rules['product_id'] ?? [];
                    if (! is_array($pids)) {
                        $pids = [$pids];
                    }

                    // ถ้ากฎข้อนี้เป็นของสินค้าปัจจุบัน ให้ข้ามไป (เพราะเราจะเช็ค Real-time ที่หน้าเว็บ)
                    if (in_array((string) $id, array_map('strval', $pids))) {
                        continue;
                    }

                    // ถ้าเป็นสินค้าอื่น ให้เก็บ ID ไว้แสดงเป็นสินค้าแนะนำ
                    foreach ($pids as $pid) {
                        $partnerProductIds[] = (int) $pid;
                    }

                    // ตรวจสอบว่าสินค้าอื่นมีในตะกร้าครบตามจำนวนหรือยัง
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

            // ดึงข้อมูลสินค้าคู่ขา (Partner)
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

            // ส่ง Logic ไปให้หน้าบ้านคำนวณต่อ
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

        return asset('storage/'.ltrim($path, '/'));
    }
}
