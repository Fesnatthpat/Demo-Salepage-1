@extends('layouts.admin') {{-- หรือชื่อ Layout หลักของหน้าร้านคุณ --}}

@section('title', 'รายละเอียดคำสั่งซื้อ ' . $order->ord_code)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">

            {{-- Header & ปุ่มย้อนกลับ --}}
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-file-invoice mr-2 text-emerald-600"></i>รายละเอียดคำสั่งซื้อ
                </h1>
                <a href="{{ route('orders.index') }}" class="text-sm text-gray-600 hover:text-emerald-600 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> กลับไปหน้าประวัติ
                </a>
            </div>

            {{-- แจ้งเตือนเมื่อสั่งซื้อสำเร็จ --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 shadow-sm">
                    <span class="block sm:inline"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
                </div>
            @endif

            {{-- ข้อมูลบิลหลัก --}}
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">หมายเลขคำสั่งซื้อ</p>
                        <p class="font-bold text-lg text-gray-800">{{ $order->ord_code }}</p>

                        <p class="text-sm text-gray-500 mb-1 mt-4">วันที่สั่งซื้อ</p>
                        <p class="text-gray-800">
                            {{ \Carbon\Carbon::parse($order->ord_date)->addYears(543)->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="md:text-right">
                        <p class="text-sm text-gray-500 mb-1">สถานะคำสั่งซื้อ</p>
                        @php
                            $statusClass = 'bg-gray-100 text-gray-800 border-gray-200';
                            $statusText = $order->status;

                            if ($order->status == 'pending' || $order->status_id == 1) {
                                $statusClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                                $statusText = 'รอชำระเงิน';
                            } elseif ($order->status == 'paid' || $order->status_id == 2) {
                                $statusClass = 'bg-blue-100 text-blue-800 border-blue-200';
                                $statusText = 'ชำระเงินแล้ว';
                            } elseif ($order->status_id == 3) {
                                $statusClass = 'bg-indigo-100 text-indigo-800 border-indigo-200';
                                $statusText = 'กำลังเตรียมจัดส่ง';
                            } elseif ($order->status_id == 4) {
                                $statusClass = 'bg-green-100 text-green-800 border-green-200';
                                $statusText = 'จัดส่งแล้ว';
                            } elseif ($order->status_id == 5) {
                                $statusClass = 'bg-red-100 text-red-800 border-red-200';
                                $statusText = 'ยกเลิก';
                            }
                        @endphp
                        <span class="inline-block py-1 px-3 rounded-full text-sm font-medium border {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- รายการสินค้า --}}
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="font-bold text-gray-800"><i class="fas fa-box-open mr-2"></i>รายการสินค้า</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-600 text-sm">
                            <tr>
                                <th class="py-3 px-6 border-b">สินค้า</th>
                                <th class="py-3 px-6 border-b text-right">ราคาต่อชิ้น</th>
                                <th class="py-3 px-6 border-b text-center">จำนวน</th>
                                <th class="py-3 px-6 border-b text-right">รวม</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            @if (isset($order->details) && count($order->details) > 0)
                                @foreach ($order->details as $item)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-4 px-6">
                                            <p class="font-medium text-gray-800">
                                                {{ $item->productSalepage->pd_sp_name ?? 'สินค้า (SKU: ' . $item->pd_id . ')' }}
                                            </p>
                                            @if ($item->option_name)
                                                <p class="text-xs text-gray-500 mt-1">ตัวเลือก: {{ $item->option_name }}</p>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-right">฿{{ number_format($item->ordd_price, 2) }}</td>
                                        <td class="py-4 px-6 text-center">{{ $item->ordd_count }}</td>
                                        <td class="py-4 px-6 text-right font-medium text-emerald-600">
                                            ฿{{ number_format($item->ordd_price * $item->ordd_count, 2) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="py-8 px-6 text-center text-gray-500">
                                        ไม่พบรายการสินค้าในออเดอร์นี้</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- สรุปยอดเงิน --}}
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <div class="w-full md:w-1/2 lg:w-1/3">
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-600">ยอดรวมสินค้า</span>
                            <span class="font-medium text-gray-800">฿{{ number_format($order->total_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-3">
                            <span class="text-gray-800 font-bold text-lg">ยอดชำระสุทธิ</span>
                            <span
                                class="font-bold text-lg text-emerald-600">฿{{ number_format($order->total_price, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ข้อมูลจัดส่ง --}}
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="font-bold text-gray-800"><i class="fas fa-map-marker-alt mr-2"></i>ข้อมูลการจัดส่ง</h2>
                </div>
                <div class="p-6">
                    <p class="mb-2"><span class="font-medium text-gray-700">เบอร์โทรศัพท์:</span>
                        {{ $order->shipping_phone }}</p>
                    <p><span class="font-medium text-gray-700 block mb-1">ที่อยู่จัดส่ง:</span></p>
                    <p class="text-gray-600 bg-gray-50 p-3 rounded border border-gray-200 whitespace-pre-line">
                        {{ $order->shipping_address }}</p>
                </div>
            </div>

        </div>
    </div>
@endsection
