<!doctype html>
<html lang="th">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $settings['site_description'] ?? 'Salepage Demo - เว็บไซต์ขายสินค้าออนไลน์' }}">

    @vite('resources/css/app.css')

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- ★★★ 1. CSS ของ Swiper ★★★ --}}
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
            /* ขนาดในมือถือ */
            height: 40px;
            border-radius: 50%;
            backdrop-filter: blur(4px);
            transition: all 0.3s ease;
        }

        /* ปรับขนาดปุ่มใหญ่ขึ้นในจอ PC */
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
    </style>
</head>

<body class="font-['Noto_Sans_Thai'] bg-[#f9fafb]">

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
        $siteLogo = isset($settings['site_logo']) ? asset('storage/' . $settings['site_logo']) : '/images/logo_hm.png';
    @endphp

    <div class="sticky top-0 z-50 bg-white shadow-sm border-b border-gray-100">
        <div class="container mx-auto px-4 md:px-6">
            <div class="navbar min-h-[4rem] px-0">
                <div class="navbar-start">
                    <div class="dropdown md:hidden">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle -ml-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                        </div>
                        <ul tabindex="-1"
                            class="menu menu-sm dropdown-content bg-base-100 rounded-box z-50 mt-3 w-64 p-2 shadow-lg">
                            @php $menuItems = [['name' => 'หน้าหลัก', 'url' => '/'], ['name' => 'สินค้าทั้งหมด', 'url' => '/allproducts']]; @endphp
                            @foreach ($menuItems as $item)
                                <li><a href="{{ $item['url'] }}" class="py-3 font-bold">{{ $item['name'] }}</a></li>
                            @endforeach
                            @auth
                                <li><a href="/orderhistory" class="py-3 font-bold">ประวัติการสั่งซื้อ</a></li>
                                <li><a href="{{ route('profile.edit') }}" class="py-3 font-bold">ข้อมูลส่วนตัว</a></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="w-full">@csrf<button
                                            type="submit"
                                            class="text-red-500 font-bold py-2 w-full text-left">ออกจากระบบ</button></form>
                                </li>
                            @else
                                <div class="p-2 mt-2"><a href="/login"
                                        class="btn bg-[#06C755] text-white w-full">เข้าสู่ระบบ</a></div>
                            @endauth
                        </ul>
                    </div>
                    <a href="/" class="hidden md:flex btn btn-ghost text-xl p-0 hover:bg-transparent"><img
                            src="{{ $siteLogo }}" alt="Logo" class="h-10 md:h-12 w-auto object-contain"></a>
                </div>
                <div class="navbar-center">
                    <a href="/" class="md:hidden btn btn-ghost text-xl p-0 hover:bg-transparent"><img
                            src="{{ $siteLogo }}" alt="Logo" class="h-10 w-auto object-contain"></a>
                    <ul class="menu menu-horizontal px-1 gap-6 text-base font-medium text-gray-600 hidden md:flex">
                        @foreach ($menuItems as $item)
                            <li><a href="{{ $item['url'] }}"
                                    class="hover:text-emerald-600 hover:bg-transparent">{{ $item['name'] }}</a></li>
                        @endforeach
                        @auth <li><a href="/orderhistory"
                                class="hover:text-emerald-600 hover:bg-transparent">ประวัติการสั่งซื้อ</a></li> @endauth
                    </ul>
                </div>
                <div class="navbar-end flex items-center gap-2 md:gap-4">
                    <a href="/cart" class="btn btn-ghost btn-circle relative hover:bg-gray-100">
                        <div class="indicator">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span id="cart-badge"
                                class="badge badge-sm indicator-item bg-red-500 text-white border-none {{ $cartCount > 0 ? '' : 'hidden' }}">{{ $cartCount }}</span>
                        </div>
                    </a>
                    @guest <a href="/login"
                            class="hidden md:flex items-center gap-2 btn bg-[#06C755] hover:bg-[#00B900] text-white border-none px-5 rounded-full">เข้าสู่ระบบ</a>
                    @endguest
                    @auth
                        <div class="dropdown dropdown-end hidden md:block">
                            <div tabindex="0" role="button"
                                class="btn btn-ghost btn-circle avatar border border-emerald-200">
                                <div class="w-10 rounded-full"><img
                                        src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . auth()->user()->name }}" />
                                </div>
                            </div>
                            <ul tabindex="-1"
                                class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow-xl bg-white rounded-xl w-64 border border-gray-100">
                                <li class="menu-title px-4 py-3 bg-gray-50 border-b mb-2">{{ auth()->user()->name }}</li>
                                <li><a href="{{ route('profile.edit') }}">ข้อมูลส่วนตัว</a></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit"
                                            class="text-red-500">ออกจากระบบ</button></form>
                                </li>
                            </ul>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="min-h-screen">
        @yield('content')
    </div>

    <div class="bg-base-200 text-base-content mt-10">
        <footer class="footer sm:footer-horizontal p-10 container mx-auto">
            <nav>
                <h6 class="footer-title text-emerald-600">ศูนย์ช่วยเหลือ</h6><a class="link link-hover">ติดตามสถานะ</a>
            </nav>
            <nav>
                <h6 class="footer-title text-emerald-600">เกี่ยวกับเรา</h6><a
                    class="link link-hover">นโยบายความเป็นส่วนตัว</a>
            </nav>
            <form>
                <h6 class="footer-title text-emerald-600">รับข่าวสาร</h6>
                <div class="join"><input type="text" placeholder="email"
                        class="input input-bordered join-item" /><button
                        class="btn bg-emerald-600 text-white join-item">สมัคร</button></div>
            </form>
        </footer>
    </div>

    {{-- ★★★ 2. JS ของ Swiper ★★★ --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        window.updateCartBadge = function(count) {
            const badge = document.getElementById('cart-badge');
            if (badge) {
                badge.innerText = count;
                badge.classList.toggle('hidden', count <= 0);
                badge.classList.add('scale-125');
                setTimeout(() => badge.classList.remove('scale-125'), 200);
            }
        };
    </script>

    {{-- ★★★ [แก้ไขสำคัญ] ใช้ @yield('scripts') เพื่อให้ตรงกับ @section('scripts') ในหน้า index ★★★ --}}
    @stack('scripts')
</body>

</html>
