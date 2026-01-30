<?php $__env->startSection('title', 'รายละเอียดลูกค้า'); ?>
<?php $__env->startSection('page-title'); ?>
    <a href="<?php echo e(route('admin.customers.index')); ?>" class="text-gray-400 hover:text-emerald-400 transition-colors">ลูกค้า</a> /
    <span class="text-gray-100 font-medium">รายละเอียดลูกค้า: <?php echo e($customer->name); ?></span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card bg-gray-800 shadow-lg border border-gray-700">
        <div class="card-body">
            <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
                <h2 class="card-title text-gray-100">ข้อมูลลูกค้า</h2>
                <a href="<?php echo e(route('admin.customers.index')); ?>"
                    class="btn btn-sm btn-ghost text-gray-400 hover:text-white hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    กลับ
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-300">
                
                <div>
                    <p class="text-sm text-gray-500">ชื่อ</p>
                    <p class="text-lg font-bold text-gray-100"><?php echo e($customer->name); ?></p>
                </div>

                
                <div>
                    <p class="text-sm text-gray-500">อีเมล</p>
                    <div class="flex items-center justify-start gap-2">
                        <span class="text-lg font-bold text-gray-100"><?php echo e($customer->email ?? '-'); ?></span>
                        <?php if($customer->email): ?>
                            <button onclick="copyToClipboard('<?php echo e($customer->email); ?>', this)"
                                class="btn btn-xs btn-circle btn-ghost text-gray-500 hover:text-emerald-400 hover:bg-gray-700"
                                title="คัดลอกอีเมล">
                                <i class="fas fa-copy"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div>
                    <p class="text-sm text-gray-500">เบอร์โทรศัพท์</p>
                    <div class="flex items-center justify-start gap-2">
                        <span class="text-lg font-bold text-gray-100"><?php echo e($customer->phone ?? '-'); ?></span>
                        <?php if($customer->phone): ?>
                            <button onclick="copyToClipboard('<?php echo e($customer->phone); ?>', this)"
                                class="btn btn-xs btn-circle btn-ghost text-gray-500 hover:text-emerald-400 hover:bg-gray-700"
                                title="คัดลอกเบอร์โทรศัพท์">
                                <i class="fas fa-copy"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <p class="text-sm text-gray-500">อายุ</p>
                    <p class="text-lg font-bold text-gray-100"><?php echo e($customer->age ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">เพศ</p>
                    <p class="text-lg font-bold text-gray-100"><?php echo e($customer->gender ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">วันเกิด</p>
                    <p class="text-lg font-bold text-gray-100">
                        <?php echo e($customer->date_of_birth ? \Carbon\Carbon::parse($customer->date_of_birth)->format('d M Y') : '-'); ?>

                    </p>
                </div>

                
                <div>
                    <p class="text-sm text-gray-500">สถานะ LINE</p>
                    <div class="text-lg font-bold flex items-center justify-start gap-2">
                        <?php if($customer->line_id): ?>
                            <span class="badge badge-success text-white">เชื่อมต่อแล้ว</span>
                            <div class="tooltip" data-tip="<?php echo e($customer->line_id); ?>">
                                <button onclick="copyToClipboard('<?php echo e($customer->line_id); ?>', this)"
                                    class="btn btn-xs btn-circle btn-ghost text-gray-500 hover:text-emerald-400 hover:bg-gray-700">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        <?php else: ?>
                            <span class="badge badge-warning bg-yellow-600 border-none text-white">ไม่ได้เชื่อมต่อ</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <p class="text-sm text-gray-500">วันที่ลงทะเบียน</p>
                    <p class="text-lg font-bold text-gray-100"><?php echo e($customer->created_at->format('d M Y, H:i')); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">อัปเดตล่าสุด</p>
                    <p class="text-lg font-bold text-gray-100"><?php echo e($customer->updated_at->format('d M Y, H:i')); ?></p>
                </div>
            </div>

            <div class="divider border-gray-700 my-6"></div>

            <h3 class="card-title mb-4 text-gray-100">ออเดอร์ล่าสุดของลูกค้า</h3>
            <div class="overflow-x-auto">
                <table class="table w-full text-gray-300">
                    <thead>
                        <tr class="border-b border-gray-700 bg-gray-900/50 text-gray-400">
                            <th>รหัสออเดอร์</th>
                            <th class="text-right">ยอดสุทธิ</th>
                            <th class="text-center">สถานะ</th>
                            <th>วันที่สั่งซื้อ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $customer->orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-700/50 transition-colors border-b border-gray-700 last:border-0">
                                <td class="font-mono text-emerald-400"><?php echo e($order->ord_code); ?></td>
                                <td class="text-right font-bold text-gray-200">฿<?php echo e(number_format($order->net_amount, 2)); ?>

                                </td>
                                <td class="text-center">
                                    <?php
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
                                        class="badge border-none text-white
                                    <?php switch($order->status_id):
                                        case (1): ?> bg-yellow-600 <?php break; ?>
                                        <?php case (2): ?> bg-blue-600 <?php break; ?>
                                        <?php case (3): ?> bg-indigo-600 <?php break; ?>
                                        <?php case (4): ?> bg-emerald-600 <?php break; ?>
                                        <?php case (5): ?> bg-red-600 <?php break; ?>
                                        <?php default: ?> bg-gray-600
                                    <?php endswitch; ?>">
                                        <?php echo e($statusText); ?>

                                    </span>
                                </td>
                                <td class="text-gray-400"><?php echo e($order->ord_date->format('d M Y, H:i')); ?></td>
                                <td>
                                    <a href="<?php echo e(route('admin.orders.show', $order)); ?>"
                                        class="btn btn-ghost btn-sm text-gray-400 hover:text-emerald-400">
                                        <i class="fas fa-eye mr-2"></i>
                                        รายละเอียด
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center py-8 text-gray-500">
                                    ลูกค้ารายนี้ยังไม่มีออเดอร์
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    
    <script>
        function copyToClipboard(text, btn) {
            navigator.clipboard.writeText(text).then(function() {
                let originalContent = btn.innerHTML;

                btn.classList.remove('text-gray-500', 'hover:text-emerald-400');
                btn.classList.add('text-emerald-500');
                btn.innerHTML = '<i class="fas fa-check"></i>';

                setTimeout(function() {
                    btn.classList.remove('text-emerald-500');
                    btn.classList.add('text-gray-500', 'hover:text-emerald-400');
                    btn.innerHTML = originalContent;
                }, 2000);
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/customers/show.blade.php ENDPATH**/ ?>