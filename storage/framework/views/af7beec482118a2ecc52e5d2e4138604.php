<?php $__env->startSection('title', 'เพิ่มสินค้าใหม่'); ?>

<?php $__env->startSection('page-title'); ?>
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <a href="<?php echo e(route('admin.products.index')); ?>" class="hover:text-emerald-400 transition-colors">
            <i class="fas fa-box mr-1"></i> สินค้า
        </a>
        <span>/</span>
        <span class="text-gray-100 font-medium">เพิ่มสินค้าใหม่</span>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-7xl mx-auto">
        <form action="<?php echo e(route('admin.products.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>

            <?php echo $__env->make('admin.products._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div
                class="fixed bottom-0 left-0 right-0 bg-gray-800 border-t border-gray-700 p-4 z-50 md:static md:bg-transparent md:border-0 md:p-0 md:mt-8">
                <div class="flex justify-end gap-3 max-w-7xl mx-auto">
                    <a href="<?php echo e(route('admin.products.index')); ?>"
                        class="btn btn-ghost text-gray-400 hover:text-white hover:bg-gray-700">
                        ยกเลิก
                    </a>
                    <button type="submit"
                        class="btn btn-primary bg-emerald-600 hover:bg-emerald-700 border-none text-white px-8 shadow-lg shadow-emerald-900/20">
                        <i class="fas fa-save mr-2"></i> บันทึกสินค้า
                    </button>
                </div>
            </div>
            <div class="h-20 md:hidden"></div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/products/create.blade.php ENDPATH**/ ?>