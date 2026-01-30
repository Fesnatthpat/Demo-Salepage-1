@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Overview Dashboard')

@push('styles')
    <style>
        .stat-card {
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
        }
    </style>
@endpush

@section('content')

    {{-- 1. Header & Filters --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-100">ยินดีต้อนรับกลับ, Admin 👋</h1>
            <p class="text-sm text-gray-400 mt-1">นี่คือภาพรวมร้านค้าของคุณในช่วงเวลาที่เลือก</p>
        </div>

        {{-- Filter Buttons (Dark Mode) --}}
        <div class="flex flex-wrap items-center gap-2 bg-gray-800 p-1.5 rounded-xl shadow-md border border-gray-700">
            @php
                function getBtnClass($isActive)
                {
                    return $isActive
                        ? 'bg-emerald-600 text-white shadow-md'
                        : 'text-gray-400 hover:bg-gray-700 hover:text-white';
                }
                $commonClass = 'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200';
            @endphp

            <a href="{{ route('admin.dashboard', ['period' => 'today']) }}"
                class="{{ $commonClass }} {{ getBtnClass($period == 'today') }}">
                วันนี้
            </a>
            <a href="{{ route('admin.dashboard', ['period' => 'last_7_days']) }}"
                class="{{ $commonClass }} {{ getBtnClass($period == 'last_7_days') }}">
                7 วัน
            </a>
            <a href="{{ route('admin.dashboard', ['period' => 'this_month']) }}"
                class="{{ $commonClass }} {{ getBtnClass($period == 'this_month') }}">
                เดือนนี้
            </a>
            <a href="{{ route('admin.dashboard', ['period' => 'last_30_days']) }}"
                class="{{ $commonClass }} {{ getBtnClass($period == 'last_30_days') }}">
                30 วัน
            </a>
        </div>
    </div>

    {{-- 2. Stat Cards (Dark Mode) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Sales Card --}}
        <div class="stat-card bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">ยอดขายรวม</p>
                    <h3 class="text-3xl font-bold text-emerald-400">฿{{ number_format($totalSales, 0) }}</h3>
                </div>
                <div class="p-3 bg-gray-700 rounded-xl text-emerald-400">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <span class="text-emerald-300 bg-emerald-900/50 px-2 py-0.5 rounded-full font-medium">
                    <i class="fas fa-chart-line mr-1"></i> รายรับ
                </span>
                <span class="text-gray-500 ml-2">ในช่วงเวลาที่เลือก</span>
            </div>
        </div>

        {{-- Orders Card --}}
        <div class="stat-card bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">จำนวนออเดอร์</p>
                    <h3 class="text-3xl font-bold text-blue-400">{{ number_format($totalOrders) }}</h3>
                </div>
                <div class="p-3 bg-gray-700 rounded-xl text-blue-400">
                    <i class="fas fa-shopping-bag text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <span class="text-blue-300 bg-blue-900/50 px-2 py-0.5 rounded-full font-medium">
                    รายการ
                </span>
                <span class="text-gray-500 ml-2">ออเดอร์ทั้งหมด</span>
            </div>
        </div>

        {{-- Avg Value Card --}}
        <div class="stat-card bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">ยอดต่อออเดอร์</p>
                    <h3 class="text-3xl font-bold text-purple-400">฿{{ number_format($avgOrderValue, 0) }}</h3>
                </div>
                <div class="p-3 bg-gray-700 rounded-xl text-purple-400">
                    <i class="fas fa-receipt text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <span class="text-purple-300 bg-purple-900/50 px-2 py-0.5 rounded-full font-medium">
                    เฉลี่ย
                </span>
                <span class="text-gray-500 ml-2">บาท / 1 คำสั่งซื้อ</span>
            </div>
        </div>

        {{-- Customers Card --}}
        <div class="stat-card bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">ลูกค้าใหม่</p>
                    <h3 class="text-3xl font-bold text-orange-400">{{ number_format($newCustomers) }}</h3>
                </div>
                <div class="p-3 bg-gray-700 rounded-xl text-orange-400">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <span class="text-orange-300 bg-orange-900/50 px-2 py-0.5 rounded-full font-medium">
                    คน
                </span>
                <span class="text-gray-500 ml-2">สมัครสมาชิกใหม่</span>
            </div>
        </div>
    </div>

    {{-- 3. Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        {{-- Line Chart --}}
        <div class="lg:col-span-2 bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-gray-200 text-lg flex items-center">
                    <span class="w-2 h-6 bg-emerald-500 rounded-full mr-3"></span>
                    แนวโน้มยอดขาย
                </h3>
            </div>
            <div class="relative h-80">
                <canvas id="salesOverTimeChart"></canvas>
            </div>
        </div>

        {{-- Doughnut Chart --}}
        <div class="bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-gray-200 text-lg flex items-center">
                    <span class="w-2 h-6 bg-blue-500 rounded-full mr-3"></span>
                    สถานะออเดอร์
                </h3>
            </div>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>

    {{-- 4. Recent Orders & Top Products --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

        {{-- Table --}}
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
                                    ฿{{ number_format($order->net_amount, 2) }}
                                </td>
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
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $status['class'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ $order->created_at->format('d M H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-3 block opacity-20"></i>
                                    ยังไม่มีรายการสั่งซื้อในช่วงนี้
                                </td>
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
                                {{ $index + 1 }}
                            </div>
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
                    <div class="text-center py-8 text-gray-500">
                        ไม่มีข้อมูลสินค้าขายดี
                    </div>
                @endforelse
            </div>
            <div class="mt-6 pt-6 border-t border-gray-700 text-center">
                <a href="{{ route('admin.products.index') }}"
                    class="text-sm text-emerald-400 font-medium hover:text-emerald-300 transition-colors">
                    ดูสินค้าทั้งหมด &rarr;
                </a>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ✅ ปรับสี Font ของกราฟให้เป็นสีสว่าง (สำหรับ Dark Mode)
            Chart.defaults.font.family = "'Sarabun', sans-serif";
            Chart.defaults.color = '#9ca3af'; // gray-400
            Chart.defaults.borderColor = '#374151'; // gray-700 (เส้นตาราง)

            // 1. Sales Chart
            const salesCtx = document.getElementById('salesOverTimeChart');
            if (salesCtx) {
                const ctx = salesCtx.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(16, 185, 129, 0.5)'); // Emerald-500 transparent
                gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($salesChartLabels),
                        datasets: [{
                            label: 'ยอดขาย (บาท)',
                            data: @json($salesChartValues),
                            backgroundColor: gradient,
                            borderColor: '#10b981', // Emerald-500
                            borderWidth: 2,
                            pointBackgroundColor: '#064e3b', // Emerald-900
                            pointBorderColor: '#10b981',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4
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
                                backgroundColor: '#111827', // gray-900
                                titleColor: '#f3f4f6',
                                bodyColor: '#f3f4f6',
                                borderColor: '#374151',
                                borderWidth: 1,
                                padding: 10,
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
                                    color: '#374151',
                                    drawBorder: false
                                }, // เส้นตารางสีเข้ม
                                ticks: {
                                    color: '#9ca3af',
                                    padding: 10
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    color: '#9ca3af',
                                    padding: 10
                                }
                            }
                        }
                    }
                });
            }

            // 2. Order Status Chart
            const statusCtx = document.getElementById('orderStatusChart');
            const statusData = @json($orderStatusBreakdown);

            if (statusCtx && Object.keys(statusData).length > 0) {
                const labelsMap = {
                    1: 'รอชำระ',
                    2: 'ชำระแล้ว',
                    3: 'เตรียมส่ง',
                    4: 'ส่งแล้ว',
                    5: 'ยกเลิก'
                };

                // ปรับสี Chart ให้สดขึ้นบนพื้นดำ
                const colors = {
                    1: '#f59e0b', // Amber-500
                    2: '#3b82f6', // Blue-500
                    3: '#6366f1', // Indigo-500
                    4: '#10b981', // Emerald-500
                    5: '#ef4444' // Red-500
                };

                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(statusData).map(k => labelsMap[k] || 'Other'),
                        datasets: [{
                            data: Object.values(statusData),
                            backgroundColor: Object.keys(statusData).map(k => colors[k] ||
                                '#6b7280'),
                            borderWidth: 0,
                            hoverOffset: 5
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
                                    usePointStyle: true,
                                    padding: 20,
                                    color: '#d1d5db' // gray-300
                                }
                            },
                            tooltip: {
                                backgroundColor: '#111827',
                                bodyColor: '#f3f4f6',
                                borderColor: '#374151',
                                borderWidth: 1,
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
@endpush
