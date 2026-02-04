@extends('layout')

@section('title', 'หน้าหลัก | ติดใจ - ของกินเล่นสูตรเด็ด')

@section('content')

    {{-- ★★★ HERO SECTION (สไลด์หลักด้านบน) ★★★ --}}
    <div class="relative w-full h-[200px] md:h-[350px] lg:h-[700px] bg-gray-100 group">
        <div class="swiper mySwiper w-full h-full">
            <div class="swiper-wrapper">
                {{-- Slide 1 --}}
                <div class="swiper-slide">
                    <a href="/allproducts" class="block w-full h-full">
                        <img src="{{ asset('images/th-1.png') }}" class="w-full h-full object-cover object-center"
                            alt="โปรโมชั่น Sale"
                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x500/783630/ffffff?text=Missing+image_275bca.png';" />
                    </a>
                </div>
                {{-- Slide 2 --}}
                <div class="swiper-slide">
                    <a href="/allproducts" class="block w-full h-full">
                        <img src="{{ asset('images/th-2.png') }}" class="w-full h-full object-cover object-center"
                            alt="จัดส่งวันไหน"
                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x500/ef4444/ffffff?text=Image+Not+Found';" />
                    </a>
                </div>
                {{-- Slide 3 --}}
                <div class="swiper-slide">
                    <a href="/allproducts" class="block w-full h-full">
                        <img src="{{ asset('images/th-3.png') }}" class="w-full h-full object-cover object-center"
                            alt="ขอขอบคุณลูกค้า"
                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x500/ef4444/ffffff?text=Image+Not+Found';" />
                    </a>
                </div>
                {{-- Slide 4 --}}
                <div class="swiper-slide">
                    <a href="/allproducts" class="block w-full h-full">
                        <img src="{{ asset('images/th-4.png') }}" class="w-full h-full object-cover object-center"
                            alt="ข้อมูลฮาลาล"
                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x500/ef4444/ffffff?text=Image+Not+Found';" />
                    </a>
                </div>
                {{-- Slide 5 --}}
                <div class="swiper-slide">
                    <a href="/allproducts" class="block w-full h-full">
                        <img src="{{ asset('images/th-5.png') }}" class="w-full h-full object-cover object-center"
                            alt="โปรโมชั่นส่งฟรี"
                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x500/ef4444/ffffff?text=Image+Not+Found';" />
                    </a>
                </div>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
        </div>
    </div>

    {{-- ★★★ ส่วนข้อมูลแพ้อาหาร (อยู่ใต้สไลด์หลัก) ★★★ --}}
    <div class="w-full bg-red-50">
        <div class="container mx-auto">
            <img src="{{ asset('images/image_27e610.png') }}" alt="ข้อมูลสำหรับผู้แพ้อาหาร"
                class="w-full h-auto block shadow-sm hover:shadow-lg transition-shadow duration-300"
                onerror="this.onerror=null;this.style.display='none';" />
        </div>
    </div>

    {{-- SERVICE BAR --}}
    <div class="bg-white border-b border-gray-100 py-8 relative">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-gray-100">
                @php
                    $serviceBarItems = [
                        [
                            'icon' =>
                                '<svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                            'text' => 'สูตรเด็ดต้นตำรับ',
                        ],
                        [
                            'icon' =>
                                '<svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>',
                            'text' => 'ส่งไว ทันใจ',
                        ],
                        [
                            'icon' =>
                                '<svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>',
                            'text' => 'ชำระเงินปลอดภัย',
                        ],
                        [
                            'icon' =>
                                '<svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>',
                            'text' => 'ทำด้วยใจทุกขั้นตอน',
                        ],
                    ];
                @endphp
                @foreach ($serviceBarItems as $item)
                    <div class="flex flex-col items-center gap-3 group cursor-default">
                        <div class="p-3 bg-red-50 rounded-full group-hover:bg-red-100 transition duration-300">
                            {!! $item['icon'] !!}</div>
                        <span
                            class="text-base font-bold text-gray-700 group-hover:text-red-600 transition">{{ $item['text'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ★★★ สไลด์ตัวที่ 2 (แก้ไขตามคำขอ: ทำให้ส่วนนี้สไลด์ได้) ★★★ --}}
    <div class="w-full bg-gray-50/50 pt-8 pb-4">
        <div class="container mx-auto px-4">
            {{-- สร้าง Container สไลด์ใหม่ ชื่อ mySwiper2 --}}
            <div class="swiper mySwiper2 w-full rounded-2xl shadow-md overflow-hidden">
                <div class="swiper-wrapper">

                    {{-- Slide 1: รูป th-c.png --}}
                    <div class="swiper-slide">
                        <img src="{{ asset('images/th-a.png') }}" alt="โปรโมชั่นพิเศษ"
                            class="w-full h-auto block hover:shadow-xl transition-all duration-300"
                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x400/ef4444/ffffff?text=Image+Not+Found';" />
                    </div>

                    <div class="swiper-slide">
                        <img src="{{ asset('images/th-b.png') }}" alt="โปรโมชั่นพิเศษ"
                            class="w-full h-auto block hover:shadow-xl transition-all duration-300"
                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x400/ef4444/ffffff?text=Image+Not+Found';" />
                    </div>

                    <div class="swiper-slide">
                        <img src="{{ asset('images/th-c.png') }}" alt="โปรโมชั่นพิเศษ"
                            class="w-full h-auto block hover:shadow-xl transition-all duration-300"
                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x400/ef4444/ffffff?text=Image+Not+Found';" />
                    </div>

                    {{-- (ตัวอย่าง) Slide 2: คุณสามารถเพิ่มรูปอื่นๆ ได้ที่นี่ --}}
                    {{-- 
                    <div class="swiper-slide">
                        <img src="{{ asset('images/another-image.png') }}" class="w-full h-auto block" />
                    </div> 
                    --}}

                </div>
                {{-- Pagination ของสไลด์ตัวที่ 2 --}}
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>

    {{-- PRODUCTS SECTION --}}
    <div class="bg-gray-50/50 pb-12 pt-4">
        <div class="container mx-auto px-4 mb-10">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <div class="inline-block px-3 py-1 bg-red-100 text-red-600 rounded-lg text-sm font-bold mb-2">
                        Recommended</div>
                    <h2 class="text-3xl md:text-4xl font-black text-gray-800 tracking-tight">เมนูแนะนำ <span
                            class="text-red-600">ต้องลอง!</span></h2>
                </div>
                <a href="/allproducts"
                    class="group flex items-center gap-1 text-red-600 font-bold hover:text-red-700 hidden md:flex transition">
                    ดูทั้งหมด <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @if (isset($recommendedProducts) && count($recommendedProducts) > 0)
                    @foreach ($recommendedProducts as $product)
                        @php
                            $originalPrice = (float) ($product->pd_sp_price ?? 0);
                            $discountAmount = (float) ($product->pd_sp_discount ?? 0);
                            $finalSellingPrice = max(0, $originalPrice - $discountAmount);
                            $isOnSale = $discountAmount > 0;
                            $displayImage = 'https://via.placeholder.com/400x400.png?text=Snack+Image';
                            if ($product->images && $product->images->isNotEmpty()) {
                                $primaryImage =
                                    $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                $rawPath = $primaryImage->image_path ?? $primaryImage->img_path;
                                if ($rawPath) {
                                    $displayImage = filter_var($rawPath, FILTER_VALIDATE_URL)
                                        ? $rawPath
                                        : asset('storage/' . ltrim(str_replace('storage/', '', $rawPath), '/'));
                                }
                            }
                        @endphp
                        <div
                            class="card bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group flex flex-col h-full rounded-2xl overflow-hidden">
                            <a href="{{ route('product.show', $product->pd_sp_id) }}"
                                class="block overflow-hidden relative pt-[100%]">
                                <img src="{{ $displayImage }}" alt="{{ $product->pd_sp_name }}"
                                    class="absolute top-0 left-0 w-full h-full object-cover group-hover:scale-110 transition duration-700"
                                    onerror="this.onerror=null;this.src='https://via.placeholder.com/400x400.png?text=No+Image';" />
                                @if ($isOnSale)
                                    <div
                                        class="absolute top-3 right-3 bg-red-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md animate-pulse">
                                        ลด {{ number_format($discountAmount) }}.-</div>
                                @endif
                            </a>
                            <div class="p-5 flex-1 flex flex-col">
                                <div class="mb-2">
                                    <h2 class="text-lg font-bold text-gray-800 leading-tight line-clamp-2 hover:text-red-600 transition cursor-pointer"
                                        onclick="window.location='{{ route('product.show', $product->pd_sp_id) }}'">
                                        {{ $product->pd_sp_name }}</h2>
                                    <p class="text-xs text-gray-400 mt-1">รหัส: {{ $product->pd_sp_code }}</p>
                                </div>
                                <div class="mt-auto">
                                    <div class="flex justify-between items-center mb-4">
                                        <div class="flex flex-col">
                                            @if ($isOnSale)
                                                <span
                                                    class="text-xs text-gray-400 line-through">฿{{ number_format($originalPrice) }}</span>
                                                <span
                                                    class="text-xl font-black text-red-600">฿{{ number_format($finalSellingPrice) }}</span>
                                            @else
                                                <span
                                                    class="text-xl font-black text-red-600">฿{{ number_format($finalSellingPrice) }}</span>
                                            @endif
                                        </div>
                                        <div
                                            class="text-xs font-semibold {{ $product->pd_sp_stock > 0 ? 'text-green-600 bg-green-50' : 'text-red-500 bg-red-50' }} px-2 py-1 rounded-md">
                                            {{ $product->pd_sp_stock > 0 ? 'มีสินค้า' : 'สินค้าหมด' }}</div>
                                    </div>
                                    <button type="button"
                                        onclick="addToCartQuick(this, '{{ route('cart.add', ['id' => $product->pd_sp_id]) }}')"
                                        class="btn w-full rounded-xl border-none font-bold text-white shadow-md transition-transform active:scale-95 {{ $product->pd_sp_stock > 0 ? 'bg-red-600 hover:bg-red-700 shadow-red-200' : 'bg-gray-300 cursor-not-allowed' }}"
                                        {{ $product->pd_sp_stock <= 0 ? 'disabled' : '' }}>
                                        @if ($product->pd_sp_stock > 0)
                                            ใส่ตะกร้าเลย
                                        @else
                                            สินค้าหมด
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div
                        class="col-span-full flex flex-col items-center justify-center py-16 bg-white rounded-3xl border border-dashed border-gray-300">
                        <p class="text-gray-500 font-medium">ไม่พบสินค้าแนะนำในขณะนี้</p>
                    </div>
                @endif
            </div>
            <div class="mt-10 text-center md:hidden">
                <a href="/allproducts"
                    class="btn btn-outline border-red-600 text-red-600 w-full rounded-xl font-bold">ดูสินค้าทั้งหมด</a>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    {{-- ★★★ SCRIPT INITIALIZE SWIPER (2 ตัว) ★★★ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 1. สไลด์หลักด้านบน (mySwiper)
            var swiper1 = new Swiper(".mySwiper", {
                slidesPerView: 1,
                spaceBetween: 0,
                loop: true,
                speed: 800,
                effect: 'slide',
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                    dynamicBullets: true,
                },
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
            });

            // 2. สไลด์ตัวที่สอง (mySwiper2) - ตรง th-c.png
            var swiper2 = new Swiper(".mySwiper2", {
                slidesPerView: 1,
                spaceBetween: 10,
                loop: true, // วนลูปได้ (ถ้ามีรูปเดียวอาจจะไม่วน)
                speed: 800,
                autoHeight: true, // ปรับความสูงอัตโนมัติ
                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
            });
        });

        // --- Cart Function ---
        function addToCartQuick(btnElement, url) {
            if (btnElement.disabled) return;
            const originalHTML = btnElement.innerHTML;
            btnElement.disabled = true;
            btnElement.innerHTML = '<span class="loading loading-spinner loading-xs"></span> กำลังปรุง...';
            const formData = new FormData();
            formData.append('quantity', 1);

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (typeof window.flyToCart === 'function') window.flyToCart(btnElement);
                        Swal.fire({
                            icon: 'success',
                            title: 'เพิ่มเรียบร้อย!',
                            showConfirmButton: false,
                            timer: 1500,
                            position: 'top-end',
                            toast: true,
                            background: '#FEF2F2',
                            iconColor: '#DC2626'
                        });
                        if (window.updateCartBadge) window.updateCartBadge(data.cartCount);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'แจ้งเตือน',
                            text: data.message || 'เพิ่มสินค้าไม่ได้',
                            confirmButtonColor: '#DC2626'
                        });
                    }
                })
                .catch(err => Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Connection failed',
                    confirmButtonColor: '#DC2626'
                }))
                .finally(() => {
                    setTimeout(() => {
                        btnElement.disabled = false;
                        btnElement.innerHTML = originalHTML;
                    }, 500);
                });
        }
    </script>
@endsection
