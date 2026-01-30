<?php $__env->startSection('title', 'รายละเอียดออเดอร์'); ?>
<?php $__env->startSection('page-title'); ?>
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <a href="<?php echo e(route('admin.orders.index')); ?>" class="hover:text-emerald-400 transition-colors">
            <i class="fas fa-shopping-bag mr-1"></i> ออเดอร์
        </a>
        <span>/</span>
        <span class="text-gray-100 font-medium">รหัส: <?php echo e($order->ord_code); ?></span>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php if(session('success')): ?>
        <div class="alert alert-success bg-green-900/50 border-green-800 text-green-200 shadow-sm mb-6">
            <div>
                <i class="fas fa-check-circle"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-8">

            
            <div class="card bg-gray-800 shadow-lg border border-gray-700">
                <div class="card-body">
                    <h2 class="card-title text-gray-100 mb-4 border-b border-gray-700 pb-3">
                        <i class="fas fa-list-ul text-emerald-500 mr-2"></i> รายการสินค้า
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table w-full text-gray-300">
                            <thead>
                                <tr class="bg-gray-900/50 text-gray-400 border-b border-gray-700">
                                    <th>สินค้า</th>
                                    <th class="text-right">ราคาต่อหน่วย</th>
                                    <th class="text-center">จำนวน</th>
                                    <th class="text-right">ราคารวม</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $order->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="border-b border-gray-700 last:border-0 hover:bg-gray-700/30">
                                        <td>
                                            <div class="flex items-center space-x-3">
                                                <div class="avatar">
                                                    <div class="mask mask-squircle w-12 h-12 bg-gray-700">
                                                        <?php
                                                            $displayImage =
                                                                'https://via.placeholder.com/64?text=No+Image';
                                                            $product = $detail->productSalepage;
                                                            if ($product && $product->images->isNotEmpty()) {
                                                                $imgObj =
                                                                    $product->images->sortBy('img_sort')->first() ??
                                                                    $product->images->first();
                                                                $rawPath = $imgObj->img_path ?? $imgObj->image_path;
                                                                if ($rawPath) {
                                                                    $displayImage = filter_var(
                                                                        $rawPath,
                                                                        FILTER_VALIDATE_URL,
                                                                    )
                                                                        ? $rawPath
                                                                        : asset(
                                                                            'storage/' .
                                                                                ltrim(
                                                                                    str_replace(
                                                                                        'storage/',
                                                                                        '',
                                                                                        $rawPath,
                                                                                    ),
                                                                                    '/',
                                                                                ),
                                                                        );
                                                                }
                                                            }
                                                        ?>
                                                        <img src="<?php echo e($displayImage); ?>"
                                                            alt="<?php echo e($product->pd_sp_name ?? 'Product Image'); ?>"
                                                            class="object-cover w-full h-full"
                                                            onerror="this.src='https://via.placeholder.com/64?text=Error'">
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-200">
                                                        <?php echo e($product->pd_sp_name ?? 'สินค้าถูกลบไปแล้ว'); ?>

                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        SKU: <?php echo e($product->pd_code ?? '-'); ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <?php if($detail->pd_original_price > $detail->pd_price): ?>
                                                <div class="text-xs text-gray-500 line-through">
                                                    ฿<?php echo e(number_format($detail->pd_original_price, 2)); ?></div>
                                            <?php endif; ?>
                                            <span
                                                class="font-medium text-gray-300">฿<?php echo e(number_format($detail->pd_price, 2)); ?></span>
                                        </td>
                                        <td class="text-center font-mono text-gray-300"><?php echo e($detail->ordd_count); ?></td>
                                        <td class="text-right font-bold text-emerald-400">
                                            ฿<?php echo e(number_format($detail->pd_price * $detail->ordd_count, 2)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="divider border-gray-700 my-4"></div>
                    <div class="space-y-2 max-w-sm ml-auto bg-gray-900/50 p-4 rounded-lg border border-gray-700">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">ยอดรวมสินค้า</span>
                            <span class="font-semibold text-gray-200">฿<?php echo e(number_format($order->total_price, 2)); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">ค่าจัดส่ง</span>
                            <span class="font-semibold text-gray-200">฿<?php echo e(number_format($order->shipping_cost, 2)); ?></span>
                        </div>
                        <?php if($order->total_discount > 0): ?>
                            <div class="flex justify-between text-sm text-red-400">
                                <span>ส่วนลด</span>
                                <span class="font-semibold">-฿<?php echo e(number_format($order->total_discount, 2)); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="divider my-1 border-gray-700"></div>
                        <div class="flex justify-between text-lg font-bold text-emerald-400">
                            <span>ยอดสุทธิ</span>
                            <span>฿<?php echo e(number_format($order->net_amount, 2)); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="lg:col-span-1 space-y-6">
            
            <div class="card bg-gray-800 shadow-lg border border-gray-700">
                <div class="card-body">
                    <h2 class="card-title text-gray-100 text-base">
                        <i class="fas fa-tasks text-emerald-500 mr-2"></i> สถานะออเดอร์
                    </h2>
                    <form action="<?php echo e(route('admin.orders.updateStatus', $order)); ?>" method="POST" class="mt-4">
                        <?php echo csrf_field(); ?>
                        <div class="form-control w-full">
                            <div class="join w-full">
                                <select name="status_id"
                                    class="select select-bordered join-item flex-grow bg-gray-700 border-gray-600 text-gray-100 focus:outline-none focus:border-emerald-500">
                                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($id); ?>"
                                            <?php echo e($order->status_id == $id ? 'selected' : ''); ?>>
                                            <?php echo e($name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <button type="submit"
                                    class="btn btn-primary bg-emerald-600 hover:bg-emerald-700 border-none join-item text-white">บันทึก</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            
            <div class="card bg-gray-800 shadow-lg border border-gray-700">
                <div class="card-body">
                    <h2 class="card-title text-gray-100 text-base">
                        <i class="fas fa-user-circle text-emerald-500 mr-2"></i> ข้อมูลจัดส่ง
                    </h2>
                    <div class="space-y-3 text-sm mt-2 text-gray-300">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-user mt-1 text-gray-500 w-4"></i>
                            <div>
                                <p class="font-bold text-gray-100"><?php echo e($order->shipping_name); ?></p>
                                <p class="text-xs text-gray-500">User Email: <?php echo e($order->user->email ?? '-'); ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-phone text-gray-500 w-4"></i>
                            <p><?php echo e($order->shipping_phone); ?></p>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt mt-1 text-gray-500 w-4"></i>
                            <p class="whitespace-pre-line leading-relaxed text-gray-400"><?php echo e($order->shipping_address); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if($order->slip_path): ?>
                <div class="card bg-gray-800 shadow-lg border border-gray-700">
                    <div class="card-body">
                        <h2 class="card-title text-gray-100 text-base">
                            <i class="fas fa-receipt text-emerald-500 mr-2"></i> หลักฐานการโอน
                        </h2>
                        <div class="mt-4 rounded-lg overflow-hidden border border-gray-600 bg-gray-900">
                            <?php
                                $slipUrl = '';
                                if (filter_var($order->slip_path, FILTER_VALIDATE_URL)) {
                                    $slipUrl = $order->slip_path;
                                } else {
                                    $cleanSlipPath = ltrim(str_replace('storage/', '', $order->slip_path), '/');
                                    $slipUrl = asset('storage/' . $cleanSlipPath);
                                }
                            ?>

                            <a href="<?php echo e($slipUrl); ?>" target="_blank" class="block hover:opacity-90 transition">
                                <img src="<?php echo e($slipUrl); ?>" alt="Payment Slip" class="w-full object-contain"
                                    onerror="this.src='https://via.placeholder.com/300?text=Slip+Error'">
                            </a>
                        </div>
                        <p class="text-center text-xs text-gray-500 mt-2">คลิกที่รูปเพื่อดูภาพขยาย</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/orders/show.blade.php ENDPATH**/ ?>