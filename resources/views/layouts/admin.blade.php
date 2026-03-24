<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <title>@yield('title', 'CRM Admin')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Tom Select (Dark Theme Overrides) --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('styles')

    <style>
        body {
            font-family: 'Sarabun', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Dark Mode Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #111827;
        }

        ::-webkit-scrollbar-thumb {
            background: #374151;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #4b5563;
        }

        /* TomSelect Dark Mode */
        .ts-control {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #f3f4f6 !important;
            border-radius: 0.5rem;
        }

        .ts-dropdown {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #f3f4f6 !important;
        }

        .ts-dropdown .option:hover,
        .ts-dropdown .active {
            background-color: #4b5563 !important;
            color: #fff !important;
        }

        .ts-input {
            color: #fff !important;
        }
    </style>
</head>

<body class="bg-gray-900 antialiased text-gray-100">
    <div x-data="{
        sidebarOpen: window.innerWidth > 1024,
        isMobile: window.innerWidth <= 1024
    }" x-init="window.addEventListener('resize', () => {
        isMobile = window.innerWidth <= 1024;
        if (!isMobile) sidebarOpen = true;
    })" class="flex h-screen overflow-hidden bg-gray-900">

        {{-- Mobile Sidebar Overlay --}}
        <div x-show="isMobile && sidebarOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden">
        </div>

        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 w-72 flex flex-col border-r border-gray-700 transition-all duration-300 bg-gray-800 lg:static lg:translate-x-0"
            :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">

            <div class="h-16 flex items-center justify-between px-6 border-b border-gray-700 bg-gray-800">
                <h1 class="text-xl font-bold text-emerald-400 tracking-wider flex items-center">
                    TIDJAI ADMIN
                </h1>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-emerald-400 font-semibold border-l-4 border-emerald-500' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-tachometer-alt mr-3 w-5 text-center"></i>
                    แดชบอร์ด
                </a>
                <a href="{{ route('admin.orders.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-gray-700 text-emerald-400 font-semibold border-l-4 border-emerald-500' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-shopping-cart mr-3 w-5 text-center"></i>
                    ออเดอร์
                </a>
                <a href="{{ route('admin.products.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-gray-700 text-emerald-400 font-semibold border-l-4 border-emerald-500' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-boxes mr-3 w-5 text-center"></i>
                    จัดการสินค้า
                </a>
                <a href="{{ route('admin.customers.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.customers.*') ? 'bg-gray-700 text-emerald-400 font-semibold border-l-4 border-emerald-500' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-users mr-3 w-5 text-center"></i>
                    ลูกค้า
                </a>
                <a href="{{ route('admin.promotions.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.promotions.index') ? 'bg-gray-700 text-emerald-400 font-semibold border-l-4 border-emerald-500' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-tags mr-3 w-5 text-center"></i>
                    โปรโมชั่น
                </a>
                <a href="{{ route('admin.birthday-promotion.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.birthday-promotion.*') ? 'bg-gray-700 text-emerald-400 font-semibold border-l-4 border-emerald-500' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-birthday-cake mr-3 w-5 text-center text-pink-400"></i>
                    โปรโมชั่นวันเกิด
                </a>
                <a href="{{ route('admin.promotions.logs') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.promotions.logs') ? 'bg-gray-700 text-emerald-400 font-semibold border-l-4 border-emerald-500' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-history mr-3 w-5 text-center"></i>
                    ประวัติการใช้โปรโมชั่น
                </a>

                {{-- ส่วนเมนู Super Admin ที่นำกลับมา --}}
                @if (auth()->guard('admin')->check() && auth()->guard('admin')->user()->role === 'superadmin')
                    <div class="pt-4 pb-2">
                        <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">System</p>
                    </div>

                    {{-- Dropdown for Content Management (Super Admin) --}}
                    <div x-data="{ open: {{ request()->routeIs('admin.faqs.*') || request()->routeIs('admin.favorites.*') || request()->routeIs('admin.contacts.*') || request()->routeIs('admin.homepage-content.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors text-gray-400 hover:bg-gray-700 hover:text-white">
                            <span class="flex items-center">
                                <i class="fas fa-file-alt mr-3 w-5 text-center"></i>
                                จัดการเนื้อหา
                            </span>
                            <i class="fas fa-chevron-down transform transition-transform duration-200"
                                :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-transition class="pl-8 pr-4 space-y-1">
                            <a href="{{ route('admin.homepage-content.index') }}"
                                class="block w-full px-4 py-2 rounded-lg transition-colors text-sm {{ request()->routeIs('admin.homepage-content.*') ? 'text-emerald-400 font-bold' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white' }}">
                                - จัดการเนื้อหาหน้าแรก
                            </a>
                            <a href="{{ route('admin.faqs.index') }}"
                                class="block w-full px-4 py-2 rounded-lg transition-colors text-sm {{ request()->routeIs('admin.faqs.*') ? 'text-emerald-400 font-bold' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white' }}">
                                - จัดการคำถามที่พบบ่อย
                            </a>
                            <a href="{{ route('admin.favorites.index') }}"
                                class="block w-full px-4 py-2 rounded-lg transition-colors text-sm {{ request()->routeIs('admin.favorites.*') ? 'text-emerald-400 font-bold' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white' }}">
                                - จัดการเกี่ยวกับติดใจ
                            </a>
                            <a href="{{ route('admin.contacts.index') }}"
                                class="block w-full px-4 py-2 rounded-lg transition-colors text-sm {{ request()->routeIs('admin.contacts.*') ? 'text-emerald-400 font-bold' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white' }}">
                                - จัดการติดต่อเรา
                            </a>
                        </div>
                    </div>

                    {{-- Dropdown for System Management --}}
                    <div x-data="{ open: {{ request()->routeIs('admin.admins.*') || request()->routeIs('admin.activity-log.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors text-gray-400 hover:bg-gray-700 hover:text-white">
                            <span class="flex items-center">
                                <i class="fas fa-server mr-3 w-5 text-center"></i>
                                จัดการระบบ
                            </span>
                            <i class="fas fa-chevron-down transform transition-transform duration-200"
                                :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-transition class="pl-8 pr-4 space-y-1">
                            <a href="{{ route('admin.admins.index') }}"
                                class="block w-full px-4 py-2 rounded-lg transition-colors text-sm {{ request()->routeIs('admin.admins.*') ? 'text-emerald-400 font-bold' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white' }}">
                                - จัดการแอดมิน
                            </a>
                            <a href="{{ route('admin.activity-log.index') }}"
                                class="block w-full px-4 py-2 rounded-lg transition-colors text-sm {{ request()->routeIs('admin.activity-log.*') ? 'text-emerald-400 font-bold' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white' }}">
                                - ประวัติกิจกรรม
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('admin.popups.index') }}"
                        class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.popups.*') ? 'bg-gray-700 text-emerald-400 font-semibold border-l-4 border-emerald-500' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-window-restore mr-3 w-5 text-center"></i>
                        จัดการ Popup หน้าแรก
                    </a>
                    <a href="{{ route('admin.settings.index') }}"
                        class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-gray-700 text-emerald-400 font-semibold border-l-4 border-emerald-500' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-cogs mr-3 w-5 text-center"></i>
                        ตั้งค่าเว็บไซต์
                    </a>
                @endif
            </nav>

            <div class="px-4 py-4 border-t border-gray-700 bg-gray-800">
                <a href="{{ route('home') }}" target="_blank"
                    class="flex items-center px-4 py-2 text-sm text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition-all">
                    <i class="fas fa-globe mr-3 w-5 text-center"></i>
                    ไปหน้าเว็บไซต์
                </a>
                <form method="POST" action="{{ route('admin.logout') }}" id="logout-form">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center px-4 py-2 text-sm text-red-400 hover:bg-red-900/30 rounded-md mt-2 transition-colors">
                        <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>
                        ออกจากระบบ
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content Wrapper --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-gray-900">
            {{-- Header --}}
            <header
                class="flex justify-between items-center h-16 px-4 sm:px-6 bg-gray-800 border-b border-gray-700 shadow-sm z-20">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="text-gray-400 focus:outline-none hover:text-white transition-colors p-2 rounded-lg hover:bg-gray-700">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    <h1
                        class="text-lg sm:text-xl font-semibold ml-2 sm:ml-4 text-gray-100 truncate max-w-[150px] sm:max-w-none">
                        @yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="flex items-center space-x-2">
                    @if (auth()->guard('admin')->check())
                        <div x-data="{ dropdownOpen: false }" class="relative">
                            <button @click="dropdownOpen = !dropdownOpen"
                                class="flex items-center p-1 sm:p-2 rounded-xl focus:outline-none hover:bg-gray-700 transition-all">
                                <div class="text-right mr-3 hidden md:block">
                                    <div class="text-sm font-semibold text-gray-200">
                                        {{ Auth::user()->name ?? 'Admin User' }}</div>
                                    <div class="text-[10px] text-gray-500 uppercase tracking-tighter">Administrator
                                    </div>
                                </div>
                                <img class="h-8 w-8 sm:h-10 sm:w-10 rounded-lg object-cover border border-gray-600 shadow-inner"
                                    src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . (Auth::user()->name ?? 'Admin') . '&background=10b981&color=fff' }}"
                                    alt="Avatar">
                            </button>

                            <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-md overflow-hidden shadow-xl z-50 border border-gray-700"
                                style="display: none;">

                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">
                                    <i class="fas fa-user-circle mr-2"></i> โปรไฟล์
                                </a>
                                <div class="border-t border-gray-700"></div>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="block px-4 py-2 text-sm text-red-400 hover:bg-red-900/30">
                                    <i class="fas fa-sign-out-alt mr-2"></i> ออกจากระบบ
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-900 p-4 sm:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- CodeMirror Editor Assets --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/theme/dracula.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/addon/hint/show-hint.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/htmlmixed/htmlmixed.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/addon/hint/show-hint.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/addon/hint/xml-hint.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/addon/hint/html-hint.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/addon/hint/html-hint.js"></script>

    <script>
        /**
         * ฟังก์ชันกลางสำหรับแสดง Notification (SweetAlert2)
         * ปรับแต่งให้แสดงตรงกลางและสวยงามเหมือนกันทั้งเว็บ
         */
        window.showNotification = function(type, title, message = '', timer = 2000) {
            const config = {
                icon: type,
                title: title,
                text: message,
                showConfirmButton: (type === 'error' || type === 'warning'),
                confirmButtonColor: '#10b981', // ใช้สี emerald ให้เข้ากับ Admin
                timer: (type === 'success' || type === 'info') ? timer : null,
                timerProgressBar: (type === 'success' || type === 'info'),
                position: 'center',
                padding: '1.5rem',
                borderRadius: '1.25rem',
                customClass: {
                    popup: 'rounded-3xl shadow-2xl border border-gray-700 bg-gray-800 text-white',
                    title: 'text-xl font-black text-gray-100',
                    htmlContainer: 'text-sm font-medium text-gray-400',
                    confirmButton: 'px-8 py-2.5 rounded-xl font-bold transition-all hover:scale-105 active:scale-95'
                }
            };

            return Swal.fire(config);
        };
    </script>

    @stack('scripts')
</body>

</html>
