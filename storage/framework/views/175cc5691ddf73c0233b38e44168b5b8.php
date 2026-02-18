<?php $__env->startSection('title', 'จัดการคำสั่งซื้อ'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto px-4 sm:px-8 py-8">

        
        <div class="flex flex-col md:flex-row justify-between items-end gap-4 mb-8">
            <div>
                <h2 class="text-3xl font-bold text-white tracking-tight">รายการคำสั่งซื้อ (Orders)</h2>
                <p class="text-gray-400 mt-2 text-sm">ตรวจสอบและจัดการรายการสั่งซื้อทั้งหมดในระบบ</p>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="px-4 py-2 bg-gray-800 rounded-lg border border-gray-700 text-gray-300 text-sm shadow-sm">
                    <i class="fas fa-list-ul mr-2 text-emerald-500"></i> ทั้งหมด: <span
                        class="font-bold text-white"><?php echo e($orders->total()); ?></span> รายการ
                </span>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div
                class="mb-6 flex items-center gap-3 rounded-xl bg-green-500/10 border border-green-500/20 p-4 text-green-400 backdrop-blur-sm shadow-lg">
                <i class="fas fa-check-circle text-xl"></i>
                <span class="font-medium"><?php echo e(session('success')); ?></span>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="bg-gray-800 rounded-2xl shadow-xl border border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap text-left">
                    <thead>
                        <tr
                            class="bg-gray-900/50 text-gray-400 text-xs uppercase tracking-wider font-semibold border-b border-gray-700">
                            <th class="px-6 py-4">รหัสออเดอร์</th>
                            <th class="px-6 py-4">วันที่สั่งซื้อ</th>
                            <th class="px-6 py-4">ข้อมูลลูกค้า</th>
                            <th class="px-6 py-4 text-right">ยอดรวม</th>
                            <th class="px-6 py-4 text-center">สถานะ</th>
                            <th class="px-6 py-4 text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <tr class="group hover:bg-gray-700/30 transition-colors duration-200">
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-700 text-gray-400 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <span class="font-mono font-bold text-blue-400 group-hover:underline">
                                            <?php echo e($order->ord_code); ?>

                                        </span>
                                    </div>
                                </td>

                                
                                <td class="px-6 py-4">
                                    <div class="flex flex-col text-sm">
                                        <span class="text-gray-200 font-medium">
                                            <?php echo e(\Carbon\Carbon::parse($order->ord_date)->format('d/m/Y')); ?>

                                        </span>
                                        <span class="text-gray-500 text-xs">
                                            <?php echo e(\Carbon\Carbon::parse($order->ord_date)->format('H:i')); ?> น.
                                        </span>
                                    </div>
                                </td>

                                
                                <td class="px-6 py-4 text-sm text-gray-300">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-user-circle text-gray-500 text-lg"></i>
                                        
                                        <span><?php echo e($order->user->name ?? ($order->shipping_phone ?? 'Guest')); ?></span>
                                    </div>
                                </td>

                                
                                <td class="px-6 py-4 text-right">
                                    <span class="font-mono font-bold text-emerald-400 text-lg">
                                        ฿<?php echo e(number_format($order->total_price, 2)); ?>

                                    </span>
                                </td>

                                
                                <td class="px-6 py-4 text-center">
                                    <?php
                                        // ตั้งค่าสีป้ายสถานะ (Badge)
                                        $statusConfig = match ($order->status_id) {
                                            1 => [
                                                'class' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
                                                'icon' => 'far fa-clock',
                                                'label' => 'รอชำระเงิน',
                                            ],
                                            2 => [
                                                'class' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                                'icon' => 'fas fa-check',
                                                'label' => 'ชำระแล้ว',
                                            ],
                                            3 => [
                                                'class' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
                                                'icon' => 'fas fa-box-open',
                                                'label' => 'เตรียมจัดส่ง',
                                            ],
                                            4 => [
                                                'class' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                                'icon' => 'fas fa-shipping-fast',
                                                'label' => 'จัดส่งแล้ว',
                                            ],
                                            5 => [
                                                'class' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                                'icon' => 'fas fa-times',
                                                'label' => 'ยกเลิก',
                                            ],
                                            default => [
                                                'class' => 'bg-gray-500/10 text-gray-400 border-gray-500/20',
                                                'icon' => 'fas fa-question',
                                                'label' => $order->status,
                                            ],
                                        };

                                        // เผื่อกรณีเก็บ status เป็น string (pending, paid)
                                        if ($order->status == 'pending') {
                                            $statusConfig = [
                                                'class' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
                                                'icon' => 'far fa-clock',
                                                'label' => 'รอชำระเงิน',
                                            ];
                                        }
                                        if ($order->status == 'paid') {
                                            $statusConfig = [
                                                'class' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                                'icon' => 'fas fa-check',
                                                'label' => 'ชำระแล้ว',
                                            ];
                                        }
                                    ?>

                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border <?php echo e($statusConfig['class']); ?>">
                                        <i class="<?php echo e($statusConfig['icon']); ?>"></i> <?php echo e($statusConfig['label']); ?>

                                    </span>
                                </td>

                                
                                <td class="px-6 py-4 text-center">
                                    <a href="<?php echo e(route('orders.show', $order->ord_code)); ?>"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-gray-700 text-gray-400 hover:bg-emerald-600 hover:text-white hover:shadow-lg hover:shadow-emerald-500/30 transition-all duration-200"
                                        title="ดูรายละเอียด">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            
                            <tr>
                                <td colspan="6" class="py-20 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <div class="mb-4 rounded-full bg-gray-700/50 p-6">
                                            <i class="fas fa-box-open text-4xl opacity-50"></i>
                                        </div>
                                        <p class="text-lg font-medium">ยังไม่มีรายการสั่งซื้อเข้ามา</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders->hasPages()): ?>
                <div class="border-t border-gray-700 bg-gray-800 px-6 py-4">
                    <?php echo e($orders->links()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>