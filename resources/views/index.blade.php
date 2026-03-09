@extends('layout')

@section('title', 'หน้าหลัก | ติดใจ - ของกินเล่นสูตรเด็ด')

@section('content')

    {{-- ★★★ CSS ปรับแต่งสไลเดอร์ และ Product Card ★★★ --}}
    <style>
        /* --- Swiper Styles --- */
        .mySwiper .swiper-pagination-bullet,
        .productSwiper .swiper-pagination-bullet {
            background-color: #dc2626 !important;
            opacity: 0.3 !important;
            transition: all 0.3s ease;
        }

        .mySwiper .swiper-pagination-bullet-active,
        .productSwiper .swiper-pagination-bullet-active {
            background-color: #dc2626 !important;
            opacity: 1 !important;
            transform: scale(1.2);
        }

        /* Navigation Arrows */
        .swiper-button-next,
        .swiper-button-prev {
            width: 32px !important;
            height: 32px !important;
            background-color: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(2px);
            border-radius: 50% !important;
            color: #dc2626 !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.3s ease !important;
            margin-top: -16px !important;
            z-index: 50;
            /* เพิ่ม z-index ให้แน่ใจว่าอยู่บนสุด */
        }

        .swiper-button-next::after,
        .swiper-button-prev::after {
            font-size: 14px !important;
            font-weight: 900 !important;
        }

        .swiper-button-prev {
            left: 0px !important;
            /* ปรับตำแหน่งให้ชิดขอบมากขึ้นสำหรับ Product Slider */
        }

        .swiper-button-next {
            right: 0px !important;
        }

        /* ปรับตำแหน่งเฉพาะของ Hero Slider ให้ขยับเข้ามาหน่อย */
        .mySwiper .swiper-button-prev {
            left: 12px !important;
        }

        .mySwiper .swiper-button-next {
            right: 12px !important;
        }

        @media (min-width: 768px) {

            .swiper-button-next,
            .swiper-button-prev {
                width: 45px !important;
                height: 45px !important;
                margin-top: -22.5px !important;
            }

            .swiper-button-next::after,
            .swiper-button-prev::after {
                font-size: 18px !important;
            }

            .mySwiper .swiper-button-prev {
                left: 24px !important;
            }

            .mySwiper .swiper-button-next {
                right: 24px !important;
            }
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background-color: #dc2626 !important;
            color: #ffffff !important;
            transform: scale(1.1) !important;
        }

        /* --- Product Title Fixed Height --- */
        .product-title-fixed {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 2.5rem;
            line-height: 1.25;
        }

        @media (min-width: 768px) {
            .product-title-fixed {
                min-height: 3.5rem;
            }
        }
    </style>

    {{-- ★★★ HERO SECTION (สไลด์หลัก) ★★★ --}}
    <div class="w-full bg-white pb-6 pt-2 md:pt-4">
        <div class="container mx-auto px-4">
            <div
                class="relative w-full aspect-[16/10] md:aspect-[2.5/1] lg:aspect-[3/1] bg-gray-100 group rounded-2xl overflow-hidden shadow-xl">
                <div class="swiper mySwiper w-full h-full absolute inset-0">
                    <div class="swiper-wrapper">
                        @if (isset($heroSlides) && $heroSlides->count() > 0)
                            @foreach ($heroSlides as $slide)
                                <div class="swiper-slide">
                                    <a href="{{ $slide->link_url ?? '/allproducts' }}" class="block w-full h-full">
                                        <img src="{{ Storage::url($slide->image_path) }}"
                                            class="w-full h-full object-center object-center"
                                            alt="{{ $slide->title ?? 'Slide' }}"
                                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1600x600?text=Banner+Image';" />
                                    </a>
                                </div>
                            @endforeach
                        @else
                            {{-- Fallback Images --}}
                            @foreach (['th-1.png', 'th-2.png', 'th-3.png', 'th-4.png', 'th-5.png'] as $img)
                                <div class="swiper-slide">
                                    <a href="/allproducts" class="block w-full h-full">
                                        <img src="{{ asset('images/' . $img) }}"
                                            class="w-full h-full object-cover object-center" alt="Slide"
                                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1600x600?text=Welcome+Banner';" />
                                    </a>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ★★★ ส่วนข้อมูลแพ้อาหาร (ปรับขนาด) ★★★ --}}
    @if (isset($infoBanner) && $infoBanner->is_active)
        <div class="w-full bg-red-50 py-6">
            <div class="container mx-auto px-4">
                <img src="{{ Storage::url($infoBanner->image_path) }}"
                    alt="{{ $infoBanner->title ?? 'ข้อมูลสำหรับผู้แพ้อาหาร' }}"
                    class="w-full md:max-w-3xl mx-auto h-auto block rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 border-4 border-white"
                    onerror="this.onerror=null;this.style.display='none';" />
            </div>
        </div>
    @elseif(!isset($infoBanner))
        <div class="w-full bg-red-50 py-6">
            <div class="container mx-auto px-4">
                <img src="{{ asset('images/image_27e610.png') }}" alt="ข้อมูลสำหรับผู้แพ้อาหาร"
                    class="w-full md:max-w-3xl mx-auto h-auto block rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 border-4 border-white"
                    onerror="this.onerror=null;this.style.display='none';" />
            </div>
        </div>
    @endif

    {{-- ★★★ PRODUCTS SECTION (เมนูแนะนำ - Slider) ★★★ --}}
    <div class="w-full pb-16 pt-10 bg-gray-50">
        <div class="container mx-auto px-4 mb-10">

            {{-- Header Section --}}
            <div class="flex justify-between items-end mb-8 md:mb-10">
                <div>
                    <div
                        class="inline-block px-3 py-1 bg-red-100 text-red-600 rounded-lg text-xs md:text-sm font-bold mb-2 shadow-sm">
                        Recommended
                    </div>
                    <h2 class="text-3xl md:text-4xl font-black text-gray-800 tracking-tight">
                        เมนูแนะนำ <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-red-500">ต้องลอง!</span>
                    </h2>
                </div>
                <a href="/allproducts"
                    class="group flex items-center gap-2 text-red-600 font-bold hover:text-red-700 hidden md:flex transition-all hover:bg-red-50 px-4 py-2 rounded-full">
                    ดูทั้งหมด
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            {{-- Product Slider (Swiper) --}}
            @if (isset($recommendedProducts) && count($recommendedProducts) > 0)
                <div class="relative group">
                    <div class="swiper productSwiper !pb-12 !px-2">
                        <div class="swiper-wrapper">
                            @foreach ($recommendedProducts as $product)
                                @php
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

                                    $productPromo = null;
                                    if (isset($promotions)) {
                                        $productPromo = $promotions->first(function ($promo) use ($product) {
                                            return $promo->rules->contains(function ($rule) use ($product) {
                                                $pids = (array) ($rule->rules['product_id'] ?? []);
                                                return in_array($product->pd_sp_id, array_map('intval', $pids));
                                            });
                                        });
                                    }
                                @endphp

                                <div class="swiper-slide h-auto">
                                    <div
                                        class="card bg-white border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 group flex flex-col h-full rounded-2xl overflow-hidden relative">

                                        {{-- Promotion Badge --}}
                                        @if ($productPromo)
                                            <div class="absolute top-0 right-0 z-20">
                                                <div
                                                    class="bg-gradient-to-l from-pink-600 to-red-500 text-white px-3 py-1 rounded-bl-xl shadow-lg flex flex-col items-end">
                                                    <span class="text-[10px] font-black uppercase tracking-tighter">Free
                                                        Gift</span>
                                                    <span
                                                        class="text-[9px] font-bold opacity-90 leading-none">{{ $productPromo->name }}</span>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Image --}}
                                        <a href="{{ route('product.show', $product->pd_sp_id) }}"
                                            class="relative aspect-square overflow-hidden bg-gray-100 block">
                                            @if ($product->pd_sp_stock <= 0)
                                                <div
                                                    class="absolute inset-0 flex items-center justify-center z-10 bg-black/60 backdrop-blur-[2px]">
                                                    <span
                                                        class="bg-white/10 text-white border border-white/50 text-sm px-4 py-1.5 rounded-full font-bold">สินค้าหมด</span>
                                                </div>
                                            @endif
                                            <img src="{{ $displayImage }}" alt="{{ $product->pd_sp_name }}"
                                                class="w-full h-full object-cover group-hover:scale-110 transition duration-700 {{ $product->pd_sp_stock <= 0 ? 'opacity-50 grayscale' : '' }}"
                                                onerror="this.onerror=null;this.src='https://via.placeholder.com/400x400?text=No+Image';" />

                                            @if ($isOnSale)
                                                <div
                                                    class="absolute top-3 left-3 bg-red-600 text-white px-2.5 py-1 rounded-lg text-[10px] md:text-xs font-bold shadow-lg shadow-red-600/30">
                                                    ลด {{ number_format($discountAmount) }}.-
                                                </div>
                                            @endif
                                        </a>

                                        {{-- Details --}}
                                        <div class="p-4 flex-1 flex flex-col">
                                            <h2 class="text-sm md:text-base font-bold text-gray-800 leading-tight product-title-fixed hover:text-red-600 transition cursor-pointer mb-2"
                                                onclick="window.location='{{ route('product.show', $product->pd_sp_id) }}'">
                                                {{ $product->pd_sp_name }}
                                            </h2>

                                            <div class="flex items-center justify-between mb-3">
                                                <p
                                                    class="text-[10px] md:text-xs font-semibold {{ $product->pd_sp_stock > 0 ? 'text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full' : 'text-red-500 bg-red-50 px-2 py-0.5 rounded-full' }}">
                                                    {{ $product->pd_sp_stock > 0 ? '● มีสินค้า' : '● หมด' }}
                                                </p>
                                                <p class="text-[10px] text-gray-400">ขายแล้ว
                                                    {{ number_format($product->pd_sp_sold ?? 0) }}</p>
                                            </div>

                                            <div class="mt-auto pt-3 border-t border-gray-50">
                                                <div class="flex justify-between items-end w-full mb-3">
                                                    <div class="flex flex-col">
                                                        @if ($isOnSale)
                                                            <span
                                                                class="text-xs text-gray-400 line-through">฿{{ number_format($originalPrice) }}</span>
                                                        @endif
                                                        <div class="flex items-center gap-1">
                                                            @if ($hasOptions)
                                                                <span class="text-[10px] text-gray-400 mb-0.5">เริ่ม</span>
                                                            @endif
                                                            <span
                                                                class="text-lg md:text-xl font-black text-gray-800">฿{{ number_format($finalSellingPrice) }}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="button"
                                                    @if ($hasOptions) onclick="window.location='{{ route('product.show', $product->pd_sp_id) }}'"
                                                    @else onclick="addToCartQuick(this, '{{ route('cart.add', ['id' => $product->pd_sp_id]) }}')" @endif
                                                    class="btn btn-sm w-full rounded-xl border-none font-bold text-white shadow-md shadow-red-200 transition-all hover:shadow-lg hover:shadow-red-300 active:scale-95 {{ $product->pd_sp_stock > 0 ? 'bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600' : 'bg-gray-300 cursor-not-allowed' }}"
                                                    {{ $product->pd_sp_stock <= 0 ? 'disabled' : '' }}>
                                                    @if ($product->pd_sp_stock > 0)
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                        </svg>
                                                        {{ $hasOptions ? 'เลือกตัวเลือก' : 'ใส่ตะกร้า' }}
                                                    @else
                                                        สินค้าหมด
                                                    @endif
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>

                    {{-- ปุ่มกดซ้ายขวาสำหรับ Product Slider --}}
                    <div class="swiper-button-next !w-8 !h-8 md:!w-10 md:!h-10 after:!text-xs md:after:!text-sm"></div>
                    <div class="swiper-button-prev !w-8 !h-8 md:!w-10 md:!h-10 after:!text-xs md:after:!text-sm"></div>
                </div>
            @else
                {{-- Empty State --}}
                <div
                    class="col-span-full flex flex-col items-center justify-center py-20 bg-white rounded-2xl border-2 border-dashed border-gray-200">
                    <div class="bg-gray-50 p-4 rounded-full mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <p class="text-gray-500 font-medium">ไม่พบสินค้าแนะนำในขณะนี้</p>
                </div>
            @endif

            <div class="mt-5 text-center md:hidden">
                <a href="/allproducts"
                    class="btn btn-outline border-red-600 text-red-600 w-full rounded-xl font-bold hover:bg-red-50">
                    ดูสินค้าทั้งหมด
                </a>
            </div>
        </div>
    </div>

    {{-- ★★★ สไลด์ตัวที่ 2 ★★★ --}}
    <div class="w-full bg-white pt-10 pb-12">
        <div class="container mx-auto px-4">
            <div
                class="swiper mySwiper2 w-full max-w-[1000px] mx-auto rounded-2xl shadow-lg overflow-hidden relative border border-gray-100">
                <div class="swiper-wrapper">
                    @if (isset($secSlides) && $secSlides->count() > 0)
                        @foreach ($secSlides as $slide)
                            <div class="swiper-slide">
                                <a href="{{ $slide->link_url ?? '#' }}" class="block w-full h-full">
                                    <img src="{{ Storage::url($slide->image_path) }}" class="w-full h-auto block"
                                        alt="{{ $slide->title ?? 'Banner' }}"
                                        onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x400?text=Promotion';" />
                                </a>
                            </div>
                        @endforeach
                    @else
                        @foreach (['th-a.png', 'th-b.png', 'th-c.png'] as $img)
                            <div class="swiper-slide">
                                <img src="{{ asset('images/' . $img) }}" class="w-full h-auto block"
                                    onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x400?text=Promotion+Banner';" />
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>

    {{-- SERVICE BAR --}}
    <div class="bg-gradient-to-b from-white to-gray-50 border-t border-gray-100 py-12 relative">
        <div class="container mx-auto px-4">
            <div
                class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 text-center divide-x-0 md:divide-x divide-gray-200">
                @if (isset($services) && $services->count() > 0)
                    @foreach ($services as $service)
                        <div class="flex flex-col items-center gap-4 group cursor-default p-2">
                            <div
                                class="p-4 bg-white shadow-md rounded-2xl group-hover:bg-red-500 group-hover:text-white transition-all duration-300 transform group-hover:-translate-y-2 ring-1 ring-gray-100">
                                @if (str_contains($service->icon, '<svg'))
                                    <div class="w-8 h-8 group-hover:text-white text-red-500 transition-colors">
                                        {!! $service->icon !!}
                                    </div>
                                @else
                                    <i
                                        class="{{ $service->icon }} text-3xl text-red-500 group-hover:text-white transition-colors"></i>
                                @endif
                            </div>
                            <span
                                class="text-sm md:text-lg font-bold text-gray-700 group-hover:text-red-600 transition">{{ $service->title }}</span>
                        </div>
                    @endforeach
                @else
                    @php
                        $serviceBarItems = [
                            ['icon' => 'fas fa-utensils', 'text' => 'สูตรเด็ดต้นตำรับ'],
                            ['icon' => 'fas fa-shipping-fast', 'text' => 'ส่งไว ทันใจ'],
                            ['icon' => 'fas fa-shield-alt', 'text' => 'ชำระเงินปลอดภัย'],
                            ['icon' => 'fas fa-heart', 'text' => 'ทำด้วยใจทุกขั้นตอน'],
                        ];
                    @endphp
                    @foreach ($serviceBarItems as $item)
                        <div class="flex flex-col items-center gap-4 group cursor-default p-2">
                            <div
                                class="p-4 bg-white shadow-md rounded-2xl group-hover:bg-red-500 transition-all duration-300 transform group-hover:-translate-y-2">
                                <i
                                    class="{{ $item['icon'] }} text-3xl text-red-500 group-hover:text-white transition-colors"></i>
                            </div>
                            <span
                                class="text-sm md:text-lg font-bold text-gray-700 group-hover:text-red-600 transition">{{ $item['text'] }}</span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- ★★★ 6 REASONS SECTION ★★★ --}}
    <div class="w-full py-20 bg-gradient-to-br from-red-800 to-red-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10"
            style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>

        <div class="container mx-auto px-4 relative z-10">
            <h2 class="text-3xl md:text-5xl font-extrabold text-center text-white mb-16 drop-shadow-lg">
                6 เหตุผลทำไมต้องเลือกเรา
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-12 px-4 md:px-10 lg:px-20">
                @if (isset($reasons) && $reasons->count() > 0)
                    @foreach ($reasons as $reason)
                        <div class="flex flex-col items-center text-center group">
                            <div
                                class="mb-6 text-white transition-all duration-300 group-hover:scale-110 group-hover:bg-white/20 bg-white/10 p-5 rounded-2xl backdrop-blur-sm shadow-inner">
                                @if (str_contains($reason->icon, '<svg'))
                                    <div class="w-10 h-10">
                                        {!! $reason->icon !!}
                                    </div>
                                @else
                                    <i class="{{ $reason->icon }} text-4xl"></i>
                                @endif
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3 drop-shadow-md">{{ $reason->title }}</h3>
                            <p class="text-white/80 text-sm md:text-base leading-relaxed max-w-xs">
                                {{ $reason->description }}</p>
                        </div>
                    @endforeach
                @else
                    <div class="flex flex-col items-center text-center group">
                        <div class="mb-6 bg-white/10 p-5 rounded-2xl text-white">
                            <i class="fas fa-check-circle text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">เรารู้จริง</h3>
                        <p class="text-white/80 text-sm leading-relaxed max-w-xs">คัดสรรแต่วัตถุดิบคุณภาพดีที่สุดเพื่อคุณ
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hero Slider
            var swiper1 = new Swiper(".mySwiper", {
                slidesPerView: 1,
                spaceBetween: 0,
                loop: true,
                speed: 1000,
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
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

            // ★★★ Product Slider (แก้ไขเพิ่ม Navigation) ★★★
            var swiperProducts = new Swiper(".productSwiper", {
                slidesPerView: 2,
                spaceBetween: 15,
                loop: true,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true
                },
                pagination: {
                    el: ".productSwiper .swiper-pagination",
                    clickable: true,
                },
                navigation: {
                    nextEl: ".group .swiper-button-next",
                    prevEl: ".group .swiper-button-prev",
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 20,
                    },
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 20,
                    },
                    1024: {
                        slidesPerView: 4,
                        spaceBetween: 25,
                    },
                },
            });

            // Secondary Slider
            var swiper2 = new Swiper(".mySwiper2", {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                speed: 800,
                autoHeight: true,
                autoplay: {
                    delay: 4500,
                    disableOnInteraction: false
                },
                pagination: {
                    el: ".mySwiper2 .swiper-pagination",
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
                            timer: 1500,
                            toast: true,
                            position: 'top-end'
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
                .catch(err => console.error(err))
                .finally(() => {
                    setTimeout(() => {
                        btnElement.disabled = false;
                        btnElement.innerHTML = originalHTML;
                    }, 500);
                });
        }
    </script>
@endsection
