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
            width: 8px !important;
            height: 8px !important;
            transition: all 0.3s ease;
        }

        .mySwiper .swiper-pagination-bullet-active,
        .mySwiper .swiper-pagination-bullet-active-main {
            background-color: #ffffff !important;
            opacity: 1 !important;
            transform: scale(1.3);
        }

        .mySwiper .swiper-button-next,
        .mySwiper .swiper-button-prev {
            width: 32px !important;
            height: 32px !important;
            background-color: rgba(255, 255, 255, 0.9) !important;
            border-radius: 50% !important;
            color: #dc2626 !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2) !important;
            transition: all 0.3s ease !important;
            margin-top: -16px !important;
        }

        .mySwiper .swiper-button-next:hover,
        .mySwiper .swiper-button-prev:hover {
            background-color: #dc2626 !important;
            color: #ffffff !important;
            transform: scale(1.1) !important;
        }

        .mySwiper .swiper-button-next::after,
        .mySwiper .swiper-button-prev::after {
            font-size: 14px !important;
            font-weight: 900 !important;
        }

        /* Category Slider Arrows */
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
            min-height: 2.5rem;
            line-height: 1.25;
        }

        @media (min-width: 768px) {
            .product-title-fixed {
                min-height: 3rem;
            }
        }
    </style>

    {{-- พื้นหลัง --}}
    <div class="min-h-screen py-4 md:py-8 bg-cover bg-center bg-no-repeat bg-fixed"
        style="background-image: url('{{ asset('') }}');">

        <div class="container mx-auto px-2 md:px-4">

            <div class="flex flex-col gap-6 md:gap-8">

                <main class="w-full">

                    {{-- ★★★ HERO SECTION (สไลด์หลัก) ★★★ --}}
                    <div class="w-full pb-6 pt-2 md:pt-4">
                        <div class="container mx-auto px-4">
                            <div
                                class="relative w-full aspect-[16/10] md:aspect-[2.5/1] lg:aspect-[3/1] bg-gray-100 group rounded-2xl overflow-hidden shadow-xl">
                                <div class="swiper mySwiper w-full h-full absolute inset-0">
                                    <div class="swiper-wrapper">
                                        @if (isset($heroSlides) && $heroSlides->count() > 0)
                                            @foreach ($heroSlides as $slide)
                                                <div class="swiper-slide">
                                                    <a href="{{ $slide->link_url ?? '/allproducts' }}"
                                                        class="block w-full h-full">
                                                        <img src="{{ Storage::url($slide->image_path) }}"
                                                            class="w-full h-full object-cover object-center"
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

                    {{-- ★★★ CATEGORY MENU SECTION ★★★ --}}
                    <div
                        class="w-full py-4 rounded-xl mt-4 mb-6 shadow-md shadow-gray-300 bg-red-600 overflow-hidden relative group select-none">
                        <div class="container mx-auto px-2 relative">
                            <div class="swiper categorySwiper w-full pb-2">
                                <div class="swiper-wrapper items-start">
                                    @if (isset($dbCategories) && $dbCategories->count() > 0)
                                        @foreach ($dbCategories as $menu)
                                            <div class="swiper-slide !h-auto">
                                                <a href="/allproducts?category={{ $menu->name }}"
                                                    class="flex flex-col items-center group w-full transition-transform duration-300 active:scale-95 px-2 md:px-4">
                                                    <div
                                                        class="w-12 h-12 md:w-16 md:h-16 bg-gray-50 rounded-full flex items-center justify-center p-2 mb-2 shadow-sm transition-colors overflow-hidden">
                                                        @if ($menu->image_path)
                                                            <img src="{{ Storage::url($menu->image_path) }}"
                                                                alt="{{ $menu->name }}"
                                                                class="w-full h-full object-contain"
                                                                onerror="this.onerror=null;this.src='https://via.placeholder.com/150x150/fca5a5/ffffff?text=IMG';" />
                                                        @else
                                                            <i
                                                                class="{{ $menu->icon ?? 'fas fa-th-large' }} text-red-600 text-xl md:text-2xl"></i>
                                                        @endif
                                                    </div>
                                                    <span
                                                        class="text-[10px] md:text-xs font-bold text-white text-center leading-tight select-none">
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
                    <div
                        class="flex flex-col sm:flex-row justify-between items-center bg-white p-3 md:p-4 rounded-xl shadow-sm border border-gray-100 mb-6 gap-3">
                        <div class="w-full sm:w-auto">
                            <h2 class="text-gray-800 font-bold text-base md:text-lg flex items-center gap-2">
                                สินค้าทั้งหมด
                                <span class="badge badge-outline text-xs font-normal text-gray-500">
                                    {{ $products->total() }} รายการ
                                </span>
                            </h2>
                        </div>

                        <form id="sortForm" action="{{ route('allproducts') }}" method="GET"
                            class="flex items-center gap-3 w-full sm:w-auto">
                            @if (request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            @if (request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif

                            <label class="text-sm text-gray-600 whitespace-nowrap hidden sm:block">เรียงตาม:</label>
                            <select name="sort" onchange="document.getElementById('sortForm').submit();"
                                class="select select-bordered select-sm w-full sm:w-48 bg-gray-50 focus:border-red-500 focus:ring-red-500 text-gray-700 text-xs md:text-sm">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>ล่าสุด</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>ยอดนิยม
                                </option>
                                <option value="bestseller" {{ request('sort') == 'bestseller' ? 'selected' : '' }}>ขายดี
                                </option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>ราคา: ต่ำ
                                    - สูง</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>ราคา:
                                    สูง - ต่ำ</option>
                            </select>
                        </form>
                    </div>

                    {{-- ★★★ PRODUCT GRID (Optimized for Mobile) ★★★ --}}
                    @if ($products->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 md:gap-6">
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

                                    <div
                                        class="card relative bg-white border border-gray-100 shadow-sm hover:shadow-md transition-all rounded-xl overflow-hidden flex flex-col h-full group">
                                        <a href="{{ route('product.show', $product->pd_sp_id) }}">
                                            <figure class="relative aspect-square overflow-hidden bg-gray-50">
                                                @if (($product->pd_sp_stock ?? 0) <= 0)
                                                    <div
                                                        class="absolute inset-0 flex items-center justify-center z-10 bg-black/40">
                                                        <span
                                                            class="bg-black/70 text-white text-xs px-3 py-1 rounded-full font-bold">สินค้าหมด</span>
                                                    </div>
                                                @endif
                                                <img src="{{ Str::startsWith($imagePath, 'http') ? $imagePath : asset('storage/' . $imagePath) }}"
                                                    alt="{{ $product->pd_sp_name }}"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500 {{ ($product->pd_sp_stock ?? 0) <= 0 ? 'opacity-50' : '' }}"
                                                    onerror="this.onerror=null;this.src='https://via.placeholder.com/400x500.png?text=No+Image';" />

                                                @if ($isOnSale)
                                                    <div
                                                        class="absolute top-2 left-2 bg-red-600 px-1.5 py-0.5 md:px-2 md:py-1 rounded text-white text-[10px] md:text-xs font-bold shadow-sm">
                                                        ลด ฿{{ number_format($discountAmount) }}
                                                    </div>
                                                @endif
                                                @if ($product->gifts_per_item)
                                                    <div
                                                        class="absolute top-2 right-2 bg-red-500 px-1.5 py-0.5 md:px-2 md:py-1 rounded text-white text-[10px] md:text-xs font-bold shadow-sm flex items-center gap-1">
                                                        แถม {{ $product->gifts_per_item }}
                                                    </div>
                                                @endif
                                            </figure>
                                        </a>

                                        <div class="card-body p-2 md:p-4 flex flex-col flex-1">
                                            <h2
                                                class="card-title text-xs md:text-sm font-bold text-gray-800 leading-tight product-title-fixed mb-1">
                                                <a href="{{ route('product.show', $product->pd_sp_id) }}"
                                                    class="hover:text-red-600 transition">
                                                    {{ $product->pd_sp_name ?? 'Product Name' }}
                                                </a>
                                            </h2>

                                            {{-- ★★★ [ส่วนที่เพิ่ม] แสดงยอดขายคู่กับสถานะสต็อก ★★★ --}}
                                            <div class="flex items-center justify-between mb-1">
                                                <p
                                                    class="text-[10px] md:text-xs font-medium {{ ($product->pd_sp_stock ?? 0) > 0 ? 'text-green-600' : 'text-red-500' }}">
                                                    {{ ($product->pd_sp_stock ?? 0) > 0 ? '● มีสินค้า' : '● หมด' }}
                                                </p>
                                                <p class="text-[10px] text-gray-400">
                                                    ขายแล้ว {{ number_format($product->pd_sp_sold ?? 0) }} ชิ้น
                                                </p>
                                            </div>

                                            <div class="mt-auto pt-2 border-t border-gray-50">
                                                {{-- ราคาขาย (ซ้าย) | ราคาขีดฆ่า (ขวา) --}}
                                                <div class="flex flex-row items-center justify-between w-full mb-2 gap-1">
                                                    <div class="flex items-baseline gap-1">
                                                        @if ($hasOptions)
                                                            <span
                                                                class="text-[9px] md:text-[10px] text-gray-400">เริ่ม</span>
                                                        @endif
                                                        <span
                                                            class="text-sm md:text-lg font-bold text-red-600">฿{{ number_format($finalSellingPrice) }}</span>
                                                    </div>

                                                    @if ($isOnSale)
                                                        <span
                                                            class="text-[10px] md:text-xs text-gray-400 line-through">฿{{ number_format($originalPrice) }}</span>
                                                    @endif
                                                </div>

                                                <form class="add-to-cart-form-listing w-full"
                                                    data-action="{{ route('cart.add', ['id' => $product->pd_sp_id]) }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit"
                                                        class="btn btn-sm w-full {{ ($product->pd_sp_stock ?? 0) > 0 ? 'bg-red-600 hover:bg-red-700 text-white' : 'btn-disabled bg-gray-100 text-gray-400' }} border-none shadow-sm flex items-center justify-center gap-1 h-[32px] min-h-[32px]"
                                                        {{ ($product->pd_sp_stock ?? 0) <= 0 ? 'disabled' : '' }}>

                                                        <span class="text-xs font-normal">
                                                            @if (($product->pd_sp_stock ?? 0) <= 0)
                                                                สินค้าหมด
                                                            @elseif ($hasOptions)
                                                                เลือกตัวเลือก
                                                            @else
                                                                เพิ่มลงตะกร้า
                                                            @endif
                                                        </span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="mt-10 flex justify-center">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div
                            class="flex flex-col items-center justify-center py-20 bg-white rounded-xl border-2 border-dashed border-gray-200 text-center mx-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mb-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-lg font-bold text-gray-600">ไม่พบสินค้าที่คุณค้นหา</h3>
                            <a href="{{ route('allproducts') }}"
                                class="btn btn-sm btn-outline border-red-500 text-red-500 hover:bg-red-500 hover:text-white mt-4">ล้างคำค้นหา</a>
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
                    delay: 5000
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

            // ★★★ Category (แก้ไขเพิ่ม Autoplay) ★★★
            new Swiper(".categorySwiper", {
                slidesPerView: 4.5,
                spaceBetween: 10,
                loop: true,
                // เพิ่มการตั้งค่า Autoplay ตรงนี้
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false, // เลื่อนต่อแม้จะมีการกดปุ่ม
                    pauseOnMouseEnter: true, // หยุดเมื่อเอาเมาส์ชี้ (เพื่อให้กดง่ายขึ้น)
                },
                navigation: {
                    nextEl: ".categorySwiper .swiper-button-next",
                    prevEl: ".categorySwiper .swiper-button-prev"
                },
                breakpoints: {
                    640: {
                        slidesPerView: 6,
                        spaceBetween: 10
                    },
                    1024: {
                        slidesPerView: 8,
                        spaceBetween: 15
                    },
                },
            });

            // Cart Logic
            const forms = document.querySelectorAll('.add-to-cart-form-listing');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const btn = this.querySelector('button[type="submit"]');
                    if (btn && btn.textContent.includes('เลือกตัวเลือก')) {
                        e.preventDefault();
                        window.location.href = this.getAttribute('data-action').split('?')[0]
                            .replace('/cart/add/', '/product/');
                        return;
                    }
                    e.preventDefault();
                    const currentForm = this;
                    const submitBtn = currentForm.querySelector('button[type="submit"]');
                    const actionUrl = currentForm.getAttribute('data-action');
                    const quantity = currentForm.querySelector('[name="quantity"]').value;
                    const originalBtnContent = submitBtn.innerHTML;

                    submitBtn.disabled = true;
                    submitBtn.innerHTML =
                        '<span class="loading loading-spinner loading-xs"></span>';

                    const formData = new FormData();
                    formData.append('quantity', quantity);

                    fetch(actionUrl, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (typeof window.flyToCart === 'function') window.flyToCart(
                                    submitBtn);
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
