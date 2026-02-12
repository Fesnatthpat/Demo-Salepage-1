@extends('layout')

@section('title', 'ตรวจสอบสถานะพัสดุ')

@section('content')
    <div class="container mx-auto p-4 lg:px-20 lg:py-10 max-w-4xl">
        <div class="bg-white border border-gray-200 rounded-lg p-6 lg:p-8 shadow-sm">

            {{-- ★★★ ส่วน Logo (เพิ่มใหม่) ★★★ --}}
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/logo/logo1.png') }}" alt="Logo"
                    class="w-36 h-36 object-cover hover:scale-120 transition-transform duration-300 drop-shadow-sm">
            </div>

            <h1 class="text-2xl font-bold text-gray-800 text-center mb-2">
                ตรวจสอบสถานะพัสดุ
            </h1>
            <p class="text-center text-gray-500 mb-8">
                กรุณากรอกหมายเลขคำสั่งซื้อของคุณเพื่อตรวจสอบสถานะล่าสุด
            </p>

            {{-- Search Form --}}
            <form action="{{ route('order.tracking') }}" method="POST" class="max-w-xl mx-auto">
                @csrf
                <div
                    class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-200 transition-all">
                    <input type="text" name="order_code" placeholder="เช่น ORD-..."
                        class="input input-ghost w-full focus:outline-none text-lg bg-transparent"
                        value="{{ old('order_code', $order->ord_code ?? '') }}" required />
                    <button type="submit"
                        class="btn btn-primary bg-emerald-600 hover:bg-emerald-700 border-none text-white px-8">
                        <i class="fas fa-search mr-2"></i>
                        ค้นหา
                    </button>
                </div>
            </form>

            {{-- Error Message --}}
            @if (session('error'))
                <div class="max-w-xl mx-auto mt-6">
                    <div class="alert alert-error shadow-lg bg-red-100 border-red-200 text-red-700">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6"
                                fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                </div>
            @endif


            {{-- Result Display --}}
            @if (isset($order))
                @php
                    // Map status ID to text and color
                    $statusMap = [
                        1 => [
                            'text' => 'รอชำระเงิน',
                            'class' => 'bg-yellow-100 text-yellow-800',
                            'icon' => 'fa-hourglass-half',
                        ],
                        2 => ['text' => 'กำลังดำเนินการ', 'class' => 'bg-blue-100 text-blue-800', 'icon' => 'fa-cogs'],
                        3 => ['text' => 'จัดส่งแล้ว', 'class' => 'bg-green-100 text-green-800', 'icon' => 'fa-truck'],
                        4 => [
                            'text' => 'สำเร็จ',
                            'class' => 'bg-emerald-100 text-emerald-800',
                            'icon' => 'fa-check-circle',
                        ],
                        5 => ['text' => 'ยกเลิก', 'class' => 'bg-red-100 text-red-800', 'icon' => 'fa-times-circle'],
                    ];
                    $statusInfo = $statusMap[$order->status_id] ?? [
                        'text' => 'ไม่ระบุ',
                        'class' => 'bg-gray-100 text-gray-800',
                        'icon' => 'fa-question-circle',
                    ];
                @endphp
                <div class="max-w-xl mx-auto mt-8 border-t border-gray-200 pt-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">ผลการค้นหา</h2>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">หมายเลขคำสั่งซื้อ:</span>
                            <span class="font-bold text-gray-800">{{ $order->ord_code }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">วันที่สั่งซื้อ:</span>
                            <span class="font-semibold text-gray-800">{{ $order->created_at->format('d/m/Y H:i') }}
                                น.</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">สถานะปัจจุบัน:</span>
                            <span
                                class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusInfo['class'] }}">
                                <i class="fas {{ $statusInfo['icon'] }} mr-2"></i> {{ $statusInfo['text'] }}
                            </span>
                        </div>
                        {{-- Placeholder for Tracking Number --}}
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">หมายเลขพัสดุ:</span>
                            <span class="font-semibold text-gray-800">
                                {{-- This column needs to be added to the 'orders' table --}}
                                {{ $order->tracking_number ?? 'ยังไม่มีหมายเลขพัสดุ' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
