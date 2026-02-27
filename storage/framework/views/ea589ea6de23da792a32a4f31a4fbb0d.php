

<?php $__env->startSection('title', 'จัดการคำถามที่พบบ่อย'); ?>

<?php $__env->startSection('page-title'); ?>
    <div class="text-2xl font-bold">จัดการคำถามที่พบบ่อย (FAQ)</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-gray-800 rounded-lg shadow-lg p-6">
    <div class="flex justify-end mb-4">
        <a href="<?php echo e(route('admin.faqs.create')); ?>" class="btn bg-emerald-600 hover:bg-emerald-700 text-white border-none">
            <i class="fas fa-plus mr-2"></i> เพิ่มคำถามใหม่
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr class="text-gray-300">
                    <th class="w-10">#</th>
                    <th>คำถาม</th>
                    <th>สถานะ</th>
                    <th class="text-right">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <tr class="hover:bg-gray-700">
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e($faq->question); ?></td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($faq->is_active): ?>
                                <span class="badge badge-success">เปิดใช้งาน</span>
                            <?php else: ?>
                                <span class="badge badge-error">ปิดใช้งาน</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="text-right">
                            <a href="<?php echo e(route('admin.faqs.edit', $faq->id)); ?>" class="btn btn-sm btn-warning text-white">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('admin.faqs.destroy', $faq->id)); ?>" method="POST" class="inline-block" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบคำถามนี้?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-error text-white">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-400">
                            ยังไม่มีคำถามที่พบบ่อย
                        </td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/faqs/index.blade.php ENDPATH**/ ?>