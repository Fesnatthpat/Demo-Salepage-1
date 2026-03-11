@extends('layout')

@section('title', $product->pd_name . ' | Salepage Demo')

@section('content')
    @php
        $initialBasePrice = (float) $product->pd_price;
        $initialBasePrice2 = (float) $product->pd_price2;
        $discountAmount = (float) $product->pd_sp_discount;
        $finalPrice = max(0, $initialBasePrice - $discountAmount);
        $allImages = $product->images->pluck('image_url')->all();
        if (empty($allImages)) {
            $allImages[] = $product->cover_image_url;
        }

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
                    'name' => $promo->name,
                    'condition_type' => $promo->condition_type,
                    'end_date' => $promo->end_date ? $promo->end_date->toIso8601String() : null,
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
                'final_price' => (float) $option->final_price,
                'price2' => (float) $option->option_price2,
                'stock' => (int) $option->option_stock,
                'image_url' => $option->option_image_url,
            ];
        });

        $reviewImagesList = [];
        if ($product->reviewImages && $product->reviewImages->count() > 0) {
            $reviewImagesList = $product->reviewImages
                ->map(function ($img) {
                    return filter_var($img->image_url, FILTER_VALIDATE_URL)
                        ? $img->image_url
                        : asset('storage/' . ltrim($img->image_url, '/'));
                })
                ->all();
        }
    @endphp

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
        cartUrl: @js(route('cart.index')),
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
                        <div x-show="!imagesLoaded"
                            class="relative aspect-square rounded-2xl bg-gray-200 animate-pulse flex items-center justify-center">
                            <i class="fas fa-spinner fa-spin text-gray-400 text-4xl"></i>
                        </div>

                        <div x-show="imagesLoaded"
                            class="relative aspect-square rounded-2xl bg-white overflow-hidden shadow-sm border border-gray-100 cursor-zoom-in group"
                            @click="isModalOpen = true">
                            <button x-show="images.length > 1" @click.stop="prevImage()"
                                class="absolute left-3 top-1/2 -translate-y-1/2 z-10 p-2 rounded-full bg-white/90 text-gray-800 shadow-md transition-all duration-300 hover:bg-red-600 hover:text-white transform hover:scale-110 opacity-100 lg:opacity-0 lg:group-hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>

                            <img :src="activeImage" @load="imagesLoaded = true"
                                class="w-full h-full object-contain p-4 transition-all duration-500 group-hover:scale-105"
                                onerror="this.src='https://via.placeholder.com/600?text=Image+Error'">

                            <button x-show="images.length > 1" @click.stop="nextImage()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 z-10 p-2 rounded-full bg-white/90 text-gray-800 shadow-md transition-all duration-300 hover:bg-red-600 hover:text-white transform hover:scale-110 opacity-100 lg:opacity-0 lg:group-hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>

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

                        <div class="mb-4 text-sm font-semibold text-gray-500">
                            จำนวนสินค้าคงเหลือ:
                            <span :class="currentStock > 0 ? 'text-emerald-600' : 'text-red-500'"
                                x-text="currentStock.toLocaleString() + ' ชิ้น'"></span>
                        </div>

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

                        @if ($product->pd_details)
                            <div x-data="{ expanded: false }" class="mb-8">
                                <h2 class="text-lg font-bold text-gray-800 mb-4">รายละเอียดสินค้า:</h2>
                                <div class="relative">
                                    <div class="prose max-w-none text-gray-600 transition-all duration-300"
                                        :class="expanded ? '' : 'max-h-[150px] overflow-hidden'">
                                        {!! nl2br(e($product->pd_details)) !!}
                                    </div>
                                    <div x-show="!expanded"
                                        class="absolute bottom-0 left-0 w-full h-16 bg-gradient-to-t from-white to-transparent pointer-events-none">
                                    </div>
                                </div>
                                <button @click="expanded = !expanded"
                                    class="text-red-600 font-bold hover:underline mt-2 flex items-center gap-1">
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
                                            :class="selectedOption == option.id ? 'border-red-500 bg-red-50 shadow-sm' :
                                                'border-gray-200 bg-white hover:border-red-200'">
                                            <input type="radio" name="product_option" x-model="selectedOption"
                                                :value="option.id"
                                                class="h-5 w-5 border-gray-300 text-red-600 focus:ring-red-500">
                                            <div class="flex-1">
                                                <p class="font-bold text-gray-800" x-text="option.name"></p>
                                                <p class="text-sm text-red-600 font-bold"
                                                    x-text="'฿' + option.price.toLocaleString()"></p>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- Promotion Section --}}
                        <template x-if="promotions.length > 0">
                            <div class="space-y-6 mb-8">
                                <template x-for="promo in promotions" :key="promo.id">
                                    <div
                                        class="bg-gradient-to-br from-pink-50 to-red-50 rounded-2xl p-6 border border-red-100 shadow-sm relative overflow-hidden">
                                        <div class="absolute top-0 right-0" x-show="promo.remainingTime">
                                            <div class="flex items-center gap-1.5 px-4 py-2 bg-white/80 backdrop-blur-sm rounded-bl-2xl border-b border-l border-red-100 shadow-sm"
                                                :class="promo.isExpiringSoon ? 'text-red-600 animate-pulse' : 'text-gray-600'">
                                                <i class="fas fa-stopwatch text-sm"></i>
                                                <span class="text-xs font-black tracking-tighter"
                                                    x-text="promo.remainingTime"></span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3 mb-4">
                                            <span
                                                class="flex items-center justify-center w-8 h-8 rounded-full bg-red-600 text-white shadow-md">
                                                <i class="fas fa-gift text-sm"></i>
                                            </span>
                                            <h3 class="text-lg font-black text-gray-900">โปรโมชั่นสุดพิเศษ!</h3>
                                        </div>

                                        <template x-if="promo.condition_type !== 'all'">
                                            <div>
                                                <p class="text-gray-700 font-bold mb-4">
                                                    ซื้อครบ <span class="text-red-600"
                                                        x-text="promo.logic.required_qty"></span> ชิ้น
                                                    รับของแถมฟรีทันที <span class="text-red-600"
                                                        x-text="promo.gifts_per_item * Math.floor(quantity / promo.logic.required_qty)"></span>
                                                    ชิ้น
                                                </p>

                                                <div class="mb-6">
                                                    <div class="flex justify-between text-xs font-bold mb-2">
                                                        <span
                                                            class="text-gray-500 uppercase tracking-wider">ความคืบหน้า</span>
                                                        <span class="text-red-600"
                                                            x-text="Math.min(100, Math.round((quantity / promo.logic.required_qty) * 100)) + '%'"></span>
                                                    </div>
                                                    <div
                                                        class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden shadow-inner">
                                                        <div class="bg-red-600 h-2.5 rounded-full transition-all duration-500"
                                                            :style="`width: ${Math.min(100, (quantity / promo.logic.required_qty) * 100)}%`"
                                                            :class="quantity >= promo.logic.required_qty ?
                                                                'bg-emerald-500 animate-pulse' : 'bg-red-600'">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div x-show="quantity >= promo.logic.required_qty" x-transition
                                                    class="bg-white/80 backdrop-blur-sm rounded-xl p-4 border border-red-100/50">
                                                    <div class="flex items-center justify-between mb-4">
                                                        <h4 class="font-bold text-gray-800">ของแถมที่คุณจะได้รับ:</h4>
                                                        <span
                                                            class="px-3 py-1 bg-red-600 text-white text-xs font-black rounded-full shadow-sm"
                                                            x-text="`รับฟรี ${giftLimit} ชิ้น`"></span>
                                                    </div>

                                                    {{-- แสดงช่องว่างของแถมตามโควตา --}}
                                                    <div
                                                        class="flex flex-wrap gap-3 mb-6 p-3 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                                                        <template
                                                            x-for="(giftId, index) in Array.from({length: giftLimit}, (_, i) => selectedGifts[i])"
                                                            :key="index">
                                                            <div class="relative w-16 h-16 sm:w-20 sm:h-20 rounded-2xl border-2 transition-all duration-300 overflow-hidden shadow-sm"
                                                                :class="giftId ? 'border-red-500 bg-white scale-105' :
                                                                    'border-gray-200 bg-gray-100/30'">

                                                                <template x-if="giftId">
                                                                    <div class="w-full h-full group">
                                                                        <img :src="promo.gifts.find(g => g.id === giftId)?.image"
                                                                            class="w-full h-full object-cover">
                                                                        <button @click.stop="removeGift(giftId)"
                                                                            class="absolute -top-1 -right-1 w-5 h-5 bg-red-600 text-white rounded-full flex items-center justify-center shadow-md hover:bg-black transition-colors">
                                                                            <i class="fas fa-times text-[10px]"></i>
                                                                        </button>
                                                                    </div>
                                                                </template>

                                                                <template x-if="!giftId">
                                                                    <div
                                                                        class="w-full h-full flex flex-col items-center justify-center text-gray-300 opacity-50">
                                                                        <i class="fas fa-gift text-xl mb-1"></i>
                                                                        <span
                                                                            class="text-[8px] font-bold uppercase">ว่าง</span>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </template>
                                                    </div>

                                                    <p
                                                        class="text-[11px] font-bold text-gray-500 mb-3 flex items-center gap-1">
                                                        <i class="fas fa-mouse-pointer text-red-400"></i>
                                                        เลือกสินค้าที่ต้องการด้านล่าง:
                                                    </p>

                                                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                                                        <template x-for="gift in promo.gifts" :key="gift.id">
                                                            <div class="relative group flex flex-col items-center p-1.5 rounded-xl border-2 transition-all duration-300 cursor-pointer"
                                                                :class="getGiftCount(gift.id) > 0 ?
                                                                    'border-red-500 bg-red-50 ring-2 ring-red-100' :
                                                                    'border-gray-100 bg-white hover:border-red-200'"
                                                                @click="addGift(gift.id)">
                                                                <div
                                                                    class="aspect-square w-full rounded-lg overflow-hidden mb-1.5 bg-gray-50">
                                                                    <img :src="gift.image"
                                                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                                                                </div>
                                                                <span
                                                                    class="text-[9px] font-bold text-center text-gray-700 line-clamp-1"
                                                                    x-text="gift.name"></span>

                                                                <button x-show="getGiftCount(gift.id) > 0"
                                                                    @click.stop="removeGift(gift.id)"
                                                                    class="absolute -top-2 -left-2 w-6 h-6 bg-white border border-gray-300 text-gray-700 rounded-full flex items-center justify-center shadow-md hover:bg-gray-100 z-10 transition-transform hover:scale-110">
                                                                    <i class="fas fa-minus text-[10px]"></i>
                                                                </button>
                                                                <div x-show="getGiftCount(gift.id) > 0"
                                                                    class="absolute top-1 right-1 bg-red-600 text-white w-5 h-5 rounded-full flex items-center justify-center shadow-sm">
                                                                    <span class="text-[10px] font-bold"
                                                                        x-text="getGiftCount(gift.id)"></span>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>

                                                <div x-show="quantity < promo.logic.required_qty"
                                                    class="text-center p-4 bg-gray-100/50 rounded-xl border border-dashed border-gray-300">
                                                    <p class="text-sm text-gray-500 font-medium italic">
                                                        เพิ่มอีก <span class="text-red-600 font-bold"
                                                            x-text="promo.logic.required_qty - quantity"></span> ชิ้น
                                                        เพื่อรับของแถมฟรี!
                                                    </p>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="promo.condition_type === 'all'">
                                            <div
                                                class="bg-white/60 backdrop-blur-sm p-5 rounded-xl border border-pink-100 mt-2">
                                                <div class="flex gap-3 items-start">
                                                    <div
                                                        class="w-10 h-10 shrink-0 bg-pink-100 rounded-full flex items-center justify-center text-pink-600">
                                                        <i class="fas fa-layer-group"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-gray-800 text-sm font-bold leading-tight">
                                                            โปรโมชั่นเซ็ตสุดคุ้ม!</p>
                                                        <p class="text-gray-600 text-xs mt-1">
                                                            ซื้อสินค้านี้ร่วมกับสินค้าอื่นที่ร่วมรายการครบชุด
                                                            รับของแถมฟรีทันที</p>
                                                        <div
                                                            class="mt-3 inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-[10px] font-bold">
                                                            <i class="fas fa-info-circle mr-1"></i>
                                                            เลือกของแถมได้ที่หน้าตะกร้าเมื่อครบเงื่อนไข
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- Partner Products (Upsell) --}}
                        <template x-if="promotions.some(p => p.partner_products && p.partner_products.length > 0)">
                            <div class="mb-8">
                                <h3 class="text-lg font-bold text-gray-800 mb-4">แนะนำทานคู่กับ:</h3>
                                <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
                                    <template x-for="promo in promotions" :key="'partner-' + promo.id">
                                        <template x-for="partner in promo.partner_products" :key="partner.id">
                                            <a :href="partner.url" class="flex-shrink-0 w-32 group">
                                                <div
                                                    class="aspect-square rounded-xl overflow-hidden mb-2 shadow-sm border border-gray-100 group-hover:shadow-md transition-all">
                                                    <img :src="partner.image"
                                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                                                </div>
                                                <p class="text-xs font-bold text-gray-800 truncate" x-text="partner.name">
                                                </p>
                                                <p class="text-xs text-red-600 font-black" x-text="'฿' + partner.price">
                                                </p>
                                            </a>
                                        </template>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- Review Images Section --}}
                        <template x-if="reviewImages.length > 0">
                            <div class="mt-12 pt-8 border-t border-gray-100">
                                <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-2">
                                    <i class="fas fa-camera-retro text-amber-500"></i>
                                    รีวิวจากลูกค้า
                                </h3>
                                <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
                                    <template x-for="(img, index) in reviewImages" :key="index">
                                        <div @click="openReviewModal(img)"
                                            class="aspect-square rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition-all cursor-pointer group">
                                            <img :src="img"
                                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        </div>
                                    </template>
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
                                :class="currentStock > 0 ? 'border-red-600 text-red-600 hover:bg-red-50' :
                                    'border-gray-300 bg-gray-200 text-gray-400 cursor-not-allowed'"
                                class="h-14 rounded-2xl border-2 font-bold transition-all flex items-center justify-center gap-2 text-lg">
                                <span x-show="!isLoading && currentStock > 0">ใส่ตะกร้า</span>
                                <span x-show="!isLoading && currentStock <= 0">สินค้าหมด</span>
                                <span x-show="isLoading" class="loading loading-spinner"></span>
                            </button>
                            <button @click="handleAddToCartClick(true)" :disabled="currentStock <= 0 || isLoading"
                                :class="currentStock > 0 ?
                                    'bg-red-600 text-white hover:bg-red-700 shadow-lg shadow-red-500/30' :
                                    'bg-gray-400 text-gray-100 cursor-not-allowed'"
                                class="h-14 rounded-2xl font-bold transition-all text-lg">
                                <span x-show="currentStock > 0">สั่งเลย</span>
                                <span x-show="currentStock <= 0">สินค้าหมด</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Image Gallery Fullscreen Modal --}}
        <template x-teleport="body">
            <div x-show="isModalOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/95 backdrop-blur-sm"
                @click="isModalOpen = false">
                <button @click.stop="isModalOpen = false"
                    class="absolute top-6 right-6 text-white/70 hover:text-white transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="relative w-full max-w-5xl aspect-square flex items-center justify-center pointer-events-none"
                    @click.stop>
                    <button x-show="images.length > 1" @click="prevImage()"
                        class="absolute left-0 lg:-left-16 z-10 p-3 rounded-full bg-white/10 text-white hover:bg-white/20 transition-all duration-300 pointer-events-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <img :src="activeImage" class="max-w-full max-h-[85vh] object-contain shadow-2xl pointer-events-auto"
                        @click.stop>

                    <button x-show="images.length > 1" @click="nextImage()"
                        class="absolute right-0 lg:-right-16 z-10 p-3 rounded-full bg-white/10 text-white hover:bg-white/20 transition-all duration-300 pointer-events-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </template>

        {{-- Review Image Fullscreen Modal --}}
        <template x-teleport="body">
            <div x-show="isReviewModalOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/95 backdrop-blur-sm"
                @click="closeReviewModal()">
                <button @click.stop="closeReviewModal()"
                    class="absolute top-6 right-6 text-white/70 hover:text-white transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="relative w-full max-w-5xl aspect-square flex items-center justify-center pointer-events-none"
                    @click.stop>
                    <button x-show="reviewImages.length > 1" @click="prevReviewImage()"
                        class="absolute left-0 lg:-left-16 z-10 p-3 rounded-full bg-white/10 text-white hover:bg-white/20 transition-all duration-300 pointer-events-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <img :src="activeReviewImage" class="max-w-full max-h-[85vh] object-contain shadow-2xl pointer-events-auto"
                        @click.stop>

                    <button x-show="reviewImages.length > 1" @click="nextReviewImage()"
                        class="absolute right-0 lg:-right-16 z-10 p-3 rounded-full bg-white/10 text-white hover:bg-white/20 transition-all duration-300 pointer-events-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </template>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productPage', (config) => ({
                isModalOpen: false,
                activeImage: config.initialImage,
                images: config.allImages,
                reviewImages: config.reviewImages || [],
                isReviewModalOpen: false,
                activeReviewImage: null,
                quantity: 1,
                isLoading: false,
                imagesLoaded: false,
                basePrice: config.initialBasePrice || 0,
                basePrice2: config.initialBasePrice2 || 0,
                initialStock: config.initialStock || 0,
                discountAmount: config.discountAmount || 0,
                options: config.options || [],
                selectedOption: null,
                promotions: (config.promotions || []).map(p => ({
                    ...p,
                    remainingTime: '',
                    isExpiringSoon: false
                })),
                selectedGifts: [],

                get giftLimit() {
                    if (this.promotions.length === 0) return 0;

                    const activePromo = this.promotions.find(p => {
                        if (p.condition_type === 'all') return false;
                        return this.quantity >= p.logic.required_qty;
                    });

                    if (!activePromo) return 0;
                    return activePromo.gifts_per_item * Math.floor(this.quantity / activePromo.logic
                        .required_qty);
                },

                get isConditionMet() {
                    return this.giftLimit > 0;
                },

                updateCountdowns() {
                    const now = new Date();
                    let hasExpired = false;

                    this.promotions = this.promotions.filter(promo => {
                        if (!promo.end_date) {
                            promo.remainingTime = 'ใช้งานได้เรื่อยๆ';
                            promo.isExpiringSoon = false;
                            return true;
                        }

                        const end = new Date(promo.end_date);
                        const diff = end - now;

                        if (diff <= 0) {
                            hasExpired = true;
                            return false; // ลบโปรโมชั่นนี้ออกทันที
                        }

                        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                        promo.isExpiringSoon = diff < (1000 * 60 * 60 * 24);

                        if (days > 0) {
                            promo.remainingTime = `${days} วัน ${hours} ชม. ${minutes} นาที`;
                        } else if (hours > 0) {
                            promo.remainingTime = `${hours} ชม. ${minutes} นาที ${seconds} วิ`;
                        } else {
                            promo.remainingTime = `${minutes} นาที ${seconds} วิ`;
                        }
                        return true;
                    });

                    if (hasExpired) {
                        this.validateSelection();
                        // ถ้าหลังจาก validate แล้วเงื่อนไขไม่ครบ ให้ล้างของแถม
                        if (!this.isConditionMet && this.selectedGifts.length > 0) {
                            this.selectedGifts = [];
                            Swal.fire({
                                title: 'โปรโมชั่นสิ้นสุดแล้ว',
                                text: 'ขออภัย โปรโมชั่นหมดเวลาแล้ว รายการของแถมถูกยกเลิก',
                                icon: 'warning',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        }
                    }
                },

                // เปลี่ยนระบบให้รองรับการเลือกของแถมเดิมซ้ำ
                addGift(giftId) {
                    if (this.selectedGifts.length < this.giftLimit) {
                        this.selectedGifts.push(giftId);
                    } else {
                        Swal.fire('สิทธิ์เต็มแล้ว',
                            `คุณสามารถเลือกของแถมได้สูงสุด ${this.giftLimit} ชิ้น`, 'warning');
                    }
                },
                removeGift(giftId) {
                    const index = this.selectedGifts.lastIndexOf(giftId);
                    if (index > -1) {
                        this.selectedGifts.splice(index, 1);
                    }
                },
                getGiftCount(giftId) {
                    return this.selectedGifts.filter(id => id === giftId).length;
                },

                init() {
                    this.imagesLoaded = true;
                    this.updateCountdowns();
                    setInterval(() => this.updateCountdowns(), 1000);

                    this.$watch('quantity', () => this.validateSelection());
                    this.$watch('giftLimit', () => this.validateSelection());

                    this.validateSelection();

                    this.$watch('selectedOption', (newId) => {
                        if (newId) {
                            const foundOption = this.options.find(o => o.id == newId);
                            if (foundOption && foundOption.image_url) {
                                this.activeImage = foundOption.image_url;
                            }
                        }
                    });
                },

                get currentStock() {
                    if (this.selectedOption) {
                        const option = this.options.find(o => o.id == this.selectedOption);
                        return option ? option.stock : 0;
                    }
                    return this.initialStock;
                },

                get finalPrice() {
                    if (this.options.length > 0 && !this.selectedOption)
                        return `฿${config.initialDisplayPrice}`;
                    if (this.selectedOption) {
                        const option = this.options.find(o => o.id == this.selectedOption);
                        if (option) return `฿${option.final_price.toLocaleString()}`;
                    }
                    let final = Math.max(0, this.basePrice - this.discountAmount);
                    return `฿${final.toLocaleString()}`;
                },

                get originalPriceDisplay() {
                    if (this.options.length > 0 && !this.selectedOption) return null;
                    if (this.selectedOption) {
                        const option = this.options.find(o => o.id == this.selectedOption);
                        if (option && option.price > option.final_price)
                            return `฿${option.price.toLocaleString()}`;
                        return null;
                    }
                    return this.discountAmount > 0 ? `฿${this.basePrice.toLocaleString()}` : null;
                },

                get discountDisplay() {
                    if (this.options.length > 0 && !this.selectedOption) return null;
                    if (this.selectedOption) {
                        const option = this.options.find(o => o.id == this.selectedOption);
                        if (option && option.price > option.final_price)
                            return `ประหยัด ฿${(option.price - option.final_price).toLocaleString()}`;
                        return null;
                    }
                    return this.discountAmount > 0 ?
                        `ประหยัด ฿${this.discountAmount.toLocaleString()}` : null;
                },

                prevImage() {
                    let idx = this.images.indexOf(this.activeImage);
                    this.activeImage = this.images[(idx - 1 + this.images.length) % this.images.length];
                },
                nextImage() {
                    let idx = this.images.indexOf(this.activeImage);
                    this.activeImage = this.images[(idx + 1) % this.images.length];
                },

                openReviewModal(img) {
                    this.activeReviewImage = img;
                    this.isReviewModalOpen = true;
                    document.body.style.overflow = 'hidden';
                },
                closeReviewModal() {
                    this.isReviewModalOpen = false;
                    document.body.style.overflow = 'auto';
                },
                prevReviewImage() {
                    let idx = this.reviewImages.indexOf(this.activeReviewImage);
                    this.activeReviewImage = this.reviewImages[(idx - 1 + this.reviewImages.length) % this.reviewImages.length];
                },
                nextReviewImage() {
                    let idx = this.reviewImages.indexOf(this.activeReviewImage);
                    this.activeReviewImage = this.reviewImages[(idx + 1) % this.reviewImages.length];
                },

                validateSelection() {
                    if (!this.isConditionMet) {
                        this.selectedGifts = [];
                        return;
                    }

                    if (this.selectedGifts.length > this.giftLimit) {
                        this.selectedGifts = this.selectedGifts.slice(0, this.giftLimit);
                    }

                    // Auto-fill ถ้ามีของแถมแค่ชนิดเดียว 
                    const activePromo = this.promotions.find(p => p.condition_type !== 'all' && this
                        .quantity >= p.logic.required_qty);
                    if (activePromo && activePromo.gifts.length === 1) {
                        const giftId = activePromo.gifts[0].id;
                        while (this.selectedGifts.length < this.giftLimit) {
                            this.selectedGifts.push(giftId);
                        }
                    }
                },

                async handleAddToCartClick(isBuyNow) {
                    if (this.currentStock <= 0) return;
                    if (this.options.length > 0 && !this.selectedOption) {
                        Swal.fire('กรุณาเลือกตัวเลือก', 'เลือกสินค้าก่อนหยิบใส่ตะกร้า', 'warning');
                        return;
                    }

                    if (this.isConditionMet && this.selectedGifts.length < this.giftLimit) {
                        const remaining = this.giftLimit - this.selectedGifts.length;
                        Swal.fire({
                            title: 'รับของแถมฟรี!',
                            text: `คุณยังเลือกของแถมไม่ครบ (ขาดอีก ${remaining} ชิ้น) ต้องการเลือกให้ครบก่อนไหม?`,
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'เลือกของแถม',
                            cancelButtonText: 'ไม่เป็นไร (สละสิทธิ์)',
                            confirmButtonColor: '#d33',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.querySelector('.bg-gradient-to-br')
                                    .scrollIntoView({
                                        behavior: 'smooth'
                                    });
                            } else {
                                this.proceedAddToCart(isBuyNow);
                            }
                        });
                        return;
                    }

                    this.proceedAddToCart(isBuyNow);
                },

                async proceedAddToCart(isBuyNow) {
                    if (this.isLoading) return;
                    this.isLoading = true;
                    try {
                        const response = await fetch(config.standardAction, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                quantity: this.quantity,
                                selected_option_id: this.selectedOption,
                                selected_gift_ids: this.selectedGifts
                            })
                        });

                        if (!response.ok) {
                            const errorData = await response.json().catch(() => ({
                                message: 'Server error (' + response.status + ')'
                            }));
                            throw new Error(errorData.message || 'Something went wrong');
                        }

                        const data = await response.json();
                        if (data.success) {
                            if (isBuyNow) window.location.href = config.cartUrl;
                            else {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'เพิ่มสินค้าแล้ว',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                Livewire.dispatch('cartUpdated');
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
