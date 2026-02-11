<!doctype html>
<html lang="th">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $settings['site_description'] ?? 'Salepage Demo - เว็บไซต์ขายสินค้าออนไลน์' }}">

    @vite('resources/css/app.css')
    @livewireStyles

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- ★★★ CSS ของ Swiper (จำเป็นสำหรับสไลด์) ★★★ --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <title>@yield('title', 'Salepage Demo')</title>

    <style>
        /* CSS ปรับแต่งปุ่มลูกศร Swiper */
        .swiper-button-next,
        .swiper-button-prev {
            color: #ffffff !important;
            background: rgba(0, 0, 0, 0.3);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            backdrop-filter: blur(4px);
            transition: all 0.3s ease;
        }

        /* ปรับปุ่มให้ใหญ่ขึ้นในจอคอม */
        @media (min-width: 768px) {

            .swiper-button-next,
            .swiper-button-prev {
                width: 50px;
                height: 50px;
            }
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background: rgba(220, 38, 38, 0.9);
            /* สีแดง */
            transform: scale(1.1);
        }

        .swiper-pagination-bullet-active {
            background: #dc2626 !important;
            /* สีแดง */
            width: 24px;
            border-radius: 5px;
        }
    </style>
</head>

{{-- ★★★ แก้ไขพื้นหลัง BODY เป็นรูป f2.png (ตามที่คุณตั้งค่าไว้) ★★★ --}}

