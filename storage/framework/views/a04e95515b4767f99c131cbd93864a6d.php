

<?php $__env->startSection('title', 'เพิ่มคำถามที่พบบ่อย'); ?>

<?php $__env->startSection('page-title'); ?>
    <div class="text-2xl font-bold">เพิ่มคำถามที่พบบ่อย (FAQ)</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-gray-800 rounded-lg shadow-lg p-6 max-w-2xl mx-auto">
    <form action="<?php echo e(route('admin.faqs.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="space-y-6">
            
            <div>
                <label for="question" class="block text-sm font-medium text-gray-300">คำถาม</label>
                <input type="text" name="question" id="question" value="<?php echo e(old('question')); ?>" required
                       class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white focus:ring-emerald-500 focus:border-emerald-500">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['question'];
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
                <label for="answer" class="block text-sm font-medium text-gray-300">คำตอบ</label>
                <textarea name="answer" id="answer" rows="5" required
                          class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white focus:ring-emerald-500 focus:border-emerald-500"><?php echo e(old('answer')); ?></textarea>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['answer'];
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
                <label for="sort_order" class="block text-sm font-medium text-gray-300">ลำดับการแสดงผล</label>
                <input type="number" name="sort_order" id="sort_order" value="<?php echo e(old('sort_order', 0)); ?>"
                       class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white focus:ring-emerald-500 focus:border-emerald-500">
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

            
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked
                       class="h-4 w-4 rounded border-gray-600 bg-gray-700 text-emerald-600 focus:ring-emerald-500">
                <label for="is_active" class="ml-2 block text-sm text-gray-300">เปิดใช้งาน</label>
            </div>
        </div>

        
        <div class="mt-8 flex justify-end gap-4">
            <a href="<?php echo e(route('admin.faqs.index')); ?>" class="btn btn-ghost">ยกเลิก</a>
            <button type="submit" class="btn bg-emerald-600 hover:bg-emerald-700 text-white border-none">
                <i class="fas fa-save mr-2"></i> บันทึก
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/faqs/create.blade.php ENDPATH**/ ?>