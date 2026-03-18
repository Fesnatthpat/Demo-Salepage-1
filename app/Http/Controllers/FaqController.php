<?php

namespace App\Http\Controllers;

use App\Models\Faq;

use App\Models\SiteSetting;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();
            
        $settings = [
            'faq_badge' => SiteSetting::get('faq_badge', 'ศูนย์ช่วยเหลือ'),
            'faq_title' => SiteSetting::get('faq_title', 'คุณมีคำถาม <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-red-400">เรามีคำตอบ</span>'),
            'faq_subtitle' => SiteSetting::get('faq_subtitle', 'รวมคำถามที่พบบ่อยเกี่ยวกับการใช้งาน การสั่งซื้อ และการชำระเงิน'),
        ];

        return view('faq.index', compact('faqs', 'settings'));
    }
}
