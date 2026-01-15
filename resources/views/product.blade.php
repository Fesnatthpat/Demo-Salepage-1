@extends('layout')

@section('title', $product->pd_name . ' | Salepage Demo')

@section('content')
    @php
        $originalPrice = (float) ($product->pd_price ?? 0);
        $discountAmount = (float) ($product->pd_sp_discount ?? 0);
        $finalSellingPrice = max(0, $originalPrice - $discountAmount);
        $isOnSale = $discountAmount > 0;

        // --- Refactored Image Logic (แก้ไขใหม่) ---
        $allImagePaths = [];
        $activeImageUrl = 'https://via.placeholder.com/600x600.png?text=No+Image';

        if (isset($product->images) && $product->images->isNotEmpty()) {
            // เรียงลำดับรูปภาพ: เอา img_sort มากสุด (1) ขึ้นก่อน
            $sortedImages = $product->images->sortByDesc('img_sort');

            $allImagePaths = $sortedImages
                ->pluck('img_path') // แก้ไข: ใช้ img_path ตาม DB
                ->map(fn($path) => asset('storage/' . $path))
                ->values()
                ->all();
            
            // รูปแรกสุดคือรูปปก
            $activeImageUrl = $allImagePaths[0] ?? $activeImageUrl;
        } else {
            $allImagePaths[] = $activeImageUrl;
        }

        $isBogo =
            ($product->is_bogo_active ?? false) && 
            isset($product->bogoFreeOptions) && 
            $product->bogoFreeOptions->isNotEmpty();
    @endphp

    <div x-data="productPage({
        initialImage: '{{ $activeImageUrl }}',
        allImages: {{ json_encode($allImagePaths) }},
        isBogo: {{ $isBogo ? 'true' : 'false' }},
        productId: {{ $product->id }},
        bogoAction: '{{ route('cart.add.bogo') }}',
        standardAction: '{{ route('cart.add', ['id' => $product->id]) }}'
    })" class="container mx-auto px-4 py-8">

        <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">

                {{-- Image Gallery --}}
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
                                <i class="fas fa-gift"></i> 1 แถม 1
                            </div>
                        @endif
                    </div>
                    <div class="grid grid-cols-5 gap-2 mt-4">
                        <template x-for="image in images" :key="image">
                            <div @click="activeImage = image"
                                class="aspect-square rounded-md overflow-hidden cursor-pointer border-2"
                                :class="{ 'border-emerald-500': activeImage === image, 'border-transparent': activeImage !==
                                        image }">
                                <img :src="image" class="w-full h-full object-cover">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Product Details --}}
                <div class="p-6 lg:p-10">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 leading-tight">{{ $product->pd_name }}</h1>
                    <div class="mt-2 text-sm text-gray-500">
                        @if (isset($product->brand_name))
                            <span class="font-semibold">Brand:</span> {{ $product->brand_name }} |
                        @endif
                        <span class="font-semibold">Code:</span> {{ $product->pd_code }} |
                        <span class="font-semibold">Stock:</span> {{ $product->quantity }}
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg mb-6 border border-gray-100 mt-4">
                        <h2 class="text-3xl lg:text-4xl font-bold text-emerald-600 flex items-end gap-3">
                            @if ($isOnSale)
                                <span>฿{{ number_format($finalSellingPrice) }}</span>
                                <span
                                    class="text-lg text-gray-400 font-normal line-through">฿{{ number_format($originalPrice) }}</span>
                            @else
                                <span>฿{{ number_format($finalSellingPrice) }}</span>
                            @endif
                        </h2>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <div class="flex items-center border border-gray-300 rounded h-12 w-full sm:w-32 bg-white">
                            <button type="button" @click="quantity > 1 ? quantity-- : null"
                                class="w-10 h-full text-gray-500 hover:bg-gray-100 text-xl font-bold rounded-l">-</button>
                            <input type="number" name="quantity" x-model="quantity"
                                class="w-full h-full text-center border-none focus:ring-0 text-gray-900 font-bold text-lg m-0"
                                readonly>
                            <button type="button" @click="quantity++"
                                class="w-10 h-full text-gray-500 hover:bg-gray-100 text-xl font-bold rounded-r">+</button>
                        </div>
                        <button type="button" id="btn-add-submit" @click="handleAddToCartClick()" :disabled="isLoading"
                            class="flex-1 btn bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-lg rounded h-12 flex items-center justify-center shadow-md transition">
                            <span x-show="!isLoading"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg> เพิ่มลงตะกร้า</span>
                            <span x-show="isLoading" class="loading loading-spinner"></span>
                            <span x-show="isLoading">กำลังเพิ่ม...</span>
                        </button>
                    </div>

                    {{-- BOGO Selection Area --}}
                    @if ($isBogo)
                        <div class="mt-6 pt-6 border-t">
                            <h3 class="text-xl font-bold text-gray-800">โปรโมชั่น 1 แถม 1</h3>
                            <p class="text-gray-600 mb-4">เลือกสินค้าแถม 1 ชิ้นจากรายการด้านล่าง</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                @foreach ($product->bogoFreeOptions as $freebie)
                                    <div @click="selectedFreebieId = {{ $freebie->pd_sp_id }}"
                                        class="block group border-2 rounded-lg p-2 transition-all cursor-pointer"
                                        :class="selectedFreebieId == {{ $freebie->pd_sp_id }} ?
                                            'border-emerald-500 shadow-md' : 'border-gray-200 hover:border-emerald-400'">
                                        <div class="aspect-square rounded-md overflow-hidden bg-gray-50 mb-2">
                                            @php
                                                // แก้ไข: ใช้ img_sort แทน is_primary
                                                $freebieImage =
                                                    $freebie->images->where('img_sort', 1)->first() ??
                                                    $freebie->images->first();
                                                
                                                // แก้ไข: ใช้ img_path
                                                $freebieImagePath = $freebieImage
                                                    ? asset('storage/' . $freebieImage->img_path)
                                                    : 'https://via.placeholder.com/300x300.png?text=No+Image';
                                            @endphp
                                            <img src="{{ $freebieImagePath }}" alt="{{ $freebie->pd_sp_name }}"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <h4 class="text-sm font-semibold text-gray-700 truncate">{{ $freebie->pd_sp_name }}
                                        </h4>
                                        <p class="text-sm font-bold text-emerald-600">ฟรี</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="p-6 lg:p-10 border-t lg:border-r border-gray-200 lg:col-start-1 lg:row-start-2 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900 border-b-2 border-emerald-500 inline-block pb-1 mb-4">
                        รายละเอียดสินค้า</h3>
                    <p class="text-gray-700 text-sm leading-7">
                        {{ $product->pd_sp_details ?? ($product->pd_details ?? $product->pd_name) }}</p>
                </div>

                @if (isset($product->options) && $product->options->isNotEmpty())
                    <div class="lg:col-span-2 border-t border-gray-200 mt-8 pt-8 p-6 lg:p-10">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">ตัวเลือกอื่น ๆ</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            @foreach ($product->options as $option)
                                <a href="{{ route('product.show', $option->pd_sp_id) }}"
                                    class="block group border border-gray-200 rounded-lg p-2 hover:border-emerald-500 hover:shadow-lg transition-all">
                                    <div class="aspect-square rounded-md overflow-hidden bg-gray-50 mb-2">
                                        @php
                                            // แก้ไข: ใช้ img_sort แทน is_primary
                                            $optionImage =
                                                $option->images->where('img_sort', 1)->first() ??
                                                $option->images->first();
                                            
                                            // แก้ไข: ใช้ img_path
                                            $optionImagePath = $optionImage
                                                ? asset('storage/' . $optionImage->img_path)
                                                : 'https://via.placeholder.com/300x300.png?text=No+Image';
                                        @endphp
                                        <img src="{{ $optionImagePath }}" alt="{{ $option->pd_sp_name }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                    <h4 class="text-sm font-semibold text-gray-800 truncate group-hover:text-emerald-600">
                                        {{ $option->pd_sp_name }}</h4>
                                    <p class="text-sm text-gray-500">
                                        ฿{{ number_format($option->pd_sp_price - ($option->pd_sp_discount ?? 0)) }}</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
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

                    handleAddToCartClick() {
                        if (this.isBogo) {
                            if (!this.selectedFreebieId) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'โปรดเลือกของแถม',
                                    text: 'กรุณาเลือกสินค้าแถม 1 ชิ้นก่อนเพิ่มลงตะกร้า'
                                });
                                return;
                            }
                            this.addBogoToCart(this.selectedFreebieId);
                        } else {
                            this.standardAddToCart();
                        }
                    },

                    standardAddToCart() {
                        const payload = {
                            quantity: this.quantity
                        };
                        this.performAjaxAddToCart(config.standardAction, payload);
                    },

                    addBogoToCart(freebieId) {
                        const payload = {
                            quantity: this.quantity,
                            main_product_id: config.productId,
                            free_product_id: freebieId
                        };
                        this.performAjaxAddToCart(config.bogoAction, payload);
                    },

                    performAjaxAddToCart(url, data) {
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
                                    if (window.flyToCart) window.flyToCart(document.getElementById(
                                        'btn-add-submit'));
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'เพิ่มลงตะกร้าแล้ว',
                                        text: data.message || 'สินค้าถูกเพิ่มเรียบร้อย',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    if (window.updateCartBadge) setTimeout(() => window.updateCartBadge(
                                        data.cartCount), 800);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'เกิดข้อผิดพลาด',
                                        text: data.message || 'ไม่สามารถเพิ่มสินค้าได้'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
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