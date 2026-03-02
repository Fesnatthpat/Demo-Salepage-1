


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
    <div class="alert alert-error shadow-lg mb-6 bg-red-900/50 border-red-800 text-red-200">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="font-bold">พบข้อผิดพลาด!</h3>
                <ul class="list-disc pl-5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <li><?php echo e($error); ?></li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<div class="card bg-gray-800 shadow-lg border border-gray-700 rounded-xl overflow-hidden" x-data="{
    options: <?php echo e(json_encode(
        old(
            'product_options',
            isset($productSalepage) && $productSalepage->options
                ? $productSalepage->options->map(function ($opt) {
                    return [
                        'id' => $opt->option_id,
                        'option_name' => $opt->option_name,
                        'option_SKU' => $opt->option_SKU,
                        'option_price' => $opt->option_price,
                        'option_price2' => $opt->option_price2,
                        'option_stock' => $opt->stock ? $opt->stock->quantity : 0,
                    ];
                })
                : [],
        ),
    )); ?>,
    mainStock: <?php echo e(old('pd_sp_stock', $productSalepage->pd_sp_stock ?? 0)); ?>,
    addOption() {
        this.options.push({ id: Date.now(), option_name: '', option_SKU: '', option_price: '', option_price2: '', option_stock: 0 });
        this.mainStock = 0;
    }
}">
    <div class="bg-gray-900/50 px-6 py-4 border-b border-gray-700 flex flex-wrap justify-between items-center gap-4">
        <h3 class="text-lg font-bold text-gray-100 flex items-center gap-2">
            <i class="fas fa-info-circle text-emerald-500"></i> ข้อมูลทั่วไป
        </h3>

        <div class="flex items-center gap-4">
            
            <div class="flex items-center gap-3 bg-gray-800 px-3 py-1.5 rounded-lg border border-gray-600 shadow-sm">
                <span class="text-sm font-medium text-gray-300">สถานะการขาย:</span>
                <input type="hidden" name="pd_sp_active" value="0">
                <input type="checkbox" name="pd_sp_active" value="1" class="toggle toggle-success toggle-sm"
                    <?php echo e(old('pd_sp_active', $productSalepage->pd_sp_active ?? 0) == 1 ? 'checked' : ''); ?> />
            </div>

            
            <div class="flex items-center gap-3 bg-gray-800 px-3 py-1.5 rounded-lg border border-gray-600 shadow-sm">
                <span class="text-sm font-medium text-gray-300">สินค้าแนะนำ:</span>
                <div class="flex items-center gap-4">
                    <label class="label cursor-pointer gap-1 p-0">
                        <input type="radio" name="is_recommended" value="1" class="radio radio-primary radio-xs"
                            <?php echo e(old('is_recommended', $productSalepage->is_recommended ?? 0) == 1 ? 'checked' : ''); ?> />
                        <span class="label-text text-xs text-gray-400">ใช่</span>
                    </label>
                    <label class="label cursor-pointer gap-1 p-0">
                        <input type="radio" name="is_recommended" value="0" class="radio radio-primary radio-xs"
                            <?php echo e(old('is_recommended', $productSalepage->is_recommended ?? 0) == 0 ? 'checked' : ''); ?> />
                        <span class="label-text text-xs text-gray-400">ไม่ใช่</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body p-6">
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($productSalepage->pd_sp_code)): ?>
            <div
                class="mb-6 flex items-center gap-2 text-sm text-blue-300 bg-blue-900/30 p-3 rounded-lg border border-blue-800">
                <i class="fas fa-tag"></i>
                <span>รหัสสินค้า (System): <strong><?php echo e($productSalepage->pd_sp_code); ?></strong></span>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="form-control w-full mb-6">
            <label class="label font-bold text-gray-300">รหัส SKU (สินค้าหลัก)</label>
            <div class="relative">
                <span class="absolute left-4 top-3 text-gray-500"><i class="fas fa-barcode"></i></span>
                <input type="text" name="pd_sp_SKU"
                    class="input input-bordered w-full pl-10 text-lg h-12 bg-gray-700 border-gray-600 text-gray-100 placeholder-gray-500 focus:border-emerald-500"
                    placeholder="ระบุรหัส SKU (ถ้ามี)"
                    value="<?php echo e(old('pd_sp_SKU', $productSalepage->pd_sp_SKU ?? '')); ?>" />
            </div>
        </div>

        
        <div class="form-control w-full mb-6">
            <label class="label font-bold text-gray-300">ชื่อสินค้า <span class="text-red-400">*</span></label>
            <input type="text" name="pd_sp_name"
                class="input input-bordered w-full text-lg h-12 bg-gray-700 border-gray-600 text-gray-100 placeholder-gray-500 focus:border-emerald-500"
                placeholder="ระบุชื่อสินค้า (เช่น เสื้อยืด Cotton 100%)"
                value="<?php echo e(old('pd_sp_name', $productSalepage->pd_sp_name ?? '')); ?>" />
        </div>



        
        <div class="form-control w-full mb-6">
            <label class="label font-bold text-gray-300">รายละเอียดสินค้า</label>
            <textarea name="pd_sp_details" rows="5"
                class="textarea textarea-bordered h-62 w-full text-base leading-relaxed bg-gray-700 border-gray-600 text-gray-100 placeholder-gray-500 focus:border-emerald-500"
                placeholder="อธิบายรายละเอียด คุณสมบัติ ขนาด หรือวิธีใช้..."><?php echo e(old('pd_sp_details', $productSalepage->pd_sp_description ?? ($productSalepage->pd_sp_details ?? ''))); ?></textarea>
        </div>

        
        <div class="divider text-gray-500 text-sm my-6">ข้อมูลการจัดส่ง (น้ำหนัก, ขนาด และค่าขนส่ง)</div>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            
            <div class="md:col-span-3 form-control">
                <label class="label font-bold text-gray-300">น้ำหนัก (กิโลกรัม)</label>
                <input type="number" step="0.01" name="pd_sp_weight"
                    class="input input-bordered w-full bg-gray-700 border-gray-600 text-gray-100 placeholder-gray-500 focus:border-emerald-500"
                    placeholder="0.00" value="<?php echo e(old('pd_sp_weight', $productSalepage->pd_sp_weight ?? '')); ?>" />
            </div>

            
            <div class="md:col-span-3 form-control">
                <label class="label font-bold text-gray-300">กว้าง (ซม.)</label>
                <input type="number" step="0.01" name="pd_sp_width"
                    class="input input-bordered w-full bg-gray-700 border-gray-600 text-gray-100 placeholder-gray-500 focus:border-emerald-500"
                    placeholder="0.00" value="<?php echo e(old('pd_sp_width', $productSalepage->pd_sp_width ?? '')); ?>" />
            </div>

            
            <div class="md:col-span-3 form-control">
                <label class="label font-bold text-gray-300">ยาว (ซม.)</label>
                <input type="number" step="0.01" name="pd_sp_length"
                    class="input input-bordered w-full bg-gray-700 border-gray-600 text-gray-100 placeholder-gray-500 focus:border-emerald-500"
                    placeholder="0.00" value="<?php echo e(old('pd_sp_length', $productSalepage->pd_sp_length ?? '')); ?>" />
            </div>

            
            <div class="md:col-span-3 form-control">
                <label class="label font-bold text-gray-300">สูง (ซม.)</label>
                <input type="number" step="0.01" name="pd_sp_height"
                    class="input input-bordered w-full bg-gray-700 border-gray-600 text-gray-100 placeholder-gray-500 focus:border-emerald-500"
                    placeholder="0.00" value="<?php echo e(old('pd_sp_height', $productSalepage->pd_sp_height ?? '')); ?>" />
            </div>
        </div>

        
        <div class="mt-8 mb-2">
            <h1 class="text-xl font-bold text-gray-100">จัดการค่าจัดส่ง</h1>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            
            <div class="flex items-center gap-4 bg-gray-900/40 px-5 py-4 rounded-xl border border-gray-700 shadow-sm hover:border-emerald-500/50 transition-colors cursor-pointer"
                onclick="const cb = this.querySelector('input[type=checkbox]'); cb.checked = !cb.checked;">
                <input type="hidden" name="pd_sp_free_shipping" value="0">
                <input type="checkbox" name="pd_sp_free_shipping" value="1"
                    class="toggle toggle-success bg-gray-600 border-gray-500 [--tglbg:theme(colors.gray.200)] checked:[--tglbg:theme(colors.white)]"
                    <?php echo e(old('pd_sp_free_shipping', $productSalepage->pd_sp_free_shipping ?? 0) == 1 ? 'checked' : ''); ?>

                    onclick="event.stopPropagation();" />
                <div>
                    <span class="block text-base font-bold text-gray-200">ฟรีโอน</span>
                    <span class="block text-xs text-gray-400">ฟรีค่าจัดส่งเมื่อลูกค้าชำระเงินแบบโอนเงิน</span>
                </div>
            </div>

            
            <div class="flex items-center gap-4 bg-gray-900/40 px-5 py-4 rounded-xl border border-gray-700 shadow-sm hover:border-emerald-500/50 transition-colors cursor-pointer"
                onclick="const cb = this.querySelector('input[type=checkbox]'); cb.checked = !cb.checked;">
                <input type="hidden" name="pd_sp_free_cod" value="0">
                <input type="checkbox" name="pd_sp_free_cod" value="1"
                    class="toggle toggle-success bg-gray-600 border-gray-500 [--tglbg:theme(colors.gray.200)] checked:[--tglbg:theme(colors.white)]"
                    <?php echo e(old('pd_sp_free_cod', $productSalepage->pd_sp_free_cod ?? 0) == 1 ? 'checked' : ''); ?>

                    onclick="event.stopPropagation();" />
                <div>
                    <span class="block text-base font-bold text-gray-200">ฟรีเก็บปลายทาง</span>
                    <span class="block text-xs text-gray-400">ฟรีค่าจัดส่งเมื่อลูกค้าเลือกชำระเงินปลายทาง (COD)</span>
                </div>
            </div>
        </div>

    </div>

    
    <div class="card-body p-6 border-t border-gray-700 bg-gray-800/20">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-100 flex items-center gap-2">
                <i class="fas fa-tags text-emerald-500"></i> ตัวเลือกสินค้า (Variants)
            </h3>
            <button type="button" @click="addOption()"
                class="btn btn-sm btn-emerald bg-emerald-600 hover:bg-emerald-700 border-none text-white">
                <i class="fas fa-plus mr-1"></i> เพิ่มตัวเลือก
            </button>
        </div>

        <p class="text-sm text-gray-400 mb-4 italic">เพิ่มตัวเลือกสินค้า เช่น ขวดเล็ก, ขวดใหญ่ หรือ สีขาว, สีดำ
            (หากไม่มี ให้ข้ามส่วนนี้)</p>

        <div class="space-y-3">
            <template x-for="(option, index) in options" :key="option.id || index">
                <div class="flex flex-col gap-3 p-4 bg-gray-900/30 rounded-xl border border-gray-700">

                    
                    <div class="flex flex-wrap md:flex-nowrap gap-3 items-end">
                        
                        <div class="form-control w-full md:flex-1">
                            <label class="label py-1"><span class="label-text-alt text-gray-400">ชื่อตัวเลือก (เช่น
                                    สีดำ,
                                    ไซส์ L)</span></label>
                            <input type="text" :name="`product_options[${index}][option_name]`"
                                x-model="option.option_name" required
                                class="input input-bordered w-full bg-gray-700 border-gray-600 text-gray-100 focus:border-emerald-500"
                                placeholder="ระบุชื่อตัวเลือก">
                        </div>

                        
                        <div class="form-control w-full md:w-1/3">
                            <label class="label py-1"><span class="label-text-alt text-gray-400">รหัส SKU
                                    (Option)</span></label>
                            <input type="text" :name="`product_options[${index}][option_SKU]`"
                                x-model="option.option_SKU"
                                class="input input-bordered w-full bg-gray-700 border-gray-600 text-gray-100 focus:border-emerald-500"
                                placeholder="เช่น T-SHIRT-BLK-L">
                        </div>
                    </div>

                    
                    <div class="flex flex-wrap md:flex-nowrap gap-3 items-end">
                        
                        <div class="form-control w-full md:flex-1">
                            <label class="label py-1"><span class="label-text-alt text-gray-400">ราคา 1
                                    (บาท)</span></label>
                            <input type="number" step="0.01" :name="`product_options[${index}][option_price]`"
                                x-model="option.option_price"
                                class="input input-bordered w-full bg-gray-700 border-gray-600 text-gray-100"
                                placeholder="ใช้ราคาหลัก">
                        </div>

                        
                        <div class="form-control w-full md:flex-1">
                            <label class="label py-1"><span class="label-text-alt text-gray-400">สต็อก</span></label>
                            <input type="number" :name="`product_options[${index}][option_stock]`"
                                x-model="option.option_stock"
                                class="input input-bordered w-full bg-gray-700 border-gray-600 text-gray-100"
                                placeholder="0">
                        </div>

                        
                        <button type="button"
                            @click="options = options.filter(o => (o.id || o.option_id) !== (option.id || option.option_id))"
                            class="btn btn-square btn-error btn-outline border-red-800 hover:bg-red-600 text-red-500 hover:text-white md:mt-auto">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                </div>
            </template>

            <div x-show="options.length === 0"
                class="text-center py-8 border-2 border-dashed border-gray-700 rounded-xl bg-gray-800/50">
                <p class="text-gray-500">ยังไม่มีตัวเลือกสินค้า คลิกปุ่ม "เพิ่มตัวเลือก" ด้านบนเพื่อเริ่มสร้าง</p>
            </div>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6 mt-4">
            
            <div class="md:col-span-4 form-control">
                <label class="label font-bold text-gray-300">ราคาขาย (บาท) <span class="text-red-400">*</span></label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-gray-500 font-bold">฿</span>
                    <input type="number" step="0.01" name="pd_sp_price"
                        class="input input-bordered w-full pl-10 font-mono text-xl font-bold bg-gray-700 border-gray-600 text-gray-100 placeholder-gray-500 focus:border-emerald-500"
                        placeholder="0.00" value="<?php echo e(old('pd_sp_price', $productSalepage->pd_sp_price ?? '')); ?>" />
                </div>
            </div>

            
            <div class="md:col-span-4 form-control">
                <label class="label font-bold text-gray-300">ส่วนลด (บาท)</label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-gray-500 font-bold">฿</span>
                    <input type="number" step="0.01" name="pd_sp_discount"
                        class="input input-bordered w-full pl-10 font-mono text-xl text-red-400 bg-gray-700 border-gray-600 placeholder-gray-500 focus:border-emerald-500"
                        placeholder="0.00"
                        value="<?php echo e(old('pd_sp_discount', $productSalepage->pd_sp_discount ?? '')); ?>" />
                </div>
            </div>

            
            

            
            <div class="md:col-span-4 form-control">
                <label class="label font-bold text-gray-300">ตำแหน่งแสดงผล</label>
                <select name="pd_sp_display_location"
                    class="select select-bordered w-full text-base bg-gray-700 border-gray-600 text-gray-100 focus:border-emerald-500">
                    <option value="general"
                        <?php echo e(old('pd_sp_display_location', $productSalepage->pd_sp_display_location ?? 'general') == 'general' ? 'selected' : ''); ?>>
                        📦 สินค้าทั่วไป
                    </option>
                    <option value="homepage"
                        <?php echo e(old('pd_sp_display_location', $productSalepage->pd_sp_display_location ?? '') == 'homepage' ? 'selected' : ''); ?>>
                        ⭐ สินค้าแนะนำ (หน้าแรก)
                    </option>
                </select>
            </div>
        </div>
    </div>

