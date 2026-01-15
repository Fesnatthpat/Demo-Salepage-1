{{-- resources/views/admin/products/_form.blade.php --}}

{{-- Load Alpine.js --}}
<script src="//unpkg.com/alpinejs" defer></script>

{{-- Display All Validation Errors --}}
@if ($errors->any())
    <div class="alert alert-error shadow-lg mb-6">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <div>
                <h3 class="font-bold">พบข้อผิดพลาด!</h3>
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

{{-- ส่วนที่ 1: ข้อมูลหลัก --}}
<div class="card bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex flex-wrap justify-between items-center gap-4">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-info-circle text-primary"></i> ข้อมูลทั่วไป
        </h3>

        <div class="flex items-center gap-4">
            {{-- สถานะ เปิด/ปิด สินค้า --}}
            <div class="flex items-center gap-3 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                <span class="text-sm font-medium text-gray-600">สถานะการขาย:</span>
                <input type="hidden" name="pd_sp_active" value="0">
                <input type="checkbox" name="pd_sp_active" value="1" class="toggle toggle-success toggle-sm"
                    {{ old('pd_sp_active', $productSalepage->pd_sp_active ?? 0) == 1 ? 'checked' : '' }} />
                <span class="text-xs text-gray-400">(เปิด/ปิด)</span>
            </div>

            {{-- สินค้าแนะนำ --}}
            <div class="flex items-center gap-3 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                <span class="text-sm font-medium text-gray-600">สินค้าแนะนำ:</span>
                <div class="flex items-center gap-4">
                    <label class="label cursor-pointer gap-1 p-0">
                        <input type="radio" name="is_recommended" value="1" class="radio radio-primary radio-xs"
                            {{ old('is_recommended', $productSalepage->is_recommended ?? 0) == 1 ? 'checked' : '' }} />
                        <span class="label-text text-xs">ใช่</span>
                    </label>
                    <label class="label cursor-pointer gap-1 p-0">
                        <input type="radio" name="is_recommended" value="0" class="radio radio-primary radio-xs"
                            {{ old('is_recommended', $productSalepage->is_recommended ?? 0) == 0 ? 'checked' : '' }} />
                        <span class="label-text text-xs">ไม่ใช่</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body p-6">
        {{-- รหัสสินค้า --}}
        @if (isset($productSalepage->pd_sp_code) || isset($productSalepage->pd_code))
            <div
                class="mb-6 flex items-center gap-2 text-sm text-blue-600 bg-blue-50 p-3 rounded-lg border border-blue-100">
                <i class="fas fa-tag"></i>
                <span>รหัสสินค้า: <strong>{{ $productSalepage->pd_sp_code ?? $productSalepage->pd_code }}</strong> (สร้างอัตโนมัติ)</span>
            </div>
        @endif

        {{-- ชื่อสินค้า --}}
        <div class="form-control w-full mb-6">
            <label class="label font-bold text-gray-700">ชื่อสินค้า <span class="text-error">*</span></label>
            <input type="text" name="pd_sp_name"
                class="input input-bordered w-full text-lg h-12 focus:border-primary focus:ring-2 focus:ring-primary/20"
                placeholder="ระบุชื่อสินค้า (เช่น เสื้อยืด Cotton 100%)"
                value="{{ old('pd_sp_name', $productSalepage->pd_sp_name ?? '') }}" />
            @error('pd_sp_name')
                <span class="text-error text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        {{-- Grid: ราคา และ การแสดงผล --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
            {{-- ราคาขาย --}}
            <div class="md:col-span-4 form-control">
                <label class="label font-bold text-gray-700">ราคาขาย (บาท) <span class="text-error">*</span></label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-gray-400 font-bold">฿</span>
                    <input type="number" step="0.01" name="pd_sp_price"
                        class="input input-bordered w-full pl-10 font-mono text-xl font-bold text-gray-800"
                        placeholder="0.00" value="{{ old('pd_sp_price', $productSalepage->pd_sp_price ?? '') }}" />
                </div>
                @error('pd_sp_price')
                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            {{-- ส่วนลด --}}
            <div class="md:col-span-4 form-control">
                <label class="label font-bold text-gray-700">ส่วนลด (บาท)</label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-gray-400 font-bold">฿</span>
                    <input type="number" step="0.01" name="pd_sp_discount"
                        class="input input-bordered w-full pl-10 font-mono text-xl text-red-500" placeholder="0.00"
                        value="{{ old('pd_sp_discount', $productSalepage->pd_sp_discount ?? '') }}" />
                </div>
                <label class="label py-0 mt-1"><span class="label-text-alt text-gray-400">ใส่ 0 หากไม่มี</span></label>
            </div>

            {{-- จำนวนสินค้าในคลัง --}}
            <div class="md:col-span-4 form-control">
                <label class="label font-bold text-gray-700">จำนวนสินค้าในคลัง <span class="text-error">*</span></label>
                <input type="number" name="pd_sp_stock"
                    class="input input-bordered w-full text-lg h-12 focus:border-primary focus:ring-2 focus:ring-primary/20"
                    placeholder="0"
                    value="{{ old('pd_sp_stock', $productSalepage->pd_sp_stock ?? '') }}" />
                @error('pd_sp_stock')
                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            {{-- ตำแหน่งแสดงผล --}}
            <div class="md:col-span-4 form-control">
                <label class="label font-bold text-gray-700">ตำแหน่งแสดงผล</label>
                <select name="pd_sp_display_location" class="select select-bordered w-full text-base">
                    <option value="general"
                        {{ old('pd_sp_display_location', $productSalepage->pd_sp_display_location ?? '') == 'general' ? 'selected' : '' }}>
                        📦 สินค้าทั่วไป
                    </option>
                    <option value="homepage"
                        {{ old('pd_sp_display_location', $productSalepage->pd_sp_display_location ?? '') == 'homepage' ? 'selected' : '' }}>
                        ⭐ สินค้าแนะนำ (หน้าแรก)
                    </option>
                </select>
            </div>
        </div>

        {{-- รายละเอียดสินค้า --}}
        <div class="form-control w-full">
            <label class="label font-bold text-gray-700">รายละเอียดสินค้า</label>
            <textarea name="pd_sp_details" rows="5" class="textarea textarea-bordered h-32 text-base leading-relaxed"
                placeholder="อธิบายรายละเอียด คุณสมบัติ ขนาด หรือวิธีใช้...">{{ old('pd_sp_details', $productSalepage->pd_sp_description ?? ($productSalepage->pd_sp_details ?? '')) }}</textarea>
        </div>
    </div>
