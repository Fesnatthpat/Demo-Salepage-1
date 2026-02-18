

<?php $__env->startSection('title', 'จัดการ "เกี่ยวกับติดใจ" (Visual Editor)'); ?>

<?php $__env->startSection('page-title'); ?>
    <div class="text-2xl font-bold text-gray-100 flex items-center">
        <i class="fas fa-desktop text-emerald-500 mr-3"></i> ระบบจำลองหน้าเว็บ (Visual Editor)
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <?php
        // ดึงค่า Settings
        $aboutTitle = \App\Models\SiteSetting::get('about_title') ?? 'เกี่ยวกับติดใจ';
        $aboutSub = \App\Models\SiteSetting::get('about_subtitle') ?? 'ความตั้งใจของเรา...เพื่อส่งมอบความสุขให้คุณ';
        $email = \App\Models\SiteSetting::get('contact_email') ?? 'contact@tidjai.com';
        $phone = \App\Models\SiteSetting::get('contact_phone') ?? '02-123-4567';
        $address =
            \App\Models\SiteSetting::get('contact_address') ?? "123 ถนนสุขุมวิท แขวงคลองเตย\nเขตคลองเตย กรุงเทพฯ 10110";
        $mapUrl = \App\Models\SiteSetting::get('map_url');

        $siteLogoPath = \App\Models\SiteSetting::get('site_logo');
        $siteLogoUrl = $siteLogoPath ? asset('storage/' . $siteLogoPath) : asset('images/logo/logo1.png');
    ?>

    <div class="bg-gray-800 rounded-2xl shadow-2xl overflow-hidden border-4 border-gray-700">

        
        <div
            class="bg-gray-900 px-6 py-4 flex flex-col sm:flex-row justify-between items-center border-b border-gray-700 gap-4">
            <div class="text-emerald-400 font-medium text-sm flex items-center">
                <i class="fas fa-eye mr-2"></i> โหมดแสดงผลจริง (Preview Mode) - คลิกที่ส่วนต่างๆ เพื่อแก้ไข
            </div>
            <button onclick="openCreateModal()"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg font-bold shadow-lg transition-transform hover:scale-105 border-none">
                <i class="fas fa-plus-circle mr-2"></i> เพิ่มเนื้อหาใหม่
            </button>
        </div>

        
        <div class="bg-gray-100 h-[800px] overflow-y-auto relative font-sans">

            
            
            
            <div class="relative bg-red-600 text-white pt-16 pb-32 overflow-hidden group">
                
                <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-50">
                    <button onclick="openSettingsModal('hero')"
                        class="bg-yellow-500 text-white px-4 py-2 rounded shadow font-bold text-sm hover:bg-yellow-600">
                        <i class="fas fa-edit mr-1"></i> แก้ไขส่วนหัว
                    </button>
                </div>
                <div
                    class="absolute inset-0 border-2 border-transparent group-hover:border-yellow-400 border-dashed pointer-events-none z-40">
                </div>

                <div class="container mx-auto px-4 text-center relative z-10">
                    <div class="mb-6 flex justify-center">
                        <div class="p-4 bg-white rounded-full shadow-lg w-24 h-24 flex items-center justify-center">
                            <img src="<?php echo e($siteLogoUrl); ?>" class="max-w-full max-h-full object-contain" />
                        </div>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold mb-4"><?php echo e($aboutTitle); ?></h1>
                    <p class="text-white/90 text-lg font-light"><?php echo e($aboutSub); ?></p>
                </div>

                
                <div class="absolute bottom-0 left-0 right-0 h-16 bg-gray-100 rounded-t-[3rem] transform translate-y-8">
                </div>
            </div>

            <div class="container mx-auto px-4 max-w-5xl -mt-20 relative z-20 pb-20 space-y-16">

                
                
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $favorites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $fav): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <div
                        class="bg-white rounded-3xl shadow-xl overflow-hidden relative group transition-all duration-300 hover:shadow-2xl">

                        
                        <div
                            class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-50 flex gap-2">
                            <button onclick="openEditModal(this)" data-id="<?php echo e($fav->id); ?>"
                                data-title="<?php echo e($fav->title); ?>" data-content="<?php echo e($fav->content); ?>"
                                data-image="<?php echo e($fav->image_path ? asset('storage/' . $fav->image_path) : ''); ?>"
                                data-sort="<?php echo e($fav->sort_order); ?>" data-active="<?php echo e($fav->is_active); ?>"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded shadow text-xs font-bold">
                                <i class="fas fa-edit"></i> แก้ไข
                            </button>
                            <form action="<?php echo e(route('admin.favorites.destroy', $fav->id)); ?>" method="POST"
                                onsubmit="return confirm('ลบ?');">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded shadow text-xs font-bold">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                        <div
                            class="absolute inset-0 border-2 border-transparent group-hover:border-blue-500 border-dashed rounded-3xl pointer-events-none z-40">
                        </div>

                        
                        <div class="flex flex-col md:flex-row">

                            
                            <div
                                class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center <?php echo e($index % 2 != 0 ? 'md:order-2' : ''); ?>">
                                
                                <div class="flex items-start gap-4 mb-6">
                                    <div class="w-1.5 h-10 bg-red-600 rounded-full mt-1 flex-shrink-0"></div>
                                    <h2 class="text-3xl font-bold text-gray-800 leading-tight"><?php echo e($fav->title); ?></h2>
                                </div>

                                
                                <div class="text-gray-600 text-lg leading-relaxed space-y-4 font-light">
                                    <?php echo nl2br($fav->content); ?>

                                </div>
                            </div>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fav->image_path): ?>
                                <div
                                    class="w-full md:w-1/2 h-[400px] md:h-auto relative <?php echo e($index % 2 != 0 ? 'md:order-1' : ''); ?>">
                                    <img src="<?php echo e(asset('storage/' . $fav->image_path)); ?>"
                                        class="absolute inset-0 w-full h-full object-cover">
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="text-center py-20 bg-white rounded-3xl border-dashed border-2 border-gray-300">
                        <p class="text-gray-400">ยังไม่มีเนื้อหา</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                
                
                <div class="bg-white rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.08)] overflow-hidden relative group">

                    
                    <div
                        class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-50">
                        <button onclick="openSettingsModal('contact')"
                            class="bg-yellow-500 text-white px-3 py-1.5 rounded shadow text-xs font-bold hover:bg-yellow-600">
                            <i class="fas fa-edit mr-1"></i> แก้ไขข้อมูลติดต่อ
                        </button>
                    </div>
                    <div
                        class="absolute inset-0 border-2 border-transparent group-hover:border-yellow-400 border-dashed rounded-xl pointer-events-none z-40">
                    </div>

                    <div class="p-8 md:p-10">
                        <div class="text-center mb-10">
                            <h2 class="text-3xl font-bold text-[#1e3a5f] mb-3">ติดต่อติดใจ</h2>
                            <p class="text-gray-500">เรายินดีให้บริการและพร้อมตอบทุกคำถามของคุณ
                                กรุณาติดต่อเราผ่านช่องทางด้านล่าง</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-10">
                            
                            <div>
                                <h3 class="text-lg font-bold text-[#1e3a5f] mb-4">ข้อมูลการติดต่อ</h3>
                                <div class="space-y-4">
                                    <div class="flex items-start gap-3">
                                        <i class="fas fa-map-marker-alt text-red-500 mt-1 w-5"></i>
                                        <div>
                                            <p class="font-bold text-gray-700">บริษัท ติดใจ จำกัด</p>
                                            <p class="text-gray-600 text-sm leading-relaxed"><?php echo nl2br(e($address)); ?></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-phone-alt text-red-500 w-5"></i>
                                        <p class="text-gray-600">โทรศัพท์: <span
                                                class="text-gray-800"><?php echo e($phone); ?></span></p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-envelope text-red-500 w-5"></i>
                                        <p class="text-gray-600">อีเมล: <span
                                                class="text-gray-800"><?php echo e($email); ?></span></p>
                                    </div>
                                </div>
                            </div>

                            
                            <div>
                                <h3 class="text-lg font-bold text-[#1e3a5f] mb-4">เวลาทำการ</h3>
                                <div class="space-y-2 text-gray-600">
                                    <div class="flex justify-between">
                                        <span>วันจันทร์ - วันศุกร์:</span>
                                        <span>9:00 น. - 18:00 น.</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>วันเสาร์ - วันอาทิตย์:</span>
                                        <span class="text-red-500">ปิดทำการ</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-400 mt-4 leading-relaxed">
                                    หากคุณมีข้อสงสัยหรือต้องการความช่วยเหลือเร่งด่วน กรุณาโทรศัพท์หาเราในช่วงเวลาทำการ
                                </p>
                            </div>
                        </div>
                    </div>

                    
                    <div class="w-full h-[300px] bg-gray-100 border-t border-gray-200 relative">
                        <div
                            class="absolute top-4 left-4 z-10 bg-white px-3 py-1 rounded shadow text-sm font-bold text-gray-500">
                            แผนที่
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mapUrl): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(str_contains($mapUrl, '<iframe')): ?>
                                <?php echo str_replace('<iframe', '<iframe class="w-full h-full border-0"', $mapUrl); ?>

                            <?php else: ?>
                                <iframe src="<?php echo e($mapUrl); ?>" class="w-full h-full border-0" allowfullscreen=""
                                    loading="lazy"></iframe>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <i class="fas fa-map-marked-alt text-4xl mr-2"></i> ไม่พบข้อมูลแผนที่
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    
    
    

    
    <div id="frontendEditModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
        <div
            class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh] border border-gray-600">
            <div class="bg-gray-900 text-white px-6 py-4 flex justify-between items-center border-b border-gray-700">
                <h3 id="modalTitle" class="text-xl font-bold text-emerald-400">จัดการเนื้อหา</h3>
                <button onclick="closeModal('frontendEditModal')" class="text-gray-400 hover:text-white"><i
                        class="fas fa-times text-2xl"></i></button>
            </div>

            <form id="frontendEditForm" method="POST" enctype="multipart/form-data"
                class="flex-1 overflow-y-auto p-6 md:p-8">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" id="formMethod" value="PUT">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
                    <div class="md:col-span-3 space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">หัวข้อ (Title)</label>
                            <input type="text" name="title" id="m_title" required
                                class="w-full bg-gray-900 border-gray-600 text-white rounded-lg p-3 border">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">รายละเอียด (Content)</label>
                            <textarea name="content" id="m_content" rows="12" required
                                class="w-full bg-gray-900 border-gray-600 text-white rounded-lg p-3 border font-mono"></textarea>
                            <p class="mt-1 text-xs text-gray-400">รองรับ HTML Tags เช่น &lt;h2&gt;, &lt;b&gt;, &lt;br&gt;
                            </p>
                        </div>
                    </div>
                    <div class="md:col-span-2 space-y-5">
                        <div class="bg-gray-900/50 p-5 rounded-xl border border-gray-700">
                            <label class="block text-sm font-semibold text-gray-300 mb-2">รูปภาพประกอบ</label>
                            <div
                                class="mb-4 h-40 bg-gray-800 rounded-lg flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-600 relative">
                                <img id="m_img_preview" src=""
                                    class="hidden object-cover w-full h-full absolute inset-0 z-10">
                                <span id="m_img_placeholder"
                                    class="text-gray-500 text-sm z-0 flex flex-col items-center"><i
                                        class="fas fa-image text-3xl mb-1"></i>อัปโหลดรูป</span>
                            </div>
                            <input type="file" name="image" accept="image/*" onchange="previewModalImage(event)"
                                class="w-full text-sm text-gray-400">
                        </div>
                        <div class="bg-gray-900/50 p-5 rounded-xl border border-gray-700 space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">ลำดับ</label>
                                <input type="number" name="sort_order" id="m_sort"
                                    class="w-full bg-gray-800 border-gray-600 text-white rounded-lg p-2.5 border">
                            </div>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" name="is_active" id="m_active" value="1"
                                    class="w-5 h-5 rounded border-gray-500 bg-gray-800 text-emerald-500">
                                <span class="text-sm font-semibold text-gray-300">เปิดแสดงผล</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mt-8 pt-5 border-t border-gray-700 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('frontendEditModal')"
                        class="px-6 py-2.5 bg-gray-700 text-gray-200 rounded-lg">ยกเลิก</button>
                    
                    <button type="button" onclick="submitFrontendForm()"
                        class="px-8 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold"><i
                            class="fas fa-save mr-2"></i>บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>

    
    <div id="settingsEditModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
        <div
            class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col border border-gray-600">
            <div class="bg-gray-900 text-white px-6 py-4 flex justify-between items-center border-b border-gray-700">
                <h3 class="text-xl font-bold text-yellow-400">แก้ไขข้อมูลระบบ</h3>
                <button onclick="closeModal('settingsEditModal')" class="text-gray-400 hover:text-white"><i
                        class="fas fa-times text-2xl"></i></button>
            </div>

            <form action="<?php echo e(route('admin.settings.update')); ?>" method="POST" enctype="multipart/form-data"
                class="p-6">
                <?php echo csrf_field(); ?>
                <div id="setting_hero_fields" class="space-y-4 hidden">
                    <div class="bg-gray-900/50 p-4 rounded-lg border border-gray-700">
                        <label class="block text-sm font-semibold text-gray-300 mb-2">โลโก้ (Site Logo)</label>
                        <input type="file" name="site_logo" accept="image/*" class="w-full text-sm text-gray-400">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-1">หัวข้อหลัก (Title)</label>
                        <input type="text" name="settings[about_title]" value="<?php echo e($aboutTitle); ?>"
                            class="w-full bg-gray-900 border-gray-600 text-white rounded-lg p-3 border">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-1">คำโปรย (Subtitle)</label>
                        <input type="text" name="settings[about_subtitle]" value="<?php echo e($aboutSub); ?>"
                            class="w-full bg-gray-900 border-gray-600 text-white rounded-lg p-3 border">
                    </div>
                </div>

                <div id="setting_contact_fields" class="space-y-4 hidden">
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-1">ที่อยู่ (Address)</label>
                        <textarea name="settings[contact_address]" rows="3"
                            class="w-full bg-gray-900 border-gray-600 text-white rounded-lg p-3 border"><?php echo e($address); ?></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-1">อีเมล</label>
                            <input type="text" name="settings[contact_email]" value="<?php echo e($email); ?>"
                                class="w-full bg-gray-900 border-gray-600 text-white rounded-lg p-3 border">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-1">โทรศัพท์</label>
                            <input type="text" name="settings[contact_phone]" value="<?php echo e($phone); ?>"
                                class="w-full bg-gray-900 border-gray-600 text-white rounded-lg p-3 border">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-1">Google Map Embed URL</label>
                        <input type="text" name="settings[map_url]" value="<?php echo e($mapUrl); ?>"
                            class="w-full bg-gray-900 border-gray-600 text-white rounded-lg p-3 border text-xs font-mono">
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('settingsEditModal')"
                        class="px-6 py-2.5 bg-gray-700 text-gray-200 rounded-lg">ยกเลิก</button>
                    <button type="submit"
                        class="px-8 py-2.5 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-semibold"><i
                            class="fas fa-save mr-2"></i>บันทึก</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let codeEditorInstance = null;

        function initializeCodeMirror(content) {
            if (codeEditorInstance) {
                codeEditorInstance.toTextArea();
                codeEditorInstance = null;
            }
            const textarea = document.getElementById('m_content');
            textarea.value = content;
            setTimeout(() => {
                codeEditorInstance = CodeMirror.fromTextArea(textarea, {
                    lineNumbers: true,
                    mode: 'htmlmixed',
                    theme: 'dracula',
                    indentUnit: 4,
                    indentWithTabs: true,
                    lineWrapping: true,
                });
                codeEditorInstance.refresh();
            }, 50);
        }

        function submitFrontendForm() {
            if (codeEditorInstance) codeEditorInstance.save();
            document.getElementById('frontendEditForm').submit();
        }

        function openEditModal(btn) {
            document.getElementById('frontendEditModal').classList.remove('hidden');
            document.getElementById('frontendEditModal').classList.add('flex');

            document.getElementById('frontendEditForm').action = `/admin/favorites/${btn.dataset.id}`;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('modalTitle').innerHTML =
            '<i class="fas fa-edit mr-2 text-yellow-400"></i>แก้ไขเนื้อหา';

            document.getElementById('m_title').value = btn.dataset.title;
            document.getElementById('m_sort').value = btn.dataset.sort;
            document.getElementById('m_active').checked = btn.dataset.active === "1";

            const imgP = document.getElementById('m_img_preview'),
                imgPh = document.getElementById('m_img_placeholder');
            if (btn.dataset.image) {
                imgP.src = btn.dataset.image;
                imgP.classList.remove('hidden');
                imgPh.classList.add('hidden');
            } else {
                imgP.src = '';
                imgP.classList.add('hidden');
                imgPh.classList.remove('hidden');
            }
            initializeCodeMirror(btn.dataset.content);
        }

        function openCreateModal() {
            document.getElementById('frontendEditModal').classList.remove('hidden');
            document.getElementById('frontendEditModal').classList.add('flex');
            document.getElementById('frontendEditForm').action = `<?php echo e(route('admin.favorites.store')); ?>`;
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('modalTitle').innerHTML =
                '<i class="fas fa-plus-circle mr-2 text-emerald-400"></i>เพิ่มเนื้อหาใหม่';
            document.getElementById('frontendEditForm').reset();
            document.getElementById('m_sort').value = '0';
            document.getElementById('m_active').checked = true;
            document.getElementById('m_img_preview').classList.add('hidden');
            document.getElementById('m_img_placeholder').classList.remove('hidden');
            initializeCodeMirror('');
        }

        function openSettingsModal(type) {
            document.getElementById('settingsEditModal').classList.remove('hidden');
            document.getElementById('settingsEditModal').classList.add('flex');
            if (type === 'hero') {
                document.getElementById('setting_hero_fields').classList.remove('hidden');
                document.getElementById('setting_contact_fields').classList.add('hidden');
            } else {
                document.getElementById('setting_hero_fields').classList.add('hidden');
                document.getElementById('setting_contact_fields').classList.remove('hidden');
            }
        }

        function closeModal(modalId) {
            if (modalId === 'frontendEditModal' && codeEditorInstance) {
                codeEditorInstance.toTextArea();
                codeEditorInstance = null;
            }
            document.getElementById(modalId).classList.add('hidden');
            document.getElementById(modalId).classList.remove('flex');
        }

        function previewModalImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var o = document.getElementById('m_img_preview'),
                    p = document.getElementById('m_img_placeholder');
                o.src = reader.result;
                o.classList.remove('hidden');
                p.classList.add('hidden');
            };
            if (event.target.files[0]) reader.readAsDataURL(event.target.files[0]);
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/favorites/index.blade.php ENDPATH**/ ?>