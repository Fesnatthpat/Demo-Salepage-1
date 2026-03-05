@extends('layout')

@section('title', 'หน้าหลัก | ติดใจ - ของกินเล่นสูตรเด็ด')

@section('content')

    {{-- ★★★ CSS ปรับแต่งสไลเดอร์ และ Product Card ★★★ --}}
    <style>
        /* --- Swiper Styles --- */
        .mySwiper .swiper-pagination-bullet {
            background-color: #ffffff !important;
            opacity: 0.5 !important;
            transition: all 0.3s ease;
        }

        .mySwiper .swiper-pagination-bullet-active {
            background-color: #ffffff !important;
            opacity: 1 !important;
            transform: scale(1.2);
        }

        /* Navigation Arrows */
        .swiper-button-next,
        .swiper-button-prev {
            width: 26px !important;
            height: 26px !important;
            background-color: rgba(255, 255, 255, 0.95) !important;
            border-radius: 50% !important;
            color: #dc2626 !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2) !important;
            transition: all 0.3s ease !important;
            margin-top: -13px !important;
        }

        .swiper-button-next::after,
        .swiper-button-prev::after {
            font-size: 11px !important;
            font-weight: 900 !important;
        }

        .swiper-button-prev {
            left: 8px !important;
        }

        .swiper-button-next {
            right: 8px !important;
        }

        @media (min-width: 768px) {

            .swiper-button-next,
            .swiper-button-prev {
                width: 35px !important;
                height: 35px !important;
                margin-top: -17.5px !important;
            }

            .swiper-button-next::after,
            .swiper-button-prev::after {
                font-size: 14px !important;
            }

            .swiper-button-prev {
                left: 16px !important;
            }

            .swiper-button-next {
                right: 16px !important;
            }
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background-color: #dc2626 !important;
            color: #ffffff !important;
            transform: scale(1.1) !important;
        }

        /* --- Product Title Fixed Height (ทำให้ชื่อสินค้าสูงเท่ากัน) --- */
        .product-title-fixed {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 2.5rem;
            /* Mobile Height */
            line-height: 1.25;
        }

        @media (min-width: 768px) {
            .product-title-fixed {
                min-height: 3.5rem;
                /* Desktop Height */
            }
        }
    </style>

    {{-- ★★★ HERO SECTION (สไลด์หลัก) ★★★ --}}
    <div class="w-full bg-white py-4 md:py-6">
        <div class="container mx-auto px-4">
            <div
                class="relative w-full aspect-[2/1] md:aspect-[3/1] lg:aspect-[5/1] bg-gray-100 group rounded-xl overflow-hidden shadow-lg">
                <div class="swiper mySwiper w-full h-full absolute inset-0">
                    <div class="swiper-wrapper">
                        @foreach (['th-1.png', 'th-2.png', 'th-3.png', 'th-4.png', 'th-5.png'] as $img)
                            <div class="swiper-slide">
                                <a href="/allproducts" class="block w-full h-full">
                                    <img src="{{ asset('images/' . $img) }}" class="w-full h-full object-cover object-center"
                                        alt="Slide"
                                        onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x500?text=Banner';" />
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ★★★ ส่วนข้อมูลแพ้อาหาร ★★★ --}}
    <div class="w-full bg-red-50 py-2">
        <div class="container mx-auto px-4">
            <img src="{{ asset('images/image_27e610.png') }}" alt="ข้อมูลสำหรับผู้แพ้อาหาร"
                class="w-full h-auto block rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300"
                onerror="this.onerror=null;this.style.display='none';" />
        </div>
    </div>

    {{-- PRODUCTS SECTION (เมนูแนะนำ) --}}
    <div class="w-full pb-12 pt-8 bg-gray-50">
        <div class="container mx-auto px-4 mb-10">

            {{-- Header Section --}}
            <div class="flex justify-between items-end mb-6 md:mb-8">
                <div>
                    <div
                        class="inline-block px-3 py-1 bg-red-100 text-red-600 rounded-lg text-xs md:text-sm font-bold mb-2">
                        Recommended
                    </div>
                    <h2 class="text-2xl md:text-4xl font-black text-gray-800 tracking-tight">
                        เมนูแนะนำ <span class="text-red-600">ต้องลอง!</span>
                    </h2>
                </div>
                <a href="/allproducts"
                    class="group flex items-center gap-1 text-red-600 font-bold hover:text-red-700 hidden md:flex transition text-sm md:text-base">
                    ดูทั้งหมด
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            {{-- Product Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                @if (isset($recommendedProducts) && count($recommendedProducts) > 0)
                    @foreach ($recommendedProducts as $product)
                        @php
                            // Logic คำนวณราคา
                            $hasOptions = isset($product->options) && $product->options->count() > 0;
                            if ($hasOptions) {
                                $originalPrice = (float) $product->options->min('option_price');
                            } else {
                                $originalPrice = (float) ($product->pd_sp_price ?? 0);
                            }
                            $discountAmount = (float) ($product->pd_sp_discount ?? 0);
                            $finalSellingPrice = max(0, $originalPrice - $discountAmount);
                            $isOnSale = $discountAmount > 0;
                            $displayImage = $product->cover_image_url;
                        @endphp

                        <div
                            class="card bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group flex flex-col h-full rounded-xl overflow-hidden">

                            {{-- Image Section --}}
                            <a href="{{ route('product.show', $product->pd_sp_id) }}"
                                class="relative aspect-square overflow-hidden bg-gray-100">
                                @if ($product->pd_sp_stock <= 0)
                                    <div class="absolute inset-0 flex items-center justify-center z-10 bg-black/50">
                                        <span
                                            class="bg-black/70 text-white text-xs px-3 py-1 rounded-full font-bold">สินค้าหมด</span>
                                    </div>
                                @endif
                                <img src="{{ $displayImage }}" alt="{{ $product->pd_sp_name }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition duration-700 {{ $product->pd_sp_stock <= 0 ? 'opacity-50' : '' }}"
                                    onerror="this.onerror=null;this.src='https://via.placeholder.com/400x400?text=No+Image';" />

                                @if ($isOnSale)
                                    <div
                                        class="absolute top-2 left-2 bg-red-600 text-white px-2 py-1 rounded text-[10px] md:text-xs font-bold shadow-md">
                                        ลด {{ number_format($discountAmount) }}.-
                                    </div>
                                @endif
                            </a>

                            {{-- Details Section --}}
                            <div class="p-3 md:p-4 flex-1 flex flex-col">
                                {{-- Name --}}
                                <h2 class="text-sm md:text-base font-bold text-gray-800 leading-tight product-title-fixed hover:text-red-600 transition cursor-pointer mb-2"
                                    onclick="window.location='{{ route('product.show', $product->pd_sp_id) }}'">
                                    {{ $product->pd_sp_name }}
                                </h2>

                                {{-- Stock & Sold Count --}}
                                <div class="flex items-center justify-between mb-2">
                                    <p
                                        class="text-[10px] md:text-xs font-semibold {{ $product->pd_sp_stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                                        {{ $product->pd_sp_stock > 0 ? '● มีสินค้า' : '● หมด' }}
                                    </p>
                                    <p class="text-[10px] text-gray-400">
                                        ขายแล้ว {{ number_format($product->pd_sp_sold ?? 0) }}
                                    </p>
                                </div>

                                {{-- Price & Button --}}
                                <div class="mt-auto pt-2 border-t border-gray-50">
                                    <div class="flex justify-between items-center w-full mb-3">
                                        <div class="flex items-center gap-1">
                                            @if ($hasOptions)
                                                <span class="text-[9px] text-gray-400">เริ่ม</span>
                                            @endif
                                            <span
                                                class="text-base md:text-xl font-black text-red-600">฿{{ number_format($finalSellingPrice) }}</span>
                                        </div>

                                        @if ($isOnSale)
                                            <span
                                                class="text-xs md:text-sm text-gray-400 line-through">฿{{ number_format($originalPrice) }}</span>
                                        @endif
                                    </div>

                                    <button type="button"
                                        @if ($hasOptions) onclick="window.location='{{ route('product.show', $product->pd_sp_id) }}'"
                                        @else onclick="addToCartQuick(this, '{{ route('cart.add', ['id' => $product->pd_sp_id]) }}')" @endif
                                        class="btn btn-sm w-full rounded-lg border-none font-bold text-white shadow-sm transition-transform active:scale-95 {{ $product->pd_sp_stock > 0 ? 'bg-red-600 hover:bg-red-700' : 'bg-gray-300 cursor-not-allowed' }}"
                                        {{ $product->pd_sp_stock <= 0 ? 'disabled' : '' }}>
                                        @if ($product->pd_sp_stock > 0)
                                            {{ $hasOptions ? 'เลือกตัวเลือก' : 'เพิ่มลงตะกร้า' }}
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
                        class="col-span-full flex flex-col items-center justify-center py-16 bg-white rounded-xl border-2 border-dashed border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        <p class="text-gray-500 font-medium">ไม่พบสินค้าแนะนำในขณะนี้</p>
                    </div>
                @endif
            </div>

            <div class="mt-8 text-center md:hidden">
                <a href="/allproducts" class="btn btn-outline border-red-600 text-red-600 w-full rounded-lg font-bold">
                    ดูสินค้าทั้งหมด
                </a>
            </div>
        </div>
    </div>

    {{-- ★★★ สไลด์ตัวที่ 2 ★★★ --}}
    <div class="w-full bg-white pt-8 pb-8">
        <div class="container mx-auto px-4">
            <div class="swiper mySwiper2 w-full max-w-[900px] mx-auto rounded-xl shadow-md overflow-hidden relative">
                <div class="swiper-wrapper">
                    @foreach (['th-a.png', 'th-b.png', 'th-c.png'] as $img)
                        <div class="swiper-slide">
                            <img src="{{ asset('images/' . $img) }}" class="w-full h-auto block"
                                onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x400?text=Banner+2';" />
                        </div>
                    @endforeach
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>

    {{-- SERVICE BAR --}}
    <div class="bg-gray-50 border-t border-gray-100 py-10 relative">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-gray-200/50">
                @php
                    $serviceBarItems = [
                        [
                            'icon' =>
                                '<svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                            'text' => 'สูตรเด็ดต้นตำรับ',
                        ],
                        [
                            'icon' =>
                                '<svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>',
                            'text' => 'ส่งไว ทันใจ',
                        ],
                        [
                            'icon' =>
                                '<svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>',
                            'text' => 'ชำระเงินปลอดภัย',
                        ],
                        [
                            'icon' =>
                                '<svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>',
                            'text' => 'ทำด้วยใจทุกขั้นตอน',
                        ],
                    ];
                @endphp
                @foreach ($serviceBarItems as $item)
                    <div class="flex flex-col items-center gap-3 group cursor-default p-2">
                        <div
                            class="p-3 bg-white shadow-sm rounded-full group-hover:bg-red-50 transition duration-300 transform group-hover:scale-110">
                            {!! $item['icon'] !!}
                        </div>
                        <span
                            class="text-sm md:text-base font-bold text-gray-700 group-hover:text-red-600 transition">{{ $item['text'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ★★★ 6 REASONS SECTION ★★★ --}}
    <div class="w-full py-16 bg-gradient-to-br from-red-700 to-red-800">
        <div class="container mx-auto px-4 relative z-10">
            <h2 class="text-2xl md:text-4xl font-extrabold text-center text-white mb-12 drop-shadow-md">
                6 เหตุผลทำไมต้องเลือกเรา
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 px-4 md:px-10 lg:px-20">
                <div class="flex flex-col items-center text-center group">
                    <div
                        class="mb-4 text-white transition-transform duration-300 group-hover:scale-110 drop-shadow-sm bg-white/10 p-4 rounded-full">
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 drop-shadow-md">เรารู้จริง</h3>
                    <p class="text-white/80 text-sm leading-relaxed">เรารู้ว่าคุณต้องการอะไร กังวลสิ่งไหน
                        เราจึงตั้งใจส่งมอบสิ่งที่ดีที่สุดให้กับคุณและคนที่คุณรัก</p>
                </div>
                <div class="flex flex-col items-center text-center group">
                    <div
                        class="mb-4 text-white transition-transform duration-300 group-hover:scale-110 drop-shadow-sm bg-white/10 p-4 rounded-full">
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 drop-shadow-md">พิถีพิถัน</h3>
                    <p class="text-white/80 text-sm leading-relaxed">เราใส่ใจทุกรายละเอียดอย่างแท้จริง
                        ตั้งแต่การคัดเลือกวัตถุดิบคุณภาพสูง ผ่านกระบวนการผลิตที่มีมาตรฐาน</p>
                </div>
                <div class="flex flex-col items-center text-center group">
                    <div
                        class="mb-4 text-white transition-transform duration-300 group-hover:scale-110 drop-shadow-sm bg-white/10 p-4 rounded-full">
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 drop-shadow-md">ทุกเพศ ทุกวัย ทุกสไตล์</h3>
                    <p class="text-white/80 text-sm leading-relaxed">อร่อยแบบไม่จำกัด
                        ด้วยผลิตภัณฑ์ที่มีหลากหลายชนิดหลายสูตร เพื่อตอบสนองต่อความต้องการที่หลากหลาย</p>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var swiper1 = new Swiper(".mySwiper", {
                slidesPerView: 1,
                spaceBetween: 0,
                loop: true,
                speed: 800,
                effect: 'slide',
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                    dynamicBullets: true
                },
                navigation: {
                    nextEl: ".mySwiper .swiper-button-next",
                    prevEl: ".mySwiper .swiper-button-prev"
                },
            });
            var swiper2 = new Swiper(".mySwiper2", {
                slidesPerView: 1,
                spaceBetween: 10,
                loop: true,
                speed: 800,
                autoHeight: true,
                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true
                },
                navigation: {
                    nextEl: ".mySwiper2 .swiper-button-next",
                    prevEl: ".mySwiper2 .swiper-button-prev"
                },
            });
        });

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
                            timer: 1500
                        });
                        setTimeout(() => {
                            Livewire.dispatch('cartUpdated');
                        }, 50);
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
