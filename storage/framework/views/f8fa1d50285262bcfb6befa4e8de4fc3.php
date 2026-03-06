

<?php $__env->startSection('title', 'แก้ไขโปรโมชั่น'); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <nav class="flex text-sm text-gray-500 mb-2">
                    <a href="<?php echo e(route('admin.promotions.index')); ?>"
                        class="hover:text-emerald-400 transition-colors">โปรโมชั่น</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-300">แก้ไข</span>
                </nav>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-bold text-gray-100">แก้ไขโปรโมชั่น</h1>
                    <span class="badge badge-neutral bg-gray-700 border-none text-gray-300">ID: <?php echo e($promotion->id); ?></span>
                </div>
            </div>

            <div
                class="flex items-center gap-2 text-sm text-gray-400 bg-gray-800 px-3 py-1.5 rounded-lg border border-gray-700">
                <i class="far fa-clock"></i> แก้ไขล่าสุด: <?php echo e($promotion->updated_at->diffForHumans()); ?>

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