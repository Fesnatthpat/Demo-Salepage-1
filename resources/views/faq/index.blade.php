@extends('layout')

@section('title', 'คำถามที่พบบ่อย')

@section('content')
    <div class="min-h-screen bg-slate-50 py-16 px-4 sm:px-6 lg:px-8 font-sans antialiased relative overflow-hidden">
        
        {{-- Background Decoration (เพิ่มมิติให้กับพื้นหลัง) --}}
        <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-red-600/10 to-transparent -z-10"></div>
        <div class="absolute top-[-10%] right-[-5%] w-96 h-96 bg-red-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 -z-10"></div>
        <div class="absolute top-[20%] left-[-10%] w-72 h-72 bg-red-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 -z-10"></div>

        <div class="max-w-4xl mx-auto relative z-10">

            {{-- 1. ส่วนหัว (Header Section) --}}
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-50 text-red-600 text-sm font-semibold mb-6 shadow-sm border border-red-100/50 backdrop-blur-sm transition-transform hover:scale-105">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                    ศูนย์ช่วยเหลือ
                </div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 mb-6 tracking-tight leading-tight">
                    คุณมีคำถาม <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-red-400">เรามีคำตอบ</span>
                </h1>
                <p class="text-lg md:text-xl text-slate-500 max-w-2xl mx-auto">
                    ค้นหาคำตอบสำหรับคำถามที่พบบ่อยได้ที่นี่ เพื่อให้การใช้งานของคุณราบรื่นที่สุด
                </p>
            </div>

            {{-- ตัวอย่างแถบหมวดหมู่ (ถ้าไม่ใช้ ลบออกได้ครับ) --}}
            <div class="flex flex-wrap justify-center gap-3 mb-12">
                <button class="px-6 py-2.5 rounded-full bg-red-600 text-white font-medium shadow-md shadow-red-600/30 transition-all">ทั้งหมด</button>
                <button class="px-6 py-2.5 rounded-full bg-white text-slate-600 font-medium hover:bg-slate-100 border border-slate-200 transition-all">บัญชีและการใช้งาน</button>
                <button class="px-6 py-2.5 rounded-full bg-white text-slate-600 font-medium hover:bg-slate-100 border border-slate-200 transition-all">การชำระเงิน</button>
            </div>

            {{-- 2. ส่วนรายการคำถาม (Accordion List) --}}
            <div class="space-y-4">

                @forelse ($faqs as $index => $faq)
                    <div class="collapse collapse-plus bg-white border border-slate-200/60 shadow-sm hover:shadow-md hover:border-red-300/50 transition-all duration-300 rounded-2xl group">
                        
                        {{-- Radio สำหรับเปิดทีละข้อ --}}
                        <input type="radio" name="faq-accordion" class="peer" @if ($loop->first) checked="checked" @endif />
                        
                        {{-- ชื่อคำถาม --}}
                        <div class="collapse-title text-base md:text-lg font-semibold text-slate-800 peer-checked:text-red-600 flex items-center gap-4 py-5 pl-6 pr-12">
                            {{-- ไอคอนประกอบคำถาม (สลับไอคอนตามต้องการ) --}}
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-red-50 group-hover:text-red-500 peer-checked:bg-red-100 peer-checked:text-red-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <span>{{ $faq->question }}</span>
                        </div>

                        {{-- เนื้อหาคำตอบ --}}
                        <div class="collapse-content text-slate-600">
                            {{-- เส้นกั้นบางๆ ด้านบนเนื้อหา --}}
                            <div class="pt-2 pb-4 px-2 md:px-14">
                                <div class="w-full h-px bg-gradient-to-r from-slate-200 to-transparent mb-4"></div>
                                <div class="leading-relaxed text-sm md:text-base prose prose-red max-w-none">
                                    {!! nl2br(e($faq->answer)) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                @empty
                    {{-- 3. กรณีไม่มีข้อมูล (Empty State) --}}
                    <div class="text-center py-20 px-4 bg-white/50 backdrop-blur-sm rounded-3xl border-2 border-dashed border-slate-300 shadow-sm">
                        <div class="w-24 h-24 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-6 border border-slate-200 relative">
                            <i class="fas fa-folder-open text-4xl text-slate-400"></i>
                            <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                <span class="w-2 h-2 bg-red-500 rounded-full animate-ping"></span>
                                <span class="absolute w-2 h-2 bg-red-500 rounded-full"></span>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">กำลังเตรียมข้อมูลคำถาม</h3>
                        <p class="text-slate-500">ทีมงานกำลังรวบรวมคำถามที่เป็นประโยชน์ และจะนำมาอัปเดตให้ทราบเร็วๆ นี้ครับ</p>
                    </div>
                @endforelse

            </div>

            {{-- 4. ส่วนติดต่อเพิ่มเติม (Call to Action) --}}
            <div class="mt-16 relative overflow-hidden bg-gradient-to-br from-red-600 to-red-800 rounded-3xl p-10 md:p-14 shadow-2xl shadow-red-900/20 text-center">
                {{-- ลวดลายตกแต่งในกล่อง --}}
                <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full transform translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-white opacity-10 rounded-full transform -translate-x-1/2 translate-y-1/2"></div>
                
                <div class="relative z-10">
                    <h3 class="text-3xl font-bold text-white mb-4">ยังมีข้อสงสัยเพิ่มเติมใช่ไหม?</h3>
                    <p class="text-red-100 text-lg mb-8 max-w-xl mx-auto">
                        ไม่ต้องกังวล! ทีมงานผู้เชี่ยวชาญของเราพร้อมให้คำปรึกษาและช่วยเหลือคุณทุกขั้นตอน ติดต่อเราได้ทันที
                    </p>
                    <a href="#" class="inline-flex items-center justify-center gap-2 bg-white text-red-700 hover:bg-slate-50 hover:scale-105 transition-all duration-300 px-8 py-4 rounded-full font-bold shadow-lg shadow-black/10 text-lg">
                        <i class="fas fa-headset"></i> ติดต่อฝ่ายบริการลูกค้า
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection