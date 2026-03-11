

<?php $__env->startSection('title', 'แก้ไขโปรโมชั่น'); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="mb-10 animate-fade-in-down">
            <a href="<?php echo e(route('admin.promotions.index')); ?>"
                class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-white mb-6 transition-all transform hover:-translate-x-1">
                <i class="fas fa-arrow-left mr-2"></i> กลับไปหน้าจัดการ
            </a>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 p-6 bg-gradient-to-r from-gray-800 to-gray-800/50 rounded-2xl border border-gray-700/50 shadow-lg">
                <div class="flex items-center gap-5">
                    <div class="p-4 bg-indigo-500/10 rounded-2xl border border-indigo-500/20 shadow-[0_0_15px_rgba(99,102,241,0.15)]">
                        <i class="fas fa-edit text-indigo-400 text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white tracking-tight">แก้ไขโปรโมชั่น</h1>
                        <div class="flex items-center gap-3 mt-1.5">
                            <span class="px-2 py-0.5 rounded bg-gray-700 text-gray-300 text-xs font-mono">ID: <?php echo e($promotion->id); ?></span>
                            <span class="text-gray-500 text-xs">•</span>
                            <span class="text-gray-400 text-xs">แก้ไขล่าสุด: <?php echo e($promotion->updated_at->diffForHumans()); ?></span>
                        </div>
                    </div>
                </div>

                
                <div class="flex items-center px-5 py-3 bg-gray-900/50 rounded-xl border border-gray-700/50 backdrop-blur-sm">
                    <span class="text-sm text-gray-400 mr-3">สถานะปัจจุบัน:</span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider <?php echo e($promotion->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-gray-600/10 text-gray-400 border border-gray-600/20'); ?>">
                        <span class="w-2 h-2 rounded-full <?php echo e($promotion->is_active ? 'bg-emerald-400 animate-pulse' : 'bg-gray-400'); ?>"></span>
                        <?php echo e($promotion->is_active ? 'Active' : 'Inactive'); ?>

                    </span>
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