

<?php $__env->startSection('title', 'แก้ไขโปรโมชั่น'); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <a href="<?php echo e(route('admin.promotions.index')); ?>"
                    class="inline-flex items-center text-sm text-gray-400 hover:text-white mb-4 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> ย้อนกลับ
                </a>
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-indigo-600/20 rounded-xl">
                        <i class="fas fa-edit text-indigo-400 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">แก้ไขโปรโมชั่น</h1>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-gray-400 text-sm">ID: <?php echo e($promotion->id); ?></span>
                            <span class="text-gray-600">•</span>
                            <span class="text-gray-400 text-sm">แก้ไขล่าสุด:
                                <?php echo e($promotion->updated_at->diffForHumans()); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="px-4 py-2 bg-gray-800 rounded-lg border border-gray-700 text-sm">
                สถานะปัจจุบัน:
                <span class="font-bold <?php echo e($promotion->is_active ? 'text-emerald-400' : 'text-gray-400'); ?>">
                    <?php echo e($promotion->is_active ? 'เปิดใช้งาน (Active)' : 'ปิดใช้งาน (Inactive)'); ?>

                </span>
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