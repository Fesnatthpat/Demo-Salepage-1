@extends('layout')

@section('title', 'ตะกร้าสินค้า | Salepage Demo')

@section('content')
    <div class="container px-4 mx-auto md:px-8 lg:p-12">

        {{-- Card Container --}}
        <div class="p-6 bg-white shadow rounded-lg border-gray-200 md:p-8 lg:p-12">

            <form action="{{ route('payment.checkout') }}" method="GET" id="checkout-form">

                <div class="">
                    {{-- Header --}}
                    <div class="mb-6 border-b border-gray-200 pb-4 flex items-center gap-3">
                        @if (isset($items) && !$items->isEmpty())
                            <div class="flex items-center">
                                <input type="checkbox" id="select-all" checked
                                    class="w-5 h-5 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 cursor-pointer"
                                    onclick="toggleAll(this)">
                            </div>
                        @endif
                        <h1 class="text-2xl font-bold text-gray-800">ตะกร้าสินค้า</h1>
                    </div>

                    @if (isset($items) && !$items->isEmpty())
                        @php
                            $summaryTotalPrice = 0;
                            $summaryTotalOriginal = 0;
                        @endphp

                        @foreach ($items as $item)
                            @php
                                $quantity = $item->quantity;
                                $price = $item->price; // ราคาขายจริง
                                $originalPrice = $item->attributes->has('original_price')
                                    ? $item->attributes->original_price
                                    : $price;

                                $totalPrice = $price * $quantity;
                                $isFree = $totalPrice <= 0;

                                // ★★★ [แก้ไขจุดสำคัญ] ★★★
                                // ถ้าเป็นของแถม (Free) ให้คิดราคาต้น (Original) เป็น 0 บาทด้วยสำหรับการคำนวณสรุปยอด
                                // เพื่อไม่ให้ยอด Subtotal และ Discount สูงเกินจริงจนลูกค้าสับสน
                                if ($isFree) {
                                    $calcOriginalPrice = 0;
                                } else {
                                    $calcOriginalPrice = $originalPrice;
                                }

                                $totalOriginalPrice = $calcOriginalPrice * $quantity;

                                // คำนวณส่วนลดจากราคาที่เราปรับแล้ว
                                $lineDiscount = $totalOriginalPrice - $totalPrice;
                                $hasDiscount = $lineDiscount > 0;

                                // สะสมยอดรวมท้ายบิล
                                $summaryTotalPrice += $totalPrice;
                                $summaryTotalOriginal += $totalOriginalPrice;

                                // Use the new accessor from the ProductSalepage model
                                $productModel = $products[$item->id] ?? null;
                                $displayImage = $productModel ? $productModel->cover_image_url : 'https://via.placeholder.com/150?text=No+Image';
                            @endphp

                            <div
                                class="flex flex-col md:flex-row md:items-start md:justify-between border-b border-gray-200 py-6 gap-4">
                                {{-- 1. Checkbox & Image & Details --}}
                                <div class="flex flex-row gap-4 w-full md:w-auto items-start">
                                    <div class="mt-8 md:mt-10">
                                        <input type="checkbox" name="selected_items[]" value="{{ $item->id }}" checked
                                            data-price="{{ $totalPrice }}" data-original-price="{{ $totalOriginalPrice }}"
                                            data-discount="{{ $lineDiscount }}"
                                            class="item-checkbox w-5 h-5 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 cursor-pointer"
                                            onchange="calculateTotal()">
                                    </div>

                                    <div class="flex-shrink-0">
                                        <img src="{{ $displayImage }}" alt="{{ $item->name }}" loading="lazy"
                                            class="w-20 h-20 object-cover rounded-lg md:w-24 md:h-24 bg-gray-100 border border-gray-100"
                                            onerror="this.src='https://via.placeholder.com/150?text=Error'" />
                                    </div>

                                    <div class="flex-1 mt-1">
                                        <h1 class="font-bold text-gray-800 text-sm md:text-base">{{ $item->name }}</h1>
                                        <p class="text-xs text-gray-500">Code:
                                            {{ $item->attributes->pd_code ?? ($item->attributes->pd_sp_code ?? '-') }}</p>

                                        <p class="text-xs text-gray-500 mt-1">ราคาต่อชิ้น:
                                            @if ($isFree)
                                                <span class="font-bold text-red-600">ฟรี</span>
                                            @elseif ($hasDiscount)
                                                <s class="text-gray-400">฿{{ number_format($originalPrice) }}</s>
                                                <span
                                                    class="font-semibold text-red-600 ml-1">฿{{ number_format($price) }}</span>
                                            @else
                                                <span class="text-gray-800">฿{{ number_format($price) }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                {{-- 2. Actions & Total Price --}}
                                <div
                                    class="flex flex-row justify-between items-center md:flex-col md:items-end gap-4 w-full md:w-auto mt-2 md:mt-0 pl-9 md:pl-0">
                                    <div class="flex flex-col items-end">

                                        @if ($isFree)
                                            <div class="text-2xl font-bold text-red-600">ฟรี</div>
                                        @elseif ($hasDiscount)
                                            <div class="text-2xl font-bold text-red-600">฿{{ number_format($totalPrice) }}
                                            </div>
                                            <span
                                                class="text-[10px] md:text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full mt-1">
                                                ประหยัด ฿{{ number_format($lineDiscount) }}
                                            </span>
                                        @else
                                            <div class="text-2xl font-bold text-emerald-600">
                                                ฿{{ number_format($totalPrice) }}</div>
                                        @endif

                                    </div>

                                    <div class="flex flex-col sm:flex-row items-end sm:items-center gap-3">
                                        <div class="flex items-center border border-gray-300 rounded h-10 md:h-12 bg-white">
                                            <button type="button"
                                                class="cart-action-btn px-3 py-1 text-gray-600 hover:bg-gray-100 h-full flex items-center text-lg"
                                                data-url="{{ route('cart.update', ['id' => $item->id, 'action' => 'decrease']) }}"
                                                data-method="PATCH">-</button>
                                            <span
                                                class="font-bold text-gray-700 text-sm md:text-base w-12 text-center">{{ $quantity }}</span>
                                            <button type="button"
                                                class="cart-action-btn px-3 py-1 text-gray-600 hover:bg-gray-100 h-full flex items-center text-lg"
                                                data-url="{{ route('cart.update', ['id' => $item->id, 'action' => 'increase']) }}"
                                                data-method="PATCH">+</button>
                                        </div>
                                        <button type="button"
                                            class="cart-action-btn text-red-500 hover:text-red-700 font-medium text-sm md:text-base underline md:no-underline md:btn md:btn-ghost md:btn-sm md:text-red-500"
                                            data-url="{{ route('cart.remove', $item->id) }}"
                                            data-method="DELETE">ลบรายการ</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Summary Section --}}
                        <div class="flex flex-col lg:flex-row justify-end gap-5 mt-10">
                            <div class="w-full lg:w-[400px]">
                                <div class="flex justify-between mt-5 text-base text-gray-600">
                                    <div>ยอดรวมสินค้า (<span id="selected-count">{{ count($items) }}</span> รายการ)</div>
                                    {{-- ยอดรวมนี้จะไม่รวมราคาแฝงของสินค้าฟรีแล้ว --}}
                                    <div class="font-medium">฿<span
                                            id="subtotal-display">{{ number_format($summaryTotalOriginal) }}</span></div>
                                </div>
                                <div class="flex justify-between mt-2 text-base text-red-500">
                                    <div>ส่วนลดรวม</div>
                                    <div class="font-medium">-฿<span
                                            id="discount-display">{{ number_format($summaryTotalOriginal - $summaryTotalPrice) }}</span>
                                    </div>
                                </div>
                                <div class="border-t border-gray-200 my-4"></div>
                                <div class="flex justify-between items-center mb-6">
                                    <div>
                                        <h1 class="font-bold text-xl md:text-2xl text-gray-800">ยอดสุทธิที่ต้องชำระ</h1>
                                        <p class="text-xs text-gray-500">(รวมภาษีมูลค่าเพิ่มแล้ว)</p>
                                    </div>
                                    <div>
                                        <h1 class="text-emerald-600 font-bold text-2xl md:text-3xl">฿<span
                                                id="total-display">{{ number_format($summaryTotalPrice) }}</span></h1>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-3">
                                    @auth
                                        <button type="submit" id="checkout-btn"
                                            class="btn btn-success text-white w-full text-lg h-12">ดำเนินการชำระเงิน</button>
                                    @endauth
                                    @guest
                                        <a href="{{ route('login') }}"
                                            class="btn btn-success text-white w-full text-lg h-12 flex items-center justify-center">เข้าสู่ระบบเพื่อชำระเงิน</a>
                                    @endguest
                                    <a href="{{ route('allproducts') }}"
                                        class="btn btn-outline border-gray-300 text-gray-600 hover:bg-gray-50 w-full flex items-center justify-center">ซื้อสินค้าต่อ</a>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Empty State --}}
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

    <script>
        function numberWithCommas(x) {
            if (x === undefined || x === null) return "0";
            return Math.round(x).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function toggleAll(source) {
            document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = source.checked);
            calculateTotal();
        }

        function calculateTotal() {
            let totalSale = 0,
                totalOrig = 0,
                totalDisc = 0,
                count = 0;
            const checkoutBtn = document.getElementById('checkout-btn');

            document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
                let price = parseFloat(cb.dataset.price) || 0;
                let orig = parseFloat(cb.dataset.originalPrice) || price;

                // คำนวณส่วนลดใหม่ใน JS (ราคาเต็ม - ราคาขาย)
                // ถ้าเป็นของแถม orig จะถูกส่งมาเป็น 0 แล้วจาก PHP ทำให้ส่วนลดเป็น 0 (ไม่เอามาคิด)
                let disc = orig - price;
                if (disc < 0) disc = 0; // กันพลาด

                totalSale += price;
                totalOrig += orig;
                totalDisc += disc;
                count++;
            });

            const setVal = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.innerText = numberWithCommas(val);
            };
            setVal('total-display', totalSale);
            setVal('subtotal-display', totalOrig);
            setVal('discount-display', totalDisc);
            const countEl = document.getElementById('selected-count');
            if (countEl) countEl.innerText = count;

            const selectAll = document.getElementById('select-all');
            const totalBoxes = document.querySelectorAll('.item-checkbox').length;
            if (selectAll) {
                selectAll.checked = (count === totalBoxes && count > 0);
                selectAll.indeterminate = (count > 0 && count < totalBoxes);
            }

            if (checkoutBtn) {
                checkoutBtn.disabled = (count === 0);
                checkoutBtn.classList.toggle('opacity-50', count === 0);
                checkoutBtn.classList.toggle('cursor-not-allowed', count === 0);
            }
        }

        window.addEventListener("pageshow", calculateTotal);
        document.addEventListener("DOMContentLoaded", function() {
            calculateTotal();
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            document.querySelectorAll('.cart-action-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (!csrfToken) return;

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = this.dataset.url;
                    form.style.display = 'none';

                    const addInput = (n, v) => {
                        const i = document.createElement('input');
                        i.type = 'hidden';
                        i.name = n;
                        i.value = v;
                        form.appendChild(i);
                    };
                    addInput('_token', csrfToken);
                    if (this.dataset.method !== 'POST') addInput('_method', this.dataset.method);

                    document.body.appendChild(form);
                    form.submit();
                });
            });
        });
    </script>
@endsection
