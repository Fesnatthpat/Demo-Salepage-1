@extends('layout')

@section('title', 'รายละเอียดออเดอร์ | Salepage Demo')

@section('content')
    @php
        // Map status ID to text and color
        $statusMap = [
            1 => ['text' => 'รอชำระเงิน', 'class' => 'bg-yellow-100 text-yellow-800'],
            2 => ['text' => 'กำลังดำเนินการ', 'class' => 'bg-blue-100 text-blue-800'],
            3 => ['text' => 'จัดส่งแล้ว', 'class' => 'bg-green-100 text-green-800'],
            4 => ['text' => 'สำเร็จ', 'class' => 'bg-emerald-100 text-emerald-800'],
            5 => ['text' => 'ยกเลิก', 'class' => 'bg-red-100 text-red-800'],
        ];
        $statusInfo = $statusMap[$order->status_id] ?? ['text' => 'ไม่ระบุ', 'class' => 'bg-gray-100 text-gray-800'];
    @endphp

    <div class="container mx-auto p-4 lg:px-20 lg:py-10 max-w-7xl">
        <div class="bg-white border border-gray-200 rounded-lg p-6 lg:p-8 shadow-sm">

            {{-- Order Header --}}
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            รายละเอียดคำสั่งซื้อ
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">
                            หมายเลข: {{ $order->ord_code }}
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span
                            class="px-4 py-1.5 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusInfo['class'] }}">
                            {{ $statusInfo['text'] }}
                        </span>
                        <a href="{{ route('order.history') }}" class="btn btn-sm btn-ghost text-gray-600 hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            กลับ
                        </a>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-2">
                    วันที่สั่งซื้อ: {{ $order->formatted_ord_date }} น.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Left Column: Item Details --}}
                <div class="lg:col-span-2">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">รายการสินค้า</h2>
                    <div class="space-y-4">
                        @foreach ($order->details as $detail)
                            <div
                                class="flex justify-between items-start border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                <div class="flex items-center gap-4">
                                    @if ($detail->product && $detail->product->pd_img)
                                        <div
                                            class="w-20 h-20 bg-gray-100 rounded-md overflow-hidden border border-gray-200 flex-shrink-0">
                                            <img src="https://crm.kawinbrothers.com/product_images/{{ $detail->product->pd_img }}"
                                                class="w-full h-full object-cover" alt="{{ $detail->product->pd_name }}" />
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm md:text-base line-clamp-2">
                                            {{ $detail->product->pd_name ?? 'ไม่พบข้อมูลสินค้า' }}
                                        </p>
                                        <p class="text-xs text-gray-500">Code: {{ $detail->product->pd_code ?? 'N/A' }}</p>
                                        <p class="text-sm text-gray-500">จำนวน: {{ $detail->ordd_count }} ชิ้น</p>
                                        <p class="text-sm text-gray-500">ราคาต่อชิ้น:
                                            ฿{{ number_format($detail->pd_price, 2) }}</p>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="font-bold text-emerald-600">
                                        ฿{{ number_format($detail->pd_price * $detail->ordd_count, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right Column: Summary & Shipping --}}
                <div>
                    {{-- Shipping Address --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 mb-6">
                        <h3 class="font-bold text-gray-800 mb-3 text-base">ข้อมูลการจัดส่ง</h3>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p class="font-semibold text-gray-900">{{ $order->shipping_name }}</p>
                            @php
                                $addressParts = explode("\nหมายเหตุ:", $order->shipping_address, 2);
                                $mainAddress = $addressParts[0];
                                $noteText = isset($addressParts[1]) ? trim($addressParts[1]) : null;
                            @endphp
                            <p>{!! nl2br(e($mainAddress)) !!}</p>
                            <div class="divider my-2"></div>
                            {{-- <p class="pt-2 font-semibold text-gray-900">เบอร์โทรศัพท์: {{ $order->shipping_phone }}</p> --}}
                            <p class="max-h-20 overflow-y-auto"><span class="font-semibold text-gray-700">เบอร์โทรศัพท์:</span> {{ $order->shipping_phone }}</p>
                            @if ($noteText)
                                <div class="divider my-2"></div>
                                <p class="max-h-20 overflow-y-auto"><span class="font-semibold text-gray-700">หมายเหตุ:</span> {{ $noteText }}</p>
                            @endif
                            {{-- <p class="pt-2">เบอร์โทรศัพท์: {{ $order->shipping_phone }}</p> --}}
                            {{-- <p class="pt-2">เบอร์โทรศัพท์: {{ $order->shipping_phone }}</p> --}}
                        </div>
                    </div>

                    {{-- Order Summary --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                        <h3 class="font-bold text-gray-800 mb-4 text-base">สรุปยอดชำระ</h3>
                        <div class="space-y-2 text-sm text-gray-600 mb-4">
                            <div class="flex justify-between">
                                <span>รวมการสั่งซื้อ</span>
                                <span class="font-medium text-gray-900">฿{{ number_format($order->total_price, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>ค่าจัดส่ง</span>
                                <span
                                    class="font-medium text-gray-900">฿{{ number_format($order->shipping_cost, 2) }}</span>
                            </div>
                            @if ($order->total_discount > 0)
                                <div class="flex justify-between text-green-600">
                                    <span>ส่วนลด</span>
                                    <span>-฿{{ number_format($order->total_discount, 2) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex justify-between items-center border-t border-gray-200 pt-4">
                            <span class="font-bold text-gray-800">ยอดชำระทั้งหมด</span>
                            <span class="font-bold text-red-500 text-xl">฿{{ number_format($order->net_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
