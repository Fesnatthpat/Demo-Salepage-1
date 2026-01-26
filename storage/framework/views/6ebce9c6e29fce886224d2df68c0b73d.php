


<script src="//unpkg.com/alpinejs" defer></script>


<?php if($errors->any()): ?>
    <div class="alert alert-error shadow-lg mb-6 text-white">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="font-bold">พบข้อผิดพลาด!</h3>
                <ul class="list-disc pl-5">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>


<div class="card bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex flex-wrap justify-between items-center gap-4">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-info-circle text-primary"></i> ข้อมูลทั่วไป
        </h3>

        <div class="flex items-center gap-4">
            
            <div class="flex items-center gap-3 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                <span class="text-sm font-medium text-gray-600">สถานะการขาย:</span>
                <input type="hidden" name="pd_sp_active" value="0">
                <input type="checkbox" name="pd_sp_active" value="1" class="toggle toggle-success toggle-sm"
                    <?php echo e(old('pd_sp_active', $productSalepage->pd_sp_active ?? 0) == 1 ? 'checked' : ''); ?> />
                <span class="text-xs text-gray-400">(เปิด/ปิด)</span>
            </div>

            
            <div class="flex items-center gap-3 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                <span class="text-sm font-medium text-gray-600">สินค้าแนะนำ:</span>
                <div class="flex items-center gap-4">
                    <label class="label cursor-pointer gap-1 p-0">
                        <input type="radio" name="is_recommended" value="1" class="radio radio-primary radio-xs"
                            <?php echo e(old('is_recommended', $productSalepage->is_recommended ?? 0) == 1 ? 'checked' : ''); ?> />
                        <span class="label-text text-xs">ใช่</span>
                    </label>
                    <label class="label cursor-pointer gap-1 p-0">
                        <input type="radio" name="is_recommended" value="0" class="radio radio-primary radio-xs"
                            <?php echo e(old('is_recommended', $productSalepage->is_recommended ?? 0) == 0 ? 'checked' : ''); ?> />
                        <span class="label-text text-xs">ไม่ใช่</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body p-6">
        
        <?php if(isset($productSalepage->pd_sp_code) || isset($productSalepage->pd_code)): ?>
            <div
                class="mb-6 flex items-center gap-2 text-sm text-blue-600 bg-blue-50 p-3 rounded-lg border border-blue-100">
                <i class="fas fa-tag"></i>
                <span>รหัสสินค้า: <strong><?php echo e($productSalepage->pd_sp_code ?? $productSalepage->pd_code); ?></strong>
                    (สร้างอัตโนมัติ)</span>
            </div>
        <?php endif; ?>

        
        <div class="form-control w-full mb-6">
            <label class="label font-bold text-gray-700">ชื่อสินค้า <span class="text-error">*</span></label>
            <input type="text" name="pd_sp_name"
                class="input input-bordered w-full text-lg h-12 focus:border-primary focus:ring-2 focus:ring-primary/20"
                placeholder="ระบุชื่อสินค้า (เช่น เสื้อยืด Cotton 100%)"
                value="<?php echo e(old('pd_sp_name', $productSalepage->pd_sp_name ?? '')); ?>" />
            <?php $__errorArgs = ['pd_sp_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="text-error text-sm mt-1"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
            
            <div class="md:col-span-4 form-control">
                <label class="label font-bold text-gray-700">ราคาขาย (บาท) <span class="text-error">*</span></label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-gray-400 font-bold">฿</span>
                    <input type="number" step="0.01" name="pd_sp_price"
                        class="input input-bordered w-full pl-10 font-mono text-xl font-bold text-gray-800"
                        placeholder="0.00" value="<?php echo e(old('pd_sp_price', $productSalepage->pd_sp_price ?? '')); ?>" />
                </div>
                <?php $__errorArgs = ['pd_sp_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="text-error text-sm mt-1"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="md:col-span-4 form-control">
                <label class="label font-bold text-gray-700">ส่วนลด (บาท)</label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-gray-400 font-bold">฿</span>
                    <input type="number" step="0.01" name="pd_sp_discount"
                        class="input input-bordered w-full pl-10 font-mono text-xl text-red-500" placeholder="0.00"
                        value="<?php echo e(old('pd_sp_discount', $productSalepage->pd_sp_discount ?? '')); ?>" />
                </div>
                <label class="label py-0 mt-1"><span class="label-text-alt text-gray-400">ใส่ 0 หากไม่มี</span></label>
            </div>

            
            <div class="md:col-span-4 form-control">
                <label class="label font-bold text-gray-700">จำนวนสินค้าในคลัง <span class="text-error">*</span></label>
                <input type="number" name="pd_sp_stock"
                    class="input input-bordered w-full text-lg h-12 focus:border-primary focus:ring-2 focus:ring-primary/20"
                    placeholder="0" value="<?php echo e(old('pd_sp_stock', $productSalepage->pd_sp_stock ?? '')); ?>" />
                <?php $__errorArgs = ['pd_sp_stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="text-error text-sm mt-1"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="md:col-span-4 form-control">
                <label class="label font-bold text-gray-700">ตำแหน่งแสดงผล</label>
                <select name="pd_sp_display_location" class="select select-bordered w-full text-base">
                    <option value="general"
                        <?php echo e(old('pd_sp_display_location', $productSalepage->pd_sp_display_location ?? '') == 'general' ? 'selected' : ''); ?>>
                        📦 สินค้าทั่วไป
                    </option>
                    <option value="homepage"
                        <?php echo e(old('pd_sp_display_location', $productSalepage->pd_sp_display_location ?? '') == 'homepage' ? 'selected' : ''); ?>>
                        ⭐ สินค้าแนะนำ (หน้าแรก)
                    </option>
                </select>
            </div>
        </div>

        
        <div class="form-control w-full">
            <label class="label font-bold text-gray-700">รายละเอียดสินค้า</label>
            <textarea name="pd_sp_details" rows="5" class="textarea textarea-bordered h-32 text-base leading-relaxed"
                placeholder="อธิบายรายละเอียด คุณสมบัติ ขนาด หรือวิธีใช้..."><?php echo e(old('pd_sp_details', $productSalepage->pd_sp_description ?? ($productSalepage->pd_sp_details ?? ''))); ?></textarea>
        </div>
    </div>
</div>


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
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(!isset($productSalepage) || $product->pd_sp_id !== $productSalepage->pd_sp_id): ?>
                        <option value="<?php echo e($product->pd_sp_id); ?>"
                            <?php echo e(in_array($product->pd_sp_id, old('options', isset($productSalepage) && $productSalepage->exists ? $productSalepage->options->pluck('pd_sp_id')->toArray() : [])) ? 'selected' : ''); ?>>
                            <?php echo e($product->pd_sp_name); ?> (<?php echo e($product->pd_sp_code ?? $product->pd_code); ?>)
                        </option>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <label class="label">
                <span class="label-text-alt">ใช้สำหรับจัดกลุ่มสินค้าที่มีลักษณะเดียวกันแต่มีรายละเอียดต่างกัน เช่น
                    เสื้อคนละสี</span>
            </label>
        </div>
    </div>
</div>




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

        <?php if(isset($productSalepage) && $productSalepage->images->count() > 0): ?>
            <div class="divider text-gray-400 text-sm">รูปภาพปัจจุบัน</div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php $__currentLoopData = $productSalepage->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="relative group rounded-lg overflow-hidden border border-gray-200 shadow-sm aspect-square bg-gray-100"
                        id="image-card-<?php echo e($image->img_id); ?>">

                        <img src="<?php echo e(asset('storage/' . $image->img_path)); ?>" class="w-full h-full object-cover">

                        <?php if($image->img_sort == 1): ?>
                            <div class="absolute top-2 right-2 badge badge-primary shadow-md z-10">ปก</div>
                        <?php endif; ?>

                        <div
                            class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-center items-center gap-2 p-2">
                            <label class="btn btn-xs btn-white w-full gap-2">
                                <input type="radio" name="is_primary" value="<?php echo e($image->img_id); ?>"
                                    <?php echo e($image->img_sort == 1 ? 'checked' : ''); ?> class="radio radio-primary radio-xs">
                                ตั้งเป็นปก
                            </label>

                            
                            <button type="button" class="btn btn-xs btn-error w-full text-white delete-image"
                                data-image-id="<?php echo e($image->img_id); ?>">
                                <i class="fas fa-trash"></i> ลบ
                            </button>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>


<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadInput = document.getElementById('images');
            const previewContainer = document.getElementById('new-image-preview');
            const uploadZone = document.getElementById('upload-zone');
            const form = document.querySelector('form');

            // Drag & Drop
            if (uploadZone) {
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
            }

            // Image Preview
            if (uploadInput) {
                uploadInput.addEventListener('change', function() {
                    previewContainer.innerHTML = '';
                    const files = Array.from(this.files);
                    const MAX_SIZE = 64 * 1024 * 1024; // 64MB

                    files.forEach(file => {
                        if (file.size > MAX_SIZE) {
                            alert(`ไฟล์ "${file.name}" ใหญ่เกินไป! (ต้องไม่เกิน 64MB)`);
                            this.value = '';
                            return;
                        }
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const div = document.createElement('div');
                                div.className =
                                    'relative rounded-lg overflow-hidden border border-gray-200 aspect-square shadow-sm';
                                div.innerHTML =
                                    `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                                previewContainer.appendChild(div);
                            }
                            reader.readAsDataURL(file);
                        }
                    });
                });
            }

            // AJAX Delete Image
            document.querySelectorAll('.delete-image').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (confirm('ยืนยันที่จะลบรูปภาพนี้?')) {
                        const id = this.dataset.imageId;
                        const card = document.getElementById(`image-card-${id}`);
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                        this.disabled = true;

                        // ยิง AJAX ไปที่ Route ที่เราสร้าง
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
                                this.innerHTML = originalText;
                                this.disabled = false;
                            }
                        }).catch(e => {
                            console.error(e);
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
<?php $__env->stopPush(); ?>
<?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/products/_form.blade.php ENDPATH**/ ?>