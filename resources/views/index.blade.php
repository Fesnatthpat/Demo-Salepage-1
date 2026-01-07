@extends('layout')

@section('content')

    {{-- HERO SECTION (คงเดิม) --}}
    <div class="relative w-full h-[600px] lg:h-[700px] bg-gray-900 overflow-hidden">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=2070&auto=format&fit=crop"
                class="w-full h-full object-cover opacity-60 hover:scale-105 transition-transform duration-1000 ease-in-out"
                alt="Sale Background">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
        </div>
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-4 z-10">
            <span
                class="inline-block py-1 px-3 rounded-full bg-red-600 text-white text-xs font-bold tracking-widest mb-4 animate-bounce">
                LIMITED TIME OFFER
            </span>
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-4 leading-tight drop-shadow-lg">
                <span class="block text-gray-300 text-2xl md:text-3xl font-light mb-2">สมาชิกช้อปสินค้า</span>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-orange-500">SALE</span>
                ก่อนใคร
            </h1>
            <p class="text-gray-200 text-base md:text-lg max-w-2xl mx-auto mb-8 leading-relaxed font-light">
                ลดสูงสุด <span class="text-yellow-400 font-bold text-xl">50%</span> | ที่ร้านและออนไลน์ <br
                    class="hidden md:block">
                เฉพาะสินค้าที่ร่วมรายการ จนกว่าสินค้าจะหมด
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="/allproducts"
                    class="btn bg-white text-gray-900 border-none hover:bg-gray-200 px-8 py-3 rounded-full font-bold text-lg transition-transform hover:-translate-y-1 shadow-lg">
                    ช้อปสินค้าลดราคา
                </a>
                <a href="/login">
                    <button
                        class="btn btn-outline text-white border-white hover:bg-white/20 px-8 py-3 rounded-full font-bold text-lg transition-transform hover:-translate-y-1">
                        เข้าสู่ระบบสมาชิก
                    </button>
                </a>
            </div>
            <p class="mt-8 text-xs text-gray-400 opacity-80 max-w-lg">
                *สินค้าและราคาของที่ร้านและออนไลน์อาจแตกต่างกัน ลงชื่อเข้าใช้เพื่อรับสิทธิพิเศษ
            </p>
        </div>
    </div>

    {{-- SERVICE BAR (คงเดิม) --}}
    <div class="bg-white border-b border-gray-100 py-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center divide-x divide-gray-100">
                <div class="flex flex-col items-center gap-2 group">
                    <svg class="w-8 h-8 text-emerald-600 group-hover:scale-110 transition" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span class="text-sm font-semibold text-gray-700">สินค้าของแท้ 100%</span>
                </div>
                <div class="flex flex-col items-center gap-2 group">
                    <svg class="w-8 h-8 text-emerald-600 group-hover:scale-110 transition" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-gray-700">จัดส่งไวใน 24 ชม.</span>
                </div>
                <div class="flex flex-col items-center gap-2 group">
                    <svg class="w-8 h-8 text-emerald-600 group-hover:scale-110 transition" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        </path>
                    </svg>
                    <span class="text-sm font-semibold text-gray-700">ชำระเงินปลอดภัย</span>
                </div>
                <div class="flex flex-col items-center gap-2 group">
                    <svg class="w-8 h-8 text-emerald-600 group-hover:scale-110 transition" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <span class="text-sm font-semibold text-gray-700">บริการหลังการขาย</span>
                </div>
            </div>
        </div>
    </div>

    {{-- PRODUCTS SECTION --}}
    <div class="container mx-auto px-4 mt-12 mb-20">
        <div class="flex justify-between items-end mb-8">
            <div>
                {{-- ปรับหัวข้อตามต้องการ --}}
                <h2 class="text-3xl font-bold text-gray-900">สินค้าแนะนำ</h2>
                <p class="text-gray-500 mt-1">คัดสรรมาเพื่อคุณโดยเฉพาะ</p>
            </div>
            <a href="/allproducts" class="text-emerald-600 font-bold hover:underline hidden md:block">ดูทั้งหมด →</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            @if (isset($recommendedProducts) && count($recommendedProducts) > 0)
                @foreach ($recommendedProducts as $product)
                    @php
                        // More robust price calculation
                        $currentPrice = (float) $product->pd_price;
                        $discount = isset($product->pd_sp_discount) ? (float) $product->pd_sp_discount : 0;
                        $fullPrice =
                            isset($product->pd_full_price) && $product->pd_full_price > 0
                                ? (float) $product->pd_full_price
                                : $currentPrice + $discount;
                        $isOnSale = $discount > 0 && $fullPrice > $currentPrice;
                    @endphp

                    {{-- ★★★ เพิ่ม class flex flex-col h-full เพื่อจัด layout ให้ปุ่มอยู่ล่างสุดเสมอ ★★★ --}}
                    <div
                        class="card bg-white border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group flex flex-col h-full">

                        {{-- รูปภาพ --}}
                        <a href="{{ url('/product/' . $product->pd_id) }}">
                            <figure class="relative aspect-[4/5] overflow-hidden bg-gray-100">
                                <img src="https://crm.kawinbrothers.com/product_images/{{ $product->pd_img }}"
                                    alt="{{ $product->pd_name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />

                                {{-- Logic ป้าย SALE: แสดงจำนวนเงินที่ลด --}}
                                @if ($isOnSale)
                                    <div
                                        class="absolute top-2 left-2 bg-red-500 p-2 rounded-2xl text-white gap-1 text-xs font-bold shadow-sm">
                                        ลด ฿{{ number_format($discount) }}
                                    </div>
                                @endif

                                <div
                                    class="absolute bottom-4 left-0 right-0 px-4 translate-y-full group-hover:translate-y-0 transition duration-300 opacity-0 group-hover:opacity-100 z-10">
                                    <a href="{{ url('/product/' . $product->pd_id) }}"
                                        class="btn btn-block bg-white/90 hover:bg-emerald-600 hover:text-white text-gray-800 border-none shadow-md text-sm h-10 min-h-0">
                                        ดูรายละเอียด
                                    </a>
                                </div>
                            </figure>
                        </a>

                        {{-- รายละเอียด --}}
                        {{-- ★★★ เพิ่ม flex-1 flex flex-col เพื่อดันปุ่มไปล่างสุด ★★★ --}}
                        <div class="card-body p-4 flex-1 flex flex-col">
                            {{-- หมวดหมู่ (Mockup) --}}
                            <div class="text-xs text-gray-400 mb-1">สินค้าแนะนำ</div>

                            <h2
                                class="card-title text-sm md:text-base font-bold text-gray-800 leading-tight min-h-[2.5em] line-clamp-2">
                                <a href="{{ url('/product/' . $product->pd_id) }}"
                                    class="hover:text-emerald-600 transition">
                                    {{ $product->pd_name }}
                                </a>
                            </h2>
                            <p class="text-xs text-gray-500">Code: {{ $product->pd_code }}</p>

                            <div class="flex justify-between items-end mt-2 mb-3">
                                <div class="flex items-baseline gap-2">
                                    @if ($isOnSale)
                                        {{-- กรณีมีส่วนลด: แสดงราคาขายปัจจุบัน (สีเขียว) + ราคาเต็มขีดฆ่า (สีเทา) --}}
                                        <span
                                            class="text-lg font-bold text-emerald-600">฿{{ number_format($currentPrice) }}</span>
                                        <span
                                            class="text-xs text-gray-400 line-through">฿{{ number_format($fullPrice) }}</span>
                                    @else
                                        {{-- กรณีไม่มีส่วนลด: แสดงราคาขายปัจจุบันสีเขียว --}}
                                        <span
                                            class="text-lg font-bold text-emerald-600">฿{{ number_format($currentPrice) }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- ★★★ เพิ่มปุ่ม: Add to Cart (Quick Add) ★★★ --}}
                            <div class="mt-auto">
                                <button type="button"
                                    onclick="addToCartQuick(this, '{{ route('cart.add', ['id' => $product->pd_id]) }}')"
                                    class="btn btn-sm w-full btn-outline border-emerald-600 text-emerald-600 hover:bg-emerald-600 hover:text-white hover:border-emerald-600 font-bold gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    เพิ่มลงตะกร้า
                                </button>
                            </div>

                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-span-full text-center py-10">
                    <p class="text-gray-500">ไม่พบสินค้าแนะนำในขณะนี้</p>
                </div>
            @endif
        </div>

        <div class="mt-8 text-center md:hidden">
            <a href="/allproducts" class="btn btn-outline w-full">ดูสินค้าทั้งหมด</a>
        </div>
    </div>

    {{-- ★★★ SCRIPT จัดการปุ่มกด (เหมือนเดิมทุกประการ) ★★★ --}}
    <script>
        function addToCartQuick(btnElement, url) {
            // ป้องกันการกดซ้ำ
            if (btnElement.disabled) return;

            // เก็บข้อความเดิม และเปลี่ยนเป็น Loading
            const originalText = btnElement.innerHTML;
            btnElement.disabled = true;
            btnElement.innerHTML = '<span class="loading loading-spinner loading-xs"></span> กำลังเพิ่ม...';

            // ส่ง Request แบบ POST
            const formData = new FormData();
            formData.append('quantity', 1); // quantity is always 1 for quick add
            // formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content')); // CSRF token handled by axios automatically or can be added manually

            fetch(url, {
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
                        // Animation ลอยไปตะกร้า (ถ้ามี)
                        if (typeof window.flyToCart === 'function') {
                            window.flyToCart(btnElement);
                        }

                        // Popup แจ้งเตือนสำเร็จ
                        Swal.fire({
                            icon: 'success',
                            title: 'เพิ่มลงตะกร้าแล้ว',
                            position: 'center',
                            showConfirmButton: false,
                            timer: 1500
                        });

                        // อัปเดตตัวเลขที่เมนูตะกร้า (ถ้ามี)
                        if (window.updateCartBadge) {
                            window.updateCartBadge(data.cartCount);
                        }
                    } else {
                        // แจ้งเตือน Error
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: data.message || 'ไม่สามารถเพิ่มสินค้าได้',
                            position: 'center',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้'
                    });
                })
                .finally(() => {
                    // คืนค่าปุ่มกลับสู่สภาพเดิม
                    setTimeout(() => {
                        btnElement.disabled = false;
                        btnElement.innerHTML = originalText;
                    }, 500);
                });
        }
    </script>
@endsection
