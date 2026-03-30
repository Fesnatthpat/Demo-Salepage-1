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

            <div class="bg-white p-6 rounded-3xl shadow-xl shadow-red-100 border border-red-50">
                <form action="{{ route('order.tracking') }}" method="GET" class="flex flex-col gap-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="relative">
                            <i class="fas fa-hashtag absolute left-4 top-1/2 -translate-y-1/2 text-red-300"></i>
                            <input type="text" name="order_code" placeholder="รหัสออเดอร์ (เช่น ORD-...)"
                                class="input border-none bg-red-50/50 w-full pl-12 focus:ring-2 focus:ring-red-500 focus:outline-none h-14 rounded-xl text-lg text-red-900"
                                value="{{ request('order_code') ?? request('search') }}" required />
                        </div>
                        <div class="relative">
                            <i class="fas fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-red-300"></i>
                            <input type="text" name="phone" placeholder="เบอร์โทรศัพท์ที่ใช้สั่งซื้อ"
                                class="input border-none bg-red-50/50 w-full pl-12 focus:ring-2 focus:ring-red-500 focus:outline-none h-14 rounded-xl text-lg text-red-900"
                                value="{{ request('phone') }}" required />
                        </div>
                    </div>
                    <button type="submit"
                        class="btn btn-primary bg-red-600 hover:bg-red-700 border-none text-white h-14 px-10 rounded-xl text-lg transition-all shadow-lg shadow-red-200 w-full">
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
                        <div class="bg-white rounded-3xl shadow-sm border border-red-100 overflow-hidden p-8 flex flex-col md:flex-row items-center gap-6 transition-all hover:shadow-md">
                            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center shrink-0">
                                <i class="fas fa-shipping-fast text-2xl text-red-600"></i>
                            </div>
                            <div class="flex-grow text-center md:text-left">
                                <h3 class="text-lg font-bold text-slate-800 mb-1">
                                    {{ $data['carrier_name'] }}
                                </h3>
                                <p class="text-slate-500 text-sm mb-1">หมายเลขติดตาม: <span
                                        class="font-bold text-red-600">{{ $data['od_ref'] ?? $data['tracking_number'] }}</span></p>
                                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 mt-1">
                                    <p class="text-slate-400 text-xs italic">วันที่สั่งซื้อ: {{ $data['order_date'] }}</p>
                                    @if(!isset($data['is_external']) || !$data['is_external'])
                                        <p class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full w-fit mx-auto md:mx-0">
                                            สถานะ: {{ $data['status_text'] }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="shrink-0 flex flex-col gap-2">
                                @if (isset($data['is_external']) && $data['is_external'])
                                    <a href="{{ $data['external_url'] }}" target="_blank"
                                        class="btn btn-primary bg-red-600 hover:bg-red-700 border-none text-white px-6 rounded-xl transition-all shadow-md w-full">
                                        ไปที่หน้าติดตามพัสดุ
                                    </a>
                                @else
                                    <button type="button" 
                                        onclick="showTimelineModal({{ json_encode($data) }})"
                                        class="btn btn-primary bg-slate-800 hover:bg-slate-900 border-none text-white px-6 rounded-xl transition-all shadow-md w-full">
                                        ดูรายละเอียดสถานะ
                                    </button>
                                @endif
                                
                                <a href="{{ route('orders.show', $data['od_ref'] ?? $data['order_code']) }}" 
                                    class="btn btn-outline border-red-600 text-red-600 hover:bg-red-600 hover:border-red-600 hover:text-white px-6 rounded-xl transition-all w-full text-center py-2 border font-bold text-sm">
                                    รายละเอียดคำสั่งซื้อ
                                </a>
                            </div>
                        </div>
                    @endforeach

                </div>
            @endif
        </div>
    </div>

    {{-- Timeline Modal --}}
    <div id="timelineModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="bg-red-600 px-6 py-4 flex justify-between items-center text-white">
                <div>
                    <h3 class="font-bold text-lg">รายละเอียดสถานะพัสดุ</h3>
                    <p class="text-red-100 text-xs" id="modalTrackingNumber"></p>
                </div>
                <button onclick="closeTimelineModal()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="p-6 max-h-[70vh] overflow-y-auto" id="timelineContent">
                {{-- Timeline items will be injected here --}}
            </div>

            <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                <button onclick="closeTimelineModal()" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-bold transition-colors">
                    ปิดหน้าต่าง
                </button>
            </div>
        </div>
    </div>

    <script>
        function escapeHTML(str) {
            if (!str) return '';
            const p = document.createElement('p');
            p.textContent = str;
            return p.innerHTML;
        }

        function showTimelineModal(data) {
            const modal = document.getElementById('timelineModal');
            const modalContent = document.getElementById('timelineContent');
            const modalTracking = document.getElementById('modalTrackingNumber');
            
            modalTracking.innerText = 'หมายเลขติดตาม: ' + data.tracking_number;
            
            let html = '';
            if (data.timeline_data && data.timeline_data.length > 0) {
                html = '<div class="relative border-l-2 border-red-200 ml-3 space-y-8 py-2">';
                data.timeline_data.forEach((item, index) => {
                    const description = escapeHTML(item.description || 'อัปเดตสถานะ');
                    const dateTime = escapeHTML(item.dateTime || '');
                    const city = escapeHTML(item.address?.city || '');
                    const postCode = escapeHTML(item.address?.postCode || '');

                    html += `
                        <div class="relative pl-8">
                            <span class="absolute -left-[11px] top-1 w-5 h-5 rounded-full ${index === 0 ? 'bg-red-500 timeline-pulse' : 'bg-red-300'} border-4 border-white"></span>
                            <div class="flex flex-col gap-1">
                                <h4 class="text-base font-bold text-slate-800 leading-tight">${description}</h4>
                                <span class="text-xs font-semibold text-slate-500">${dateTime}</span>
                                ${item.is_system_generated ? 
                                    '<p class="text-xs text-slate-600 mt-1 italic">ระบบได้รับคำสั่งซื้อของคุณแล้วและกำลังเตรียมการจัดส่ง</p>' : 
                                    (item.address && item.address.city ? `<p class="text-xs text-slate-600 mt-1"><i class="fas fa-map-marker-alt text-red-400 mr-1"></i> ${city} ${postCode}</p>` : '')
                                }
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            } else {
                html = '<div class="text-center py-10 text-slate-500 italic">ไม่พบรายละเอียดเส้นทางการจัดส่ง</div>';
            }
            
            modalContent.innerHTML = html;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('div').classList.remove('scale-95');
            }, 10);
            document.body.style.overflow = 'hidden';
        }

        function closeTimelineModal() {
            const modal = document.getElementById('timelineModal');
            modal.classList.add('opacity-0');
            modal.querySelector('div').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('timelineModal');
            if (event.target == modal) {
                closeTimelineModal();
            }
        }
    </script>
        </div>
    </div>
@endsection
