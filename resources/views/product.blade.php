@extends('layout')

@section('title', $product->pd_name . ' | Salepage Demo')

@section('content')
    @php
        $originalPrice  = (float) ($product->pd_price ?? 0);
        $discountAmount = (float) ($product->pd_sp_discount ?? 0);
        $finalSellingPrice = max(0, $originalPrice - $discountAmount);
        $isOnSale = $discountAmount > 0;
        $currentPriceForDisplay = $finalSellingPrice;
        $fullPriceForDisplay = $originalPrice;

        $imagePaths = collect($product->images)->pluck('image_path')->map(function ($path) {
            return asset('storage/' . $path);
        })->toArray();

        if (empty($imagePaths) && $product->pd_img) {
            $imagePaths[] = asset('storage/' . $product->pd_img);
        }
        
        $activeImageUrl = $imagePaths[0] ?? 'https://via.placeholder.com/600x600.png?text=No+Image';

    @endphp

    <div x-data="{
        activeImage: '{{ $activeImageUrl }}',
        images: {{ json_encode($imagePaths) }},
        quantity: 1
    }" class="container mx-auto px-4 py-8">

        <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">

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
                     <div class="grid grid-cols-5 gap-2 mt-4">
                        <template x-for="image in images" :key="image">
                            <div @click="activeImage = image"
                                class="aspect-square rounded-md overflow-hidden cursor-pointer border-2"
                                :class="{ 'border-emerald-500': activeImage === image, 'border-transparent': activeImage !== image }">
                                <img :src="image" class="w-full h-full object-cover">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ส่วนข้อมูลสินค้า --}}
                <div class="p-6 lg:p-10">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 leading-tight">{{ $product->pd_name }}</h1>
                    <div class="mt-2 text-sm text-gray-500">
                        @if ($product->brand_name)
                            <span class="font-semibold">Brand:</span> {{ $product->brand_name }} |
                        @endif
                        <span class="font-semibold">Code:</span> {{ $product->pd_code }} |
                        <span class="font-semibold">Stock:</span> {{ $product->quantity }}
                    </div>

                    {{-- ... (ส่วนรายละเอียดสินค้าคงเดิม) ... --}}

                    {{-- เพิ่ม : แสดงรายละเอียดแบบย่อ หรือ rating ถ้ามี (ส่วนนี้ผมคงไว้ตามเดิม ไม่แตะต้อง) --}}

                    <div class="bg-gray-50 p-4 rounded-lg mb-6 border border-gray-100 mt-4">
                        <h2 class="text-3xl lg:text-4xl font-bold text-emerald-600 flex items-end gap-3">
                            @if ($isOnSale)
                                <span>฿{{ number_format($currentPriceForDisplay) }}</span>
                                <span
                                    class="text-lg text-gray-400 font-normal line-through">฿{{ number_format($fullPriceForDisplay) }}</span>
                            @else
                                <span>฿{{ number_format($currentPriceForDisplay) }}</span>
                            @endif
                        </h2>
                    </div>

                    {{-- ฟอร์มเพิ่มสินค้า (AJAX) และปุ่มเพิ่มลงตะกร้า --}}
                    <form id="add-to-cart-form" data-action="{{ route('cart.add', ['id' => $product->id]) }}"
                        class="flex flex-col sm:flex-row gap-3 pt-2">

                        {{-- Input จำนวนสินค้า --}}
                        <div class="flex items-center border border-gray-300 rounded h-12 w-full sm:w-32 bg-white">
                            <button type="button" @click="quantity > 1 ? quantity-- : null"
                                class="w-10 h-full text-gray-500 hover:bg-gray-100 text-xl font-bold rounded-l">-</button>

                            <input type="number" name="quantity" x-model="quantity"
                                class="w-full h-full text-center border-none focus:ring-0 text-gray-900 font-bold text-lg m-0"
                                readonly>

                            <button type="button" @click="quantity++"
                                class="w-10 h-full text-gray-500 hover:bg-gray-100 text-xl font-bold rounded-r">+</button>
                        </div>

                        {{-- ปุ่ม Submit เพิ่มลงตะกร้า --}}
                        <button type="submit" id="btn-add-submit"
                            class="flex-1 btn bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-lg rounded h-12 flex items-center justify-center shadow-md transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            เพิ่มลงตะกร้า
                        </button>
                    </form>
                </div>

                {{-- ส่วนล่างของ Grid (คงเดิม) --}}
                <div class="p-6 lg:p-10 border-t lg:border-r border-gray-200 lg:col-start-1 lg:row-start-2 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900 border-b-2 border-emerald-500 inline-block pb-1 mb-4">
                        รายละเอียดสินค้า</h3>
                    <p class="text-gray-700 text-sm leading-7">{{ $product->pd_sp_details ?? $product->pd_details ?? $product->pd_name }}</p>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const submitBtn = document.getElementById('btn-add-submit');
            const originalBtnContent = submitBtn.innerHTML; // เก็บเนื้อหาเดิมของปุ่มไว้
            const actionUrl = form.getAttribute('data-action');
            const quantity = form.querySelector('[name="quantity"]').value;

            // เพิ่ม Loading State
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading loading-spinner"></span> กำลังเพิ่ม...';

            const formData = new FormData();
            formData.append('quantity', quantity);

            fetch(actionUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // 1. เรียก Animation ลอยไปตะกร้า
                        if (typeof window.flyToCart === 'function') {
                            window.flyToCart(submitBtn);
                        }

                        // 2. แสดง Popup
                        Swal.fire({
                            icon: 'success',
                            title: 'เพิ่มลงตะกร้าแล้ว',
                            text: 'สินค้าถูกเพิ่มเรียบร้อย',
                            position: 'center',
                            showConfirmButton: false,
                            timer: 1500
                        });

                        // 3. อัปเดตตัวเลขตะกร้า
                        setTimeout(() => {
                            if (window.updateCartBadge) {
                                window.updateCartBadge(data.cartCount);
                            }
                        }, 800);
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
                    // คืนค่าปุ่ม
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnContent;
                    }, 500);
                });
        });
    </script>
@endsection