<body class="font-['Noto_Sans_Thai'] bg-cover bg-center bg-fixed bg-no-repeat"
    style="background-image: url('{{ asset('images/f2.png') }}');">

    {{-- Logic คำนวณจำนวนสินค้า --}}
    @php
        $cartCount = 0;
        if (auth()->check()) {
            $cartSessionId = auth()->id();
        } else {
            $cartSessionId = '_guest_' . session()->getId();
        }
        if (class_exists('Cart')) {
            $cartCount = \Cart::session($cartSessionId)->getTotalQuantity();
        }
        $siteLogo = isset($settings['site_logo']) ? asset('storage/' . $settings['site_logo']) : '/images/logo1.png';

        $menuItems = [['name' => 'หน้าหลัก', 'url' => '/'], ['name' => 'สินค้าทั้งหมด', 'url' => '/allproducts']];
    @endphp

    {{-- ★★★ NAVBAR ★★★ --}}
    @unless (isset($hideNavbar) && $hideNavbar)
        <div class="sticky top-0 z-50 shadow-sm bg-cover bg-center bg-red-600 bg-no-repeat"
            style="background-image: url('{{ asset('') }}');">

            <div class="container mx-auto px-4">

                {{-- ================= MOBILE NAV (หน้าจอมือถือ) ================= --}}
                <div class="navbar md:hidden px-0">
                    <div class="navbar-start">
                        <div class="dropdown">
                            <div tabindex="0" role="button" class="btn btn-ghost btn-circle text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h7" />
                                </svg>
                            </div>
                            <ul tabindex="-1"
                                class="menu menu-sm dropdown-content bg-base-100 rounded-box z-50 mt-3 w-64 p-2 shadow-lg text-gray-800">
                                @foreach ($menuItems as $item)
                                    <li><a href="{{ $item['url'] }}"
                                            class="py-3 font-bold hover:text-red-600">{{ $item['name'] }}</a></li>
                                @endforeach
                                @auth
                                    <li><a href="/orderhistory" class="py-3 font-bold hover:text-red-600">ประวัติการสั่งซื้อ</a>
                                    </li>
                                    <li><a href="/about" class="py-3 font-bold hover:text-red-600">เกี่ยวกับเรา</a></li>
                                    <li><a href="/contact" class="py-3 font-bold hover:text-red-600">ติดต่อเรา</a></li>
                                    <li><a href="/contact" class="py-3 font-bold hover:text-red-600">คำถามที่พบบ่อย</a></li>
                                    <li><a href="{{ route('profile.edit') }}"
                                            class="py-3 font-bold hover:text-red-600">ข้อมูลส่วนตัว</a></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit"
                                                class="text-red-500 font-bold py-2 w-full text-left">ออกจากระบบ</button>
                                        </form>
                                    </li>
                                @else
                                    <div class="p-2 mt-2"><a href="/login"
                                            class="btn bg-red-600 hover:bg-red-700 text-white w-full border-none">เข้าสู่ระบบ</a>
                                    </div>
                                @endauth
                            </ul>
                        </div>
                    </div>
                    <div class="navbar-center">
                        <a href="/" class="btn btn-ghost text-xl p-0 hover:bg-transparent">
                            <img src="{{ $siteLogo }}" alt="Logo" class="h-10 w-auto object-contain">
                        </a>
                    </div>
                    <div class="navbar-end flex items-center gap-2">
                        <a href="/cart" class="btn btn-ghost btn-circle relative">
                            <div class="indicator">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <livewire:cart-icon />
                            </div>
                        </a>
                        @auth
                            <a href="{{ route('profile.edit') }}" class="btn btn-ghost btn-circle avatar">
                                <div class="w-8 rounded-full border border-white">
                                    <img
                                        src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . auth()->user()->name }}" />
                                </div>
                            </a>
                        @endauth
                    </div>
                </div>
                {{-- ช่องค้นหาสำหรับมือถือ --}}
                @unless (isset($hideSearchBar) && $hideSearchBar)
                    <div class="w-full flex justify-center pb-3 md:hidden">
                        <form action="/allproducts" method="GET" class="relative w-full">
                            <input type="text" name="search" placeholder="ค้นหาสินค้า..."
                                class="input input-sm w-full rounded-full pl-4 pr-10 text-gray-700 bg-white border-none shadow focus:outline-none" />
                            <button type="submit"
                                class="absolute right-1 top-1/2 -translate-y-1/2 btn btn-circle btn-ghost btn-xs text-red-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @endunless


                {{-- ================= DESKTOP NAV (หน้าจอคอม) ================= --}}
                {{-- ปรับ Layout ใหม่ให้เป็น Flex Row เรียงต่อกัน --}}
                <div class="hidden md:flex items-center justify-between py-3 gap-6">

                    {{-- 1. Logo & Menu Group --}}
                    <div class="flex items-center gap-6 flex-shrink-0">
                        {{-- Logo --}}
                        <a href="/" class="hover:opacity-80 transition-opacity">
                            {{-- ปรับความสูงโลโก้เป็น h-14 เพื่อไม่ให้ใหญ่เกินไป --}}
                            <img src="{{ $siteLogo }}" alt="Logo" class="h-14 w-auto object-contain">
                        </a>

                        {{-- Menu --}}
                        <nav class="flex items-center gap-4 text-white text-base font-bold">
                            @foreach ($menuItems as $item)
                                <a href="{{ $item['url'] }}"
                                    class="hover:text-red-200 transition-colors">{{ $item['name'] }}</a>
                            @endforeach
                            @auth
                                <a href="/orderhistory" class="hover:text-red-200 transition-colors">ประวัติการสั่งซื้อ</a>
                                <a href="/about" class="hover:text-red-200 transition-colors">เกี่ยวกับเรา</a>
                                <a href="/contact" class="hover:text-red-200 transition-colors">ติดต่อเรา</a>
                                <a href="/faq" class="hover:text-red-200 transition-colors">คำถามที่พบบ่อย</a>
                                <a href="https://kawinbrothers.com/tracking/index.php" target="_blank"
                                    rel="noopener noreferrer" class="hover:text-red-200 transition-colors">เช็คพัสดุ</a>
                            @endauth
                        </nav>
                    </div>

                    {{-- 2. Search Bar Group (Flexible Width) --}}
                    @unless (isset($hideSearchBar) && $hideSearchBar)
                        <div class="flex-1 max-w-md mx-4">
                            <form action="/allproducts" method="GET" class="relative w-full">
                                <input type="text" name="search" placeholder="ค้นหาสินค้าที่ต้องการ..."
                                    class="input input-sm md:input-md w-full rounded-full pl-5 pr-12 text-gray-700 bg-white/90 focus:bg-white border-none shadow-md focus:outline-none focus:ring-2 focus:ring-white/50 transition-all" />
                                <button type="submit"
                                    class="absolute right-1 top-1/2 -translate-y-1/2 btn btn-circle btn-ghost btn-sm text-red-600 hover:bg-red-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endunless

                    {{-- 3. Icons Group (Cart & Profile) --}}
                    <div class="flex items-center gap-3 flex-shrink-0">
                        {{-- Cart --}}
                        <a href="/cart" class="btn btn-ghost btn-circle relative hover:bg-white/20">
                            <div class="indicator">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <livewire:cart-icon />
                            </div>
                        </a>

                        {{-- Login / Profile --}}
                        @guest
                            <a href="/login"
                                class="btn bg-white text-red-600 hover:bg-gray-100 border-none rounded-full px-5 shadow-sm font-bold">
                                เข้าสู่ระบบ
                            </a>
                        @endguest

                        @auth
                            <div class="dropdown dropdown-end">
                                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar hover:bg-white/20">
                                    <div class="w-10 rounded-full border-2 border-white/50">
                                        <img
                                            src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . auth()->user()->name }}" />
                                    </div>
                                </div>
                                <ul tabindex="-1"
                                    class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow-xl bg-white rounded-xl w-52 border border-gray-100 text-gray-800">
                                    <li class="menu-title px-4 py-2 border-b mb-1 text-red-600 font-bold">
                                        {{ auth()->user()->name }}
                                    </li>
                                    <li><a href="{{ route('profile.edit') }}">ข้อมูลส่วนตัว</a></li>
                                    <li><a href="/orderhistory">ประวัติการสั่งซื้อ</a></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">@csrf
                                            <button type="submit" class="text-red-500 hover:bg-red-50">ออกจากระบบ</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endauth
                    </div>

                </div>

            </div>
        </div>
    @endunless


    <div class="min-h-screen">
        @yield('content')
    </div>

    {{-- ★★★ FOOTER ★★★ --}}
    <div class="bg-red-600 text-white mt-10 border-t border-red-700">
        <footer class="container mx-auto p-10 flex flex-wrap lg:flex-nowrap justify-between gap-10">
            {{-- Column 1 --}}
            <nav class="flex flex-col gap-2 w-full sm:w-1/2 lg:w-auto">
                <h6 class="text-lg font-bold text-white mb-2 opacity-100">ศูนย์ช่วยเหลือ</h6>
                <a href="/track"
                    class="link link-hover text-red-50 hover:text-white transition-colors">ติดตามสถานะคำสั่งซื้อ</a>
                <a href="#"
                    class="link link-hover text-red-50 hover:text-white transition-colors">การรับประกันสินค้า</a>
                <a href="#"
                    class="link link-hover text-red-50 hover:text-white transition-colors">การคืนสินค้าและการคืนเงิน</a>
                <a href="#"
                    class="link link-hover text-red-50 hover:text-white transition-colors">วิธีการสั่งซื้อ</a>
            </nav>
            {{-- Column 2 --}}
            <nav class="flex flex-col gap-2 w-full sm:w-1/2 lg:w-auto">
                <h6 class="text-lg font-bold text-white mb-2 opacity-100">เกี่ยวกับเรา</h6>
                <a href="/about"
                    class="link link-hover text-red-50 hover:text-white transition-colors">เรื่องราวของเรา</a>
                <a href="#"
                    class="link link-hover text-red-50 hover:text-white transition-colors">บทความน่ารู้</a>
                <a href="#"
                    class="link link-hover text-red-50 hover:text-white transition-colors">นโยบายความเป็นส่วนตัว</a>
                <a href="#"
                    class="link link-hover text-red-50 hover:text-white transition-colors">ข้อกำหนดและเงื่อนไข</a>
            </nav>
            {{-- Column 3 --}}
            <nav class="flex flex-col gap-2 w-full sm:w-1/2 lg:w-auto">
                <h6 class="text-lg font-bold text-white mb-2 opacity-100">ติดต่อเรา</h6>
                <div class="flex items-start gap-2 text-red-50">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        class="w-5 h-5 flex-shrink-0 mt-0.5">
                        <path fill-rule="evenodd"
                            d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 103 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 002.273 1.765 11.842 11.842 0 00.976.544l.062.029.006.003.002.001.003.001a.79.79 0 00.01.003zM10 11a2 2 0 100-4 2 2 0 000 4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>บริษัท ติดใจ จำกัด<br>123 ถนนสุขุมวิท แขวงคลองเตย<br>เขตคลองเตย กรุงเทพฯ 10110</span>
                </div>
                <div class="flex items-center gap-2 text-red-50 mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                        <path
                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                    </svg>
                    <span>02-123-4567</span>
                </div>
                <div class="flex items-center gap-2 text-red-50">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                    </svg>
                    <span>contact@tidjai.com</span>
                </div>
            </nav>
            {{-- Column 4 --}}
            <form class="flex flex-col gap-2 w-full sm:w-1/2 lg:w-auto min-w-[250px]">
                <h6 class="text-lg font-bold text-white mb-2 opacity-100">รับข่าวสารโปรโมชั่น</h6>
                <fieldset class="form-control w-full">
                    <label class="label pt-0"><span
                            class="label-text text-red-100">กรอกอีเมลเพื่อรับสิทธิพิเศษก่อนใคร</span></label>
                    <div class="join w-full">
                        <input type="text" placeholder="your-email@example.com"
                            class="input input-bordered join-item text-gray-800 w-full focus:outline-none" />
                        <button
                            class="btn bg-white text-red-600 hover:bg-red-50 border-none join-item font-bold">สมัคร</button>
                    </div>
                </fieldset>
            </form>
        </footer>

        <div class="bg-red-700 py-4 text-center text-red-200 text-sm border-t border-red-800">
            <div class="container mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-2">
                <p>Copyright © {{ date('Y') }} - All right reserved by Tidjai Co., Ltd.</p>
                <div class="flex gap-4">
                    <a href="#" class="hover:text-white transition-colors"><svg fill="currentColor"
                            class="w-5 h-5" viewBox="0 0 24 24">
                            <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
                        </svg></a>
                    <a href="#" class="hover:text-white transition-colors"><svg fill="currentColor"
                            class="w-5 h-5" viewBox="0 0 24 24">
                            <path
                                d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z">
                            </path>
                        </svg></a>
                    <a href="#" class="hover:text-white transition-colors"><svg fill="none"
                            stroke="currentColor" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01"></path>
                        </svg></a>
                </div>
            </div>
        </div>
    </div>

    {{-- ★★★ JS ของ Swiper ★★★ --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    @yield('scripts')
    @livewireScripts
</body>

</html>
