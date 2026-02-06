<?php $__env->startSection('title', 'จัดการลูกค้า'); ?>
<?php $__env->startSection('page-title', 'รายชื่อลูกค้าทั้งหมด'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card bg-gray-800 shadow-lg border border-gray-700">
        <div class="card-body">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                <h2 class="card-title text-gray-100">ลูกค้าทั้งหมด <span
                        class="text-gray-500 text-sm font-normal">(<?php echo e($customers->total()); ?>)</span></h2>
                <form action="<?php echo e(route('admin.customers.index')); ?>" method="GET">
                    <div class="form-control">
                        <div class="relative">
                            <input type="text" name="search" placeholder="ค้นหาชื่อ, อีเมล, เบอร์โทร..."
                                class="input input-bordered w-full sm:w-64 pr-10 bg-gray-700 border-gray-600 text-gray-200 placeholder-gray-400 focus:border-emerald-500 focus:ring-emerald-500"
                                value="<?php echo e(request('search')); ?>">
                            <button type="submit"
                                class="absolute top-0 right-0 rounded-l-none btn btn-square btn-primary bg-emerald-600 hover:bg-emerald-700 border-none text-white">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full text-gray-300">
                    <thead>
                        <tr class="border-b border-gray-700 bg-gray-900/50 text-gray-400">
                            <th>#</th>
                            <th>ชื่อ</th>
                            <th>อีเมล</th>
                            <th>เบอร์โทร</th>
                            <th>อายุ</th>
                            <th>เพศ</th>
                            <th>วันเกิด</th>
                            <th>สถานะ LINE</th>
                            <th>วันที่ลงทะเบียน</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <tr class="hover:bg-gray-700/50 transition-colors border-b border-gray-700 last:border-0">
                                <td class="text-gray-500"><?php echo e($customer->id); ?></td>
                                <td>
                                    <div class="font-bold text-gray-200"><?php echo e($customer->name); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo e($customer->line_id ? 'LINE Linked' : 'No LINE'); ?>

                                    </div>
                                </td>
                                <td class="text-gray-400"><?php echo e($customer->email); ?></td>
                                <td class="text-gray-400"><?php echo e($customer->phone ?? 'N/A'); ?></td>
                                <td class="text-gray-400"><?php echo e($customer->age ?? 'N/A'); ?></td>
                                <td class="text-gray-400"><?php echo e($customer->gender ?? 'N/A'); ?></td>
                                <td class="text-gray-400">
                                    <?php echo e($customer->date_of_birth ? \Carbon\Carbon::parse($customer->date_of_birth)->format('d M Y') : 'N/A'); ?>

                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($customer->line_id): ?>
                                        <span class="badge badge-success text-white">เชื่อมต่อแล้ว</span>
                                    <?php else: ?>
                                        <span
                                            class="badge badge-warning bg-yellow-600 border-none text-white">ไม่ได้เชื่อมต่อ</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="text-gray-500"><?php echo e($customer->created_at->format('d M Y, H:i')); ?></td>
                                <td>
                                    <a href="<?php echo e(route('admin.customers.show', $customer)); ?>"
                                        class="btn btn-ghost btn-sm text-gray-400 hover:text-emerald-400">
                                        <i class="fas fa-eye mr-2"></i>
                                        รายละเอียด
                                    </a>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="10" class="text-center py-12 text-gray-500">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('search')): ?>
                                        ไม่พบลูกค้าที่ตรงกับคำค้นหา "<?php echo e(request('search')); ?>"
                                    <?php else: ?>
                                        ยังไม่มีข้อมูลลูกค้าในระบบ
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-8">
                <?php echo e($customers->appends(request()->query())->links()); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/customers/index.blade.php ENDPATH**/ ?>