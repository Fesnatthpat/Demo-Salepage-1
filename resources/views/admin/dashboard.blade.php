@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'แดชบอร์ดภาพรวม')

@section('content')

    {{-- Page Header --}}
    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.dashboard.export') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
            <i class="fas fa-file-csv mr-2"></i>
            <span>Export to CSV</span>
        </a>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Revenue --}}
        <div class="card bg-white border-l-4 border-emerald-500 shadow-md">
            <div class="card-body">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">ยอดขายทั้งหมด</p>
                        <p class="text-3xl font-bold text-gray-800">฿{{ number_format($stats['totalRevenue'], 2) }}</p>
                    </div>
                    <div class="text-emerald-500">
                        <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        {{-- Total Orders --}}
        <div class="card bg-white border-l-4 border-blue-500 shadow-md">
            <div class="card-body">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">ออเดอร์ทั้งหมด</p>
                        <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['totalOrders']) }}</p>
                    </div>
                    <div class="text-blue-500">
                        <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        {{-- New Customers --}}
        <div class="card bg-white border-l-4 border-yellow-500 shadow-md">
            <div class="card-body">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">ลูกค้าใหม่ (30 วัน)</p>
                        <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['newCustomers']) }}</p>
                    </div>
                    <div class="text-yellow-500">
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        {{-- Average Order Value --}}
        <div class="card bg-white border-l-4 border-red-500 shadow-md">
            <div class="card-body">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">ยอดสั่งซื้อเฉลี่ย</p>
                        <p class="text-3xl font-bold text-gray-800">฿{{ number_format($stats['averageOrderValue'], 2) }}</p>
                    </div>
                    <div class="text-red-500">
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Area --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

        {{-- Recent Orders Table --}}
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
                                @forelse ($recentOrders as $order)
                                    <tr>
                                        <td class="font-mono">{{ $order->ord_code }}</td>
                                        <td>{{ $order->shipping_name ?? ($order->user->name ?? 'N/A') }}</td>
                                        <td>฿{{ number_format($order->net_amount, 2) }}</td>
                                        <td>
                                            <span
                                                class="badge 
                                            @switch($order->status_id)
                                                @case(1) badge-warning @break
                                                @case(2) badge-info @break
                                                @case(3) badge-success @break
                                                @case(4) badge-primary @break
                                                @default badge-ghost
                                            @endswitch
                                            ">
                                                สถานะ {{ $order->status_id }}
                                            </span>
                                        </td>
                                        <td>{{ $order->ord_date->format('d/m/Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-8 text-gray-500">ยังไม่มีออเดอร์</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Selling Products --}}
        <div class="xl:col-span-1">
            <div class="card bg-white shadow-md">
                <div class="card-body">
                    <h2 class="card-title mb-4">สินค้าขายดี 5 อันดับ</h2>
                    <ul class="space-y-4">
                        @forelse($topSellingProducts as $item)
                            {{-- ★★★ PHP Logic สำหรับหารูปภาพแบบละเอียด ★★★ --}}
                            @php
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
                            @endphp

                            <li class="flex items-center space-x-4">
                                {{-- Avatar รูปสินค้า --}}
                                <div class="avatar">
                                    <div class="rounded border w-12 h-12">
                                        <img src="{{ $displayImage }}" alt="{{ $product->pd_sp_name ?? 'Product' }}"
                                            class="object-cover w-full h-full"
                                            onerror="this.src='https://via.placeholder.com/150?text=Error'">
                                    </div>
                                </div>

                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800 line-clamp-1"
                                        title="{{ $product->pd_sp_name ?? '' }}">
                                        {{ $product->pd_sp_name ?? 'ไม่พบข้อมูลสินค้า' }}
                                    </p>
                                    <p class="text-sm text-gray-500">ขายแล้ว {{ number_format($item->total_sold) }} ชิ้น
                                    </p>
                                </div>
                            </li>
                        @empty
                            <li class="text-center py-8 text-gray-500">ยังไม่มีข้อมูลสินค้าขายดี</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection
