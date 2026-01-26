<!doctype html>
<html lang="th">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- SweetAlert2 สำหรับแจ้งเตือนสวยๆ --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <title>@yield('title', 'Salepage Demo')</title>
</head>

<body class="font-['Noto_Sans_Thai'] bg-[#f9fafb]">

    {{-- [แก้ไข 1] Logic คำนวณจำนวนสินค้า (รองรับทั้ง Member และ Guest) --}}
    @php
        $cartCount = 0;
        if (auth()->check()) {
            $cartSessionId = auth()->id();
        } else {
            $cartSessionId = '_guest_' . session()->getId();
        }
        $cartCount = \Cart::session($cartSessionId)->getTotalQuantity();
    @endphp

    <div class="sticky top-0 z-50 bg-white shadow-sm border-b border-gray-100">
        <div class="container mx-auto px-4 md:px-6">
            <div class="navbar min-h-[4rem] px-0">

                {{-- ... (ส่วน Navbar Start / Center คงเดิม) ... --}}
                <div class="navbar-start">
                    {{-- ... (คงเดิม) ... --}}
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
                            <li><a href="/" class="py-3 font-bold">หน้าหลัก</a></li>
                            <li><a href="/allproducts" class="py-3 font-bold">สินค้าทั้งหมด</a></li>
                            @auth
                                <li><a href="/orderhistory" class="py-3 font-bold">ประวัติการสั่งซื้อ</a></li>
                                                            <li><a href="/ordertracking" class="py-3 font-bold">เช็คสถานะ</a></li>
                                                            <li><a href="{{ route('profile.edit') }}" class="py-3 font-bold">ข้อมูลส่วนตัว</a></li>
                                                            <li class="border-t mt-2 pt-2">                                    <div class="flex items-center gap-2 p-2">
                                        @if (auth()->user()->avatar)
                                            <img src="{{ auth()->user()->avatar }}" class="w-8 h-8 rounded-full border">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-gray-200"></div>
                                        @endif
                                        <span class="font-bold text-gray-700">{{ auth()->user()->name }}</span>
                                    </div>
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="w-full">@csrf<button
                                            type="submit"
                                            class="text-red-500 font-bold py-2 w-full text-left">ออกจากระบบ</button></form>
                                </li>
                            @else
                                <div class="p-2 mt-2"><a href="/login"
                                        class="btn bg-[#06C755] hover:bg-[#00B900] text-white w-full border-none">เข้าสู่ระบบด้วย
                                        LINE</a></div>
                            @endauth
                        </ul>
                    </div>
                    <a href="/" class="hidden md:flex btn btn-ghost text-xl p-0 hover:bg-transparent"><img
                            src="/images/logo_hm.png" alt="Logo" class="h-10 md:h-12 w-auto object-contain"></a>
                </div>

                <div class="navbar-center">
                    <a href="/" class="md:hidden btn btn-ghost text-xl p-0 hover:bg-transparent"><img
                            src="/images/logo_hm.png" alt="Logo" class="h-10 w-auto object-contain"></a>
                    <ul class="menu menu-horizontal px-1 gap-6 text-base font-medium text-gray-600 hidden md:flex">
                        <li><a href="/" class="hover:text-emerald-600 hover:bg-transparent">หน้าหลัก</a></li>
                        <li><a href="/allproducts" class="hover:text-emerald-600 hover:bg-transparent">สินค้าทั้งหมด</a>
                        </li>
                        @auth
                            <li><a href="/orderhistory"
                                    class="hover:text-emerald-600 hover:bg-transparent">ประวัติการสั่งซื้อ</a></li>
                            <li><a href="/ordertracking" class="hover:text-emerald-600 hover:bg-transparent">เช็คสถานะ</a>
                            </li>
                        @endauth
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
                            {{-- ใส่ ID และ Logic ซ่อน/แสดง --}}
                            <span id="cart-badge"
                                class="badge badge-sm indicator-item bg-red-500 text-white border-none {{ $cartCount > 0 ? '' : 'hidden' }}">
                                {{ $cartCount }}
                            </span>
                        </div>
                    </a>

                    {{-- ... (ส่วน Login / Profile คงเดิม) ... --}}
                    @guest
                        <a href="/login"
                            class="hidden md:flex items-center gap-2 btn bg-[#06C755] hover:bg-[#00B900] text-white border-none px-5 rounded-full shadow-sm hover:shadow-md transition">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M472 0H40C17.9 0 0 17.9 0 40v304c0 22.1 17.9 40 40 40h214.3l98.4 91.8c6.6 6.2 16.8 6.7 23.9 1.4 5.9-4.3 9.4-11 9.4-18.3v-74.9H472c22.1 0 40-17.9 40-40V40c0-22.1-17.9-40-40-40zM362.2 178.6c11.7 0 21.1 9.4 21.1 21.1 0 11.7-9.4 21.1-21.1 21.1-11.7 0-21.1-9.4-21.1-21.1 0-11.7 9.5-21.1 21.1-21.1zm-88.7 0c11.7 0 21.1 9.4 21.1 21.1 0 11.7-9.4 21.1-21.1 21.1-11.7 0-21.1-9.4-21.1-21.1 0-11.7 9.4-21.1 21.1-21.1zm-88.7 0c11.7 0 21.1 9.4 21.1 21.1 0 11.7-9.4 21.1-21.1 21.1-11.7 0-21.1-9.4-21.1-21.1 0-11.7 9.4-21.1 21.1-21.1z" />
                            </svg>
                            เข้าสู่ระบบ
                        </a>
                    @endguest

                    @auth
                        <div class="dropdown dropdown-end hidden md:block">
                            <div tabindex="0" role="button"
                                class="btn btn-ghost btn-circle avatar border border-emerald-200 ring-2 ring-transparent hover:ring-emerald-400 transition">
                                <div class="w-10 rounded-full">
                                    @if (auth()->user()->avatar)
                                        <img src="{{ auth()->user()->avatar }}" alt="Profile" />
                                    @else
                                        <img
                                            src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=0D8ABC&color=fff" />
                                    @endif
                                </div>
                            </div>
                            <ul tabindex="-1"
                                class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow-xl bg-white rounded-xl w-64 border border-gray-100">
                                <li class="menu-title px-4 py-3 bg-gray-50 rounded-t-lg border-b border-gray-100 mb-2">
                                    <div class="flex flex-col gap-0.5">
                                        <span class="text-xs font-normal text-gray-500">เข้าสู่ระบบโดย</span>
                                        <span class="text-sm font-bold text-gray-800 truncate w-full"
                                            title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</span>
                                    </div>
                                </li>
                                <div class="divider my-1 before:bg-gray-100 after:bg-gray-100"></div>
                                <li>
                                    <a href="{{ route('profile.edit') }}" class="w-full text-left py-3 px-4 text-gray-700 hover:bg-gray-100 hover:text-gray-800 rounded-lg flex items-center gap-3 transition-colors duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="font-medium">ข้อมูลส่วนตัว</span>
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="w-full p-0 block">
                                        @csrf<button type="submit"
                                            class="w-full text-left py-3 px-4 text-red-500 hover:bg-red-50 hover:text-red-600 rounded-lg flex items-center gap-3 transition-colors duration-200"><svg
                                                xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg><span class="font-medium">ออกจากระบบ</span></button></form>
                                </li>
                            </ul>
                        </div>
                    @endauth

                </div>
            </div>
        </div>
    </div>

    {{-- Contents --}}
    <div class="min-h-screen">
        @yield('content')
    </div>

    {{-- Footer --}}
    <div class="bg-base-200 text-base-content mt-10">
        {{-- ... (ส่วน Footer เดิม เพื่อความกระชับ) ... --}}
        <footer class="footer sm:footer-horizontal p-10 container mx-auto">
            <nav>
                <h6 class="footer-title text-emerald-600 opacity-100">ศูนย์ช่วยเหลือ</h6><a href="/ordertracking"
                    class="link link-hover">ติดตามสถานะคำสั่งซื้อ</a><a href="#"
                    class="link link-hover">ติดต่อเรา</a>
            </nav>
            <nav>
                <h6 class="footer-title text-emerald-600 opacity-100">เกี่ยวกับ H&M-R</h6><a href="#"
                    class="link link-hover">นโยบายความเป็นส่วนตัว</a>
            </nav>
            <form>
                <h6 class="footer-title text-emerald-600 opacity-100">รับข่าวสาร</h6>
                <fieldset class="form-control w-80">
                    <div class="join"><input type="text" placeholder="username@site.com"
                            class="input input-bordered join-item w-full" /><button
                            class="btn bg-emerald-600 hover:bg-emerald-700 text-white join-item border-none">สมัคร</button>
                    </div>
                </fieldset>
            </form>
        </footer>
        <footer class="footer bg-base-300 text-base-content border-base-300 border-t px-10 py-4 container mx-auto">
            <aside class="grid-flow-col items-center">
                <p>H&M-R Store Thailand © 2025</p>
            </aside>
        </footer>
    </div>

    {{-- ★★★ [แก้ไข 3] Script สำคัญ: ฟังก์ชัน JavaScript สำหรับอัปเดตตัวเลข ★★★ --}}
    <script>
        // ฟังก์ชันนี้จะถูกเรียกโดยหน้า product.blade.php เมื่อกดเพิ่มสินค้าสำเร็จ
        window.updateCartBadge = function(count) {
            const badge = document.getElementById('cart-badge');
            if (badge) {
                badge.innerText = count; // เปลี่ยนตัวเลข

                // แสดง Badge ถ้ามีของ / ซ่อนถ้าไม่มี
                if (count > 0) {
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }

                // เอฟเฟกต์เด้งดึ๋งเล็กน้อย
                badge.classList.remove('scale-100');
                badge.classList.add('scale-125');
                setTimeout(() => {
                    badge.classList.remove('scale-125');
                    badge.classList.add('scale-100');
                }, 200);
            }
        };
    </script>
    @stack('scripts')
</body>

</html>
