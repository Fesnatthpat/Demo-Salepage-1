@extends('layout')

@section('title', 'หน้าหลัก | ติดใจ - ของกินเล่นสูตรเด็ด')

@section('content')

    <style>
        /* --- Swiper Styles --- */
        .mySwiper .swiper-pagination-bullet,
        .productSwiper .swiper-pagination-bullet,
        .mySwiper2 .swiper-pagination-bullet {
            background-color: #dc2626 !important;
            opacity: 0.3 !important;
            transition: all 0.3s ease;
            width: 6px !important;
            height: 6px !important;
        }

        @media (min-width: 640px) {

            .mySwiper .swiper-pagination-bullet,
            .productSwiper .swiper-pagination-bullet,
            .mySwiper2 .swiper-pagination-bullet {
                width: 8px !important;
                height: 8px !important;
            }
        }

        .mySwiper .swiper-pagination-bullet-active,
        .productSwiper .swiper-pagination-bullet-active,
        .mySwiper2 .swiper-pagination-bullet-active {
            background-color: #dc2626 !important;
            opacity: 1 !important;
            transform: scale(1.3);
        }

        .swiper-button-next,
        .swiper-button-prev {
            width: 28px !important;
            height: 28px !important;
            background-color: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(2px);
            border-radius: 50% !important;
            color: #dc2626 !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.3s ease !important;
            margin-top: -14px !important;
            z-index: 50;
        }

        .swiper-button-next::after,
        .swiper-button-prev::after {
            font-size: 12px !important;
            font-weight: 900 !important;
        }

        .swiper-button-prev {
            left: 4px !important;
        }

        .swiper-button-next {
            right: 4px !important;
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
                left: 16px !important;
            }

            .mySwiper .swiper-button-next {
                right: 16px !important;
            }
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background-color: #dc2626 !important;
            color: #ffffff !important;
            transform: scale(1.1) !important;
        }

        .product-title-fixed {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 2.5rem;
            line-height: 1.25;
        }

        @media (min-width: 640px) {
            .product-title-fixed {
                min-height: 2.8rem;
            }
        }
    </style>

    {{-- ★★★ HERO SECTION ★★★ --}}
    <div class="w-full pb-4 sm:pb-6 pt-2 md:pt-4">
        <div class="container mx-auto px-3 sm:px-4 max-w-7xl">
            <div
                class="relative w-full aspect-[16/10] md:aspect-[2.5/1] lg:aspect-[3/1] bg-gray-50 group rounded-xl sm:rounded-2xl overflow-hidden shadow-sm sm:shadow-xl border border-gray-100">
                <div class="swiper mySwiper w-full h-full absolute inset-0">
                    <div class="swiper-wrapper">
                        @if (isset($heroSlides) && $heroSlides->count() > 0)
                            @foreach ($heroSlides as $slide)
                                <div class="swiper-slide">
                                    <a href="{{ $slide->link_url ?? '/allproducts' }}" class="block w-full h-full">
                                        <img src="{{ Storage::url($slide->image_path) }}"
                                            class="w-full h-full object-contain object-center"
                                            alt="{{ $slide->title ?? 'Slide' }}"
                                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1600x600?text=Banner+Image';" />
                                    </a>
                                </div>
                            @endforeach
                        @else
                            @foreach (['th-1.png', 'th-2.png', 'th-3.png', 'th-4.png', 'th-5.png'] as $img)
                                <div class="swiper-slide">
                                    <a href="/allproducts" class="block w-full h-full">
                                        <img src="{{ asset('images/' . $img) }}"
                                            class="w-full h-full object-contain object-center" alt="Slide"
                                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1600x600?text=Welcome+Banner';" />
                                    </a>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    {{-- เปลี่ยนจาก hidden sm:flex เป็น flex ธรรมดาเพื่อให้แสดงบนมือถือ --}}
                    <div class="swiper-button-next flex"></div>
                    <div class="swiper-button-prev flex"></div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </div>

    

    {{-- ★★★ PRODUCTS SECTION ★★★ --}}
    <div class="w-full pb-10 sm:pb-16 pt-6 sm:pt-10">
        <div class="container mx-auto px-3 sm:px-4 mb-6 sm:mb-10 max-w-7xl">

            <div
                class="bg-white/90 backdrop-blur-sm rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 shadow-sm sm:shadow-xl border border-gray-100">

                {{-- Header Section --}}
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end mb-6 md:mb-8 gap-3">
                    <div>
                        <div
                            class="inline-block px-2 sm:px-3 py-0.5 sm:py-1 bg-red-100 text-red-600 rounded-md sm:rounded-lg text-[10px] sm:text-sm font-bold mb-1.5 sm:mb-2 shadow-sm uppercase tracking-wider">
                            {{ $settings['home_recommended_badge'] ?? 'Recommended' }}
                        </div>
                        <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-gray-800 tracking-tight">
                            {!! $settings['home_recommended_title'] ?? 'เมนูแนะนำ <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-red-500">ต้องลอง!</span>' !!}
                        </h2>
                    </div>
                    <a href="/allproducts"
                        class="group items-center gap-2 text-red-600 font-bold hover:text-red-700 hidden sm:flex transition-all hover:bg-red-50 px-4 py-2 rounded-full text-sm sm:text-base">
                        ดูทั้งหมด
                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>

                {{-- Product Slider --}}
                @if (isset($recommendedProducts) && count($recommendedProducts) > 0)
                    <div class="relative group">
                        <div class="swiper productSwiper !pb-10 sm:!pb-12 !px-1 sm:!px-2">
                            <div class="swiper-wrapper">
                                @foreach ($recommendedProducts as $product)
                                    @php
                                        $hasOptions = isset($product->options) && $product->options->count() > 0;
                                        $originalPrice = $hasOptions
                                            ? (float) $product->options->min('option_price')
                                            : (float) ($product->pd_sp_price ?? 0);
                                        $discountAmount = (float) ($product->pd_sp_discount ?? 0);
                                        $finalSellingPrice = max(0, $originalPrice - $discountAmount);
                                        $isOnSale = $discountAmount > 0;
                                        $displayImage = $product->cover_image_url;
                                        $productPromo = isset($promotions)
                                            ? $promotions->first(function ($promo) use ($product) {
                                                return $promo->rules->contains(function ($rule) use ($product) {
                                                    $pids = (array) ($rule->rules['product_id'] ?? []);
                                                    return in_array($product->pd_sp_id, array_map('intval', $pids));
                                                });
                                            })
                                            : null;
                                    @endphp

                                    <div class="swiper-slide h-auto">
                                        <div
                                            class="card bg-white border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col h-full rounded-xl sm:rounded-2xl overflow-hidden relative group/card">

                                            @if ($productPromo)
                                                <div class="absolute top-0 right-0 z-20">
                                                    <div
                                                        class="bg-gradient-to-l from-pink-600 to-red-500 text-white px-2 sm:px-3 py-0.5 sm:py-1 rounded-bl-lg sm:rounded-bl-xl shadow-md flex flex-col items-end">
                                                        <span
                                                            class="text-[8px] sm:text-[10px] font-black uppercase tracking-tighter"><i
                                                                class="fas fa-gift mr-0.5"></i> Free Gift</span>
                                                        <span
                                                            class="text-[7px] sm:text-[9px] font-bold opacity-90 leading-none truncate max-w-[80px] sm:max-w-[100px]">{{ $productPromo->name }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            <a href="{{ route('product.show', $product->pd_sp_id) }}"
                                                class="relative aspect-square overflow-hidden bg-gray-50 block">
                                                @if ($product->pd_sp_stock <= 0)
                                                    <div
                                                        class="absolute inset-0 flex items-center justify-center z-10 bg-white/60 backdrop-blur-[2px]">
                                                        <span
                                                            class="bg-gray-800 text-white text-[10px] sm:text-xs px-3 sm:px-4 py-1 sm:py-1.5 rounded-full font-bold shadow-md uppercase tracking-wider">สินค้าหมด</span>
                                                    </div>
                                                @endif
                                                <img src="{{ Str::startsWith($displayImage, 'http') ? $displayImage : asset('storage/' . $displayImage) }}"
                                                    alt="{{ $product->pd_sp_name }}"
                                                    class="w-full h-full object-cover group-hover/card:scale-105 transition duration-500 {{ $product->pd_sp_stock <= 0 ? 'opacity-60 grayscale' : '' }}"
                                                    onerror="this.onerror=null;this.src='https://via.placeholder.com/400x400?text=No+Image';" />

                                                @if ($isOnSale)
                                                    <div
                                                        class="absolute top-2 left-2 bg-red-600 text-white px-1.5 sm:px-2 py-0.5 sm:py-1 rounded text-[9px] sm:text-[10px] font-black shadow-sm uppercase tracking-wide border border-red-500">
                                                        ลด {{ number_format($discountAmount, 2) }}.-
                                                    </div>
                                                @endif
                                            </a>

                                            <div class="p-3 sm:p-4 flex-1 flex flex-col">
                                                <h2 class="text-xs sm:text-sm md:text-base font-bold text-gray-800 leading-tight product-title-fixed hover:text-red-600 transition cursor-pointer mb-1.5 sm:mb-2"
                                                    onclick="window.location='{{ route('product.show', $product->pd_sp_id) }}'">
                                                    {{ $product->pd_sp_name }}
                                                </h2>

                                                <div class="flex items-center justify-between mb-2">
                                                    <p
                                                        class="text-[9px] sm:text-[10px] font-bold {{ $product->pd_sp_stock > 0 ? 'text-emerald-500' : 'text-red-500' }} flex items-center gap-1">
                                                        <span class="relative flex h-1.5 w-1.5 sm:h-2 sm:w-2">
                                                            <span
                                                                class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $product->pd_sp_stock > 0 ? 'bg-emerald-400' : 'hidden' }}"></span>
                                                            <span
                                                                class="relative inline-flex rounded-full h-1.5 w-1.5 sm:h-2 sm:w-2 {{ $product->pd_sp_stock > 0 ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                                        </span>
                                                        {{ $product->pd_sp_stock > 0 ? 'มีสินค้า' : 'หมด' }}
                                                    </p>
                                                    <p
                                                        class="text-[9px] sm:text-[10px] font-medium text-gray-400 bg-gray-50 px-1.5 py-0.5 rounded">
                                                        ขายแล้ว {{ number_format($product->pd_sp_sold ?? 0) }}
                                                    </p>
                                                </div>

                                                <div class="mt-auto pt-2 sm:pt-3 border-t border-gray-100/80">
                                                    <div
                                                        class="flex flex-wrap items-baseline justify-between w-full mb-2.5 sm:mb-3 gap-x-1 gap-y-0.5">
                                                        <div class="flex items-baseline gap-1">
                                                            @if ($hasOptions)
                                                                <span
                                                                    class="text-[9px] sm:text-[10px] text-gray-400 font-bold">เริ่ม</span>
                                                            @endif
                                                            <span
                                                                class="text-base sm:text-lg font-black text-red-600 tracking-tight leading-none">฿{{ number_format($finalSellingPrice, 2) }}</span>
                                                        </div>
                                                        @if ($isOnSale)
                                                            <span
                                                                class="text-[9px] sm:text-[11px] font-bold text-gray-400 line-through decoration-gray-300">฿{{ number_format($originalPrice, 2) }}</span>
                                                        @endif
                                                    </div>

                                                    <button type="button"
                                                        @if ($hasOptions) onclick="window.location='{{ route('product.show', $product->pd_sp_id) }}'" @else onclick="addToCartQuick(this, '{{ route('cart.add', ['id' => $product->pd_sp_id]) }}')" @endif
                                                        class="w-full rounded-lg sm:rounded-xl font-bold text-[11px] sm:text-xs transition-all flex items-center justify-center gap-1.5 h-9 sm:h-10 min-h-[36px] sm:min-h-[40px]
                                                        {{ $product->pd_sp_stock > 0
                                                            ? 'bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-red-100 hover:border-red-600 shadow-sm active:scale-95'
                                                            : 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200' }}"
                                                        {{ $product->pd_sp_stock <= 0 ? 'disabled' : '' }}>

                                                        @if ($product->pd_sp_stock > 0)
                                                            @if ($hasOptions)
                                                                <i class="fas fa-list-ul"></i> เลือกตัวเลือก
                                                            @else
                                                                <i class="fas fa-cart-plus text-sm"></i> ใส่ตะกร้า
                                                            @endif
                                                        @else
                                                            <i class="fas fa-ban opacity-70"></i> สินค้าหมด
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
                        {{-- เปลี่ยนจาก hidden sm:flex เป็น flex ธรรมดา และเพิ่ม class product-next, product-prev --}}
                        <div
                            class="swiper-button-next product-next flex !w-8 !h-8 md:!w-10 md:!h-10 after:!text-xs md:after:!text-sm">
                        </div>
                        <div
                            class="swiper-button-prev product-prev flex !w-8 !h-8 md:!w-10 md:!h-10 after:!text-xs md:after:!text-sm">
                        </div>
                    </div>
                @else
                    <div
                        class="col-span-full flex flex-col items-center justify-center py-16 sm:py-20 bg-white rounded-2xl border-2 border-dashed border-gray-100">
                        <div class="bg-gray-50 p-4 rounded-full mb-3">
                            <i class="fas fa-box-open text-4xl text-gray-300"></i>
                        </div>
                        <p class="text-sm sm:text-base text-gray-500 font-medium">ไม่พบสินค้าแนะนำในขณะนี้</p>
                    </div>
                @endif

                <div class="mt-4 sm:mt-5 text-center sm:hidden">
                    <a href="/allproducts"
                        class="block w-full py-2.5 bg-white border border-red-600 text-red-600 rounded-xl font-bold text-sm shadow-sm hover:bg-red-50 transition-colors">
                        ดูสินค้าทั้งหมด <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                {{-- ★★★ REVIEW IMAGES SECTION ★★★ --}}
                @if (isset($reviewImages) && count($reviewImages) > 0)
                    <div class="mt-12 sm:mt-16 pt-8 border-t border-gray-100" x-data="{
                        isReviewModalOpen: false,
                        activeReviewIndex: 0,
                        reviewImages: @js($reviewImages->map(fn($img) => Str::startsWith($img->image_url, 'http') ? $img->image_url : asset('storage/' . ltrim($img->image_url, '/')))),
                        openReviewModal(index) {
                            this.activeReviewIndex = index;
                            this.isReviewModalOpen = true;
                            document.body.style.overflow = 'hidden';
                        },
                        closeReviewModal() {
                            this.isReviewModalOpen = false;
                            document.body.style.overflow = 'auto';
                        },
                        nextReview() {
                            this.activeReviewIndex = (this.activeReviewIndex + 1) % this.reviewImages.length;
                        },
                        prevReview() {
                            this.activeReviewIndex = (this.activeReviewIndex - 1 + this.reviewImages.length) % this.reviewImages.length;
                        }
                    }"
                        @keydown.escape.window="closeReviewModal()"
                        @keydown.right.window="if(isReviewModalOpen) nextReview()"
                        @keydown.left.window="if(isReviewModalOpen) prevReview()">

                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end mb-6 md:mb-8 gap-3">
                            <div>
                                <div
                                    class="inline-block px-2 sm:px-3 py-0.5 sm:py-1 bg-amber-100 text-amber-600 rounded-md sm:rounded-lg text-[10px] sm:text-sm font-bold mb-1.5 sm:mb-2 shadow-sm uppercase tracking-wider">
                                    Customer Reviews
                                </div>
                                <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-gray-800 tracking-tight">
                                    รีวิวจาก <span
                                        class="text-transparent bg-clip-text bg-gradient-to-r from-amber-600 to-amber-500">ลูกค้าของเรา</span>
                                </h2>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
                            @foreach ($reviewImages as $index => $img)
                                <div class="aspect-square rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 group cursor-pointer"
                                    @click="openReviewModal({{ $index }})">
                                    <img src="{{ Str::startsWith($img->image_url, 'http') ? $img->image_url : asset('storage/' . ltrim($img->image_url, '/')) }}"
                                        alt="Review"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                </div>
                            @endforeach
                        </div>

                        {{-- Fullscreen Modal for Review Images (Alpine.js) --}}
                        <template x-teleport="body">
                            <div x-show="isReviewModalOpen" x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/95 backdrop-blur-md"
                                @click="closeReviewModal()" style="display: none;">

                                <button @click.stop="closeReviewModal()"
                                    class="absolute top-6 right-6 text-white/70 hover:text-white transition-colors duration-200 z-[110]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <div class="relative w-full max-w-5xl aspect-square flex items-center justify-center"
                                    @click.stop>
                                    {{-- Navigation --}}
                                    <button x-show="reviewImages.length > 1" @click="prevReview()"
                                        class="absolute left-0 lg:-left-20 z-10 p-3 rounded-full bg-white/10 text-white hover:bg-white/20 transition-all duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>

                                    <img :src="reviewImages[activeReviewIndex]"
                                        class="max-w-full max-h-[85vh] object-contain shadow-2xl rounded-lg">

                                    <button x-show="reviewImages.length > 1" @click="nextReview()"
                                        class="absolute right-0 lg:-right-20 z-10 p-3 rounded-full bg-white/10 text-white hover:bg-white/20 transition-all duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>

                                    {{-- Counter --}}
                                    <div
                                        class="absolute -bottom-10 left-1/2 -translate-x-1/2 text-white/60 font-medium tracking-widest text-sm">
                                        <span x-text="activeReviewIndex + 1"></span> / <span
                                            x-text="reviewImages.length"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ★★★ สไลด์ตัวที่ 2 ★★★ --}}
    <div class="w-full pt-6 sm:pt-10 pb-8 sm:pb-12">
        <div class="container mx-auto px-3 sm:px-4 max-w-7xl">
            <div
                class="swiper mySwiper2 w-full max-w-[1000px] mx-auto rounded-xl sm:rounded-2xl shadow-sm sm:shadow-lg overflow-hidden relative border border-gray-100">
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
                {{-- เปลี่ยนจาก hidden sm:flex เป็น flex ธรรมดา --}}
                <div class="swiper-button-next flex"></div>
                <div class="swiper-button-prev flex"></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>

    {{-- SERVICE BAR --}}
    <div class="bg-white/95 backdrop-blur-sm border-t border-gray-100 py-8 sm:py-12 relative">
        <div class="container mx-auto px-4 max-w-7xl">
            <div
                class="grid grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8 md:gap-12 text-center divide-x-0 md:divide-x divide-gray-100">
                @if (isset($services) && $services->count() > 0)
                    @foreach ($services as $service)
                        <div class="flex flex-col items-center gap-3 sm:gap-4 group cursor-default p-2">
                            <div
                                class="p-3 sm:p-4 bg-white shadow-sm sm:shadow-md rounded-xl sm:rounded-2xl group-hover:bg-red-500 group-hover:text-white transition-all duration-300 transform group-hover:-translate-y-1 sm:group-hover:-translate-y-2 ring-1 ring-gray-100">
                                @if (str_contains($service->icon, '<svg'))
                                    <div
                                        class="w-6 h-6 sm:w-8 sm:h-8 group-hover:text-white text-red-500 transition-colors">
                                        {!! $service->icon !!}
                                    </div>
                                @else
                                    <i
                                        class="{{ $service->icon }} text-2xl sm:text-3xl text-red-500 group-hover:text-white transition-colors"></i>
                                @endif
                            </div>
                            <span
                                class="text-xs sm:text-sm md:text-base font-bold text-gray-700 group-hover:text-red-600 transition">{{ $service->title }}</span>
                        </div>
                    @endforeach
                @else
                    @php $serviceBarItems = [['icon' => 'fas fa-utensils', 'text' => 'สูตรเด็ดต้นตำรับ'], ['icon' => 'fas fa-shipping-fast', 'text' => 'ส่งไว ทันใจ'], ['icon' => 'fas fa-shield-alt', 'text' => 'ชำระเงินปลอดภัย'], ['icon' => 'fas fa-heart', 'text' => 'ทำด้วยใจทุกขั้นตอน']]; @endphp
                    @foreach ($serviceBarItems as $item)
                        <div class="flex flex-col items-center gap-3 sm:gap-4 group cursor-default p-2">
                            <div
                                class="p-3 sm:p-4 bg-white shadow-sm sm:shadow-md rounded-xl sm:rounded-2xl group-hover:bg-red-500 group-hover:text-white transition-all duration-300 transform group-hover:-translate-y-1 sm:group-hover:-translate-y-2 ring-1 ring-gray-100">
                                <i
                                    class="{{ $item['icon'] }} text-2xl sm:text-3xl text-red-500 group-hover:text-white transition-colors"></i>
                            </div>
                            <span
                                class="text-xs sm:text-sm md:text-base font-bold text-gray-700 group-hover:text-red-600 transition">{{ $item['text'] }}</span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- ★★★ 6 REASONS SECTION ★★★ --}}
    <div class="w-full py-12 sm:py-20 bg-gradient-to-br from-red-800 to-red-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10"
            style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>
        <div class="container mx-auto px-4 max-w-7xl relative z-10">
            <h2
                class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-extrabold text-center text-white mb-10 sm:mb-16 drop-shadow-lg">
                {{ $settings['home_reasons_title'] ?? '6 เหตุผลทำไมต้องเลือกเรา' }}
            </h2>
            <div
                class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10 sm:gap-y-12 px-2 sm:px-10 lg:px-20">
                @if (isset($reasons) && $reasons->count() > 0)
                    @foreach ($reasons as $reason)
                        <div class="flex flex-col items-center text-center group">
                            <div
                                class="mb-4 sm:mb-6 text-white transition-all duration-300 group-hover:scale-110 group-hover:bg-white/20 bg-white/10 p-4 sm:p-5 rounded-2xl backdrop-blur-sm shadow-inner">
                                @if (str_contains($reason->icon, '<svg'))
                                    <div class="w-8 h-8 sm:w-10 sm:h-10">{!! $reason->icon !!}</div>
                                @else
                                    <i class="{{ $reason->icon }} text-3xl sm:text-4xl"></i>
                                @endif
                            </div>
                            <h3 class="text-lg sm:text-xl font-bold text-white mb-2 sm:mb-3 drop-shadow-md">
                                {{ $reason->title }}</h3>
                            <p
                                class="text-white/80 text-xs sm:text-sm md:text-base leading-relaxed max-w-[250px] sm:max-w-xs">
                                {{ $reason->description }}
                            </p>
                        </div>
                    @endforeach
                @else
                    <div class="flex flex-col items-center text-center group">
                        <div
                            class="mb-4 sm:mb-6 bg-white/10 p-4 sm:p-5 rounded-2xl text-white transition-all duration-300 group-hover:scale-110 group-hover:bg-white/20">
                            <i class="fas fa-check-circle text-3xl sm:text-4xl"></i>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-white mb-2 sm:mb-3">เรารู้จริง</h3>
                        <p class="text-white/80 text-xs sm:text-sm leading-relaxed max-w-[250px] sm:max-w-xs">
                            คัดสรรแต่วัตถุดิบคุณภาพดีที่สุดเพื่อคุณ</p>
                    </div>
                @endif
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

            // ปรับ Breakpoints ให้สมูทขึ้นสำหรับ Product Slider
            var swiperProducts = new Swiper(".productSwiper", {
                loop: true,
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true
                },
                pagination: {
                    el: ".productSwiper .swiper-pagination",
                    clickable: true,
                },
                navigation: {
                    // เปลี่ยนชื่อคลาสเป้าหมายเพื่อป้องกันไม่ให้ชนกับ Swiper ตัวอื่น
                    nextEl: ".product-next",
                    prevEl: ".product-prev",
                },
                breakpoints: {
                    0: {
                        slidesPerView: 2,
                        spaceBetween: 10,
                    },
                    480: {
                        slidesPerView: 2,
                        spaceBetween: 15,
                    },
                    640: {
                        slidesPerView: 3,
                        spaceBetween: 15,
                    },
                    1024: {
                        slidesPerView: 4,
                        spaceBetween: 20,
                    },
                    1280: {
                        slidesPerView: 5,
                        spaceBetween: 24,
                    }
                },
            });

            var swiper2 = new Swiper(".mySwiper2", {
                slidesPerView: 1,
                spaceBetween: 15,
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
                breakpoints: {
                    640: {
                        spaceBetween: 20
                    }
                }
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
                        showNotification('success', 'เพิ่มเรียบร้อย!', '', 1500);
                        setTimeout(() => {
                            Livewire.dispatch('cartUpdated');
                        }, 50);
                    } else {
                        showNotification('error', 'แจ้งเตือน', data.message || 'เพิ่มสินค้าไม่ได้');
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
