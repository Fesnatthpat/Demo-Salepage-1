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
        
        // ค้นหาแคมเปญตาม ID หรือดึงตัวที่ Active และอยู่ในช่วงวันที่ใช้งาน (Priority: Dated -> Default)
        $campaign = $campaignId 
            ? BirthdayPromotion::with('giftProduct')->find($campaignId)
            : BirthdayPromotion::with('giftProduct')->activeForToday()->first();

        if (!$campaign) {
            return redirect(config('app.url'))->with('error', 'ไม่พบแคมเปญวันเกิดที่กำหนด');
        }

        // ✅ ตรวจสอบวันเกิด (ถ้า Login แล้ว)
        if (auth()->check()) {
            $user = auth()->user();
            
            // 1. ตรวจสอบว่าระบุวันเกิดหรือยัง
            if (!$user->date_of_birth) {
                // ถ้ายังไม่ระบุวันเกิด ให้แจ้งเตือนและพาไปหน้า Profile
                return redirect()->route('profile.edit')->with('warning', 'กรุณาระบุวันเกิดในข้อมูลส่วนตัวเพื่อรับสิทธิ์วันเกิดครับ');
            }

            // 2. ตรวจสอบว่าเดือนนี้เป็นเดือนเกิดหรือไม่
            if ($user->date_of_birth->month !== now()->month) {
                return redirect(config('app.url'))->with('error', 'ขออภัยครับ สิทธิ์นี้เฉพาะลูกค้าที่เกิดในเดือนนี้เท่านั้น');
            }

            // 3. ตรวจสอบว่าเคยรับของขวัญของแคมเปญนี้ไปหรือยัง (เช็คจาก PromotionUsageLog)
            if ($campaign->promotion_id) {
                $alreadyClaimed = \App\Models\PromotionUsageLog::where('promotion_id', $campaign->promotion_id)
                    ->where('user_id', $user->id)
                    ->whereHas('order', fn($q) => $q->where('status_id', '!=', 5))
                    ->exists();
                
                if ($alreadyClaimed) {
                    return redirect(config('app.url'))->with('info', 'คุณเคยรับสิทธิ์ของขวัญวันเกิดในปีนี้ไปเรียบร้อยแล้วครับ');
                }
            }
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
        $user = auth()->user();

        // ✅ ตรวจสอบซ้ำอีกครั้งตอนกด Apply (ความปลอดภัย)
        if (!$user->date_of_birth || $user->date_of_birth->month !== now()->month) {
            return redirect(config('app.url'))->with('error', 'ไม่สามารถรับสิทธิ์ได้เนื่องจากข้อมูลวันเกิดไม่ตรงเงื่อนไข');
        }

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
