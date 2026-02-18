@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Overview Dashboard')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        .stat-card {
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
        }

        /* ปรับแต่ง Input Date ให้สวยงามบน Dark Mode */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
    </style>
@endpush

@section('content')

    {{-- 1. Header & Filters --}}
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-100">ยินดีต้อนรับกลับ, Admin 👋</h1>
            <p class="text-sm text-gray-400 mt-1">
                ข้อมูลช่วง: <span
                    class="text-emerald-400 font-medium">{{ \Carbon\Carbon::parse($currentStartDate)->format('d/m/Y') }}</span>
                ถึง <span
                    class="text-emerald-400 font-medium">{{ \Carbon\Carbon::parse($currentEndDate)->format('d/m/Y') }}</span>
            </p>
        </div>

        {{-- Filter Section --}}
        <div
            class="flex flex-col sm:flex-row gap-3 items-start sm:items-center bg-gray-800 p-2 rounded-xl shadow-md border border-gray-700">

            {{-- Quick Filters --}}
            <div class="flex items-center gap-1">
                @php
                    function getBtnClass($isActive)
                    {
                        return $isActive
                            ? 'bg-emerald-600 text-white shadow-md'
                            : 'text-gray-400 hover:bg-gray-700 hover:text-white';
                    }
                    $commonClass = 'px-3 py-1.5 text-xs font-medium rounded-lg transition-all duration-200';
                @endphp
                <a href="{{ route('admin.dashboard', ['period' => 'today']) }}"
                    class="{{ $commonClass }} {{ getBtnClass($period == 'today') }}">วันนี้</a>
                <a href="{{ route('admin.dashboard', ['period' => 'last_7_days']) }}"
                    class="{{ $commonClass }} {{ getBtnClass($period == 'last_7_days') }}">7 วัน</a>
                <a href="{{ route('admin.dashboard', ['period' => 'this_month']) }}"
                    class="{{ $commonClass }} {{ getBtnClass($period == 'this_month') }}">เดือนนี้</a>
            </div>

            <div class="hidden sm:block w-px h-6 bg-gray-700 mx-1"></div>

            {{-- Custom Date Picker Form --}}
            <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="period" value="custom">

                <div class="relative">
                    <input type="date" name="start_date" value="{{ request('start_date', $currentStartDate) }}"
                        class="bg-gray-900 border border-gray-600 text-gray-200 text-xs rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-2 pr-1 py-1.5"
                        required>
                </div>
                <span class="text-gray-500 text-xs">ถึง</span>
                <div class="relative">
                    <input type="date" name="end_date" value="{{ request('end_date', $currentEndDate) }}"
                        class="bg-gray-900 border border-gray-600 text-gray-200 text-xs rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-2 pr-1 py-1.5"
                        required>
                </div>

                <button type="submit"
                    class="btn btn-xs btn-primary bg-emerald-600 hover:bg-emerald-700 border-none text-white">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- 2. Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- ยอดขายรวม --}}
        <div class="stat-card bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">ยอดขายรวม</p>
                    <h3 class="text-3xl font-bold text-emerald-400">฿{{ number_format($totalSales, 0) }}</h3>
                </div>
                <div class="p-3 bg-gray-700 rounded-xl text-emerald-400"><i class="fas fa-wallet text-xl"></i></div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <span class="text-emerald-300 bg-emerald-900/50 px-2 py-0.5 rounded-full font-medium">รายรับ</span>
                <span class="text-gray-500 ml-2">ในช่วงเวลาที่เลือก</span>
            </div>
        </div>
        {{-- จำนวนออเดอร์ --}}
        <div class="stat-card bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">จำนวนออเดอร์</p>
                    <h3 class="text-3xl font-bold text-blue-400">{{ number_format($totalOrders) }}</h3>
                </div>
                <div class="p-3 bg-gray-700 rounded-xl text-blue-400"><i class="fas fa-shopping-bag text-xl"></i></div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <span class="text-blue-300 bg-blue-900/50 px-2 py-0.5 rounded-full font-medium">รายการ</span>
                <span class="text-gray-500 ml-2">ออเดอร์ทั้งหมด</span>
            </div>
        </div>
        {{-- ยอดต่อออเดอร์ --}}
        <div class="stat-card bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">ยอดต่อออเดอร์</p>
                    <h3 class="text-3xl font-bold text-purple-400">฿{{ number_format($avgOrderValue, 0) }}</h3>
                </div>
                <div class="p-3 bg-gray-700 rounded-xl text-purple-400"><i class="fas fa-receipt text-xl"></i></div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <span class="text-purple-300 bg-purple-900/50 px-2 py-0.5 rounded-full font-medium">เฉลี่ย</span>
                <span class="text-gray-500 ml-2">บาท / 1 คำสั่งซื้อ</span>
            </div>
        </div>
        {{-- ลูกค้าใหม่ --}}
        <div class="stat-card bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">ลูกค้าใหม่</p>
                    <h3 class="text-3xl font-bold text-orange-400">{{ number_format($newCustomers) }}</h3>
                </div>
                <div class="p-3 bg-gray-700 rounded-xl text-orange-400"><i class="fas fa-users text-xl"></i></div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <span class="text-orange-300 bg-orange-900/50 px-2 py-0.5 rounded-full font-medium">คน</span>
                <span class="text-gray-500 ml-2">สมาชิกใหม่</span>
            </div>
        </div>
    </div>

    {{-- 3. Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        {{-- Bar Chart --}}
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

        {{-- Order Status Chart --}}
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

    {{-- 3.5 Candlestick Chart (Full Width) --}}
    <div class="mb-8">
        <div class="w-full bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-gray-200 text-lg flex items-center">
                    <span class="w-2 h-6 bg-amber-500 rounded-full mr-3"></span>
                    การวิเคราะห์แนวโน้มยอดขาย (Candlestick)
                </h3>
            </div>
            <div id="candleStickChartContainer" class="w-full h-80">
                <div id="candleStickChart"></div>
            </div>
        </div>
    </div>

    {{-- 4. Recent Orders Table & Top Products --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        {{-- Recent Orders --}}
        <div class="xl:col-span-2 bg-gray-800 rounded-2xl shadow-lg border border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-700 flex justify-between items-center">
                <h3 class="font-bold text-gray-200 text-lg">รายการสั่งซื้อล่าสุด</h3>
                <a href="{{ route('admin.dashboard.export') }}"
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
                        @forelse ($recentOrders as $order)
                            <tr class="hover:bg-gray-700/50 transition-colors border-b border-gray-700 last:border-0">
                                <td class="px-6 py-4 font-medium text-emerald-400">
                                    <a href="{{ route('admin.orders.show', $order->id) }}">#{{ $order->ord_code }}</a>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-300 border border-gray-600">
                                            {{ substr($order->user->name ?? 'G', 0, 1) }}
                                        </div>
                                        <span class="text-gray-300">{{ $order->user->name ?? 'Guest' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-200">
                                    ฿{{ number_format($order->net_amount, 2) }}</td>
                                <td class="px-6 py-4">
                                    @php
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
                                    @endphp
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-medium {{ $status['class'] }}">{{ $status['label'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $order->created_at->format('d M H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">ไม่มีรายการสั่งซื้อ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top Products --}}
        <div class="bg-gray-800 rounded-2xl shadow-lg border border-gray-700 p-6">
            <h3 class="font-bold text-gray-200 text-lg mb-6">สินค้าขายดี 5 อันดับ</h3>
            <div class="space-y-6">
                @forelse($topSellingProducts as $index => $item)
                    @php
                        $product = $item->productSalepage;
                        $img = $product ? $product->images->first() : null;
                        $imgPath = $img ? asset('storage/' . $img->img_path) : 'https://via.placeholder.com/150';
                    @endphp
                    <div class="flex items-center gap-4">
                        <div class="relative w-12 h-12 flex-shrink-0">
                            <img src="{{ $imgPath }}"
                                class="w-full h-full object-cover rounded-lg border border-gray-600 bg-gray-700">
                            <div
                                class="absolute -top-2 -left-2 w-5 h-5 bg-gray-700 text-white text-[10px] flex items-center justify-center rounded-full border border-gray-500 shadow-sm font-bold">
                                {{ $index + 1 }}</div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between mb-1">
                                <span
                                    class="text-sm font-medium text-gray-300 truncate pr-2">{{ $product->pd_sp_name ?? 'Product Deleted' }}</span>
                                <span class="text-sm font-bold text-gray-100">{{ number_format($item->total_quantity) }}
                                    ชิ้น</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-1.5">
                                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $item->percent }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">ไม่มีข้อมูลสินค้าขายดี</div>
                @endforelse
            </div>
            <div class="mt-6 pt-6 border-t border-gray-700 text-center">
                <a href="{{ route('admin.products.index') }}"
                    class="text-sm text-emerald-400 font-medium hover:text-emerald-300 transition-colors">ดูสินค้าทั้งหมด
                    &rarr;</a>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            Chart.defaults.font.family = "'Sarabun', sans-serif";
            Chart.defaults.color = '#9ca3af';
            Chart.defaults.borderColor = '#374151';

            // 1. Sales Chart (Bar Chart - Blue/Orange)
            const salesCtx = document.getElementById('salesOverTimeChart');
            if (salesCtx) {
                const incomeData = @json($salesChartValues);
                const expenseData = incomeData.map(val => val * 0.65); // Dummy expense logic

                new Chart(salesCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($salesChartLabels),
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
                const statusData = @json($orderStatusBreakdown);
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

            // 3. Candlestick Chart (แก้ไขใหม่)
            // ✅ เปลี่ยนมารับค่า Raw Dates แทน Labels
            const salesRawDates = @json($salesChartRawDates ?? []);
            const salesValues = @json($salesChartValues ?? []);

            if (salesRawDates && salesRawDates.length > 0) {
                const candleSeriesData = salesRawDates.map((date, index) => {
                    const close = parseFloat(salesValues[index]);

                    // Simulation Logic: จำลองราคา Open, High, Low จาก Close (ยอดขายจริง)
                    // เนื่องจากเรามียอดขายแค่ค่าเดียวต่อวัน จึงต้องจำลองความผันผวนเพื่อให้เป็นกราฟแท่งเทียน
                    const open = close * (0.9 + Math.random() * 0.2);
                    const high = Math.max(open, close) * 1.05;
                    const low = Math.min(open, close) * 0.95;

                    return {
                        x: new Date(date), // ✅ ใช้ date format มาตรฐาน (YYYY-MM-DD) รับรองว่าไม่ error
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
                            },
                            labels: {
                                formatter: (value) => {
                                    return "฿" + value.toFixed(0)
                                }
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
            } else {
                // กรณีไม่มีข้อมูล ให้แสดงข้อความแจ้งเตือน
                const chartDiv = document.querySelector("#candleStickChartContainer");
                if (chartDiv) {
                    chartDiv.innerHTML =
                        '<div class="flex items-center justify-center h-full text-gray-500 border border-dashed border-gray-700 rounded-xl bg-gray-800/50">ยังไม่มีข้อมูลยอดขายในช่วงเวลานี้</div>';
                }
            }
        });
    </script>
@endpush
