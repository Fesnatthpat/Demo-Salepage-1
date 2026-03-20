@extends('layouts.admin')

@section('title', 'สร้าง Popup ใหม่')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-white tracking-tight flex items-center gap-3">
            <a href="{{ route('admin.popups.index') }}" class="text-gray-500 hover:text-white transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            สร้าง Popup ใหม่
        </h1>
    </div>

    @if ($errors->any())
        <div class="px-6 py-4 bg-red-500/20 border border-red-500 text-red-300 rounded-2xl flex flex-col gap-1 mb-6 shadow-lg shadow-red-500/10">
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

    <form action="{{ route('admin.popups.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-2xl">
            <div class="p-8 space-y-8">
                {{-- Name & Basic Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-400 uppercase tracking-widest">ชื่อเรียก Popup (สำหรับ Admin)</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all"
                            placeholder="เช่น โปรโมชั่นสงกรานต์ 2026">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-400 uppercase tracking-widest">ลำดับการแสดงผล</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" required
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all"
                            placeholder="0">
                        <p class="text-[10px] text-gray-500 italic">* ตัวเลขน้อยจะแสดงก่อน</p>
                    </div>
                </div>

                {{-- Display Pages & Type --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-gray-700/50">
                    <div class="space-y-4">
                        <label class="block text-sm font-bold text-gray-400 uppercase tracking-widest">หน้าที่ต้องการให้แสดง</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-3 p-3 bg-gray-900 border border-gray-700 rounded-xl cursor-pointer hover:border-indigo-500/50 transition-colors">
                                <input type="checkbox" name="display_pages[]" value="home" checked class="w-5 h-5 rounded border-gray-700 bg-gray-800 text-indigo-600 focus:ring-indigo-500/50">
                                <span class="text-sm text-gray-300 font-medium">หน้าแรก</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-gray-900 border border-gray-700 rounded-xl cursor-pointer hover:border-indigo-500/50 transition-colors">
                                <input type="checkbox" name="display_pages[]" value="product.show" class="w-5 h-5 rounded border-gray-700 bg-gray-800 text-indigo-600 focus:ring-indigo-500/50">
                                <span class="text-sm text-gray-300 font-medium">หน้าสินค้า</span>
                            </label>
                        </div>
                        <p class="text-[10px] text-gray-500 italic text-left">* หากไม่เลือกเลยจะแสดงทุกหน้า</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-400 uppercase tracking-widest">ประเภทการแสดงผล</label>
                        <select name="display_type" required
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 focus:ring-2 focus:ring-indigo-500/50 outline-none">
                            <option value="once_per_session" {{ old('display_type') == 'once_per_session' ? 'selected' : '' }}>แสดงครั้งเดียวต่อเซสชัน (แนะนำ)</option>
                            <option value="always" {{ old('display_type') == 'always' ? 'selected' : '' }}>แสดงทุกครั้งที่โหลดหน้าแรก</option>
                            <option value="once_per_day" {{ old('display_type') == 'once_per_day' ? 'selected' : '' }}>แสดงวันละครั้ง</option>
                        </select>
                    </div>
                </div>

                {{-- Image Upload & Preview --}}
                <div class="space-y-4">
                    <label class="block text-sm font-bold text-gray-400 uppercase tracking-widest">รูปภาพโฆษณา</label>
                    <div class="relative group">
                        <div class="w-full max-w-md mx-auto aspect-square md:aspect-video bg-gray-900 rounded-2xl border-2 border-dashed border-gray-700 overflow-hidden relative transition-all group-hover:border-indigo-500/50">
                            <img src="https://placehold.co/1200x800/111827/4b5563?text=Click+to+Upload"
                                class="w-full h-full object-contain" id="popup-image-preview">
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center cursor-pointer backdrop-blur-sm">
                                <i class="fas fa-cloud-upload-alt text-white text-3xl mb-2"></i>
                                <span class="text-white text-sm font-medium">คลิกเพื่อเลือกรูปภาพ</span>
                            </div>
                            <input type="file" name="image" accept="image/*" required
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                onchange="const file = this.files[0]; if(file) { document.getElementById('popup-image-preview').src = window.URL.createObjectURL(file); }">
                        </div>
                        <p class="mt-2 text-[10px] text-gray-500 italic text-center">* แนะนำไฟล์รูปภาพขนาดไม่เกิน 2MB (รองรับ JPG, PNG, WEBP)</p>
                    </div>
                </div>

                {{-- Link Selection Logic --}}
                <div class="space-y-6 pt-4 border-t border-gray-700/50" x-data="{ linkType: '{{ old('product_id') ? 'product' : (old('link_url') ? 'url' : 'none') }}' }">
                    <label class="block text-sm font-bold text-gray-400 uppercase tracking-widest">การเชื่อมโยงลิงก์ (Link Action)</label>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <button type="button" @click="linkType = 'none'" 
                            class="py-3 px-4 rounded-xl border-2 transition-all font-bold text-sm"
                            :class="linkType === 'none' ? 'border-indigo-500 bg-indigo-500/10 text-indigo-400' : 'border-gray-700 text-gray-500 hover:border-gray-600'">
                            <i class="fas fa-unlink mr-2"></i> ไม่ระบุลิงก์
                        </button>
                        <button type="button" @click="linkType = 'product'" 
                            class="py-3 px-4 rounded-xl border-2 transition-all font-bold text-sm"
                            :class="linkType === 'product' ? 'border-indigo-500 bg-indigo-500/10 text-indigo-400' : 'border-gray-700 text-gray-500 hover:border-gray-600'">
                            <i class="fas fa-box mr-2"></i> เลือกสินค้า
                        </button>
                        <button type="button" @click="linkType = 'url'" 
                            class="py-3 px-4 rounded-xl border-2 transition-all font-bold text-sm"
                            :class="linkType === 'url' ? 'border-indigo-500 bg-indigo-500/10 text-indigo-400' : 'border-gray-700 text-gray-500 hover:border-gray-600'">
                            <i class="fas fa-link mr-2"></i> ระบุ URL เอง
                        </button>
                    </div>

                    {{-- Product Select --}}
                    <div x-show="linkType === 'product'" x-transition class="space-y-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest">เลือกสินค้าที่ต้องการเชื่อมโยง</label>
                        <select name="product_id" id="product_id" 
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 focus:ring-2 focus:ring-indigo-500/50 outline-none">
                            <option value="">-- กรุณาเลือกสินค้า --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->pd_sp_id }}" {{ old('product_id') == $product->pd_sp_id ? 'selected' : '' }}>
                                    {{ $product->pd_sp_name }} (฿{{ number_format($product->pd_sp_price) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Custom URL --}}
                    <div x-show="linkType === 'url'" x-transition class="space-y-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest">ระบุ URL ปลายทาง</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-link text-gray-600"></i>
                            </div>
                            <input type="url" name="link_url" value="{{ old('link_url') }}"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl pl-10 pr-4 py-3 text-gray-100 focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all"
                                placeholder="https://your-store.com/special-offer">
                        </div>
                    </div>
                </div>

                {{-- Schedule --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-gray-700/50">
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">เริ่มแสดงผลตั้งแต่วันที่/เวลา</label>
                        <input type="datetime-local" name="start_date" value="{{ old('start_date') }}"
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 focus:ring-2 focus:ring-indigo-500/50 outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">สิ้นสุดวันที่/เวลา</label>
                        <input type="datetime-local" name="end_date" value="{{ old('end_date') }}"
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 focus:ring-2 focus:ring-indigo-500/50 outline-none">
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-4">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-5 h-5 rounded border-gray-700 bg-gray-900 text-indigo-600 focus:ring-indigo-500/50">
                    <label for="is_active" class="text-sm font-bold text-gray-300 uppercase tracking-widest cursor-pointer">เปิดใช้งานทันที</label>
                </div>
            </div>

            <div class="px-8 py-6 bg-gray-900/50 border-t border-gray-700 flex justify-end gap-4">
                <a href="{{ route('admin.popups.index') }}" class="px-8 py-3 bg-gray-700 hover:bg-gray-600 text-white font-bold rounded-xl transition-all">ยกเลิก</a>
                <button type="submit" class="px-10 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg transition-all transform hover:scale-105 active:scale-95">
                    <i class="fas fa-save mr-2"></i> สร้าง Popup
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
