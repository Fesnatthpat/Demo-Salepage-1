@extends('layouts.admin')

@section('title', 'สร้างโปรโมชั่นวันเกิด')

@section('content')
    <div class="container mx-auto pb-24 max-w-4xl">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <h1 class="text-3xl font-bold text-gray-100 flex items-center">
                <a href="{{ route('admin.birthday-promotion.index') }}" class="mr-4 text-gray-500 hover:text-white transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                สร้างแคมเปญวันเกิดใหม่
            </h1>
        </div>

        @if ($errors->any())
            <div class="px-6 py-4 bg-red-500/20 border border-red-500 text-red-300 rounded-2xl flex flex-col gap-1 animate-fade-in shadow-lg shadow-red-500/10 mb-6">
                <div class="flex items-center gap-2 font-bold text-sm mb-2">
                    <i class="fas fa-exclamation-triangle text-red-400"></i> ไม่สามารถบันทึกได้เนื่องจาก:
                </div>
                <ul class="text-xs list-disc list-inside opacity-90 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="pl-2">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.birthday-promotion.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-2xl">
                <div class="px-8 py-5 bg-gray-900/80 border-b border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-pink-500/10 rounded-xl"><i class="fas fa-magic text-pink-400 text-xl"></i></div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-100">รายละเอียดแคมเปญ</h3>
                            <p class="text-xs text-gray-500">กำหนดรูปภาพและข้อความที่จะแสดงบน LINE Flex Message</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                        {{-- Left: Inputs --}}
                        <div class="space-y-8">
                            {{-- Image Upload --}}
                            <div class="space-y-4">
                                <label class="block text-sm font-bold text-gray-400 uppercase tracking-widest">รูปภาพโปรโมชั่น</label>
                                <div class="relative group">
                                    <div class="w-full aspect-[20/13] bg-gray-900 rounded-2xl border-2 border-dashed border-gray-700 overflow-hidden relative transition-all group-hover:border-pink-500/50">
                                        <img src="https://placehold.co/1200x780/111827/4b5563?text=Upload+Image" class="w-full h-full object-cover" id="birthday-image-preview">
                                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center cursor-pointer backdrop-blur-sm">
                                            <i class="fas fa-cloud-upload-alt text-white text-3xl mb-2"></i>
                                            <span class="text-white text-sm font-medium">คลิกเพื่อเลือกรูปภาพ</span>
                                        </div>
                                        <input type="file" name="image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                            onchange="const file = this.files[0]; if(file) { document.getElementById('birthday-image-preview').src = window.URL.createObjectURL(file); document.getElementById('preview-img-card').src = window.URL.createObjectURL(file); }">
                                    </div>
                                    <p class="mt-2 text-[10px] text-gray-500 italic">* แนะนำขนาด 1200x780 px</p>
                                </div>
                            </div>

                            {{-- Title --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-400 uppercase tracking-widest">หัวข้อ (Title)</label>
                                <input type="text" name="title" value="{{ old('title', 'HAPPY BIRTHDAY') }}" required
                                    class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 focus:ring-2 focus:ring-pink-500/50 outline-none transition-all"
                                    oninput="document.getElementById('preview-title').innerText = this.value">
                            </div>

                            {{-- Message --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-400 uppercase tracking-widest">ข้อความอวยพร</label>
                                <textarea name="message" rows="4" required
                                    class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 focus:ring-2 focus:ring-pink-500/50 outline-none transition-all resize-none"
                                    oninput="document.getElementById('preview-message').innerText = this.value">{{ old('message', 'สุขสันต์วันเกิดครับ! เรามีของขวัญพิเศษให้คุณ...') }}</textarea>
                            </div>
                        </div>

                        {{-- Right: Preview --}}
                        <div class="flex flex-col items-center">
                            <label class="block text-sm font-bold text-gray-400 uppercase tracking-widest mb-4 self-start text-center w-full">LINE Preview</label>
                            <div class="w-full max-w-[280px] bg-[#ebebeb] rounded-[2.5rem] p-4 shadow-2xl border-[6px] border-gray-900">
                                <div class="bg-white rounded-2xl overflow-hidden shadow-md flex flex-col">
                                    <div class="aspect-[20/13] w-full bg-gray-200">
                                        <img id="preview-img-card" src="https://placehold.co/1200x780/111827/4b5563?text=Promotion+Image" class="w-full h-full object-cover">
                                    </div>
                                    <div class="p-4 space-y-1">
                                        <div id="preview-title" class="text-pink-500 font-bold text-[10px] uppercase">HAPPY BIRTHDAY</div>
                                        <div class="text-black font-bold text-base">คุณ ชื่อลูกค้า</div>
                                        <div id="preview-message" class="text-gray-600 text-xs leading-relaxed">สุขสันต์วันเกิดครับ! เรามีของขวัญพิเศษให้คุณ...</div>
                                    </div>
                                    <div class="px-4 pb-4">
                                        <div class="w-full bg-red-500 text-white py-2 rounded-lg text-center font-bold text-xs">🎁 กดรับสิทธิ์เลย</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 pt-8 border-t border-gray-700/50 grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Link --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-400 uppercase tracking-widest">ลิงก์ปุ่ม (URL)</label>
                            <input type="url" name="link_url" value="{{ old('link_url') }}"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 focus:ring-2 focus:ring-pink-500/50 outline-none"
                                placeholder="https://...">
                        </div>

                        {{-- Link to System Promotion --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-400 uppercase tracking-widest">ผูกโปรโมชั่นในระบบ</label>
                            <select name="promotion_id" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 focus:ring-2 focus:ring-pink-500/50 outline-none">
                                <option value="">-- ไม่ระบุ (ใช้เฉพาะลิงก์) --</option>
                                @foreach ($promotions as $promo)
                                    <option value="{{ $promo->id }}" {{ old('promotion_id') == $promo->id ? 'selected' : '' }}>
                                        {{ $promo->name }} ({{ $promo->discount_type == 'percentage' ? $promo->discount_value.'%' : '฿'.$promo->discount_value }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-6 bg-gray-900/50 border-t border-gray-700 flex justify-end">
                    <button type="submit" class="px-10 py-3 bg-pink-600 hover:bg-pink-500 text-white font-bold rounded-xl shadow-lg transition-all transform hover:scale-105 active:scale-95">
                        <i class="fas fa-plus mr-2"></i> สร้างแคมเปญ
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
