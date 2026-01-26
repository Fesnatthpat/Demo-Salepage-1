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
        $salePageProduct = ProductSalepage::with(['images', 'options.images'])
            ->where('pd_sp_id', $id)
            ->firstOrFail();

        $coverImg = $salePageProduct->images->where('img_sort', 1)->first() ?? $salePageProduct->images->first();
        $activeImageUrl = $this->formatUrl($coverImg?->img_path ?? $coverImg?->image_path);
        $productImages = $salePageProduct->images->map(fn ($img) => (object) ['image_url' => $this->formatUrl($img->img_path ?? $img->image_path)]);

        // 1. ดึงโปรโมชั่น
        $promotions = $this->cartService->getPromotionsForProduct((int) $id);

        // ดึงจำนวนสินค้าชิ้นนี้ที่มีอยู่ในตะกร้าแล้ว (เพื่อเอาไปคำนวณต่อ)
        $userId = Auth::check() ? Auth::id() : '_guest_'.session()->getId();
        $cartContent = Cart::session($userId)->getContent();
        $currentCartQty = $cartContent->where('id', $id)->first()->quantity ?? 0;

        // 2. Map ข้อมูลโปรโมชั่นเพื่อส่งไปคำนวณหน้าบ้าน
        $promotions->map(function ($promo) use ($id, $currentCartQty, $cartContent) {

            // หาว่าสินค้านี้ (ID ปัจจุบัน) ต้องการจำนวนเท่าไหร่ในโปรโมชั่นนี้
            $myRule = $promo->rules->filter(function ($rule) use ($id) {
                $pids = $rule->rules['product_id'] ?? [];
                if (! is_array($pids)) {
                    $pids = [$pids];
                }

                return in_array((string) $id, array_map('strval', $pids));
            })->first();

            // จำนวนที่ต้องซื้อของสินค้านี้ (ถ้าหาไม่เจอให้เป็น 1)
            $requiredQty = $myRule ? ($myRule->rules['quantity_to_buy'] ?? 1) : 1;

            // ตรวจสอบสินค้า *ชิ้นอื่น* ในเงื่อนไข (กรณี Buy A + B)
            $otherRulesMet = true;
            if (($promo->condition_type ?? 'any') === 'all') {
                $cartQuantities = $cartContent->pluck('quantity', 'id')->toArray();

                foreach ($promo->rules as $rule) {
                    $pids = $rule->rules['product_id'] ?? [];
                    if (! is_array($pids)) {
                        $pids = [$pids];
                    }

                    // ถ้ากฎข้อนี้เป็นของสินค้าปัจจุบัน ให้ข้ามไปก่อน (เพราะเราจะเช็ค Real-time หน้าเว็บ)
                    if (in_array((string) $id, array_map('strval', $pids))) {
                        continue;
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
                        break;
                    }
                }
            }

            // ส่งตัวแปรพิเศษออกไปให้ JavaScript ใช้
            $promo->frontend_logic = [
                'required_qty' => (int) $requiredQty,     // ต้องซื้อกี่ชิ้น
                'cart_qty' => (int) $currentCartQty,      // ในตะกร้ามีแล้วกี่ชิ้น
                'other_rules_met' => $otherRulesMet,     // เงื่อนไขสินค้าอื่นครบหรือยัง
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
