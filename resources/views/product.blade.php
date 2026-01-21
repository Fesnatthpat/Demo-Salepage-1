@extends('layout')

@section('title', $product->pd_name . ' | Salepage Demo')

@section('content')
    @php
        $originalPrice = (float) ($product->pd_price ?? 0);
        $discountAmount = (float) ($product->pd_sp_discount ?? 0);
        $finalSellingPrice = max(0, $originalPrice - $discountAmount);
        $isOnSale = $discountAmount > 0;

        // --- Image Logic ---
        $allImagePaths = [];
        $activeImageUrl = 'https://via.placeholder.com/600x600.png?text=No+Image';

        if (isset($product->images) && $product->images->isNotEmpty()) {
            $sortedImages = $product->images->sortByDesc('img_sort');
            $allImagePaths = $sortedImages
                ->pluck('img_path')
                ->map(fn($path) => asset('storage/' . $path))
                ->values()
                ->all();
            $activeImageUrl = $allImagePaths[0] ?? $activeImageUrl;
        } else {
            $allImagePaths[] = $activeImageUrl;
        }

        // --- Unified Promotion Logic (แก้ไขใหม่ให้แม่นยำ) ---
        $promoName = '';
        $freebieOptions = collect();
        $quantityToBuy = 1; // Default
        $quantityToGet = 0;
        $hasActivePromotions = $product->active_promotions && $product->active_promotions->isNotEmpty();

        if ($hasActivePromotions) {
            $firstActivePromo = $product->active_promotions->first();
            $promoName = $firstActivePromo->name;

            // ★ แก้ไข: ค้นหา Rule ที่เป็นของสินค้านี้จริงๆ (ไม่เอาตัวแรกมั่วๆ)
            $specificRule = $firstActivePromo->rules->firstWhere('product_id', $product->pd_sp_id);
            if ($specificRule) {
                // ใช้ Accessor getQuantityAttribute ที่เราสร้างใน Model
                $quantityToBuy = $specificRule->quantity > 0 ? $specificRule->quantity : 1;
            }

            // Action (ของแถม) - ตรวจสอบสำหรับ 'free_gift_selection'
            if (isset($giftableProducts) && $giftableProducts->isNotEmpty()) {
                $freebieOptions = $giftableProducts;
                // หาจำนวนของแถมที่เลือกได้
                foreach ($product->active_promotions as $promo) {
                    $giftAction = $promo->actions->firstWhere('type', 'free_gift_selection');
                    if ($giftAction) {
                        $quantityToGet = $giftAction->actions['quantity_to_get'] ?? 0;
                        break; // เจอแล้วหยุดเลย
                    }
                }
            } else { // Logic เดิมสำหรับ BOGO
                $firstAction = $firstActivePromo->actions->first();
                if ($firstAction) {
                    $quantityToGet = $firstAction->quantity > 0 ? $firstAction->quantity : 0;
                }
                // รวบรวมของแถมทั้งหมดจากทุก Action
                foreach ($product->active_promotions as $promo) {
                    if ($promo->actions->isNotEmpty()) {
                        foreach ($promo->actions as $action) {
                            if ($action->productToGet) {
                                $freebieOptions->push($action->productToGet);
                            }
                        }
                    }
                }
            }
        }

        // กรองของแถมซ้ำ
        $freebieOptions = $freebieOptions->unique('pd_sp_id');

        // เช็คว่าจะโชว์ส่วนเลือกของแถมไหม
        $showFreebieSelection = $quantityToGet > 0 && $quantityToBuy > 0 && $freebieOptions->isNotEmpty();


        $hasOptions = isset($product->options) && $product->options->isNotEmpty();
    @endphp

    <div x-data="productPage({
        initialImage: '{{ $activeImageUrl }}',
        allImages: {{ json_encode($allImagePaths) }},
        hasFreebieSelection: {{ $showFreebieSelection ? 'true' : 'false' }},
        productId: {{ $product->pd_sp_id }},
        {{-- แก้ไข: ใช้ pd_sp_id --}}
        quantityToBuy: {{ $quantityToBuy }},
        quantityToGet: {{ $quantityToGet }},
        bogoAction: '{{ route('cart.add.bogo') }}',
        standardAction: '{{ route('cart.add', ['id' => $product->pd_sp_id]) }}',
        checkoutUrl: '{{ route('payment.checkout') }}'
    })" class="container mx-auto px-4 py-8">

        <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">

                {{-- Left Column: Image Gallery --}}
                <div class="p-6 lg:p-10 lg:border-r border-gray-200">
                    <div
                        class="w-full relative aspect-square lg:aspect-[4/3] overflow-hidden rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                        <img :src="activeImage" class="w-full h-full object-contain" alt="{{ $product->pd_name }}">
                        @if ($isOnSale)
                            <div
                                class="absolute top-4 left-4 badge badge-error text-white gap-1 text-sm font-bold shadow-md px-3 py-1">
                                ลด ฿{{ number_format($discountAmount) }}
                            </div>
                        @endif
                        <template x-if="showFreebieSelectionSection">
                            <div
                                class="absolute top-4 right-4 badge badge-primary text-white gap-1 text-sm font-bold shadow-md px-3 py-1 animate-pulse">
                                <i class="fas fa-gift mr-1"></i> แถม <span x-text="freebieLimit"></span> ชิ้น
                            </div>
                        </template>
                    </div>

                    {{-- Thumbnails --}}
                    @if (count($allImagePaths) > 1)
                        <div class="grid grid-cols-5 gap-2 mt-4">
                            <template x-for="image in images" :key="image">
                                <div @click="activeImage = image"
                                    class="aspect-square rounded-md overflow-hidden cursor-pointer border-2 transition-all hover:opacity-80"
                                    :class="{
                                        'border-emerald-500 ring-2 ring-emerald-100': activeImage === image,
                                        'border-transparent': activeImage !== image
                                    }">
                                    <img :src="image" class="w-full h-full object-cover">
                                </div>
                            </template>
                        </div>
                    @endif

                    {{-- Description --}}
                    <div class="hidden lg:block mt-8 pt-8 border-t border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 border-b-2 border-emerald-500 inline-block pb-1 mb-4">
                            รายละเอียดสินค้า
                        </h3>
                        <div class="prose prose-sm text-gray-700 leading-7">
                            {{ $product->pd_sp_details ?? ($product->pd_details ?? $product->pd_name) }}
                        </div>
                    </div>
                </div>

                {{-- Right Column: Product Details --}}
                <div class="p-6 lg:p-10 flex flex-col h-full">
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 leading-tight">{{ $product->pd_name }}</h1>
                        <div class="mt-2 text-sm text-gray-500 flex flex-wrap gap-2 items-center">
                            <span>รหัส: {{ $product->pd_code }}</span>
                            <span class="text-gray-400">|</span>
                            <span class="{{ $product->pd_sp_stock > 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ $product->pd_sp_stock > 0 ? 'มีสินค้า (' . $product->pd_sp_stock . ')' : 'สินค้าหมด' }}
                            </span>
                        </div>

                        <div class="bg-emerald-50/50 p-5 rounded-xl mb-6 border border-emerald-100 mt-6">
                            <div class="flex items-end gap-3">
                                @if ($isOnSale)
                                    <h2 class="text-4xl font-bold text-emerald-600 leading-none">
                                        ฿{{ number_format($finalSellingPrice) }}</h2>
                                    <span
                                        class="text-xl text-gray-400 font-normal line-through mb-1">฿{{ number_format($originalPrice) }}</span>
                                    <span
                                        class="badge badge-error badge-outline text-xs mb-2">-{{ number_format(($discountAmount / $originalPrice) * 100) }}%</span>
                                @else
                                    <h2 class="text-4xl font-bold text-emerald-600 leading-none">
                                        ฿{{ number_format($finalSellingPrice) }}</h2>
                                @endif
                            </div>
                        </div>

                        {{-- Options Section --}}
                        @if ($hasOptions)
                            <div class="mb-6">
                                <h3 class="text-sm font-bold text-gray-900 mb-3">ตัวเลือกสินค้า:</h3>
                                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                                    @foreach ($product->options as $option)
                                        <a href="{{ route('product.show', $option->pd_sp_id) }}"
                                            class="group relative border rounded-lg p-2 hover:border-emerald-500 hover:shadow-md transition-all bg-white text-center">
                                            <div class="text-xs font-semibold text-gray-800 truncate">
                                                {{ $option->pd_sp_name }}</div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Add to Cart Section --}}
                    <div class="mt-auto border-t border-gray-100 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="font-bold text-gray-700">จำนวน:</span>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <div
                                class="flex items-center border border-gray-300 rounded-lg h-12 w-full sm:w-32 bg-white shadow-sm">
                                <button type="button" @click="quantity > 1 ? quantity-- : null"
                                    class="w-10 h-full text-gray-500 hover:bg-gray-100 hover:text-emerald-600 text-xl font-bold rounded-l transition">-</button>
                                <input type="number" name="quantity" x-model.number="quantity"
                                    class="w-full h-full text-center border-none focus:ring-0 text-gray-900 font-bold text-lg m-0"
                                    readonly>
                                <button type="button" @click="quantity++"
                                    class="w-10 h-full text-gray-500 hover:bg-gray-100 hover:text-emerald-600 text-xl font-bold rounded-r transition">+</button>
                            </div>

                            <div class="flex-1 grid grid-cols-2 gap-3">
                                <button type="button" @click="handleAddToCartClick(false)"
                                    :disabled="isLoading || {{ $product->pd_sp_stock <= 0 ? 'true' : 'false' }}"
                                    class="btn btn-outline border-emerald-600 text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-700 font-bold text-base rounded-lg h-12">
                                    <span class="flex items-center justify-center gap-1">
                                        <i class="fas fa-shopping-cart text-lg"></i>
                                        <span>ใส่ตะกร้า</span>
                                    </span>
                                </button>

                                <button type="button" @click="handleAddToCartClick(true)"
                                    :disabled="isLoading || {{ $product->pd_sp_stock <= 0 ? 'true' : 'false' }}"
                                    class="btn bg-emerald-600 hover:bg-emerald-700 border-none text-white font-bold text-base rounded-lg h-12 shadow-lg shadow-emerald-200/50">
                                    <span x-show="!isLoading">
                                        <i class="fas fa-bolt mr-1"></i> ซื้อเลย
                                    </span>
                                    <span x-show="isLoading" class="loading loading-spinner loading-sm"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ★★★ Freebies Selection Section ★★★ --}}
                    <template x-if="showFreebieSelectionSection">
                        <div class="mt-6 pt-6 border-t border-dashed border-gray-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-primary">แถมฟรี</span>
                                    <span class="text-sm font-bold text-gray-700">
                                        {{ $promoName }}: เลือกของแถม <span x-text="freebieLimit"></span> ชิ้น
                                    </span>
                                </div>
                                <span class="text-xs font-medium"
                                    :class="selectedFreebies.length == freebieLimit ? 'text-emerald-600' : 'text-gray-500'">
                                    เลือกแล้ว <span x-text="selectedFreebies.length"></span>/<span
                                        x-text="freebieLimit"></span>
                                </span>
                            </div>

                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                                @foreach ($freebieOptions as $freebie)
                                    <div @click="toggleFreebie({{ $freebie->pd_sp_id }})"
                                        class="relative cursor-pointer group rounded-lg overflow-hidden border-2 transition-all duration-200 bg-white shadow-sm"
                                        :class="selectedFreebies.includes({{ $freebie->pd_sp_id }}) ?
                                            'border-emerald-500 ring-2 ring-emerald-100 ring-offset-1 transform scale-[1.02]' :
                                            'border-gray-100 hover:border-emerald-300'">

                                        {{-- Checkmark --}}
                                        <div x-show="selectedFreebies.includes({{ $freebie->pd_sp_id }})"
                                            class="absolute top-1 right-1 z-10 bg-emerald-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs shadow-sm">
                                            <i class="fas fa-check"></i>
                                        </div>

                                        {{-- Image --}}
                                        <div class="aspect-square bg-gray-50 relative">
                                            @php
                                                $freebieImage = $freebie->images->sortByDesc('img_sort')->first();
                                                $freebieImagePath = $freebieImage
                                                    ? asset('storage/' . $freebieImage->img_path)
                                                    : 'https://via.placeholder.com/300x300.png?text=No+Image';
                                            @endphp
                                            <img src="{{ $freebieImagePath }}" class="w-full h-full object-cover">
                                            <div x-show="selectedFreebies.includes({{ $freebie->pd_sp_id }})"
                                                class="absolute inset-0 bg-emerald-500/10"></div>
                                        </div>

                                        <div class="p-2 text-center">
                                            <p class="text-xs font-medium text-gray-700 truncate">
                                                {{ $freebie->pd_sp_name }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:initializing', () => {
                Alpine.data('productPage', (config) => ({
                    activeImage: config.initialImage,
                    images: config.allImages,
                    quantity: 1,
                    selectedFreebies: [],
                    hasFreebieSelection: config.hasFreebieSelection,
                    isLoading: false,

                    quantityToBuy: parseInt(config.quantityToBuy), // Ensure Number
                    quantityToGet: parseInt(config.quantityToGet), // Ensure Number

                    get freebieLimit() {
                        if (!this.hasFreebieSelection || this.quantityToBuy <= 0) return 0;
                        const timesApplied = Math.floor(this.quantity / this.quantityToBuy);
                        return timesApplied * this.quantityToGet;
                    },

                    get showFreebieSelectionSection() {
                        return this.hasFreebieSelection && this.freebieLimit > 0;
                    },

                    init() {
                        this.$watch('freebieLimit', (newLimit) => {
                            if (newLimit < this.selectedFreebies.length) {
                                this.selectedFreebies.splice(newLimit);
                            }
                        });
                    },

                    toggleFreebie(id) {
                        const index = this.selectedFreebies.indexOf(id);
                        if (index > -1) {
                            this.selectedFreebies.splice(index, 1);
                        } else {
                            if (this.selectedFreebies.length < this.freebieLimit) {
                                this.selectedFreebies.push(id);
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'ครบจำนวนแล้ว',
                                    text: `คุณเลือกของแถมครบ ${this.freebieLimit} ชิ้นแล้ว`,
                                    confirmButtonColor: '#10b981'
                                });
                            }
                        }
                    },

                    handleAddToCartClick(isBuyNow) {
                        if (this.hasFreebieSelection) {
                            if (this.quantity < this.quantityToBuy) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'ซื้อเพิ่มอีกนิด',
                                    text: `ซื้อครบ ${this.quantityToBuy} ชิ้น เพื่อรับของแถมฟรี!`,
                                    confirmButtonColor: '#10b981'
                                });
                                return;
                            }
                            if (this.selectedFreebies.length !== this.freebieLimit) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'เลือกของแถมไม่ครบ',
                                    text: `กรุณาเลือกของแถมให้ครบ ${this.freebieLimit} ชิ้น (ขาดอีก ${this.freebieLimit - this.selectedFreebies.length} ชิ้น)`,
                                    confirmButtonColor: '#10b981'
                                });
                                return;
                            }
                            this.addWithFreebies(isBuyNow);
                        } else {
                            this.standardAddToCart(isBuyNow);
                        }
                    },

                    standardAddToCart(isBuyNow) {
                        this.performAjaxAddToCart(config.standardAction, {
                            quantity: this.quantity
                        }, isBuyNow);
                    },

                    addWithFreebies(isBuyNow) {
                        this.performAjaxAddToCart(config.bogoAction, {
                            quantity: this.quantity,
                            main_product_id: config.productId,
                            free_product_ids: this.selectedFreebies
                        }, isBuyNow);
                    },

                    performAjaxAddToCart(url, data, isBuyNow) {
                        this.isLoading = true;
                        const formData = new FormData();
                        for (const key in data) {
                            if (Array.isArray(data[key])) {
                                data[key].forEach(val => formData.append(`${key}[]`, val));
                            } else {
                                formData.append(key, data[key]);
                            }
                        }

                        fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    if (isBuyNow) window.location.href = config.checkoutUrl;
                                    else {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'เพิ่มลงตะกร้าแล้ว',
                                            showConfirmButton: false,
                                            timer: 1500
                                        });
                                        if (window.updateCartBadge) window.updateCartBadge(data
                                            .cartCount);
                                    }
                                } else {
                                    throw new Error(data.message);
                                }
                            })
                            .catch(err => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: err.message
                                });
                            })
                            .finally(() => this.isLoading = false);
                    }
                }));
            });
        </script>
    @endpush
@endsection
