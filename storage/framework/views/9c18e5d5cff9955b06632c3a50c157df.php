<?php $__env->startSection('title', 'จัดการลูกค้า'); ?>
<?php $__env->startSection('page-title', 'รายชื่อลูกค้าทั้งหมด'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card bg-white shadow-md">
        <div class="card-body">
            <!-- Header & Search -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                <h2 class="card-title">ลูกค้าทั้งหมด (<?php echo e($customers->total()); ?>)</h2>
                <form action="<?php echo e(route('admin.customers.index')); ?>" method="GET">
                    <div class="form-control">
                        <div class="relative">
                            <input type="text" name="search" placeholder="ค้นหาชื่อ, อีเมล, เบอร์โทร..."
                                class="input input-bordered w-full sm:w-64 pr-10" value="<?php echo e(request('search')); ?>">
                            <button type="submit" class="absolute top-0 right-0 rounded-l-none btn btn-square btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Customers Table -->
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
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
                        <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover">
                                <td><?php echo e($customer->id); ?></td>
                                <td>
                                    <div class="font-bold"><?php echo e($customer->name); ?></div>
                                    <div class="text-sm opacity-50"><?php echo e($customer->line_id ? 'LINE Linked' : 'No LINE'); ?>

                                    </div>
                                </td>
                                <td><?php echo e($customer->email); ?></td>
                                <td><?php echo e($customer->phone ?? 'N/A'); ?></td>
                                <td><?php echo e($customer->age ?? 'N/A'); ?></td>
                                <td><?php echo e($customer->gender ?? 'N/A'); ?></td>
                                <td><?php echo e($customer->date_of_birth ? \Carbon\Carbon::parse($customer->date_of_birth)->format('d M Y') : 'N/A'); ?>

                                </td>
                                <td>
                                    <?php if($customer->line_id): ?>
                                        <span class="badge badge-success">เชื่อมต่อแล้ว</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">ไม่ได้เชื่อมต่อ</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($customer->created_at->format('d M Y, H:i')); ?></td>
                                <td>
                                    <a href="<?php echo e(route('admin.customers.show', $customer)); ?>" class="btn btn-ghost btn-sm">
                                        <i class="fas fa-eye mr-2"></i>
                                        รายละเอียด
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="10" class="text-center py-8 text-gray-500">
                                    <?php if(request('search')): ?>
                                        ไม่พบลูกค้าที่ตรงกับคำค้นหา "<?php echo e(request('search')); ?>"
                                    <?php else: ?>
                                        ยังไม่มีข้อมูลลูกค้าในระบบ
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                <?php echo e($customers->appends(request()->query())->links()); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/customers/index.blade.php ENDPATH**/ ?>