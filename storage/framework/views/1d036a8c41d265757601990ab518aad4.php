<?php $__env->startSection('title', 'ตั้งค่าเว็บไซต์'); ?>

<?php $__env->startSection('page-title'); ?>
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <i class="fas fa-cogs mr-1"></i>
        <span class="text-gray-100 font-medium">การตั้งค่าหน้าหลักเว็บไซต์</span>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-7xl mx-auto">

        
        <?php if($errors->any()): ?>
            <div class="alert alert-error shadow-lg mb-6 bg-red-900/50 border-red-800 text-red-200">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <div>
                        <h3 class="font-bold">พบข้อผิดพลาด!</h3>
                        <ul class="list-disc pl-5 mt-2">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        
        <?php if(session('success')): ?>
            <div class="alert alert-success shadow-lg mb-6 bg-emerald-800/80 border-emerald-700 text-emerald-100">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span><?php echo e(session('success')); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('admin.settings.update')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                
                <div class="lg:col-span-2 space-y-6">
                    <div class="card bg-gray-800 shadow-lg border border-gray-700 rounded-xl">
                        <div class="card-body p-6">
                            <h3 class="text-lg font-bold text-gray-100 mb-4 border-b border-gray-700 pb-3">เนื้อหา Hero Section</h3>
                            
                            
                            <div class="form-control w-full">
                                <label class="label font-bold text-gray-300">Tagline (ข้อความเล็กบนสุด)</label>
                                <input type="text" name="hero_section_tagline" class="input input-bordered w-full bg-gray-700 text-gray-100 placeholder-gray-400 focus:border-emerald-500" value="<?php echo e($settings['hero_section_tagline'] ?? 'ซื้อก่อน ลดก่อน'); ?>" />
                            </div>

                            
                            <div class="form-control w-full">
                                <label class="label font-bold text-gray-300">ส่วนต้นของหัวเรื่อง (เช่น "สมาชิกช้อปสินค้า")</label>
                                <input type="text" name="hero_section_title_prefix" class="input input-bordered w-full bg-gray-700 text-gray-100 placeholder-gray-400 focus:border-emerald-500" value="<?php echo e($settings['hero_section_title_prefix'] ?? 'สมาชิกช้อปสินค้า'); ?>" />
                            </div>

                            
                            <div class="form-control w-full">
                                <label class="label font-bold text-gray-300">ข้อความไฮไลท์ในหัวเรื่อง (เช่น "SALE")</label>
                                <input type="text" name="hero_section_title_highlight" class="input input-bordered w-full bg-gray-700 text-gray-100 placeholder-gray-400 focus:border-emerald-500" value="<?php echo e($settings['hero_section_title_highlight'] ?? 'SALE'); ?>" />
                            </div>

                            
                            <div class="form-control w-full">
                                <label class="label font-bold text-gray-300">ส่วนท้ายของหัวเรื่อง (เช่น "ก่อนใคร")</label>
                                <input type="text" name="hero_section_title_suffix" class="input input-bordered w-full bg-gray-700 text-gray-100 placeholder-gray-400 focus:border-emerald-500" value="<?php echo e($settings['hero_section_title_suffix'] ?? 'ก่อนใคร'); ?>" />
                            </div>

                            
                            <div class="form-control w-full">
                                <label class="label font-bold text-gray-300">คำอธิบายหลัก Hero Section</label>
                                <textarea name="hero_section_description" class="textarea textarea-bordered h-24 bg-gray-700 text-gray-100 placeholder-gray-400 focus:border-emerald-500"><?php echo e($settings['hero_section_description'] ?? 'ลดสูงสุด 50% | ที่ร้านและออนไลน์'); ?></textarea>
                            </div>

                             
                            <div class="form-control w-full">
                                <label class="label font-bold text-gray-300">ข้อความเล็กด้านล่าง (Disclaimer)</label>
                                <textarea name="hero_section_small_text" class="textarea textarea-bordered h-24 bg-gray-700 text-gray-100 placeholder-gray-400 focus:border-emerald-500"><?php echo e($settings['hero_section_small_text'] ?? '*สินค้าและราคาของที่ร้านและออนไลน์อาจแตกต่างกัน ลงชื่อเข้าใช้เพื่อรับสิทธิพิเศษ'); ?></textarea>
                            </div>

                            <h3 class="text-lg font-bold text-gray-100 mb-4 border-b border-gray-700 pb-3 mt-8">การตั้งค่าทั่วไปเว็บไซต์</h3>

                            
                            <div class="form-control w-full">
                                <label class="label font-bold text-gray-300">รายละเอียดเว็บไซต์ (SEO & Footer)</label>
                                <textarea name="site_description" class="textarea textarea-bordered h-24 bg-gray-700 text-gray-100 placeholder-gray-400 focus:border-emerald-500" placeholder="คำอธิบายสั้นๆ เกี่ยวกับเว็บไซต์ของคุณ"><?php echo e($settings['site_description'] ?? ''); ?></textarea>
                            </div>

                            
                            <div class="form-control w-full mt-6">
                                <label class="label font-bold text-gray-300">เมนูนำทาง (JSON format)</label>
                                <textarea name="site_menu" class="textarea textarea-bordered h-48 bg-gray-900 font-mono text-sm text-gray-100 focus:border-emerald-500" placeholder='[&#10;    {"name": "หน้าแรก", "url": "/"},&#10;    {"name": "สินค้าทั้งหมด", "url": "/allproducts"}&#10;]'><?php echo e(old('site_menu', $settings['site_menu'] ?? '')); ?></textarea>
                                <label class="label">
                                    <span class="label-text-alt text-gray-400">ตัวอย่าง: <code class="text-xs">[{"name": "Home", "url": "/"}]</code></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="lg:col-span-1 space-y-6">
                    <div class="card bg-gray-800 shadow-lg border border-gray-700 rounded-xl">
                        <div class="card-body p-6">
                             <h3 class="text-lg font-bold text-gray-100 mb-4 border-b border-gray-700 pb-3">รูปภาพ</h3>
                            
                            
                            <div class="form-control w-full">
                                <label class="label font-bold text-gray-300">โลโก้</label>
                                <?php if(isset($settings['site_logo'])): ?>
                                <div id="logo-preview-container" class="mt-2 mb-4 p-4 bg-gray-900/50 rounded-lg border border-gray-700 text-center relative">
                                    <img src="<?php echo e(asset('storage/' . $settings['site_logo'])); ?>" alt="Site Logo" class="max-h-20 inline-block rounded-lg">
                                    <button type="button" class="btn btn-xs btn-circle btn-error absolute -top-2 -right-2 delete-setting" data-key="site_logo">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <?php endif; ?>
                                <input type="file" name="site_logo" class="file-input file-input-bordered file-input-sm w-full bg-gray-700" />
                                <label class="label">
                                    <span class="label-text-alt text-gray-400">ไฟล์ PNG ที่มีพื้นหลังโปร่งใส</span>
                                </label>
                            </div>

                            
                            <div class="form-control w-full mt-6">
                                <label class="label font-bold text-gray-300">รูปปก (Banner)</label>
                                <?php if(isset($settings['site_cover_image'])): ?>
                                <div id="cover-image-preview-container" class="mt-2 mb-4 aspect-video bg-gray-900/50 rounded-lg border border-gray-700 flex items-center justify-center overflow-hidden relative">
                                    <img src="<?php echo e(asset('storage/' . $settings['site_cover_image'])); ?>" alt="Cover Image" class="w-full h-full object-cover">
                                    <button type="button" class="btn btn-xs btn-circle btn-error absolute top-2 right-2 delete-setting" data-key="site_cover_image">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <?php endif; ?>
                                <input type="file" name="site_cover_image" class="file-input file-input-bordered file-input-sm w-full bg-gray-700" />
                                <label class="label">
                                    <span class="label-text-alt text-gray-400">แนะนำขนาด 1920x1080 pixels</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="fixed bottom-0 left-0 right-0 bg-gray-800 border-t border-gray-700 p-4 z-50 md:static md:bg-transparent md:border-0 md:p-0 md:mt-8">
                <div class="flex justify-end max-w-7xl mx-auto">
                    <button type="submit" class="btn btn-primary bg-emerald-600 hover:bg-emerald-700 border-none text-white px-8 shadow-lg shadow-emerald-900/20">
                        <i class="fas fa-save mr-2"></i> บันทึกการตั้งค่า
                    </button>
                </div>
            </div>
             <div class="h-20 md:hidden"></div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-setting').forEach(button => {
        button.addEventListener('click', function() {
            const key = this.dataset.key;
            if (!key || !confirm(`คุณแน่ใจหรือไม่ว่าต้องการลบรูปภาพนี้?`)) {
                return;
            }

            fetch(`/admin/settings/${key}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the preview container
                    document.getElementById(`${key.replace(/_/g, '-')}-preview-container`).remove();
                } else {
                    alert('เกิดข้อผิดพลาด: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
            });
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/settings/index.blade.php ENDPATH**/ ?>