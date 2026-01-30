<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title', 'CRM Admin'); ?></title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <?php echo $__env->yieldContent('styles'); ?>

    <style>
        body {
            font-family: 'Sarabun', sans-serif;
        }

        /* Scrollbar สีเข้ม */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1f2937;
        }

        ::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        /* ปรับแต่ง TomSelect สำหรับ Dark Mode */
        .ts-control {
            background-color: #374151;
            /* gray-700 */
            border-color: #4b5563;
            /* gray-600 */
            color: #f3f4f6;
            /* gray-100 */
            border-radius: 0.5rem;
            padding: 0.6rem;
        }

        .ts-control input {
            color: #f3f4f6;
        }

        .ts-dropdown {
            background-color: #374151;
            border-color: #4b5563;
            color: #f3f4f6;
        }

        .ts-dropdown .option:hover,
        .ts-dropdown .active {
            background-color: #4b5563;
            color: #fff;
        }
    </style>
</head>

<body class="bg-gray-900 antialiased text-gray-100">
    <div x-data="{ sidebarOpen: true }" class="flex h-screen overflow-hidden bg-gray-900">

        
        <aside class="flex-shrink-0 w-64 flex flex-col border-r border-gray-700 transition-all duration-300 bg-gray-800"
            :class="{ '-ml-64': !sidebarOpen }">

            <div class="h-16 flex items-center justify-center border-b border-gray-700 bg-gray-800">
                <h1 class="text-2xl font-bold text-emerald-400 tracking-wider">CRM ADMIN</h1>
            </div>

            <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
                <a href="<?php echo e(route('admin.dashboard')); ?>"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors <?php echo e(request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-emerald-400 font-semibold border-l-4 border-emerald-500' : 'text-gray-400 hover:bg-gray-700 hover:text-white'); ?>">
                    <i class="fas fa-tachometer-alt mr-3 w-5 text-center"></i>
                    แดชบอร์ด
                </a>
                <a href="<?php echo e(route('admin.orders.index')); ?>"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors <?php echo e(request()->routeIs('admin.orders.*') ? 'bg-gray-700 text-emerald-400 font-semibold border-l-4 border-emerald-500' : 'text-gray-400 hover:bg-gray-700 hover:text-white'); ?>">
                    <i class="fas fa-shopping-cart mr-3 w-5 text-center"></i>
                    ออเดอร์
                </a>
                <a href="<?php echo e(route('admin.products.index')); ?>"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors <?php echo e(request()->routeIs('admin.products.*') ? 'bg-gray-700 text-emerald-400 font-semibold border-l-4 border-emerald-500' : 'text-gray-400 hover:bg-gray-700 hover:text-white'); ?>">
                    <i class="fas fa-boxes mr-3 w-5 text-center"></i>
                    จัดการสินค้า
                </a>
                <a href="<?php echo e(route('admin.customers.index')); ?>"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors <?php echo e(request()->routeIs('admin.customers.*') ? 'bg-gray-700 text-emerald-400 font-semibold border-l-4 border-emerald-500' : 'text-gray-400 hover:bg-gray-700 hover:text-white'); ?>">
                    <i class="fas fa-users mr-3 w-5 text-center"></i>
                    ลูกค้า
                </a>
                <a href="<?php echo e(route('admin.promotions.index')); ?>"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors <?php echo e(request()->routeIs('admin.promotions.*') ? 'bg-gray-700 text-emerald-400 font-semibold border-l-4 border-emerald-500' : 'text-gray-400 hover:bg-gray-700 hover:text-white'); ?>">
                    <i class="fas fa-tags mr-3 w-5 text-center"></i>
                    โปรโมชั่น
                </a>

                <?php if(auth()->guard('admin')->check() && auth()->guard('admin')->user()->role === 'superadmin'): ?>
                    <div class="pt-4 pb-2">
                        <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">System</p>
                    </div>
                    <a href="<?php echo e(route('admin.activity-log.index')); ?>"
                        class="flex items-center px-4 py-3 rounded-lg transition-colors <?php echo e(request()->routeIs('admin.activity-log.*') ? 'bg-gray-700 text-emerald-400 font-semibold border-l-4 border-emerald-500' : 'text-gray-400 hover:bg-gray-700 hover:text-white'); ?>">
                        <i class="fas fa-history mr-3 w-5 text-center"></i>
                        ประวัติกิจกรรม
                    </a>
                    <a href="<?php echo e(route('admin.admins.index')); ?>"
                        class="flex items-center px-4 py-3 rounded-lg transition-colors <?php echo e(request()->routeIs('admin.admins.*') ? 'bg-gray-700 text-emerald-400 font-semibold border-l-4 border-emerald-500' : 'text-gray-400 hover:bg-gray-700 hover:text-white'); ?>">
                        <i class="fas fa-user-shield mr-3 w-5 text-center"></i>
                        จัดการแอดมิน
                    </a>
                <?php endif; ?>
            </nav>

            <div class="px-4 py-4 border-t border-gray-700 bg-gray-800">
                <a href="<?php echo e(route('home')); ?>" target="_blank"
                    class="flex items-center px-4 py-2 text-sm text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition-all">
                    <i class="fas fa-globe mr-3 w-5 text-center"></i>
                    ไปหน้าเว็บไซต์
                </a>
                <a href="<?php echo e(route('admin.logout')); ?>"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="flex items-center px-4 py-2 text-sm text-red-400 hover:bg-red-900/30 rounded-md mt-2 transition-colors">
                    <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>
                    ออกจากระบบ
                </a>
                <form id="logout-form" action="<?php echo e(route('admin.logout')); ?>" method="POST" style="display: none;">
                    <?php echo csrf_field(); ?>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden bg-gray-900">
            
            <header class="flex justify-between items-center p-4 bg-gray-800 border-b border-gray-700 shadow-sm z-20">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="text-gray-400 focus:outline-none hover:text-white transition-colors">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    <h1 class="text-xl font-semibold ml-4 text-gray-100"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h1>
                </div>

                <div class="flex items-center">
                    <?php if(auth()->guard('admin')->check()): ?>
                        <div x-data="{ dropdownOpen: false }" class="relative">
                            <button @click="dropdownOpen = !dropdownOpen"
                                class="flex items-center space-x-2 relative focus:outline-none hover:opacity-80 transition-opacity">
                                <div class="text-right mr-2 hidden sm:block">
                                    <div class="text-sm font-semibold text-gray-200">
                                        <?php echo e(Auth::user()->name ?? 'Admin User'); ?></div>
                                    <div class="text-xs text-gray-400">Administrator</div>
                                </div>
                                <img class="h-10 w-10 rounded-full object-cover border-2 border-gray-600"
                                    src="<?php echo e(Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . (Auth::user()->name ?? 'Admin') . '&background=10b981&color=fff'); ?>"
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
                                <a href="<?php echo e(route('logout')); ?>"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="block px-4 py-2 text-sm text-red-400 hover:bg-red-900/30">
                                    <i class="fas fa-sign-out-alt mr-2"></i> ออกจากระบบ
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-900 p-6">
                
                <div class="container mx-auto">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </main>
        </div>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH D:\laravel\salepage-demo-1\resources\views/layouts/admin.blade.php ENDPATH**/ ?>