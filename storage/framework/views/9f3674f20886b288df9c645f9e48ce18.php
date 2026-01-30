<?php $__env->startSection('title', 'Admin Dashboard'); ?>
<?php $__env->startSection('page-title', 'Overview Dashboard'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .filter-pill {
            transition: all 0.2s ease;
        }

        .filter-pill:hover {
            transform: translateY(-1px);
        }

        .filter-pill.active {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(67, 56, 202, 0.3);
        }

        .stat-card {
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
        }

        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">ยินดีต้อนรับกลับ, Admin 👋</h1>
            <p class="text-sm text-gray-500 mt-1">นี่คือภาพรวมร้านค้าของคุณในช่วงเวลาที่เลือก</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 bg-white p-1.5 rounded-xl shadow-sm border border-gray-100">
            <a href="<?php echo e(route('admin.dashboard', ['period' => 'today'])); ?>"
                class="filter-pill px-4 py-2 text-sm font-medium rounded-lg <?php echo e($period == 'today' ? 'active text-white' : 'text-gray-600 hover:bg-gray-50'); ?>">
                วันนี้
            </a>
            <a href="<?php echo e(route('admin.dashboard', ['period' => 'last_7_days'])); ?>"
                class="filter-pill px-4 py-2 text-sm font-medium rounded-lg <?php echo e($period == 'last_7_days' ? 'active text-white' : 'text-gray-600 hover:bg-gray-50'); ?>">
                7 วัน
            </a>
            <a href="<?php echo e(route('admin.dashboard', ['period' => 'this_month'])); ?>"
                class="filter-pill px-4 py-2 text-sm font-medium rounded-lg <?php echo e($period == 'this_month' ? 'active text-white' : 'text-gray-600 hover:bg-gray-50'); ?>">
                เดือนนี้
            </a>
            <a href="<?php echo e(route('admin.dashboard', ['period' => 'last_30_days'])); ?>"
                class="filter-pill px-4 py-2 text-sm font-medium rounded-lg <?php echo e($period == 'last_30_days' ? 'active text-white' : 'text-gray-600 hover:bg-gray-50'); ?>">
                30 วัน
            </a>
        </div>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="stat-card bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">ยอดขายรวม</p>
                    <h3 class="text-3xl font-bold text-gray-800">฿<?php echo e(number_format($totalSales, 0)); ?></h3>
                </div>
                <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <span class="text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full font-medium">
                    <i class="fas fa-chart-line mr-1"></i> รายรับ
                </span>
                <span class="text-gray-400 ml-2">ในช่วงเวลาที่เลือก</span>
            </div>
        </div>

        
        <div class="stat-card bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">จำนวนออเดอร์</p>
                    <h3 class="text-3xl font-bold text-gray-800"><?php echo e(number_format($totalOrders)); ?></h3>
                </div>
                <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                    <i class="fas fa-shopping-bag text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <span class="text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full font-medium">
                    รายการ
                </span>
                <span class="text-gray-400 ml-2">ออเดอร์ทั้งหมด</span>
            </div>
        </div>

        
        <div class="stat-card bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">ยอดต่อออเดอร์</p>
                    <h3 class="text-3xl font-bold text-gray-800">฿<?php echo e(number_format($avgOrderValue, 0)); ?></h3>
                </div>
                <div class="p-3 bg-purple-50 rounded-xl text-purple-600">
                    <i class="fas fa-receipt text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <span class="text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full font-medium">
                    เฉลี่ย
                </span>
                <span class="text-gray-400 ml-2">บาท / 1 คำสั่งซื้อ</span>
            </div>
        </div>

        
        <div class="stat-card bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">ลูกค้าใหม่</p>
                    <h3 class="text-3xl font-bold text-gray-800"><?php echo e(number_format($newCustomers)); ?></h3>
                </div>
                <div class="p-3 bg-orange-50 rounded-xl text-orange-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <span class="text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full font-medium">
                    คน
                </span>
                <span class="text-gray-400 ml-2">สมัครสมาชิกใหม่</span>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-gray-800 text-lg flex items-center">
                    <span class="w-2 h-6 bg-indigo-500 rounded-full mr-3"></span>
                    แนวโน้มยอดขาย
                </h3>
            </div>
            <div class="relative h-80">
                <canvas id="salesOverTimeChart"></canvas>
            </div>
        </div>

        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-gray-800 text-lg flex items-center">
                    <span class="w-2 h-6 bg-pink-500 rounded-full mr-3"></span>
                    สถานะออเดอร์
                </h3>
            </div>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="orderStatusChart"></canvas>
            </div>
            <div class="mt-6 space-y-2">
                
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

        
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-lg">รายการสั่งซื้อล่าสุด</h3>
                <a href="<?php echo e(route('admin.dashboard.export')); ?>"
                    class="text-sm text-emerald-600 hover:text-emerald-700 font-medium flex items-center">
                    <i class="fas fa-download mr-1"></i> Export CSV
                </a>
            </div>
            <div class="overflow-x-auto custom-scroll">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs text-gray-500 border-b border-gray-100 bg-gray-50/50">
                            <th class="px-6 py-4 font-medium uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-4 font-medium uppercase tracking-wider">ลูกค้า</th>
                            <th class="px-6 py-4 font-medium uppercase tracking-wider">ยอดรวม</th>
                            <th class="px-6 py-4 font-medium uppercase tracking-wider">สถานะ</th>
                            <th class="px-6 py-4 font-medium uppercase tracking-wider">วันที่</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">
                                <td class="px-6 py-4 font-medium text-indigo-600">
                                    <a href="<?php echo e(route('admin.orders.show', $order->id)); ?>">#<?php echo e($order->ord_code); ?></a>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-500">
                                            <?php echo e(substr($order->user->name ?? 'G', 0, 1)); ?>

                                        </div>
                                        <span class="text-gray-700"><?php echo e($order->user->name ?? 'Guest'); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-700">
                                    ฿<?php echo e(number_format($order->net_amount, 2)); ?>

                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                        $statusConfig = [
                                            1 => ['label' => 'รอชำระ', 'class' => 'bg-yellow-100 text-yellow-700'],
                                            2 => ['label' => 'ชำระแล้ว', 'class' => 'bg-blue-100 text-blue-700'],
                                            3 => ['label' => 'เตรียมส่ง', 'class' => 'bg-indigo-100 text-indigo-700'],
                                            4 => ['label' => 'ส่งแล้ว', 'class' => 'bg-emerald-100 text-emerald-700'],
                                            5 => ['label' => 'ยกเลิก', 'class' => 'bg-red-100 text-red-700'],
                                        ];
                                        $status = $statusConfig[$order->status_id] ?? [
                                            'label' => 'Unknown',
                                            'class' => 'bg-gray-100 text-gray-600',
                                        ];
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo e($status['class']); ?>">
                                        <?php echo e($status['label']); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    <?php echo e($order->created_at->format('d M H:i')); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-3 block opacity-30"></i>
                                    ยังไม่มีรายการสั่งซื้อในช่วงนี้
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 text-lg mb-6">สินค้าขายดี 5 อันดับ</h3>
            <div class="space-y-6">
                <?php $__empty_1 = true; $__currentLoopData = $topSellingProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $product = $item->productSalepage;
                        $img = $product ? $product->images->first() : null;
                        $imgPath = $img ? asset('storage/' . $img->img_path) : 'https://via.placeholder.com/150';
                    ?>
                    <div class="flex items-center gap-4">
                        <div class="relative w-12 h-12 flex-shrink-0">
                            <img src="<?php echo e($imgPath); ?>"
                                class="w-full h-full object-cover rounded-lg border border-gray-100">
                            <div
                                class="absolute -top-2 -left-2 w-5 h-5 bg-gray-800 text-white text-[10px] flex items-center justify-center rounded-full border-2 border-white shadow-sm font-bold">
                                <?php echo e($index + 1); ?>

                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between mb-1">
                                <span
                                    class="text-sm font-medium text-gray-700 truncate pr-2"><?php echo e($product->pd_sp_name ?? 'Product Deleted'); ?></span>
                                <span class="text-sm font-bold text-gray-900"><?php echo e(number_format($item->total_quantity)); ?>

                                    ชิ้น</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="bg-indigo-500 h-1.5 rounded-full" style="width: <?php echo e($item->percent); ?>%"></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-8 text-gray-400">
                        ไม่มีข้อมูลสินค้าขายดี
                    </div>
                <?php endif; ?>
            </div>
            <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                <a href="<?php echo e(route('admin.products.index')); ?>"
                    class="text-sm text-indigo-600 font-medium hover:text-indigo-800">
                    ดูสินค้าทั้งหมด &rarr;
                </a>
            </div>
        </div>

    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setup defaults for better looking charts
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#6b7280';

            // 1. Sales Chart with Gradient
            const salesCtx = document.getElementById('salesOverTimeChart');
            if (salesCtx) {
                const ctx = salesCtx.getContext('2d');
                // Create Gradient
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)'); // Indigo-600
                gradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode($salesChartLabels, 15, 512) ?>,
                        datasets: [{
                            label: 'ยอดขาย (บาท)',
                            data: <?php echo json_encode($salesChartValues, 15, 512) ?>,
                            backgroundColor: gradient,
                            borderColor: '#4f46e5',
                            borderWidth: 2,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#4f46e5',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4 // Smooth curve
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#1f2937',
                                padding: 12,
                                titleFont: {
                                    size: 13
                                },
                                bodyFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return '฿ ' + new Intl.NumberFormat('th-TH').format(context
                                        .raw);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    borderDash: [2, 4],
                                    color: '#f3f4f6',
                                    drawBorder: false
                                },
                                ticks: {
                                    padding: 10
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    padding: 10
                                }
                            }
                        }
                    }
                });
            }

            // 2. Order Status Chart (Doughnut)
            const statusCtx = document.getElementById('orderStatusChart');
            const statusData = <?php echo json_encode($orderStatusBreakdown, 15, 512) ?>;

            if (statusCtx && Object.keys(statusData).length > 0) {
                const labelsMap = {
                    1: 'รอชำระ',
                    2: 'ชำระแล้ว',
                    3: 'เตรียมส่ง',
                    4: 'ส่งแล้ว',
                    5: 'ยกเลิก'
                };

                // Color Palette
                const colors = {
                    1: '#fbbf24', // Amber
                    2: '#60a5fa', // Blue
                    3: '#818cf8', // Indigo
                    4: '#34d399', // Emerald
                    5: '#f87171' // Red
                };

                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(statusData).map(k => labelsMap[k] || 'Other'),
                        datasets: [{
                            data: Object.values(statusData),
                            backgroundColor: Object.keys(statusData).map(k => colors[k] ||
                                '#9ca3af'),
                            borderWidth: 0,
                            hoverOffset: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%', // Thinner ring
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1f2937',
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.chart._metasets[context.datasetIndex]
                                            .total;
                                        const percentage = Math.round((value / total) * 100) + '%';
                                        return `${label}: ${value} (${percentage})`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>