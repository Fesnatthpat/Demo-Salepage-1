

<?php $__env->startSection('title', 'สร้างโปรโมชั่นใหม่'); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="mb-8">
            <a href="<?php echo e(route('admin.promotions.index')); ?>"
                class="inline-flex items-center text-sm text-gray-400 hover:text-white mb-4 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> ย้อนกลับ
            </a>
            <div class="flex items-center gap-3">
                <div class="p-3 bg-emerald-600/20 rounded-xl">
                    <i class="fas fa-bullhorn text-emerald-400 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">สร้างแคมเปญใหม่</h1>
                    <p class="text-gray-400 text-sm mt-0.5">กำหนดรายละเอียด เงื่อนไข และระยะเวลาของโปรโมชั่น</p>
                </div>
            </div>
        </div>

        <form action="<?php echo e(route('admin.promotions.store')); ?>" method="POST" autocomplete="off">
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('admin.promotions._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/promotions/create.blade.php ENDPATH**/ ?>