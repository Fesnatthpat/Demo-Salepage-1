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

    {{-- CSS ของ Swiper --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <title>@yield('title', $settings['site_name'] ?? 'Salepage Demo')</title>

    <style>
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

        .site-bg-main {
            min-height: 100vh;
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            @php
                $bgPath = $settings['site_cover_image'] ?? null;
                $isBgFileExists = $bgPath && file_exists(storage_path('app/public/' . $bgPath));
                $bgUrl = $isBgFileExists ? asset('storage/' . $bgPath) : asset('images/BG/fruit2.png');
            @endphp
            background-image: url('{{ $bgUrl }}');
        }

        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="font-['Noto_Sans_Thai'] bg-gray-100" x-data="{ mobileMenuOpen: false }">

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
        
        $logoPath = $settings['site_logo'] ?? null;
        if ($logoPath) {
            $siteLogo = str_starts_with($logoPath, 'http') ? $logoPath : asset('storage/' . $logoPath);
        } else {
            $siteLogo = asset('images/logo/logo1.png');
        }

        $socialLinks = [
            'facebook' => $settings['social_facebook'] ?? '#',
            'twitter' => $settings['social_twitter'] ?? '#',
            'instagram' => $settings['social_instagram'] ?? '#',
            'line' => $settings['social_line'] ?? '#',
        ];

        $menuItems = [
            ['name' => 'หน้าหลัก', 'url' => '/', 'auth_required' => false, 'icon' => 'fas fa-home'],
            ['name' => 'สินค้าทั้งหมด', 'url' => '/allproducts', 'auth_required' => false, 'icon' => 'fas fa-th-large'],
            ['name' => 'คำถามที่พบบ่อย', 'url' => '/faq', 'auth_required' => false, 'icon' => 'fas fa-question-circle'],
            ['name' => 'ประวัติการสั่งซื้อ', 'url' => '/orderhistory', 'auth_required' => true, 'icon' => 'fas fa-history'],
            ['name' => 'เกี่ยวกับเรา', 'url' => '/about', 'auth_required' => false, 'icon' => 'fas fa-info-circle'],
            ['name' => 'ติดต่อเรา', 'url' => '/contact', 'auth_required' => false, 'icon' => 'fas fa-envelope'],
            ['name' => 'เช็คพัสดุ', 'url' => route('order.tracking.form'), 'auth_required' => false, 'icon' => 'fas fa-truck'],
        ];
    @endphp

    {{-- ★★★ NAVBAR ★★★ --}}
    @unless (isset($hideNavbar) && $hideNavbar)
        <div class="sticky top-0 z-50 shadow-sm bg-red-600 border-b border-red-500">
            <div class="container mx-auto px-4 max-w-7xl">

                {{-- ================= MOBILE & TABLET NAV ================= --}}
                <div class="navbar lg:hidden px-2 sm:px-4 min-h-[70px] gap-3">
                    <div class="navbar-start w-auto flex-none">
                        <button @click="mobileMenuOpen = true" class="btn btn-ghost btn-circle text-white hover:bg-white/20 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-7 sm:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>

                    <div class="navbar-center flex-1">
                        <form action="/allproducts" method="GET" class="relative w-full shadow-sm rounded-full">
                            <input type="text" name="search" placeholder="ค้นหา..."
                                class="w-full rounded-full pl-4 pr-10 py-2 sm:py-2.5 text-sm text-gray-700 bg-white border border-transparent focus:outline-none focus:ring-2 focus:ring-red-300 transition-all" />
                            <button type="submit" class="absolute right-1 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full text-red-600">
                                <i class="fas fa-search text-sm"></i>
                            </button>
                        </form>
                    </div>
                    
                    <div class="navbar-end w-auto flex-none">
                        <a href="/" class="hover:scale-105 transition-transform duration-300">
                            <img src="{{ $siteLogo }}" alt="Logo" class="h-12 sm:h-14 w-auto object-contain">
                        </a>
                    </div>
                </div>

                {{-- ================= SIDE SLIDE MENU (Mobile) ================= --}}
                <div x-show="mobileMenuOpen" class="fixed inset-0 z-[100] lg:hidden" x-cloak>
                    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="mobileMenuOpen = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

                    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="absolute inset-y-0 left-0 w-80 max-w-[85%] bg-white shadow-2xl flex flex-col">
                        
                        <div class="p-6 bg-red-600 flex items-center justify-between">
                            <img src="{{ $siteLogo }}" alt="Logo" class="h-10 w-auto brightness-0 invert">
                            <button @click="mobileMenuOpen = false" class="text-white hover:rotate-90 transition-transform duration-300">
                                <i class="fas fa-times text-2xl"></i>
                            </button>
                        </div>

                        <div class="flex-1 overflow-y-auto py-4">
                            <ul class="menu w-full px-4">
                                @foreach ($menuItems as $item)
                                    @if (!$item['auth_required'] || auth()->check())
                                        <li class="mb-1">
                                            <a href="{{ $item['url'] }}" class="flex items-center gap-4 py-3.5 px-4 text-gray-700 font-bold text-base hover:bg-red-50 hover:text-red-600 rounded-xl transition-all">
                                                <span class="w-8 text-center text-lg text-gray-400">
                                                    <i class="{{ $item['icon'] ?? 'fas fa-link' }}"></i>
                                                </span>
                                                {{ $item['name'] }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach

                                <div class="divider px-4 opacity-50">บัญชีผู้ใช้</div>
                                
                                @auth
                                    <li><a href="{{ route('profile.edit') }}" class="flex items-center gap-4 py-3.5 px-4 text-gray-700 font-bold text-base hover:bg-red-50 rounded-xl"><span class="w-8 text-center"><i class="fas fa-user-circle"></i></span>ข้อมูลส่วนตัว</a></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="p-0">@csrf
                                            <button type="submit" class="flex items-center gap-4 py-3.5 px-4 text-red-600 font-bold text-base hover:bg-red-50 rounded-xl w-full text-left"><span class="w-8 text-center"><i class="fas fa-sign-out-alt"></i></span>ออกจากระบบ</button>
                                        </form>
                                    </li>
                                @else
                                    <div class="p-4"><a href="/login" class="btn bg-red-600 hover:bg-red-700 text-white w-full border-none rounded-xl shadow-lg">เข้าสู่ระบบ</a></div>
                                @endauth
                            </ul>
                        </div>

                        <div class="p-6 border-t border-gray-100 bg-gray-50">
                            <p class="text-xs text-gray-400 text-center">© {{ date('Y') }} {{ $settings['site_name'] ?? 'Salepage' }}</p>
                        </div>
                    </div>
                </div>

                {{-- ★★ FLOATING CART ★★ --}}
                <div class="fixed bottom-6 right-6 z-[90] lg:hidden">
                    <a href="/cart" class="flex items-center justify-center w-14 h-14 bg-red-600 text-white rounded-full shadow-2xl hover:scale-110 border-2 border-white transition-all">
                        <div class="indicator">
                            <i class="fas fa-shopping-cart text-xl"></i>
                            <livewire:cart-icon :key="'cart-mobile'" />
                        </div>
                    </a>
                </div>

                {{-- ================= DESKTOP NAV ================= --}}
                <div class="hidden lg:flex items-center justify-between py-3 gap-6">
                    <div class="flex items-center gap-8 flex-shrink-0">
                        <a href="/" class="hover:opacity-80 transition-opacity">
                            <img src="{{ $siteLogo }}" alt="Logo" class="h-14 w-auto object-contain">
                        </a>
                        <nav class="flex items-center gap-6 text-white text-base font-bold">
                            @foreach ($menuItems as $item)
                                @if (!$item['auth_required'] || auth()->check())
                                    <a href="{{ $item['url'] }}" class="hover:text-red-200 transition-colors whitespace-nowrap">{{ $item['name'] }}</a>
                                @endif
                            @endforeach
                        </nav>
                    </div>

                    @unless (isset($hideSearchBar) && $hideSearchBar)
                        <div class="flex-1 max-w-lg mx-4">
                            <form action="/allproducts" method="GET" class="relative w-full">
                                <input type="text" name="search" placeholder="ค้นหาสินค้าที่ต้องการ..."
                                    class="w-full rounded-full pl-5 pr-12 py-2.5 text-sm text-gray-700 bg-white border-none shadow-inner focus:outline-none focus:ring-2 focus:ring-white/50 transition-all" />
                                <button type="submit" class="absolute right-1 top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center rounded-full text-red-600"><i class="fas fa-search"></i></button>
                            </form>
                        </div>
                    @endunless

                    <div class="flex items-center gap-3 flex-shrink-0">
                        <a href="/cart" class="btn btn-ghost btn-circle relative hover:bg-white/20">
                            <div class="indicator"><i class="fas fa-shopping-cart text-2xl text-white"></i><livewire:cart-icon :key="'cart-desktop'" /></div>
                        </a>
                        @guest <a href="/login" class="bg-white text-red-600 hover:bg-gray-50 border-none rounded-full px-6 py-2 shadow-sm font-bold transition-colors">เข้าสู่ระบบ</a> @endguest
                        @auth
                            <div class="dropdown dropdown-end">
                                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar hover:bg-white/20"><div class="w-10 rounded-full border-2 border-white"><img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . auth()->user()->name }}" /></div></div>
                                <ul tabindex="-1" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow-xl bg-white rounded-xl w-52 text-gray-800">
                                    <li class="menu-title px-4 py-2 border-b border-gray-100 text-red-600 font-bold">{{ auth()->user()->name }}</li>
                                    <li><a href="{{ route('profile.edit') }}" class="py-2.5 font-medium hover:bg-red-50 hover:text-red-600">ข้อมูลส่วนตัว</a></li>
                                    <li><a href="/orderhistory" class="py-2.5 font-medium hover:bg-red-50 hover:text-red-600">ประวัติการสั่งซื้อ</a></li>
                                    <li><form action="{{ route('logout') }}" method="POST" class="p-0">@csrf<button type="submit" class="text-red-500 font-bold py-2.5 px-4 w-full text-left hover:bg-red-50">ออกจากระบบ</button></form></li>
                                </ul>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    @endunless

    <div class="site-bg-main">
        @yield('content')
    </div>

    {{-- FOOTER --}}
    <div class="bg-red-600 text-white border-t border-red-700">
        <footer class="container mx-auto px-4 py-12 max-w-7xl">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10">
                <div class="sm:col-span-2 lg:col-span-4 flex flex-col gap-4 items-center sm:items-start text-center sm:text-left">
                    <img src="{{ $siteLogo }}" alt="Logo" class="w-24 h-24 object-contain bg-white rounded-full p-2 shadow-md">
                    <p class="text-red-100 text-sm leading-relaxed">
                        <span class="font-bold text-white text-lg">{{ $settings['site_name'] ?? 'Salepage' }}</span><br>
                        {!! nl2br(e($settings['footer_slogan'] ?? "สูตรเด็ด ต้นตำรับความอร่อย")) !!}
                    </p>
                </div>

                <div class="flex flex-col gap-3 lg:col-span-2">
                    <h6 class="text-lg font-bold mb-3">{{ $settings['faq_badge'] ?? 'ช่วยเหลือ' }}</h6>
                    @for ($i = 1; $i <= 4; $i++)
                        @if (!empty($settings['footer_col2_link' . $i . '_label']))
                            <a href="{{ $settings['footer_col2_link' . $i . '_url'] ?? '#' }}" class="text-sm text-red-100 hover:text-white">
                                {{ $settings['footer_col2_link' . $i . '_label'] }}
                            </a>
                        @endif
                    @endfor
                </div>

                <div class="flex flex-col gap-3 lg:col-span-2">
                    <h6 class="text-lg font-bold mb-3">{{ $settings['footer_about_title'] ?? 'เกี่ยวกับเรา' }}</h6>
                    @for ($i = 1; $i <= 4; $i++)
                        @if (!empty($settings['footer_col3_link' . $i . '_label']))
                            <a href="{{ $settings['footer_col3_link' . $i . '_url'] ?? '#' }}" class="text-sm text-red-100 hover:text-white">
                                {{ $settings['footer_col3_link' . $i . '_label'] }}
                            </a>
                        @endif
                    @endfor
                </div>

                <div class="flex flex-col gap-4 lg:col-span-4 sm:col-span-2">
                    <h6 class="text-lg font-bold mb-3">ติดต่อเรา</h6>
                    <div class="text-sm text-red-100 space-y-2">
                        <p><i class="fas fa-map-marker-alt w-5"></i> {!! nl2br(e($settings['site_address'] ?? '')) !!}</p>
                        <p><i class="fas fa-phone-alt w-5"></i> {{ $settings['site_phone'] ?? '' }}</p>
                        <p><i class="fas fa-envelope w-5"></i> {{ $settings['site_email'] ?? '' }}</p>
                    </div>
                </div>
            </div>
        </footer>

        <div class="bg-red-700 py-4 border-t border-red-800/50">
            <div class="container mx-auto px-4 max-w-7xl flex flex-col md:flex-row justify-between items-center gap-3">
                <p class="text-xs text-red-200">{{ $settings['footer_copyright'] ?? 'Copyright © ' . date('Y') . ' - ' . ($settings['site_name'] ?? 'Salepage') . ' Co., Ltd.' }}</p>
                <div class="flex gap-4">
                    <a href="{{ $socialLinks['facebook'] }}" class="hover:scale-110 transition-transform"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="{{ $socialLinks['twitter'] }}" class="hover:scale-110 transition-transform"><i class="fa-brands fa-twitter"></i></a>
                    <a href="{{ $socialLinks['line'] }}" class="hover:scale-110 transition-transform"><i class="fa-brands fa-line"></i></a>
                    <a href="{{ $socialLinks['instagram'] }}" class="hover:scale-110 transition-transform"><i class="fa-brands fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        window.showNotification = function(type, title, message = '', timer = 2000) {
            Swal.fire({ icon: type, title: title, text: message, timer: timer, timerProgressBar: true, confirmButtonColor: '#dc2626' });
        };
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success')) showNotification('success', 'สำเร็จ!', "{{ session('success') }}"); @endif
            @if (session('error')) showNotification('error', 'ขออภัย!', "{{ session('error') }}"); @endif
        });
    </script>

    @yield('scripts')
    @livewireScripts
</body>
</html>