</div>

{{-- ส่วนที่ 1.5: ตัวเลือกสินค้า --}}
<div class="card bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden mt-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-cogs text-primary"></i> ตัวเลือกสินค้า
        </h3>
    </div>
    <div class="card-body p-6">
        <div class="form-control w-full">
            <label class="label font-bold text-gray-700">เลือกสินค้าที่เป็นตัวเลือก (เช่น สี, ขนาด)</label>
            <select name="options[]" id="product-options" multiple>
                @foreach ($products as $product)
                    @if(!isset($productSalepage) || $product->pd_sp_id !== $productSalepage->pd_sp_id)
                    <option value="{{ $product->pd_sp_id }}"
                        {{ in_array($product->pd_sp_id, old('options', isset($productSalepage) && $productSalepage->exists ? $productSalepage->options->pluck('pd_sp_id')->toArray() : [])) ? 'selected' : '' }}>
                        {{ $product->pd_sp_name }} ({{ $product->pd_sp_code ?? $product->pd_code }})
                    </option>
                    @endif
                @endforeach
            </select>
            <label class="label">
                <span class="label-text-alt">ใช้สำหรับจัดกลุ่มสินค้าที่มีลักษณะเดียวกันแต่มีรายละเอียดต่างกัน เช่น เสื้อคนละสี</span>
            </label>
        </div>
    </div>
</div>

