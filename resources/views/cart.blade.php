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
                                <input type="checkbox" id="select-all"
                                    class="w-5 h-5 text-red-600 rounded border-gray-300 focus:ring-red-500 cursor-pointer"
                                    onclick="toggleAll(this)">
                            </div>
                        @endif
                        <h1 class="text-2xl font-bold text-gray-800">ตะกร้าสินค้า</h1>
                    </div>

                    @if (isset($items) && !$items->isEmpty())
                        <div id="cart-items-list">
                            @foreach ($items as $item)
                                @php
                                    $quantity = $item->quantity;
                                    $price = $item->price;
                                    $originalPrice = $item->attributes->has('original_price')
                                        ? $item->attributes->original_price
                                        : $price;
                                    $totalPrice = $price * $quantity;
                                    $isFree = $item->attributes->has('is_freebie') && $item->attributes->is_freebie;

                                    $calcOriginalPrice = $isFree ? 0 : $originalPrice;
                                    $totalOriginalPrice = $calcOriginalPrice * $quantity;

                                    $lineDiscount = $totalOriginalPrice - $totalPrice;
                                    $hasDiscount = $lineDiscount > 0;

                                    $displayImage =
                                        $item->attributes->image ?? 'https://via.placeholder.com/150?text=No+Image';
                                @endphp

                                <div
                                    class="flex flex-col md:flex-row md:items-start md:justify-between border-b border-gray-200 py-6 gap-4">
                                    {{-- Checkbox & Details --}}
                                    <div class="flex flex-row gap-4 w-full md:w-auto items-start">
                                        <div class="mt-8 md:mt-10">
                                            @if (!$isFree)
                                                <input type="checkbox" name="selected_items[]" value="{{ $item->id }}"
                                                    data-price="{{ $totalPrice }}"
                                                    data-original-price="{{ $totalOriginalPrice }}"
                                                    class="item-checkbox w-5 h-5 text-red-600 rounded border-gray-300 focus:ring-red-500 cursor-pointer"
                                                    onchange="onItemSelectionChange()">
                                            @else
                                                <div class="w-5">
                                                    {{-- ✅ ส่งเข้าช่อง selected_freebies[] แทน และใช้ ID ตัวเลขจริง --}}
                                                    <input type="checkbox" name="selected_freebies[]"
                                                        value="{{ $item->attributes->product_id ?? str_replace('_free', '', $item->id) }}"
                                                        class="free-item-checkbox hidden">
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-shrink-0">
                                            <img src="{{ $displayImage }}" alt="{{ $item->name }}"
                                                class="w-20 h-20 object-cover rounded-lg bg-gray-100 border border-gray-100">
                                        </div>
                                        <div class="flex-1 mt-1">
                                            <h1 class="font-bold text-gray-800 text-sm md:text-base break-words">
                                                {{ $item->name }}</h1>

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
                                                <div class="text-2xl font-bold text-red-600">
                                                    ฿{{ number_format($totalPrice) }}</div>
                                            @endif
                                        </div>
                                        <div class="flex flex-col sm:flex-row items-end sm:items-center gap-3">
                                            @if (!$isFree)
                                                <div
                                                    class="flex items-center border border-gray-300 rounded h-10 md:h-12 bg-white">
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
                                            @else
                                                <div
                                                    class="flex items-center px-4 h-10 md:h-12 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                                                    <span class="text-xs font-bold text-gray-400 italic">ของแถมจำนวน
                                                        {{ $quantity }} ชิ้น</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Summary --}}
                            <div class="flex flex-col lg:flex-row justify-end gap-5 mt-10">
                                <div class="w-full lg:w-[400px]">
                                    <div class="flex justify-between mt-5 text-base text-gray-600">
                                        <div>ยอดรวมสินค้า (<span id="selected-count">{{ count($items) }}</span> รายการ)
                                        </div>
                                        <div class="font-medium">฿<span
                                                id="subtotal-display">{{ number_format($subTotal) }}</span></div>
                                    </div>
                                    <div class="flex justify-between mt-2 text-base text-red-500 font-semibold">
                                        <div>ส่วนลดโปรโมชั่น</div>
                                        <div class="font-medium">-฿<span
                                                id="discount-display">{{ number_format($totalDiscount) }}</span>
                                        </div>
                                    </div>
                                    <div class="border-t border-gray-200 my-4"></div>
                                    <div class="flex justify-between items-center mb-6">
                                        <h1 class="font-bold text-xl text-gray-800">ยอดสุทธิ</h1>
                                        <h1 class="text-red-600 font-bold text-3xl">฿<span
                                                id="total-display">{{ number_format($total) }}</span></h1>
                                    </div>

                                    @if (isset($freebieLimit) && $freebieLimit > 0 && isset($giftableProducts) && $giftableProducts->count() > 0)
                                        <div class="mt-2 mb-6 p-5 bg-gradient-to-br from-pink-50 to-red-50 rounded-2xl border border-pink-100 shadow-sm"
                                            id="gift-selection-area">
                                            <div class="flex items-center justify-between mb-4">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="flex items-center justify-center w-6 h-6 rounded-full bg-pink-600 text-white shadow-sm">
                                                        <i class="fas fa-gift text-[10px]"></i>
                                                    </span>
                                                    <h3 class="font-bold text-pink-800 text-sm">เลือกของแถมของคุณ</h3>
                                                </div>
                                                <span
                                                    class="text-[10px] font-bold bg-white text-pink-600 border border-pink-200 px-2.5 py-1 rounded-full shadow-sm">
                                                    สิทธิ์คงเหลือ: <span id="gift-limit-display">{{ $freebieLimit }}</span>
                                                    ชิ้น
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3" id="gift-pool">
                                                @foreach ($giftableProducts as $gift)
                                                    <div class="relative group flex flex-col items-center p-2 rounded-xl border-2 transition-all duration-300 cursor-pointer bg-white border-gray-100 hover:border-pink-300"
                                                        id="gift-card-{{ $gift->pd_sp_id }}"
                                                        onclick="addCartGift('{{ $gift->pd_sp_id }}')">

                                                        <div
                                                            class="aspect-square w-full rounded-lg overflow-hidden bg-gray-50 mb-2">
                                                            <img src="{{ $gift->cover_image_url }}"
                                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                                        </div>
                                                        <p
                                                            class="text-[10px] font-bold text-gray-700 truncate px-1 text-center w-full">
                                                            {{ $gift->pd_sp_name }}</p>

                                                        <button type="button"
                                                            class="absolute -top-2 -left-2 w-6 h-6 bg-white border border-gray-300 text-gray-700 rounded-full items-center justify-center shadow-md hover:bg-gray-100 z-10 hidden"
                                                            id="gift-remove-{{ $gift->pd_sp_id }}"
                                                            onclick="event.stopPropagation(); removeCartGift('{{ $gift->pd_sp_id }}')">
                                                            <i class="fas fa-minus text-[10px]"></i>
                                                        </button>
                                                        <div class="absolute top-2 right-2 bg-pink-600 text-white w-5 h-5 rounded-full items-center justify-center shadow-sm hidden"
                                                            id="gift-badge-{{ $gift->pd_sp_id }}">
                                                            <span class="text-[10px] font-bold"
                                                                id="gift-count-{{ $gift->pd_sp_id }}">0</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="mt-4 pt-3 border-t border-pink-100 flex justify-center">
                                                <p class="text-[11px] font-bold text-pink-500 bg-white px-4 py-1 rounded-full shadow-sm border border-pink-50"
                                                    id="gift-count-text">
                                                    เลือกไปแล้ว 0 / {{ $freebieLimit }} ชิ้น
                                                </p>
                                            </div>
                                        </div>
                                    @endif

                                    <div id="hidden-gifts-inputs"></div>

                                    <button type="submit" id="checkout-btn"
                                        class="btn bg-red-600 hover:bg-red-700 border-none text-white w-full text-lg h-12 shadow-lg shadow-red-600/20 transition-all active:scale-95 font-black uppercase tracking-wider">
                                        ชำระเงิน <i class="fas fa-arrow-right ml-2"></i>
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-20 bg-gray-50 rounded-lg">
                                <h2 class="text-2xl font-bold text-gray-400 mb-2">ตะกร้าว่างเปล่า</h2>
                                <a href="{{ route('allproducts') }}"
                                    class="btn bg-red-600 hover:bg-red-700 border-none text-white mt-4">ไปเลือกซื้อสินค้า</a>
                            </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <script>
        const STORAGE_KEY = 'cart_selected_items';
        let selectedFreebiesArray = [];

        function getSavedSelectedItems() {
            const saved = localStorage.getItem(STORAGE_KEY);
            return saved ? JSON.parse(saved) : null;
        }

        function saveSelectedItems() {
            const selectedIds = Array.from(document.querySelectorAll('.item-checkbox:checked'))
                .map(cb => cb.value);
            localStorage.setItem(STORAGE_KEY, JSON.stringify(selectedIds));
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function onItemSelectionChange() {
            const selectedIds = Array.from(document.querySelectorAll('.item-checkbox:checked'))
                .map(cb => cb.value);

            // ดึง ID ของแถมที่ระบบแถมให้อัตโนมัติมา
            const autoFreeIds = Array.from(document.querySelectorAll('.free-item-checkbox')).map(cb => cb.value);

            const url = new URL(window.location.href);
            url.searchParams.set('selected_items', selectedIds.join(','));

            let allFreebies = [];
            if (selectedIds.length > 0) {
                allFreebies = [...autoFreeIds, ...selectedFreebiesArray];
            } else {
                allFreebies = [...selectedFreebiesArray];
            }

            allFreebies = allFreebies.filter(id => id && id.trim() !== '');

            if (allFreebies.length > 0) {
                url.searchParams.set('selected_freebies', [...new Set(allFreebies)].join(','));
            } else {
                url.searchParams.delete('selected_freebies');
            }
            window.location.href = url.toString();
        }

        function addCartGift(giftId) {
            const limit = parseInt(document.getElementById('gift-limit-display')?.innerText || 0);
            if (selectedFreebiesArray.length < limit) {
                selectedFreebiesArray.push(giftId);
                updateCartGiftUI();
                updateGiftUrlParams();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'เลือกเกินจำนวน',
                    text: `คุณสามารถเลือกของแถมได้สูงสุด ${limit} ชิ้น`,
                    confirmButtonColor: '#ec4899',
                });
            }
        }

        function removeCartGift(giftId) {
            const index = selectedFreebiesArray.lastIndexOf(giftId);
            if (index > -1) {
                selectedFreebiesArray.splice(index, 1);
                updateCartGiftUI();
                updateGiftUrlParams();
            }
        }

        function updateCartGiftUI() {
            const limit = parseInt(document.getElementById('gift-limit-display')?.innerText || 0);

            document.querySelectorAll('[id^="gift-card-"]').forEach(el => {
                el.classList.remove('border-pink-500', 'bg-pink-50');
                el.classList.add('border-gray-100', 'bg-white');
            });
            document.querySelectorAll('[id^="gift-remove-"]').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('[id^="gift-badge-"]').forEach(el => {
                el.classList.add('hidden');
                el.classList.remove('flex');
            });

            const hiddenContainer = document.getElementById('hidden-gifts-inputs');
            if (hiddenContainer) hiddenContainer.innerHTML = '';

            const counts = {};
            selectedFreebiesArray.forEach(id => {
                counts[id] = (counts[id] || 0) + 1;

                if (hiddenContainer) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'selected_freebies[]';
                    input.value = id;
                    hiddenContainer.appendChild(input);
                }
            });

            for (const [id, count] of Object.entries(counts)) {
                const card = document.getElementById(`gift-card-${id}`);
                const removeBtn = document.getElementById(`gift-remove-${id}`);
                const badge = document.getElementById(`gift-badge-${id}`);
                const countText = document.getElementById(`gift-count-${id}`);

                if (card) {
                    card.classList.remove('border-gray-100', 'bg-white');
                    card.classList.add('border-pink-500', 'bg-pink-50');
                }
                if (removeBtn) {
                    removeBtn.classList.remove('hidden');
                    removeBtn.classList.add('flex');
                }
                if (badge && countText) {
                    badge.classList.remove('hidden');
                    badge.classList.add('flex');
                    countText.innerText = count;
                }
            }

            const countTextEl = document.getElementById('gift-count-text');
            if (countTextEl) {
                countTextEl.innerText = `เลือกไปแล้ว ${selectedFreebiesArray.length} / ${limit} ชิ้น`;
            }

            const giftCards = document.querySelectorAll('[id^="gift-card-"]');
            if (giftCards.length === 1 && selectedFreebiesArray.length < limit && limit > 0) {
                const onlyGiftId = giftCards[0].id.replace('gift-card-', '');
                while (selectedFreebiesArray.length < limit) {
                    selectedFreebiesArray.push(onlyGiftId);
                }
                updateCartGiftUI();
                updateGiftUrlParams();
            }
        }

        function updateGiftUrlParams() {
            const url = new URL(window.location.href);

            const autoFreeIds = Array.from(document.querySelectorAll('.free-item-checkbox')).map(cb => cb.value);
            let allFreebies = [...autoFreeIds, ...selectedFreebiesArray].filter(id => id && id.trim() !== '');

            if (allFreebies.length > 0) {
                url.searchParams.set('selected_freebies', [...new Set(allFreebies)].join(','));
            } else {
                url.searchParams.delete('selected_freebies');
            }
            window.history.replaceState({}, '', url);
        }

        function calculateTotal() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            const checkedBoxes = Array.from(checkboxes).filter(cb => cb.checked);
            const count = checkedBoxes.length;

            const freeCheckboxes = document.querySelectorAll('.free-item-checkbox');
            freeCheckboxes.forEach(fcb => {
                fcb.checked = (count > 0);
            });

            saveSelectedItems();

            const selectAll = document.getElementById('select-all');
            if (selectAll) {
                const totalNormalItems = document.querySelectorAll('.item-checkbox').length;
                selectAll.checked = (totalNormalItems > 0 && totalNormalItems === count);
            }

            const selectedCountEl = document.getElementById('selected-count');
            if (selectedCountEl) {
                const freebieCount =
                    {{ isset($items) ? $items->filter(fn($i) => $i->attributes->get('is_freebie'))->sum('quantity') : 0 }};
                selectedCountEl.innerText = count + freebieCount;
            }

            const btn = document.getElementById('checkout-btn');
            if (btn) {
                btn.disabled = (count === 0);
                if (count === 0) {
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }

        function toggleAll(source) {
            document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = source.checked);
            onItemSelectionChange();
        }

        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const selectedFromUrl = urlParams.get('selected_items');
            const savedIds = getSavedSelectedItems();
            const checkboxes = document.querySelectorAll('.item-checkbox');

            if (selectedFromUrl !== null) {
                const ids = selectedFromUrl ? selectedFromUrl.split(',').filter(id => id !== '') : [];
                checkboxes.forEach(cb => {
                    cb.checked = ids.includes(cb.value);
                });
            } else if (savedIds !== null) {
                checkboxes.forEach(cb => {
                    cb.checked = savedIds.includes(cb.value);
                });
            } else {
                checkboxes.forEach(cb => cb.checked = false);
            }

            const selectedFreebiesFromUrl = urlParams.get('selected_freebies');
            if (selectedFreebiesFromUrl) {
                const urlFreebies = selectedFreebiesFromUrl.split(',').filter(id => id !== '');
                const autoFreeIds = Array.from(document.querySelectorAll('.free-item-checkbox')).map(cb => cb
                .value);
                selectedFreebiesArray = urlFreebies.filter(id => !autoFreeIds.includes(id));
            }
            updateCartGiftUI();
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
@endsection
