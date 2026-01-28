<?php $__env->startSection('title', 'Admin Dashboard'); ?>
<?php $__env->startSection('page-title', 'แดชบอร์ดภาพรวม'); ?>

<?php $__env->startSection('content'); ?>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="card bg-white border-l-4 border-emerald-500 shadow-md">
            <div class="card-body">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">ยอดขายทั้งหมด</p>
                        <p class="text-3xl font-bold text-gray-800">฿<?php echo e(number_format($stats['totalRevenue'], 2)); ?></p>
                    </div>
                    <div class="text-emerald-500">
                        <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card bg-white border-l-4 border-blue-500 shadow-md">
            <div class="card-body">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">ออเดอร์ทั้งหมด</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo e(number_format($stats['totalOrders'])); ?></p>
                    </div>
                    <div class="text-blue-500">
                        <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card bg-white border-l-4 border-yellow-500 shadow-md">
            <div class="card-body">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">ลูกค้าใหม่ (30 วัน)</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo e(number_format($stats['newCustomers'])); ?></p>
                    </div>
                    <div class="text-yellow-500">
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card bg-white border-l-4 border-red-500 shadow-md">
            <div class="card-body">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">ยอดสั่งซื้อเฉลี่ย</p>
                        <p class="text-3xl font-bold text-gray-800">฿<?php echo e(number_format($stats['averageOrderValue'], 2)); ?></p>
                    </div>
                    <div class="text-red-500">
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

        
        <div class="xl:col-span-2">
            <div class="card bg-white shadow-md">
                <div class="card-body">
                    <h2 class="card-title mb-4">ออเดอร์ล่าสุด</h2>
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>รหัสออเดอร์</th>
                                    <th>ลูกค้า</th>
                                    <th>ยอดรวม</th>
                                    <th>สถานะ</th>
                                    <th>วันที่</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="font-mono"><?php echo e($order->ord_code); ?></td>
                                        <td><?php echo e($order->shipping_name ?? ($order->user->name ?? 'N/A')); ?></td>
                                        <td>฿<?php echo e(number_format($order->net_amount, 2)); ?></td>
                                        <td>
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
                                                สถานะ <?php echo e($order->status_id); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e($order->ord_date->format('d/m/Y')); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-8 text-gray-500">ยังไม่มีออเดอร์</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="xl:col-span-1">
            <div class="card bg-white shadow-md">
                <div class="card-body">
                    <h2 class="card-title mb-4">สินค้าขายดี 5 อันดับ</h2>
                    <ul class="space-y-4">
                        <?php $__empty_1 = true; $__currentLoopData = $topSellingProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            
                            <?php
                                $displayImage = 'https://via.placeholder.com/150?text=No+Image'; // Default
                                $product = $item->productSalepage;

                                if ($product && $product->images->isNotEmpty()) {
                                    // พยายามหารูปแรก หรือรูปที่มี sort น้อยที่สุด
                                    $imgObj =
                                        $product->images->sortBy('img_sort')->first() ?? $product->images->first();

                                    // รองรับชื่อฟิลด์ทั้ง 2 แบบ
                                    $rawPath = $imgObj->img_path ?? $imgObj->image_path;

                                    if ($rawPath) {
                                        if (filter_var($rawPath, FILTER_VALIDATE_URL)) {
                                            $displayImage = $rawPath; // ใช้ URL ตรงๆ
                                        } else {
                                            // ลบ storage/ ออกก่อนกันพลาด แล้วเติมใหม่ด้วย asset()
                                            $cleanPath = ltrim(str_replace('storage/', '', $rawPath), '/');
                                            $displayImage = asset('storage/' . $cleanPath);
                                        }
                                    }
                                }
                            ?>

                            <li class="flex items-center space-x-4">
                                
                                <div class="avatar">
                                    <div class="rounded border w-12 h-12">
                                        <img src="<?php echo e($displayImage); ?>" alt="<?php echo e($product->pd_sp_name ?? 'Product'); ?>"
                                            class="object-cover w-full h-full"
                                            onerror="this.src='https://via.placeholder.com/150?text=Error'">
                                    </div>
                                </div>

                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800 line-clamp-1"
                                        title="<?php echo e($product->pd_sp_name ?? ''); ?>">
                                        <?php echo e($product->pd_sp_name ?? 'ไม่พบข้อมูลสินค้า'); ?>

                                    </p>
                                    <p class="text-sm text-gray-500">ขายแล้ว <?php echo e(number_format($item->total_sold)); ?> ชิ้น
                                    </p>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="text-center py-8 text-gray-500">ยังไม่มีข้อมูลสินค้าขายดี</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>