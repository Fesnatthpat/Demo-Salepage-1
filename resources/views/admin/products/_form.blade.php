{{-- resources/views/admin/products/_form.blade.php --}}

@if ($errors->any())
    <div class="alert bg-red-500/10 border border-red-500/20 text-red-400 shadow-lg mb-6 rounded-2xl animate-fade-in-up">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-xl mt-0.5"></i>
            <div>
                <h3 class="font-bold text-lg mb-2">โปรดตรวจสอบข้อมูลอีกครั้ง!</h3>
                <ul class="list-disc pl-5 space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

<div class="space-y-6" x-data="{
    options: {{ json_encode(
        old(
            'product_options',
            isset($productSalepage) && $productSalepage->options
                ? $productSalepage->options->map(function ($opt) {
                    return [
                        'id' => $opt->option_id,
                        'option_name' => $opt->option_name,
                        'option_SKU' => $opt->option_SKU,
                        'option_price' => $opt->option_price,
                        'option_stock' => $opt->stock ? $opt->stock->quantity : 0,
                        'options_img_id' => $opt->options_img_id,
                        'image_preview' => $opt->options_img_id ? $opt->option_image_url : null,
                    ];
                })
                : [],
        ),
    ) }},
    mainStock: {{ old('pd_sp_stock', optional($productSalepage->stock)->quantity ?? 0) }},
    addOption() {
        this.options.push({ id: Date.now(), option_name: '', option_SKU: '', option_price: '', option_stock: 0, options_img_id: null, image_preview: null });
        this.mainStock = 0;
    },
    previewOptionImage(event, index) {
        const file = event.target.files[0];
        if (file) this.options[index].image_preview = URL.createObjectURL(file);
    }
}">

    {{-- 📝 ข้อมูลทั่วไป --}}
    <div class="bg-gray-800 rounded-3xl shadow-xl border border-gray-700/50 overflow-hidden">
        <div
            class="bg-gray-900/40 px-6 py-5 border-b border-gray-700/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center"><i
                        class="fas fa-info-circle"></i></div>
                ข้อมูลทั่วไป
            </h3>

            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <label
                    class="flex items-center gap-3 bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded-xl border border-gray-600 transition-colors cursor-pointer w-full sm:w-auto justify-between">
                    <span class="text-sm font-bold text-yellow-400"><i class="fas fa-star mr-1"></i> สินค้าแนะนำ</span>
                    <input type="hidden" name="is_recommended" value="0">
                    <input type="checkbox" name="is_recommended" value="1" class="toggle toggle-warning toggle-sm"
                        {{ old('is_recommended', $productSalepage->is_recommended ?? 0) == 1 ? 'checked' : '' }} />
                </label>
                <label
                    class="flex items-center gap-3 bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded-xl border border-gray-600 transition-colors cursor-pointer w-full sm:w-auto justify-between">
                    <span class="text-sm font-bold text-emerald-400"><i class="fas fa-globe mr-1"></i> เปิดขาย</span>
                    <input type="hidden" name="pd_sp_active" value="0">
                    <input type="checkbox" name="pd_sp_active" value="1" class="toggle toggle-success toggle-sm"
                        {{ old('pd_sp_active', $productSalepage->pd_sp_active ?? 0) == 1 ? 'checked' : '' }} />
                </label>
            </div>
        </div>

        <div class="p-6 md:p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="form-control w-full">
                    <label class="label text-xs font-bold text-gray-400 uppercase tracking-wider">ชื่อสินค้า <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="pd_sp_name"
                        value="{{ old('pd_sp_name', $productSalepage->pd_sp_name ?? '') }}"
                        class="input w-full bg-gray-900 border border-gray-600 text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl h-12"
                        required />
                </div>
                <div class="form-control w-full">
                    <label class="label text-xs font-bold text-gray-400 uppercase tracking-wider">หมวดหมู่สินค้า</label>
                    <select name="category_id"
                        class="select w-full bg-gray-900 border border-gray-600 text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl h-12">
                        <option value="">-- ไม่ระบุ --</option>
                        @foreach ($categories ?? [] as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $productSalepage->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control w-full">
                    <label class="label text-xs font-bold text-gray-400 uppercase tracking-wider">รหัส SKU
                        (สินค้าหลัก)</label>
                    <input type="text" name="pd_sp_SKU"
                        value="{{ old('pd_sp_SKU', $productSalepage->pd_sp_SKU ?? '') }}"
                        class="input w-full bg-gray-900 border border-gray-600 text-gray-300 font-mono focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl h-12 placeholder-gray-600"
                        placeholder="เช่น P-001" />
                </div>
            </div>

            <div class="form-control w-full">
                <label class="label text-xs font-bold text-gray-400 uppercase tracking-wider">รายละเอียดสินค้า</label>
                <textarea name="pd_sp_description" rows="5"
                    class="textarea w-full bg-gray-900 border border-gray-600 text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl leading-relaxed p-4">{{ old('pd_sp_description', $productSalepage->pd_sp_description ?? '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- 📦 ข้อมูลจัดส่ง --}}
    <div class="bg-gray-800 rounded-3xl shadow-xl border border-gray-700/50 overflow-hidden">
        <div class="bg-gray-900/40 px-6 py-5 border-b border-gray-700/50 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-orange-500/20 text-orange-400 flex items-center justify-center"><i
                    class="fas fa-truck"></i></div>
            <h3 class="text-lg font-bold text-white">ข้อมูลการจัดส่ง</h3>
        </div>

        <div class="p-6 md:p-8 space-y-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                <div class="form-control bg-gray-900/50 p-3 rounded-xl border border-gray-700/50">
                    <label class="label text-[10px] font-bold text-gray-400 uppercase tracking-wider pb-1">น้ำหนัก
                        (kg)</label>
                    <input type="number" step="0.01" name="pd_sp_weight"
                        value="{{ old('pd_sp_weight', $productSalepage->pd_sp_weight ?? '') }}"
                        class="input input-sm h-10 w-full bg-gray-800 border-gray-600 text-white rounded-lg focus:border-orange-500 text-center font-mono">
                </div>
                <div class="form-control bg-gray-900/50 p-3 rounded-xl border border-gray-700/50">
                    <label class="label text-[10px] font-bold text-gray-400 uppercase tracking-wider pb-1">กว้าง
                        (cm)</label>
                    <input type="number" name="pd_sp_width"
                        value="{{ old('pd_sp_width', $productSalepage->pd_sp_width ?? '') }}"
                        class="input input-sm h-10 w-full bg-gray-800 border-gray-600 text-white rounded-lg focus:border-orange-500 text-center font-mono">
                </div>
                <div class="form-control bg-gray-900/50 p-3 rounded-xl border border-gray-700/50">
                    <label class="label text-[10px] font-bold text-gray-400 uppercase tracking-wider pb-1">ยาว
                        (cm)</label>
                    <input type="number" name="pd_sp_length"
                        value="{{ old('pd_sp_length', $productSalepage->pd_sp_length ?? '') }}"
                        class="input input-sm h-10 w-full bg-gray-800 border-gray-600 text-white rounded-lg focus:border-orange-500 text-center font-mono">
                </div>
                <div class="form-control bg-gray-900/50 p-3 rounded-xl border border-gray-700/50">
                    <label class="label text-[10px] font-bold text-gray-400 uppercase tracking-wider pb-1">สูง
                        (cm)</label>
                    <input type="number" name="pd_sp_height"
                        value="{{ old('pd_sp_height', $productSalepage->pd_sp_height ?? '') }}"
                        class="input input-sm h-10 w-full bg-gray-800 border-gray-600 text-white rounded-lg focus:border-orange-500 text-center font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-gray-700/50 pt-6">
                <label
                    class="flex items-center gap-4 bg-gray-900/80 hover:bg-gray-700 p-4 rounded-2xl border border-gray-600 transition-colors cursor-pointer">
                    <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-400"><i
                            class="fas fa-shipping-fast"></i></div>
                    <div class="flex-1">
                        <span class="block text-sm font-bold text-white">ฟรีค่าจัดส่ง (โอน)</span>
                        <span class="text-xs text-gray-500">สำหรับลูกค้ายอดโอนเงิน</span>
                    </div>
                    <input type="hidden" name="pd_sp_free_shipping" value="0">
                    <input type="checkbox" name="pd_sp_free_shipping" value="1" class="toggle toggle-info"
                        {{ old('pd_sp_free_shipping', $productSalepage->pd_sp_free_shipping ?? 0) ? 'checked' : '' }}>
                </label>

                <label
                    class="flex items-center gap-4 bg-gray-900/80 hover:bg-gray-700 p-4 rounded-2xl border border-gray-600 transition-colors cursor-pointer">
                    <div class="w-10 h-10 rounded-full bg-pink-500/10 flex items-center justify-center text-pink-400">
                        <i class="fas fa-hand-holding-usd"></i></div>
                    <div class="flex-1">
                        <span class="block text-sm font-bold text-white">ฟรีค่าจัดส่ง (COD)</span>
                        <span class="text-xs text-gray-500">สำหรับลูกค้าเก็บเงินปลายทาง</span>
                    </div>
                    <input type="hidden" name="pd_sp_free_cod" value="0">
                    <input type="checkbox" name="pd_sp_free_cod" value="1" class="toggle toggle-secondary"
                        {{ old('pd_sp_free_cod', $productSalepage->pd_sp_free_cod ?? 0) ? 'checked' : '' }}>
                </label>
            </div>
        </div>
    </div>

    {{-- 🎨 ตัวเลือกสินค้า (Variants) --}}
    <div class="bg-gray-800 rounded-3xl shadow-xl border border-gray-700/50 overflow-hidden">
        <div class="bg-gray-900/40 px-6 py-5 border-b border-gray-700/50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-white flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-purple-500/20 text-purple-400 flex items-center justify-center"><i
                        class="fas fa-list-ul"></i></div>
                <div>ตัวเลือกสินค้า <span class="block text-[10px] text-gray-500 font-normal">เช่น สี, ไซส์,
                        รสชาติ</span></div>
            </h3>
            <button type="button" @click="addOption()"
                class="btn btn-sm bg-purple-600 hover:bg-purple-700 border-none text-white rounded-lg shadow-md shadow-purple-900/30">
                <i class="fas fa-plus"></i> <span class="hidden sm:inline ml-1">เพิ่มตัวเลือก</span>
            </button>
        </div>

        <div class="p-6 md:p-8 space-y-4 bg-gray-800/30">
            <template x-for="(option, index) in options" :key="option.id || index">
                <div class="relative bg-gray-900/80 border border-gray-700 p-5 rounded-2xl shadow-inner group">

                    <button type="button"
                        @click="options = options.filter(o => (o.id || o.option_id) !== (option.id || option.option_id))"
                        class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center shadow-lg hover:scale-110 transition-transform z-10 border-2 border-gray-900"
                        title="ลบตัวเลือก">
                        <i class="fas fa-times text-sm"></i>
                    </button>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                        {{-- Upload Pic --}}
                        <div class="lg:col-span-2 flex flex-col items-center">
                            <label class="relative cursor-pointer group/img w-24 h-24">
                                <div
                                    class="w-full h-full rounded-xl border-2 border-dashed border-gray-600 flex items-center justify-center overflow-hidden bg-gray-800 group-hover/img:border-emerald-500 transition-colors">
                                    <template x-if="option.image_preview">
                                        <img :src="option.image_preview" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!option.image_preview">
                                        <i
                                            class="fas fa-camera text-2xl text-gray-500 group-hover/img:text-emerald-400"></i>
                                    </template>
                                </div>
                                <input type="file" :name="`product_options[${index}][image]`" class="hidden"
                                    accept="image/*" @change="previewOptionImage($event, index)">
                                <input type="hidden" :name="`product_options[${index}][options_img_id]`"
                                    x-model="option.options_img_id">
                            </label>
                            <span class="text-[10px] text-gray-500 mt-2 font-medium">รูปตัวเลือก (ถ้ามี)</span>
                        </div>

                        {{-- Details --}}
                        <div class="lg:col-span-10 grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="form-control">
                                <label
                                    class="label text-[10px] font-bold text-gray-400 uppercase tracking-wider">ชื่อตัวเลือก
                                    <span class="text-red-500">*</span></label>
                                <input type="text" :name="`product_options[${index}][option_name]`"
                                    x-model="option.option_name"
                                    class="input w-full bg-gray-800 border-gray-600 text-white rounded-xl focus:border-purple-500 h-11"
                                    placeholder="เช่น สีดำ, Size L" required>
                            </div>
                            <div class="form-control">
                                <label
                                    class="label text-[10px] font-bold text-gray-400 uppercase tracking-wider">SKU</label>
                                <input type="text" :name="`product_options[${index}][option_SKU]`"
                                    x-model="option.option_SKU"
                                    class="input w-full bg-gray-800 border-gray-600 text-white rounded-xl focus:border-purple-500 h-11 font-mono text-sm"
                                    placeholder="SKU (ไม่บังคับ)">
                            </div>
                            <div class="form-control">
                                <label
                                    class="label text-[10px] font-bold text-gray-400 uppercase tracking-wider">ราคา</label>
                                <input type="number" step="0.01" :name="`product_options[${index}][option_price]`"
                                    x-model="option.option_price"
                                    class="input w-full bg-gray-800 border-gray-600 text-white rounded-xl focus:border-purple-500 h-11 font-bold"
                                    placeholder="ราคาสุทธิ">
                            </div>
                            <div class="form-control sm:col-span-3">
                                <label
                                    class="label text-[10px] font-bold text-emerald-400 uppercase tracking-wider">สต็อกของตัวเลือกนี้</label>
                                <input type="number" :name="`product_options[${index}][option_stock]`"
                                    x-model="option.option_stock"
                                    class="input w-full bg-emerald-900/10 border-emerald-500/30 text-emerald-300 rounded-xl focus:border-emerald-500 h-11 font-bold"
                                    placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="options.length === 0"
                class="text-center py-10 text-gray-500 border-2 border-dashed border-gray-700 rounded-2xl bg-gray-900/30">
                <i class="fas fa-code-branch text-4xl mb-3 opacity-30"></i>
                <p class="text-base font-bold text-gray-400">ไม่มีตัวเลือกสินค้า</p>
                <p class="text-xs mt-1">หากมีหลายสี หลายขนาด ให้คลิก "เพิ่มตัวเลือก"</p>
            </div>
        </div>

        {{-- 💰 ราคาหลัก & สต็อกหลัก --}}
        <div class="p-6 md:p-8 bg-gray-900 border-t border-gray-700">
            <h4 class="text-sm font-bold text-white mb-4"><i class="fas fa-tags text-emerald-500 mr-2"></i>
                ราคาและสต็อก (กรณีไม่มีตัวเลือกย่อย)</h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="form-control w-full relative">
                    <label class="label text-[10px] font-bold text-gray-400 uppercase tracking-wider">ราคาขายจริง <span
                            class="text-red-500">*</span></label>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500 font-bold">
                            ฿</div>
                        <input type="number" step="0.01" name="pd_sp_price"
                            value="{{ old('pd_sp_price', $productSalepage->pd_sp_price ?? '') }}"
                            class="input w-full bg-gray-800 border-gray-600 text-white focus:border-emerald-500 rounded-xl pl-8 font-black text-lg h-14"
                            required />
                    </div>
                </div>
                <div class="form-control w-full relative">
                    <label class="label text-[10px] font-bold text-red-400 uppercase tracking-wider">ส่วนลดโปรโมชั่น
                        (โชว์ป้าย Sale)</label>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-red-500 font-bold">
                            -฿</div>
                        <input type="number" step="0.01" name="pd_sp_discount"
                            value="{{ old('pd_sp_discount', $productSalepage->pd_sp_discount ?? 0) }}"
                            class="input w-full bg-red-500/10 border-red-500/30 text-red-400 focus:border-red-500 rounded-xl pl-10 font-bold h-14" />
                    </div>
                </div>
                <div class="form-control w-full relative">
                    <label class="label text-[10px] font-bold text-emerald-400 uppercase tracking-wider">สต็อกสินค้ารวม
                        <span x-show="options.length > 0"
                            class="text-[9px] text-gray-500 ml-1">(คำนวณอัตโนมัติ)</span></label>
                    <input type="number" name="pd_sp_stock" x-model="mainStock" :readonly="options.length > 0"
                        class="input w-full rounded-xl font-bold h-14"
                        :class="options.length > 0 ? 'bg-gray-800 border-gray-700 text-gray-500 cursor-not-allowed' :
                            'bg-emerald-900/20 border-emerald-500/50 text-emerald-400 focus:border-emerald-400'">
                </div>
            </div>
        </div>
    </div>

    {{-- 📸 แกลเลอรีรูปภาพหลัก (Drag & Drop) --}}
    <div class="bg-gray-800 rounded-3xl shadow-xl border border-gray-700/50 overflow-hidden">
        <div class="bg-gray-900/40 px-6 py-5 border-b border-gray-700/50 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-pink-500/20 text-pink-400 flex items-center justify-center"><i
                    class="fas fa-images"></i></div>
            <h3 class="text-lg font-bold text-white">แกลเลอรีรูปภาพหลัก</h3>
        </div>
        <div class="p-6 md:p-8">
            <div id="upload-zone"
                class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-600 rounded-2xl bg-gray-900/50 hover:bg-gray-800 hover:border-emerald-500 transition-all cursor-pointer relative group">
                <input type="file" name="images[]" id="images" multiple accept="image/*"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                <div
                    class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center text-gray-400 group-hover:text-emerald-400 group-hover:scale-110 transition-all mb-3 shadow-inner">
                    <i class="fas fa-cloud-upload-alt text-3xl"></i>
                </div>
                <p class="text-sm font-bold text-gray-300">คลิก หรือ ลากไฟล์รูปภาพมาวางที่นี่</p>
                <p class="text-xs text-gray-500 mt-1">รองรับอัปโหลดพร้อมกันหลายไฟล์</p>
            </div>

            <div id="new-image-preview" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4 mt-6"></div>

            @if (isset($productSalepage) && $productSalepage->images->count() > 0)
                <div class="mt-8">
                    <h4
                        class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-700 pb-2">
                        รูปภาพที่อัปโหลดไว้แล้ว</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4" id="image-list">
                        @foreach ($productSalepage->images->sortBy('img_sort') as $image)
                            @php
                                $isMain = $image->img_sort == 0;
                                $borderColor = $isMain
                                    ? 'border-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.2)]'
                                    : 'border-gray-700 hover:border-gray-500';
                                $imagePath = \Illuminate\Support\Str::startsWith($image->img_path, 'http')
                                    ? $image->img_path
                                    : asset('storage/' . $image->img_path);
                            @endphp
                            <div class="relative group rounded-2xl overflow-hidden border-2 {{ $borderColor }} bg-gray-900 aspect-square flex items-center justify-center transition-all"
                                id="image-card-{{ $image->img_id }}">

                                <img src="{{ $imagePath }}" alt="Product Image"
                                    class="w-full h-full object-cover opacity-90 group-hover:opacity-40 transition-opacity duration-300">

                                @if ($isMain)
                                    <div
                                        class="absolute top-2 left-2 bg-emerald-500 text-white text-[10px] font-black px-2 py-0.5 rounded shadow-md border border-emerald-400">
                                        ภาพหลัก</div>
                                @endif

                                {{-- Overlay Actions --}}
                                <div
                                    class="absolute inset-0 flex flex-col items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    @if (!$isMain)
                                        <button type="button"
                                            class="btn btn-sm bg-emerald-600 hover:bg-emerald-500 border-none text-white w-24 rounded-xl font-bold text-xs shadow-lg set-main-image"
                                            data-image-id="{{ $image->img_id }}">
                                            ตั้งเป็นปก
                                        </button>
                                    @endif
                                    <button type="button"
                                        class="btn btn-sm bg-red-600 hover:bg-red-500 border-none text-white w-24 rounded-xl font-bold text-xs shadow-lg delete-image"
                                        data-image-id="{{ $image->img_id }}">
                                        ลบรูปนี้
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadInput = document.getElementById('images');
            const previewContainer = document.getElementById('new-image-preview');
            const uploadZone = document.getElementById('upload-zone');

            if (uploadZone) {
                ['dragenter', 'dragover'].forEach(eName => {
                    uploadZone.addEventListener(eName, (e) => {
                        e.preventDefault();
                        uploadZone.classList.add('border-emerald-500', 'bg-gray-800');
                    });
                });
                ['dragleave', 'drop'].forEach(eName => {
                    uploadZone.addEventListener(eName, (e) => {
                        e.preventDefault();
                        uploadZone.classList.remove('border-emerald-500', 'bg-gray-800');
                    });
                });

                uploadZone.addEventListener('drop', (e) => {
                    const files = e.dataTransfer.files;
                    uploadInput.files = files;
                    const event = new Event('change');
                    uploadInput.dispatchEvent(event);
                });
            }

            if (uploadInput) {
                uploadInput.addEventListener('change', function() {
                    previewContainer.innerHTML = '';
                    const files = Array.from(this.files);

                    files.forEach(file => {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const div = document.createElement('div');
                                div.className =
                                    'relative rounded-2xl overflow-hidden border-2 border-emerald-500/50 aspect-square shadow-md transform transition-all animate-fade-in-up';
                                div.innerHTML =
                                    `<img src="${e.target.result}" class="w-full h-full object-cover"><div class="absolute top-2 right-2 bg-emerald-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded">NEW</div>`;
                                previewContainer.appendChild(div);
                            }
                            reader.readAsDataURL(file);
                        }
                    });
                });
            }

            document.getElementById('image-list')?.addEventListener('click', function(e) {
                const deleteButton = e.target.closest('.delete-image');
                const setMainButton = e.target.closest('.set-main-image');

                if (deleteButton) handleDeleteImage(deleteButton);
                else if (setMainButton) handleSetMainImage(setMainButton);
            });

            function handleDeleteImage(button) {
                if (confirm('ยืนยันที่จะลบรูปภาพนี้?')) {
                    const id = button.dataset.imageId;
                    const card = document.getElementById(`image-card-${id}`);
                    const originalText = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    button.disabled = true;

                    fetch(`/admin/products/image/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(r => r.json()).then(data => {
                        if (data.success) {
                            card.style.transform = 'scale(0.8)';
                            card.style.opacity = '0';
                            setTimeout(() => card.remove(), 300);
                        } else {
                            alert('ลบไม่สำเร็จ: ' + (data.message || 'Error'));
                            button.innerHTML = originalText;
                            button.disabled = false;
                        }
                    }).catch(e => {
                        alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                        button.innerHTML = originalText;
                        button.disabled = false;
                    });
                }
            }

            function handleSetMainImage(button) {
                const imageId = button.dataset.imageId;
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;

                fetch(`/admin/products/image/${imageId}/set-main`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }).then(r => r.json()).then(data => {
                    if (!data.success) {
                        alert('ตั้งเป็นภาพหลักไม่สำเร็จ');
                    }
                    location.reload();
                }).catch(e => {
                    alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            }
        });
    </script>
@endpush
