<?php $__env->startSection('title', 'Admin Dashboard'); ?>
<?php $__env->startSection('page-title', 'Overview Dashboard'); ?>

<?php $__env->startPush('styles'); ?>
    
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <style>
        .stat-card {
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-100">ยินดีต้อนรับกลับ, Admin 👋</h1>
            <p class="text-sm text-gray-400 mt-1">นี่คือภาพรวมร้านค้าของคุณในช่วงเวลาที่เลือก</p>
        </div>

        
        <div class="flex flex-wrap items-center gap-2 bg-gray-800 p-1.5 rounded-xl shadow-md border border-gray-700">
            <?php
                function getBtnClass($isActive)
                {
                    return $isActive
                        ? 'bg-emerald-600 text-white shadow-md'
                        : 'text-gray-400 hover:bg-gray-700 hover:text-white';
                }
                $commonClass = 'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200';
            ?>
            <a href="<?php echo e(route('admin.dashboard', ['period' => 'today'])); ?>"
                class="<?php echo e($commonClass); ?> <?php echo e(getBtnClass($period == 'today')); ?>">วันนี้</a>
            <a href="<?php echo e(route('admin.dashboard', ['period' => 'last_7_days'])); ?>"
                class="<?php echo e($commonClass); ?> <?php echo e(getBtnClass($period == 'last_7_days')); ?>">7 วัน</a>
            <a href="<?php echo e(route('admin.dashboard', ['period' => 'this_month'])); ?>"
                class="<?php echo e($commonClass); ?> <?php echo e(getBtnClass($period == 'this_month')); ?>">เดือนนี้</a>
            <a href="<?php echo e(route('admin.dashboard', ['period' => 'last_30_days'])); ?>"
                class="<?php echo e($commonClass); ?> <?php echo e(getBtnClass($period == 'last_30_days')); ?>">30 วัน</a>
        </div>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="stat-card bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">ยอดขายรวม</p>
                    <h3 class="text-3xl font-bold text-emerald-400">฿<?php echo e(number_format($totalSales, 0)); ?></h3>
                </div>
                <div class="p-3 bg-gray-700 rounded-xl text-emerald-400"><i class="fas fa-wallet text-xl"></i></div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <span class="text-emerald-300 bg-emerald-900/50 px-2 py-0.5 rounded-full font-medium">รายรับ</span>
                <span class="text-gray-500 ml-2">ในช่วงเวลาที่เลือก</span>
            </div>
        </div>
        
        <div class="stat-card bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">จำนวนออเดอร์</p>
                    <h3 class="text-3xl font-bold text-blue-400"><?php echo e(number_format($totalOrders)); ?></h3>
                </div>
                <div class="p-3 bg-gray-700 rounded-xl text-blue-400"><i class="fas fa-shopping-bag text-xl"></i></div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <span class="text-blue-300 bg-blue-900/50 px-2 py-0.5 rounded-full font-medium">รายการ</span>
                <span class="text-gray-500 ml-2">ออเดอร์ทั้งหมด</span>
            </div>
        </div>
        
        <div class="stat-card bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">ยอดต่อออเดอร์</p>
                    <h3 class="text-3xl font-bold text-purple-400">฿<?php echo e(number_format($avgOrderValue, 0)); ?></h3>
                </div>
                <div class="p-3 bg-gray-700 rounded-xl text-purple-400"><i class="fas fa-receipt text-xl"></i></div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <span class="text-purple-300 bg-purple-900/50 px-2 py-0.5 rounded-full font-medium">เฉลี่ย</span>
                <span class="text-gray-500 ml-2">บาท / 1 คำสั่งซื้อ</span>
            </div>
        </div>
        
        <div class="stat-card bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">ลูกค้าใหม่</p>
                    <h3 class="text-3xl font-bold text-orange-400"><?php echo e(number_format($newCustomers)); ?></h3>
                </div>
                <div class="p-3 bg-gray-700 rounded-xl text-orange-400"><i class="fas fa-users text-xl"></i></div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <span class="text-orange-300 bg-orange-900/50 px-2 py-0.5 rounded-full font-medium">คน</span>
                <span class="text-gray-500 ml-2">สมาชิกใหม่</span>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        
        <div class="lg:col-span-2 bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-gray-200 text-lg flex items-center">
                    <span class="w-2 h-6 bg-blue-400 rounded-full mr-3"></span>
                    ภาพรวมการเงิน (Income vs Expense)
                </h3>
            </div>
            <div class="relative h-80">
                <canvas id="salesOverTimeChart"></canvas>
            </div>
        </div>

        
        <div class="bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-gray-200 text-lg flex items-center">
                    <span class="w-2 h-6 bg-indigo-500 rounded-full mr-3"></span>
                    สถานะออเดอร์
                </h3>
            </div>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        
        <div class="lg:col-span-2 bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-gray-200 text-lg flex items-center">
                    <span class="w-2 h-6 bg-amber-500 rounded-full mr-3"></span>
                    การวิเคราะห์ราคา (Candlestick)
                </h3>
            </div>
            <div id="candleStickChart" class="w-full h-80"></div>
        </div>

        
        <div class="bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-200 text-lg flex items-center">
                    <span class="w-2 h-6 bg-purple-500 rounded-full mr-3"></span>
                    ปฏิทินออเดอร์
                </h3>
            </div>

            
            <div id='calendar'
                class="text-sm flex-grow min-h-[500px]
                
                bg-gray-800 text-gray-200
                
                
                [&_.fc-toolbar-title]:text-2xl [&_.fc-toolbar-title]:font-bold [&_.fc-toolbar-title]:text-white
                
                
                [&_.fc-button]:bg-gray-700 [&_.fc-button]:border-gray-600 [&_.fc-button]:text-gray-200 [&_.fc-button:hover]:bg-gray-600
                [&_.fc-button-active]:bg-emerald-600 [&_.fc-button-active]:border-emerald-600 [&_.fc-button-active]:text-white
                
                
                [&_.fc-scrollgrid-section-header_th]:bg-transparent [&_.fc-theme-standard_th]:border-gray-700
                [&_.fc-col-header-cell]:bg-transparent 
                
                
                [&_.fc-col-header-cell-cushion]:text-gray-400 [&_.fc-col-header-cell-cushion]:no-underline [&_.fc-col-header-cell-cushion]:font-bold
                
                
                [&_td]:border-gray-700 [&_.fc-scrollgrid]:border-gray-700
                
                
                [&_.fc-daygrid-day-number]:text-gray-400 [&_.fc-daygrid-day-number]:no-underline
                
                
                [&_.fc-day-today]:bg-emerald-500/10
                
                
                [&_.fc-popover]:bg-gray-800 [&_.fc-popover]:border-gray-600 [&_.fc-popover]:shadow-xl
                [&_.fc-popover-header]:bg-gray-700 [&_.fc-popover-header]:text-white
                [&_.fc-popover-body]:bg-gray-800 [&_.fc-popover-body]:text-gray-200
                [&_.fc-popover-close]:text-gray-400 [&_.fc-popover-close]:hover:text-white
            ">
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <div class="xl:col-span-2 bg-gray-800 rounded-2xl shadow-lg border border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-700 flex justify-between items-center">
                <h3 class="font-bold text-gray-200 text-lg">รายการสั่งซื้อล่าสุด</h3>
                <a href="<?php echo e(route('admin.dashboard.export')); ?>"
                    class="text-sm text-emerald-400 hover:text-emerald-300 font-medium flex items-center transition-colors">
                    <i class="fas fa-download mr-1"></i> Export CSV
                </a>
            </div>
            <div class="overflow-x-auto custom-scroll">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs text-gray-400 border-b border-gray-700 bg-gray-900/50">
                            <th class="px-6 py-4 font-medium uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-4 font-medium uppercase tracking-wider">ลูกค้า</th>
                            <th class="px-6 py-4 font-medium uppercase tracking-wider">ยอดรวม</th>
                            <th class="px-6 py-4 font-medium uppercase tracking-wider">สถานะ</th>
                            <th class="px-6 py-4 font-medium uppercase tracking-wider">วันที่</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-700/50 transition-colors border-b border-gray-700 last:border-0">
                                <td class="px-6 py-4 font-medium text-emerald-400">
                                    <a href="<?php echo e(route('admin.orders.show', $order->id)); ?>">#<?php echo e($order->ord_code); ?></a>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-300 border border-gray-600">
                                            <?php echo e(substr($order->user->name ?? 'G', 0, 1)); ?>

                                        </div>
                                        <span class="text-gray-300"><?php echo e($order->user->name ?? 'Guest'); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-200">
                                    ฿<?php echo e(number_format($order->net_amount, 2)); ?></td>
                                <td class="px-6 py-4">
                                    <?php
                                        $statusConfig = [
                                            1 => [
                                                'label' => 'รอชำระ',
                                                'class' => 'bg-yellow-900/50 text-yellow-300 border border-yellow-800',
                                            ],
                                            2 => [
                                                'label' => 'ชำระแล้ว',
                                                'class' => 'bg-blue-900/50 text-blue-300 border border-blue-800',
                                            ],
                                            3 => [
                                                'label' => 'เตรียมส่ง',
                                                'class' => 'bg-indigo-900/50 text-indigo-300 border border-indigo-800',
                                            ],
                                            4 => [
                                                'label' => 'ส่งแล้ว',
                                                'class' =>
                                                    'bg-emerald-900/50 text-emerald-300 border border-emerald-800',
                                            ],
                                            5 => [
                                                'label' => 'ยกเลิก',
                                                'class' => 'bg-red-900/50 text-red-300 border border-red-800',
                                            ],
                                        ];
                                        $status = $statusConfig[$order->status_id] ?? [
                                            'label' => 'Unknown',
                                            'class' => 'bg-gray-700 text-gray-400',
                                        ];
                                    ?>
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-medium <?php echo e($status['class']); ?>"><?php echo e($status['label']); ?></span>
                                </td>
                                <td class="px-6 py-4 text-gray-500"><?php echo e($order->created_at->format('d M H:i')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">ไม่มีรายการสั่งซื้อ</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="bg-gray-800 rounded-2xl shadow-lg border border-gray-700 p-6">
            <h3 class="font-bold text-gray-200 text-lg mb-6">สินค้าขายดี 5 อันดับ</h3>
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
                                class="w-full h-full object-cover rounded-lg border border-gray-600 bg-gray-700">
                            <div
                                class="absolute -top-2 -left-2 w-5 h-5 bg-gray-700 text-white text-[10px] flex items-center justify-center rounded-full border border-gray-500 shadow-sm font-bold">
                                <?php echo e($index + 1); ?></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between mb-1">
                                <span
                                    class="text-sm font-medium text-gray-300 truncate pr-2"><?php echo e($product->pd_sp_name ?? 'Product Deleted'); ?></span>
                                <span class="text-sm font-bold text-gray-100"><?php echo e(number_format($item->total_quantity)); ?>

                                    ชิ้น</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-1.5">
                                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: <?php echo e($item->percent); ?>%">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-8 text-gray-500">ไม่มีข้อมูลสินค้าขายดี</div>
                <?php endif; ?>
            </div>
            <div class="mt-6 pt-6 border-t border-gray-700 text-center">
                <a href="<?php echo e(route('admin.products.index')); ?>"
                    class="text-sm text-emerald-400 font-medium hover:text-emerald-300 transition-colors">ดูสินค้าทั้งหมด
                    &rarr;</a>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            Chart.defaults.font.family = "'Sarabun', sans-serif";
            Chart.defaults.color = '#9ca3af';
            Chart.defaults.borderColor = '#374151';

            // 1. Sales Chart (Bar Chart - Blue/Orange)
            const salesCtx = document.getElementById('salesOverTimeChart');
            if (salesCtx) {
                const incomeData = <?php echo json_encode($salesChartValues, 15, 512) ?>;
                const expenseData = incomeData.map(val => val * 0.65);

                new Chart(salesCtx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($salesChartLabels, 15, 512) ?>,
                        datasets: [{
                                label: 'Earnings',
                                data: incomeData,
                                backgroundColor: '#bae6fd',
                                hoverBackgroundColor: '#7dd3fc',
                                borderRadius: {
                                    topLeft: 10,
                                    topRight: 10,
                                    bottomLeft: 0,
                                    bottomRight: 0
                                },
                                barPercentage: 0.6,
                                categoryPercentage: 0.8
                            },
                            {
                                label: 'Expenses',
                                data: expenseData,
                                backgroundColor: '#fb923c',
                                hoverBackgroundColor: '#f97316',
                                borderRadius: {
                                    topLeft: 10,
                                    topRight: 10,
                                    bottomLeft: 0,
                                    bottomRight: 0
                                },
                                barPercentage: 0.6,
                                categoryPercentage: 0.8
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#fff',
                                titleColor: '#1f2937',
                                bodyColor: '#1f2937',
                                borderColor: '#e5e7eb',
                                borderWidth: 1,
                                padding: 12,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': $' + new Intl.NumberFormat(
                                            'en-US').format(context.raw);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#e5e7eb',
                                    borderDash: [5, 5],
                                    drawBorder: false
                                },
                                ticks: {
                                    color: '#9ca3af',
                                    font: {
                                        size: 12
                                    }
                                },
                                border: {
                                    display: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#9ca3af',
                                    font: {
                                        size: 12
                                    }
                                },
                                border: {
                                    display: false
                                }
                            }
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                    }
                });
            }

            // 2. Order Status Chart
            const statusCtx = document.getElementById('orderStatusChart');
            if (statusCtx) {
                const statusData = <?php echo json_encode($orderStatusBreakdown, 15, 512) ?>;
                const labelsMap = {
                    1: 'รอชำระ',
                    2: 'ชำระแล้ว',
                    3: 'เตรียมส่ง',
                    4: 'ส่งแล้ว',
                    5: 'ยกเลิก'
                };
                const colors = {
                    1: '#f59e0b',
                    2: '#3b82f6',
                    3: '#6366f1',
                    4: '#10b981',
                    5: '#ef4444'
                };

                if (Object.keys(statusData).length > 0) {
                    new Chart(statusCtx, {
                        type: 'doughnut',
                        data: {
                            labels: Object.keys(statusData).map(k => labelsMap[k] || 'Other'),
                            datasets: [{
                                data: Object.values(statusData),
                                backgroundColor: Object.keys(statusData).map(k => colors[k] ||
                                    '#6b7280'),
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '75%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: '#d1d5db'
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // 3. Candlestick Chart
            const salesLabels = <?php echo json_encode($salesChartLabels, 15, 512) ?>;
            const salesValues = <?php echo json_encode($salesChartValues, 15, 512) ?>;
            const candleSeriesData = salesLabels.map((date, index) => {
                const close = parseFloat(salesValues[index]);
                const open = close * (0.9 + Math.random() * 0.2);
                const high = Math.max(open, close) * 1.05;
                const low = Math.min(open, close) * 0.95;
                return {
                    x: new Date(date),
                    y: [open.toFixed(2), high.toFixed(2), low.toFixed(2), close.toFixed(2)]
                };
            });

            if (document.querySelector("#candleStickChart")) {
                new ApexCharts(document.querySelector("#candleStickChart"), {
                    series: [{
                        data: candleSeriesData
                    }],
                    chart: {
                        type: 'candlestick',
                        height: 320,
                        background: 'transparent',
                        toolbar: {
                            show: false
                        }
                    },
                    theme: {
                        mode: 'dark'
                    },
                    xaxis: {
                        type: 'datetime'
                    },
                    yaxis: {
                        tooltip: {
                            enabled: true
                        }
                    },
                    grid: {
                        borderColor: '#374151'
                    },
                    plotOptions: {
                        candlestick: {
                            colors: {
                                upward: '#10b981',
                                downward: '#ef4444'
                            }
                        }
                    }
                }).render();
            }

            // 4. Calendar (Fixed with Tailwind Classes)
            const calendarEl = document.getElementById('calendar');
            if (calendarEl && typeof FullCalendar !== 'undefined') {
                const recentOrders = <?php echo json_encode($recentOrders, 15, 512) ?>;
                const events = recentOrders.map(order => ({
                    title: '#' + order.ord_code,
                    start: order.created_at,
                    url: '/admin/orders/' + order.id,
                    backgroundColor: order.status_id == 4 ? '#10b981' : (order.status_id == 5 ?
                        '#ef4444' : '#3b82f6'),
                    borderColor: 'transparent',
                    className: 'cursor-pointer hover:scale-105 transition-transform'
                }));

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    height: 'auto',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,listWeek'
                    },
                    events: events,
                    locale: 'th',
                    dayMaxEvents: 2,
                    buttonText: {
                        today: 'วันนี้',
                        month: 'เดือน',
                        list: 'รายการ'
                    }
                });
                calendar.render();
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>