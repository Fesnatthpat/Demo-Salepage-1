{{-- resources/views/allproducts.blade.php --}}
@extends('layout')

@section('title', 'สินค้าทั้งหมด | Salepage Demo')

@section('content')

    {{-- ★★★ รวม CSS ปรับแต่งสไลเดอร์และ Product Card ★★★ --}}
    <style>
        /* --- 1. Swiper Styles --- */
        .mySwiper .swiper-pagination-bullet {
            background-color: #ffffff !important;
            opacity: 0.5 !important;
            width: 6px !important;
            height: 6px !important;
            transition: all 0.3s ease;
        }

        @media (min-width: 640px) {
            .mySwiper .swiper-pagination-bullet {
                width: 8px !important;
                height: 8px !important;
            }
        }

        .mySwiper .swiper-pagination-bullet-active,
        .mySwiper .swiper-pagination-bullet-active-main {
            background-color: #ffffff !important;
            opacity: 1 !important;
            transform: scale(1.3);
        }

        .mySwiper .swiper-button-next,
        .mySwiper .swiper-button-prev {
            width: 28px !important;
            height: 28px !important;
            background-color: rgba(255, 255, 255, 0.9) !important;
            border-radius: 50% !important;
            color: #dc2626 !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2) !important;
            transition: all 0.3s ease !important;
            margin-top: -14px !important;
        }

        @media (min-width: 640px) {
            .mySwiper .swiper-button-next,
            .mySwiper .swiper-button-prev {
                width: 32px !important;
                height: 32px !important;
                margin-top: -16px !important;
            }
        }

        .mySwiper .swiper-button-next:hover,
        .mySwiper .swiper-button-prev:hover {
            background-color: #dc2626 !important;
            color: #ffffff !important;
            transform: scale(1.1) !important;
        }

        .mySwiper .swiper-button-next::after,
        .mySwiper .swiper-button-prev::after {
            font-size: 12px !important;
            font-weight: 900 !important;
        }

        @media (min-width: 640px) {
            .mySwiper .swiper-button-next::after,
            .mySwiper .swiper-button-prev::after {
                font-size: 14px !important;
            }
        }

        /* Category Slider Arrows - ปรับขนาดบนมือถือให้เล็กลงเพื่อไม่ให้บังไอคอน (เอาการซ่อนออก) */
        @media (max-width: 767px) {
            .categorySwiper .swiper-button-next,
            .categorySwiper .swiper-button-prev {
                width: 24px !important;
                height: 24px !important;
                margin-top: -12px !important;
            }
            .categorySwiper .swiper-button-next::after,
            .categorySwiper .swiper-button-prev::after {
                font-size: 10px !important;
            }
        }

        .categorySwiper .swiper-button-next,
        .categorySwiper .swiper-button-prev {
            width: 32px !important;
            height: 32px !important;
            background-color: #ffffff !important;
            border-radius: 50% !important;
            color: #dc2626 !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
            transition: all 0.3s ease !important;
            top: 50% !important;
            margin-top: -16px !important;
            z-index: 10 !important;
        }

        .categorySwiper .swiper-button-next::after,
        .categorySwiper .swiper-button-prev::after {
            font-size: 12px !important;
            font-weight: 900 !important;
        }

        .categorySwiper .swiper-button-prev {
            left: 4px !important;
        }

        .categorySwiper .swiper-button-next {
            right: 4px !important;
        }

        .categorySwiper .swiper-button-disabled {
            opacity: 0.3 !important;
            cursor: not-allowed !important;
        }

        /* --- 2. Custom Product Card Styles (Mobile Optimization) --- */
        .product-title-fixed {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 2.5rem; /* ความสูงเผื่อ 2 บรรทัดสำหรับฟอนต์เล็ก */
            line-height: 1.25;
        }

        @media (min-width: 640px) {
            .product-title-fixed {
                min-height: 2.8rem;
            }
        }
    </style>

    {{-- พื้นหลัง --}}
    <div class="min-h-screen py-4 md:py-8 bg-cover bg-center bg-no-repeat bg-fixed bg-gray-50/50"
        style="background-image: url('{{ asset('') }}');">

        <div class="container mx-auto px-3 sm:px-4 md:px-6 max-w-7xl">

            <div class="flex flex-col gap-5 md:gap-8">

                <main class="w-full">

                    {{-- ★★★ HERO SECTION (สไลด์หลัก) ★★★ --}}
                    <div class="w-full pb-4 sm:pb-6 pt-2 sm:pt-4 bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100">
                        <div class="px-2 sm:px-4">
                            <div class="relative w-full aspect-[16/10] md:aspect-[2.5/1] lg:aspect-[3/1] bg-gray-50 group rounded-xl sm:rounded-2xl overflow-hidden shadow-inner border border-gray-100">
                                <div class="swiper mySwiper w-full h-full absolute inset-0">
                                    <div class="swiper-wrapper">
                                        @if (isset($heroSlides) && $heroSlides->count() > 0)
                                            @foreach ($heroSlides as $slide)
                                                <div class="swiper-slide">
                                                    <a href="{{ $slide->link_url ?? '/allproducts' }}" class="block w-full h-full bg-gray-50">
                                                        <img src="{{ Storage::url($slide->image_path) }}"
                                                            class="w-full h-full object-contain object-center"
                                                            alt="{{ $slide->title ?? 'Slide' }}"
                                                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1600x600?text=Banner+Image';" />
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            {{-- Fallback Images --}}
                                            @foreach (['th-1.png', 'th-2.png', 'th-3.png', 'th-4.png', 'th-5.png'] as $img)
                                                <div class="swiper-slide">
                                                    <a href="/allproducts" class="block w-full h-full bg-gray-50">
                                                        <img src="{{ asset('images/' . $img) }}"
                                                            class="w-full h-full object-contain object-center"
                                                            alt="Slide"
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

                    {{-- ★★★ CATEGORY MENU SECTION ★★★ --}}
                    <div class="w-full py-3 sm:py-4 rounded-xl mt-4 mb-5 shadow-sm bg-red-600 overflow-hidden relative group select-none">
                        <div class="container mx-auto px-1 sm:px-2 relative">
                            <div class="swiper categorySwiper w-full pb-1 sm:pb-2">
                                <div class="swiper-wrapper items-start">
                                    @if (isset($dbCategories) && $dbCategories->count() > 0)
                                        @foreach ($dbCategories as $menu)
                                            <div class="swiper-slide !h-auto">
                                                <a href="/allproducts?category={{ $menu->name }}"
                                                    class="flex flex-col items-center group w-full transition-transform duration-300 active:scale-95 px-1 sm:px-2 md:px-4">
                                                    <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-16 md:h-16 bg-gray-50 rounded-full flex items-center justify-center p-1.5 sm:p-2 mb-1.5 sm:mb-2 shadow-sm transition-colors overflow-hidden border border-red-500/30">
                                                        @if ($menu->image_path)
                                                            <img src="{{ Storage::url($menu->image_path) }}"
                                                                alt="{{ $menu->name }}"
                                                                class="w-full h-full object-contain"
                                                                onerror="this.onerror=null;this.src='https://via.placeholder.com/150x150/fca5a5/ffffff?text=IMG';" />
                                                        @else
                                                            <i class="{{ $menu->icon ?? 'fas fa-th-large' }} text-red-600 text-lg sm:text-xl md:text-2xl"></i>
                                                        @endif
                                                    </div>
                                                    <span class="text-[9px] sm:text-[10px] md:text-xs font-bold text-white text-center leading-tight select-none">
                                                        {!! nl2br(e($menu->name)) !!}
                                                    </span>
                                                </a>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-white text-xs w-full text-center py-4">ยังไม่มีหมวดหมู่</div>
                                    @endif
                                </div>
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                        </div>
                    </div>

                    {{-- ★★★ SORTING BAR ★★★ --}}
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-4 sm:p-4 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 mb-5 gap-3 sm:gap-4">
                        <div>
                            <h2 class="text-gray-800 font-extrabold text-base sm:text-lg md:text-xl flex items-center gap-2">
                                สินค้าทั้งหมด
                                <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded-md text-[10px] sm:text-xs font-bold border border-gray-200">
                                    {{ $products->total() }} รายการ
                                </span>
                            </h2>
                        </div>

                        <form id="sortForm" action="{{ route('allproducts') }}" method="GET" class="w-full sm:w-auto">
                            @if (request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            @if (request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif

                            <div class="flex items-center gap-2 w-full">
                                <label class="text-xs sm:text-sm font-bold text-gray-500 whitespace-nowrap shrink-0"><i class="fas fa-sort-amount-down-alt mr-1"></i> เรียงตาม:</label>
                                <select name="sort" onchange="document.getElementById('sortForm').submit();"
                                    class="w-full sm:w-48 bg-gray-50 border border-gray-200 text-gray-700 text-xs sm:text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all outline-none">
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>ล่าสุด</option>
                                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>ยอดนิยม</option>
                                    <option value="bestseller" {{ request('sort') == 'bestseller' ? 'selected' : '' }}>ขายดี</option>
                                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>ราคา: ต่ำ - สูง</option>
                                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>ราคา: สูง - ต่ำ</option>
                                </select>
                            </div>
                        </form>
                    </div>

                    {{-- ★★★ PRODUCT GRID (Optimized for Mobile) ★★★ --}}
                    @if ($products->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4 md:gap-6">
                            @foreach ($products as $product)
                                @if ($product)
                                    @php
                                        // 1. Logic ราคาและ Options
                                        $hasOptions = isset($product->options) && $product->options->count() > 0;
                                        if ($hasOptions) {
                                            $originalPrice = (float) $product->options->min('option_price');
                                        } else {
                                            $originalPrice = (float) ($product->pd_sp_price ?? 0);
                                        }
                                        $discountAmount = (float) ($product->pd_sp_discount ?? 0);
                                        $finalSellingPrice = max(0, $originalPrice - $discountAmount);
                                        $isOnSale = $discountAmount > 0;

                                        // 2. รูปภาพ
                                        $primaryImage = $product->images->sortBy('img_sort')->first();
                                        $imagePath = $primaryImage
                                            ? $primaryImage->img_path
                                            : 'https://via.placeholder.com/400x500.png?text=No+Image';
                                    @endphp

                                    <div class="relative bg-white border border-gray-100 shadow-sm hover:shadow-lg hover:border-red-100 transition-all duration-300 rounded-xl sm:rounded-2xl overflow-hidden flex flex-col h-full group">
                                        
                                        <a href="{{ route('product.show', $product->pd_sp_id) }}" class="block relative aspect-square overflow-hidden bg-gray-50/50">
                                            @if (($product->pd_sp_stock ?? 0) <= 0)
                                                <div class="absolute inset-0 flex items-center justify-center z-10 bg-white/60 backdrop-blur-[2px]">
                                                    <span class="bg-gray-800 text-white text-[10px] sm:text-xs px-3 sm:px-4 py-1 sm:py-1.5 rounded-full font-bold uppercase tracking-wider shadow-md">สินค้าหมด</span>
                                                </div>
                                            @endif
                                            <img src="{{ Str::startsWith($imagePath, 'http') ? $imagePath : asset('storage/' . $imagePath) }}"
                                                alt="{{ $product->pd_sp_name }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition duration-700 ease-in-out {{ ($product->pd_sp_stock ?? 0) <= 0 ? 'grayscale opacity-60' : '' }}"
                                                onerror="this.onerror=null;this.src='https://via.placeholder.com/400x500.png?text=No+Image';" />

                                            {{-- Badges --}}
                                            <div class="absolute top-2 left-2 flex flex-col gap-1.5 items-start z-10">
                                                @if ($isOnSale)
                                                    <div class="bg-red-600 text-white px-1.5 py-0.5 sm:px-2 sm:py-1 rounded text-[9px] sm:text-[10px] font-black shadow-sm uppercase tracking-wide border border-red-500">
                                                        ลด ฿{{ number_format($discountAmount, 2) }}
                                                    </div>
                                                @endif
                                                @if ($product->gifts_per_item)
                                                    <div class="bg-pink-500 text-white px-1.5 py-0.5 sm:px-2 sm:py-1 rounded text-[9px] sm:text-[10px] font-black shadow-sm flex items-center gap-1 uppercase tracking-wide border border-pink-400">
                                                        <i class="fas fa-gift text-[8px] sm:text-[9px]"></i> แถม {{ $product->gifts_per_item }}
                                                    </div>
                                                @endif
                                            </div>
                                        </a>

                                        <div class="p-3 sm:p-4 flex flex-col flex-1">
                                            <h2 class="text-xs sm:text-sm font-bold text-gray-800 leading-tight product-title-fixed mb-1 sm:mb-2 group-hover:text-red-600 transition-colors">
                                                <a href="{{ route('product.show', $product->pd_sp_id) }}">
                                                    {{ $product->pd_sp_name ?? 'Product Name' }}
                                                </a>
                                            </h2>

                                            {{-- สถานะสต็อกและยอดขาย --}}
                                            <div class="flex items-center justify-between mb-2">
                                                <p class="text-[9px] sm:text-[10px] font-bold {{ ($product->pd_sp_stock ?? 0) > 0 ? 'text-emerald-500' : 'text-red-500' }} flex items-center gap-1">
                                                    <span class="relative flex h-1.5 w-1.5 sm:h-2 sm:w-2">
                                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ ($product->pd_sp_stock ?? 0) > 0 ? 'bg-emerald-400' : 'hidden' }}"></span>
                                                      <span class="relative inline-flex rounded-full h-1.5 w-1.5 sm:h-2 sm:w-2 {{ ($product->pd_sp_stock ?? 0) > 0 ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                                    </span>
                                                    {{ ($product->pd_sp_stock ?? 0) > 0 ? 'มีสินค้า' : 'หมด' }}
                                                </p>
                                                <p class="text-[9px] sm:text-[10px] font-medium text-gray-400 bg-gray-50 px-1.5 py-0.5 rounded">
                                                    ขายแล้ว {{ number_format($product->pd_sp_sold ?? 0) }}
                                                </p>
                                            </div>

                                            <div class="mt-auto pt-2 sm:pt-3 border-t border-gray-100/80">
                                                {{-- ราคาสินค้า --}}
                                                <div class="flex flex-wrap items-baseline justify-between w-full mb-2.5 sm:mb-3 gap-x-1 gap-y-0.5">
                                                    <div class="flex items-baseline gap-1">
                                                        @if ($hasOptions)
                                                            <span class="text-[9px] sm:text-[10px] text-gray-400 font-bold">เริ่ม</span>
                                                        @endif
                                                        <span class="text-base sm:text-lg font-black text-red-600 tracking-tight leading-none">฿{{ number_format($finalSellingPrice, 2) }}</span>
                                                    </div>

                                                    @if ($isOnSale)
                                                        <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 line-through decoration-gray-300">฿{{ number_format($originalPrice, 2) }}</span>
                                                    @endif
                                                </div>

                                                {{-- 🛠️ จุดที่แก้ไข: แยกปุ่ม "เลือกตัวเลือก" ออกจากการเป็น Form เพื่อป้องกันการชนกันของ GET/POST --}}
                                                @if (($product->pd_sp_stock ?? 0) <= 0)
                                                    {{-- กรณีสินค้าหมด: ปุ่มกดไม่ได้ --}}
                                                    <button disabled
                                                        class="w-full rounded-lg sm:rounded-xl font-bold text-[11px] sm:text-xs transition-all flex items-center justify-center gap-1.5 h-9 sm:h-10 min-h-[36px] sm:min-h-[40px] bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200">
                                                        <i class="fas fa-ban opacity-70"></i> สินค้าหมด
                                                    </button>

                                                @elseif ($hasOptions)
                                                    {{-- กรณีมีตัวเลือก: เป็นแท็ก <a> เพื่อ Redirect ไปหน้าสินค้าโดยตรง (GET) --}}
                                                    <a href="{{ route('product.show', $product->pd_sp_id) }}" 
                                                        class="w-full rounded-lg sm:rounded-xl font-bold text-[11px] sm:text-xs transition-all flex items-center justify-center gap-1.5 h-9 sm:h-10 min-h-[36px] sm:min-h-[40px] bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-red-100 hover:border-red-600 shadow-sm hover:shadow-red-500/30">
                                                        <i class="fas fa-list-ul"></i> เลือกตัวเลือก
                                                    </a>

                                                @else
                                                    {{-- กรณีไม่มีตัวเลือก: ใช้ Form ส่ง AJAX แบบ POST --}}
                                                    <form class="add-to-cart-form-listing w-full" data-action="{{ route('cart.add', ['id' => $product->pd_sp_id]) }}">
                                                        <input type="hidden" name="quantity" value="1">
                                                        <button type="submit"
                                                            class="w-full rounded-lg sm:rounded-xl font-bold text-[11px] sm:text-xs transition-all flex items-center justify-center gap-1.5 h-9 sm:h-10 min-h-[36px] sm:min-h-[40px] bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-red-100 hover:border-red-600 shadow-sm hover:shadow-red-500/30">
                                                            <i class="fas fa-cart-plus text-sm"></i> เพิ่มลงตะกร้า
                                                        </button>
                                                    </form>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="mt-8 sm:mt-12 flex justify-center">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-16 sm:py-24 bg-white rounded-2xl border-2 border-dashed border-gray-200 text-center">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-box-open text-4xl text-gray-300"></i>
                            </div>
                            <h3 class="text-lg sm:text-xl font-bold text-gray-700 mb-2">ไม่พบสินค้าที่คุณค้นหา</h3>
                            <p class="text-xs sm:text-sm text-gray-400 mb-6">ลองเปลี่ยนคำค้นหา หรือเลือกดูหมวดหมู่สินค้าอื่นๆ</p>
                            <a href="{{ route('allproducts') }}" class="px-6 py-2.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white font-bold rounded-xl transition-colors border border-red-100 hover:border-red-600 shadow-sm">
                                ล้างคำค้นหาและดูสินค้าทั้งหมด
                            </a>
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </div>

    {{-- Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Banner
            new Swiper(".mySwiper", {
                slidesPerView: 1,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true
                },
                navigation: {
                    nextEl: ".mySwiper .swiper-button-next",
                    prevEl: ".mySwiper .swiper-button-prev"
                },
            });

            // ★★★ Category Responsive Breakpoints ★★★
            new Swiper(".categorySwiper", {
                loop: true,
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                },
                navigation: {
                    nextEl: ".categorySwiper .swiper-button-next",
                    prevEl: ".categorySwiper .swiper-button-prev"
                },
                breakpoints: {
                    0: { slidesPerView: 3.5, spaceBetween: 8 },
                    480: { slidesPerView: 4.5, spaceBetween: 10 },
                    640: { slidesPerView: 6, spaceBetween: 12 },
                    1024: { slidesPerView: 8, spaceBetween: 16 },
                },
            });

            // Cart Logic 
            const forms = document.querySelectorAll('.add-to-cart-form-listing');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    // 🛠️ จุดที่แก้ไข: เอาเงื่อนไขที่คอยจับคำว่า "เลือกตัวเลือก" ออกไปเลย 
                    // เพราะเราแยกปุ่มนั้นไปเป็น <a> แท็กแล้ว
                    e.preventDefault();
                    
                    const currentForm = this;
                    const submitBtn = currentForm.querySelector('button[type="submit"]');
                    const actionUrl = currentForm.getAttribute('data-action');
                    const quantity = currentForm.querySelector('[name="quantity"]').value;
                    const originalBtnContent = submitBtn.innerHTML;

                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังเพิ่ม...';

                    const formData = new FormData();
                    formData.append('quantity', quantity);

                    fetch(actionUrl, {
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
                                if (typeof window.flyToCart === 'function') window.flyToCart(submitBtn);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'เพิ่มลงตะกร้าแล้ว',
                                    showConfirmButton: false,
                                    timer: 1000
                                });
                                setTimeout(() => {
                                    Livewire.dispatch('cartUpdated');
                                }, 50);
                            } else {
                                throw new Error(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: error.message || 'ไม่สามารถเพิ่มสินค้าได้',
                                confirmButtonColor: '#dc2626'
                            });
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnContent;
                        });
                });
            });
        });
    </script>
@endsection