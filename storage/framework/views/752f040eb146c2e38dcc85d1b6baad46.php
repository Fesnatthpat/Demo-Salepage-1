

<?php $__env->startSection('title', 'จัดการติดต่อเรา'); ?>

<?php $__env->startSection('page-title'); ?>
    <div class="text-2xl font-bold">จัดการข้อมูลติดต่อเรา</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-gray-800 rounded-lg shadow-lg p-6">
    <div class="flex justify-end mb-4">
        <a href="<?php echo e(route('admin.contacts.create')); ?>" class="btn bg-emerald-600 hover:bg-emerald-700 text-white border-none">
            <i class="fas fa-plus mr-2"></i> เพิ่มข้อมูลติดต่อ
        </a>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success mb-4">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr class="text-gray-300">
                    <th class="w-10">#</th>
                    <th>หัวข้อ</th>
                    <th>เบอร์โทร</th>
                    <th>อีเมล</th>
                    <th>สถานะ</th>
                    <th class="text-right">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <tr class="hover:bg-gray-700">
                        <td><?php echo e($contact->sort_order); ?></td>
                        <td><?php echo e($contact->title); ?></td>
                        <td><?php echo e($contact->phone); ?></td>
                        <td><?php echo e($contact->email); ?></td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($contact->is_active): ?>
                                <span class="badge badge-success">เปิดใช้งาน</span>
                            <?php else: ?>
                                <span class="badge badge-error">ปิดใช้งาน</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="text-right">
                            <a href="<?php echo e(route('admin.contacts.edit', $contact->id)); ?>" class="btn btn-sm btn-warning text-white">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('admin.contacts.destroy', $contact->id)); ?>" method="POST" class="inline-block" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลนี้?')">
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
                        <td colspan="6" class="text-center py-8 text-gray-400">
                            ยังไม่มีข้อมูลติดต่อ
                        </td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/contacts/index.blade.php ENDPATH**/ ?>