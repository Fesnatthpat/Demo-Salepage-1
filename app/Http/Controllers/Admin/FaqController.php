<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

use App\Models\SiteSetting;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $faqs = Faq::orderBy('sort_order', 'asc')->get();
        
        $settings = [
            'faq_badge' => SiteSetting::get('faq_badge', 'ศูนย์ช่วยเหลือ'),
            'faq_title' => SiteSetting::get('faq_title', 'คุณมีคำถาม <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-red-400">เรามีคำตอบ</span>'),
            'faq_subtitle' => SiteSetting::get('faq_subtitle', 'รวมคำถามที่พบบ่อยเกี่ยวกับการใช้งาน การสั่งซื้อ และการชำระเงิน'),
        ];

        return view('admin.faqs.index', compact('faqs', 'settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.faqs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);

        Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.faqs.index')
                         ->with('success', 'สร้างคำถามที่พบบ่อยเรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
        public function show(Faq $faq)
        {
            return redirect()->route('admin.faqs.edit', $faq);
        }
    
        /**
         * Show the form for editing the specified resource.
         */
        public function edit(Faq $faq)
        {
            return view('admin.faqs.edit', compact('faq'));
        }
    
        /**
         * Update the specified resource in storage.
         */
        public function update(Request $request, Faq $faq)
        {
            $request->validate([
                'question' => 'required|string|max:255',
                'answer' => 'required|string',
                'sort_order' => 'nullable|integer',
            ]);
    
            $faq->update([
                'question' => $request->question,
                'answer' => $request->answer,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => $request->boolean('is_active'),
            ]);
    
            return redirect()->route('admin.faqs.index')
                             ->with('success', 'อัปเดตคำถามที่พบบ่อยเรียบร้อยแล้ว');
        }
    
        /**
         * Remove the specified resource from storage.
         */
        public function destroy(Faq $faq)
        {
            $faq->delete();
    
            return redirect()->route('admin.faqs.index')
                             ->with('success', 'ลบคำถามที่พบบ่อยเรียบร้อยแล้ว');
        }
}
