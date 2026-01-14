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
                <input type="checkbox" name="pd_sp_active" value="1" 
                    class="toggle toggle-success toggle-sm" 
                    {{ old('pd_sp_active', $productSalepage->pd_sp_active ?? 0) == 1 ? 'checked' : '' }} />
                <span class="text-xs text-gray-400">(เปิด/ปิด)</span>
            </div>

            {{-- สินค้าแนะนำ --}}
            <div class="flex items-center gap-3 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                <span class="text-sm font-medium text-gray-600">สินค้าแนะนำ:</span>
                <div class="flex items-center gap-4">
                    <label class="label cursor-pointer gap-1 p-0">
                        <input type="radio" name="is_recommended" value="1" class="radio radio-primary radio-xs" {{ old('is_recommended', $productSalepage->is_recommended ?? 0) == 1 ? 'checked' : '' }} />
                        <span class="label-text text-xs">ใช่</span> 
                    </label>
                    <label class="label cursor-pointer gap-1 p-0">
                        <input type="radio" name="is_recommended" value="0" class="radio radio-primary radio-xs" {{ old('is_recommended', $productSalepage->is_recommended ?? 0) == 0 ? 'checked' : '' }} />
                        <span class="label-text text-xs">ไม่ใช่</span> 
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body p-6">
        {{-- ถ้ามีรหัสสินค้าแล้ว (หน้าแก้ไข) ให้แสดงโชว์เฉยๆ --}}
        @if(isset($productSalepage->pd_code))
            <div class="mb-6 flex items-center gap-2 text-sm text-blue-600 bg-blue-50 p-3 rounded-lg border border-blue-100">
                <i class="fas fa-tag"></i>
                <span>รหัสสินค้า: <strong>{{ $productSalepage->pd_code }}</strong> (สร้างอัตโนมัติ)</span>
            </div>
        @endif

        {{-- ชื่อสินค้า --}}
        <div class="form-control w-full mb-6">
            <label class="label font-bold text-gray-700">ชื่อสินค้า <span class="text-error">*</span></label>
            <input type="text" name="pd_sp_name" 
                class="input input-bordered w-full text-lg h-12 focus:border-primary focus:ring-2 focus:ring-primary/20"
                placeholder="ระบุชื่อสินค้า (เช่น เสื้อยืด Cotton 100%)"
                value="{{ old('pd_sp_name', $productSalepage->pd_sp_name ?? '') }}" />
            @error('pd_sp_name') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Grid: ราคา และ การแสดงผล --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
            
            {{-- ราคาขาย (กินพื้นที่ 4 ส่วน) --}}
            <div class="md:col-span-4 form-control">
                <label class="label font-bold text-gray-700">ราคาขาย (บาท) <span class="text-error">*</span></label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-gray-400 font-bold">฿</span>
                    <input type="number" step="0.01" name="pd_sp_price" 
                        class="input input-bordered w-full pl-10 font-mono text-xl font-bold text-gray-800"
                        placeholder="0.00"
                        value="{{ old('pd_sp_price', $productSalepage->pd_sp_price ?? '') }}" />
                </div>
                @error('pd_sp_price') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- ส่วนลด (กินพื้นที่ 4 ส่วน) --}}
            <div class="md:col-span-4 form-control">
                <label class="label font-bold text-gray-700">ส่วนลด (บาท)</label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-gray-400 font-bold">฿</span>
                    <input type="number" step="0.01" name="pd_sp_discount" 
                        class="input input-bordered w-full pl-10 font-mono text-xl text-red-500"
                        placeholder="0.00"
                        value="{{ old('pd_sp_discount', $productSalepage->pd_sp_discount ?? '') }}" />
                </div>
                <label class="label py-0 mt-1"><span class="label-text-alt text-gray-400">ใส่ 0 หากไม่มี</span></label>
            </div>

            {{-- ตำแหน่งแสดงผล (กินพื้นที่ 4 ส่วน) --}}
            <div class="md:col-span-4 form-control">
                <label class="label font-bold text-gray-700">ตำแหน่งแสดงผล</label>
                <select name="pd_sp_display_location" class="select select-bordered w-full text-base">
                    <option value="general" {{ (old('pd_sp_display_location', $productSalepage->pd_sp_display_location ?? '') == 'general') ? 'selected' : '' }}>
                        📦 สินค้าทั่วไป
                    </option>
                    <option value="homepage" {{ (old('pd_sp_display_location', $productSalepage->pd_sp_display_location ?? '') == 'homepage') ? 'selected' : '' }}>
                        ⭐ สินค้าแนะนำ (หน้าแรก)
                    </option>
                </select>
            </div>
        </div>

        {{-- รายละเอียดสินค้า --}}
        <div class="form-control w-full">
            <label class="label font-bold text-gray-700">รายละเอียดสินค้า</label>
            <textarea name="pd_sp_details" rows="5"
                class="textarea textarea-bordered h-32 text-base leading-relaxed"
                placeholder="อธิบายรายละเอียด คุณสมบัติ ขนาด หรือวิธีใช้..."
            >{{ old('pd_sp_details', $productSalepage->pd_sp_details ?? '') }}</textarea>
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
                @foreach($products as $product)
                    <option value="{{ $product->pd_sp_id }}" 
                        {{ in_array($product->pd_sp_id, old('options', $productSalepage->exists ? $productSalepage->options->pluck('pd_sp_id')->toArray() : [])) ? 'selected' : '' }}>
                        {{ $product->pd_sp_name }} ({{ $product->pd_code }})
                    </option>
                @endforeach
            </select>
            <label class="label">
                <span class="label-text-alt">ใช้สำหรับจัดกลุ่มสินค้าที่มีลักษณะเดียวกันแต่มีรายละเอียดต่างกัน เช่น เสื้อคนละสี</span>
            </label>
        </div>
    </div>
