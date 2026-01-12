<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="emerald">
<head>
    <meta charset="utf-g">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'CRM Admin')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <script src="https://kit.fontawesome.com/c8014560d3.js" crossorigin="anonymous"></script>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
        }
        .admin-bg {
            background-color: #f0f2f5;
        }
    </style>
</head>
<body class="admin-bg antialiased">
    <div x-data="{ sidebarOpen: true }" class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside 
            class="flex-shrink-0 w-64 flex flex-col border-r transition-all duration-300 bg-white"
            :class="{ '-ml-64': !sidebarOpen }">
            <div class="h-16 flex items-center justify-center border-b">
                <h1 class="text-2xl font-bold text-emerald-600">CRM ADMIN</h1>
            </div>
            <nav class="flex-1 px-4 py-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-gray-200' : 'hover:bg-gray-200' }}">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    แดชบอร์ด
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-2 text-gray-600 rounded-md {{ request()->routeIs('admin.orders.*') ? 'bg-gray-200' : 'hover:bg-gray-200' }}">
                    <i class="fas fa-shopping-cart mr-3"></i>
                    ออเดอร์
                </a>
                <a href="{{ route('admin.salepages.index') }}" class="flex items-center px-4 py-2 text-gray-600 rounded-md {{ request()->routeIs('admin.salepages.*') ? 'bg-gray-200' : 'hover:bg-gray-200' }}">
                    <i class="fas fa-box mr-3"></i>
                    จัดการราคาสินค้า
                </a>
                <a href="{{ route('admin.products.index') }}" class="flex items-center px-4 py-2 text-gray-600 rounded-md {{ request()->routeIs('admin.products.*') ? 'bg-gray-200' : 'hover:bg-gray-200' }}">
                    <i class="fas fa-boxes mr-3"></i> {{-- Changed icon to fas fa-boxes --}}
                    จัดการสินค้า
                </a>
                <a href="#" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-200 rounded-md">
                    <i class="fas fa-users mr-3"></i>
                    ลูกค้า
                </a>
            </nav>
            <div class="px-4 py-4 border-t">
                <a href="{{ route('home') }}" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-200 rounded-md">
                    <i class="fas fa-globe mr-3"></i>
                    กลับหน้าเว็บไซต์
                </a>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center px-4 py-2 text-red-500 hover:bg-red-50 rounded-md mt-2">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    ออกจากระบบ
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top bar -->
            <header class="flex justify-between items-center p-4 bg-white border-b">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none lg:hidden">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    <h1 class="text-xl font-semibold ml-4">@yield('page-title', 'Dashboard')</h1>
                </div>

                <div class="flex items-center">
                    <!-- User dropdown -->
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2 relative focus:outline-none">
                            <h2 class="text-gray-700 text-sm hidden sm:block">
                                {{ Auth::user()->name ?? 'Admin User' }}
                            </h2>
                            <img class="h-9 w-9 rounded-full object-cover" src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.(Auth::user()->name ?? 'Admin').'&background=random' }}" alt="Your avatar">
                        </button>

                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md overflow-hidden shadow-xl z-10" style="display: none;">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-600 hover:text-white">Profile</a>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-600 hover:text-white">Logout</a>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                <div class="container mx-auto px-4 py-4">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @stack('scripts') {{-- Add this line to render scripts from child views --}}
</body>
</html>
