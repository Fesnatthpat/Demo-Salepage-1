<?php

namespace App\Http\Controllers;

use App\Models\ProductSalepage;
use App\Models\Promotion;
use App\Models\PromotionRule;

class ProductController extends Controller
{
    /**
     * แสดงหน้ารายละเอียดสินค้า
     */
    public function show($id)
    {
        // 1. ดึงข้อมูลสินค้าหลัก
        $salePageProduct = ProductSalepage::with([
            'images',
            'options.images',
        ])->where('pd_sp_id', $id)->first();

        // 2. ถ้าไม่เจอสินค้า
        if (! $salePageProduct) {
            return redirect('/')->with('error', 'ไม่พบสินค้านี้');
        }

        // --- START: Promotion Logic (รองรับ DB ของคุณ) ---
        $now = now();
        $giftableProducts = collect();

        // ค้นหา Promotion ID ที่มี Rule ตรงกับสินค้านี้
        // (DB เก็บ JSON rules -> "product_id": "7")
        $relevantPromotionIds = PromotionRule::where(function ($query) use ($salePageProduct) {
            $query->where('rules->product_id', (string) $salePageProduct->pd_sp_id)
                ->orWhere('rules->product_id', (int) $salePageProduct->pd_sp_id);
        })
            ->pluck('promotion_id')
            ->unique();

        if ($relevantPromotionIds->isNotEmpty()) {
            // โหลด Promotion ที่ Active
            $activePromotions = Promotion::with([
                'rules',
                'actions.productToGet.images',    // ของแถมแบบ Fixed
                'actions.giftableProducts.images', // ของแถมแบบ Pool (จากตาราง promotion_action_gifts)
            ])
                ->whereIn('id', $relevantPromotionIds)
                ->where('is_active', true)
                ->where(function ($query) use ($now) {
                    $query->where('start_date', '<=', $now)->orWhereNull('start_date');
                })
                ->where(function ($query) use ($now) {
                    $query->where('end_date', '>=', $now)->orWhereNull('end_date');
                })
                ->get();

            // จัดการข้อมูลของแถม
            $giftableProducts = $activePromotions->flatMap(function ($promo) {
                return $promo->actions->flatMap(function ($action) {
                    $gifts = collect();

                    // กรณี 1: ของแถมแบบ Fixed (ระบุ ID ใน JSON)
                    if ($action->productToGet) {
                        $action->productToGet->gift_quantity = $action->quantity; // ใช้ Accessor: quantity_to_get
                        $gifts->push($action->productToGet);
                    }

                    // กรณี 2: ของแถมแบบ Pool (เลือกจาก promotion_action_gifts)
                    if ($action->giftableProducts->isNotEmpty()) {
                        foreach ($action->giftableProducts as $poolItem) {
                            $poolItem->gift_quantity = $action->quantity; // ใช้จำนวนจาก Action
                            $gifts->push($poolItem);
                        }
                    }

                    return $gifts;
                });
            })->unique('pd_sp_id'); // กรองของแถมซ้ำออก

        } else {
            $salePageProduct->active_promotions = collect();
        }
        // --- END: Promotion Logic ---

        // 3. เตรียมข้อมูลสินค้าเพื่อส่งไป View (Map ให้ตรงกับชื่อตัวแปรที่ View ใช้)
        $primaryImage = $salePageProduct->images->where('img_sort', 1)->first();
        $imagePath = $primaryImage ? $primaryImage->img_path : ($salePageProduct->images->first()->img_path ?? null);

        // แปลง Path รูปภาพให้สมบูรณ์ (URL)
        $activeImageUrl = $imagePath;
        if ($activeImageUrl && ! filter_var($activeImageUrl, FILTER_VALIDATE_URL)) {
            $activeImageUrl = asset('storage/'.str_replace('storage/', '', $activeImageUrl));
        }

        $product = (object) [
            'pd_sp_id' => $salePageProduct->pd_sp_id, // สำคัญ: ใช้สำหรับ Add to Cart
            'id' => $salePageProduct->pd_sp_id,
            'pd_name' => $salePageProduct->pd_sp_name,
            'pd_sp_name' => $salePageProduct->pd_sp_name,
            'pd_details' => $salePageProduct->pd_sp_description,
            'pd_sp_details' => $salePageProduct->pd_sp_description,
            'pd_price' => $salePageProduct->pd_sp_price,
            'pd_sp_price' => $salePageProduct->pd_sp_price,
            'pd_sp_discount' => $salePageProduct->pd_sp_discount ?? 0,
            'pd_code' => $salePageProduct->pd_sp_code,
            'pd_sp_code' => $salePageProduct->pd_sp_code,
            'pd_sp_stock' => $salePageProduct->pd_sp_stock ?? 0,

            // รูปภาพ
            'cover_image_url' => $activeImageUrl,
            'images' => $salePageProduct->images->map(function ($img) {
                $url = $img->img_path ?? $img->image_path;
                if ($url && ! filter_var($url, FILTER_VALIDATE_URL)) {
                    $url = asset('storage/'.str_replace('storage/', '', $url));
                }

                return (object) ['image_url' => $url];
            }),

            'options' => $salePageProduct->options,
        ];

        // ส่ง $giftableProducts ไปยัง View
        return view('product', compact('product', 'giftableProducts'));
    }
}
