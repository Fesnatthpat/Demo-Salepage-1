@extends('layout')

@section('title', 'คำถามที่พบบ่อย')

@section('content')
    {{-- 
        ★ แก้ไขจุดที่ 1: ใช้ style + asset() เพื่อระบุที่อยู่รูปภาพแบบเจาะจง 
        (วิธีนี้ชัวร์ที่สุดสำหรับ Laravel)
    --}}
    <div class="min-h-screen bg-cover bg-center bg-fixed py-16 px-4 sm:px-6 lg:px-8 font-sans antialiased relative overflow-hidden"
        style="background-image: url('{{ asset('') }}');">

        {{-- Background Decoration --}}
        <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-red-600/10 to-transparent -z-10"></div>
        <div
            class="absolute top-[-10%] right-[-5%] w-96 h-96 bg-red-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 -z-10">
        </div>
        <div
            class="absolute top-[20%] left-[-10%] w-72 h-72 bg-red-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 -z-10">
        </div>

        {{-- 
            ★ แก้ไขจุดที่ 2: ใช้ style + asset() สำหรับกล่องเนื้อหาเช่นกัน
        --}}
        <div
            class="max-w-4xl mx-auto relative z-10 bg-white rounded-3xl shadow-xl p-6 md:p-10 border border-white/50">

            {{-- ส่วนหัว --}}
            <div class="text-center mb-10">
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-50 text-red-600 text-sm font-semibold mb-6 shadow-sm border border-red-100/50 backdrop-blur-sm transition-transform hover:scale-105">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                            clip-rule="evenodd"></path>
                    </svg>
                    {{ $settings['faq_badge'] ?? 'ศูนย์ช่วยเหลือ' }}
                </div>
                <h1 class="text-3xl md:text-5xl font-extrabold text-slate-900 mb-4 tracking-tight leading-tight">
                    {!! $settings['faq_title'] ?? 'คุณมีคำถาม <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-red-400">เรามีคำตอบ</span>' !!}
                </h1>
                <p class="text-lg text-slate-500 max-w-2xl mx-auto">
                    {{ $settings['faq_subtitle'] ?? 'รวมคำถามที่พบบ่อยเกี่ยวกับการใช้งาน การสั่งซื้อ และการชำระเงิน' }}
                </p>
            </div>

            {{-- ★ เพิ่มส่วนช่องค้นหา (Search Box) --}}
            <div class="max-w-2xl mx-auto mb-10">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-focus-within:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" id="faqSearchInput" placeholder="ค้นหาคำถามที่คุณสงสัย..."
                        class="block w-full pl-12 pr-4 py-3.5 bg-slate-50/50 border border-slate-200 rounded-2xl shadow-sm focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-400 transition-all text-slate-700 outline-none placeholder-slate-400 text-base">
                </div>
            </div>

            {{-- ปุ่มหมวดหมู่ --}}
            <div class="flex flex-wrap justify-center gap-3 mb-10">
                <button
                    class="px-6 py-2 rounded-full bg-red-600 text-white font-medium shadow-md shadow-red-600/20 hover:bg-red-700 transition-all">ทั้งหมด</button>
                <button
                    class="px-6 py-2 rounded-full bg-white text-slate-600 font-medium hover:bg-slate-50 border border-slate-200 transition-all">การสั่งซื้อ</button>
                <button
                    class="px-6 py-2 rounded-full bg-white text-slate-600 font-medium hover:bg-slate-50 border border-slate-200 transition-all">การชำระเงิน</button>
            </div>

            {{-- รายการคำถาม --}}
            <div class="space-y-4" id="faqContainer">
                @forelse ($faqs as $index => $faq)
                    {{-- ★ เพิ่ม class faq-item และ data-search สำหรับระบบค้นหา --}}
                    <div class="faq-item collapse collapse-plus bg-white border border-slate-200/80 shadow-sm hover:shadow-md hover:border-red-200 transition-all duration-300 rounded-2xl group"
                         data-search="{{ strtolower($faq->question . ' ' . strip_tags($faq->answer)) }}">

                        <input type="radio" name="faq-accordion" class="peer"
                            @if ($loop->first) checked="checked" @endif />

                        <div
                            class="collapse-title text-base md:text-lg font-semibold text-slate-800 peer-checked:text-red-600 flex items-center gap-4 py-5 pl-6 pr-12">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-red-50 group-hover:text-red-500 peer-checked:bg-red-100 peer-checked:text-red-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <span>{{ $faq->question }}</span>
                        </div>

                        <div class="collapse-content text-slate-600">
                            <div class="pt-0 pb-5 px-6 md:pl-[4.5rem] md:pr-10">
                                <div class="w-full h-px bg-slate-100 mb-4"></div>
                                <div class="leading-relaxed text-sm md:text-base prose prose-red max-w-none">
                                    {!! nl2br(e($faq->answer)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Empty State --}}
                    <div
                        class="text-center py-16 px-4 bg-white/60 backdrop-blur-sm rounded-3xl border-2 border-dashed border-slate-300">
                        <div class="w-20 h-20 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-search text-3xl text-slate-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-700">ยังไม่มีข้อมูลคำถาม</h3>
                        <p class="text-slate-500">ระบบกำลังอัปเดตข้อมูล</p>
                    </div>
                @endforelse

                {{-- ★ ข้อความแจ้งเตือนเมื่อค้นหาไม่พบ (ซ่อนไว้เป็นค่าเริ่มต้น) --}}
                <div id="noResultsMessage" class="hidden text-center py-12 px-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <div class="w-16 h-16 mx-auto bg-red-50 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700">ไม่พบคำถามที่ค้นหา</h3>
                    <p class="text-sm text-slate-500">ลองใช้คำค้นหาอื่นดูอีกครั้งนะครับ</p>
                </div>
            </div>

            {{-- Footer Card --}}
            {{-- <div
                class="mt-12 relative overflow-hidden bg-gradient-to-br from-red-600 to-red-800 rounded-2xl p-8 md:p-12 shadow-lg text-center">
                ... (ถูกคอมเมนต์ไว้ตามโค้ดเดิมของคุณ)
            </div> --}}

        </div>
    </div>

    {{-- ★ Script สำหรับระบบค้นหา (Live Search) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('faqSearchInput');
            const faqItems = document.querySelectorAll('.faq-item');
            const noResultsMessage = document.getElementById('noResultsMessage');

            // เมื่อมีการพิมพ์ในช่องค้นหา
            searchInput.addEventListener('input', function(e) {
                // แปลงคำค้นหาเป็นตัวพิมพ์เล็กทั้งหมดเพื่อไม่ให้สนใจตัวพิมพ์ใหญ่-เล็ก
                const searchTerm = e.target.value.toLowerCase().trim();
                let hasVisibleItems = false;

                // วนลูปตรวจสอบคำถามแต่ละข้อ
                faqItems.forEach(function(item) {
                    const searchableText = item.getAttribute('data-search');
                    
                    // ถ้าข้อความที่ซ่อนไว้ตรงกับคำค้นหา
                    if (searchableText.includes(searchTerm)) {
                        item.style.display = ''; // แสดงผล
                        hasVisibleItems = true;
                    } else {
                        item.style.display = 'none'; // ซ่อน
                    }
                });

                // ตรวจสอบว่ามีข้อมูลให้แสดงหรือไม่ เพื่อโชว์ข้อความ "ไม่พบข้อมูล"
                if (!hasVisibleItems && faqItems.length > 0) {
                    noResultsMessage.classList.remove('hidden');
                } else {
                    noResultsMessage.classList.add('hidden');
                }
            });
        });
    </script>
@endsection