@extends('layout')

@section('title', 'ตะกร้าสินค้า | Salepage Demo')

@section('content')
    <div class="container px-4 mx-auto md:px-8 lg:p-12">

        {{-- Card Container --}}
        <div class="p-6 bg-white shadow rounded-lg border-gray-200 md:p-8 lg:p-12">

            {{-- 
               [แก้ไขจุดสำคัญ 1] 
               Action ชี้ไปที่ route 'payment.checkout' (หน้าเลือกที่อยู่) 
               Method เป็น GET เพื่อให้เข้ากันได้กับ Route ที่ประกาศไว้ 
            --}}
            <form action="{{ route('payment.checkout') }}" method="GET" id="checkout-form">

                <div class="">
                    {{-- Header --}}
                    <div class="mb-6 border-b border-gray-200 pb-4 flex items-center gap-3">
                        {{-- Checkbox เลือกทั้งหมด --}}
                        @if (isset($items) && !$items->isEmpty())
                            <div class="flex items-center">
                                <input type="checkbox" id="select-all" checked
                                    class="w-5 h-5 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 cursor-pointer"
                                    onclick="toggleAll(this)">
                            </div>
                        @endif
                        <h1 class="text-2xl font-bold text-gray-800">ตะกร้าสินค้า</h1>
                    </div>

                    {{-- เช็คว่าตะกร้าว่างไหม --}}
                    @if (isset($items) && !$items->isEmpty())
                        {{-- ตัวแปรสำหรับเก็บผลรวมเริ่มต้น --}}
                        @php
                            $summaryTotalPrice = 0;
                            $summaryTotalOriginal = 0;
                        @endphp

                        {{-- Loop แสดงรายการสินค้า --}}
                        @foreach ($items as $item)
                            @php
                                $quantity = $item->quantity;
                                $price = $item->price; // ราคาขายจริง

                                // ราคาเต็ม (Original Price)
                                $originalPrice = $item->attributes->has('original_price')
                                    ? $item->attributes->original_price
                                    : $price;

                                // คำนวณราคารวมของสินค้ารายการนี้
                                $totalPrice = $price * $quantity;
                                $totalOriginalPrice = $originalPrice * $quantity;

                                // เช็คว่ามีส่วนลดหรือไม่
                                $hasDiscount = ($item->attributes->discount ?? 0) > 0;


                                // บวกสะสมเข้ายอดรวมทั้งหมด (PHP)
                                $summaryTotalPrice += $totalPrice;
                                $summaryTotalOriginal += $totalOriginalPrice;
                            @endphp

                            <div
                                class="flex flex-col md:flex-row md:items-start md:justify-between border-b border-gray-200 py-6 gap-4">

                                {{-- 1. ส่วน Checkbox และ รูปสินค้า --}}
                                <div class="flex flex-row gap-4 w-full md:w-auto items-start">
                                    {{-- Checkbox --}}
                                    <div class="mt-8 md:mt-10">
                                        {{-- ใส่ data-price และ data-original-price เพื่อให้ JS คำนวณ --}}
                                        <input type="checkbox" name="selected_items[]" value="{{ $item->id }}" checked
                                            data-price="{{ $totalPrice }}" 
                                            data-original-price="{{ $totalOriginalPrice }}"
                                            data-discount="{{ $item->attributes->discount ?? 0 }}"
                                            class="item-checkbox w-5 h-5 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 cursor-pointer"
                                            onchange="calculateTotal()">
                                    </div>

                                    {{-- รูปภาพ --}}
                                    <div class="flex-shrink-0">
                                        <img src="https://crm.kawinbrothers.com/product_images/{{ $item->attributes->image }}"
                                            alt="{{ $item->name }}"
                                            class="w-20 h-20 object-cover rounded-lg md:w-24 md:h-24" />
                                    </div>
                                    <div class="flex-1 mt-1">
                                        <h1 class="font-bold text-gray-800 text-sm md:text-base">{{ $item->name }}</h1>
                                        <p class="text-xs text-gray-500">Code: {{ $item->attributes->pd_code }}</p>
                                        <p class="text-xs text-gray-500 mt-1">ราคาต่อชิ้น:
                                            @if ($hasDiscount)
                                                <s class="text-gray-400">฿{{ number_format($originalPrice) }}</s>
                                                <span class="font-semibold text-red-600 ml-1">฿{{ number_format($price) }}</span>
                                            @else
                                                <span class="text-gray-800">฿{{ number_format($price) }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                {{-- 2. ราคาและปุ่มจัดการ --}}
                                <div
                                    class="flex flex-row justify-between items-center md:flex-col md:items-end gap-4 w-full md:w-auto mt-2 md:mt-0 pl-9 md:pl-0">

                                    {{-- ส่วนแสดงราคา --}}
                                    <div class="flex flex-col items-end">
                                        @if ($hasDiscount)
                                            <div class="text-2xl font-bold text-red-600">
                                                ฿{{ number_format($totalPrice) }}
                                            </div>
                                            <span
                                                class="text-[10px] md:text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full mt-1">
                                                ประหยัด ฿{{ number_format($item->attributes->discount ?? 0) }}
                                            </span>
                                        @else
                                            <div class="text-2xl font-bold text-emerald-600">
                                                ฿{{ number_format($totalPrice) }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- ปุ่มจัดการจำนวน --}}
                                    <div class="flex flex-col sm:flex-row items-end sm:items-center gap-3">
                                        <div class="flex items-center border border-gray-300 rounded h-10 md:h-12 bg-white">
                                            <button type="button"
                                                class="cart-action-btn px-3 py-1 text-gray-600 hover:bg-gray-100 h-full flex items-center text-lg"
                                                data-url="{{ route('cart.update', ['id' => $item->id, 'action' => 'decrease']) }}"
                                                data-method="PATCH">
                                                -
                                            </button>

                                            <span class="font-bold text-gray-700 text-sm md:text-base w-12 text-center">
                                                {{ $quantity }}
                                            </span>

                                            <button type="button"
                                                class="cart-action-btn px-3 py-1 text-gray-600 hover:bg-gray-100 h-full flex items-center text-lg"
                                                data-url="{{ route('cart.update', ['id' => $item->id, 'action' => 'increase']) }}"
                                                data-method="PATCH">
                                                +
                                            </button>
                                        </div>

                                        <button type="button"
                                            class="cart-action-btn text-red-500 hover:text-red-700 font-medium text-sm md:text-base underline md:no-underline md:btn md:btn-ghost md:btn-sm md:text-red-500"
                                            data-url="{{ route('cart.remove', $item->id) }}" data-method="DELETE">
                                            ลบรายการ
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Footer Section --}}
                        <div class="flex flex-col lg:flex-row justify-end gap-5 mt-10">
                            <div class="w-full lg:w-[400px]">

                                {{-- ยอดรวมสินค้า (ราคาเต็ม) --}}
                                <div class="flex justify-between mt-5 text-base text-gray-600">
                                    <div>ยอดรวมสินค้า (<span id="selected-count">{{ count($items) }}</span> รายการ)</div>
                                    <div class="font-medium">฿<span
                                            id="subtotal-display">{{ number_format($summaryTotalOriginal) }}</span></div>
                                </div>

                                {{-- ส่วนลดรวม --}}
                                <div class="flex justify-between mt-2 text-base text-red-500">
                                    <div>ส่วนลดรวม</div>
                                    <div class="font-medium">-฿<span
                                            id="discount-display">{{ number_format($summaryTotalOriginal - $summaryTotalPrice) }}</span>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 my-4"></div>

                                {{-- ยอดสุทธิ --}}
                                <div class="flex justify-between items-center mb-6">
                                    <div>
                                        <h1 class="font-bold text-xl md:text-2xl text-gray-800">ยอดสุทธิที่ต้องชำระ</h1>
                                        <p class="text-xs text-gray-500">(รวมภาษีมูลค่าเพิ่มแล้ว)</p>
                                    </div>
                                    <div>
                                        <h1 class="text-emerald-600 font-bold text-2xl md:text-3xl">
                                            ฿<span id="total-display">{{ number_format($summaryTotalPrice) }}</span>
                                        </h1>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-3">
                                    {{-- 
                                       [แก้ไขจุดสำคัญ 2] 
                                       ใช้ @auth / @guest แทน Auth::check() 
                                    --}}
                                    @auth
                                        <button type="submit" id="checkout-btn"
                                            class="btn btn-success text-white w-full text-lg h-12">
                                            ดำเนินการชำระเงิน
                                        </button>
                                    @endauth

                                    @guest
                                        <a href="{{ route('login') }}"
                                            class="btn btn-success text-white w-full text-lg h-12 flex items-center justify-center">
                                            เข้าสู่ระบบเพื่อชำระเงิน
                                        </a>
                                    @endguest

                                    <a href="{{ route('allproducts') }}"
                                        class="btn btn-outline border-gray-300 text-gray-600 hover:bg-gray-50 w-full flex items-center justify-center">
                                        ซื้อสินค้าต่อ
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- กรณีตะกร้าว่าง --}}
                        <div class="text-center py-20 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mx-auto text-gray-300 mb-6"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h2 class="text-2xl font-bold text-gray-400 mb-2">ตะกร้าของคุณว่างเปล่า</h2>
                            <p class="text-gray-500 mb-6">ดูเหมือนคุณยังไม่ได้เลือกสินค้าลงตะกร้าเลย</p>
                            <a href="{{ route('allproducts') }}"
                                class="btn btn-primary text-white px-8">ไปเลือกซื้อสินค้า</a>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Script คำนวณราคา Real-time --}}
    <script>
        function numberWithCommas(x) {
            if (x === undefined || x === null) return "0";
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function toggleAll(source) {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = source.checked;
            });
            calculateTotal();
        }

        function calculateTotal() {
            let totalSalePrice = 0; // ราคาทีต้องจ่ายจริง
            let totalOriginalPrice = 0; // ราคาเต็ม
            let totalDiscount = 0; // [FIXED] ส่วนลดรวม
            let count = 0;

            const checkboxes = document.querySelectorAll('.item-checkbox');
            const checkoutBtn = document.getElementById('checkout-btn');

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    let price = parseFloat(checkbox.getAttribute('data-price'));
                    let original = parseFloat(checkbox.getAttribute('data-original-price'));
                    let discount = parseFloat(checkbox.getAttribute('data-discount')); // [NEW] Get fixed discount

                    if (!isNaN(price)) totalSalePrice += price;
                    if (!isNaN(original)) totalOriginalPrice += original;
                    else totalOriginalPrice += price; // Fallback

                    if (!isNaN(discount)) totalDiscount += discount; // [NEW] Sum up fixed discounts

                    count++;
                }
            });

            // Update หน้าจอ
            const totalDisplay = document.getElementById('total-display');
            const subtotalDisplay = document.getElementById('subtotal-display');
            const discountDisplay = document.getElementById('discount-display');
            const selectedCount = document.getElementById('selected-count');

            if (totalDisplay) totalDisplay.innerText = numberWithCommas(totalSalePrice);
            if (subtotalDisplay) subtotalDisplay.innerText = numberWithCommas(totalOriginalPrice);
            if (discountDisplay) discountDisplay.innerText = numberWithCommas(totalDiscount); // [FIXED] Use the new totalDiscount
            if (selectedCount) selectedCount.innerText = count;

            // จัดการ Checkbox Select All
            const selectAllCheckbox = document.getElementById('select-all');
            if (selectAllCheckbox) {
                if (count === checkboxes.length && count > 0) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else if (count === 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = true;
                }
            }

            // ปิดปุ่ม Checkout ถ้าไม่ได้เลือกสินค้า
            if (checkoutBtn) {
                if (count === 0) {
                    checkoutBtn.disabled = true;
                    checkoutBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    checkoutBtn.disabled = false;
                    checkoutBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }

        // เรียกใช้ฟังก์ชันเมื่อโหลดหน้าเสร็จ
        document.addEventListener("DOMContentLoaded", function() {
            calculateTotal();
        });

        // เรียกใช้เมื่อกดย้อนกลับมาหน้านี้ (สำหรับ Safari/Mobile)
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                calculateTotal();
            } else {
                calculateTotal();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            document.querySelectorAll('.cart-action-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    const url = this.getAttribute('data-url');
                    const method = this.getAttribute('data-method');

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.style.display = 'none';

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);

                    if (method !== 'POST') {
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = method;
                        form.appendChild(methodInput);
                    }

                    document.body.appendChild(form);
                    form.submit();
                });
            });
        });
    </script>
@endsection
