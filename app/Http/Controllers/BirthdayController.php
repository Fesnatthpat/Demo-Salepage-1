<?php

namespace App\Http\Controllers;

use App\Models\BirthdayPromotion;
use App\Models\ProductSalepage;
use App\Services\CartService;
use Illuminate\Http\Request;

class BirthdayController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * หน้า Landing Page สำหรับรับสิทธิ์วันเกิด
     */
    public function claim(Request $request)
    {
        // หากยังไม่ได้ Login ให้เก็บ URL หน้าใบอวยพรนี้ไว้ เพื่อให้ Login เสร็จแล้ววนกลับมาที่เดิม
        if (!auth()->check()) {
            session(['birthday_redirect_url' => $request->fullUrl()]);
            // สำรองไว้ใน Cookie ด้วย 30 นาที (กัน Session หายระหว่างไป LINE)
            cookie()->queue('birthday_redirect_backup', $request->fullUrl(), 30);
        }

        $campaignId = $request->query('id');
        
        // ค้นหาแคมเปญตาม ID หรือดึงตัวที่ Active ล่าสุดถ้าไม่ได้ระบุ ID
        $campaign = $campaignId 
            ? BirthdayPromotion::with('giftProduct')->find($campaignId)
            : BirthdayPromotion::with('giftProduct')->where('is_active', true)->first();

        if (!$campaign) {
            return redirect(config('app.url'))->with('error', 'ไม่พบแคมเปญวันเกิดที่กำหนด');
        }

        return view('birthday.claim', compact('campaign'));
    }

    /**
     * ดำเนินการใช้โค้ดและเพิ่มของแถมเข้าตะกร้า
     */
    public function apply(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:birthday_promotions,id',
        ]);

        $campaign = BirthdayPromotion::findOrFail($request->campaign_id);

        try {
            // 1. ใช้รหัสส่วนลด (ถ้ามี)
            if ($campaign->discount_code) {
                $this->cartService->applyPromoCode($campaign->discount_code);
            }

            // 2. เพิ่มของแถมเข้าตะกร้า (ถ้ามี)
            if ($campaign->gift_product_id) {
                $this->cartService->addBirthdayGift($campaign->gift_product_id);
            }

            return redirect(config('app.url'))->with('success', 'ใช้รหัสวันเกิดและเพิ่มของขวัญเรียบร้อยแล้ว! เลือกสินค้าที่ต้องการได้เลยครับ');
        } catch (\Exception $e) {
            return redirect(config('app.url'))->with('error', 'เกิดข้อผิดพลาดในการรับสิทธิ์: ' . $e->getMessage());
        }
    }
}
