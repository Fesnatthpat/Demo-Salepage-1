@extends('layout')

@section('title', $product->pd_name . ' | Salepage Demo')

@section('content')
    @php
        // --- 1. เตรียมข้อมูล PHP ---
        $initialBasePrice = (float) $product->pd_price;
        $initialBasePrice2 = (float) $product->pd_price2;
        $discountAmount = (float) $product->pd_sp_discount;
        $finalPrice = max(0, $initialBasePrice - $discountAmount);
        $allImages = $product->images->pluck('image_url')->all();
        if (empty($allImages)) {
            $allImages[] = $product->cover_image_url;
        }

        // เตรียมข้อมูลโปรโมชั่น
        $promotionsData = [];
        if (isset($promotions) && $promotions->isNotEmpty()) {
            foreach ($promotions as $promo) {
                $gifts = collect();
                foreach ($promo->actions as $action) {
                    if ($action->productToGet) {
                        $gifts->push($action->productToGet);
                    }
                    if ($action->giftableProducts) {
                        $gifts = $gifts->merge($action->giftableProducts);
                    }
                }
                $promotionsData[] = [
                    'id' => $promo->id,
                    'logic' => $promo->frontend_logic,
                    'gifts_per_item' => $promo->actions->sum(fn($a) => (int) ($a->actions['quantity_to_get'] ?? 0)),
                    'gifts' => $gifts
                        ->unique('pd_sp_id')
                        ->map(function ($g) {
                            return [
                                'id' => $g->pd_sp_id,
                                'name' => $g->pd_sp_name,
                                'image' => $g->cover_image_url ?? 'https://via.placeholder.com/150',
                            ];
                        })
                        ->values()
                        ->all(),
                    'partner_products' => ($promo->partner_products ?? collect())
                        ->map(function ($p) {
                            return [
                                'id' => $p->pd_sp_id,
                                'name' => $p->pd_sp_name,
                                'price' => number_format($p->pd_sp_price, 0),
                                'image' => $p->display_image,
                                'url' => route('product.show', $p->pd_sp_id),
                            ];
                        })
                        ->values()
                        ->all(),
                ];
            }
        }

        $optionsData = $product->options->map(function ($option) {
            return [
                'id' => $option->option_id,
                'name' => $option->option_name,
                'price' => (float) $option->option_price,
                'final_price' => (float) $option->final_price, // ราคาที่หักส่วนลดของสินค้าหลักแล้ว
                'price2' => (float) $option->option_price2,
                'stock' => (int) $option->option_stock,
            ];
        });

        // --- เตรียมข้อมูลรูปรีวิวให้ Alpine.js ใช้งาน ---
        $reviewImagesList = [];
        if ($product->reviewImages && $product->reviewImages->count() > 0) {
            $reviewImagesList = $product->reviewImages
                ->map(function ($img) {
                    return filter_var($img->image_url, FILTER_VALIDATE_URL)
                        ? $img->image_url
                        : asset('storage/' . $img->image_url);
                })
                ->all();
        }
    @endphp

    {{-- --- 2. Alpine Data --- --}}
    <div x-data="productPage({
        currentProductId: @js($product->pd_sp_id),
        initialImage: @js($product->cover_image_url),
        allImages: @js($allImages),
        initialBasePrice: @js($initialBasePrice),
        initialBasePrice2: @js($initialBasePrice2),
        initialDisplayPrice: @js($product->display_price),
        initialStock: @js($product->pd_sp_stock),
        discountAmount: @js($discountAmount),
        options: @js($optionsData),
        standardAction: @js(route('cart.add', ['id' => $product->pd_sp_id])),
        bundleAddUrl: @js(route('cart.addBundle')),
        checkoutUrl: @js(route('payment.checkout')),
        promotions: @js($promotionsData),
        reviewImages: @js($reviewImagesList)
    })" class="max-w-6xl mx-auto px-4 py-8 font-sans antialiased"
        @keydown.escape.window="isModalOpen = false; isReviewModalOpen = false;"
        @keydown.arrow-left.window="if(isModalOpen) prevImage(); if(isReviewModalOpen) prevReviewImage();"
        @keydown.arrow-right.window="if(isModalOpen) nextImage(); if(isReviewModalOpen) nextReviewImage();">

        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
            <div class="grid grid-cols-1 lg:grid-cols-12">

                {{-- Image Gallery Section --}}
                <div class="lg:col-span-5 p-8 bg-gray-50/50">
                    <div class="sticky top-8">

                        {{-- Skeleton Loader for Main Image --}}
                        <div x-show="!imagesLoaded"
                            class="relative aspect-square rounded-2xl bg-gray-200 animate-pulse flex items-center justify-center">
                            <i class="fas fa-spinner fa-spin text-gray-400 text-4xl"></i>
                        </div>

                        {{-- Main Image (คลิกเพื่อเปิด Modal) --}}
                        <div x-show="imagesLoaded"
                            class="relative aspect-square rounded-2xl bg-white overflow-hidden shadow-sm border border-gray-100 cursor-zoom-in group"
                            @click="isModalOpen = true">

                            {{-- ปุ่มเลื่อนซ้าย --}}
                            <button x-show="images.length > 1" @click.stop="prevImage()"
                                class="absolute left-3 top-1/2 -translate-y-1/2 z-10 p-2 rounded-full bg-white/90 text-gray-800 shadow-md transition-all duration-300 hover:bg-red-600 hover:text-white transform hover:scale-110 opacity-100 lg:opacity-0 lg:group-hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>

                            {{-- รูปภาพหลัก --}}
                            <img :src="activeImage" @load="imagesLoaded = true"
                                class="w-full h-full object-contain p-4 transition-all duration-300 group-hover:scale-105"
                                onerror="this.src='https://via.placeholder.com/600?text=Image+Error'">

                            {{-- ปุ่มเลื่อนขวา --}}
                            <button x-show="images.length > 1" @click.stop="nextImage()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 z-10 p-2 rounded-full bg-white/90 text-gray-800 shadow-md transition-all duration-300 hover:bg-red-600 hover:text-white transform hover:scale-110 opacity-100 lg:opacity-0 lg:group-hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

                            {{-- Icon แว่นขยาย --}}
                            <div
                                class="absolute bottom-4 right-4 bg-black/10 p-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                <i class="fas fa-search-plus text-gray-600"></i>
                            </div>
                        </div>

                        {{-- Skeleton Loader for Thumbnails --}}
                        <div x-show="!imagesLoaded" class="grid grid-cols-5 gap-3 mt-6">
                            <template x-for="i in 5" :key="i">
                                <div class="aspect-square rounded-xl bg-gray-200 animate-pulse"></div>
                            </template>
                        </div>

                        {{-- Thumbnails --}}
                        @if (count($allImages) > 1)
                            <div x-show="imagesLoaded" class="grid grid-cols-5 gap-3 mt-6">
                                <template x-for="img in images" :key="img">
                                    <button @click="activeImage = img"
                                        class="aspect-square rounded-xl border-2 overflow-hidden bg-white transition-all"
                                        :class="activeImage === img ? 'border-red-500 shadow-md transform scale-105' :
                                            'border-transparent opacity-60 hover:opacity-100'">
                                        <img :src="img" class="w-full h-full object-cover">
                                    </button>
                                </template>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Product Details Section --}}
                <div class="lg:col-span-7 p-8 lg:p-12 flex flex-col">
                    <div class="flex-1">
                        <h1 class="text-3xl font-extrabold text-gray-900 mb-6">{{ $product->pd_name }}</h1>

                        <div class="mb-4 text-sm font-semibold">
                            จำนวนสินค้าคงเหลือ:
                            <span :class="currentStock > 0 ? 'text-emerald-600' : 'text-red-500'"
                                x-text="currentStock.toLocaleString() + ' ชิ้น'">
                            </span>
                        </div>

                        {{-- Price Block --}}
                        <div class="inline-flex flex-col items-start bg-gray-50 rounded-2xl p-4 mb-8">
                            <div class="flex items-baseline gap-2">
                                <span class="text-4xl font-black text-red-600" x-text="finalPrice"></span>
                                <template x-if="originalPriceDisplay">
                                    <span class="text-lg text-gray-400 line-through" x-text="originalPriceDisplay"></span>
                                </template>
                            </div>
                            <template x-if="discountDisplay">
                                <span class="text-sm font-semibold text-red-500 mt-1" x-text="discountDisplay"></span>
                            </template>
                        </div>

                        {{-- รายละเอียดสินค้าแบบย่อ/ขยายได้ --}}
                        @if ($product->pd_details)
                            <div x-data="{ expanded: false }" class="mb-8">
                                <h2 class="text-lg font-bold text-gray-800 mb-4">รายละเอียดสินค้า:</h2>
                                <div class="relative">
                                    <div class="prose max-w-none text-gray-600 transition-all duration-300"
                                        :class="expanded ? '' : 'max-h-[150px] overflow-hidden'">
                                        {!! nl2br(e($product->pd_details)) !!}
                                    </div>
                                    {{-- Fade effect --}}
                                    <div x-show="!expanded"
                                        class="absolute bottom-0 left-0 w-full h-16 bg-gradient-to-t from-white to-transparent pointer-events-none">
                                    </div>
                                </div>
                                <button @click="expanded = !expanded"
                                    class="text-red-600 font-bold hover:underline mt-2 flex items-center gap-1 focus:outline-none">
                                    <span x-text="expanded ? 'ย่อน้อยลง' : 'แสดงเพิ่มเติม'"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform transition-transform"
                                        :class="expanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>
                        @endif

                        {{-- Options --}}
                        <template x-if="options.length > 0">
                            <div class="mb-8">
                                <h3 class="text-lg font-bold text-gray-800 mb-4">เลือกตัวเลือก:</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <template x-for="option in options" :key="option.id">
                                        <label
                                            class="flex items-center gap-4 p-4 rounded-xl border-2 transition-all duration-300 cursor-pointer"
                                            :class="selectedOption == option.id ? 'border-red-500 bg-red-50' :
                                                'border-gray-200 bg-white hover:border-red-200'">
                                            <input type="radio" name="product_option" x-model="selectedOption"
                                                :value="option.id"
                                                class="h-5 w-5 rounded-full border-gray-300 text-red-600 focus:ring-red-500">
                                            <div class="flex-1">
                                                <p class="font-bold text-gray-800" x-text="option.name"></p>
                                                <p class="text-sm text-red-600 font-bold">
                                                    <template x-if="option.price2 && option.price2 > 0">
                                                        <span
                                                            x-text="`฿${option.price.toLocaleString(undefined, {minimumFractionDigits: 0})} - ฿${option.price2.toLocaleString(undefined, {minimumFractionDigits: 0})}`"></span>
                                                    </template>
                                                    <template x-if="!(option.price2 && option.price2 > 0)">
                                                        <span
                                                            x-text="`฿${option.price.toLocaleString(undefined, {minimumFractionDigits: 0})}`"></span>
                                                    </template>
                                                </p>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- Promotion UI --}}
                        <template x-if="activePromotion">
                            <div id="promotion-section" class="mb-10 scroll-mt-24">
                                <div class="p-6 rounded-2xl border-2 border-dashed bg-red-50/30 mb-4 transition-all duration-500"
                                    :class="isConditionMet ? 'border-red-300 shadow-inner' : 'border-gray-200 opacity-75'">

                                    <h3 class="text-sm font-bold text-gray-800 mb-2 flex items-center gap-2">
                                        <span>🎉 ของแถมโปรโมชั่น</span>
                                        <template x-if="activePromotion && activePromotion.gifts_per_item > 0">
                                            <span class="badge badge-lg badge-error text-white font-bold ml-2">
                                                แถม <span x-text="activePromotion.gifts_per_item"></span> ชิ้น
                                            </span>
                                        </template>
                                        <span x-show="isConditionMet"
                                            class="badge badge-error badge-sm text-white">ปลดล็อคแล้ว!</span>
                                    </h3>

                                    <template x-if="isConditionMet">
                                        <div class="mb-4 text-sm text-gray-700">
                                            ✅ เงื่อนไขครบถ้วน! คุณได้รับสิทธิ์เลือกฟรี <strong class="text-red-600 text-lg"
                                                x-text="giftLimit"></strong> ชิ้น
                                        </div>
                                    </template>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <template x-for="gift in activePromotion.gifts" :key="gift.id">
                                            <label
                                                class="flex items-center gap-4 p-3 rounded-xl border-2 transition-all duration-300"
                                                :class="{
                                                    'bg-gray-100 border-gray-200 opacity-60 cursor-not-allowed grayscale': isGiftDisabled(
                                                        gift.id) && !selectedGifts.includes(gift.id),
                                                    'bg-white cursor-pointer border-red-200 ring-2 ring-red-100 hover:border-red-300': isConditionMet &&
                                                        !isGiftDisabled(gift.id),
                                                    'border-red-500 bg-red-50 ring-0': selectedGifts.includes(gift.id)
                                                }">
                                                <input type="checkbox"
                                                    :disabled="!isConditionMet || (selectedGifts.length >= giftLimit && !
                                                        selectedGifts.includes(gift.id))"
                                                    @click="toggleGift(gift.id)"
                                                    :checked="selectedGifts.includes(gift.id)"
                                                    class="h-5 w-5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                                <div
                                                    class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 border border-gray-100">
                                                    <img :src="gift.image" class="w-full h-full object-cover">
                                                </div>
                                                <div class="flex-1 overflow-hidden">
                                                    <p class="text-xs font-bold text-gray-800 truncate"
                                                        x-text="gift.name">
                                                    </p>
                                                    <span class="text-xs font-bold"
                                                        :class="!isConditionMet ? 'text-gray-400' : 'text-red-500'"
                                                        x-text="!isConditionMet ? 'ล็อค' : 'เลือกฟรี'"></span>
                                                </div>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Main Actions --}}
                    <div class="pt-8 border-t border-gray-100 flex flex-col sm:flex-row items-center gap-6">
                        <div class="flex items-center bg-gray-100 rounded-2xl p-1.5 shadow-inner">
                            <button @click="quantity > 1 ? quantity-- : null"
                                class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-red-600 font-bold text-lg">-</button>
                            <input type="number" x-model.number="quantity"
                                class="w-12 text-center bg-transparent border-none font-black text-gray-900 focus:ring-0 text-lg"
                                readonly>
                            <button @click="quantity++"
                                class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-red-600 font-bold text-lg">+</button>
                        </div>
                        <div class="flex-1 w-full grid grid-cols-2 gap-4">
                            <button @click="handleAddToCartClick(false)" :disabled="currentStock <= 0 || isLoading"
                                :class="{
                                    'border-red-600 text-red-600 hover:bg-red-50': currentStock > 0,
                                    'border-gray-300 bg-gray-200 text-gray-400 cursor-not-allowed': currentStock <= 0
                                }"
                                class="h-14 rounded-2xl border-2 font-bold transition-all flex items-center justify-center gap-2 text-lg">
                                <span x-show="!isLoading && currentStock > 0">ใส่ตะกร้า</span>
                                <span x-show="!isLoading && currentStock <= 0">สินค้าหมด</span>
                                <span x-show="isLoading" class="loading loading-spinner"></span>
                            </button>
                            <button @click="handleAddToCartClick(true)" :disabled="currentStock <= 0 || isLoading"
                                :class="{
                                    'bg-red-600 text-white hover:bg-red-700 shadow-lg shadow-red-500/30': currentStock >
                                        0,
                                    'bg-gray-400 text-gray-100 cursor-not-allowed': currentStock <= 0
                                }"
                                class="h-14 rounded-2xl font-bold transition-all text-lg">
                                <span x-show="currentStock > 0">สั่งเลย</span>
                                <span x-show="currentStock <= 0">สินค้าหมด</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- 🌟 Review Images Section (อัปเกรดคลิกเพื่อขยายได้) --}}
        {{-- ========================================== --}}
        @if ($product->reviewImages && $product->reviewImages->count() > 0)
            <div class="mt-12 mb-8">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-8 flex items-center justify-center gap-2">
                    <i class="fas fa-camera text-red-500"></i> รูปรีวิวจากลูกค้า
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-2 gap-4">
                    @foreach ($product->reviewImages as $index => $reviewImage)
                        @php
                            $imgUrl = filter_var($reviewImage->image_url, FILTER_VALIDATE_URL)
                                ? $reviewImage->image_url
                                : asset('storage/' . $reviewImage->image_url);
                        @endphp
                        {{-- กล่องรูปรีวิว เปลี่ยนเคอร์เซอร์เป็นแว่นขยาย และเรียกเปิด Modal เมื่อคลิก --}}
                        <div class="rounded-xl overflow-hidden border-2 border-transparent hover:border-red-400 shadow-sm hover:shadow-md cursor-zoom-in group relative aspect-square transition-all duration-300"
                            @click="openReviewModal('{{ $imgUrl }}')">

                            <img src="{{ $imgUrl }}" alt="Product Review Image"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">

                            {{-- Hover Effect รูปแว่นขยาย --}}
                            <div
                                class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center pointer-events-none">
                                <i class="fas fa-search-plus text-white text-3xl drop-shadow-lg"></i>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ========================================== --}}
        {{-- 🖼️ MODAL POPUP สำหรับรูปสินค้าหลัก --}}
        {{-- ========================================== --}}
        <div x-show="isModalOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/95 backdrop-blur-sm"
            @click.self="isModalOpen = false" style="display: none;">

            {{-- Close Button --}}
            <button @click="isModalOpen = false"
                class="absolute top-4 right-4 z-50 p-2 text-white/70 hover:text-white transition-colors bg-black/20 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 md:h-10 md:w-10" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Slider Container --}}
            <div class="relative w-full max-w-5xl h-full flex items-center justify-center">

                {{-- Left Button (Previous) --}}
                <button x-show="images.length > 1" @click.stop="prevImage()"
                    class="absolute left-2 md:left-4 z-40 p-2 md:p-3 rounded-full bg-black/50 hover:bg-black/80 text-white border border-white/10 backdrop-blur-sm transition-all transform hover:scale-110 focus:outline-none shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 md:h-8 md:w-8" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                {{-- Image Display --}}
                <div class="relative max-h-full w-full flex justify-center items-center" @click.stop>
                    <img :src="activeImage"
                        class="max-w-full max-h-[85vh] object-contain rounded-md shadow-2xl transition-all duration-300 select-none"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-50 scale-95" x-transition:enter-end="opacity-100 scale-100">
                </div>

                {{-- Right Button (Next) --}}
                <button x-show="images.length > 1" @click.stop="nextImage()"
                    class="absolute right-2 md:right-4 z-40 p-2 md:p-3 rounded-full bg-black/50 hover:bg-black/80 text-white border border-white/10 backdrop-blur-sm transition-all transform hover:scale-110 focus:outline-none shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 md:h-8 md:w-8" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            {{-- Image Counter Indicator --}}
            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 bg-black/60 border border-white/10 px-4 py-1.5 rounded-full text-white text-sm font-medium backdrop-blur-md"
                x-show="images.length > 1">
                <span x-text="images.indexOf(activeImage) + 1"></span> / <span x-text="images.length"></span>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- 📸 MODAL POPUP สำหรับรูปรีวิวโดยเฉพาะ --}}
        {{-- ========================================== --}}
        <div x-show="isReviewModalOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/95 backdrop-blur-sm"
            @click.self="isReviewModalOpen = false" style="display: none;">

            {{-- Close Button --}}
            <button @click="isReviewModalOpen = false"
                class="absolute top-4 right-4 z-50 p-2 text-white/70 hover:text-white transition-colors bg-black/20 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 md:h-10 md:w-10" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Slider Container --}}
            <div class="relative w-full max-w-5xl h-full flex items-center justify-center">

                {{-- Left Button (Previous) --}}
                <button x-show="reviewImages.length > 1" @click.stop="prevReviewImage()"
                    class="absolute left-2 md:left-4 z-40 p-2 md:p-3 rounded-full bg-black/50 hover:bg-black/80 text-white border border-white/10 backdrop-blur-sm transition-all transform hover:scale-110 focus:outline-none shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 md:h-8 md:w-8" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                {{-- Image Display --}}
                <div class="relative max-h-full w-full flex justify-center items-center" @click.stop>
                    <img :src="activeReviewImage"
                        class="max-w-full max-h-[85vh] object-contain rounded-md shadow-2xl transition-all duration-300 select-none"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-50 scale-95" x-transition:enter-end="opacity-100 scale-100">
                </div>

                {{-- Right Button (Next) --}}
                <button x-show="reviewImages.length > 1" @click.stop="nextReviewImage()"
                    class="absolute right-2 md:right-4 z-40 p-2 md:p-3 rounded-full bg-black/50 hover:bg-black/80 text-white border border-white/10 backdrop-blur-sm transition-all transform hover:scale-110 focus:outline-none shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 md:h-8 md:w-8" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            {{-- Review Image Counter Indicator --}}
            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 bg-black/60 border border-white/10 px-4 py-1.5 rounded-full text-white text-sm font-medium backdrop-blur-md"
                x-show="reviewImages.length > 1">
                รีวิว <span x-text="reviewImages.indexOf(activeReviewImage) + 1"></span> / <span
                    x-text="reviewImages.length"></span>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productPage', (config) => ({
                isModalOpen: false,
                activeImage: config.initialImage,
                images: config.allImages,

                // --- เพิ่ม State สำหรับ Review Images ---
                reviewImages: config.reviewImages || [],
                isReviewModalOpen: false,
                activeReviewImage: null,
                // ------------------------------------

                quantity: 1,
                isLoading: false,
                imagesLoaded: false,
                basePrice: config.initialBasePrice || 0, // Updated
                basePrice2: config.initialBasePrice2 || 0, // Added
                initialStock: config.initialStock || 0,
                discountAmount: config.discountAmount || 0, // Added
                options: config.options || [],
                selectedOption: null,
                promotions: config.promotions || [],
                selectedGifts: [],

                // --- ฟังก์ชัน Slider รูปหลัก ---
                prevImage() {
                    let currentIndex = this.images.indexOf(this.activeImage);
                    if (currentIndex === -1) currentIndex = 0;
                    let prevIndex = (currentIndex - 1 + this.images.length) % this.images.length;
                    this.activeImage = this.images[prevIndex];
                },

                nextImage() {
                    let currentIndex = this.images.indexOf(this.activeImage);
                    if (currentIndex === -1) currentIndex = 0;
                    let nextIndex = (currentIndex + 1) % this.images.length;
                    this.activeImage = this.images[nextIndex];
                },

                // --- ฟังก์ชัน Slider รูปรีวิว ---
                openReviewModal(imgUrl) {
                    this.activeReviewImage = imgUrl;
                    this.isReviewModalOpen = true;
                },

                prevReviewImage() {
                    let currentIndex = this.reviewImages.indexOf(this.activeReviewImage);
                    if (currentIndex === -1) currentIndex = 0;
                    let prevIndex = (currentIndex - 1 + this.reviewImages.length) % this.reviewImages
                        .length;
                    this.activeReviewImage = this.reviewImages[prevIndex];
                },

                nextReviewImage() {
                    let currentIndex = this.reviewImages.indexOf(this.activeReviewImage);
                    if (currentIndex === -1) currentIndex = 0;
                    let nextIndex = (currentIndex + 1) % this.reviewImages.length;
                    this.activeReviewImage = this.reviewImages[nextIndex];
                },
                // --------------------------

                get currentStock() {
                    if (this.selectedOption) {
                        const option = this.options.find(o => o.id == this.selectedOption);
                        return option ? option.stock : 0;
                    }
                    return this.initialStock;
                },

                get finalPrice() {
                    // หากยังไม่ได้เลือก Option ให้แสดงช่วงราคาเริ่มต้นที่มาจาก Model (เช่น 199-299)
                    if (this.options.length > 0 && !this.selectedOption) {
                        return `฿${config.initialDisplayPrice}`;
                    }

                    let priceToDisplay = this.basePrice;
                    let price2ToDisplay = this.basePrice2;
                    let actualDiscount = this.discountAmount;

                    if (this.selectedOption) {
                        const option = this.options.find(o => o.id == this.selectedOption);
                        if (option) {
                            // ใช้ราคาที่หักส่วนลดแล้ว (final_price) มาแสดงเป็นราคาหลัก
                            return `฿${option.final_price.toLocaleString(undefined, {minimumFractionDigits: 0})}`;
                        }
                    }

                    let final = Math.max(0, priceToDisplay - actualDiscount);

                    if (price2ToDisplay && price2ToDisplay > 0) {
                        return `฿${priceToDisplay.toLocaleString(undefined, {minimumFractionDigits: 0})} - ฿${price2ToDisplay.toLocaleString(undefined, {minimumFractionDigits: 0})}`;
                    }
                    return `฿${final.toLocaleString(undefined, {minimumFractionDigits: 0})}`;
                },
                get originalPriceDisplay() {
                    // หากยังไม่ได้เลือก Option ไม่ต้องแสดงราคาเดิมขีดฆ่า
                    if (this.options.length > 0 && !this.selectedOption) {
                        return null;
                    }

                    if (this.selectedOption) {
                        const option = this.options.find(o => o.id == this.selectedOption);
                        // ถ้าราคาเดิมของ Option (price) มากกว่าราคาลดแล้ว (final_price) ให้แสดงราคาเดิมขีดฆ่า
                        if (option && option.price > option.final_price) {
                            return `฿${option.price.toLocaleString(undefined, {minimumFractionDigits: 0})}`;
                        }
                        return null;
                    }

                    if (this.discountAmount > 0) {
                        return `฿${this.basePrice.toLocaleString(undefined, {minimumFractionDigits: 0})}`;
                    }
                    return null;
                },
                get discountDisplay() {
                    // หากยังไม่ได้เลือก Option ไม่ต้องแสดงส่วนลด
                    if (this.options.length > 0 && !this.selectedOption) {
                        return null;
                    }

                    if (this.selectedOption) {
                        const option = this.options.find(o => o.id == this.selectedOption);
                        if (option && option.price > option.final_price) {
                            let diff = option.price - option.final_price;
                            return `ประหยัด ฿${diff.toLocaleString(undefined, {minimumFractionDigits: 0})}`;
                        }
                        return null;
                    }

                    if (this.discountAmount > 0) {
                        return `ประหยัด ฿${this.discountAmount.toLocaleString(undefined, {minimumFractionDigits: 0})}`;
                    }
                    return null;
                },
                get activePromotion() {
                    return this.promotions.length > 0 ? this.promotions[0] : null;
                },
                get isConditionMet() {
                    if (!this.activePromotion) return false;
                    const logic = this.activePromotion.logic;
                    const totalQty = logic.cart_qty + this.quantity;
                    return logic.condition_type === 'all' ?
                        (logic.other_rules_met && (totalQty >= logic.required_qty)) :
                        (totalQty >= logic.required_qty);
                },
                get giftLimit() {
                    return (!this.activePromotion || !this.isConditionMet) ? 0 : this
                        .activePromotion.gifts_per_item;
                },
                isGiftDisabled(giftId) {
                    if (!this.isConditionMet) return true;
                    if (this.selectedGifts.includes(giftId)) return false;
                    return this.selectedGifts.length >= this.giftLimit;
                },
                init() {
                    this.imagesLoaded = true; // Added this line
                    // No default selection for options. User must choose.
                    // if (this.options.length > 0) this.selectedOption = this.options[0].id;
                    this.$watch('quantity', () => this.validateSelection());

                    if (localStorage.getItem('justAddedPartner') === 'true') {
                        localStorage.removeItem('justAddedPartner');
                        setTimeout(() => {
                            const promoSection = document.getElementById('promotion-section');
                            if (promoSection) promoSection.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                            Swal.fire({
                                icon: 'success',
                                title: 'ปลดล็อคของแถมแล้ว!',
                                text: 'คุณสามารถเลือกของแถมได้เลย',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }, 500);
                    }
                },
                validateSelection() {
                    if (!this.isConditionMet) this.selectedGifts = [];
                    else if (this.selectedGifts.length > this.giftLimit) this.selectedGifts.splice(this
                        .giftLimit);
                },
                toggleGift(id) {
                    if (!this.isConditionMet) return;
                    const index = this.selectedGifts.indexOf(id);
                    if (index > -1) this.selectedGifts.splice(index, 1);
                    else if (this.selectedGifts.length < this.giftLimit) this.selectedGifts.push(id);
                    else Swal.fire({
                        icon: 'warning',
                        title: 'เลือกของแถมครบแล้ว',
                        confirmButtonColor: '#ef4444'
                    });
                },
                async addToCartPartner(partnerId) {
                    if (this.isLoading) return;
                    this.isLoading = true;
                    try {
                        const response = await fetch(config.bundleAddUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                main_product_id: parseInt(config.currentProductId),
                                secondary_product_id: parseInt(partnerId),
                                gift_ids: this.selectedGifts
                            })
                        });

                        // ★★★ ตรวจสอบ Response ว่าเป็น JSON หรือไม่ ★★★
                        const contentType = response.headers.get("content-type");
                        if (!contentType || !contentType.includes("application/json")) {
                            const text = await response.text();
                            console.error("Server HTML Response:",
                                text); // แสดง HTML ใน Console เพื่อช่วย Debug
                            throw new Error(
                                "เกิดข้อผิดพลาดที่เซิร์ฟเวอร์ (ได้รับ HTML แทน JSON) กรุณาลองใหม่อีกครั้ง"
                            );
                        }

                        const data = await response.json();
                        if (data.success) {
                            localStorage.setItem('justAddedPartner', 'true');
                            setTimeout(() => { // Add setTimeout
                                Livewire.dispatch('cartUpdated');
                                console.log(
                                    'Dispatched cartUpdated from product.blade.php (addToCartPartner)'
                                ); // Add log
                            }, 50); // Small delay
                            window.location.reload();
                        } else throw new Error(data.message);
                    } catch (e) {
                        Swal.fire('ข้อผิดพลาด', e.message, 'error');
                        this.isLoading = false;
                    }
                },
                async handleAddToCartClick(isBuyNow) {
                    if (this.currentStock <= 0) return; // Do nothing if out of stock
                    if (this.options.length > 0 && !this.selectedOption) {
                        Swal.fire('กรุณาเลือกตัวเลือก', 'เลือกตัวเลือกก่อน', 'warning');
                        return;
                    }
                    if (this.isConditionMet && this.giftLimit > 0 && this.selectedGifts.length !==
                        this.giftLimit) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'กรุณาเลือกของแถม',
                            confirmButtonColor: '#ef4444'
                        });
                        return;
                    }
                    if (this.isLoading) return;
                    this.isLoading = true;
                    try {
                        const response = await fetch(config.standardAction, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                quantity: parseInt(this.quantity),
                                selected_gift_ids: this.selectedGifts,
                                selected_option_id: this.selectedOption
                            })
                        });

                        // ★★★ ตรวจสอบ Response ว่าเป็น JSON หรือไม่ ★★★
                        const contentType = response.headers.get("content-type");
                        if (!contentType || !contentType.includes("application/json")) {
                            const text = await response.text();
                            console.error("Server HTML Response:", text);
                            throw new Error(
                                "เกิดข้อผิดพลาดที่เซิร์ฟเวอร์ (ได้รับ HTML แทน JSON) กรุณาลองใหม่อีกครั้ง"
                            );
                        }

                        const data = await response.json();
                        if (data.success) {
                            if (isBuyNow) window.location.href = config.checkoutUrl;
                            else {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'เพิ่มสินค้าแล้ว',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                setTimeout(() => { // Add setTimeout
                                    Livewire.dispatch('cartUpdated');
                                    console.log(
                                        'Dispatched cartUpdated from product.blade.php (handleAddToCartClick)'
                                    ); // Add log
                                }, 50); // Small delay
                            }
                        } else throw new Error(data.message);
                    } catch (e) {
                        Swal.fire('ข้อผิดพลาด', e.message, 'error');
                    } finally {
                        this.isLoading = false;
                    }
                }
            }));
        });
    </script>
@endsection
