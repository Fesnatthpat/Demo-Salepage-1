<?php $__env->startSection('title', 'ตั้งค่าเว็บไซต์'); ?>

<?php $__env->startSection('page-title'); ?>
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <i class="fas fa-cogs mr-1"></i>
        <span class="text-gray-100 font-medium">การตั้งค่าหน้าหลักเว็บไซต์</span>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-7xl mx-auto pb-24">

        
        <?php if($errors->any()): ?>
            <div class="alert alert-error shadow-lg mb-6 bg-red-900/50 border-red-800 text-red-200">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="font-bold">พบข้อผิดพลาด!</h3>
                        <ul class="list-disc pl-5 mt-2 text-sm">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        
        <?php if(session('success')): ?>
            <div class="alert alert-success shadow-lg mb-6 bg-emerald-900/50 border-emerald-800 text-emerald-200">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?php echo e(session('success')); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('admin.settings.update')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

                
                <div class="xl:col-span-2 space-y-8">

                    
                    <div class="card bg-gray-800 shadow-xl border border-gray-700 rounded-xl overflow-hidden">
                        <div class="bg-gray-900/50 px-6 py-4 border-b border-gray-700 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-400">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-100">Hero Section (ส่วนหัวแรก)</h3>
                        </div>

                        <div class="card-body p-6 space-y-6">
                            
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-bold text-gray-300">Tagline (ข้อความโปรย)</span>
                                    <span class="label-text-alt text-gray-500">ข้อความเล็กๆ ด้านบนสุด</span>
                                </label>
                                <input type="text" name="hero_section_tagline"
                                    class="input input-bordered w-full bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                    value="<?php echo e($settings['hero_section_tagline'] ?? 'ซื้อก่อน ลดก่อน'); ?>" />
                            </div>

                            
                            <div class="p-4 bg-gray-700/30 rounded-xl border border-gray-700">
                                <label class="label mb-2">
                                    <span class="label-text font-bold text-gray-300">หัวข้อหลัก (Main Headline)</span>
                                    <span class="label-text-alt text-gray-500">ประกอบกันเป็น 3 ส่วน</span>
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="form-control">
                                        <label class="label-text-alt text-gray-400 mb-1">ส่วนหน้า</label>
                                        <input type="text" name="hero_section_title_prefix"
                                            class="input input-bordered w-full bg-gray-700 text-gray-100 text-sm"
                                            value="<?php echo e($settings['hero_section_title_prefix'] ?? 'สมาชิกช้อปสินค้า'); ?>" />
                                    </div>
                                    <div class="form-control">
                                        <label class="label-text-alt text-blue-400 mb-1 font-bold">ส่วนเน้น
                                            (Highlight)</label>
                                        <input type="text" name="hero_section_title_highlight"
                                            class="input input-bordered w-full bg-gray-700 text-blue-300 font-bold text-sm border-blue-500/50"
                                            value="<?php echo e($settings['hero_section_title_highlight'] ?? 'SALE'); ?>" />
                                    </div>
                                    <div class="form-control">
                                        <label class="label-text-alt text-gray-400 mb-1">ส่วนหลัง</label>
                                        <input type="text" name="hero_section_title_suffix"
                                            class="input input-bordered w-full bg-gray-700 text-gray-100 text-sm"
                                            value="<?php echo e($settings['hero_section_title_suffix'] ?? 'ก่อนใคร'); ?>" />
                                    </div>
                                </div>
                                <div class="mt-2 text-xs text-gray-500">
                                    ตัวอย่างที่แสดง: <span><?php echo e($settings['hero_section_title_prefix'] ?? '...'); ?></span>
                                    <span
                                        class="text-blue-400 font-bold"><?php echo e($settings['hero_section_title_highlight'] ?? '...'); ?></span>
                                    <span><?php echo e($settings['hero_section_title_suffix'] ?? '...'); ?></span>
                                </div>
                            </div>

                            
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-bold text-gray-300">คำอธิบายหลัก</span>
                                </label>
                                <textarea name="hero_section_description" rows="3"
                                    class="textarea textarea-bordered w-full bg-gray-700 text-gray-100 text-base leading-relaxed"><?php echo e($settings['hero_section_description'] ?? 'ลดสูงสุด 50% | ที่ร้านและออนไลน์'); ?></textarea>
                            </div>

                            
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-bold text-gray-300">ข้อความหมายเหตุ (Disclaimer)</span>
                                </label>
                                <textarea name="hero_section_small_text" rows="2"
                                    class="textarea textarea-bordered w-full bg-gray-700 text-gray-400 text-sm"><?php echo e($settings['hero_section_small_text'] ?? '*สินค้าและราคาของที่ร้านและออนไลน์อาจแตกต่างกัน'); ?></textarea>
                            </div>
                        </div>
                    </div>

                    
                    <div class="card bg-gray-800 shadow-xl border border-gray-700 rounded-xl overflow-hidden">
                        <div class="bg-gray-900/50 px-6 py-4 border-b border-gray-700 flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-400">
                                <i class="fas fa-globe"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-100">ข้อมูลทั่วไป (General)</h3>
                        </div>

                        <div class="card-body p-6 space-y-6">
                            
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-bold text-gray-300">รายละเอียดเว็บไซต์ (SEO &
                                        Footer)</span>
                                </label>
                                <textarea name="site_description" rows="3" class="textarea textarea-bordered w-full bg-gray-700 text-gray-100"
                                    placeholder="คำอธิบายสั้นๆ เกี่ยวกับเว็บไซต์ของคุณ"><?php echo e($settings['site_description'] ?? ''); ?></textarea>
                            </div>

                            
                            <div class="form-control w-full">
                                <div class="flex justify-between items-end mb-2">
                                    <label class="label p-0">
                                        <span class="label-text font-bold text-gray-300">เมนูนำทาง</span>
                                    </label>
                                </div>

                                
                                <input type="hidden" name="site_menu" id="site_menu_input"
                                    value="<?php echo e(old('site_menu', $settings['site_menu'] ?? '[]')); ?>">

                                <div class="bg-gray-700/30 p-4 rounded-xl border border-gray-600 space-y-4">
                                    
                                    <div
                                        class="grid grid-cols-12 gap-2 text-xs text-gray-400 font-bold uppercase tracking-wider px-1">
                                        <div class="col-span-5">ชื่อเมนู</div>
                                        <div class="col-span-6">ลิงก์ URL</div>
                                        <div class="col-span-1 text-center">ลบ</div>
                                    </div>

                                    
                                    <div id="menu-builder-list" class="space-y-2">
                                        
                                    </div>

                                    
                                    <div id="menu-empty-state"
                                        class="hidden text-center py-4 text-gray-500 text-sm border-2 border-dashed border-gray-600 rounded-lg">
                                        ยังไม่มีรายการเมนู
                                    </div>

                                    
                                    <div
                                        class="grid grid-cols-12 gap-2 items-center pt-3 border-t border-gray-600/50 mt-2">
                                        <div class="col-span-5">
                                            <input type="text" id="new_menu_name"
                                                class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500"
                                                placeholder="เช่น หน้าแรก">
                                        </div>
                                        <div class="col-span-5">
                                            <input type="text" id="new_menu_url"
                                                class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500"
                                                placeholder="เช่น /products">
                                        </div>
                                        <div class="col-span-2">
                                            <button type="button" onclick="addMenuItem()"
                                                class="btn btn-sm btn-success w-full text-white shadow-lg shadow-emerald-900/20">
                                                <i class="fas fa-plus"></i> <span
                                                    class="hidden sm:inline ml-1">เพิ่ม</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <label class="label">
                                    <span class="label-text-alt text-gray-500">ระบบจะแปลงเป็น JSON
                                        โดยอัตโนมัติเมื่อกดบันทึก</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="xl:col-span-1 space-y-8">
                    <div class="card bg-gray-800 shadow-xl border border-gray-700 rounded-xl overflow-hidden sticky top-8">
                        <div class="bg-gray-900/50 px-6 py-4 border-b border-gray-700 flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                                <i class="fas fa-images"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-100">รูปภาพเว็บไซต์</h3>
                        </div>

                        <div class="card-body p-6 space-y-8">

                            
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-bold text-gray-300">โลโก้ (Logo)</span>
                                    <span class="label-text-alt text-gray-500">PNG พื้นหลังใส</span>
                                </label>

                                <div
                                    class="bg-gray-900/50 rounded-xl border border-gray-700 p-4 text-center group relative overflow-hidden transition-all hover:border-gray-600">
                                    <?php if(isset($settings['site_logo'])): ?>
                                        <div id="logo-preview-wrapper">
                                            <img src="<?php echo e(asset('storage/' . $settings['site_logo'])); ?>" alt="Site Logo"
                                                class="h-16 mx-auto object-contain">
                                            <button type="button"
                                                class="btn btn-xs btn-circle btn-error absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity delete-setting"
                                                data-key="site_logo" title="ลบรูปภาพ">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="h-16 flex items-center justify-center text-gray-600">
                                            <i class="fas fa-image text-3xl"></i>
                                            <span class="ml-2 text-sm">ยังไม่มีโลโก้</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="site_logo"
                                    class="file-input file-input-bordered file-input-sm w-full bg-gray-700 mt-3 text-gray-300"
                                    accept="image/png, image/jpeg, image/webp" />
                            </div>

                            <div class="divider bg-gray-700 h-px my-0"></div>

                            
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-bold text-gray-300">รูปปก (Banner)</span>
                                    <span class="label-text-alt text-gray-500">แนะนำ 1920x1080px</span>
                                </label>

                                <div
                                    class="bg-gray-900/50 rounded-xl border border-gray-700 p-1 group relative overflow-hidden transition-all hover:border-gray-600">
                                    <?php if(isset($settings['site_cover_image'])): ?>
                                        <div id="cover-image-preview-wrapper"
                                            class="relative aspect-video rounded-lg overflow-hidden">
                                            <img src="<?php echo e(asset('storage/' . $settings['site_cover_image'])); ?>"
                                                alt="Cover Image" class="w-full h-full object-cover">
                                            <div
                                                class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button type="button" class="btn btn-sm btn-error delete-setting"
                                                    data-key="site_cover_image">
                                                    <i class="fas fa-trash mr-1"></i> ลบรูป
                                                </button>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div
                                            class="aspect-video rounded-lg bg-gray-800 flex flex-col items-center justify-center text-gray-600 border border-dashed border-gray-600">
                                            <i class="far fa-image text-4xl mb-2"></i>
                                            <span class="text-sm">ยังไม่มีรูปปก</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="site_cover_image"
                                    class="file-input file-input-bordered file-input-sm w-full bg-gray-700 mt-3 text-gray-300"
                                    accept="image/*" />
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            
            <div
                class="fixed bottom-0 left-0 right-0 bg-gray-800/95 backdrop-blur-sm border-t border-gray-700 p-4 z-50 transition-transform duration-300 shadow-2xl">
                <div class="max-w-7xl mx-auto flex justify-between items-center">
                    <div class="text-sm text-gray-400 hidden sm:block">
                        <i class="fas fa-info-circle mr-1"></i> การเปลี่ยนแปลงจะมีผลทันทีที่หน้าเว็บไซต์หลัก
                    </div>
                    <div class="flex gap-3">
                        <a href="<?php echo e(route('home')); ?>" target="_blank"
                            class="btn btn-ghost text-gray-300 hover:text-white">
                            <i class="fas fa-external-link-alt mr-2"></i> ดูหน้าเว็บ
                        </a>
                        <button type="submit"
                            class="btn btn-primary bg-emerald-600 hover:bg-emerald-700 border-none text-white px-8 shadow-lg shadow-emerald-900/20">
                            <i class="fas fa-save mr-2"></i> บันทึกการตั้งค่า
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        // --- Menu Builder Logic ---
        let menuItems = [];

        // Initialize Menu
        function initMenuBuilder() {
            const input = document.getElementById('site_menu_input');
            try {
                // Check if input has value, otherwise default to empty array
                const rawValue = input.value.trim();
                menuItems = rawValue ? JSON.parse(rawValue) : [];
            } catch (e) {
                console.error('Invalid JSON for menu:', e);
                menuItems = [];
            }
            renderMenuBuilder();
        }

        // Render List
        function renderMenuBuilder() {
            const container = document.getElementById('menu-builder-list');
            const emptyState = document.getElementById('menu-empty-state');
            container.innerHTML = '';

            if (menuItems.length === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
                menuItems.forEach((item, index) => {
                    const row = document.createElement('div');
                    row.className =
                        'grid grid-cols-12 gap-2 items-center bg-gray-800/50 p-2 rounded-lg border border-gray-700/50 hover:border-gray-600 transition-colors';
                    row.innerHTML = `
                    <div class="col-span-5">
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-gray-200 focus:ring-0 px-0 font-medium" 
                            value="${item.name}" 
                            onchange="updateMenuItem(${index}, 'name', this.value)" placeholder="ชื่อเมนู">
                    </div>
                    <div class="col-span-6 border-l border-gray-700 pl-2">
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-blue-400 focus:ring-0 px-0 font-mono" 
                            value="${item.url}" 
                            onchange="updateMenuItem(${index}, 'url', this.value)" placeholder="URL">
                    </div>
                    <div class="col-span-1 text-center">
                        <button type="button" onclick="removeMenuItem(${index})" class="text-gray-500 hover:text-red-400 transition-colors p-1">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `;
                    container.appendChild(row);
                });
            }
            updateHiddenInput();
        }

        // Add Item
        window.addMenuItem = function() {
            const nameInput = document.getElementById('new_menu_name');
            const urlInput = document.getElementById('new_menu_url');
            const name = nameInput.value.trim();
            const url = urlInput.value.trim();

            if (!name || !url) {
                alert('กรุณากรอกทั้ง "ชื่อเมนู" และ "ลิงก์ URL"');
                return;
            }

            menuItems.push({
                name,
                url
            });

            // Clear inputs and focus on name
            nameInput.value = '';
            urlInput.value = '';
            nameInput.focus();

            renderMenuBuilder();
        }

        // Remove Item
        window.removeMenuItem = function(index) {
            if (confirm('ต้องการลบเมนูนี้ใช่หรือไม่?')) {
                menuItems.splice(index, 1);
                renderMenuBuilder();
            }
        }

        // Update Item
        window.updateMenuItem = function(index, field, value) {
            menuItems[index][field] = value;
            updateHiddenInput();
        }

        // Sync to Hidden Input
        function updateHiddenInput() {
            const input = document.getElementById('site_menu_input');
            input.value = JSON.stringify(menuItems);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Start Menu Builder
            initMenuBuilder();

            // --- Image Deletion Logic ---
            document.querySelectorAll('.delete-setting').forEach(button => {
                button.addEventListener('click', function() {
                    const key = this.dataset.key;
                    if (!key || !confirm(
                            `คุณแน่ใจหรือไม่ว่าต้องการลบรูปภาพนี้? การกระทำนี้ไม่สามารถย้อนกลับได้`
                            )) {
                        return;
                    }

                    // เปลี่ยนปุ่มเป็น Loading
                    const originalContent = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    this.disabled = true;

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
                                window.location.reload();
                            } else {
                                alert('เกิดข้อผิดพลาด: ' + data.message);
                                this.innerHTML = originalContent;
                                this.disabled = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                            this.innerHTML = originalContent;
                            this.disabled = false;
                        });
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/settings/index.blade.php ENDPATH**/ ?>