</div>



<div class="card bg-gray-800 shadow-lg border border-gray-700 rounded-xl overflow-hidden mt-6">
    <div class="bg-gray-900/50 px-6 py-4 border-b border-gray-700">
        <h3 class="text-lg font-bold text-gray-100 flex items-center gap-2">
            <i class="fas fa-images text-emerald-500"></i> รูปภาพสินค้า
        </h3>
    </div>

    <div class="card-body p-6">
        <div class="form-control w-full mb-8">
            <div class="relative group">
                <div id="upload-zone"
                    class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-600 rounded-2xl bg-gray-700 hover:bg-gray-600 hover:border-emerald-500 transition-all cursor-pointer">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <div class="bg-gray-600 p-3 rounded-full shadow-sm mb-3">
                            <i class="fas fa-cloud-upload-alt text-2xl text-emerald-400"></i>
                        </div>
                        <p class="mb-1 text-base text-gray-300"><span
                                class="font-bold text-emerald-400">คลิกเพื่อเลือกรูป</span> หรือลากไฟล์มาวาง</p>
                        <p class="text-xs text-gray-500">รองรับ JPG, PNG, WEBP (สูงสุด 64MB/รูป)</p>
                    </div>
                    <input type="file" name="images[]" id="images" multiple accept="image/*"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                </div>
            </div>
        </div>

        <div id="new-image-preview" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-4"></div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($productSalepage) && $productSalepage->images->count() > 0): ?>
            <div class="divider text-gray-500 text-sm">รูปภาพปัจจุบัน</div>
            <div id="image-list" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php
                    // เรียงตาม img_sort ให้รูปหลัก (0) ขึ้นก่อนเสมอ
                    $sortedImages = $productSalepage->images->sortBy('img_sort');
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sortedImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    
                    <?php $isMain = !is_null($image->img_sort) && (int)$image->img_sort === 0; ?>

                    <div class="relative group rounded-lg overflow-hidden border-2 shadow-sm aspect-square bg-gray-900 <?php echo e($isMain ? 'border-emerald-500' : 'border-gray-600'); ?>"
                        id="image-card-<?php echo e($image->img_id); ?>" data-image-id="<?php echo e($image->img_id); ?>">

                        <img src="<?php echo e(asset('storage/' . $image->img_path)); ?>" class="w-full h-full object-cover">

                        
                        <div class="absolute top-2 right-2 bg-emerald-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg main-badge <?php echo e($isMain ? '' : 'hidden'); ?>"
                            title="รูปภาพหลัก">
                            <i class="fas fa-star text-xs"></i>
                        </div>

                        <div
                            class="absolute inset-0 bg-black/80 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-center items-center gap-2 p-2 hover-actions">

                            
                            <button type="button" class="btn btn-xs btn-success w-full text-white set-main-image"
                                data-image-id="<?php echo e($image->img_id); ?>">
                                <i class="fas fa-star"></i> ตั้งเป็นหลัก
                            </button>

                            <button type="button" class="btn btn-xs btn-error w-full text-white delete-image"
                                data-image-id="<?php echo e($image->img_id); ?>">
                                <i class="fas fa-trash"></i> ลบ
                            </button>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image Upload Preview Logic
            const uploadInput = document.getElementById('images');
            const previewContainer = document.getElementById('new-image-preview');
            const uploadZone = document.getElementById('upload-zone');

            if (uploadZone) {
                ['dragenter', 'dragover'].forEach(eName => {
                    uploadZone.addEventListener(eName, (e) => {
                        e.preventDefault();
                        uploadZone.classList.add('border-emerald-500', 'bg-gray-600');
                    });
                });
                ['dragleave', 'drop'].forEach(eName => {
                    uploadZone.addEventListener(eName, (e) => {
                        e.preventDefault();
                        uploadZone.classList.remove('border-emerald-500', 'bg-gray-600');
                    });
                });

                uploadZone.addEventListener('drop', (e) => {
                    const files = e.dataTransfer.files;
                    uploadInput.files = files; // assign dropped files to input
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
                                    'relative rounded-lg overflow-hidden border border-gray-600 aspect-square shadow-sm';
                                div.innerHTML =
                                    `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                                previewContainer.appendChild(div);
                            }
                            reader.readAsDataURL(file);
                        }
                    });
                });
            }

            // Unified Image Action Logic (Delete & Set Main)
            document.getElementById('image-list')?.addEventListener('click', function(e) {
                const deleteButton = e.target.closest('.delete-image');
                const setMainButton = e.target.closest('.set-main-image');

                if (deleteButton) {
                    handleDeleteImage(deleteButton);
                } else if (setMainButton) {
                    handleSetMainImage(setMainButton);
                }
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
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                            'Content-Type': 'application/json'
                        }
                    }).then(r => r.json()).then(data => {
                        if (data.success) {
                            card.remove();
                        } else {
                            alert('ลบไม่สำเร็จ: ' + (data.message || 'Error'));
                            button.innerHTML = originalText;
                            button.disabled = false;
                        }
                    }).catch(e => {
                        console.error(e);
                        alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                        button.innerHTML = originalText;
                        button.disabled = false;
                    });
                }
            }

            function handleSetMainImage(button) {
                const imageId = button.dataset.imageId;
                const targetCard = document.getElementById(`image-card-${imageId}`);

                // 1. Reset all cards to non-main visual state
                const allCards = document.querySelectorAll('#image-list > div');
                allCards.forEach(c => {
                    // Border
                    c.classList.remove('border-emerald-500');
                    c.classList.add('border-gray-600');

                    // Hide Badge
                    const badge = c.querySelector('.main-badge');
                    if (badge) badge.classList.add('hidden');
                });

                // 2. Set target card as main (Optimistic UI Update)
                targetCard.classList.remove('border-gray-600');
                targetCard.classList.add('border-emerald-500');

                // Show Badge
                const targetBadge = targetCard.querySelector('.main-badge');
                if (targetBadge) targetBadge.classList.remove('hidden');

                // 3. Send Request to Backend
                fetch(`/admin/products/image/${imageId}/set-main`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }).then(r => r.json()).then(data => {
                    if (!data.success) {
                        alert('ตั้งเป็นภาพหลักไม่สำเร็จ: ' + (data.message || 'Error'));
                        location.reload();
                    } else {
                        // Success -> Refresh to re-order images properly
                        location.reload();
                    }
                }).catch(e => {
                    console.error('Set main image error:', e);
                    alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                });
            }
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/products/_form.blade.php ENDPATH**/ ?>