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

        $isBogo =
            ($product->is_bogo_active ?? false) &&
            isset($product->bogoFreeOptions) &&
            $product->bogoFreeOptions->isNotEmpty();

        // เช็คว่ามีตัวเลือกสินค้าหรือไม่
        $hasOptions = isset($product->options) && $product->options->isNotEmpty();
    @endphp

    <div x-data="productPage({
        initialImage: '{{ $activeImageUrl }}',
        allImages: {{ json_encode($allImagePaths) }},
        isBogo: {{ $isBogo ? 'true' : 'false' }},
        productId: {{ $product->id }},
        bogoAction: '{{ route('cart.add.bogo') }}',
        standardAction: '{{ route('cart.add', ['id' => $product->id]) }}',
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
                        @if ($isBogo)
                            <div
                                class="absolute top-4 right-4 badge badge-primary text-white gap-1 text-sm font-bold shadow-md px-3 py-1">
                                <i class="fas fa-gift mr-1"></i> 1 แถม 1
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
                                        'border-emerald-500 ring-2 ring-emerald-100': activeImage ===
                                            image,
                                        'border-transparent': activeImage !== image
                                    }">
                                    <img :src="image" class="w-full h-full object-cover">
                                </div>
                            </template>
                        </div>
                    @endif

                    {{-- Detailed Description (Desktop) --}}
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
                            @if (isset($product->brand_name))
                                <span
                                    class="bg-gray-100 px-2 py-0.5 rounded text-gray-600">{{ $product->brand_name }}</span>
                            @endif
                            <span class="text-gray-400">|</span>
                            <span>รหัส: {{ $product->pd_code }}</span>
                            <span class="text-gray-400">|</span>
                            <span class="{{ $product->quantity > 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ $product->quantity > 0 ? 'มีสินค้า (' . $product->quantity . ')' : 'สินค้าหมด' }}
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

                        {{-- ★★★ ส่วนที่เพิ่มคืนมา: ตัวเลือกสินค้า (Options) ★★★ --}}
                        @if ($hasOptions)
                            <div class="mb-6">
                                <h3 class="text-sm font-bold text-gray-900 mb-3">ตัวเลือกสินค้า:</h3>
                                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                                    @foreach ($product->options as $option)
                                        <a href="{{ route('product.show', $option->pd_sp_id) }}"
                                            class="group relative border rounded-lg p-2 hover:border-emerald-500 hover:shadow-md transition-all bg-white text-center">

                                            {{-- รูปตัวเลือก --}}
                                            <div class="aspect-square bg-gray-100 rounded mb-2 overflow-hidden">
                                                @php
                                                    $optImg = 'https://via.placeholder.com/150?text=No+Image';
                                                    if ($option->images && $option->images->isNotEmpty()) {
                                                        $optPrimary =
                                                            $option->images->sortByDesc('img_sort')->first() ??
                                                            $option->images->first();
                                                        $optImg = asset('storage/' . $optPrimary->img_path);
                                                    }
                                                @endphp
                                                <img src="{{ $optImg }}"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            </div>

                                            <div class="text-xs font-semibold text-gray-800 truncate">
                                                {{ $option->pd_sp_name }}</div>
                                            <div class="text-xs text-emerald-600 font-bold">
                                                ฿{{ number_format($option->pd_sp_price - ($option->pd_sp_discount ?? 0)) }}
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        {{-- ★★★ จบส่วนที่เพิ่ม ★★★ --}}

                    </div>

                    {{-- Add to Cart Section --}}
                    <div class="mt-auto border-t border-gray-100 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="font-bold text-gray-700">จำนวน:</span>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4">
                            {{-- Quantity Input --}}
                            <div
                                class="flex items-center border border-gray-300 rounded-lg h-12 w-full sm:w-32 bg-white shadow-sm">
                                <button type="button" @click="quantity > 1 ? quantity-- : null"
                                    class="w-10 h-full text-gray-500 hover:bg-gray-100 hover:text-emerald-600 text-xl font-bold rounded-l transition">-</button>
                                <input type="number" name="quantity" x-model="quantity"
                                    class="w-full h-full text-center border-none focus:ring-0 text-gray-900 font-bold text-lg m-0"
                                    readonly>
                                <button type="button" @click="quantity++"
                                    class="w-10 h-full text-gray-500 hover:bg-gray-100 hover:text-emerald-600 text-xl font-bold rounded-r transition">+</button>
                            </div>

                            {{-- Buttons --}}
                            <div class="flex-1 grid grid-cols-2 gap-3">
                                <button type="button" @click="handleAddToCartClick(false)"
                                    :disabled="isLoading || {{ $product->quantity <= 0 ? 'true' : 'false' }}"
                                    class="btn btn-outline border-emerald-600 text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-700 font-bold text-base rounded-lg h-12">
                                    <template x-if="{{ $product->quantity > 0 ? 'true' : 'false' }}">
                                        <span class="flex items-center justify-center gap-1">
                                            <i class="fas fa-shopping-cart text-lg"></i>
                                            <span class="hidden sm:inline">ใส่ตะกร้า</span>
                                            <span class="sm:hidden">ใส่ตะกร้า</span>
                                        </span>
                                    </template>
                                    <template x-if="{{ $product->quantity <= 0 ? 'true' : 'false' }}">
                                        <span>สินค้าหมด</span>
                                    </template>
                                </button>

                                <button type="button" @click="handleAddToCartClick(true)"
                                    :disabled="isLoading || {{ $product->quantity <= 0 ? 'true' : 'false' }}"
                                    class="btn bg-emerald-600 hover:bg-emerald-700 border-none text-white font-bold text-base rounded-lg h-12 shadow-lg shadow-emerald-200/50">
                                    <span x-show="!isLoading" class="flex items-center gap-2">
                                        <i class="fas fa-bolt"></i> {{ $product->quantity > 0 ? 'ซื้อเลย' : 'หมด' }}
                                    </span>
                                    <span x-show="isLoading" class="loading loading-spinner loading-sm"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- BOGO Section --}}
                    @if ($isBogo)
                        <div class="mt-6 pt-6 border-t border-dashed border-gray-200">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="badge badge-primary">แถมฟรี</span>
                                <span class="text-sm text-gray-600">เลือกของแถม 1 ชิ้น:</span>
                            </div>
                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                                @foreach ($product->bogoFreeOptions as $freebie)
                                    <div @click="selectedFreebieId = {{ $freebie->pd_sp_id }}"
                                        class="relative cursor-pointer group rounded-lg overflow-hidden border-2 transition-all duration-200"
                                        :class="selectedFreebieId == {{ $freebie->pd_sp_id }} ?
                                            'border-emerald-500 ring-2 ring-emerald-100 ring-offset-1' :
                                            'border-gray-100 hover:border-emerald-300'">

                                        <div x-show="selectedFreebieId == {{ $freebie->pd_sp_id }}"
                                            class="absolute top-1 right-1 z-10 bg-emerald-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs shadow-sm">
                                            <i class="fas fa-check"></i>
                                        </div>

                                        <div class="aspect-square bg-gray-50">
                                            @php
                                                $freebieImage =
                                                    $freebie->images->sortByDesc('img_sort')->first() ??
                                                    $freebie->images->first();
                                                $freebieImagePath = $freebieImage
                                                    ? asset('storage/' . $freebieImage->img_path)
                                                    : 'https://via.placeholder.com/300x300.png?text=No+Image';
                                            @endphp
                                            <img src="{{ $freebieImagePath }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="p-2 bg-white text-center">
                                            <p class="text-xs font-medium text-gray-700 truncate">
                                                {{ $freebie->pd_sp_name }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Details (Mobile) --}}
                    <div class="block lg:hidden mt-8 pt-8 border-t border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 border-b-2 border-emerald-500 inline-block pb-1 mb-4">
                            รายละเอียดสินค้า
                        </h3>
                        <div class="prose prose-sm text-gray-700 leading-7">
                            {{ $product->pd_sp_details ?? ($product->pd_details ?? $product->pd_name) }}
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
                    selectedFreebieId: null,
                    isLoading: false,
                    isBogo: config.isBogo,

                    handleAddToCartClick(isBuyNow = false) {
                        if (this.isBogo) {
                            if (!this.selectedFreebieId) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'กรุณาเลือกของแถม',
                                    text: 'โปรดเลือกสินค้าแถม 1 ชิ้นก่อนดำเนินการต่อ',
                                    confirmButtonText: 'ตกลง',
                                    confirmButtonColor: '#10b981'
                                });
                                return;
                            }
                            this.addBogoToCart(this.selectedFreebieId, isBuyNow);
                        } else {
                            this.standardAddToCart(isBuyNow);
                        }
                    },

                    standardAddToCart(isBuyNow) {
                        const payload = {
                            quantity: this.quantity
                        };
                        this.performAjaxAddToCart(config.standardAction, payload, isBuyNow);
                    },

                    addBogoToCart(freebieId, isBuyNow) {
                        const payload = {
                            quantity: this.quantity,
                            main_product_id: config.productId,
                            free_product_id: freebieId
                        };
                        this.performAjaxAddToCart(config.bogoAction, payload, isBuyNow);
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
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    if (isBuyNow) {
                                        window.location.href = config.checkoutUrl;
                                    } else {
                                        if (window.flyToCart) window.flyToCart(document.querySelector(
                                            '.btn-outline'));

                                        const Toast = Swal.mixin({
                                            toast: true,
                                            position: 'top-end',
                                            showConfirmButton: false,
                                            timer: 2000,
                                            timerProgressBar: true,
                                            didOpen: (toast) => {
                                                toast.addEventListener('mouseenter', Swal
                                                    .stopTimer)
                                                toast.addEventListener('mouseleave', Swal
                                                    .resumeTimer)
                                            }
                                        });

                                        Toast.fire({
                                            icon: 'success',
                                            title: 'เพิ่มลงตะกร้าแล้ว'
                                        });

                                        if (window.updateCartBadge) setTimeout(() => window
                                            .updateCartBadge(data.cartCount), 500);
                                    }
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'เกิดข้อผิดพลาด',
                                        text: data.message || 'ไม่สามารถเพิ่มสินค้าได้',
                                        confirmButtonColor: '#10b981'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Connection Error',
                                    text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้'
                                });
                            })
                            .finally(() => {
                                this.isLoading = false;
                            });
                    }
                }));
            });
        </script>
    @endpush
@endsection
