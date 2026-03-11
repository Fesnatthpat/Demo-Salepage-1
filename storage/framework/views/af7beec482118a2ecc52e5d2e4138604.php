<?php $__env->startSection('title', 'เพิ่มสินค้าใหม่'); ?>

<?php $__env->startSection('page-title'); ?>
    <div class="flex items-center gap-2 text-sm text-gray-400 overflow-x-auto whitespace-nowrap pb-1">
        <a href="<?php echo e(route('admin.products.index')); ?>"
            class="hover:text-emerald-400 transition-colors flex items-center gap-1">
            <i class="fas fa-box"></i> สินค้าทั้งหมด
        </a>
        <span class="text-gray-600">/</span>
        <span class="text-gray-100 font-bold text-emerald-400">เพิ่มสินค้าใหม่</span>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-7xl mx-auto pb-28 md:pb-0"> 

        <div class="mb-6 flex items-center gap-4 animate-fade-in-down">
            <div
                class="w-12 h-12 rounded-2xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 border border-emerald-500/30">
                <i class="fas fa-plus text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">สร้างสินค้าใหม่</h1>
                <p class="text-sm text-gray-400 mt-1">กรอกข้อมูลรายละเอียด ราคา และตัวเลือกสินค้า</p>
            </div>
        </div>

        <form action="<?php echo e(route('admin.products.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?php echo csrf_field(); ?>

            <?php echo $__env->make('admin.products._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <div
                class="fixed bottom-0 left-0 right-0 bg-gray-900/90 backdrop-blur-md border-t border-gray-700 p-4 z-50 md:static md:bg-transparent md:border-0 md:p-0 md:mt-8 shadow-[0_-10px_20px_rgba(0,0,0,0.3)] md:shadow-none transition-all">
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 max-w-7xl mx-auto w-full">
                    <a href="<?php echo e(route('admin.products.index')); ?>"
                        class="btn bg-gray-800 hover:bg-gray-700 text-gray-300 border-none w-full sm:w-auto h-14 sm:h-12 rounded-xl font-bold transition-colors">
                        <i class="fas fa-times mr-1"></i> ยกเลิก
                    </a>
                    <button type="submit"
                        class="btn bg-emerald-600 hover:bg-emerald-700 border-none text-white px-8 shadow-lg shadow-emerald-900/30 w-full sm:w-auto h-14 sm:h-12 rounded-xl font-bold text-base transition-transform active:scale-95">
                        <i class="fas fa-save mr-2"></i> บันทึกข้อมูลสินค้า
                    </button>
                </div>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/products/create.blade.php ENDPATH**/ ?>