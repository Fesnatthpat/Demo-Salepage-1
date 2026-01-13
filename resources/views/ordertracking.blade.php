@extends('layout')

@section('title', 'ติดตามคำสั่งซื้อ | Salepage Demo')

@section('content')

    @php
        // Reuse status mapping logic from orderdetail.blade.php
        $statusMap = [
            1 => ['text' => 'รอชำระเงิน', 'class' => 'bg-yellow-100 text-yellow-800', 'step_label' => 'สั่งซื้อสินค้า'],
            2 => ['text' => 'กำลังดำเนินการ', 'class' => 'bg-blue-100 text-blue-800', 'step_label' => 'กำลังเตรียมของ'],
            3 => ['text' => 'จัดส่งแล้ว', 'class' => 'bg-green-100 text-green-800', 'step_label' => 'จัดส่งแล้ว'],
            4 => ['text' => 'สำเร็จ', 'class' => 'bg-emerald-100 text-emerald-800', 'step_label' => 'สำเร็จ'],
            5 => ['text' => 'ยกเลิก', 'class' => 'bg-red-100 text-red-800', 'step_label' => 'ยกเลิก'],
        ];

        // Define steps for the tracking bar with icons
        $trackingSteps = [
            1 => [
                'label' => 'สั่งซื้อสินค้า',
                'icon' =>
                    'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            ],
            2 => [
                'label' => 'กำลังเตรียมของ',
                'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            ],
            3 => [
                'label' => 'จัดส่งแล้ว',
                'icon' =>
                    'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v9h1m14.39 2.09A2.518 2.518 0 0018.63 8c-.314 0-.621.06-.91.17l-9.72 3.61A1 1 0 007 12.73V16m9 0h2.5a.5.5 0 01.5.5v.5a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5v-.5a.5.5 0 01.5-.5z',
            ],
            4 => ['label' => 'สำเร็จ', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ];
    @endphp

    <div class="container mx-auto px-4 py-8 lg:py-12 min-h-screen">

        <div class="max-w-4xl mx-auto">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">ติดตามคำสั่งซื้อ</h1>
                    @if (isset($order))
                        <p class="text-gray-500">หมายเลขคำสั่งซื้อ: <span
                                class="text-gray-900 font-medium">{{ $order->ord_code }}</span></p>
                    @endif
                </div>
                <div class="flex gap-3">
                    <a href="/contact" class="btn btn-outline btn-sm">ติดต่อร้านค้า</a>
                    @auth
                        <a href="{{ route('order.history') }}" class="btn btn-ghost btn-sm">ย้อนกลับ</a>
                    @endauth
                    @guest
                        <a href="{{ route('home') }}" class="btn btn-ghost btn-sm">กลับหน้าหลัก</a>
                    @endguest
                </div>
            </div>

            {{-- Tracking Form --}}
            <div class="bg-white p-6 lg:p-10 rounded-xl shadow-lg border border-gray-100 mb-8">
                <form action="{{ route('order.tracking') }}" method="POST">
                    @csrf
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="form-control flex-grow">
                            <label class="label hidden sm:block"><span class="label-text">รหัสคำสั่งซื้อ</span></label>
                            <input type="text" name="order_code" placeholder="กรอกรหัสคำสั่งซื้อ"
                                class="input input-bordered w-full"
                                value="{{ old('order_code', $order->ord_code ?? '') }}" />
                            @error('order_code')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-control flex-grow">
                            <label class="label hidden sm:block"><span
                                    class="label-text">เบอร์โทรศัพท์ที่ใช้สั่งซื้อ</span></label>
                            <input type="tel" name="phone" placeholder="กรอกเบอร์โทรศัพท์ที่ใช้ในการสั่งซื้อ"
                                class="input input-bordered w-full"
                                value="{{ old('phone', auth()->user()->phone ?? '') }}" />
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-control flex-shrink-0 sm:self-end">
                            <button type="submit" class="btn btn-primary text-white sm:w-auto w-full">ติดตาม</button>
                        </div>
                    </div>
                </form>
            </div>

            @if (isset($order))
                @php
                    $currentStep = $order->status_id;
                    $statusInfo = $statusMap[$order->status_id] ?? [
                        'text' => 'ไม่ระบุ',
                        'class' => 'bg-gray-100 text-gray-800',
                    ];
                @endphp
                {{-- ================= TRACKING STATUS BAR ================= --}}
                <div class="bg-white p-6 lg:p-10 rounded-xl shadow-lg border border-gray-100 mb-8 overflow-hidden">
                    <div class="relative">
                        {{-- Progress Line Background (สีเทา) --}}
                        <div class="absolute top-5 left-5 right-5 h-1 bg-gray-200 rounded-full -z-10 hidden md:block"></div>

                        {{-- Progress Line Active (สีเขียว) --}}
                        {{-- คำนวณความกว้าง: (step - 1) / (total - 1) * 100 --}}
                        <div class="absolute top-5 left-5 h-1 bg-emerald-500 rounded-full -z-10 transition-all duration-500 hidden md:block"
                            style="width: {{ (($currentStep - 1) / (count($trackingSteps) - 1)) * 100 }}%"></div>

                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 md:gap-0">
                            @foreach ($trackingSteps as $key => $step)
                                @php
                                    $isActive = $key <= $currentStep;
                                    $isCurrent = $key === $currentStep;
                                    $stepDate =
                                        isset($order) && $key <= $order->status_id
                                            ? $order->formatted_ord_date
                                            : 'รอการอัปเดต';
                                @endphp

                                {{-- Step Item --}}
                                <div
                                    class="relative flex md:flex-col items-center gap-4 md:gap-2 w-full md:w-auto {{ $isActive ? 'text-emerald-600' : 'text-gray-400' }}">

                                    {{-- Line for Mobile (Vertical) --}}
                                    @if (!$loop->last)
                                        <div
                                            class="absolute left-[19px] top-10 bottom-[-24px] w-0.5 bg-gray-200 md:hidden -z-10">
                                        </div>
                                        @if ($isActive && $key < $currentStep)
                                            <div
                                                class="absolute left-[19px] top-10 bottom-[-24px] w-0.5 bg-emerald-500 md:hidden -z-10">
                                            </div>
                                        @endif
                                    @endif

                                    {{-- Icon Circle --}}
                                    <div
                                        class="w-10 h-10 flex items-center justify-center rounded-full border-2 bg-white z-10 transition-all duration-300
                                        {{ $isActive ? 'border-emerald-500 bg-emerald-50' : 'border-gray-300' }}
                                        {{ $isCurrent ? 'ring-4 ring-emerald-100 scale-110' : '' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="{{ $step['icon'] }}"></path>
                                        </svg>
                                    </div>

                                    {{-- Text --}}
                                    <div class="flex flex-col md:items-center">
                                        <span
                                            class="font-bold text-sm md:text-base {{ $isActive ? 'text-gray-900' : 'text-gray-400' }}">
                                            {{ $step['label'] }}
                                        </span>
                                        <span class="text-xs text-gray-400 mt-1 md:mt-0">
                                            @if ($key === 1 && isset($order))
                                                {{ $order->formatted_ord_date }}
                                            @else
                                                {{ $stepDate }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- ================= ORDER DETAILS ================= --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- Product List --}}
                    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-4 mb-4">รายการสินค้า</h3>

                        <div class="space-y-4">
                            @foreach ($order->details as $detail)
                                <div class="flex gap-4">
                                    @if ($detail->productSalepage && $detail->productSalepage->images->isNotEmpty())
                                        <img src="{{ asset('storage/' . (optional($detail->productSalepage->images->first())->image_path ?? 'images/img.png')) }}"
                                            class="w-20 h-20 rounded-md object-cover border border-gray-100"
                                            alt="{{ $detail->productSalepage->pd_sp_name ?? 'Product Image' }}">
                                    @endif
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">
                                            {{ $detail->productSalepage->pd_sp_name ?? 'ไม่พบข้อมูลสินค้า' }}</h4>
                                        <p class="text-sm text-gray-500">จำนวน: {{ $detail->ordd_count }} ชิ้น</p>
                                        <div class="flex justify-between items-center mt-2">
                                            <span class="text-gray-500 text-sm">x{{ $detail->ordd_count }}</span>
                                            <span
                                                class="font-bold text-gray-900">฿{{ number_format($detail->pd_price * $detail->ordd_count, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Totals --}}
                        <div class="bg-gray-50 rounded-lg p-4 mt-6 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">รวมการสั่งซื้อ</span>
                                <span>฿{{ number_format($order->total_price, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">ค่าจัดส่ง</span>
                                <span>฿{{ number_format($order->shipping_cost, 2) }}</span>
                            </div>
                            @if ($order->total_discount > 0)
                                <div class="flex justify-between text-sm text-green-600">
                                    <span>ส่วนลด</span>
                                    <span>-฿{{ number_format($order->total_discount, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2 mt-2">
                                <span>ยอดรวมสุทธิ</span>
                                <span class="text-emerald-600">฿{{ number_format($order->net_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Shipping & Info --}}
                    <div class="flex flex-col gap-6">
                        {{-- Address --}}
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                ที่อยู่จัดส่ง
                            </h3>
                            <p class="font-medium text-gray-900">{{ $order->shipping_name }}</p>
                            <p class="text-sm text-gray-500 mt-1 leading-relaxed">
                                {{ $order->shipping_address }}<br>
                                โทร: {{ $order->shipping_phone }}
                            </p>
                        </div>

                        {{-- Help --}}
                        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 text-center">
                            <p class="text-sm text-blue-800 mb-2">มีปัญหากับคำสั่งซื้อ?</p>
                            <a href="/contact" class="text-blue-600 font-bold text-sm underline">ติดต่อศูนย์ช่วยเหลือ</a>
                        </div>
                    </div>

                </div>
            @endif
        </div>
    </div>
@endsection
