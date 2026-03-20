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

    {{-- ★★★ [เพิ่มใหม่] Font Awesome (จำเป็นสำหรับ Icons) ★★★ --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <title>@yield('title', $settings['site_name'] ?? 'Salepage Demo')</title>

    <style>
        /* CSS ปรับแต่งปุ่มลูกศร Swiper */
        .swiper-button-next,
        .swiper-button-prev {
            color: #ffffff !important;
            background: rgba(0, 0, 0, 0.3);
            width: 32px;
            height: 32px;
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
            transform: scale(1.1);
        }

        .swiper-pagination-bullet-active {
            background: #dc2626 !important;
            width: 24px;
            border-radius: 5px;
        }

        /* Site Background Dynamic */
        .site-bg-main {
            min-height: 100vh;
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            @php
                $bgPath = $settings['site_cover_image'] ?? null;
                // เช็คว่าไฟล์มีอยู่จริงใน storage/app/public หรือไม่
                $isBgFileExists = $bgPath && file_exists(storage_path('app/public/' . $bgPath));
                $bgUrl = $isBgFileExists ? asset('storage/' . $bgPath) : asset('images/BG/fruit2.png');
            @endphp
            background-image: url('{{ $bgUrl }}');
        }
    </style>
</head>

<body class="font-['Noto_Sans_Thai'] bg-gray-100">

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
        
        // จัดการ URL ของ Logo
        $logoPath = $settings['site_logo'] ?? null;
        if ($logoPath) {
            $siteLogo = str_starts_with($logoPath, 'http') ? $logoPath : asset('storage/' . $logoPath);
        } else {
            $siteLogo = asset('images/logo/logo1.png');
        }

        $siteName = $settings['site_name'] ?? 'ติดใจ';
        $sitePhone = $settings['site_phone'] ?? '02-123-4567';
        $siteEmail = $settings['site_email'] ?? 'contact@tidjai.com';
        $siteAddress = $settings['site_address'] ?? "บริษัท ติดใจ จำกัด\n123 ถนนสุขุมวิท แขวงคลองเตย\nเขตคลองเตย กรุงเทพฯ 10110";

        $socialLinks = [
            'facebook' => $settings['social_facebook'] ?? '#',
            'twitter' => $settings['social_twitter'] ?? '#',
            'instagram' => $settings['social_instagram'] ?? '#',
            'line' => $settings['social_line'] ?? '#',
        ];

        $menuItems = [
            ['name' => 'หน้าหลัก', 'url' => '/', 'auth_required' => false],
            ['name' => 'สินค้าทั้งหมด', 'url' => '/allproducts', 'auth_required' => false],
            ['name' => 'คำถามที่พบบ่อย', 'url' => '/faq', 'auth_required' => false],
            ['name' => 'ประวัติการสั่งซื้อ', 'url' => '/orderhistory', 'auth_required' => true],
            ['name' => 'เกี่ยวกับติดใจ', 'url' => '/about', 'auth_required' => false],
            ['name' => 'ติดต่อติดใจ', 'url' => '/contact', 'auth_required' => false],
            ['name' => 'เช็คพัสดุ', 'url' => route('order.tracking.form'), 'auth_required' => false],
        ];
    @endphp

    {{-- ★★★ NAVBAR ★★★ --}}
    @unless (isset($hideNavbar) && $hideNavbar)
        <div class="sticky top-0 z-50 shadow-sm bg-red-600 border-b border-red-500">

            <div class="container mx-auto px-4 max-w-7xl">

                {{-- ================= MOBILE NAV (หน้าจอมือถือ) ================= --}}
                <div class="navbar md:hidden px-0 min-h-[60px]">
                    <div class="navbar-start w-1/4">
                        <div class="dropdown">
                            <div tabindex="0" role="button" class="btn btn-ghost btn-circle text-white hover:bg-white/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                                </svg>
                            </div>
                            <ul tabindex="-1" class="menu menu-sm dropdown-content bg-base-100 rounded-box z-50 mt-3 w-64 p-3 shadow-xl border border-gray-100 text-gray-800">
                                @foreach ($menuItems as $item)
                                    @if (!$item['auth_required'] || auth()->check())
                                        <li><a href="{{ $item['url'] }}" class="py-3 px-4 font-bold text-sm hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">{{ $item['name'] }}</a></li>
                                    @endif
                                @endforeach
                                @auth
                                    <div class="divider my-1"></div>
                                    <li><a href="{{ route('profile.edit') }}" class="py-3 px-4 font-bold text-sm hover:text-red-600 hover:bg-red-50 rounded-lg">ข้อมูลส่วนตัว</a></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="p-0">@csrf
                                            <button type="submit" class="text-red-500 font-bold py-3 px-4 w-full text-left hover:bg-red-50 rounded-lg">ออกจากระบบ</button>
                                        </form>
                                    </li>
                                @else
                                    <div class="p-2 mt-2">
                                        <a href="/login" class="btn bg-red-600 hover:bg-red-700 text-white w-full border-none rounded-xl">เข้าสู่ระบบ</a>
                                    </div>
                                @endauth
                            </ul>
                        </div>
                    </div>
                    <div class="navbar-center flex-1 justify-center">
                        <a href="/" class="btn btn-ghost p-0 hover:bg-transparent h-auto min-h-0">
                            <img src="{{ $siteLogo }}" alt="Logo" class="h-10 sm:h-12 w-auto object-contain">
                        </a>
                    </div>
                    <div class="navbar-end w-1/4 flex justify-end items-center gap-1 sm:gap-2">
                        <a href="/cart" class="btn btn-ghost btn-circle relative hover:bg-white/20">
                            <div class="indicator">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <livewire:cart-icon />
                            </div>
                        </a>
                        @auth
                            <a href="{{ route('profile.edit') }}" class="btn btn-ghost btn-circle avatar hover:bg-white/20 hidden sm:flex">
                                <div class="w-8 rounded-full border border-white">
                                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . auth()->user()->name }}" />
                                </div>
                            </a>
                        @endauth
                    </div>
                </div>
                
                {{-- ช่องค้นหาสำหรับมือถือ --}}
                @unless (isset($hideSearchBar) && $hideSearchBar)
                    <div class="w-full flex justify-center pb-3 px-2 sm:px-4 md:hidden">
                        <form action="/allproducts" method="GET" class="relative w-full">
                            <input type="text" name="search" placeholder="ค้นหาสินค้า..."
                                class="w-full rounded-full pl-4 pr-10 py-2 text-sm text-gray-700 bg-white border-none shadow-inner focus:outline-none focus:ring-2 focus:ring-red-300 transition-all" />
                            <button type="submit"
                                class="absolute right-1 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full text-red-600 hover:bg-red-50 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @endunless

                {{-- ================= DESKTOP NAV (หน้าจอคอม) ================= --}}
                <div class="hidden md:flex items-center justify-between py-3 gap-6">

                    {{-- 1. Logo & Menu Group --}}
                    <div class="flex items-center gap-6 xl:gap-8 flex-shrink-0">
                        {{-- Logo --}}
                        <a href="/" class="hover:opacity-80 transition-opacity">
                            <img src="{{ $siteLogo }}" alt="Logo" class="h-12 lg:h-14 w-auto object-contain">
                        </a>

                        {{-- Menu --}}
                        <nav class="flex items-center gap-4 xl:gap-6 text-white text-sm lg:text-base font-bold">
                            @foreach ($menuItems as $item)
                                @if (!$item['auth_required'] || auth()->check())
                                    <a href="{{ $item['url'] }}" class="hover:text-red-200 transition-colors whitespace-nowrap">{{ $item['name'] }}</a>
                                @endif
                            @endforeach
                        </nav>
                    </div>

                    {{-- 2. Search Bar Group --}}
                    @unless (isset($hideSearchBar) && $hideSearchBar)
                        <div class="flex-1 max-w-md xl:max-w-lg mx-4">
                            <form action="/allproducts" method="GET" class="relative w-full">
                                <input type="text" name="search" placeholder="ค้นหาสินค้าที่ต้องการ..."
                                    class="w-full rounded-full pl-5 pr-12 py-2.5 text-sm text-gray-700 bg-white border-none shadow-inner focus:outline-none focus:ring-2 focus:ring-white/50 transition-all" />
                                <button type="submit"
                                    class="absolute right-1 top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center rounded-full text-red-600 hover:bg-red-50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endunless

                    {{-- 3. Icons Group (Cart & Profile) --}}
                    <div class="flex items-center gap-2 lg:gap-3 flex-shrink-0">
                        {{-- Cart --}}
                        <a href="/cart" class="btn btn-ghost btn-circle relative hover:bg-white/20">
                            <div class="indicator">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 lg:h-7 lg:w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <livewire:cart-icon />
                            </div>
                        </a>

                        {{-- Login / Profile --}}
                        @guest
                            <a href="/login"
                                class="bg-white text-red-600 hover:bg-gray-50 border-none rounded-full px-5 py-2 text-sm lg:text-base shadow-sm font-bold transition-colors">
                                เข้าสู่ระบบ
                            </a>
                        @endguest

                        @auth
                            <div class="dropdown dropdown-end">
                                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar hover:bg-white/20">
                                    <div class="w-9 lg:w-10 rounded-full border-2 border-white/50">
                                        <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . auth()->user()->name }}" />
                                    </div>
                                </div>
                                <ul tabindex="-1" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow-xl bg-white rounded-xl w-52 border border-gray-100 text-gray-800">
                                    <li class="menu-title px-4 py-2 border-b border-gray-100 mb-1 text-red-600 font-bold">
                                        {{ auth()->user()->name }}
                                    </li>
                                    <li><a href="{{ route('profile.edit') }}" class="py-2.5 font-medium hover:bg-red-50 hover:text-red-600 rounded-lg">ข้อมูลส่วนตัว</a></li>
                                    <li><a href="/orderhistory" class="py-2.5 font-medium hover:bg-red-50 hover:text-red-600 rounded-lg">ประวัติการสั่งซื้อ</a></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="p-0 mt-1">@csrf
                                            <button type="submit" class="text-red-500 font-bold py-2.5 px-4 w-full text-left hover:bg-red-50 rounded-lg">ออกจากระบบ</button>
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

    {{-- Main Content Background (เพิ่ม bg-cover bg-center bg-fixed เพื่อให้มือถือสเกลรูปได้ถูกต้อง) --}}
    <div class="site-bg-main">
        @yield('content')
    </div>

    {{-- ★★★ FOOTER ★★★ --}}
    <div class="bg-red-600 text-white border-t border-red-700">
        <footer class="container mx-auto px-4 py-10 lg:py-12 max-w-7xl">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-10">
                
                {{-- Column 1: โลโก้และข้อมูลร้าน --}}
                <div class="sm:col-span-2 lg:col-span-4 flex flex-col gap-4 items-center sm:items-start text-center sm:text-left">
                    <img src="{{ $siteLogo }}" alt="{{ $siteName }} Logo"
                        class="w-24 h-24 sm:w-28 sm:h-28 object-contain bg-white rounded-full p-2 shadow-md hover:scale-105 transition-transform duration-300">
                    <p class="text-red-100 text-sm leading-relaxed mt-2">
                        <span class="font-bold text-white text-lg">{{ $siteName }}</span><br>
                        {!! nl2br(e($settings['footer_slogan'] ?? "ของกินเล่นสูตรเด็ด ต้นตำรับความอร่อย\nคัดสรรวัตถุดิบคุณภาพเพื่อคุณ")) !!}
                    </p>
                </div>

                {{-- Column 2: ศูนย์ช่วยเหลือ --}}
                <div class="flex flex-col gap-3 lg:col-span-2">
                    <h6 class="text-base sm:text-lg font-bold text-white mb-2 sm:mb-3">{{ $settings['faq_badge'] ?? 'ศูนย์ช่วยเหลือ' }}</h6>
                    @for($i=1; $i<=4; $i++)
                        @php
                            $label = $settings['footer_col2_link'.$i.'_label'] ?? ($i==1?'ติดตามสถานะคำสั่งซื้อ':($i==2?'การรับประกันสินค้า':($i==3?'การคืนสินค้าและการคืนเงิน':'วิธีการสั่งซื้อ')));
                            $url = $settings['footer_col2_link'.$i.'_url'] ?? ($i==1?route('order.tracking.form'):'#');
                        @endphp
                        @if($label)
                            <a href="{{ $url }}" class="text-sm text-red-100 hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-angle-right text-[10px]"></i> {{ $label }}</a>
                        @endif
                    @endfor
                </div>

                {{-- Column 3: เกี่ยวกับติดใจ --}}
                <div class="flex flex-col gap-3 lg:col-span-2">
                    <h6 class="text-base sm:text-lg font-bold text-white mb-2 sm:mb-3">{{ $settings['footer_about_title'] ?? 'เกี่ยวกับติดใจ' }}</h6>
                    @for($i=1; $i<=4; $i++)
                        @php
                            $label = $settings['footer_col3_link'.$i.'_label'] ?? ($i==1?'เรื่องราวของเรา':($i==2?'บทความน่ารู้':($i==3?'นโยบายความเป็นส่วนตัว':'ข้อกำหนดและเงื่อนไข')));
                            $url = $settings['footer_col3_link'.$i.'_url'] ?? ($i==1?'/about':'#');
                        @endphp
                        @if($label)
                            <a href="{{ $url }}" class="text-sm text-red-100 hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-angle-right text-[10px]"></i> {{ $label }}</a>
                        @endif
                    @endfor
                </div>

                {{-- Column 4: ติดต่อ --}}
                <div class="flex flex-col gap-4 lg:col-span-4 sm:col-span-2">
                    {{-- Contact Info --}}
                    <div>
                        <h6 class="text-base sm:text-lg font-bold text-white mb-2 sm:mb-3">ติดต่อ{{ $siteName }}</h6>
                        <div class="flex flex-col gap-3 text-sm text-red-100">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-map-marker-alt mt-1 flex-shrink-0 w-4 text-center"></i>
                                <span class="leading-relaxed">{!! nl2br(e($siteAddress)) !!}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-phone-alt flex-shrink-0 w-4 text-center"></i>
                                <span>{{ $sitePhone }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-envelope flex-shrink-0 w-4 text-center"></i>
                                <span>{{ $siteEmail }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </footer>

        {{-- Footer Bottom --}}
        <div class="bg-red-700 py-4 text-center border-t border-red-800/50">
            <div class="container mx-auto px-4 max-w-7xl flex flex-col md:flex-row justify-between items-center gap-3">
                <p class="text-xs sm:text-sm text-red-200">Copyright © {{ date('Y') }} - All right reserved by {{ $siteName }} Co., Ltd.</p>
                
                {{-- ส่วนที่แก้ไข Icon โซเชียลมีเดียให้รองรับ Font Awesome 6 --}}
                <div class="flex gap-4">
                    <a href="{{ $socialLinks['facebook'] }}" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-white hover:text-red-700 transition-all">
                        <i class="fa-brands fa-facebook-f text-lg"></i>
                    </a>
                    <a href="{{ $socialLinks['twitter'] }}" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-white hover:text-red-700 transition-all">
                        <i class="fa-brands fa-x-twitter text-lg"></i>
                    </a>
                    <a href="{{ $socialLinks['instagram'] }}" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-white hover:text-red-700 transition-all">
                        <i class="fa-brands fa-instagram text-lg"></i>
                    </a>
                    <a href="{{ $socialLinks['line'] }}" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-white hover:text-red-700 transition-all">
                        <i class="fa-brands fa-line text-lg"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ★★★ JS ของ Swiper ★★★ --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#dc2626',
                    borderRadius: '1rem'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'ขออภัย!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#dc2626',
                    borderRadius: '1rem'
                });
            @endif
        });
    </script>

    @yield('scripts')
    @livewireScripts

    {{-- ★★★ [เพิ่มใหม่] Homepage Popup Modal ★★★ --}}
    @if(isset($activePopup) && $activePopup)
    <div x-data="{ 
            showPopup: false,
            popupId: '{{ $activePopup->id }}',
            displayType: '{{ $activePopup->display_type }}',
            init() {
                // Logic การตรวจสอบความถี่การแสดงผล
                const lastShow = localStorage.getItem('popup_show_' + this.popupId);
                const now = new Date().getTime();
                
                if (this.displayType === 'always') {
                    this.showPopup = true;
                } else if (this.displayType === 'once_per_day') {
                    // แสดงวันละครั้ง (24 ชั่วโมง)
                    if (!lastShow || (now - lastShow > 24 * 60 * 60 * 1000)) {
                        this.showPopup = true;
                    }
                } else {
                    // default: once_per_session (ใช้ sessionStorage แทนถ้าต้องการปิดแท็บแล้วหาย)
                    // หรือใช้ localStorage แบบล้างเมื่อเวลาผ่านไป (เช่น 2 ชม.)
                    const sessionShow = sessionStorage.getItem('popup_session_' + this.popupId);
                    if (!sessionShow) {
                        this.showPopup = true;
                    }
                }

                if (this.showPopup) {
                    // หน่วงเวลาเล็กน้อยเพื่อให้หน้าเว็บโหลดเสร็จก่อนแสดง
                    setTimeout(() => {
                        this.showPopup = true;
                    }, 1000);
                }
            },
            closePopup() {
                this.showPopup = false;
                localStorage.setItem('popup_show_' + this.popupId, new Date().getTime());
                sessionStorage.setItem('popup_session_' + this.popupId, 'true');
            }
         }" 
         x-show="showPopup" 
         x-cloak
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        
        <div class="relative w-full max-w-lg bg-transparent rounded-2xl overflow-visible animate-pop-in">
            {{-- Close Button --}}
            <button @click="closePopup()" 
                class="absolute -top-4 -right-4 w-10 h-10 bg-white text-gray-800 rounded-full shadow-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all z-10 border-2 border-white">
                <i class="fas fa-times text-lg"></i>
            </button>

            {{-- Popup Content --}}
            <div class="overflow-hidden rounded-2xl shadow-2xl border-4 border-white/20">
                @if($activePopup->link_url)
                    <a href="{{ $activePopup->link_url }}" class="block group">
                        <img src="{{ asset('storage/' . $activePopup->image_path) }}" 
                            class="w-full h-auto object-contain transition-transform duration-500 group-hover:scale-105" 
                            alt="{{ $activePopup->name }}">
                    </a>
                @else
                    <img src="{{ asset('storage/' . $activePopup->image_path) }}" 
                        class="w-full h-auto object-contain" 
                        alt="{{ $activePopup->name }}">
                @endif
            </div>

            {{-- Optional: Footer if needed --}}
            <div class="mt-4 text-center">
                <button @click="closePopup()" class="text-white/60 hover:text-white text-sm underline underline-offset-4">
                    ปิดหน้าต่างนี้
                </button>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        @keyframes pop-in {
            0% { transform: scale(0.9); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-pop-in {
            animation: pop-in 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
    </style>
    @endif
</body>

</html>