{{-- ส่วนที่ 1.7: โปรโมชั่น (BOGO - Buy One Get One) --}}
@php
    $rawBogoValue = old('is_bogo_active', $productSalepage->is_bogo_active ?? 0);
    $isBogoOn = ($rawBogoValue == 1 || $rawBogoValue === 'on' || $rawBogoValue === true) ? 'true' : 'false';

    // แปลง ID เป็น Int เพื่อความชัวร์
    $selectedBogoIds = collect(old('bogo_options', ($productSalepage->bogoFreeOptions ?? collect())->pluck('pd_sp_id')->toArray()))
        ->map(fn($id) => (int)$id)
        ->values()
        ->toArray();
@endphp

<div class="card bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden mt-6" 
     x-data="{
        isBogoEnabled: {{ $isBogoOn }},
        selectedBogo: {{ json_encode($selectedBogoIds) }},
        searchBogo: '',

        toggleBogo(id) {
            let numId = Number(id);
            let index = this.selectedBogo.indexOf(numId);
            if (index > -1) {
                this.selectedBogo.splice(index, 1);
            } else {
                this.selectedBogo.push(numId);
            }
        }
    }">

    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-gift text-primary"></i> โปรโมชั่น (ซื้อ 1 แถม 1)
        </h3>
    </div>
    <div class="card-body p-6">
        {{-- BOGO Toggle --}}
        <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 shadow-sm mb-6 bg-gray-50">
            <span class="text-sm font-medium text-gray-700">เปิดใช้งานโปรโมชั่น &quot;ซื้อ 1 แถม 1&quot;:</span>
            <input type="hidden" name="is_bogo_active" value="0">
            <input type="checkbox" name="is_bogo_active" value="1" class="toggle toggle-primary toggle-sm"
                x-model="isBogoEnabled" />
            <span class="text-xs text-gray-500">(เปิด/ปิด)</span>
        </div>

        {{-- BOGO Product Selection (Grid Style) --}}
        <div class="form-control w-full" x-show="isBogoEnabled" x-transition>

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
                <label class="label font-bold text-gray-700 p-0">เลือกสินค้าที่จะให้เป็นของแถม</label>
                <div class="relative w-full md:w-64">
                    <input type="text" x-model="searchBogo" placeholder="ค้นหาชื่อสินค้าหรือรหัส..."
                        class="input input-sm input-bordered w-full pr-8">
                    <i class="fas fa-search absolute right-3 top-2 text-gray-400 text-xs"></i>
                </div>
            </div>

            {{-- Grid แสดงรายการสินค้า --}}
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 max-h-[500px] overflow-y-auto p-1 border border-gray-100 rounded-lg bg-gray-50/50">
                @foreach ($products as $productOption)
                    {{-- ✅ แก้ไข: ใช้ pd_sp_code ในการค้นหา --}}
                    <div x-show='@json($productOption->pd_sp_name).toLowerCase().includes(searchBogo.toLowerCase()) || (@json($productOption->pd_sp_code) || "").toLowerCase().includes(searchBogo.toLowerCase())'
                        @click="toggleBogo({{ $productOption->pd_sp_id }})"
                        class="cursor-pointer group relative border-2 rounded-xl overflow-hidden transition-all duration-200 hover:shadow-md bg-white"
                        :class="selectedBogo.includes({{ $productOption->pd_sp_id }}) ? 'border-primary ring-2 ring-primary ring-offset-1' : 'border-gray-100 hover:border-gray-300'">

                        {{-- Checkmark Icon --}}
                        <div x-show="selectedBogo.includes({{ $productOption->pd_sp_id }})"
                             class="absolute top-2 right-2 z-10 bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm">
                            <i class="fas fa-check text-xs"></i>
                        </div>

                        {{-- รูปภาพสินค้า --}}
                        <div class="aspect-square bg-gray-100 relative">
                            @php
                                // ✅ แก้ไข Logic รูป BOGO (img_sort, img_path)
                                $optImg = 'https://via.placeholder.com/150?text=No+Image';
                                if ($productOption->images->isNotEmpty()) {
                                    $primary = $productOption->images->where('img_sort', 1)->first();
                                    $path = $primary
                                        ? $primary->img_path
                                        : $productOption->images->first()->img_path;
                                    
                                    // เช็ค URL
                                    $optImg = \Illuminate\Support\Str::startsWith($path, 'http') 
                                        ? $path 
                                        : asset('storage/' . $path);
                                }
                            @endphp
                            <img src="{{ $optImg }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            
                            <div x-show="selectedBogo.includes({{ $productOption->pd_sp_id }})" 
                                 class="absolute inset-0 bg-primary/10 transition-opacity"></div>
                        </div>

                        {{-- รายละเอียดด้านล่าง --}}
                        <div class="p-3">
                            <h4 class="text-sm font-bold text-gray-800 line-clamp-1 group-hover:text-primary transition-colors">
                                {{ $productOption->pd_sp_name }}
                            </h4>
                            <p class="text-xs text-gray-400 mt-1">{{ $productOption->pd_sp_code ?? $productOption->pd_code }}</p>
                            <div class="flex justify-between items-center mt-2">
                                <p class="text-xs font-semibold text-gray-600">
                                    ฿{{ number_format($productOption->pd_sp_price, 0) }}
                                </p>
                                <span x-text="selectedBogo.includes({{ $productOption->pd_sp_id }}) ? 'เลือกแล้ว' : 'เลือก'" 
                                      class="text-[10px] px-2 py-0.5 rounded-full"
                                      :class="selectedBogo.includes({{ $productOption->pd_sp_id }}) ? 'bg-primary text-white' : 'bg-gray-100 text-gray-500'">
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Hidden Inputs --}}
            <template x-for="id in selectedBogo" :key="id">
                <input type="hidden" name="bogo_options[]" :value="id">
            </template>

            <label class="label mt-2">
                <span class="label-text-alt text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    เลือกไปแล้ว <span x-text="selectedBogo.length" class="font-bold text-primary"></span> รายการ
                </span>
            </label>
        </div>
    </div>
