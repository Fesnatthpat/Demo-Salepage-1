@extends('layout')

@section('title', 'เกี่ยวกับเรา | Salepage Demo')

@section('content')
    {{-- Import AOS Animation CSS --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <div class="bg-gray-50 min-h-screen overflow-x-hidden font-sans pb-20">

        {{-- ★★★ HERO SECTION: ดึงข้อความจาก Settings ★★★ --}}
        <div class="relative bg-gradient-to-br from-red-700 via-red-600 to-red-500 text-white pt-20 pb-32 overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-20 pointer-events-none">
                <div
                    class="absolute -top-20 -left-20 w-72 h-72 bg-white rounded-full mix-blend-overlay filter blur-3xl animate-pulse">
                </div>
            </div>
            <div class="container mx-auto px-4 relative z-10 text-center" data-aos="zoom-in" data-aos-duration="1000">
                <div class="mb-8 flex justify-center">
                    <div
                        class="p-6 bg-white/10 backdrop-blur-md rounded-full shadow-2xl ring-4 ring-white/20 transform hover:scale-105 transition-transform duration-500">
                        {{-- ดึงรูปโลโก้ --}}
                        <img src="{{ isset($settings['site_logo']) ? asset('storage/' . $settings['site_logo']) : asset('images/logo/logo1.png') }}"
                            alt="Logo" class="h-24 w-auto drop-shadow-lg object-contain"
                            onerror="this.src='https://via.placeholder.com/150x150?text=LOGO';" />
                    </div>
                </div>
                <h1 class="text-4xl md:text-6xl font-extrabold mb-4 tracking-wide text-shadow-lg">
                    {{ $settings['about_title'] ?? 'เกี่ยวกับติดใจ' }}
                </h1>
                <p class="text-red-100 text-lg md:text-xl font-light max-w-2xl mx-auto">
                    {{ $settings['about_subtitle'] ?? 'ความตั้งใจของเรา...เพื่อส่งมอบความสุขให้คุณ' }}
                </p>
            </div>
            <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none">
                <svg class="relative block w-full h-16 md:h-24" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path
                        d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"
                        class="fill-gray-50"></path>
                </svg>
            </div>
        </div>

        {{-- ★★★ MAIN CONTENT ZONE ★★★ --}}
        <div class="container mx-auto max-w-5xl px-4 -mt-20 relative z-20">

            {{-- วนลูปแสดงเนื้อหา (ไม่มีปุ่มแอดมินแล้ว) --}}
            @forelse($favorites as $index => $fav)
                <div class="bg-white rounded-3xl shadow-xl p-8 md:p-12 mb-12" data-aos="fade-up">
                    <div class="flex flex-col md:flex-row items-center gap-12">

                        {{-- ข้อความ (สลับซ้าย/ขวาอัตโนมัติ) --}}
                        <div
                            class="w-full {{ $fav->image_path ? 'md:w-1/2' : '' }} {{ $index % 2 == 0 ? 'order-2 md:order-1' : 'order-2 md:order-2' }}">
                            <div class="flex items-center gap-3 mb-6">
                                <span class="w-1.5 h-8 bg-red-600 rounded-full"></span>
                                <h2 class="text-3xl font-bold text-gray-800">{{ $fav->title }}</h2>
                            </div>
                            <div class="prose prose-lg text-gray-600 leading-loose">{!! $fav->content !!}</div>
                        </div>

                        {{-- รูปภาพ (ถ้ามี) --}}
                        @if ($fav->image_path)
                            <div
                                class="w-full md:w-1/2  {{ $index % 2 == 0 ? 'order-1 md:order-2' : 'order-1 md:order-1' }} relative group-img">
                                <div
                                    class="absolute inset-0 bg-red-600 rounded-2xl transform rotate-3 transition-transform duration-300 opacity-10 group-hover:rotate-6">
                                </div>
                                <img src="{{ asset('storage/' . $fav->image_path) }}" alt="{{ $fav->title }}"
                                    class="relative rounded-2xl shadow-lg w-full h-full  object-cover border-4 border-white transform transition-transform duration-300 group-hover:-translate-y-2">
                            </div>
                        @endif

                    </div>
                </div>
            @empty
                <div class="text-center py-20 bg-white rounded-3xl shadow-lg border-dashed border-2 border-gray-300 mb-12">
                    <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">เนื้อหากำลังปรับปรุง</p>
                </div>
            @endforelse

            {{-- ★★★ Contact Card: ดึงข้อมูลจาก Settings ★★★ --}}
            <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-2xl shadow-lg p-8 text-white max-w-xl mx-auto mb-10"
                data-aos="fade-up">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-3 border-b border-red-400 pb-4">
                    <i class="fas fa-envelope"></i> ติดต่อเรา
                </h2>
                <div class="space-y-6">
                    <div class="flex items-center gap-4 bg-white/10 p-4 rounded-xl backdrop-blur-sm border border-white/10">
                        <div class="bg-white rounded-full p-3 text-red-700"><i class="fas fa-envelope text-xl"></i></div>
                        <div>
                            <p class="text-xs text-red-200 uppercase font-semibold">อีเมล</p>
                            <p class="font-bold text-lg">{{ $settings['contact_email'] ?? 'support@saledemo.com' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 bg-white/10 p-4 rounded-xl backdrop-blur-sm border border-white/10">
                        <div class="bg-white rounded-full p-3 text-red-700"><i class="fas fa-phone-alt text-xl"></i></div>
                        <div>
                            <p class="text-xs text-red-200 uppercase font-semibold">โทรศัพท์</p>
                            <p class="font-bold text-lg">{{ $settings['contact_phone'] ?? '012-345-6789' }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Init AOS Scripts --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                easing: 'ease-out-cubic',
                once: true,
                offset: 50
            });
        });
    </script>
@endsection
