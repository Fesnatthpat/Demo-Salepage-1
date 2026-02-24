@extends('layout')

@section('title', 'ติดตามคำสั่งซื้อ | Salepage Demo')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <div class="container mx-auto px-4 py-8 lg:py-12 min-h-screen">
        <div class="max-w-5xl mx-auto">
            
            {{-- ส่วนหัว --}}
            <div class="mb-8">
                <span class="text-sm font-bold text-gray-800">รหัสการจัดส่ง</span>
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mt-1">
                    <h2 class="text-3xl font-black text-gray-900 tracking-wide uppercase">
                        {{ $trackingData['trackingNumber'] ?? request('order_code') ?? 'N/A' }}
                    </h2>
                    <div class="flex gap-2">
                        <button class="btn btn-outline btn-square text-red-600 border-red-200 hover:bg-red-50 hover:border-red-600">
                            <i class="fas fa-history"></i>
                        </button>
                        <button class="btn btn-outline btn-square text-red-600 border-red-200 hover:bg-red-50 hover:border-red-600">
                            <i class="fas fa-envelope"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ฟอร์มค้นหา --}}
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
                <form action="{{ route('order.tracking') }}" method="GET">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="form-control flex-grow relative">
                            <input type="text" name="order_code" placeholder="กรอกรหัสการจัดส่ง เพื่อติดตามสถานะ"
                                class="input input-bordered w-full" value="{{ request('order_code') }}" required />
                        </div>
                        <button type="submit" class="btn btn-primary bg-emerald-600 hover:bg-emerald-700 border-none text-white sm:w-auto w-full px-8">
                            ค้นหา
                        </button>
                    </div>
                </form>
            </div>

            @if (session('error'))
                <div class="alert alert-error mb-8">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if (isset($trackingData))
                {{-- กรอบข้อมูลหลัก (มีขอบสีเขียวด้านซ้ายเหมือนต้นฉบับ) --}}
                <div class="bg-white border border-gray-200 border-l-[3px] border-l-green-700 mb-8">
                    
                    {{-- Status Banner --}}
                    <div class="p-4 flex flex-col md:flex-row items-start md:items-center gap-4 border-b border-gray-100">
                        <span class="bg-green-700 text-white px-3 py-1 text-sm font-bold rounded">จัดส่งสำเร็จ</span>
                        <span class="text-xs font-bold text-gray-800">จัดส่งภายใน: <span class="font-normal">{{ $trackingData['deliveredAt'] }}</span></span>
                    </div>

                    {{-- Origin / Destination --}}
                    <div class="bg-gray-50 p-6 flex flex-col md:flex-row gap-6 border-b border-gray-200">
                        <div class="flex-1 text-center border-b md:border-b-0 md:border-r border-gray-200 pb-4 md:pb-0">
                            <p class="text-red-600 font-bold text-sm mb-2">
                                <i class="fas fa-plane-departure"></i> ที่มา
                            </p>
                            <p class="text-[11px] text-gray-800 font-bold uppercase">{{ $trackingData['origin'] }}</p>
                        </div>
                        <div class="flex-1 text-center">
                            <p class="text-red-600 font-bold text-sm mb-2">
                                <i class="fas fa-plane-arrival"></i> ปลายทาง
                            </p>
                            <p class="text-[11px] text-gray-800 font-bold uppercase">{{ $trackingData['destination'] }}</p>
                        </div>
                    </div>

                    {{-- Horizontal Status Timeline --}}
                    <div class="p-6 md:p-8 border-b border-gray-200">
                        <h3 class="font-bold text-gray-800 text-lg mb-8">สถานะการจัดส่ง</h3>

                        <div class="relative flex justify-between items-center max-w-4xl mx-auto px-4 mt-8">
                            <div class="absolute left-0 right-0 top-1/2 h-1 bg-green-700 -z-10 -translate-y-1/2"></div>
                            @foreach ($trackingData['timelineSteps'] as $step)
                                <div class="flex flex-col items-center relative bg-white px-2">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white
                                        {{ $step['active'] ? 'bg-green-700' : 'bg-gray-300' }}
                                        {{ isset($step['is_truck']) ? 'w-12 h-12 ring-4 ring-green-100' : '' }}">
                                        @if (isset($step['is_truck']))
                                            <i class="fas fa-truck text-lg"></i>
                                        @else
                                            <i class="fas fa-check text-sm"></i>
                                        @endif
                                    </div>
                                    <span class="text-[10px] font-bold {{ isset($step['is_truck']) ? 'text-gray-900' : 'text-gray-500' }} absolute top-12 w-24 text-center">
                                        {{ $step['label'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        <div class="h-12 md:hidden"></div>
                    </div>

                    {{-- Accordion --}}
                    <div class="collapse collapse-arrow bg-white border-b border-gray-200 rounded-none">
                        <input type="checkbox" checked />
                        <div class="collapse-title text-base font-bold text-gray-800">
                            ข้อมูลการจัดส่งเพิ่มเติม
                        </div>
                        <div class="collapse-content text-sm text-gray-600 bg-white">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-4">
                                <div>
                                    <p class="text-gray-500 text-[11px] mb-1">รหัสการจัดส่ง</p>
                                    <p class="text-xs text-gray-800">{{ $trackingData['trackingNumber'] }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-[11px] mb-1">รหัสการติดตามสินค้า</p>
                                    <p class="text-xs text-gray-800">{{ $trackingData['referenceId'] }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-[11px] mb-1">น้ำหนัก (กรัม)</p>
                                    <p class="text-xs text-gray-800">{{ $trackingData['weight'] }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-[11px] mb-1">บริการจัดส่ง</p>
                                    <p class="text-xs text-gray-800">{{ $trackingData['service'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Vertical Events Timeline (ประวัติสถานะพัสดุ - จัด Layout ใหม่ตรงตามภาพเป๊ะ) --}}
                    <div class="p-6 md:p-8 bg-white">
                        <h3 class="font-bold text-gray-800 text-lg mb-8">สถานะพัสดุ</h3>

                        <div class="max-w-4xl">
                            @foreach ($trackingData['events'] as $event)
                                <div class="flex items-stretch group">
                                    
                                    {{-- 1. Date (วันที่ - ซ้ายสุด) --}}
                                    <div class="w-24 md:w-32 flex-shrink-0 text-xs text-gray-700 uppercase pt-1 pr-4 hidden sm:block">
                                        @if ($event['date'])
                                            {!! $event['date'] !!}
                                        @endif
                                    </div>

                                    {{-- 2. Time (เวลา - ถัดมา) --}}
                                    <div class="w-16 flex-shrink-0 text-xs text-gray-800 pt-1 text-center pr-4">
                                        {{ $event['time'] }}
                                    </div>

                                    {{-- 3. Line & Icon (จุดสถานะและเส้น) --}}
                                    <div class="relative flex flex-col items-center w-8 flex-shrink-0">
                                        
                                        {{-- จุด (สีเขียวเข้มสำหรับสถานะล่าสุด, สีเขียวอ่อนสำหรับสถานะอดีต) --}}
                                        @if ($event['is_latest'])
                                            <div class="relative z-10 w-6 h-6 rounded-full bg-green-700 text-white flex items-center justify-center">
                                                <i class="fas fa-check text-[10px]"></i>
                                            </div>
                                        @else
                                            <div class="relative z-10 w-6 h-6 rounded-full bg-green-200 text-white flex items-center justify-center">
                                                <i class="fas fa-check text-[10px]"></i>
                                            </div>
                                        @endif

                                        {{-- เส้นตรงเชื่อมต่อด้านล่าง (ยกเว้นอันสุดท้าย) --}}
                                        @if (!$loop->last)
                                            <div class="absolute top-6 bottom-[-1.5rem] w-[2px] bg-green-200"></div>
                                        @endif
                                    </div>

                                    {{-- 4. Status Content (รายละเอียดสถานะและสถานที่) --}}
                                    <div class="flex-grow pl-4 pb-6 pt-1">
                                        {{-- แสดงวันที่บนมือถือ (หน้าจอเล็ก) --}}
                                        @if ($event['date'])
                                            <div class="sm:hidden text-[10px] font-bold text-gray-500 uppercase mb-2">
                                                {!! strip_tags($event['date'], ' ') !!}
                                            </div>
                                        @endif

                                        <h4 class="text-sm font-bold text-gray-900">
                                            {{ $event['status'] }}
                                        </h4>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $event['location'] }}
                                        </p>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            @endif

        </div>
    </div>
@endsection