<?php

namespace App\Http\Controllers;

use App\Models\ProductSalepage;
use App\Services\CartService;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\Auth;

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

        // 1. ดึงโปรโมชั่นที่เกี่ยวข้อง
        $promotions = $this->cartService->getPromotionsForProduct((int) $id);

        // 2. ★★★ Logic ใหม่: ตรวจสอบเงื่อนไขแล้วแปะป้ายสถานะ (ไม่กรองทิ้ง) ★★★
        $promotions->map(function ($promo) use ($id) {
            $conditionType = $promo->condition_type ?? 'any';
            $isMet = true;

            // ถ้าเงื่อนไขเป็น 'all' (ต้องครบทุกข้อ) -> เช็คตะกร้า
            if ($conditionType === 'all') {
                $userId = Auth::check() ? Auth::id() : '_guest_' . session()->getId();
                $cartContent = Cart::session($userId)->getContent();

                // จำลองตะกร้า: ของเดิม + สินค้าชิ้นนี้ 1 ชิ้น
                $cartQuantities = $cartContent->pluck('quantity', 'id')->toArray();
                $cartQuantities[$id] = ($cartQuantities[$id] ?? 0) + 1;

                foreach ($promo->rules as $rule) {
                    $requiredPids = $rule->rules['product_id'] ?? [];
                    if (!is_array($requiredPids)) $requiredPids = [$requiredPids];
                    $requiredQty = $rule->rules['quantity_to_buy'] ?? 1;

                    // เช็คว่ากฎข้อนี้ผ่านไหม
                    $ruleMet = false;
                    foreach ($requiredPids as $reqPid) {
                        $qty = $cartQuantities[(int)$reqPid] ?? 0;
                        if ($qty >= $requiredQty) {
                            $ruleMet = true;
                            break;
                        }
                    }

                    if (!$ruleMet) {
                        $isMet = false; // มีข้อใดข้อหนึ่งไม่ผ่าน -> ปรับสถานะเป็น False
                        break; 
                    }
                }
            }

            // บันทึกสถานะลงใน Object โปรโมชั่น
            $promo->is_condition_met = $isMet;
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
        if (!$path) return 'https://via.placeholder.com/600x600.png?text=No+Image';
        if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
        return asset('storage/'.ltrim(str_replace('storage/', '', $path), '/'));
    }
}