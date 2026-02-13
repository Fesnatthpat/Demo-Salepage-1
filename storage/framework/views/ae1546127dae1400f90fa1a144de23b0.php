<?php $__env->startSection('title', 'รายละเอียดออเดอร์ | Salepage Demo'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        // Map status ID to text and color
        $statusMap = [
            1 => ['text' => 'รอชำระเงิน', 'class' => 'bg-yellow-100 text-yellow-800'],
            2 => ['text' => 'กำลังดำเนินการ', 'class' => 'bg-blue-100 text-blue-800'],
            3 => ['text' => 'จัดส่งแล้ว', 'class' => 'bg-green-100 text-green-800'],
            4 => ['text' => 'สำเร็จ', 'class' => 'bg-emerald-100 text-emerald-800'],
            5 => ['text' => 'ยกเลิก', 'class' => 'bg-red-100 text-red-800'],
        ];
        $statusInfo = $statusMap[$order->status_id] ?? ['text' => 'ไม่ระบุ', 'class' => 'bg-gray-100 text-gray-800'];
    ?>

    <div class="container mx-auto p-4 lg:px-20 lg:py-10 max-w-7xl">
        <div class="bg-white border border-gray-200 rounded-lg p-6 lg:p-8 shadow-sm">

            
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            รายละเอียดคำสั่งซื้อ
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">
                            หมายเลข: <?php echo e($order->ord_code); ?>

                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span
                            class="px-4 py-1.5 inline-flex text-sm leading-5 font-semibold rounded-full <?php echo e($statusInfo['class']); ?>">
                            <?php echo e($statusInfo['text']); ?>

                        </span>
                        <a href="<?php echo e(route('order.history')); ?>" class="btn btn-sm btn-ghost text-gray-600 hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            กลับ
                        </a>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-2">
                    วันที่สั่งซื้อ: <?php echo e($order->formatted_ord_date); ?> น.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">รายการสินค้า</h2>
                    <div class="space-y-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <div
                                class="flex justify-between items-start border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                <div class="flex items-center gap-4">
                                    <?php
                                        $displayImage = $detail->productSalepage->cover_image_url ?? 'https://via.placeholder.com/150?text=No+Image';
                                    ?>
                                    <div
                                        class="w-20 h-20 bg-gray-100 rounded-md overflow-hidden border border-gray-200 flex-shrink-0 relative">
                                        <img src="<?php echo e($displayImage); ?>" class="w-full h-full object-cover"
                                            alt="<?php echo e($detail->productSalepage->pd_sp_name ?? 'Product Image'); ?>"
                                            onerror="this.onerror=null;this.src='https://via.placeholder.com/150?text=Error';" />
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm md:text-base line-clamp-2">
                                            <?php echo e($detail->productSalepage->pd_sp_name ?? 'ไม่พบข้อมูลสินค้า'); ?>

                                        </p>
                                        

                                        
                                        <p class="text-sm text-gray-500">ราคาต่อชิ้น:
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $detail->ordd_price <= 0): ?>
                                                
                                                <span class="font-bold text-red-500 ml-1">ฟรี (0 บาท)</span>
                                            <?php elseif($detail->ordd_original_price > $detail->ordd_price): ?>
                                                
                                                <s
                                                    class="text-gray-400">฿<?php echo e(number_format($detail->ordd_original_price, 2)); ?></s>
                                                <span
                                                    class="font-semibold text-red-600 ml-1">฿<?php echo e(number_format($detail->ordd_price, 2)); ?></span>
                                            <?php else: ?>
                                                
                                                <span
                                                    class="text-gray-800">฿<?php echo e(number_format($detail->ordd_price, 2)); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </p>
                                        

                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $detail->ordd_price <= 0): ?>
                                        <p class="font-bold text-red-500">ฟรี</p>
                                    <?php else: ?>
                                        <p class="font-bold text-emerald-600">
                                            ฿<?php echo e(number_format($detail->ordd_price * $detail->ordd_count, 2)); ?>

                                        </p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>

                
                <div>
                    
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 mb-6">
                        <h3 class="font-bold text-gray-800 mb-3 text-base">ข้อมูลการจัดส่ง</h3>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p class="font-semibold text-gray-900"><?php echo e($order->shipping_name); ?></p>
                            <?php
                                $addressParts = explode("\nหมายเหตุ:", $order->shipping_address, 2);
                                $mainAddress = $addressParts[0];
                                $noteText = isset($addressParts[1]) ? trim($addressParts[1]) : null;
                            ?>
                            <p><?php echo nl2br(e($mainAddress)); ?></p>
                            <div class="divider my-2"></div>
                            <p class="max-h-20 overflow-y-auto"><span
                                    class="font-semibold text-gray-700">เบอร์โทรศัพท์:</span> <?php echo e($order->shipping_phone); ?>

                            </p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($noteText): ?>
                                <div class="divider my-2"></div>
                                <p class="max-h-20 overflow-y-auto"><span
                                        class="font-semibold text-gray-700">หมายเหตุ:</span> <?php echo e($noteText); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                        <h3 class="font-bold text-gray-800 mb-4 text-base">สรุปยอดชำระ</h3>
                        <div class="space-y-2 text-sm text-gray-600 mb-4">
                            <div class="flex justify-between">
                                <span>รวมการสั่งซื้อ</span>
                                <span class="font-medium text-gray-900">฿<?php echo e(number_format($order->total_price, 2)); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>ค่าจัดส่ง</span>
                                <span
                                    class="font-medium text-gray-900">฿<?php echo e(number_format($order->shipping_cost, 2)); ?></span>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->total_discount > 0): ?>
                                <div class="flex justify-between text-green-600">
                                    <span>ส่วนลด</span>
                                    <span>-฿<?php echo e(number_format($order->total_discount, 2)); ?></span>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="flex justify-between items-center border-t border-gray-200 pt-4">
                            <span class="font-bold text-gray-800">ยอดชำระทั้งหมด</span>
                            <?php if((float) $order->net_amount <= 0): ?>
                                <span class="font-bold text-red-500 text-xl">(แถมฟรี 0 บาท)</span>
                            <?php else: ?>
                                <span
                                    class="font-bold text-red-500 text-xl">฿<?php echo e(number_format($order->net_amount, 2)); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/orderdetail.blade.php ENDPATH**/ ?>