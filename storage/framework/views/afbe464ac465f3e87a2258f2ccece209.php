<?php $__env->startSection('title', 'รายละเอียดลูกค้า'); ?>
<?php $__env->startSection('page-title'); ?>
    <a href="<?php echo e(route('admin.customers.index')); ?>" class="text-gray-500 hover:text-gray-900">ลูกค้า</a> /
    <span class="text-gray-900">รายละเอียดลูกค้า: <?php echo e($customer->name); ?></span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card bg-white shadow-md">
        <div class="card-body">
            <div class="flex justify-between items-center mb-6">
                <h2 class="card-title">ข้อมูลลูกค้า</h2>
                <a href="<?php echo e(route('admin.customers.index')); ?>" class="btn btn-sm btn-ghost">
                    <i class="fas fa-arrow-left mr-2"></i>
                    กลับ
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">ชื่อ</p>
                    <p class="text-lg font-bold text-gray-800"><?php echo e($customer->name); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">อีเมล</p>
                    <p class="text-lg font-bold text-gray-800"><?php echo e($customer->email ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">เบอร์โทรศัพท์</p>
                    <p class="text-lg font-bold text-gray-800"><?php echo e($customer->phone ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">อายุ</p>
                    <p class="text-lg font-bold text-gray-800"><?php echo e($customer->age ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">เพศ</p>
                    <p class="text-lg font-bold text-gray-800"><?php echo e($customer->gender ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">วันเกิด</p>
                    <p class="text-lg font-bold text-gray-800">
                        <?php echo e($customer->date_of_birth ? \Carbon\Carbon::parse($customer->date_of_birth)->format('d M Y') : '-'); ?>

                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">สถานะ LINE</p>
                    <p class="text-lg font-bold text-gray-800">
                        <?php if($customer->line_id): ?>
                            <span class="badge badge-success">เชื่อมต่อแล้ว (LINE ID: <?php echo e($customer->line_id); ?>)</span>
                        <?php else: ?>
                            <span class="badge badge-warning">ไม่ได้เชื่อมต่อ</span>
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">วันที่ลงทะเบียน</p>
                    <p class="text-lg font-bold text-gray-800"><?php echo e($customer->created_at->format('d M Y, H:i')); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">อัปเดตล่าสุด</p>
                    <p class="text-lg font-bold text-gray-800"><?php echo e($customer->updated_at->format('d M Y, H:i')); ?></p>
                </div>
            </div>

            <div class="divider"></div>

            <h3 class="card-title mb-4">ออเดอร์ล่าสุดของลูกค้า</h3>
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>รหัสออเดอร์</th>
                            <th>ยอดสุทธิ</th>
                            <th>สถานะ</th>
                            <th>วันที่สั่งซื้อ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $customer->orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover">
                                <td class="font-mono"><?php echo e($order->ord_code); ?></td>
                                <td class="text-right">฿<?php echo e(number_format($order->net_amount, 2)); ?></td>
                                <td class="text-center">
                                    <?php
                                        // Assuming you have a status map like in AdminOrderController
                                        $statusMap = [
                                            1 => 'รอชำระเงิน',
                                            2 => 'แจ้งชำระเงินแล้ว',
                                            3 => 'กำลังเตรียมจัดส่ง',
                                            4 => 'จัดส่งแล้ว',
                                            5 => 'ยกเลิก',
                                        ];
                                        $statusText = $statusMap[$order->status_id] ?? 'ไม่ทราบสถานะ';
                                    ?>
                                    <span
                                        class="badge 
                                    <?php switch($order->status_id):
                                        case (1): ?> badge-warning <?php break; ?>
                                        <?php case (2): ?> badge-info <?php break; ?>
                                        <?php case (3): ?> badge-success <?php break; ?>
                                        <?php case (4): ?> badge-primary <?php break; ?>
                                        <?php default: ?> badge-ghost
                                    <?php endswitch; ?>
                                ">
                                        <?php echo e($statusText); ?>

                                    </span>
                                </td>
                                <td><?php echo e($order->ord_date->format('d M Y, H:i')); ?></td>
                                <td>
                                    <a href="<?php echo e(route('admin.orders.show', $order)); ?>" class="btn btn-ghost btn-sm">
                                        <i class="fas fa-eye mr-2"></i>
                                        รายละเอียด
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-gray-500">
                                    ลูกค้ารายนี้ยังไม่มีออเดอร์
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/customers/show.blade.php ENDPATH**/ ?>