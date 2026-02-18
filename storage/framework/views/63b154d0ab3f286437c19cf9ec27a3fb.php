

<?php $__env->startSection('title', 'แก้ไขเนื้อหา "เกี่ยวกับติดใจ"'); ?>

<?php $__env->startSection('page-title'); ?>
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <a href="<?php echo e(route('admin.favorites.index')); ?>" class="hover:text-red-400 transition-colors">
            <i class="fas fa-heart mr-1"></i> เกี่ยวกับติดใจ
        </a>
        <span>/</span>
        <span class="text-gray-100 font-medium">แก้ไขข้อมูล</span>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="bg-gray-800 rounded-xl shadow-2xl p-6 border border-gray-700">
        <div class="mb-6 border-b border-gray-700 pb-4">
            <h2 class="text-2xl font-bold text-gray-100">แก้ไข: <span class="text-red-500"><?php echo e($favorite->title); ?></span></h2>
        </div>

        <form action="<?php echo e(route('admin.favorites.update', $favorite->id)); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

                
                <div class="xl:col-span-2 space-y-6">
                    
                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-300 mb-2">หัวข้อ (Title) <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="<?php echo e(old('title', $favorite->title)); ?>"
                            required
                            class="block w-full bg-gray-900 border-gray-600 rounded-lg shadow-sm text-white focus:ring-red-500 focus:border-red-500 px-4 py-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-500"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div>
                        <label for="content" class="block text-sm font-semibold text-gray-300 mb-2">รายละเอียด (Content)
                            <span class="text-red-500">*</span></label>
                        <textarea name="content" id="content" rows="8" required
                            class="block w-full bg-gray-900 border-gray-600 rounded-lg shadow-sm text-white focus:ring-red-500 focus:border-red-500 px-4 py-3 leading-relaxed"><?php echo e(old('content', $favorite->content)); ?></textarea>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-500"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="space-y-6">
                    
                    <div class="bg-gray-900/50 p-6 rounded-xl border border-gray-700">
                        <label for="image" class="block text-sm font-semibold text-gray-300 mb-3">รูปภาพประกอบ</label>

                        
                        <div
                            class="mb-4 w-full h-48 bg-gray-800 rounded-lg border-2 border-dashed border-gray-600 flex items-center justify-center overflow-hidden relative group">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($favorite->image_path): ?>
                                <img id="imagePreview" src="<?php echo e(asset('storage/' . $favorite->image_path)); ?>"
                                    class="object-cover w-full h-full absolute inset-0 z-10 transition-transform duration-300 group-hover:scale-105">
                                <div id="imagePlaceholder" class="hidden text-center text-gray-500 z-0">
                                    <i class="fas fa-image text-3xl mb-2"></i>
                                    <p class="text-xs">ไม่มีรูปภาพ</p>
                                </div>
                            <?php else: ?>
                                <img id="imagePreview" src=""
                                    class="hidden object-cover w-full h-full absolute inset-0 z-10">
                                <div id="imagePlaceholder" class="text-center text-gray-500 z-0">
                                    <i class="fas fa-image text-4xl mb-2"></i>
                                    <p class="text-xs">ไม่มีรูปภาพ</p>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <label class="text-xs text-red-400 mb-2 block">อัปโหลดรูปใหม่เพื่อแทนที่รูปเดิม</label>
                        <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)"
                            class="block w-full text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-white hover:file:bg-gray-600 cursor-pointer transition-colors">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-500"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="bg-gray-900/50 p-6 rounded-xl border border-gray-700 space-y-5">
                        
                        <div>
                            <label for="sort_order"
                                class="block text-sm font-semibold text-gray-300 mb-2">ลำดับการแสดงผล</label>
                            <input type="number" name="sort_order" id="sort_order"
                                value="<?php echo e(old('sort_order', $favorite->sort_order)); ?>"
                                class="block w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm text-white focus:ring-red-500 focus:border-red-500 px-4 py-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-500"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <hr class="border-gray-700">

                        
                        <div class="flex items-center pt-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                <?php if(old('is_active', $favorite->is_active)): ?> checked <?php endif; ?>
                                class="h-6 w-6 rounded border-gray-500 bg-gray-800 text-yellow-500 focus:ring-yellow-500 focus:ring-offset-gray-900 cursor-pointer">
                            <label for="is_active"
                                class="ml-3 block text-sm font-medium text-gray-200 cursor-pointer">เปิดแสดงผลที่หน้าเว็บ</label>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="mt-8 pt-6 border-t border-gray-700 flex justify-end gap-4">
                <a href="<?php echo e(route('admin.favorites.index')); ?>"
                    class="btn btn-ghost text-gray-300 hover:bg-gray-700">ยกเลิก</a>
                <button type="submit" class="btn bg-yellow-600 hover:bg-yellow-700 text-white border-none shadow-lg px-8">
                    <i class="fas fa-save mr-2"></i> บันทึกการเปลี่ยนแปลง
                </button>
            </div>
        </form>
    </div>

    
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('imagePreview');
                var placeholder = document.getElementById('imagePlaceholder');
                output.src = reader.result;
                output.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            };
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/favorites/edit.blade.php ENDPATH**/ ?>