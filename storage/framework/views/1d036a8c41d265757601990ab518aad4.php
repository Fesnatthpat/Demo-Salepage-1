<?php $__env->startSection('title', 'ตั้งค่าเว็บไซต์'); ?>

<?php $__env->startSection('page-title'); ?>
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <i class="fas fa-cogs mr-1"></i>
        <span class="text-gray-100 font-medium">การตั้งค่าหน้าหลักเว็บไซต์</span>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-7xl mx-auto pb-24">

        
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
                        <ul class="list-disc pl-5 mt-2 text-sm">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <li><?php echo e($error); ?></li>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
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
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

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
                            <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center text-red-400">
                                <i class="fas fa-images"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-100">Hero Section Slider (สไลด์หลัก)</h3>
                        </div>

                        <div class="card-body p-6 space-y-6">
                            
                            <input type="hidden" name="hero_slider_items" id="hero_slider_items_input"
                                value="<?php echo e(old('hero_slider_items', $settings['hero_slider_items'] ?? '[]')); ?>">

                            <div class="bg-gray-700/30 p-4 rounded-xl border border-gray-600 space-y-4">
                                
                                <div class="grid grid-cols-12 gap-2 text-xs text-gray-400 font-bold uppercase tracking-wider px-1">
                                    <div class="col-span-3">รูปภาพ (URL)</div>
                                    <div class="col-span-3">หัวข้อ</div>
                                    <div class="col-span-5">คำอธิบาย</div>
                                    <div class="col-span-1 text-center">ลบ</div>
                                </div>

                                
                                <div id="hero-slider-list" class="space-y-2">
                                    
                                </div>

                                
                                <div id="hero-slider-empty-state"
                                    class="hidden text-center py-4 text-gray-500 text-sm border-2 border-dashed border-gray-600 rounded-lg">
                                    ยังไม่มีสไลด์
                                </div>

                                
                                <div class="grid grid-cols-12 gap-2 items-center pt-3 border-t border-gray-600/50 mt-2">
                                    <div class="col-span-3">
                                        <input type="text" id="new_hero_slide_image"
                                            class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500"
                                            placeholder="URL รูปภาพ">
                                    </div>
                                    <div class="col-span-3">
                                        <input type="text" id="new_hero_slide_title"
                                            class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500"
                                            placeholder="หัวข้อ">
                                    </div>
                                    <div class="col-span-5">
                                        <input type="text" id="new_hero_slide_description"
                                            class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500"
                                            placeholder="คำอธิบาย">
                                    </div>
                                    <div class="col-span-1">
                                        <button type="button" onclick="addHeroSlideItem()"
                                            class="btn btn-sm btn-success w-full text-white shadow-lg shadow-emerald-900/20">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-info-circle mr-1"></i> สำหรับรูปภาพ กรุณาใช้ URL รูปภาพ
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="card bg-gray-800 shadow-xl border border-gray-700 rounded-xl overflow-hidden">
                        <div class="bg-gray-900/50 px-6 py-4 border-b border-gray-700 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-yellow-500/20 flex items-center justify-center text-yellow-400">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-100">ข้อมูลแพ้อาหาร</h3>
                        </div>

                        <div class="card-body p-6 space-y-6">
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-bold text-gray-300">ข้อความข้อมูลแพ้อาหาร</span>
                                    <span class="label-text-alt text-gray-500">แสดงในหน้าสินค้า</span>
                                </label>
                                <textarea name="allergy_info_content" rows="5"
                                    class="textarea textarea-bordered w-full bg-gray-700 text-gray-100 text-base leading-relaxed"><?php echo e($settings['allergy_info_content'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    
                    <div class="card bg-gray-800 shadow-xl border border-gray-700 rounded-xl overflow-hidden">
                        <div class="bg-gray-900/50 px-6 py-4 border-b border-gray-700 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center text-green-400">
                                <i class="fas fa-list-ul"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-100">6 Reasons Section</h3>
                        </div>

                        <div class="card-body p-6 space-y-6">
                            
                            <input type="hidden" name="reasons_section_items" id="reasons_section_items_input"
                                value="<?php echo e(old('reasons_section_items', $settings['reasons_section_items'] ?? '[]')); ?>">

                            <div class="bg-gray-700/30 p-4 rounded-xl border border-gray-600 space-y-4">
                                
                                <div class="grid grid-cols-12 gap-2 text-xs text-gray-400 font-bold uppercase tracking-wider px-1">
                                    <div class="col-span-3">รูปภาพ (URL)</div>
                                    <div class="col-span-4">หัวข้อ</div>
                                    <div class="col-span-4">คำอธิบาย</div>
                                    <div class="col-span-1 text-center">ลบ</div>
                                </div>

                                
                                <div id="reasons-list" class="space-y-2">
                                    
                                </div>

                                
                                <div id="reasons-empty-state"
                                    class="hidden text-center py-4 text-gray-500 text-sm border-2 border-dashed border-gray-600 rounded-lg">
                                    ยังไม่มีเหตุผล
                                </div>

                                
                                <div class="grid grid-cols-12 gap-2 items-center pt-3 border-t border-gray-600/50 mt-2">
                                    <div class="col-span-3">
                                        <input type="text" id="new_reason_image"
                                            class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500"
                                            placeholder="URL รูปภาพ">
                                    </div>
                                    <div class="col-span-4">
                                        <input type="text" id="new_reason_title"
                                            class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500"
                                            placeholder="หัวข้อ">
                                    </div>
                                    <div class="col-span-4">
                                        <input type="text" id="new_reason_description"
                                            class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500"
                                            placeholder="คำอธิบาย">
                                    </div>
                                    <div class="col-span-1">
                                        <button type="button" onclick="addReasonItem()"
                                            class="btn btn-sm btn-success w-full text-white shadow-lg shadow-emerald-900/20">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-info-circle mr-1"></i> สำหรับรูปภาพ กรุณาใช้ URL รูปภาพ
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="card bg-gray-800 shadow-xl border border-gray-700 rounded-xl overflow-hidden">
                        <div class="bg-gray-900/50 px-6 py-4 border-b border-gray-700 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-orange-500/20 flex items-center justify-center text-orange-400">
                                <i class="fas fa-images"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-100">สไลด์ตัวที่ 2</h3>
                        </div>

                        <div class="card-body p-6 space-y-6">
                            
                            <input type="hidden" name="second_slider_items" id="second_slider_items_input"
                                value="<?php echo e(old('second_slider_items', $settings['second_slider_items'] ?? '[]')); ?>">

                            <div class="bg-gray-700/30 p-4 rounded-xl border border-gray-600 space-y-4">
                                
                                <div class="grid grid-cols-12 gap-2 text-xs text-gray-400 font-bold uppercase tracking-wider px-1">
                                    <div class="col-span-3">รูปภาพ (URL)</div>
                                    <div class="col-span-3">หัวข้อ</div>
                                    <div class="col-span-4">คำอธิบาย</div>
                                    <div class="col-span-1 text-center">ลิงก์</div>
                                    <div class="col-span-1 text-center">ลบ</div>
                                </div>

                                
                                <div id="second-slider-list" class="space-y-2">
                                    
                                </div>

                                
                                <div id="second-slider-empty-state"
                                    class="hidden text-center py-4 text-gray-500 text-sm border-2 border-dashed border-gray-600 rounded-lg">
                                    ยังไม่มีสไลด์
                                </div>

                                
                                <div class="grid grid-cols-12 gap-2 items-center pt-3 border-t border-gray-600/50 mt-2">
                                    <div class="col-span-3">
                                        <input type="text" id="new_second_slide_image"
                                            class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500"
                                            placeholder="URL รูปภาพ">
                                    </div>
                                    <div class="col-span-3">
                                        <input type="text" id="new_second_slide_title"
                                            class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500"
                                            placeholder="หัวข้อ">
                                    </div>
                                    <div class="col-span-4">
                                        <input type="text" id="new_second_slide_description"
                                            class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500"
                                            placeholder="คำอธิบาย">
                                    </div>
                                    <div class="col-span-1">
                                        <input type="text" id="new_second_slide_link"
                                            class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500"
                                            placeholder="ลิงก์">
                                    </div>
                                    <div class="col-span-1">
                                        <button type="button" onclick="addSecondSlideItem()"
                                            class="btn btn-sm btn-success w-full text-white shadow-lg shadow-emerald-900/20">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-info-circle mr-1"></i> สำหรับรูปภาพ กรุณาใช้ URL รูปภาพ
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="card bg-gray-800 shadow-xl border border-gray-700 rounded-xl overflow-hidden">
                        <div class="bg-gray-900/50 px-6 py-4 border-b border-gray-700 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-cyan-500/20 flex items-center justify-center text-cyan-400">
                                <i class="fas fa-th-list"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-100">Category Menu Section (Sticky Top)</h3>
                        </div>

                        <div class="card-body p-6 space-y-6">
                            
                            <input type="hidden" name="category_menu_items" id="category_menu_items_input"
                                value="<?php echo e(old('category_menu_items', $settings['category_menu_items'] ?? '[]')); ?>">

                            <div class="bg-gray-700/30 p-4 rounded-xl border border-gray-600 space-y-4">
                                
                                <div class="grid grid-cols-12 gap-2 text-xs text-gray-400 font-bold uppercase tracking-wider px-1">
                                    <div class="col-span-3">รูปภาพ (URL)</div>
                                    <div class="col-span-5">ชื่อหมวดหมู่</div>
                                    <div class="col-span-3">ลิงก์</div>
                                    <div class="col-span-1 text-center">ลบ</div>
                                </div>

                                
                                <div id="category-menu-list" class="space-y-2">
                                    
                                </div>

                                
                                <div id="category-menu-empty-state"
                                    class="hidden text-center py-4 text-gray-500 text-sm border-2 border-dashed border-gray-600 rounded-lg">
                                    ยังไม่มีรายการหมวดหมู่
                                </div>

                                
                                <div class="grid grid-cols-12 gap-2 items-center pt-3 border-t border-gray-600/50 mt-2">
                                    <div class="col-span-3">
                                        <input type="text" id="new_category_image"
                                            class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500"
                                            placeholder="URL รูปภาพ">
                                    </div>
                                    <div class="col-span-5">
                                        <input type="text" id="new_category_name"
                                            class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500"
                                            placeholder="ชื่อหมวดหมู่">
                                    </div>
                                    <div class="col-span-3">
                                        <input type="text" id="new_category_link"
                                            class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500"
                                            placeholder="ลิงก์ (เช่น /allproducts?category=...)">
                                    </div>
                                    <div class="col-span-1">
                                        <button type="button" onclick="addCategoryMenuItem()"
                                            class="btn btn-sm btn-success w-full text-white shadow-lg shadow-emerald-900/20">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-info-circle mr-1"></i> สำหรับรูปภาพ กรุณาใช้ URL รูปภาพ. สำหรับลิงก์ ใช้ `allproducts?category=[หมวดหมู่]`
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="card bg-gray-800 shadow-xl border border-gray-700 rounded-xl overflow-hidden">
                        <div class="bg-gray-900/50 px-6 py-4 border-b border-gray-700 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-pink-500/20 flex items-center justify-center text-pink-400">
                                <i class="fas fa-images"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-100">Small Slider (หน้าสินค้าทั้งหมด)</h3>
                        </div>

                        <div class="card-body p-6 space-y-6">
                            
                            <input type="hidden" name="small_slider_allproducts_items" id="small_slider_allproducts_items_input"
                                value="<?php echo e(old('small_slider_allproducts_items', $settings['small_slider_allproducts_items'] ?? '[]')); ?>">

                            <div class="bg-gray-700/30 p-4 rounded-xl border border-gray-600 space-y-4">
                                
                                <div class="grid grid-cols-12 gap-2 text-xs text-gray-400 font-bold uppercase tracking-wider px-1">
                                    <div class="col-span-3">รูปภาพ (URL)</div>
                                    <div class="col-span-5">หัวข้อ</div>
                                    <div class="col-span-3">ลิงก์</div>
                                    <div class="col-span-1 text-center">ลบ</div>
                                </div>

                                
                                <div id="small-slider-allproducts-list" class="space-y-2">
                                    
                                </div>

                                
                                <div id="small-slider-allproducts-empty-state"
                                    class="hidden text-center py-4 text-gray-500 text-sm border-2 border-dashed border-gray-600 rounded-lg">
                                    ยังไม่มีสไลด์
                                </div>

                                
                                <div class="grid grid-cols-12 gap-2 items-center pt-3 border-t border-gray-600/50 mt-2">
                                    <div class="col-span-3">
                                        <input type="text" id="new_small_slider_allproducts_image"
                                            class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500"
                                            placeholder="URL รูปภาพ">
                                    </div>
                                    <div class="col-span-5">
                                        <input type="text" id="new_small_slider_allproducts_title"
                                            class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500"
                                            placeholder="หัวข้อ">
                                    </div>
                                    <div class="col-span-3">
                                        <input type="text" id="new_small_slider_allproducts_link"
                                            class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500"
                                            placeholder="ลิงก์">
                                    </div>
                                    <div class="col-span-1">
                                        <button type="button" onclick="addSmallSliderAllproductsItem()"
                                            class="btn btn-sm btn-success w-full text-white shadow-lg shadow-emerald-900/20">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-info-circle mr-1"></i> สำหรับรูปภาพ กรุณาใช้ URL รูปภาพ
                                </div>
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
                                        <span class="label-text font-bold text-gray-300">Service Bar Items</span>
                                    </label>
                                </div>

                                
                                <input type="hidden" name="service_bar_items" id="service_bar_items_input"
                                    value="<?php echo e(old('service_bar_items', $settings['service_bar_items'] ?? '[]')); ?>">

                                <div class="bg-gray-700/30 p-4 rounded-xl border border-gray-600 space-y-4">
                                    
                                    <div
                                        class="grid grid-cols-12 gap-2 text-xs text-gray-400 font-bold uppercase tracking-wider px-1">
                                        <div class="col-span-4">Icon (FontAwesome Class)</div>
                                        <div class="col-span-7">ข้อความ (Text)</div>
                                        <div class="col-span-1 text-center">ลบ</div>
                                    </div>

                                    
                                    <div id="service-builder-list" class="space-y-2">
                                        
                                    </div>

                                    
                                    <div id="service-empty-state"
                                        class="hidden text-center py-4 text-gray-500 text-sm border-2 border-dashed border-gray-600 rounded-lg">
                                        ยังไม่มีรายการ Service Bar
                                    </div>

                                    
                                    <div
                                        class="grid grid-cols-12 gap-2 items-center pt-3 border-t border-gray-600/50 mt-2">
                                        <div class="col-span-4">
                                            <div class="relative">
                                                <input type="text" id="new_service_icon"
                                                    class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500 pl-8"
                                                    placeholder="fa-check-circle">
                                                <i class="fas fa-icons absolute left-2.5 top-2 text-gray-500"></i>
                                            </div>
                                        </div>
                                        <div class="col-span-6">
                                            <input type="text" id="new_service_text"
                                                class="input input-sm input-bordered w-full bg-gray-800 text-white placeholder-gray-500"
                                                placeholder="เช่น สินค้าแท้ 100%">
                                        </div>
                                        <div class="col-span-2">
                                            <button type="button" onclick="addServiceItem()"
                                                class="btn btn-sm btn-success w-full text-white shadow-lg shadow-emerald-900/20">
                                                <i class="fas fa-plus"></i> <span
                                                    class="hidden sm:inline ml-1">เพิ่ม</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-2">
                                        <i class="fas fa-info-circle mr-1"></i> ใช้ชื่อคลาสจาก <a
                                            href="https://fontawesome.com/v5/search" target="_blank"
                                            class="text-blue-400 hover:underline">FontAwesome 5</a> เช่น <code>fas
                                            fa-shipping-fast</code>, <code>fas fa-shield-alt</code>
                                    </div>
                                </div>
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
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($settings['site_logo'])): ?>
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
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($settings['site_cover_image'])): ?>
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
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
        // --- Service Bar Builder Logic ---
        let serviceItems = [];

        // Initialize
        function initServiceBuilder() {
            const input = document.getElementById('service_bar_items_input');
            try {
                const rawValue = input.value.trim();
                // Handle potentially malformed JSON from previous manual edits
                serviceItems = rawValue ? JSON.parse(rawValue) : [];

                // Convert old format (SVG string) to new format (Icon Class) if necessary
                // This is a basic migration, assumes user will fix manually if complex
                serviceItems = serviceItems.map(item => {
                    if (item.icon && item.icon.includes('<svg')) {
                        return {
                            icon: 'fas fa-check-circle',
                            text: item.text
                        }; // Default icon for migration
                    }
                    return item;
                });

            } catch (e) {
                console.error('Invalid JSON for service bar:', e);
                serviceItems = [];
            }
            renderServiceBuilder();
        }

        // Render List
        function renderServiceBuilder() {
            const container = document.getElementById('service-builder-list');
            const emptyState = document.getElementById('service-empty-state');
            container.innerHTML = '';

            if (serviceItems.length === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
                serviceItems.forEach((item, index) => {
                    const row = document.createElement('div');
                    row.className =
                        'grid grid-cols-12 gap-2 items-center bg-gray-800/50 p-2 rounded-lg border border-gray-700/50 hover:border-gray-600 transition-colors';

                    // Icon Preview logic
                    let iconPreview = `<i class="${item.icon} text-emerald-400"></i>`;

                    row.innerHTML = `
                    <div class="col-span-4 flex items-center gap-2">
                        <div class="w-6 h-6 flex items-center justify-center bg-gray-700 rounded">${iconPreview}</div>
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-blue-300 focus:ring-0 px-0 font-mono" 
                            value="${item.icon}" 
                            onchange="updateServiceItem(${index}, 'icon', this.value)" placeholder="Icon Class">
                    </div>
                    <div class="col-span-7 border-l border-gray-700 pl-2">
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-gray-200 focus:ring-0 px-0" 
                            value="${item.text}" 
                            onchange="updateServiceItem(${index}, 'text', this.value)" placeholder="Text">
                    </div>
                    <div class="col-span-1 text-center">
                        <button type="button" onclick="removeServiceItem(${index})" class="text-gray-500 hover:text-red-400 transition-colors p-1">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `;
                    container.appendChild(row);
                });
            }
            updateServiceHiddenInput();
        }

        // Add Item
        window.addServiceItem = function() {
            const iconInput = document.getElementById('new_service_icon');
            const textInput = document.getElementById('new_service_text');

            // Add "fas " prefix if user forgets it but types something like "fa-check"
            let icon = iconInput.value.trim();
            if (icon && !icon.startsWith('fas ') && !icon.startsWith('far ') && !icon.startsWith('fab ')) {
                icon = 'fas ' + icon;
            }

            const text = textInput.value.trim();

            if (!text) {
                alert('กรุณากรอกข้อความ (Text)');
                return;
            }

            serviceItems.push({
                icon: icon || 'fas fa-check-circle', // Default icon if empty
                text: text
            });

            // Clear inputs
            iconInput.value = '';
            textInput.value = '';
            iconInput.focus();

            renderServiceBuilder();
        }

        // Remove Item
        window.removeServiceItem = function(index) {
            if (confirm('ต้องการลบรายการนี้ใช่หรือไม่?')) {
                serviceItems.splice(index, 1);
                renderServiceBuilder();
            }
        }

        // Update Item
        window.updateServiceItem = function(index, field, value) {
            serviceItems[index][field] = value;
            renderServiceBuilder(); // Re-render to update icon preview
        }

        // Sync to Hidden Input
        function updateServiceHiddenInput() {
            const input = document.getElementById('service_bar_items_input');
            input.value = JSON.stringify(serviceItems);
        }

    // --- Hero Slider Builder Logic ---
    let heroSliderItems = [];

    // Initialize
    function initHeroSliderBuilder() {
        const input = document.getElementById('hero_slider_items_input');
        try {
            heroSliderItems = input.value.trim() ? JSON.parse(input.value.trim()) : [];
        } catch (e) {
            console.error('Invalid JSON for hero slider:', e);
            heroSliderItems = [];
        }
        renderHeroSliderBuilder();
    }

    // Render List
    function renderHeroSliderBuilder() {
        const container = document.getElementById('hero-slider-list');
        const emptyState = document.getElementById('hero-slider-empty-state');
        container.innerHTML = '';

        if (heroSliderItems.length === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
            heroSliderItems.forEach((item, index) => {
                const row = document.createElement('div');
                row.className =
                    'grid grid-cols-12 gap-2 items-center bg-gray-800/50 p-2 rounded-lg border border-gray-700/50 hover:border-gray-600 transition-colors';

                // Image preview (simple text for URL for now)
                let imagePreview = item.image ? `<img src="${item.image}" class="h-8 w-8 object-cover rounded" onerror="this.src='https://via.placeholder.com/80?text=No%20Image'">` : `<i class="fas fa-image text-gray-500"></i>`;

                row.innerHTML = `
                    <div class="col-span-3 flex items-center gap-2">
                        <div class="w-8 h-8 flex items-center justify-center bg-gray-700 rounded">${imagePreview}</div>
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-gray-200 focus:ring-0 px-0"
                            value="${item.image || ''}"
                            onchange="updateHeroSlideItem(${index}, 'image', this.value)" placeholder="URL รูปภาพ">
                    </div>
                    <div class="col-span-3 border-l border-gray-700 pl-2">
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-gray-200 focus:ring-0 px-0"
                            value="${item.title || ''}"
                            onchange="updateHeroSlideItem(${index}, 'title', this.value)" placeholder="หัวข้อ">
                    </div>
                    <div class="col-span-4 border-l border-gray-700 pl-2">
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-gray-200 focus:ring-0 px-0"
                            value="${item.description || ''}"
                            onchange="updateHeroSlideItem(${index}, 'description', this.value)" placeholder="คำอธิบาย">
                    </div>
                    <div class="col-span-1 text-center">
                        <button type="button" onclick="removeHeroSlideItem(${index})" class="text-gray-500 hover:text-red-400 transition-colors p-1">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `;
                container.appendChild(row);
            });
        }
        updateHeroSliderHiddenInput();
    }

    // Add Item
    window.addHeroSlideItem = function() {
        const imageInput = document.getElementById('new_hero_slide_image');
        const titleInput = document.getElementById('new_hero_slide_title');
        const descriptionInput = document.getElementById('new_hero_slide_description');

        const image = imageInput.value.trim();
        const title = titleInput.value.trim();
        const description = descriptionInput.value.trim();

        if (!image && !title && !description) {
            alert('กรุณากรอกข้อมูลอย่างน้อยหนึ่งช่องสำหรับสไลด์');
            return;
        }

        heroSliderItems.push({
            image: image,
            title: title,
            description: description,
            link: '#' // Default link
        });

        // Clear inputs
        imageInput.value = '';
        titleInput.value = '';
        descriptionInput.value = '';
        imageInput.focus();

        renderHeroSliderBuilder();
    }

    // Remove Item
    window.removeHeroSlideItem = function(index) {
        if (confirm('ต้องการลบรายการนี้ใช่หรือไม่?')) {
            heroSliderItems.splice(index, 1);
            renderHeroSliderBuilder();
        }
    }

    // Update Item
    window.updateHeroSlideItem = function(index, field, value) {
        heroSliderItems[index][field] = value;
        renderHeroSliderBuilder(); // Re-render to update image preview
    }

    // Sync to Hidden Input
    function updateHeroSliderHiddenInput() {
        const input = document.getElementById('hero_slider_items_input');
        input.value = JSON.stringify(heroSliderItems);
    }

    // --- Reasons Section Builder Logic ---
    let reasonsSectionItems = [];

    // Initialize
    function initReasonsSectionBuilder() {
        const input = document.getElementById('reasons_section_items_input');
        try {
            reasonsSectionItems = input.value.trim() ? JSON.parse(input.value.trim()) : [];
        } catch (e) {
            console.error('Invalid JSON for reasons section:', e);
            reasonsSectionItems = [];
        }
        renderReasonsSectionBuilder();
    }

    // Render List
    function renderReasonsSectionBuilder() {
        const container = document.getElementById('reasons-list');
        const emptyState = document.getElementById('reasons-empty-state');
        container.innerHTML = '';

        if (reasonsSectionItems.length === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
            reasonsSectionItems.forEach((item, index) => {
                const row = document.createElement('div');
                row.className =
                    'grid grid-cols-12 gap-2 items-center bg-gray-800/50 p-2 rounded-lg border border-gray-700/50 hover:border-gray-600 transition-colors';

                // Image preview (simple text for URL for now)
                let imagePreview = item.image ? `<img src="${item.image}" class="h-8 w-8 object-cover rounded" onerror="this.src='https://via.placeholder.com/80?text=No%20Image'">` : `<i class="fas fa-image text-gray-500"></i>`;

                row.innerHTML = `
                    <div class="col-span-3 flex items-center gap-2">
                        <div class="w-8 h-8 flex items-center justify-center bg-gray-700 rounded">${imagePreview}</div>
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-gray-200 focus:ring-0 px-0"
                            value="${item.image || ''}"
                            onchange="updateReasonItem(${index}, 'image', this.value)" placeholder="URL รูปภาพ">
                    </div>
                    <div class="col-span-4 border-l border-gray-700 pl-2">
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-gray-200 focus:ring-0 px-0"
                            value="${item.title || ''}"
                            onchange="updateReasonItem(${index}, 'title', this.value)" placeholder="หัวข้อ">
                    </div>
                    <div class="col-span-4 border-l border-gray-700 pl-2">
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-gray-200 focus:ring-0 px-0"
                            value="${item.description || ''}"
                            onchange="updateReasonItem(${index}, 'description', this.value)" placeholder="คำอธิบาย">
                    </div>
                    <div class="col-span-1 text-center">
                        <button type="button" onclick="removeReasonItem(${index})" class="text-gray-500 hover:text-red-400 transition-colors p-1">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `;
                container.appendChild(row);
            });
        }
        updateReasonsSectionHiddenInput();
    }

    // Add Item
    window.addReasonItem = function() {
        const imageInput = document.getElementById('new_reason_image');
        const titleInput = document.getElementById('new_reason_title');
        const descriptionInput = document.getElementById('new_reason_description');

        const image = imageInput.value.trim();
        const title = titleInput.value.trim();
        const description = descriptionInput.value.trim();

        if (!image && !title && !description) {
            alert('กรุณากรอกข้อมูลอย่างน้อยหนึ่งช่องสำหรับเหตุผล');
            return;
        }

        reasonsSectionItems.push({
            image: image,
            title: title,
            description: description,
        });

        // Clear inputs
        imageInput.value = '';
        titleInput.value = '';
        descriptionInput.value = '';
        imageInput.focus();

        renderReasonsSectionBuilder();
    }

    // Remove Item
    window.removeReasonItem = function(index) {
        if (confirm('ต้องการลบรายการนี้ใช่หรือไม่?')) {
            reasonsSectionItems.splice(index, 1);
            renderReasonsSectionBuilder();
        }
    }

    // Update Item
    window.updateReasonItem = function(index, field, value) {
        reasonsSectionItems[index][field] = value;
        renderReasonsSectionBuilder();
    }

    // Sync to Hidden Input
    function updateReasonsSectionHiddenInput() {
        const input = document.getElementById('reasons_section_items_input');
        input.value = JSON.stringify(reasonsSectionItems);
    }

    // --- Second Slider Builder Logic ---
    let secondSliderItems = [];

    // Initialize
    function initSecondSliderBuilder() {
        const input = document.getElementById('second_slider_items_input');
        try {
            secondSliderItems = input.value.trim() ? JSON.parse(input.value.trim()) : [];
        } catch (e) {
            console.error('Invalid JSON for second slider:', e);
            secondSliderItems = [];
        }
        renderSecondSliderBuilder();
    }

    // Render List
    function renderSecondSliderBuilder() {
        const container = document.getElementById('second-slider-list');
        const emptyState = document.getElementById('second-slider-empty-state');
        container.innerHTML = '';

        if (secondSliderItems.length === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
            secondSliderItems.forEach((item, index) => {
                const row = document.createElement('div');
                row.className =
                    'grid grid-cols-12 gap-2 items-center bg-gray-800/50 p-2 rounded-lg border border-gray-700/50 hover:border-gray-600 transition-colors';

                // Image preview
                let imagePreview = item.image ? `<img src="${item.image}" class="h-8 w-8 object-cover rounded" onerror="this.src='https://via.placeholder.com/80?text=No%20Image'">` : `<i class="fas fa-image text-gray-500"></i>`;

                row.innerHTML = `
                    <div class="col-span-3 flex items-center gap-2">
                        <div class="w-8 h-8 flex items-center justify-center bg-gray-700 rounded">${imagePreview}</div>
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-gray-200 focus:ring-0 px-0"
                            value="${item.image || ''}"
                            onchange="updateSecondSlideItem(${index}, 'image', this.value)" placeholder="URL รูปภาพ">
                    </div>
                    <div class="col-span-3 border-l border-gray-700 pl-2">
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-gray-200 focus:ring-0 px-0"
                            value="${item.title || ''}"
                            onchange="updateSecondSlideItem(${index}, 'title', this.value)" placeholder="หัวข้อ">
                    </div>
                    <div class="col-span-4 border-l border-gray-700 pl-2">
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-gray-200 focus:ring-0 px-0"
                            value="${item.description || ''}"
                            onchange="updateSecondSlideItem(${index}, 'description', this.value)" placeholder="คำอธิบาย">
                    </div>
                    <div class="col-span-1 border-l border-gray-700 pl-2">
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-gray-200 focus:ring-0 px-0"
                            value="${item.link || ''}"
                            onchange="updateSecondSlideItem(${index}, 'link', this.value)" placeholder="ลิงก์">
                    </div>
                    <div class="col-span-1 text-center">
                        <button type="button" onclick="removeSecondSlideItem(${index})" class="text-gray-500 hover:text-red-400 transition-colors p-1">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `;
                container.appendChild(row);
            });
        }
        updateSecondSliderHiddenInput();
    }

    // Add Item
    window.addSecondSlideItem = function() {
        const imageInput = document.getElementById('new_second_slide_image');
        const titleInput = document.getElementById('new_second_slide_title');
        const descriptionInput = document.getElementById('new_second_slide_description');
        const linkInput = document.getElementById('new_second_slide_link');


        const image = imageInput.value.trim();
        const title = titleInput.value.trim();
        const description = descriptionInput.value.trim();
        const link = linkInput.value.trim();

        if (!image && !title && !description && !link) {
            alert('กรุณากรอกข้อมูลอย่างน้อยหนึ่งช่องสำหรับสไลด์');
            return;
        }

        secondSliderItems.push({
            image: image,
            title: title,
            description: description,
            link: link
        });

        // Clear inputs
        imageInput.value = '';
        titleInput.value = '';
        descriptionInput.value = '';
        linkInput.value = '';
        imageInput.focus();

        renderSecondSliderBuilder();
    }

    // Remove Item
    window.removeSecondSlideItem = function(index) {
        if (confirm('ต้องการลบรายการนี้ใช่หรือไม่?')) {
            secondSliderItems.splice(index, 1);
            renderSecondSliderBuilder();
        }
    }

    // Update Item
    window.updateSecondSlideItem = function(index, field, value) {
        secondSliderItems[index][field] = value;
        renderSecondSliderBuilder();
    }

    // Sync to Hidden Input
    function updateSecondSliderHiddenInput() {
        const input = document.getElementById('second_slider_items_input');
        input.value = JSON.stringify(secondSliderItems);
    }

    // --- Category Menu Builder Logic ---
    let categoryMenuItems = [];

    // Initialize
    function initCategoryMenuBuilder() {
        const input = document.getElementById('category_menu_items_input');
        try {
            categoryMenuItems = input.value.trim() ? JSON.parse(input.value.trim()) : [];
        } catch (e) {
            console.error('Invalid JSON for category menu:', e);
            categoryMenuItems = [];
        }
        renderCategoryMenuBuilder();
    }

    // Render List
    function renderCategoryMenuBuilder() {
        const container = document.getElementById('category-menu-list');
        const emptyState = document.getElementById('category-menu-empty-state');
        container.innerHTML = '';

        if (categoryMenuItems.length === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
            categoryMenuItems.forEach((item, index) => {
                const row = document.createElement('div');
                row.className =
                    'grid grid-cols-12 gap-2 items-center bg-gray-800/50 p-2 rounded-lg border border-gray-700/50 hover:border-gray-600 transition-colors';

                // Image preview
                let imagePreview = item.image ? `<img src="${item.image}" class="h-8 w-8 object-cover rounded" onerror="this.src='https://via.placeholder.com/80?text=No%20Image'">` : `<i class="fas fa-image text-gray-500"></i>`;

                row.innerHTML = `
                    <div class="col-span-3 flex items-center gap-2">
                        <div class="w-8 h-8 flex items-center justify-center bg-gray-700 rounded">${imagePreview}</div>
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-gray-200 focus:ring-0 px-0"
                            value="${item.image || ''}"
                            onchange="updateCategoryMenuItem(${index}, 'image', this.value)" placeholder="URL รูปภาพ">
                    </div>
                    <div class="col-span-5 border-l border-gray-700 pl-2">
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-gray-200 focus:ring-0 px-0"
                            value="${item.name || ''}"
                            onchange="updateCategoryMenuItem(${index}, 'name', this.value)" placeholder="ชื่อหมวดหมู่">
                    </div>
                    <div class="col-span-3 border-l border-gray-700 pl-2">
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-gray-200 focus:ring-0 px-0"
                            value="${item.link || ''}"
                            onchange="updateCategoryMenuItem(${index}, 'link', this.value)" placeholder="ลิงก์">
                    </div>
                    <div class="col-span-1 text-center">
                        <button type="button" onclick="removeCategoryMenuItem(${index})" class="text-gray-500 hover:text-red-400 transition-colors p-1">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `;
                container.appendChild(row);
            });
        }
        updateCategoryMenuHiddenInput();
    }

    // Add Item
    window.addCategoryMenuItem = function() {
        const imageInput = document.getElementById('new_category_image');
        const nameInput = document.getElementById('new_category_name');
        const linkInput = document.getElementById('new_category_link');


        const image = imageInput.value.trim();
        const name = nameInput.value.trim();
        const link = linkInput.value.trim();

        if (!image && !name && !link) {
            alert('กรุณากรอกข้อมูลอย่างน้อยหนึ่งช่องสำหรับหมวดหมู่');
            return;
        }

        categoryMenuItems.push({
            image: image,
            name: name,
            link: link
        });

        // Clear inputs
        imageInput.value = '';
        nameInput.value = '';
        linkInput.value = '';
        imageInput.focus();

        renderCategoryMenuBuilder();
    }

    // Remove Item
    window.removeCategoryMenuItem = function(index) {
        if (confirm('ต้องการลบรายการนี้ใช่หรือไม่?')) {
            categoryMenuItems.splice(index, 1);
            renderCategoryMenuBuilder();
        }
    }

    // Update Item
    window.updateCategoryMenuItem = function(index, field, value) {
        categoryMenuItems[index][field] = value;
        renderCategoryMenuBuilder();
    }

    // Sync to Hidden Input
    function updateCategoryMenuHiddenInput() {
        const input = document.getElementById('category_menu_items_input');
        input.value = JSON.stringify(categoryMenuItems);
    }

    // --- Small Slider All Products Builder Logic ---
    let smallSliderAllproductsItems = [];

    // Initialize
    function initSmallSliderAllproductsBuilder() {
        const input = document.getElementById('small_slider_allproducts_items_input');
        try {
            smallSliderAllproductsItems = input.value.trim() ? JSON.parse(input.value.trim()) : [];
        } catch (e) {
            console.error('Invalid JSON for small slider all products:', e);
            smallSliderAllproductsItems = [];
        }
        renderSmallSliderAllproductsBuilder();
    }

    // Render List
    function renderSmallSliderAllproductsBuilder() {
        const container = document.getElementById('small-slider-allproducts-list');
        const emptyState = document.getElementById('small-slider-allproducts-empty-state');
        container.innerHTML = '';

        if (smallSliderAllproductsItems.length === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
            smallSliderAllproductsItems.forEach((item, index) => {
                const row = document.createElement('div');
                row.className =
                    'grid grid-cols-12 gap-2 items-center bg-gray-800/50 p-2 rounded-lg border border-gray-700/50 hover:border-gray-600 transition-colors';

                // Image preview
                let imagePreview = item.image ? `<img src="${item.image}" class="h-8 w-8 object-cover rounded" onerror="this.src='https://via.placeholder.com/80?text=No%20Image'">` : `<i class="fas fa-image text-gray-500"></i>`;

                row.innerHTML = `
                    <div class="col-span-3 flex items-center gap-2">
                        <div class="w-8 h-8 flex items-center justify-center bg-gray-700 rounded">${imagePreview}</div>
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-gray-200 focus:ring-0 px-0"
                            value="${item.image || ''}"
                            onchange="updateSmallSliderAllproductsItem(${index}, 'image', this.value)" placeholder="URL รูปภาพ">
                    </div>
                    <div class="col-span-5 border-l border-gray-700 pl-2">
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-gray-200 focus:ring-0 px-0"
                            value="${item.title || ''}"
                            onchange="updateSmallSliderAllproductsItem(${index}, 'title', this.value)" placeholder="หัวข้อ">
                    </div>
                    <div class="col-span-3 border-l border-gray-700 pl-2">
                        <input type="text" class="input input-xs w-full bg-transparent border-0 text-gray-200 focus:ring-0 px-0"
                            value="${item.link || ''}"
                            onchange="updateSmallSliderAllproductsItem(${index}, 'link', this.value)" placeholder="ลิงก์">
                    </div>
                    <div class="col-span-1 text-center">
                        <button type="button" onclick="removeSmallSliderAllproductsItem(${index})" class="text-gray-500 hover:text-red-400 transition-colors p-1">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `;
                container.appendChild(row);
            });
        }
        updateSmallSliderAllproductsHiddenInput();
    }

    // Add Item
    window.addSmallSliderAllproductsItem = function() {
        const imageInput = document.getElementById('new_small_slider_allproducts_image');
        const titleInput = document.getElementById('new_small_slider_allproducts_title');
        const linkInput = document.getElementById('new_small_slider_allproducts_link');


        const image = imageInput.value.trim();
        const title = titleInput.value.trim();
        const link = linkInput.value.trim();

        if (!image && !title && !link) {
            alert('กรุณากรอกข้อมูลอย่างน้อยหนึ่งช่องสำหรับสไลด์');
            return;
        }

        smallSliderAllproductsItems.push({
            image: image,
            title: title,
            link: link
        });

        // Clear inputs
        imageInput.value = '';
        titleInput.value = '';
        linkInput.value = '';
        imageInput.focus();

        renderSmallSliderAllproductsBuilder();
    }

    // Remove Item
    window.removeSmallSliderAllproductsItem = function(index) {
        if (confirm('ต้องการลบรายการนี้ใช่หรือไม่?')) {
            smallSliderAllproductsItems.splice(index, 1);
            renderSmallSliderAllproductsBuilder();
        }
    }

    // Update Item
    window.updateSmallSliderAllproductsItem = function(index, field, value) {
        smallSliderAllproductsItems[index][field] = value;
        renderSmallSliderAllproductsBuilder();
    }

    // Sync to Hidden Input
    function updateSmallSliderAllproductsHiddenInput() {
        const input = document.getElementById('small_slider_allproducts_items_input');
        input.value = JSON.stringify(smallSliderAllproductsItems);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Start Service Builder
        initServiceBuilder();
        initHeroSliderBuilder(); // Initialize the new builder
        initReasonsSectionBuilder(); // Initialize the new builder
        initSecondSliderBuilder(); // Initialize the new builder
        initCategoryMenuBuilder(); // Initialize the new builder
        initSmallSliderAllproductsBuilder(); // Initialize the new builder

            // --- Image Deletion Logic (Existing) ---
            document.querySelectorAll('.delete-setting').forEach(button => {
                button.addEventListener('click', function() {
                    const key = this.dataset.key;
                    if (!key || !confirm(
                            `คุณแน่ใจหรือไม่ว่าต้องการลบรูปภาพนี้? การกระทำนี้ไม่สามารถย้อนกลับได้`
                            )) {
                        return;
                    }

                    // Change button to loading state
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