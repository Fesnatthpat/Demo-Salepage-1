@extends('layouts.admin')

@section('title', 'จัดการออเดอร์')
@section('page-title', 'รายการออเดอร์ทั้งหมด')

@section('content')
<div class="card bg-white shadow-md">
    <div class="card-body">
        <!-- Header & Search -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
            <h2 class="card-title">ออเดอร์ทั้งหมด ({{ $orders->total() }})</h2>
            <form action="{{ route('admin.orders.index') }}" method="GET">
                <div class="form-control">
                    <div class="relative">
                        <input type="text" name="search" placeholder="ค้นหา รหัสออเดอร์, ชื่อลูกค้า..." class="input input-bordered w-full sm:w-64 pr-10" value="{{ request('search') }}">
                        <button type="submit" class="absolute top-0 right-0 rounded-l-none btn btn-square btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Status Filter -->
        <div class="mb-4">
            <div class="join">
                {{-- All Statuses --}}
                <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => 'all'])) }}"
                    class="join-item btn btn-sm {{ !request('status') || request('status') == 'all' ? 'btn-active' : '' }}">
                    ทั้งหมด
                </a>
                {{-- Specific Statuses --}}
                @php
                    $statusOptions = [
                        1 => 'รอชำระเงิน',
                        2 => 'กำลังดำเนินการ',
                        3 => 'จัดส่งแล้ว',
                        4 => 'สำเร็จ',
                        5 => 'ยกเลิก',
                    ];
                @endphp
                @foreach($statusOptions as $id => $text)
                    <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => $id])) }}"
                        class="join-item btn btn-sm {{ request('status') == $id ? 'btn-active' : '' }}">
                        {{ $text }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Orders Table -->
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>รหัสออเดอร์</th>
                        <th>ลูกค้า</th>
                        <th class="text-right">ยอดรวมสินค้า</th>
                        <th class="text-right">ส่วนลด</th>
                        <th class="text-right">ยอดสุทธิ</th>
                        <th class="text-center">สถานะ</th>
                        <th>วันที่สั่งซื้อ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr class="hover">
                            <td class="font-mono font-semibold">{{ $order->ord_code }}</td>
                            <td>
                                <div class="font-bold">{{ $order->shipping_name }}</div>
                                <div class="text-sm opacity-50">{{ $order->user->email ?? 'N/A' }}</div>
                            </td>
                            <td class="text-right">฿{{ number_format($order->total_price, 2) }}</td>
                            <td class="text-right text-red-500">-฿{{ number_format($order->total_discount, 2) }}</td>
                            <td class="text-right font-bold">฿{{ number_format($order->net_amount, 2) }}</td>
                            <td class="text-center">
                                <span class="badge 
                                    @switch($order->status_id)
                                        @case(1) badge-warning @break
                                        @case(2) badge-info @break
                                        @case(3) badge-success @break
                                        @case(4) badge-primary @break
                                        @default badge-ghost
                                    @endswitch
                                ">
                                    {{-- TODO: Create a helper for status names --}}
                                    สถานะ #{{ $order->status_id }}
                                </span>
                            </td>
                            <td>{{ $order->ord_date->format('d M Y, H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class=" text-blue-600 text-bold hover:underline">
                                    <i class="fas fa-eye mr-2"></i>
                                    รายละเอียด
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">
                                @if(request('search'))
                                    ไม่พบออเดอร์ที่ตรงกับคำค้นหา "{{ request('search') }}"
                                @else
                                    ยังไม่มีข้อมูลออเดอร์ในระบบ
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
