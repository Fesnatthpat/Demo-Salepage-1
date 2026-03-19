@extends('layouts.admin')

@section('title', 'แก้ไขโปรโมชั่นวันเกิด')

@section('content')
    <div class="container mx-auto pb-24 max-w-4xl">
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <h1 class="text-3xl font-bold text-gray-100 flex items-center">
                <a href="{{ route('admin.birthday-promotion.index') }}"
                    class="mr-4 text-gray-500 hover:text-white transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                แก้ไขแคมเปญวันเกิด
            </h1>
        </div>

        @if ($errors->any())
            <div
                class="px-6 py-4 bg-red-500/20 border border-red-500 text-red-300 rounded-2xl flex flex-col gap-1 mb-6 shadow-lg shadow-red-500/10">
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

        <form action="{{ route('admin.birthday-promotion.update', $birthdayPromotion->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-2xl">
                <div class="px-8 py-5 bg-gray-900/80 border-b border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-pink-500/10 rounded-xl"><i class="fas fa-edit text-pink-400 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-100">แก้ไขรายละเอียด</h3>
                            <p class="text-xs text-gray-500">ปรับเปลี่ยนข้อมูลแคมเปญวันเกิด</p>
                        </div>
                    </div>
                    @if ($birthdayPromotion->is_active)
                        <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 text-xs font-bold rounded-full border border-emerald-500/30">กำลังใช้งาน</span>
                    @else
                        <span class="px-3 py-1 bg-gray-700 text-gray-400 text-xs font-bold rounded-full border border-gray-600">ปิดการใช้งาน</span>
                    @endif
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                        {{-- Left: Inputs --}}
                        <div class="space-y-8">
                            {{-- Image Upload --}}
                            <div class="space-y-4">
                                <label
                                    class="block text-sm font-bold text-gray-400 uppercase tracking-widest">รูปภาพโปรโมชั่น (Flex Message)</label>
                                <div class="relative group">
                                    <div
                                        class="w-full aspect-[20/13] bg-gray-900 rounded-2xl border-2 border-dashed border-gray-700 overflow-hidden relative transition-all group-hover:border-pink-500/50">
                                        <img src="{{ $birthdayPromotion->image_path ? asset('storage/' . $birthdayPromotion->image_path) : 'https://placehold.co/1200x780/111827/4b5563?text=Upload+Image' }}"
                                            class="w-full h-full object-cover" id="birthday-image-preview">
                                        <div
                                            class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center cursor-pointer backdrop-blur-sm">
                                            <i class="fas fa-cloud-upload-alt text-white text-3xl mb-2"></i>
                                            <span class="text-white text-sm font-medium">คลิกเพื่อเปลี่ยนรูปภาพ</span>
                                        </div>
                                        <input type="file" name="image" accept="image/*"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                            onchange="const file = this.files[0]; if(file) { document.getElementById('birthday-image-preview').src = window.URL.createObjectURL(file); document.getElementById('preview-img-card').src = window.URL.createObjectURL(file); }">
                                    </div>
                                    <p class="mt-2 text-[10px] text-gray-500 italic">* แนะนำขนาด 1200x780 px</p>
                                </div>
                            </div>

                            {{-- Title --}}
                            <div class="space-y-2" x-data="{ title: '{{ old('title', $birthdayPromotion->title) }}' }">
                                <div class="flex justify-between items-center">
                                    <label class="block text-sm font-bold text-gray-400 uppercase tracking-widest">หัวข้อ
                                        (Title)</label>
                                    <span class="text-xs font-mono"
                                        :class="title.length >= 40 ? 'text-red-400' : 'text-gray-500'"
                                        x-text="title.length + '/40'"></span>
                                </div>
                                <input type="text" name="title" x-model="title" maxlength="40" required
                                    class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 focus:ring-2 focus:ring-pink-500/50 outline-none transition-all"
                                    @input="document.getElementById('preview-title').innerText = title">
                            </div>

                            {{-- Message --}}
                            <div class="space-y-2" x-data="{ message: '{{ old('message', $birthdayPromotion->message) }}' }">
                                <div class="flex justify-between items-center">
                                    <label
                                        class="block text-sm font-bold text-gray-400 uppercase tracking-widest">ข้อความอวยพร</label>
                                    <span class="text-xs font-mono"
                                        :class="message.length >= 200 ? 'text-red-400' : 'text-gray-500'"
                                        x-text="message.length + '/200'"></span>
                                </div>
                                <textarea name="message" rows="4" x-model="message" maxlength="200" required
                                    class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 focus:ring-2 focus:ring-pink-500/50 outline-none transition-all resize-none"
                                    @input="document.getElementById('preview-message').innerText = message"></textarea>
                            </div>
                        </div>

                        {{-- Right: Preview --}}
                        <div class="flex flex-col items-center">
                            <label
                                class="block text-sm font-bold text-gray-400 uppercase tracking-widest mb-4 self-start text-center w-full">LINE
                                Preview</label>
                            <div
                                class="w-full max-w-[280px] bg-[#ebebeb] rounded-[2.5rem] p-4 shadow-2xl border-[6px] border-gray-900">
                                <div class="bg-white rounded-2xl overflow-hidden shadow-md flex flex-col">
                                    <div class="aspect-[20/13] w-full bg-gray-200">
                                        <img id="preview-img-card"
                                            src="{{ $birthdayPromotion->image_path ? asset('storage/' . $birthdayPromotion->image_path) : 'https://placehold.co/1200x780/111827/4b5563?text=Promotion+Image' }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <div class="p-4 space-y-1">
                                        <div id="preview-title" class="text-pink-500 font-bold text-[10px] uppercase">
                                            {{ $birthdayPromotion->title }}</div>
                                        <div class="text-black font-bold text-base">คุณ ชื่อลูกค้า</div>
                                        <div id="preview-message" class="text-gray-600 text-xs leading-relaxed">
                                            {{ $birthdayPromotion->message }}</div>
                                    </div>
                                    <div class="px-4 pb-4">
                                        <div
                                            class="w-full bg-red-500 text-white py-2 rounded-lg text-center font-bold text-xs">
                                            🎁 กดรับสิทธิ์เลย</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- แก้ไขรูปการ์ดอวยพร --}}
                    <div class="mt-12 pt-8 border-t border-gray-700/50">
                        <div class="flex items-center gap-2 mb-6">
                            <i class="fas fa-id-card text-emerald-400 text-lg"></i>
                            <div>
                                <h3 class="font-bold text-gray-100">รูปการ์ดอวยพร (Greeting Card)</h3>
                                <p class="text-xs text-gray-500 mt-1">รูปภาพที่จะแสดงหลังลูกค้ากดรับสิทธิ์ (เช่น การ์ดจาก CEO)</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                            <div class="relative group">
                                <div
                                    class="w-full aspect-video bg-gray-900 rounded-2xl border-2 border-dashed border-gray-700 overflow-hidden relative transition-all group-hover:border-emerald-500/50">
                                    <img src="{{ $birthdayPromotion->card_image_path ? asset('storage/' . $birthdayPromotion->card_image_path) : 'https://placehold.co/800x450/111827/4b5563?text=CEO+Greeting+Card' }}"
                                        class="w-full h-full object-cover" id="card-image-preview">
                                    <div
                                        class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center cursor-pointer backdrop-blur-sm">
                                        <i class="fas fa-cloud-upload-alt text-white text-3xl mb-2"></i>
                                        <span class="text-white text-sm font-medium">คลิกเพื่อเปลี่ยนรูปการ์ด</span>
                                    </div>
                                    <input type="file" name="card_image" accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                        onchange="const file = this.files[0]; if(file) { document.getElementById('card-image-preview').src = window.URL.createObjectURL(file); }">
                                </div>
                            </div>
                            <div class="text-gray-400 text-sm italic">
                                <p><i class="fas fa-info-circle mr-2"></i> แนะนำขนาด 800x450 px หรืออัตราส่วน 16:9</p>
                                <p class="mt-2">รูปภาพนี้จะถูกส่งให้ลูกค้าหลังจากที่พวกเขากดรับสิทธิ์ผ่าน LINE เพื่อความรู้สึกประทับใจที่เป็นส่วนตัว</p>
                            </div>
                        </div>
                    </div>

                    {{-- แก้ไขการตั้งค่าโปรโมชั่นพิเศษ --}}
                    <div class="mt-12 pt-8 border-t border-gray-700/50">
                        <div class="flex items-center gap-2 mb-6">
                            <i class="fas fa-ticket-alt text-amber-400 text-lg"></i>
                            <div>
                                <h3 class="font-bold text-gray-100">การตั้งค่าสิทธิพิเศษ (Privilege Settings)</h3>
                                <p class="text-xs text-gray-500 mt-1">กำหนดโค้ดส่วนลดและของแถมที่จะได้รับ</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">รหัสโค้ดส่วนลด</label>
                                <input type="text" name="discount_code" value="{{ old('discount_code', $birthdayPromotion->discount_code) }}"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 focus:ring-2 focus:ring-amber-500/50 outline-none"
                                    placeholder="เช่น BDAY89">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">มูลค่าส่วนลด (บาท)</label>
                                <input type="number" name="discount_value" value="{{ old('discount_value', $birthdayPromotion->discount_value) }}"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 focus:ring-2 focus:ring-amber-500/50 outline-none"
                                    placeholder="0">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">เลือกสินค้าของแถม</label>
                                <select name="gift_product_id"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 focus:ring-2 focus:ring-amber-500/50 outline-none">
                                    <option value="">-- ไม่มีของแถม --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->pd_sp_id }}" {{ old('gift_product_id', $birthdayPromotion->gift_product_id) == $product->pd_sp_id ? 'selected' : '' }}>
                                            {{ $product->pd_sp_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <p class="mt-4 text-[10px] text-gray-500 italic">* การแก้ไขโค้ดหรือของแถมจะส่งผลต่อโปรโมชั่นที่ผูกไว้โดยอัตโนมัติ</p>
                    </div>

                    {{-- ตั้งเวลาแคมเปญ --}}
                    <div class="mt-12 pt-8 border-t border-gray-700/50">
                        <div class="flex items-center gap-2 mb-6">
                            <i class="fas fa-calendar-alt text-blue-400 text-lg"></i>
                            <div>
                                <h3 class="font-bold text-gray-100">ตั้งเวลาแคมเปญล่วงหน้า</h3>
                                <p class="text-xs text-gray-500 mt-1">กำหนดช่วงเวลาที่แคมเปญนี้จะมีผล</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest">เริ่มใช้งานวันที่</label>
                                <input type="date" name="start_date"
                                    value="{{ old('start_date', $birthdayPromotion->start_date ? $birthdayPromotion->start_date->format('Y-m-d') : '') }}"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 focus:ring-2 focus:ring-blue-500/50 outline-none">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">ถึงวันที่
                                    (สิ้นสุด)</label>
                                <input type="date" name="end_date"
                                    value="{{ old('end_date', $birthdayPromotion->end_date ? $birthdayPromotion->end_date->format('Y-m-d') : '') }}"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 focus:ring-2 focus:ring-blue-500/50 outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-8 border-t border-gray-700/50 grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Link --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-400 uppercase tracking-widest">ลิงก์ปุ่ม
                                (URL)</label>
                            <input type="url" name="link_url" value="{{ old('link_url', $birthdayPromotion->link_url) }}"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 focus:ring-2 focus:ring-pink-500/50 outline-none"
                                placeholder="https://...">
                        </div>

                        {{-- Link to System Promotion --}}
                        <div class="space-y-2">
                            <label
                                class="block text-sm font-bold text-gray-400 uppercase tracking-widest">ผูกโปรโมชั่นในระบบ (แมนนวล)</label>
                            <select name="promotion_id"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 focus:ring-2 focus:ring-pink-500/50 outline-none">
                                <option value="">-- ไม่ระบุ (หากกรอกด้านบนแล้วไม่ต้องเลือกที่นี่) --</option>
                                @foreach ($promotions as $promo)
                                    <option value="{{ $promo->id }}"
                                        {{ old('promotion_id', $birthdayPromotion->promotion_id) == $promo->id ? 'selected' : '' }}>
                                        {{ $promo->name }}
                                        ({{ $promo->discount_type == 'percentage' ? $promo->discount_value . '%' : '฿' . $promo->discount_value }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-6 bg-gray-900/50 border-t border-gray-700 flex justify-end gap-4">
                    <a href="{{ route('admin.birthday-promotion.index') }}"
                        class="px-8 py-3 bg-gray-700 hover:bg-gray-600 text-white font-bold rounded-xl transition-all">ยกเลิก</a>
                    <button type="submit"
                        class="px-10 py-3 bg-pink-600 hover:bg-pink-500 text-white font-bold rounded-xl shadow-lg transition-all transform hover:scale-105 active:scale-95">
                        <i class="fas fa-save mr-2"></i> บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
