@extends('layout')

@section('title', 'เกี่ยวกับเรา | Salepage Demo')

@section('content')
    {{-- Import AOS Animation CSS --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <div class="bg-gray-50 min-h-screen overflow-x-hidden font-sans">

        {{-- ★★★ HERO SECTION: พื้นหลังสีแดงไล่เฉด + โลโก้ ★★★ --}}
        <div class="relative bg-gradient-to-br from-red-700 via-red-600 to-red-500 text-white pt-20 pb-32 overflow-hidden">
            {{-- Background Decoration --}}
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-20 pointer-events-none">
                <div
                    class="absolute -top-20 -left-20 w-72 h-72 bg-white rounded-full mix-blend-overlay filter blur-3xl animate-pulse">
                </div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-black rounded-full mix-blend-overlay filter blur-3xl">
                </div>
            </div>

            <div class="container mx-auto px-4 relative z-10 text-center" data-aos="zoom-in" data-aos-duration="1000">
                {{-- Logo with Glow Effect --}}
                <div class="mb-8 flex justify-center">
                    <div
                        class="p-6 bg-white/10 backdrop-blur-md rounded-full shadow-2xl ring-4 ring-white/20 transform hover:scale-105 transition-transform duration-500 cursor-pointer">
                        {{-- ใช้ asset เพื่อดึงรูปโลโก้ --}}
                        <img src="{{ asset('images/logo/logo1.png') }}" alt="Salepage Demo Logo"
                            class="h-24 w-auto drop-shadow-lg object-contain"
                            onerror="this.src='https://via.placeholder.com/150x150?text=LOGO';" />
                    </div>
                </div>

                <h1 class="text-4xl md:text-6xl font-extrabold mb-4 tracking-wide text-shadow-lg">
                    เกี่ยวกับเรา
                </h1>
                <p class="text-red-100 text-lg md:text-xl font-light max-w-2xl mx-auto">
                    ความตั้งใจของเรา...เพื่อส่งมอบความสุขให้คุณ
                </p>
            </div>

            {{-- Decorative Curve Shape at bottom --}}
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
        <div class="container mx-auto px-4 -mt-20 relative z-20 pb-20">

            {{-- 1. Intro Card --}}
            <div class="bg-white rounded-3xl shadow-xl p-8 md:p-12 mb-12 border-b-4 border-red-500" data-aos="fade-up">
                <div class="flex flex-col md:flex-row items-center gap-12">
                    <div class="w-full md:w-1/2 order-2 md:order-1">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="w-1.5 h-8 bg-red-600 rounded-full"></span>
                            <h2 class="text-3xl font-bold text-gray-800">ยินดีต้อนรับ</h2>
                        </div>
                        <p class="text-gray-600 leading-loose text-lg mb-6">
                            ยินดีต้อนรับสู่ <span class="text-red-600 font-bold">Salepage Demo!</span>
                            เรามุ่งมั่นที่จะนำเสนอสินค้าคุณภาพสูงพร้อมประสบการณ์การช้อปปิ้งที่สะดวกสบายและปลอดภัยที่สุดแก่คุณ
                        </p>
                        <p class="text-gray-600 leading-loose">
                            ทีมงานของเรามีความตั้งใจที่จะคัดสรรสินค้าที่ดีที่สุดและพัฒนาระบบของเราอย่างต่อเนื่องเพื่อให้คุณพึงพอใจสูงสุด
                            หากคุณมีคำถามหรือข้อเสนอแนะใดๆ โปรดอย่าลังเลที่จะติดต่อเรา
                        </p>
                        <div class="mt-8">
                            <a href="#"
                                class="btn bg-red-600 hover:bg-red-700 text-white rounded-full px-8 border-none shadow-md shadow-red-200">อ่านเพิ่มเติม</a>
                        </div>
                    </div>
                    {{-- Image Side --}}
                    <div class="w-full md:w-1/2 order-1 md:order-2 relative group">
                        <div
                            class="absolute inset-0 bg-red-600 rounded-2xl transform rotate-3 group-hover:rotate-6 transition-transform duration-300 opacity-10">
                        </div>
                        <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?q=80&w=1632&auto=format&fit=crop"
                            alt="Our Team"
                            class="relative rounded-2xl shadow-lg w-full h-[300px] object-cover transform group-hover:-translate-y-2 transition-transform duration-300 border-4 border-white">
                    </div>
                </div>
            </div>

            {{-- 2. Grid: Vision & Contact --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                {{-- Vision Card --}}
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-red-50 hover:shadow-2xl transition-all duration-300 group"
                    data-aos="fade-right" data-aos-delay="100">
                    <div class="flex flex-col items-center text-center h-full justify-center">
                        <div
                            class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mb-6 group-hover:bg-red-600 transition-colors duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-red-600 group-hover:text-white transition-colors duration-300"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 group-hover:text-red-600 transition-colors">
                            วิสัยทัศน์ของเรา</h2>
                        <p class="text-gray-600 leading-relaxed italic">
                            "เป็นแพลตฟอร์มอีคอมเมิร์ซที่น่าเชื่อถือและเป็นที่หนึ่งในใจลูกค้า มุ่งเน้นการบริการที่จริงใจ
                            รวดเร็ว และสินค้าคุณภาพเยี่ยม"
                        </p>
                    </div>
                </div>

                {{-- Contact Card --}}
                <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-2xl shadow-lg p-8 text-white hover:shadow-2xl transition-transform hover:-translate-y-1 duration-300 relative overflow-hidden"
                    data-aos="fade-left" data-aos-delay="200">
                    {{-- Decorative Background Circle --}}
                    <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white opacity-10 rounded-full blur-3xl">
                    </div>

                    <h2 class="text-2xl font-bold mb-8 flex items-center gap-3 border-b border-red-400 pb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        ติดต่อเรา
                    </h2>

                    <div class="space-y-6">
                        {{-- Email --}}
                        <div
                            class="flex items-center gap-4 bg-white/10 p-4 rounded-xl backdrop-blur-sm border border-white/10 hover:bg-white/20 transition-colors">
                            <div class="bg-white rounded-full p-2.5 text-red-700 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-red-200 uppercase tracking-wider font-semibold">อีเมล</p>
                                <p class="font-bold text-lg tracking-wide">support@saledemo.com</p>
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div
                            class="flex items-center gap-4 bg-white/10 p-4 rounded-xl backdrop-blur-sm border border-white/10 hover:bg-white/20 transition-colors">
                            <div class="bg-white rounded-full p-2.5 text-red-700 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path
                                        d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-red-200 uppercase tracking-wider font-semibold">โทรศัพท์</p>
                                <p class="font-bold text-lg tracking-wide">012-345-6789</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script: Init AOS --}}
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
