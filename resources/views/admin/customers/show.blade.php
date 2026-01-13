@extends('layouts.admin')

@section('title', 'รายละเอียดลูกค้า')
@section('page-title')
    <a href="{{ route('admin.customers.index') }}" class="text-gray-500 hover:text-gray-900">ลูกค้า</a> /
    <span class="text-gray-900">รายละเอียดลูกค้า: {{ $customer->name }}</span>
@endsection

@section('content')
<div class="card bg-white shadow-md">
    <div class="card-body">
        <div class="flex justify-between items-center mb-6">
            <h2 class="card-title">ข้อมูลลูกค้า</h2>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-ghost">
                <i class="fas fa-arrow-left mr-2"></i>
                กลับ
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">ชื่อ</p>
                <p class="text-lg font-bold text-gray-800">{{ $customer->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">อีเมล</p>
                <p class="text-lg font-bold text-gray-800">{{ $customer->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">เบอร์โทรศัพท์</p>
                <p class="text-lg font-bold text-gray-800">{{ $customer->phone ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">สถานะ LINE</p>
                <p class="text-lg font-bold text-gray-800">
                    @if ($customer->line_id)
                        <span class="badge badge-success">เชื่อมต่อแล้ว (LINE ID: {{ $customer->line_id }})</span>
                    @else
                        <span class="badge badge-warning">ไม่ได้เชื่อมต่อ</span>
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500">วันที่ลงทะเบียน</p>
                <p class="text-lg font-bold text-gray-800">{{ $customer->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">อัปเดตล่าสุด</p>
                <p class="text-lg font-bold text-gray-800">{{ $customer->updated_at->format('d M Y, H:i') }}</p>
            </div>
        </div>

        <div class="divider"></div>

        <h3 class="card-title mb-4">ออเดอร์ล่าสุดของลูกค้า</h3>
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>รหัสออเดอร์</th>
                        <th>ยอดสุทธิ</th>
                        <th>สถานะ</th>
                        <th>วันที่สั่งซื้อ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customer->orders as $order)
                        <tr class="hover">
                            <td class="font-mono">{{ $order->ord_code }}</td>
                            <td class="text-right">฿{{ number_format($order->net_amount, 2) }}</td>
                            <td class="text-center">
                                @php
                                    // Assuming you have a status map like in AdminOrderController
                                    $statusMap = [
                                        1 => 'รอชำระเงิน',
                                        2 => 'แจ้งชำระเงินแล้ว',
                                        3 => 'กำลังเตรียมจัดส่ง',
                                        4 => 'จัดส่งแล้ว',
                                        5 => 'ยกเลิก',
                                    ];
                                    $statusText = $statusMap[$order->status_id] ?? 'ไม่ทราบสถานะ';
                                @endphp
                                <span class="badge 
                                    @switch($order->status_id)
                                        @case(1) badge-warning @break
                                        @case(2) badge-info @break
                                        @case(3) badge-success @break
                                        @case(4) badge-primary @break
                                        @default badge-ghost
                                    @endswitch
                                ">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td>{{ $order->ord_date->format('d M Y, H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-ghost btn-sm">
                                    <i class="fas fa-eye mr-2"></i>
                                    รายละเอียด
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">
                                ลูกค้ารายนี้ยังไม่มีออเดอร์
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
