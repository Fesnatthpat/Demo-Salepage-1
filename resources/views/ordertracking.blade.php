@extends('layout')

@section('title', 'ติดตามคำสั่งซื้อ | Salepage Demo')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #fef2f2;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .timeline-pulse {
            box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
            animation: pulse-red 2s infinite;
            border-radius: 50%;
        }

        @keyframes pulse-red {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(220, 38, 38, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0);
            }
        }
    </style>

    <div class="container mx-auto px-4 py-10 min-h-screen">
        <div class="max-w-3xl mx-auto">

            {{-- Header & Search --}}
            <div class="text-center mb-10">
                <h1 class="text-2xl font-bold text-slate-800 mb-6 uppercase tracking-tight">Track Your Order</h1>

                <div class="bg-white p-2 rounded-2xl shadow-xl shadow-red-100 border border-red-50">
                    <form action="{{ route('order.tracking') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                        <div class="relative flex-grow">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-red-300"></i>
                            <input type="text" name="search" placeholder="กรอกรหัสการจัดส่ง หรือเบอร์โทรศัพท์ของคุณ..."
                                class="input border-none bg-red-50/50 w-full pl-12 focus:ring-2 focus:ring-red-500 focus:outline-none h-14 rounded-xl text-lg text-red-900"
                                value="{{ request('search') ?? request('order_code') }}" required />
                        </div>
                        <button type="submit"
                            class="btn btn-primary bg-red-600 hover:bg-red-700 border-none text-white h-14 px-10 rounded-xl text-lg transition-all shadow-lg shadow-red-200">
                            ค้นหาพัสดุ
                        </button>
                    </form>
                </div>
            </div>

            @if (session('error'))
                <div
                    class="alert alert-error bg-rose-100 border-rose-200 text-rose-700 mb-8 rounded-xl animate-fadeIn shadow-sm">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if (isset($trackingData))
                <div class="animate-fadeIn space-y-6">

                    @foreach ($trackingData as $data)
                        @if (isset($data['is_external']) && $data['is_external'])
                            {{-- 📌 แบบที่ 1: ขนส่งภายนอก มีแค่ปุ่มลิงก์ --}}
                            <div
                                class="bg-white rounded-3xl shadow-sm border border-red-100 overflow-hidden p-8 flex flex-col md:flex-row items-center gap-6">
                                <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center shrink-0">
                                    <i class="fas fa-shipping-fast text-2xl text-red-600"></i>
                                </div>
                                <div class="flex-grow text-center md:text-left">
                                    <h3 class="text-lg font-bold text-slate-800 mb-1">
                                        {{ $data['carrier_name'] }}
                                    </h3>
                                    <p class="text-slate-500 text-sm mb-1">หมายเลขคำสั่งซื้อ: <span
                                            class="font-bold text-red-600">{{ $data['tracking_number'] }}</span></p>
                                    <p class="text-slate-400 text-xs italic">วันที่สั่งซื้อ: {{ $data['order_date'] }}</p>
                                </div>
                                <div class="shrink-0">
                                    <a href="{{ $data['external_url'] }}" target="_blank"
                                        class="btn btn-primary bg-red-600 hover:bg-red-700 border-none text-white px-6 rounded-xl transition-all shadow-md">
                                        ไปที่หน้าติดตามพัสดุ
                                    </a>
                                </div>
                            </div>
                        @else
                            {{-- 📌 แบบที่ 2: ระบบ Timeline ภายใน --}}
                            <div class="bg-white rounded-3xl shadow-sm border border-red-100 overflow-hidden mb-6">
                                {{-- Top Status Banner --}}
                                <div class="bg-red-600 p-6 flex justify-between items-center text-white">
                                    <div>
                                        <p class="text-red-100 text-xs font-bold uppercase tracking-wider mb-1">
                                            สถานะปัจจุบัน</p>
                                        <h3 class="text-xl font-bold flex items-center gap-2">
                                            <span class="w-3 h-3 bg-white rounded-full inline-block animate-pulse"></span>
                                            {{ $data['status_text'] }}
                                        </h3>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-red-100 text-xs font-bold uppercase tracking-wider mb-1">เลขพัสดุ</p>
                                        <p class="font-black tracking-widest uppercase">{{ $data['tracking_number'] }}</p>
                                    </div>
                                </div>

                                {{-- รายละเอียด Timeline (Events) --}}
                                @if (!empty($data['timeline_data']) && is_array($data['timeline_data']))
                                    <div class="p-8">
                                        <div class="relative border-l-2 border-red-200 ml-4 space-y-8">
                                            @foreach ($data['timeline_data'] as $index => $timeline)
                                                <div class="relative pl-8">
                                                    {{-- วงกลมของแต่ละ Timeline --}}
                                                    <span
                                                        class="absolute -left-[11px] top-1 w-5 h-5 rounded-full {{ $index === 0 ? 'bg-red-500 timeline-pulse' : 'bg-red-300' }} border-4 border-white"></span>

                                                    <div
                                                        class="flex flex-col sm:flex-row sm:justify-between sm:items-baseline gap-1">
                                                        <h4 class="text-base font-bold text-slate-800">
                                                            {{ $timeline['description'] ?? 'อัปเดตสถานะ' }}
                                                        </h4>
                                                        <span class="text-sm font-semibold text-slate-500">
                                                            {{ $timeline['dateTime'] ?? '' }}
                                                        </span>
                                                    </div>

                                                    {{-- โชว์สถานที่และข้อความจำลองเพิ่มเติม --}}
                                                    @if (isset($timeline['is_system_generated']) && $timeline['is_system_generated'])
                                                        <p class="text-sm text-slate-600 mt-1">
                                                            ระบบได้รับคำสั่งซื้อของคุณแล้ว
                                                            และกำลังอยู่ในขั้นตอนการเตรียมพัสดุเพื่อนำส่งให้บริษัทขนส่ง
                                                        </p>
                                                    @elseif(!empty($timeline['address']['city']))
                                                        <p class="text-sm text-slate-600 mt-1">
                                                            <i class="fas fa-map-marker-alt text-red-400 mr-1"></i>
                                                            {{ $timeline['address']['city'] }}
                                                            {{ $timeline['address']['postCode'] ?? '' }}
                                                        </p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="p-8 text-center text-slate-500">
                                        ไม่พบรายละเอียดเส้นทางการจัดส่ง
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach

                </div>
            @endif
        </div>
    </div>
@endsection
