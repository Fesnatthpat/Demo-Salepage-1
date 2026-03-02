

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
                        'option_stock' => $opt->stock ? $opt->stock->quantity : 0,
                        'options_img_id' => $opt->options_img_id,
                        // ✅ แก้ไขตรงนี้: เช็คก่อนว่ามี options_img_id จริงๆ ถึงจะดึงรูปมาโชว์
                        'image_preview' => $opt->options_img_id ? $opt->option_image_url : null,
                    ];
                })
                : [],
        ),
    )); ?>,
    mainStock: <?php echo e(old('pd_sp_stock', optional($productSalepage->stock)->quantity ?? 0)); ?>,
    addOption() {
        this.options.push({ id: Date.now(), option_name: '', option_SKU: '', option_price: '', option_stock: 0, options_img_id: null, image_preview: null });
        this.mainStock = 0;
    },
    previewOptionImage(event, index) {
        const file = event.target.files[0];
        if (file) {
            this.options[index].image_preview = URL.createObjectURL(file);
        }
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
        </div>
    </div>

    <div class="card-body p-6">
        
        <div class="form-control w-full mb-6">
            <label class="label font-bold text-gray-300">ชื่อสินค้า <span class="text-red-400">*</span></label>
            <input type="text" name="pd_sp_name"
                class="input input-bordered w-full bg-gray-700 border-gray-600 text-gray-100"
                value="<?php echo e(old('pd_sp_name', $productSalepage->pd_sp_name ?? '')); ?>" required />
        </div>

        
        <div class="form-control w-full mb-6">
            <label class="label font-bold text-gray-300">รายละเอียดสินค้า</label>
            <textarea name="pd_sp_details" rows="4"
                class="textarea textarea-bordered bg-gray-700 border-gray-600 text-gray-100"><?php echo e(old('pd_sp_details', $productSalepage->pd_sp_description ?? '')); ?></textarea>
        </div>

        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="form-control">
                <label class="label text-xs text-gray-400">น้ำหนัก (kg)</label>
                <input type="number" step="0.01" name="pd_sp_weight"
                    class="input input-bordered bg-gray-700 border-gray-600 text-gray-100"
                    value="<?php echo e(old('pd_sp_weight', $productSalepage->pd_sp_weight ?? '')); ?>">
            </div>
            <div class="form-control">
                <label class="label text-xs text-gray-400">กว้าง (cm)</label>
                <input type="number" name="pd_sp_width"
                    class="input input-bordered bg-gray-700 border-gray-600 text-gray-100"
                    value="<?php echo e(old('pd_sp_width', $productSalepage->pd_sp_width ?? '')); ?>">
            </div>
            <div class="form-control">
                <label class="label text-xs text-gray-400">ยาว (cm)</label>
                <input type="number" name="pd_sp_length"
                    class="input input-bordered bg-gray-700 border-gray-600 text-gray-100"
                    value="<?php echo e(old('pd_sp_length', $productSalepage->pd_sp_length ?? '')); ?>">
            </div>
            <div class="form-control">
                <label class="label text-xs text-gray-400">สูง (cm)</label>
                <input type="number" name="pd_sp_height"
                    class="input input-bordered bg-gray-700 border-gray-600 text-gray-100"
                    value="<?php echo e(old('pd_sp_height', $productSalepage->pd_sp_height ?? '')); ?>">
            </div>
        </div>

        
        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="flex items-center gap-3 bg-gray-900/40 p-4 rounded-xl border border-gray-700">
                <input type="hidden" name="pd_sp_free_shipping" value="0">
                <input type="checkbox" name="pd_sp_free_shipping" value="1" class="toggle toggle-success"
                    <?php echo e(old('pd_sp_free_shipping', $productSalepage->pd_sp_free_shipping ?? 0) ? 'checked' : ''); ?>>
                <span class="text-sm text-gray-200 font-bold">ฟรีค่าจัดส่ง (โอน)</span>
            </div>
            <div class="flex items-center gap-3 bg-gray-900/40 p-4 rounded-xl border border-gray-700">
                <input type="hidden" name="pd_sp_free_cod" value="0">
                <input type="checkbox" name="pd_sp_free_cod" value="1" class="toggle toggle-success"
                    <?php echo e(old('pd_sp_free_cod', $productSalepage->pd_sp_free_cod ?? 0) ? 'checked' : ''); ?>>
                <span class="text-sm text-gray-200 font-bold">ฟรีค่าจัดส่ง (COD)</span>
            </div>
        </div>

        
        <div class="divider text-gray-500 text-sm">ตัวเลือกสินค้า (Variants)</div>

        <div class="flex justify-end mb-4">
            <button type="button" @click="addOption()" class="btn btn-sm btn-success text-white">
                <i class="fas fa-plus mr-2"></i> เพิ่มตัวเลือก
            </button>
        </div>

        <div class="space-y-4">
            <template x-for="(option, index) in options" :key="option.id || index">
                <div
                    class="p-4 bg-gray-900/30 rounded-xl border border-gray-700 grid grid-cols-1 md:grid-cols-12 gap-4 items-center">

                    
                    <div class="md:col-span-2 flex flex-col items-center justify-center">
                        <label class="relative cursor-pointer group">
                            <div
                                class="w-20 h-20 rounded-lg border-2 border-dashed border-gray-600 flex items-center justify-center overflow-hidden bg-gray-800 hover:border-emerald-500 transition-colors">
                                <template x-if="option.image_preview">
                                    <img :src="option.image_preview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!option.image_preview">
                                    <i class="fas fa-camera text-gray-500 group-hover:text-emerald-400"></i>
                                </template>
                            </div>
                            
                            <input type="file" :name="`product_options[${index}][image]`" class="hidden"
                                accept="image/*" @change="previewOptionImage($event, index)">
                            <input type="hidden" :name="`product_options[${index}][options_img_id]`"
                                x-model="option.options_img_id">
                        </label>
                        <span class="text-[10px] text-gray-500 mt-1">รูปตัวเลือก</span>
                    </div>

                    <div class="md:col-span-3 form-control">
                        <label class="label py-1 text-xs text-gray-400">ชื่อตัวเลือก</label>
                        <input type="text" :name="`product_options[${index}][option_name]`"
                            x-model="option.option_name"
                            class="input input-bordered input-sm bg-gray-700 border-gray-600 text-gray-100"
                            placeholder="เช่น สีแดง, XL" required>
                    </div>

                    <div class="md:col-span-3 form-control">
                        <label class="label py-1 text-xs text-gray-400">SKU / ราคา</label>
                        <div class="flex gap-2">
                            <input type="text" :name="`product_options[${index}][option_SKU]`"
                                x-model="option.option_SKU"
                                class="input input-bordered input-sm bg-gray-700 border-gray-600 text-gray-100 w-1/2"
                                placeholder="SKU">
                            <input type="number" :name="`product_options[${index}][option_price]`"
                                x-model="option.option_price"
                                class="input input-bordered input-sm bg-gray-700 border-gray-600 text-gray-100 w-1/2"
                                placeholder="ราคา">
                        </div>
                    </div>

                    <div class="md:col-span-3 form-control">
                        <label class="label py-1 text-xs text-gray-400">สต็อก</label>
                        <input type="number" :name="`product_options[${index}][option_stock]`"
                            x-model="option.option_stock"
                            class="input input-bordered input-sm bg-gray-700 border-gray-600 text-gray-100"
                            placeholder="จำนวน">
                    </div>

                    <div class="md:col-span-1 flex justify-center md:pt-6">
                        <button type="button"
                            @click="options = options.filter(o => (o.id || o.option_id) !== (option.id || option.option_id))"
                            class="btn btn-circle btn-xs btn-error btn-outline">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 p-4 bg-gray-900/20 rounded-xl border border-gray-700">
            <div class="form-control">
                <label class="label font-bold text-gray-300">ราคาขายหลัก</label>
                <input type="number" name="pd_sp_price"
                    class="input input-bordered bg-gray-700 border-gray-600 text-gray-100 font-bold text-emerald-400"
                    value="<?php echo e(old('pd_sp_price', $productSalepage->pd_sp_price ?? '')); ?>" required />
            </div>
            <div class="form-control">
                <label class="label font-bold text-gray-300">ส่วนลด (บาท)</label>
                <input type="number" name="pd_sp_discount"
                    class="input input-bordered bg-gray-700 border-gray-600 text-red-400"
                    value="<?php echo e(old('pd_sp_discount', $productSalepage->pd_sp_discount ?? 0)); ?>" />
            </div>
            <div class="form-control">
                <label class="label font-bold text-gray-300">สต็อกรวม <span x-show="options.length > 0"
                        class="text-xs text-gray-500">(คำนวณอัตโนมัติ)</span></label>
                <input type="number" name="pd_sp_stock" x-model="mainStock" :readonly="options.length > 0"
                    class="input input-bordered border-gray-600"
                    :class="options.length > 0 ? 'bg-gray-800 text-gray-500 cursor-not-allowed' : 'bg-gray-700 text-gray-100'">
            </div>
        </div>
    </div>
</div>


<div class="card bg-gray-800 shadow-lg border border-gray-700 rounded-xl mt-6 overflow-hidden">
    <div class="bg-gray-900/50 px-6 py-4 border-b border-gray-700">
        <h3 class="text-lg font-bold text-gray-100 flex items-center gap-2">
            <i class="fas fa-images text-emerald-500"></i> แกลเลอรีรูปภาพหลัก
        </h3>
    </div>
    <div class="card-body p-6">
        <div id="upload-zone"
            class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-600 rounded-xl bg-gray-700 hover:bg-gray-600 transition-all cursor-pointer relative">
            <input type="file" name="images[]" id="images" multiple accept="image/*"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
            <i class="fas fa-cloud-upload-alt text-2xl text-emerald-400 mb-2"></i>
            <p class="text-xs text-gray-400">คลิกหรือลากไฟล์รูปภาพหลักมาวางที่นี่</p>
        </div>
        <div id="new-image-preview" class="grid grid-cols-4 md:grid-cols-6 gap-4 mt-4"></div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($productSalepage) && $productSalepage->images->count() > 0): ?>
            <div class="grid grid-cols-4 md:grid-cols-6 gap-4 mt-6 pt-6 border-t border-gray-700">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $productSalepage->images->sortBy('img_sort'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <div class="relative group aspect-square rounded-lg overflow-hidden border <?php echo e($image->img_sort == 0 ? 'border-emerald-500' : 'border-gray-700'); ?>"
                        id="image-card-<?php echo e($image->img_id); ?>">
                        <img src="<?php echo e(asset('storage/' . $image->img_path)); ?>" class="w-full h-full object-cover">
                        <div
                            class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-center gap-1 p-1">
                            <button type="button" class="btn btn-xs btn-success text-white set-main-image"
                                data-image-id="<?php echo e($image->img_id); ?>">หลัก</button>
                            <button type="button" class="btn btn-xs btn-error text-white delete-image"
                                data-image-id="<?php echo e($image->img_id); ?>">ลบ</button>
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

                const allCards = document.querySelectorAll('#image-list > div');
                allCards.forEach(c => {
                    c.classList.remove('border-emerald-500');
                    c.classList.add('border-gray-700');
                });

                targetCard.classList.remove('border-gray-700');
                targetCard.classList.add('border-emerald-500');

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