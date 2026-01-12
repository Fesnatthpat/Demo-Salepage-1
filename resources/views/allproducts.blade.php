@extends('layout')

@section('title', 'สินค้าทั้งหมด | Salepage Demo')

@section('content')

    <div class="bg-gray-50 min-h-screen py-8">
        <div class="container mx-auto px-4">

            {{-- Header --}}
            <div class="flex flex-col lg:flex-row gap-8">

                {{-- SIDEBAR --}}
                <aside class="w-full lg:w-1/4">
                    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100 sticky top-4">
                        <h3 class="font-bold text-lg mb-4 text-gray-700">ตัวกรองค้นหา</h3>
                        <form action="{{ route('allproducts') }}" method="GET">
                            <div class="form-control mb-4">
                                <label class="label"><span class="label-text">ค้นหาชื่อสินค้า</span></label>
                                <div class="relative">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="พิมพ์คำค้นหา..."
                                        class="input input-bordered w-full pr-10 bg-gray-50" />
                                    <button type="submit"
                                        class="absolute right-2 top-2.5 text-gray-400 hover:text-emerald-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="divider my-2"></div>
                            <div class="mb-4">
                                <label class="label"><span class="label-text font-bold">หมวดหมู่</span></label>
                                <ul class="menu bg-base-100 w-full p-0 text-gray-600">
                                    <li><a href="{{ route('allproducts') }}"
                                            class="{{ !request('category') ? 'active' : '' }}">ทั้งหมด</a></li>
                                    {{-- ตรวจสอบตัวแปร categories ก่อน loop เพื่อป้องกัน error --}}
                                    @if (isset($categories))
                                        @foreach ($categories as $cat)
                                            <li><a href="#">{{ $cat }}</a></li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                            <button type="submit"
                                class="btn btn-primary btn-block text-white mt-4 shadow-md">ค้นหา</button>
                        </form>
                    </div>
                </aside>

                {{-- MAIN CONTENT --}}
                <main class="w-full lg:w-3/4">
                    <div
                        class="bg-white p-3 rounded-lg shadow-sm border border-gray-100 mb-6 flex justify-between items-center">
                        <span class="text-gray-500 text-sm hidden sm:inline">พบสินค้า {{ $products->total() }} รายการ</span>
                    </div>

                    @if ($products->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach ($products as $product)
                                @if ($product->pd_id)
                                    {{-- ========================================================= --}}
                                    {{-- [Logic คำนวณราคา แบบเดียวกับหน้า Index] --}}
                                    {{-- ========================================================= --}}
                                    @php
                                        // Assume pd_price (aliased from product_salepage.pd_sp_price) is the ORIGINAL price on sale page
                                        $originalPrice  = (float) ($product->pd_price ?? 0); 
                                        // Assume pd_sp_discount is the discount AMOUNT to subtract
                                        $discountAmount = (float) ($product->pd_sp_discount ?? 0);
                                        
                                        // The final selling price is original price minus discount amount
                                        $finalSellingPrice = $originalPrice - $discountAmount;

                                        // Ensure final selling price is not negative
                                        if ($finalSellingPrice < 0) {
                                            $finalSellingPrice = 0;
                                        }
                                        
                                        // A product is on sale if a discount is applied.
                                        $isOnSale = $discountAmount > 0;

                                        // Variable names for display
                                        $currentPriceForDisplay = $finalSellingPrice; // This will be green
                                        $fullPriceForDisplay = $originalPrice; // This will be strikethrough
                                    @endphp
                                    {{-- ========================================================= --}}

                                    <div
                                        class="card bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all rounded-b-md overflow-hidden duration-300 group flex flex-col h-full">
                                        <a href="{{ url('/product/' . $product->pd_id) }}">
                                            <figure class="relative aspect-[4/5] overflow-hidden bg-gray-100">
                                                <img src="{{ asset('storage/' . $product->pd_img) }}"
                                                    alt="{{ $product->pd_name }}"
                                                    class="w-full h-full object-cover group-hover:scale-110 transition duration-500" />

                                                {{-- [แก้ใหม่] ป้าย SALE: แสดงยอดเงินที่ลด --}}
                                                @if ($isOnSale)
                                                    <div
                                                        class="absolute top-2 left-2 bg-red-500 p-2 rounded-2xl text-white gap-1 text-xs font-bold shadow-sm">
                                                        ลด ฿{{ number_format($discountAmount) }}
                                                    </div>
                                                @endif
                                            </figure>
                                        </a>

                                        <div class="card-body p-4 flex flex-col flex-1">
                                            <h2
                                                class="card-title text-sm font-bold text-gray-800 leading-tight min-h-[2.5em] line-clamp-2">
                                                <a href="{{ url('/product/' . ($product->pd_id ?? $product->pd_sp_id)) }}"
                                                    class="hover:text-emerald-600 transition">{{ $product->pd_name ?? 'Missing Product Name' }}</a>
                                            </h2>
                                            <p class="text-xs text-gray-500">Code:
                                                {{ $product->pd_code }}</p>
                                            {{-- ส่วนราคาและปุ่มเพิ่มลงตะกร้า --}}
                                            <div class="mt-auto pt-2">
                                                <div class="flex flex-col mb-3">
                                                    @if ($isOnSale)
                                                        <span
                                                            class="text-lg font-bold text-emerald-600">฿{{ number_format($currentPriceForDisplay) }}</span>
                                                        <span
                                                            class="text-xs text-gray-400 line-through">฿{{ number_format($fullPriceForDisplay) }}</span>
                                                    @else
                                                        <span
                                                            class="text-lg font-bold text-emerald-600">฿{{ number_format($currentPriceForDisplay) }}</span>
                                                    @endif
                                                </div>

                                                {{-- ★★★ [เพิ่ม] ปุ่มเพิ่มลงตะกร้า ★★★ --}}
                                                <form class="add-to-cart-form-listing w-full"
                                                    data-action="{{ route('cart.add', ['id' => $product->pd_id]) }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit"
                                                        class="btn btn-sm w-full bg-emerald-600 hover:bg-emerald-700 text-white border-none shadow-sm flex items-center justify-center gap-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                        </svg>
                                                        เพิ่มลงตะกร้า
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    {{-- Placeholder card for missing product details --}}
                                    <div
                                        class="card bg-gray-100 border border-gray-200 shadow-sm rounded-b-md overflow-hidden flex flex-col h-full p-4 items-center justify-center text-center">
                                        <div class="text-gray-500 mb-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p class="font-bold text-gray-700">ไม่พบรายละเอียดสินค้า</p>
                                        <p class="text-sm text-gray-500">สำหรับ Sale ID: {{ $product->pd_sp_id }}</p>
                                        <p class="text-sm text-gray-500">กรุณาตรวจสอบข้อมูลสินค้า</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="mt-8 flex justify-center">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-20 bg-white rounded-lg border border-dashed">
                            <p class="text-gray-500 text-lg">ไม่พบสินค้าที่คุณค้นหา</p>
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </div>

    {{-- ★★★ [เพิ่ม] Script สำหรับจัดการปุ่มเพิ่มตะกร้าในหน้า Loop ★★★ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.add-to-cart-form-listing');

            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const currentForm = this;
                    const submitBtn = currentForm.querySelector('button[type="submit"]');
                    const actionUrl = currentForm.getAttribute('data-action');
                    const quantity = currentForm.querySelector('[name="quantity"]').value;

                    // เพิ่ม Effect กดปุ่มให้รู้ว่ากดแล้ว
                    const originalBtnContent = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML =
                        '<span class="loading loading-spinner loading-xs"></span> กำลังเพิ่ม...';

                    const formData = new FormData();
                    formData.append('quantity', quantity);

                    fetch(actionUrl, { // Use actionUrl directly for POST
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
                                // 1. Animation Fly (ถ้ามี)
                                if (typeof window.flyToCart === 'function') {
                                    window.flyToCart(submitBtn);
                                }

                                // 2. Popup Success
                                Swal.fire({
                                    icon: 'success',
                                    title: 'เพิ่มลงตะกร้าแล้ว',
                                    text: 'สินค้าถูกเพิ่มเรียบร้อย',
                                    position: 'center',
                                    showConfirmButton: false,
                                    timer: 1500
                                });

                                // 3. Update Badge
                                setTimeout(() => {
                                    if (window.updateCartBadge) {
                                        window.updateCartBadge(data.cartCount);
                                    }
                                }, 500);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถเพิ่มสินค้าได้',
                                position: 'center',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        })
                        .finally(() => {
                            // คืนค่าปุ่มกลับสู่สภาพเดิม
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnContent;
                        });
                });
            });
        });
    </script>
@endsection