</div>

{{-- ส่วนที่ 1.7: โปรโมชั่น --}}
<div class="card bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden mt-6"
     x-data="{ isBogoEnabled: {{ old('is_bogo_active', $productSalepage->is_bogo_active ?? 0) == 1 ? 'true' : 'false' }} }">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-gift text-primary"></i> โปรโมชั่น
        </h3>
    </div>
    <div class="card-body p-6">
        {{-- BOGO Toggle --}}
        <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 shadow-sm mb-6 bg-gray-50">
            <span class="text-sm font-medium text-gray-700">โปรโมชั่น "ซื้อ 1 แถม 1":</span>
            <input type="hidden" name="is_bogo_active" value="0">
            <input type="checkbox" name="is_bogo_active" value="1" 
                class="toggle toggle-primary toggle-sm"
                x-model="isBogoEnabled"
                />
            <span class="text-xs text-gray-500">(เปิด/ปิด โปรโมชั่น 1 แถม 1)</span>
        </div>

        {{-- BOGO Options Selector --}}
        <div class="form-control w-full" x-show="isBogoEnabled">
            <div class="p-4 rounded-lg bg-blue-50 border border-blue-200 text-blue-800 text-sm mb-4">
                <h4 class="font-bold mb-1">วิธีเพิ่มของแถม:</h4>
                <ol class="list-decimal list-inside text-xs">
                    <li>ขั้นแรก, ไปที่หน้า "จัดการสินค้า" และ "เพิ่มสินค้าใหม่" เพื่อสร้างสินค้าที่จะใช้เป็นของแถมก่อน</li>
                    <li>ตรวจสอบให้แน่ใจว่าสินค้าของแถมนั้นมีชื่อและรูปภาพเรียบร้อย</li>
                    <li>กลับมาที่หน้านี้, เปิดโปรโมชั่น 1 แถม 1, และเลือกสินค้าของแถมจากรายการด้านล่างนี้</li>
                </ol>
            </div>
            <label class="label font-bold text-gray-700">เลือกสินค้าที่จะให้เป็นของแถม</label>
            <select name="bogo_options[]" id="bogo-options" multiple>
                @foreach($products as $productOption)
                    <option value="{{ $productOption->pd_sp_id }}" 
                        {{ in_array($productOption->pd_sp_id, old('bogo_options', ($productSalepage->bogoFreeOptions ?? collect())->pluck('pd_sp_id')->toArray())) ? 'selected' : '' }}>
                        {{ $productOption->pd_sp_name }} ({{ $productOption->pd_code }})
                    </option>
                @endforeach
            </select>
            <label class="label">
                <span class="label-text-alt">คุณสามารถเลือกสินค้าได้หลายรายการเพื่อให้ลูกค้าเลือกเป็นของแถม</span>
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
        {{-- Upload Zone --}}
        <div class="form-control w-full mb-8">
            <div class="relative group">
                <div id="upload-zone" class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-2xl bg-gray-50 hover:bg-blue-50 hover:border-blue-400 transition-all cursor-pointer">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <div class="bg-white p-3 rounded-full shadow-sm mb-3">
                            <i class="fas fa-cloud-upload-alt text-2xl text-primary"></i>
                        </div>
                        <p class="mb-1 text-base text-gray-600"><span class="font-bold text-primary">คลิกเพื่อเลือกรูป</span> หรือลากไฟล์มาวาง</p>
                        <p class="text-xs text-gray-400">รองรับ JPG, PNG, WEBP (สูงสุด 64MB/รูป)</p>
                    </div>
                    <input type="file" name="images[]" id="images" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                </div>
            </div>
        </div>

        {{-- Preview Area --}}
        <div id="new-image-preview" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-4"></div>

        {{-- Existing Images --}}
        @if (isset($productSalepage) && $productSalepage->images->count() > 0)
            <div class="divider text-gray-400 text-sm">รูปภาพปัจจุบัน</div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach ($productSalepage->images as $image)
                <div class="relative group rounded-lg overflow-hidden border border-gray-200 shadow-sm aspect-square bg-gray-100" id="image-card-{{ $image->img_pd_id }}">
                    <img src="{{ asset('storage/' . $image->image_path) }}" class="w-full h-full object-cover">
                    
                    @if($image->is_primary)
                        <div class="absolute top-2 right-2 badge badge-primary shadow-md z-10">ปก</div>
                    @endif

                    {{-- Hover Actions --}}
                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-center items-center gap-2 p-2">
                        <label class="btn btn-xs btn-white w-full gap-2">
                            <input type="radio" name="is_primary" value="{{ $image->img_pd_id }}" {{ $image->is_primary ? 'checked' : '' }} class="radio radio-primary radio-xs">
                            ตั้งเป็นปก
                        </label>
                        <button type="button" class="btn btn-xs btn-error w-full text-white delete-image" data-image-id="{{ $image->img_pd_id }}">
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
    document.addEventListener('DOMContentLoaded', function() {
        const uploadInput = document.getElementById('images');
        const previewContainer = document.getElementById('new-image-preview');
        const uploadZone = document.getElementById('upload-zone');
        const form = document.querySelector('form');

        // Drag & Drop Visuals
        ['dragenter', 'dragover'].forEach(eName => {
            uploadZone.addEventListener(eName, (e) => { e.preventDefault(); uploadZone.classList.add('border-primary', 'bg-blue-50'); });
        });
        ['dragleave', 'drop'].forEach(eName => {
            uploadZone.addEventListener(eName, (e) => { e.preventDefault(); uploadZone.classList.remove('border-primary', 'bg-blue-50'); });
        });

        // 1. Image Preview & Validation
        uploadInput.addEventListener('change', function() {
            previewContainer.innerHTML = '';
            const files = Array.from(this.files);
            const MAX_SIZE = 64 * 1024 * 1024; // 64MB
            let isTooLarge = false;

            files.forEach(file => {
                if(file.size > MAX_SIZE) {
                    isTooLarge = true;
                    alert(`ไฟล์ "${file.name}" ใหญ่เกินไป! (ต้องไม่เกิน 64MB)`);
                }
            });

            if(isTooLarge) { this.value = ''; return; }

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

        // 2. Prevent Submit if File Too Large
        form.addEventListener('submit', function(e) {
            if(uploadInput.files.length > 0) {
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

        // 3. Delete Image Logic
        document.querySelectorAll('.delete-image').forEach(btn => {
            btn.addEventListener('click', function() {
                if(confirm('ยืนยันที่จะลบรูปภาพนี้?')) {
                    const id = this.dataset.imageId;
                    const card = document.getElementById(`image-card-${id}`);
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; 
                    this.disabled = true;
                    
                    fetch(`/admin/products/image/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
                    }).then(r => r.json()).then(data => {
                        if(data.success) { 
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

        // 4. Initialize Tom Select
        if (document.getElementById('product-options')) {
            new TomSelect('#product-options',{
                plugins: ['remove_button'],
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });
        }
        if (document.getElementById('bogo-options')) {
            new TomSelect('#bogo-options',{
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