

<?php $__env->startSection('title', 'สร้างโปรโมชั่นใหม่'); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="mb-10 animate-fade-in-down">
            <a href="<?php echo e(route('admin.promotions.index')); ?>"
                class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-white mb-6 transition-all transform hover:-translate-x-1">
                <i class="fas fa-arrow-left mr-2"></i> กลับไปหน้าจัดการ
            </a>
            
            <div class="flex items-center gap-5 p-6 bg-gradient-to-r from-gray-800 to-gray-800/50 rounded-2xl border border-gray-700/50 shadow-lg">
                <div class="p-4 bg-emerald-500/10 rounded-2xl border border-emerald-500/20 shadow-[0_0_15px_rgba(16,185,129,0.15)]">
                    <i class="fas fa-bullhorn text-emerald-400 text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white tracking-tight">สร้างแคมเปญใหม่</h1>
                    <p class="text-gray-400 text-sm mt-1">กำหนดเงื่อนไข ส่วนลด และของแถม เพื่อกระตุ้นยอดขายของคุณ</p>
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