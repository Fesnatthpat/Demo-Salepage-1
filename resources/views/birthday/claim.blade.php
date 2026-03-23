@extends('layout')

@section('title', 'Happy Birthday! ของขวัญพิเศษสำหรับคุณ')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-pink-50 to-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        {{-- ส่วนหัว --}}
        <div class="text-center mb-10 animate-fade-in">
            <div class="inline-block p-4 bg-white rounded-full shadow-xl mb-6 ring-4 ring-pink-100">
                <i class="fas fa-birthday-cake text-4xl text-pink-500"></i>
            </div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl mb-2">
                Happy Birthday!
            </h1>
            <p class="text-xl text-pink-600 font-medium">
                ขอให้มีความสุขมากๆ ในวันเกิดปีนี้ครับ
            </p>
        </div>

        {{-- การ์ดอวยพรจาก CEO --}}
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden mb-10 transform transition-all hover:scale-[1.01]">
            <div class="aspect-video w-full bg-gray-100 relative">
                @if($campaign->card_image_path)
                    <img src="{{ asset('storage/' . $campaign->card_image_path) }}" class="w-full h-full object-cover" alt="Birthday Card from CEO">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 p-8 text-center border-b border-gray-100">
                        <i class="fas fa-id-card text-6xl mb-4 opacity-20"></i>
                        <p class="text-lg italic">"ขอให้เป็นปีที่ยอดเยี่ยมและเต็มไปด้วยความสำเร็จครับ"</p>
                        <p class="mt-2 font-bold text-gray-500">- จาก CEO -</p>
                    </div>
                @endif
            </div>

            <div class="p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ $campaign->title }}</h2>
                <p class="text-gray-600 leading-relaxed mb-8">
                    {{ $campaign->message }}
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    {{-- ส่วนรหัสส่วนลด --}}
                    @if($campaign->discount_code)
                    <div class="bg-amber-50 border-2 border-dashed border-amber-300 rounded-2xl p-6">
                        <p class="text-xs font-bold text-amber-600 uppercase tracking-widest mb-2">รหัสส่วนลดพิเศษ</p>
                        {{-- <div class="flex items-center justify-center gap-3">
                            <span class="text-2xl font-mono font-bold text-amber-700">{{ $campaign->discount_code }}</span>
                            <button onclick="copyToClipboard('{{ $campaign->discount_code }}')" class="text-amber-500 hover:text-amber-700 transition-colors">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div> --}}
                        @if($campaign->discount_value > 0)
                            <p class="mt-2 text-sm text-amber-600 font-medium">ลดทันที {{ number_format($campaign->discount_value) }} บาท</p>
                        @endif
                    </div>
                    @endif

                    {{-- ส่วนของแถม --}}
                    @if($campaign->giftProduct)
                    <div class="bg-emerald-50 border-2 border-dashed border-emerald-300 rounded-2xl p-6">
                        <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest mb-2">ของขวัญฟรีสำหรับคุณ</p>
                        <div class="flex items-center justify-center gap-3">
                            <i class="fas fa-gift text-2xl text-emerald-500"></i>
                            <span class="text-sm font-bold text-emerald-700 truncate max-w-[120px]">{{ $campaign->giftProduct->pd_sp_name }}</span>
                        </div>
                        <p class="mt-2 text-sm text-emerald-600 font-medium">มูลค่าประมาณ {{ number_format($campaign->giftProduct->pd_sp_price) }} บาท</p>
                    </div>
                    @endif
                </div>

                {{-- เวลานับถอยหลัง --}}
                @if($campaign->end_date)
                <div class="mb-8 p-4 rounded-2xl border transition-all duration-500 flex flex-col items-center justify-center gap-2"
                     :class="isUrgent ? 'bg-red-50 border-red-200 text-red-600 animate-pulse' : 'bg-pink-50 border-pink-100 text-pink-600'"
                     x-data="{
                        remaining: '',
                        isUrgent: false,
                        target: '{{ $campaign->end_date->format('Y-m-d H:i:s') }}',
                        updateTimer() {
                            const diff = new Date(this.target) - new Date();
                            if (diff <= 0) {
                                this.remaining = 'หมดเวลารับสิทธิ์แล้ว';
                                this.isUrgent = false;
                                return;
                            }
                            
                            this.isUrgent = diff < 3600000;

                            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                            const secs = Math.floor((diff % (1000 * 60)) / 1000);
                            
                            let str = '';
                            if (days > 0) str += `${days} วัน `;
                            str += `${hours.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                            this.remaining = str;
                        }
                     }"
                     x-init="updateTimer(); setInterval(() => updateTimer(), 1000)">
                    <p class="text-[10px] font-bold uppercase tracking-widest opacity-70">
                        <i class="fas fa-hourglass-half mr-1"></i> สิทธิ์นี้จะหมดอายุในอีก
                    </p>
                    <p class="text-3xl font-mono font-black tracking-tighter" x-text="remaining"></p>
                </div>
                @endif

                {{-- ปุ่มกดรับสิทธิ์ --}}
                @auth
                    <form action="{{ route('birthday.apply') }}" method="POST">
                        @csrf
                        <input type="hidden" name="campaign_id" value="{{ $campaign->id }}">
                        <button type="submit" class="w-full py-5 bg-gradient-to-r from-pink-600 to-rose-500 text-white rounded-2xl font-bold text-xl shadow-lg shadow-pink-200 hover:shadow-xl hover:from-pink-500 hover:to-rose-400 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3 group">
                            <span>🎁 กดรับของขวัญและเริ่มช้อป</span>
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="w-full py-5 bg-gradient-to-r from-gray-700 to-gray-900 text-white rounded-2xl font-bold text-xl shadow-lg shadow-gray-200 hover:shadow-xl hover:from-gray-600 hover:to-gray-800 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3 group">
                        <span>🔑 เข้าสู่ระบบเพื่อรับของขวัญ</span>
                        <i class="fas fa-sign-in-alt group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    <p class="mt-4 text-sm text-pink-600 font-bold animate-pulse">
                        <i class="fas fa-info-circle mr-1"></i> กรุณาเข้าสู่ระบบเพื่อยืนยันสิทธิ์วันเกิดของคุณ
                    </p>
                @endauth
                
                <p class="mt-6 text-xs text-gray-400 italic">
                    * ระบบจะนำรหัสส่วนลดและของขวัญไปใส่ในตะกร้าสินค้าให้คุณโดยอัตโนมัติ
                </p>
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-800 text-sm font-medium underline underline-offset-4">
                กลับสู่หน้าหลัก
            </a>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('คัดลอกรหัสแล้ว: ' + text);
        });
    }
</script>

<style>
    .animate-fade-in {
        animation: fadeIn 1s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