</div>

{{-- ส่วนที่ 2: รูปภาพ --}}
<div class="card bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden mt-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-images text-primary"></i> รูปภาพสินค้า
        </h3>
    </div>

    <div class="card-body p-6">
        <div class="form-control w-full mb-8">
            <div class="relative group">
                <div id="upload-zone"
                    class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-2xl bg-gray-50 hover:bg-blue-50 hover:border-blue-400 transition-all cursor-pointer">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <div class="bg-white p-3 rounded-full shadow-sm mb-3">
                            <i class="fas fa-cloud-upload-alt text-2xl text-primary"></i>
                        </div>
                        <p class="mb-1 text-base text-gray-600"><span
                                class="font-bold text-primary">คลิกเพื่อเลือกรูป</span> หรือลากไฟล์มาวาง</p>
                        <p class="text-xs text-gray-400">รองรับ JPG, PNG, WEBP (สูงสุด 64MB/รูป)</p>
                    </div>
                    <input type="file" name="images[]" id="images" multiple accept="image/*"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                </div>
            </div>
        </div>

        <div id="new-image-preview" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-4"></div>

        @if (isset($productSalepage) && $productSalepage->images->count() > 0)
            <div class="divider text-gray-400 text-sm">รูปภาพปัจจุบัน</div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach ($productSalepage->images as $image)
                    {{-- ✅ แก้ไข: ใช้ img_id (PK) และ img_path --}}
                    <div class="relative group rounded-lg overflow-hidden border border-gray-200 shadow-sm aspect-square bg-gray-100"
                        id="image-card-{{ $image->img_id }}">
                        
                        <img src="{{ asset('storage/' . $image->img_path) }}" class="w-full h-full object-cover">
                        
                        {{-- ✅ แก้ไข: ใช้ img_sort == 1 แทน is_primary --}}
                        @if ($image->img_sort == 1)
                            <div class="absolute top-2 right-2 badge badge-primary shadow-md z-10">ปก</div>
                        @endif
                        
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-center items-center gap-2 p-2">
                            <label class="btn btn-xs btn-white w-full gap-2">
                                {{-- ✅ แก้ไข: value คือ img_id --}}
                                <input type="radio" name="is_primary" value="{{ $image->img_id }}"
                                    {{ $image->img_sort == 1 ? 'checked' : '' }} class="radio radio-primary radio-xs">
                                ตั้งเป็นปก
                            </label>
                            
                            {{-- ✅ แก้ไข: data-image-id คือ img_id --}}
                            <button type="button" class="btn btn-xs btn-error w-full text-white delete-image"
                                data-image-id="{{ $image->img_id }}">
                                <i class="fas fa-trash"></i> ลบ
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Scripts --}}
@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('giftManager', (initialActive, initialGifts) => ({
                active: initialActive,
                gifts: initialGifts,

                addGift() {
                    this.gifts.push({
                        id: null,
                        name: '',
                        qty: 1,
                        desc: '',
                        preview: null,
                        uid: Date.now() + Math.random().toString(36).substr(2, 9)
                    });
                },

                removeGift(index) {
                    this.gifts.splice(index, 1);
                },

                handleFileChange(event, index) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.gifts[index].preview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                }
            }));
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadInput = document.getElementById('images');
            const previewContainer = document.getElementById('new-image-preview');
            const uploadZone = document.getElementById('upload-zone');
            const form = document.querySelector('form');

            // Drag & Drop
            ['dragenter', 'dragover'].forEach(eName => {
                uploadZone.addEventListener(eName, (e) => {
                    e.preventDefault();
                    uploadZone.classList.add('border-primary', 'bg-blue-50');
                });
            });
            ['dragleave', 'drop'].forEach(eName => {
                uploadZone.addEventListener(eName, (e) => {
                    e.preventDefault();
                    uploadZone.classList.remove('border-primary', 'bg-blue-50');
                });
            });

            // Image Preview
            uploadInput.addEventListener('change', function() {
                previewContainer.innerHTML = '';
                const files = Array.from(this.files);
                const MAX_SIZE = 64 * 1024 * 1024; // 64MB
                let isTooLarge = false;

                files.forEach(file => {
                    if (file.size > MAX_SIZE) {
                        isTooLarge = true;
                        alert(`ไฟล์ "${file.name}" ใหญ่เกินไป! (ต้องไม่เกิน 64MB)`);
                    }
                });

                if (isTooLarge) {
                    this.value = '';
                    return;
                }

                files.forEach(file => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'relative rounded-lg overflow-hidden border border-gray-200 aspect-square shadow-sm';
                            div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                            previewContainer.appendChild(div);
                        }
                        reader.readAsDataURL(file);
                    }
                });
            });

            // Prevent Submit Too Large
            form.addEventListener('submit', function(e) {
                if (uploadInput.files.length > 0) {
                    const MAX_SIZE = 64 * 1024 * 1024;
                    for (let i = 0; i < uploadInput.files.length; i++) {
                        if (uploadInput.files[i].size > MAX_SIZE) {
                            e.preventDefault();
                            alert(`ไฟล์ "${uploadInput.files[i].name}" ใหญ่เกินไป!`);
                            return;
                        }
                    }
                }
            });

            // Delete Image
            document.querySelectorAll('.delete-image').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (confirm('ยืนยันที่จะลบรูปภาพนี้?')) {
                        const id = this.dataset.imageId;
                        const card = document.getElementById(`image-card-${id}`);
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                        this.disabled = true;

                        fetch(`/admin/products/image/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        }).then(r => r.json()).then(data => {
                            if (data.success) {
                                card.remove();
                            } else {
                                alert('ลบไม่สำเร็จ: ' + (data.message || 'Error'));
                                this.innerHTML = originalText;
                                this.disabled = false;
                            }
                        }).catch(e => {
                            alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                            this.innerHTML = originalText;
                            this.disabled = false;
                        });
                    }
                });
            });

            // Tom Select
            if (document.getElementById('product-options')) {
                new TomSelect('#product-options', {
                    plugins: ['remove_button'],
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });
            }
        });
    </script>
@endpush