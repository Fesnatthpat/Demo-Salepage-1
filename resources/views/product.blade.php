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
        $activeImageUrl = $product->cover_image_url ?? 'https://via.placeholder.com/600x600.png?text=No+Image';

        if (isset($product->images) && $product->images->isNotEmpty()) {
            $allImagePaths = $product->images->pluck('image_url')->values()->all();
        } else {
            $allImagePaths[] = $activeImageUrl;
        }

        $hasOptions = isset($product->options) && $product->options->isNotEmpty();
    @endphp

    <div x-data="productPage({
        initialImage: '{{ $activeImageUrl }}',
        allImages: {{ json_encode($allImagePaths) }},
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
                            {!! nl2br(e($product->pd_sp_details ?? ($product->pd_details ?? $product->pd_name))) !!}
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

                        {{-- ★★★ GIFT SECTION (เพิ่มส่วนนี้) ★★★ --}}
                        @if (isset($giftableProducts) && $giftableProducts->isNotEmpty())
                            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl shadow-sm">
                                <div class="flex items-center gap-2 mb-3 pb-2 border-b border-red-100">
                                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">PROMOTION</span>
                                    <h3 class="font-bold text-gray-800">ของแถมสุดพิเศษ</h3>
                                </div>
                                <div class="space-y-3">
                                    @foreach ($giftableProducts as $gift)
                                        <div
                                            class="flex items-center gap-3 bg-white p-3 rounded-lg border border-red-100 shadow-sm relative overflow-hidden">
                                            {{-- Gift Image --}}
                                            <div
                                                class="w-14 h-14 bg-gray-100 rounded border border-gray-100 flex-shrink-0 overflow-hidden">
                                                @php
                                                    $giftImg = $gift->images->first();
                                                    $giftImgUrl = $giftImg
                                                        ? $giftImg->img_path ?? $giftImg->image_path
                                                        : null;
                                                    if ($giftImgUrl && !filter_var($giftImgUrl, FILTER_VALIDATE_URL)) {
                                                        $giftImgUrl = asset(
                                                            'storage/' . str_replace('storage/', '', $giftImgUrl),
                                                        );
                                                    }
                                                @endphp
                                                <img src="{{ $giftImgUrl ?? 'https://via.placeholder.com/64?text=Gift' }}"
                                                    class="w-full h-full object-cover" alt="Gift">
                                            </div>

                                            {{-- Gift Info --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-bold text-gray-800 truncate">
                                                    {{ $gift->pd_sp_name }}</div>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span
                                                        class="text-xs text-gray-400 line-through">฿{{ number_format($gift->pd_sp_price) }}</span>
                                                    <span
                                                        class="text-xs font-bold text-red-600 bg-red-100 px-2 py-0.5 rounded-full">
                                                        ฟรี {{ $gift->gift_quantity ?? 1 }} ชิ้น
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        {{-- ★★★ END GIFT SECTION ★★★ --}}

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
                    isLoading: false,

                    handleAddToCartClick(isBuyNow) {
                        this.performAjaxAddToCart(config.standardAction, {
                            quantity: this.quantity
                        }, isBuyNow);
                    },

                    performAjaxAddToCart(url, data, isBuyNow) {
                        this.isLoading = true;
                        const formData = new FormData();
                        for (const key in data) {
                            formData.append(key, data[key]);
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
