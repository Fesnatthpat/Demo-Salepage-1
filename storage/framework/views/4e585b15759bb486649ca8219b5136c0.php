

<?php $__env->startSection('title', 'สร้างโปรโมชั่นใหม่'); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <a href="<?php echo e(route('admin.promotions.index')); ?>"
                class="btn btn-ghost btn-sm gap-2 pl-0 text-gray-500 hover:text-primary mb-2">
                <i class="fas fa-arrow-left"></i> กลับหน้ารายการ
            </a>
            <h1 class="text-3xl font-bold text-gray-800">สร้างโปรโมชั่นใหม่</h1>
            <p class="text-sm text-gray-500 mt-1">กำหนดเงื่อนไข ซื้อ X แถม Y เพื่อกระตุ้นยอดขาย</p>
        </div>

        <form action="<?php echo e(route('admin.promotions.store')); ?>" method="POST" autocomplete="off">
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('admin.promotions._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/promotions/create.blade.php ENDPATH**/ ?>