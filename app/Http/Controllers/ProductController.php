<?php

namespace App\Http\Controllers;

use App\Models\ProductSalepage;
use App\Models\Promotion;
use App\Models\PromotionRule;

class ProductController extends Controller
{
    /**
     * แสดงหน้ารายละเอียดสินค้า
     *
     * @param  int|string  $id  รหัสสินค้า (pd_sp_id)
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        // 1. ดึงข้อมูลสินค้า (ใช้ pd_sp_id เป็นหลัก)
        $salePageProduct = ProductSalepage::with([
            'images',
            'options.images',
        ])->where('pd_sp_id', $id)->first();

        // 2. ถ้าไม่เจอสินค้า ให้เด้งกลับหน้าแรก
        if (! $salePageProduct) {
            return redirect('/')->with('error', 'ไม่พบสินค้านี้');
        }

        // --- START: Promotion Logic (แก้ไข Logic ส่วนนี้) ---
        $now = now();
        $giftableProducts = collect(); // เตรียมตะกร้าไว้ใส่ของแถม

        // 1. ค้นหา ID ของโปรโมชั่นที่สินค้านี้เข้าร่วม
        // ใช้การ cast ทั้ง String และ Int เพื่อความชัวร์ในการเทียบกับ JSON
        $relevantPromotionIds = PromotionRule::where(function ($query) use ($salePageProduct) {
            $query->where('rules->product_id', (string) $salePageProduct->pd_sp_id)
                ->orWhere('rules->product_id', (int) $salePageProduct->pd_sp_id);
        })
            ->pluck('promotion_id')
            ->unique();

        if ($relevantPromotionIds->isNotEmpty()) {
            // 2. โหลดโปรโมชั่น พร้อมของแถมทั้ง 2 รูปแบบ
            $activePromotions = Promotion::with([
                'rules',
                // แบบ A: Fixed Gift (ระบุตัวเจาะจงใน Action)
                'actions.productToGet.images',
                // แบบ B: Pool Gift (มีหลายตัวให้เลือก)
                'actions.giftableProducts.images',
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

            $salePageProduct->active_promotions = $activePromotions;

            // 3. รวมของแถมทั้งหมดมาไว้ในตัวแปรเดียว ($giftableProducts)
            $giftableProducts = $activePromotions->flatMap(function ($promo) {
                return $promo->actions->flatMap(function ($action) {
                    $gifts = collect();

                    // กรณี A: มีของแถมแบบเจาะจง (ProductToGet)
                    if ($action->productToGet) {
                        // แปะจำนวนที่แถม (gift_quantity) เข้าไปที่ตัวสินค้าเพื่อให้หน้าบ้านรู้
                        $action->productToGet->gift_quantity = $action->quantity;
                        $gifts->push($action->productToGet);
                    }

                    // กรณี B: มีของแถมแบบเลือกได้ (GiftableProducts)
                    if ($action->giftableProducts->isNotEmpty()) {
                        foreach ($action->giftableProducts as $poolItem) {
                            $poolItem->gift_quantity = $action->quantity;
                            $gifts->push($poolItem);
                        }
                    }

                    return $gifts;
                });
            })->unique('pd_sp_id'); // ตัดของแถมที่ซ้ำกันออก

        } else {
            $salePageProduct->active_promotions = collect();
        }
        // --- END: Promotion Logic ---

        // 3. Logic หา "รูปปก"
        $primaryImage = $salePageProduct->images->where('img_sort', 1)->first();
        $imagePath = $primaryImage ? $primaryImage->img_path : ($salePageProduct->images->first()->img_path ?? null);

        // 4. Map ข้อมูล
        $product = (object) [
            // --- ID ---
            'id' => $salePageProduct->pd_sp_id,
            'pd_id' => $salePageProduct->pd_sp_id,
            'pd_sp_id' => $salePageProduct->pd_sp_id,
            // --- Details ---
            'pd_name' => $salePageProduct->pd_sp_name,
            'pd_sp_name' => $salePageProduct->pd_sp_name,
            'pd_details' => $salePageProduct->pd_sp_description,
            'pd_sp_details' => $salePageProduct->pd_sp_description,
            // --- Price ---
            'pd_price' => $salePageProduct->pd_sp_price,
            'pd_sp_price' => $salePageProduct->pd_sp_price,
            'pd_sp_discount' => $salePageProduct->pd_sp_discount ?? 0,
            // --- Code & Stock ---
            'pd_code' => $salePageProduct->pd_sp_code,
            'pd_sp_code' => $salePageProduct->pd_sp_code,
            'quantity' => $salePageProduct->pd_sp_stock ?? 0,
            'pd_sp_stock' => $salePageProduct->pd_sp_stock ?? 0,
            // --- Images ---
            'pd_img' => $imagePath,
            'images' => $salePageProduct->images,
            // --- Relations ---
            'options' => $salePageProduct->options,
            'active_promotions' => $salePageProduct->active_promotions,

            'brand_name' => null,
        ];

        // ส่ง $giftableProducts ไปยัง View
        return view('product', compact('product', 'giftableProducts'));
    }
}
