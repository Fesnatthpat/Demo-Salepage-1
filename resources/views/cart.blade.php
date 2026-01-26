@extends('layout')

@section('title', 'ตะกร้าสินค้า | Salepage Demo')

@section('content')
    <div class="container px-4 mx-auto md:px-8 lg:p-12">
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
                                $price = $item->price;
                                $originalPrice = $item->attributes->has('original_price')
                                    ? $item->attributes->original_price
                                    : $price;
                                $totalPrice = $price * $quantity;
                                $isFree = $item->attributes->has('is_freebie') && $item->attributes->is_freebie;

                                // คิดราคาต้นเป็น 0 ถ้าเป็นของแถม เพื่อไม่ให้ยอดรวมเพี้ยน
                                $calcOriginalPrice = $isFree ? 0 : $originalPrice;
                                $totalOriginalPrice = $calcOriginalPrice * $quantity;

                                $lineDiscount = $totalOriginalPrice - $totalPrice;
                                $hasDiscount = $lineDiscount > 0;

                                $summaryTotalPrice += $totalPrice;
                                $summaryTotalOriginal += $totalOriginalPrice;

                                $productModel = $products[$item->id] ?? null;
                                $displayImage = $productModel
                                    ? $productModel->cover_image_url
                                    : 'https://via.placeholder.com/150?text=No+Image';
                            @endphp

                            <div
                                class="flex flex-col md:flex-row md:items-start md:justify-between border-b border-gray-200 py-6 gap-4">
                                {{-- Checkbox & Details --}}
                                <div class="flex flex-row gap-4 w-full md:w-auto items-start">
                                    <div class="mt-8 md:mt-10">
                                        <input type="checkbox" name="selected_items[]" value="{{ $item->id }}" checked
                                            data-price="{{ $totalPrice }}" data-original-price="{{ $totalOriginalPrice }}"
                                            class="item-checkbox w-5 h-5 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 cursor-pointer"
                                            onchange="calculateTotal()">
                                    </div>
                                    <div class="flex-shrink-0">
                                        <img src="{{ $displayImage }}" alt="{{ $item->name }}"
                                            class="w-20 h-20 object-cover rounded-lg bg-gray-100 border border-gray-100">
                                    </div>
                                    <div class="flex-1 mt-1">
                                        <h1 class="font-bold text-gray-800 text-sm md:text-base">{{ $item->name }}</h1>
                                        <p class="text-xs text-gray-500">Code: {{ $item->attributes->pd_code ?? '-' }}</p>
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

                                {{-- Actions --}}
                                <div
                                    class="flex flex-row justify-between items-center md:flex-col md:items-end gap-4 w-full md:w-auto mt-2 md:mt-0 pl-9 md:pl-0">
                                    <div class="flex flex-col items-end">
                                        @if ($isFree)
                                            <div class="text-2xl font-bold text-red-600">ฟรี</div>
                                        @else
                                            <div class="text-2xl font-bold text-emerald-600">
                                                ฿{{ number_format($totalPrice) }}</div>
                                        @endif
                                    </div>
                                    <div class="flex flex-col sm:flex-row items-end sm:items-center gap-3">
                                        <div class="flex items-center border border-gray-300 rounded h-10 md:h-12 bg-white">
                                            <button type="button"
                                                class="cart-action-btn px-3 text-gray-600 hover:bg-gray-100 h-full flex items-center"
                                                data-url="{{ route('cart.update', ['id' => $item->id, 'action' => 'decrease']) }}"
                                                data-method="PATCH">-</button>
                                            <span
                                                class="font-bold text-gray-700 text-sm md:text-base w-12 text-center">{{ $quantity }}</span>
                                            <button type="button"
                                                class="cart-action-btn px-3 text-gray-600 hover:bg-gray-100 h-full flex items-center"
                                                data-url="{{ route('cart.update', ['id' => $item->id, 'action' => 'increase']) }}"
                                                data-method="PATCH">+</button>
                                        </div>
                                        <button type="button"
                                            class="cart-action-btn text-red-500 hover:text-red-700 text-sm md:btn md:btn-ghost md:btn-sm"
                                            data-url="{{ route('cart.remove', $item->id) }}"
                                            data-method="DELETE">ลบ</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Promotions Area --}}
                        @if (isset($applicablePromotions) && $applicablePromotions->isNotEmpty() && $giftableProducts->isNotEmpty() && isset($freebieLimit) && $freebieLimit > 0)
                            <div class="mt-8 mb-6" x-data="promoManager({ freebieLimit: {{ $freebieLimit }} })">
                                <div class="p-6 bg-emerald-50 border-2 border-dashed border-emerald-200 rounded-lg">
                                    <h2 class="text-xl font-bold text-emerald-800 mb-2">🎉 คุณได้รับสิทธิ์เลือกของแถม</h2>
                                    <p class="text-sm text-gray-600 mb-4">
                                        คุณสามารถเลือกของแถมได้ <span x-text="selectedFreebies.length">0</span> จากทั้งหมด <span x-text="freebieLimit">{{ $freebieLimit }}</span> ชิ้น
                                    </p>

                                    <form action="{{ route('cart.addFreebies') }}" method="POST">
                                        @csrf
                                        {{-- This hidden div will hold our actual input values for the form submission --}}
                                        <template x-for="id in selectedFreebies" :key="id">
                                            <input type="hidden" name="selected_freebies[]" :value="id">
                                        </template>

                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                            @foreach ($giftableProducts as $gift)
                                                <label
                                                    class="relative flex flex-col items-center p-3 border rounded-lg cursor-pointer transition-all"
                                                    :class="selectedFreebies.includes({{ $gift->pd_sp_id }}) ? 'bg-white border-emerald-400 ring-2 ring-emerald-300' : 'bg-white/50 border-gray-200 hover:bg-white'">
                                                    <input type="checkbox"
                                                           class="absolute top-2 right-2 h-5 w-5 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500"
                                                           :checked="selectedFreebies.includes({{ $gift->pd_sp_id }})"
                                                           @click.prevent="toggleFreebie({{ $gift->pd_sp_id }})">

                                                    <img src="{{ $gift->cover_image_url ?? 'https://via.placeholder.com/150' }}"
                                                         class="w-20 h-20 object-cover rounded bg-white">
                                                    <p class="text-xs text-center mt-2 font-medium">{{ $gift->pd_sp_name }}</p>
                                                    <span class="text-[10px] font-bold text-white bg-red-500 px-2 py-0.5 rounded-full mt-1">ฟรี</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <button type="submit"
                                                class="btn btn-primary mt-4 w-full md:w-auto"
                                                :disabled="selectedFreebies.length === 0">ยืนยันของแถม</button>
                                    </form>
                                </div>
                            </div>
                        @endif

                        {{-- Summary --}}
                        <div class="flex flex-col lg:flex-row justify-end gap-5 mt-10">
                            <div class="w-full lg:w-[400px]">
                                <div class="flex justify-between mt-5 text-base text-gray-600">
                                    <div>ยอดรวมสินค้า (<span id="selected-count">{{ count($items) }}</span> รายการ)</div>
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
                                    <h1 class="font-bold text-xl text-gray-800">ยอดสุทธิ</h1>
                                    <h1 class="text-emerald-600 font-bold text-3xl">฿<span
                                            id="total-display">{{ number_format($summaryTotalPrice) }}</span></h1>
                                </div>
                                <button type="submit" id="checkout-btn"
                                    class="btn btn-success text-white w-full text-lg h-12">ชำระเงิน</button>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-20 bg-gray-50 rounded-lg">
                            <h2 class="text-2xl font-bold text-gray-400 mb-2">ตะกร้าว่างเปล่า</h2>
                            <a href="{{ route('allproducts') }}" class="btn btn-primary mt-4">ไปเลือกซื้อสินค้า</a>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <script>
        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function calculateTotal() {
            let totalSale = 0,
                totalOrig = 0;
            let count = 0;
            document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
                totalSale += parseFloat(cb.dataset.price) || 0;
                totalOrig += parseFloat(cb.dataset.originalPrice) || 0;
                count++;
            });

            let disc = totalOrig - totalSale;
            if (disc < 0) disc = 0;

            document.getElementById('total-display').innerText = numberWithCommas(totalSale);
            document.getElementById('subtotal-display').innerText = numberWithCommas(totalOrig);
            document.getElementById('discount-display').innerText = numberWithCommas(disc);
            document.getElementById('selected-count').innerText = count;

            const btn = document.getElementById('checkout-btn');
            if (btn) btn.disabled = (count === 0);
        }

        function toggleAll(source) {
            document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = source.checked);
            calculateTotal();
        }

        document.addEventListener("DOMContentLoaded", function() {
            calculateTotal();
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            document.querySelectorAll('.cart-action-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = this.dataset.url;
                    form.style.display = 'none';

                    const inputCsrf = document.createElement('input');
                    inputCsrf.type = 'hidden';
                    inputCsrf.name = '_token';
                    inputCsrf.value = csrfToken;
                    form.appendChild(inputCsrf);

                    if (this.dataset.method !== 'POST') {
                        const inputMethod = document.createElement('input');
                        inputMethod.type = 'hidden';
                        inputMethod.name = '_method';
                        inputMethod.value = this.dataset.method;
                        form.appendChild(inputMethod);
                    }
                    document.body.appendChild(form);
                    form.submit();
                });
            });
        });
    </script>
    <script>
        // ... (The existing script content remains here) ...

        document.addEventListener('alpine:init', () => {
            Alpine.data('promoManager', (config) => ({
                selectedFreebies: [],
                freebieLimit: config.freebieLimit || 0,
                toggleFreebie(id) {
                    const index = this.selectedFreebies.indexOf(id);
                    if (index > -1) {
                        this.selectedFreebies.splice(index, 1);
                    } else {
                        if (this.selectedFreebies.length < this.freebieLimit) {
                            this.selectedFreebies.push(id);
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'เลือกของแถมครบแล้ว',
                                    text: `คุณสามารถเลือกของแถมได้สูงสุด ${this.freebieLimit} ชิ้น`,
                                    confirmButtonColor: '#10b981'
                                });
                            } else {
                                alert(`คุณสามารถเลือกของแถมได้สูงสุด ${this.freebieLimit} ชิ้น`);
                            }
                        }
                    }
                }
            }));
        });
    </script>
@endsection
