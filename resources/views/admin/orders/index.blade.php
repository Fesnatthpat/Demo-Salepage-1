@extends('layouts.app') {{-- หรือชื่อ Layout หลักของหน้าร้านคุณ เช่น layouts.frontend --}}

@section('title', 'ประวัติการสั่งซื้อของฉัน')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-5xl mx-auto">

            <div class="flex items-center justify-between mb-6 border-b pb-4">
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-box-open mr-2 text-emerald-600"></i>ประวัติการสั่งซื้อของฉัน
                </h1>
            </div>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 shadow-sm">
                    <span class="block sm:inline"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700 uppercase text-sm leading-normal border-b">
                                <th class="py-3 px-6 text-left">รหัสออเดอร์</th>
                                <th class="py-3 px-6 text-left">วันที่สั่งซื้อ</th>
                                <th class="py-3 px-6 text-right">ยอดรวม</th>
                                <th class="py-3 px-6 text-center">สถานะ</th>
                                <th class="py-3 px-6 text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @forelse($orders as $order)
                                <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-6 text-left whitespace-nowrap">
                                        <span class="font-medium text-gray-800">{{ $order->ord_code }}</span>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <span>{{ \Carbon\Carbon::parse($order->ord_date)->addYears(543)->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td class="py-3 px-6 text-right font-semibold text-emerald-600">
                                        ฿{{ number_format($order->total_price, 2) }}
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        @php
                                            $statusClass = 'bg-gray-100 text-gray-700';
                                            $statusText = $order->status;

                                            if ($order->status == 'pending' || $order->status_id == 1) {
                                                $statusClass = 'bg-yellow-100 text-yellow-800 border border-yellow-200';
                                                $statusText = 'รอชำระเงิน';
                                            } elseif ($order->status == 'paid' || $order->status_id == 2) {
                                                $statusClass = 'bg-blue-100 text-blue-800 border border-blue-200';
                                                $statusText = 'ชำระเงินแล้ว';
                                            } elseif ($order->status_id == 3) {
                                                $statusClass = 'bg-indigo-100 text-indigo-800 border border-indigo-200';
                                                $statusText = 'กำลังเตรียมจัดส่ง';
                                            } elseif ($order->status_id == 4) {
                                                $statusClass = 'bg-green-100 text-green-800 border border-green-200';
                                                $statusText = 'จัดส่งแล้ว';
                                            }
                                        @endphp
                                        <span class="py-1 px-3 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <a href="{{ route('orders.show', $order->ord_code) }}"
                                            class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 transition-colors">
                                            ดูรายละเอียด
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-500">
                                        <p class="text-lg font-medium text-gray-600">ยังไม่มีประวัติการสั่งซื้อ</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($orders->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
