<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="emerald">

<head>
    <meta charset="utf-8"> {{-- แก้ไขคำผิดจาก utf-g --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'CRM Admin')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- 1. Font Awesome (CDN มาตรฐาน - แก้ปัญหา Icon ไม่ขึ้น) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- 2. Tom Select (สำหรับ Dropdown สวยๆ) --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    {{-- 3. Alpine.js (สำคัญมาก! ต้องมีเพื่อใข้ x-data, x-show, x-for) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('styles')

    <style>
        body {
            font-family: 'Sarabun', sans-serif;
        }

        .admin-bg {
            background-color: #f0f2f5;
        }

        /* ปรับแต่ง TomSelect ให้เข้ากับ Theme DaisyUI/Tailwind */
        .ts-control {
            border-radius: 0.5rem;
            padding: 0.6rem;
            border-color: #d1d5db;
            background-color: white;
        }

        .ts-control.focus {
            border-color: #10b981;
            /* Emerald-500 */
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        }

        .ts-dropdown {
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 50;
        }
    </style>
</head>

<body class="admin-bg antialiased text-gray-800">
    <div x-data="{ sidebarOpen: true }" class="flex h-screen bg-gray-100">

        <aside class="flex-shrink-0 w-64 flex flex-col border-r transition-all duration-300 bg-white"
            :class="{ '-ml-64': !sidebarOpen }">

            <div class="h-16 flex items-center justify-center border-b bg-emerald-50">
                <h1 class="text-2xl font-bold text-emerald-600 tracking-wider">CRM ADMIN</h1>
            </div>

            <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center px-4 py-3 text-gray-700 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-100 text-emerald-700 font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-tachometer-alt mr-3 w-5 text-center"></i>
                    แดชบอร์ด
                </a>
                <a href="{{ route('admin.orders.index') }}"
                    class="flex items-center px-4 py-3 text-gray-700 rounded-lg transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-emerald-100 text-emerald-700 font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-shopping-cart mr-3 w-5 text-center"></i>
                    ออเดอร์
                </a>
                <a href="{{ route('admin.products.index') }}"
                    class="flex items-center px-4 py-3 text-gray-700 rounded-lg transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-emerald-100 text-emerald-700 font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-boxes mr-3 w-5 text-center"></i>
                    จัดการสินค้า
                </a>
                <a href="{{ route('admin.customers.index') }}"
                    class="flex items-center px-4 py-3 text-gray-700 rounded-lg transition-colors {{ request()->routeIs('admin.customers.*') ? 'bg-emerald-100 text-emerald-700 font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-users mr-3 w-5 text-center"></i>
                    ลูกค้า
                </a>
                <a href="{{ route('admin.promotions.index') }}"
                    class="flex items-center px-4 py-3 text-gray-700 rounded-lg transition-colors {{ request()->routeIs('admin.promotions.*') ? 'bg-emerald-100 text-emerald-700 font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-tags mr-3 w-5 text-center"></i>
                    โปรโมชั่น
                </a>
                 @if (auth()->guard('admin')->check() && auth()->guard('admin')->user()->role === 'superadmin')
                <a href="{{ route('admin.admins.index') }}"
                    class="flex items-center px-4 py-3 text-gray-700 rounded-lg transition-colors {{ request()->routeIs('admin.admins.*') ? 'bg-emerald-100 text-emerald-700 font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-user-shield mr-3 w-5 text-center"></i>
                    จัดการแอดมิน
                </a>
                @endif
            </nav>

            <div class="px-4 py-4 border-t bg-gray-50">
                <a href="{{ route('home') }}"
                    class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-white hover:shadow-sm rounded-md transition-all">
                    <i class="fas fa-globe mr-3 w-5 text-center"></i>
                    ไปหน้าเว็บไซต์
                </a>
                <a href="{{ route('admin.logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="flex items-center px-4 py-2 text-sm text-red-500 hover:bg-red-50 rounded-md mt-2 transition-colors">
                    <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>
                    ออกจากระบบ
                </a>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="flex justify-between items-center p-4 bg-white border-b shadow-sm z-20">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="text-gray-500 focus:outline-none hover:text-emerald-600 transition-colors">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    <h1 class="text-xl font-semibold ml-4 text-gray-800">@yield('page-title', 'Dashboard')</h1>
                </div>

                <div class="flex items-center">
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen"
                            class="flex items-center space-x-2 relative focus:outline-none hover:opacity-80 transition-opacity">
                            <div class="text-right mr-2 hidden sm:block">
                                <div class="text-sm font-semibold text-gray-700">
                                    {{ Auth::user()->name ?? 'Admin User' }}</div>
                                <div class="text-xs text-gray-400">Administrator</div>
                            </div>
                            <img class="h-10 w-10 rounded-full object-cover border-2 border-emerald-100"
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
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md overflow-hidden shadow-xl z-50 border border-gray-100"
                            style="display: none;">

                            <a href="#"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600">
                                <i class="fas fa-user-circle mr-2"></i> โปรไฟล์
                            </a>
                            <div class="border-t border-gray-100"></div>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2"></i> ออกจากระบบ
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50/50 p-6">
                {{-- Container รองรับ Content --}}
                <div class="container mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
