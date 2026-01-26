

<?php $__env->startSection('title', 'แก้ไขโปรโมชั่น'); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8 flex justify-between items-end">
            <div>
                <a href="<?php echo e(route('admin.promotions.index')); ?>"
                    class="btn btn-ghost btn-sm gap-2 pl-0 text-gray-500 hover:text-primary mb-2">
                    <i class="fas fa-arrow-left"></i> กลับหน้ารายการ
                </a>
                <h1 class="text-3xl font-bold text-gray-800">แก้ไขโปรโมชั่น</h1>
                <div class="flex items-center gap-2 mt-1">
                    <span class="badge badge-neutral badge-sm">ID: <?php echo e($promotion->id); ?></span>
                    <span class="text-sm text-gray-500"><?php echo e($promotion->name); ?></span>
                </div>
            </div>
        </div>

        <form action="<?php echo e(route('admin.promotions.update', $promotion->id)); ?>" method="POST" autocomplete="off">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <?php echo $__env->make('admin.promotions._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/promotions/edit.blade.php ENDPATH**/ ?>