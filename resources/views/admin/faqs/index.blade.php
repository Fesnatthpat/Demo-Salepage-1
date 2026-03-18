@extends('layouts.admin')

@section('title', 'จัดการคำถามที่พบบ่อย')

@section('page-title')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight flex items-center gap-2">
                <i class="fas fa-question-circle text-emerald-500"></i> จัดการคำถามที่พบบ่อย (FAQ)
            </h1>
            <p class="text-sm text-gray-400 mt-1 pl-8">บริหารจัดการรายการคำถามและคำตอบทั้งหมดในระบบหน้าบ้าน</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="openModal('settingsFaqModal')"
                class="group flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-gray-300 hover:text-white px-4 py-2.5 rounded-xl shadow-sm transition-all duration-200 font-medium border border-gray-700 hover:border-gray-500 focus:ring-2 focus:ring-gray-600 active:scale-95">
                <i class="fas fa-cog transition-transform group-hover:rotate-90"></i>
                ตั้งค่าส่วนหัวข้อ
            </button>
            <a href="{{ route('admin.faqs.create') }}"
                class="group flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-900/20 transition-all duration-200 transform hover:-translate-y-0.5 font-medium active:scale-95">
                <i class="fas fa-plus transition-transform group-hover:scale-110"></i>
                เพิ่มคำถามใหม่
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Search & Filter Section --}}
        <div class="bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-700/50 p-2 md:p-3 flex justify-end">
            <form action="{{ route('admin.faqs.index') }}" method="GET" class="w-full md:w-96 relative">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-500 group-focus-within:text-emerald-500 transition-colors">
                        <i class="fas fa-search"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="block w-full pl-10 pr-4 py-2.5 border border-gray-600/80 rounded-xl bg-gray-900/50 text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all text-sm"
                        placeholder="ค้นหาคำถาม หรือคำตอบ...">
                </div>
            </form>
        </div>

        {{-- Table Section --}}
        <div class="bg-gray-800 rounded-2xl shadow-xl border border-gray-700/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-400 whitespace-nowrap md:whitespace-normal">
                    <thead class="bg-gray-900/80 text-xs uppercase font-bold text-gray-400 border-b border-gray-700/80">
                        <tr>
                            <th class="px-6 py-5 w-16 text-center tracking-wider">#</th>
                            <th class="px-6 py-5 w-24 text-center tracking-wider">ลำดับ</th>
                            <th class="px-6 py-5 min-w-[250px] tracking-wider">คำถาม</th>
                            <th class="px-6 py-5 min-w-[300px] tracking-wider hidden md:table-cell">คำตอบ (โดยย่อ)</th>
                            <th class="px-6 py-5 text-center w-32 tracking-wider">สถานะ</th>
                            <th class="px-6 py-5 text-right w-32 tracking-wider">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50 bg-gray-800/50">
                        @forelse ($faqs as $faq)
                            <tr class="hover:bg-gray-700/40 transition-colors duration-200 group">
                                <td class="px-6 py-4 text-center text-gray-500 font-medium">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-900 border border-gray-700 text-gray-300 font-mono text-xs font-bold shadow-inner">
                                        {{ $faq->sort_order }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-200 font-semibold text-base line-clamp-2 group-hover:text-emerald-400 transition-colors" title="{{ $faq->question }}">
                                        {{ $faq->question }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <div class="text-gray-400 text-sm line-clamp-2" title="{{ $faq->answer }}">
                                        {{ Str::limit($faq->answer, 80) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($faq->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-[0_0_10px_rgba(16,185,129,0.1)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> เปิดใช้งาน
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-500/10 text-gray-400 border border-gray-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span> ปิดใช้งาน
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.faqs.edit', $faq->id) }}"
                                            class="p-2 text-gray-400 hover:text-yellow-400 hover:bg-yellow-400/10 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-yellow-400/50"
                                            title="แก้ไขข้อมูล">
                                            <i class="fas fa-edit text-base"></i>
                                        </a>
                                        <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST"
                                            onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบคำถามนี้? \nการกระทำนี้ไม่สามารถเรียกคืนได้');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-gray-400 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-red-400/50"
                                                title="ลบข้อมูล">
                                                <i class="fas fa-trash-alt text-base"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <div class="w-20 h-20 bg-gray-900/50 rounded-full flex items-center justify-center mb-4 border border-gray-700/50">
                                            <i class="fas fa-inbox text-3xl text-gray-600"></i>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-300">ยังไม่มีข้อมูลคำถามที่พบบ่อย</h3>
                                        <p class="text-sm mt-1 mb-4 text-gray-500">เริ่มต้นด้วยการสร้างคำถามแรกของคุณเพื่อให้ลูกค้าค้นหาคำตอบได้ง่ายขึ้น</p>
                                        <a href="{{ route('admin.faqs.create') }}" class="text-emerald-500 hover:text-emerald-400 font-medium hover:underline flex items-center gap-1">
                                            <i class="fas fa-plus-circle"></i> สร้างคำถามเลย
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if (method_exists($faqs, 'links') && $faqs->hasPages())
                <div class="px-6 py-4 border-t border-gray-700 bg-gray-900/30">
                    {{ $faqs->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Settings Modal พร้อม Animation --}}
    <div id="settingsFaqModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 sm:p-0 transition-opacity duration-300 opacity-0 pointer-events-none">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" onclick="closeModal('settingsFaqModal')"></div>
        
        {{-- Modal Content --}}
        <div class="relative bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-700 overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col max-h-[90vh]" id="settingsFaqModalContent">
            
            {{-- Header --}}
            <div class="bg-gray-900/80 px-6 py-5 flex justify-between items-center border-b border-gray-700/80">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fas fa-sliders-h text-emerald-500"></i> ตั้งค่าส่วนหัวข้อ (FAQ Header)
                </h3>
                <button type="button" onclick="closeModal('settingsFaqModal')" class="text-gray-500 hover:text-white bg-gray-800 hover:bg-gray-700 p-2 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            {{-- Form Body (Scrollable) --}}
            <div class="overflow-y-auto p-6 custom-scrollbar">
                <form id="faqSettingsForm" action="{{ route('admin.settings.update') }}" method="POST" class="space-y-5">
                    @csrf
                    {{-- ป้ายกำกับ --}}
                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-gray-300">ข้อความ Badge <span class="text-gray-500 font-normal">(เช่น ศูนย์ช่วยเหลือ)</span></label>
                        <input type="text" name="settings[faq_badge]" value="{{ $settings['faq_badge'] ?? 'ศูนย์ช่วยเหลือ' }}" 
                               class="w-full bg-gray-900/50 border border-gray-600 focus:border-emerald-500 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all placeholder-gray-600 shadow-inner" placeholder="พิมพ์ป้ายกำกับ...">
                    </div>
                    
                    {{-- หัวข้อหลัก --}}
                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-gray-300">หัวข้อหลัก <span class="text-gray-500 font-normal">(รองรับ HTML, Tailwind css)</span></label>
                        <textarea id="faq_title_editor" name="settings[faq_title]" rows="2"
                               class="w-full bg-gray-900/50 border border-gray-600 focus:border-emerald-500 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all placeholder-gray-600 shadow-inner resize-none font-mono text-sm leading-relaxed">{{ $settings['faq_title'] ?? 'คุณมีคำถาม <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-red-400">เรามีคำตอบ</span>' }}</textarea>
                        <p class="text-[11px] text-gray-500 mt-1 italic flex items-start gap-1">
                            <i class="fas fa-info-circle mt-0.5"></i> คุณสามารถใช้แท็ก &lt;span&gt; เพื่อใส่สีข้อความบางส่วนได้
                        </p>
                    </div>
                    
                    {{-- คำบรรยาย --}}
                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-gray-300">คำบรรยาย</label>
                        <textarea name="settings[faq_subtitle]" rows="3" 
                                  class="w-full bg-gray-900/50 border border-gray-600 focus:border-emerald-500 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all resize-none placeholder-gray-600 shadow-inner leading-relaxed">{{ $settings['faq_subtitle'] ?? 'รวมคำถามที่พบบ่อยเกี่ยวกับการใช้งาน การสั่งซื้อ และการชำระเงิน' }}</textarea>
                    </div>
                </form>
            </div>

            {{-- Footer Actions --}}
            <div class="bg-gray-900/50 px-6 py-4 flex justify-end gap-3 border-t border-gray-700/80">
                <button type="button" onclick="closeModal('settingsFaqModal')" class="px-5 py-2.5 text-gray-400 hover:text-white bg-transparent hover:bg-gray-700 rounded-xl transition-all font-medium focus:ring-2 focus:ring-gray-600 active:scale-95">
                    ยกเลิก
                </button>
                <button type="submit" form="faqSettingsForm" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl shadow-lg shadow-emerald-900/20 transition-all font-bold flex items-center gap-2 focus:ring-2 focus:ring-emerald-500/50 active:scale-95">
                    <i class="fas fa-save"></i> บันทึกการตั้งค่า
                </button>
            </div>
        </div>
    </div>

    {{-- นำเข้า CodeMirror เพื่อทำไฮไลท์สี HTML --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/dracula.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/xml/xml.min.js"></script>

    <style>
        /* ปรับแต่ง CodeMirror ให้หน้าตาเข้ากับกรอบ Input ของ Tailwind */
        .CodeMirror {
            height: auto;
            min-height: 80px;
            border-radius: 0.75rem; /* rounded-xl */
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 14px;
            background-color: rgba(17, 24, 39, 0.5) !important; /* bg-gray-900/50 */
            border: 1px solid rgba(75, 85, 99, 0.8); /* border-gray-600 */
            padding: 6px 4px;
            line-height: 1.6;
        }
        .CodeMirror-focused {
            border-color: #10b981 !important; /* emerald-500 */
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(31, 41, 55, 0.5); /* bg-gray-800 */
            border-radius: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(75, 85, 99, 0.8); /* bg-gray-600 */
            border-radius: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(107, 114, 128, 1); /* bg-gray-500 */
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    <script>
        let titleEditor; // สร้างตัวแปรเก็บสถานะ Editor

        function openModal(id) {
            const modal = document.getElementById(id);
            const modalContent = document.getElementById(id + 'Content');
            
            // เพิ่ม class flex เพื่อให้ Modal อยู่ตรงกลาง
            modal.classList.remove('hidden', 'pointer-events-none');
            modal.classList.add('flex'); 
            
            // บังคับให้ browser วาด layout ใหม่
            void modal.offsetWidth; 
            
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
            document.body.style.overflow = 'hidden';

            // เรียกใช้ CodeMirror เมื่อเปิด Modal
            if (id === 'settingsFaqModal') {
                if (!titleEditor) {
                    titleEditor = CodeMirror.fromTextArea(document.getElementById('faq_title_editor'), {
                        mode: "xml",           // โหมด HTML/XML
                        theme: "dracula",      // ธีมสีเข้ม 
                        lineWrapping: true,    // ตัดบรรทัดอัตโนมัติ
                        viewportMargin: Infinity
                    });
                    
                    // สั่งให้ส่งค่ากลับไปยัง textarea เมื่อมีการพิมพ์
                    titleEditor.on('change', function(cm) {
                        document.getElementById('faq_title_editor').value = cm.getValue();
                    });
                }
                // รีเฟรช Editor เพื่อให้ขนาดพอดีกับกล่อง
                setTimeout(() => {
                    titleEditor.refresh();
                }, 50);
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            const modalContent = document.getElementById(id + 'Content');
            
            modal.classList.add('opacity-0');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            document.body.style.overflow = '';
            
            // รอให้ animation เล่นจบแล้วซ่อน พร้อมเอา flex ออก
            setTimeout(() => {
                modal.classList.add('hidden', 'pointer-events-none');
                modal.classList.remove('flex'); 
            }, 300);
        }
        
        // จัดการการกดปุ่ม ESC เพื่อปิด Modal
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                const modal = document.getElementById('settingsFaqModal');
                if (!modal.classList.contains('hidden')) {
                    closeModal('settingsFaqModal');
                }
            }
        });
    </script>
@endsection