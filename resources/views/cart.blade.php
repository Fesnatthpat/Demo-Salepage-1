@extends('layout')

@section('title', 'ตะกร้าสินค้า | Salepage Demo')

@section('content')
    <div class="bg-gray-50/50 min-h-screen py-4 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('payment.checkout') }}" method="GET" id="checkout-form">

                {{-- Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 mb-6 sm:mb-8">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">ตะกร้าสินค้า</h1>
                    @if (isset($items) && !$items->isEmpty())
                        <span class="bg-red-100 text-red-600 py-1 px-3 rounded-full text-xs sm:text-sm font-bold shadow-sm w-fit"
                            id="header-item-count">
                            {{ count($items) }} รายการ
                        </span>
                    @endif
                </div>

                @if (isset($items) && !$items->isEmpty())
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

                        {{-- 🛒 ฝั่งซ้าย: รายการสินค้า --}}
                        <div class="lg:col-span-8 space-y-4">

                            {{-- ปุ่มเลือกทั้งหมด --}}
                            <div class="bg-white p-3 sm:p-4 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" id="select-all"
                                        class="w-4 h-4 sm:w-5 sm:h-5 text-red-600 rounded border-gray-300 focus:ring-red-500 cursor-pointer transition-all"
                                        onclick="toggleAll(this)">
                                    <label for="select-all"
                                        class="text-sm sm:text-base text-gray-700 font-bold cursor-pointer select-none">เลือกสินค้าทั้งหมด</label>
                                </div>

                                <button type="button" id="remove-selected-btn" class="hidden text-red-500 hover:text-red-700 transition-colors flex items-center gap-1.5 text-xs sm:text-sm font-bold bg-red-50 px-3 py-1.5 rounded-lg border border-red-100" onclick="removeSelectedItems()">
                                    <i class="fas fa-trash-alt"></i> ลบที่เลือก
                                </button>
                            </div>

                            <div id="cart-items-list" class="space-y-3 sm:space-y-4">
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

                                    <div class="bg-white p-3 sm:p-5 rounded-xl sm:rounded-2xl shadow-sm border {{ $isFree ? 'border-red-100 bg-red-50/30' : 'border-gray-100 hover:border-red-200' }} transition-all flex flex-row items-start gap-3 sm:gap-4 group">

                                        {{-- Checkbox --}}
                                        <div class="pt-4 sm:pt-0 sm:self-center flex-shrink-0">
                                            @if (!$isFree)
                                                <input type="checkbox" name="selected_items[]" value="{{ $item->id }}"
                                                    data-price="{{ $totalPrice }}"
                                                    data-original-price="{{ $totalOriginalPrice }}"
                                                    class="item-checkbox w-4 h-4 sm:w-5 sm:h-5 text-red-600 rounded border-gray-300 focus:ring-red-500 cursor-pointer"
                                                    onchange="onItemSelectionChange()">
                                            @else
                                                <div class="w-4 sm:w-5">
                                                    <input type="checkbox" name="selected_items[]"
                                                        value="{{ $item->id }}" class="free-item-checkbox hidden">
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Image --}}
                                        <div class="relative flex-shrink-0">
                                            <img src="{{ $displayImage }}" alt="{{ $item->name }}"
                                                class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-lg sm:rounded-xl bg-gray-50 border border-gray-100 group-hover:scale-105 transition-transform">
                                            @if ($isFree)
                                                <div
                                                    class="absolute -top-2 -right-2 bg-red-600 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 sm:py-1 rounded-full shadow-sm shadow-red-200 uppercase tracking-wide border border-white">
                                                    Free
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Details --}}
                                        <div class="flex-1 min-w-0 flex flex-col justify-between h-full py-1">
                                            <div class="flex flex-col sm:flex-row justify-between items-start gap-1 sm:gap-4">
                                                <h3 class="font-bold text-gray-800 text-sm sm:text-base leading-snug line-clamp-2 pr-2 sm:pr-4 w-full sm:w-2/3">
                                                    {{ $item->name }}
                                                </h3>

                                                {{-- Price Section --}}
                                                <div class="text-left sm:text-right w-full sm:w-1/3 shrink-0 mt-1 sm:mt-0">
                                                    @if ($isFree)
                                                        <div class="text-lg sm:text-xl font-black text-red-600">ฟรี</div>
                                                    @else
                                                        <div class="text-lg sm:text-xl font-black text-red-600 leading-none">
                                                            ฿{{ number_format($totalPrice, 2) }}</div>
                                                        @if ($hasDiscount)
                                                            <div class="text-[10px] sm:text-xs text-gray-400 line-through mt-0.5">
                                                                ฿{{ number_format($totalOriginalPrice, 2) }}</div>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-3 mt-3 w-full">
                                                <div class="text-[11px] sm:text-xs font-medium {{ $isFree ? 'text-red-500' : 'text-gray-500' }}">
                                                    @if ($isFree)
                                                        <i class="fas fa-gift mr-1"></i> สินค้าสมนาคุณ
                                                    @elseif ($hasDiscount)
                                                        ราคาปกติ: <s>฿{{ number_format($originalPrice, 2) }}</s> <span
                                                            class="text-red-500 font-bold ml-1">฿{{ number_format($price, 2) }}</span>/ชิ้น
                                                    @else
                                                        ราคา: ฿{{ number_format($price, 2) }}/ชิ้น
                                                    @endif
                                                </div>

                                                {{-- Actions --}}
                                                <div class="flex items-center gap-2 sm:gap-3 self-end xl:self-auto">
                                                    @if (!$isFree)
                                                        <div class="flex items-center bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl h-8 sm:h-10 overflow-hidden shrink-0">
                                                            <button type="button"
                                                                class="cart-action-btn w-8 sm:w-10 text-gray-500 hover:bg-gray-200 hover:text-red-600 font-bold text-base sm:text-lg flex items-center justify-center transition-colors"
                                                                data-url="{{ route('cart.update', ['id' => $item->id, 'action' => 'decrease']) }}"
                                                                data-method="PATCH">-</button>
                                                            <span
                                                                class="font-bold text-gray-800 text-xs sm:text-sm w-8 sm:w-10 text-center flex items-center justify-center bg-white h-full border-x border-gray-100">{{ $quantity }}</span>
                                                            <button type="button"
                                                                class="cart-action-btn w-8 sm:w-10 text-gray-500 hover:bg-gray-200 hover:text-red-600 font-bold text-base sm:text-lg flex items-center justify-center transition-colors"
                                                                data-url="{{ route('cart.update', ['id' => $item->id, 'action' => 'increase']) }}"
                                                                data-method="PATCH">+</button>
                                                        </div>
                                                        <button type="button"
                                                            class="cart-action-btn w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-red-50 text-red-500 hover:bg-red-600 hover:text-white flex items-center justify-center transition-colors shadow-sm shrink-0"
                                                            data-url="{{ route('cart.remove', $item->id) }}"
                                                            data-method="DELETE" title="ลบสินค้า">
                                                            <i class="fas fa-trash-alt text-xs sm:text-sm"></i>
                                                        </button>
                                                    @else
                                                        <div
                                                            class="px-2 sm:px-4 py-1.5 sm:py-2 bg-red-50 rounded-lg sm:rounded-xl border border-red-100 text-red-600 text-[10px] sm:text-xs font-bold flex items-center gap-1.5 sm:gap-2 shrink-0">
                                                            <i class="fas fa-check-circle"></i> ได้รับ {{ $quantity }} ชิ้น
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- 🧾 ฝั่งขวา: สรุปยอดสั่งซื้อ (Sticky) --}}
                        <div class="lg:col-span-4 space-y-6 sticky top-4 sm:top-8 mt-4 lg:mt-0">

                            <div class="bg-white p-5 sm:p-6 rounded-2xl sm:rounded-3xl shadow-lg border border-gray-100">
                                <h2 class="text-base sm:text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 sm:pb-4 mb-3 sm:mb-4 flex items-center gap-2">
                                    <i class="fas fa-receipt text-red-500"></i> สรุปคำสั่งซื้อ
                                </h2>

                                <div class="space-y-2 sm:space-y-3 mb-5 sm:mb-6">
                                    <div class="flex justify-between text-xs sm:text-sm text-gray-600">
                                        <span>ยอดรวมสินค้า (<span id="selected-count"
                                                class="font-bold text-gray-900">{{ count($items) }}</span> ชิ้น)</span>
                                        <span class="font-bold text-gray-900">฿<span
                                                id="subtotal-display">{{ number_format($subTotal, 2) }}</span></span>
                                    </div>
                                    <div
                                        class="flex justify-between text-xs sm:text-sm text-red-500 font-medium bg-red-50 p-2 rounded-lg">
                                        <span><i class="fas fa-tag mr-1"></i> ส่วนลดโปรโมชั่น</span>
                                        <span>-฿<span
                                                id="discount-display">{{ number_format($totalDiscount, 2) }}</span></span>
                                    </div>
                                </div>

                                <div class="border-t border-dashed border-gray-200 my-4"></div>

                                <div class="flex justify-between items-end mb-5 sm:mb-6">
                                    <span class="font-bold text-gray-800 text-sm sm:text-base">ยอดสุทธิ</span>
                                    <div class="text-right">
                                        <div class="text-2xl sm:text-3xl font-black text-red-600 tracking-tight leading-none">฿<span
                                                id="total-display">{{ number_format($total, 2) }}</span></div>
                                        <div class="text-[9px] sm:text-[10px] text-gray-400 mt-1">รวมภาษีมูลค่าเพิ่มแล้ว</div>
                                    </div>
                                </div>

                                {{-- ★ ส่วนเลือกของแถม (สำหรับโปรโมชั่นแบบชุด) ★ --}}
                                @php
                                    $hasGifts = (isset($freebieLimit) && $freebieLimit > 0 && isset($giftableProducts) && $giftableProducts->count() > 0);
                                @endphp
                                <div class="mb-5 sm:mb-6 p-3 sm:p-4 bg-gradient-to-br from-pink-50 to-red-50 rounded-xl sm:rounded-2xl border border-pink-200 shadow-inner"
                                    id="gift-selection-area" style="display: {{ $hasGifts ? 'block' : 'none' }};">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-0 mb-3 sm:mb-4">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="flex items-center justify-center w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-pink-500 text-white shadow-md">
                                                <i class="fas fa-gift text-[10px] sm:text-xs"></i>
                                            </span>
                                            <h3 class="font-bold text-pink-900 text-xs sm:text-sm">เลือกของแถม</h3>
                                        </div>
                                        <span
                                            class="text-[10px] sm:text-xs font-black bg-white text-pink-600 border-2 border-pink-200 px-2 sm:px-3 py-1 rounded-full shadow-sm w-fit">
                                            เลือกได้: <span id="gift-limit-display">{{ $freebieLimit ?? 0 }}</span> ชิ้น
                                        </span>
                                    </div>

                                    {{-- Responsive Grid: มือถือ 2, แท็บเล็ต 3, คอม 2 (เพราะอยู่ Sidebar) --}}
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-2 gap-2 sm:gap-3" id="gift-pool">
                                        @if (isset($giftableProducts))
                                            @foreach ($giftableProducts as $gift)
                                                <div class="relative group flex flex-col items-center p-1.5 sm:p-2 rounded-lg sm:rounded-xl border-2 transition-all duration-300 cursor-pointer bg-white border-transparent shadow-sm hover:shadow-md hover:border-pink-300 hover:-translate-y-1"
                                                    id="gift-card-{{ $gift->pd_sp_id }}"
                                                    onclick="addCartGift('{{ $gift->pd_sp_id }}')">

                                                    <div
                                                        class="aspect-square w-full rounded-md sm:rounded-lg overflow-hidden bg-gray-50 mb-1.5 sm:mb-2 relative">
                                                        <img src="{{ $gift->cover_image_url }}"
                                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                                        <div
                                                            class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors">
                                                        </div>
                                                    </div>
                                                    <p
                                                        class="text-[9px] sm:text-[10px] font-bold text-gray-700 line-clamp-2 px-1 text-center w-full leading-tight">
                                                        {{ $gift->pd_sp_name }}
                                                    </p>

                                                    <button type="button"
                                                        class="absolute -top-1.5 -left-1.5 sm:-top-2 sm:-left-2 w-5 h-5 sm:w-6 sm:h-6 bg-white border-2 border-gray-200 text-gray-600 rounded-full flex items-center justify-center shadow-md hover:bg-gray-100 hover:border-gray-400 z-10 hidden transition-all"
                                                        id="gift-remove-{{ $gift->pd_sp_id }}"
                                                        onclick="event.stopPropagation(); removeCartGift('{{ $gift->pd_sp_id }}')">
                                                        <i class="fas fa-minus text-[8px] sm:text-[10px]"></i>
                                                    </button>
                                                    <div class="absolute top-1.5 right-1.5 sm:top-2 sm:right-2 bg-pink-600 text-white w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center shadow-md hidden border-2 border-white"
                                                        id="gift-badge-{{ $gift->pd_sp_id }}">
                                                        <span class="text-[10px] sm:text-xs font-black"
                                                            id="gift-count-{{ $gift->pd_sp_id }}">0</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="mt-3 sm:mt-4 pt-2 sm:pt-3 border-t border-pink-200/60 flex justify-center">
                                        <p class="text-[10px] sm:text-[11px] font-bold text-pink-600 bg-white px-3 sm:px-4 py-1 sm:py-1.5 rounded-full shadow-sm border border-pink-100"
                                            id="gift-count-text">
                                            เลือกไปแล้ว 0 / {{ $freebieLimit ?? 0 }} ชิ้น
                                        </p>
                                    </div>
                                </div>

                                <div id="hidden-gifts-inputs"></div>

                                <button type="submit" id="checkout-btn"
                                    class="w-full h-12 sm:h-14 bg-red-600 hover:bg-red-700 text-white rounded-xl sm:rounded-2xl font-bold text-base sm:text-lg shadow-lg shadow-red-600/30 transition-all transform active:scale-95 flex items-center justify-center gap-2 group disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                    <span>ดำเนินการชำระเงิน</span>
                                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                </button>

                                <div class="mt-4 text-center">
                                    <a href="{{ route('allproducts') }}"
                                        class="text-xs sm:text-sm font-semibold text-gray-500 hover:text-red-600 transition-colors flex items-center justify-center gap-1">
                                        <i class="fas fa-arrow-left"></i> ซื้อสินค้าเพิ่ม
                                    </a>
                                </div>
                            </div>

                            {{-- Trust Badges --}}
                            <div class="flex justify-center gap-3 sm:gap-4 text-gray-400">
                                <div class="flex flex-col items-center gap-1">
                                    <i class="fas fa-shield-alt text-lg sm:text-xl"></i>
                                    <span class="text-[9px] sm:text-[10px] font-medium">ปลอดภัย 100%</span>
                                </div>
                                <div class="flex flex-col items-center gap-1">
                                    <i class="fas fa-truck text-lg sm:text-xl"></i>
                                    <span class="text-[9px] sm:text-[10px] font-medium">จัดส่งรวดเร็ว</span>
                                </div>
                                <div class="flex flex-col items-center gap-1">
                                    <i class="fas fa-undo text-lg sm:text-xl"></i>
                                    <span class="text-[9px] sm:text-[10px] font-medium">คืนสินค้าได้</span>
                                </div>
                            </div>

                        </div>
                    </div>
                @else
                    {{-- Empty State --}}
                    <div
                        class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 p-8 sm:p-12 text-center max-w-2xl mx-auto mt-6 sm:mt-10 mx-4">
                        <div
                            class="w-24 h-24 sm:w-32 sm:h-32 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-5 sm:mb-6 shadow-inner">
                            <i class="fas fa-shopping-cart text-4xl sm:text-5xl text-gray-300"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">ตะกร้าสินค้าของคุณว่างเปล่า</h2>
                        <p class="text-sm sm:text-base text-gray-500 mb-6 sm:mb-8">ดูเหมือนว่าคุณยังไม่ได้เพิ่มสินค้าใดๆ
                            ลงในตะกร้าเลย<br class="hidden sm:block">ลองดูสินค้าที่น่าสนใจของเราสิครับ</p>
                        <a href="{{ route('allproducts') }}"
                            class="inline-flex items-center justify-center h-12 sm:h-14 px-6 sm:px-8 bg-red-600 hover:bg-red-700 text-white rounded-xl sm:rounded-2xl font-bold text-base sm:text-lg shadow-lg shadow-red-600/30 transition-all transform hover:-translate-y-1">
                            ไปช้อปปิ้งกันเลย <i class="fas fa-shopping-bag ml-2"></i>
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Script --}}
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
            return x.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // ตัวแปรเก็บสถานะการโหลดเพื่อป้องกันการส่งซ้ำซ้อน
        let isUpdatingTotals = false;

        function onItemSelectionChange() {
            if (isUpdatingTotals) return;

            const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
            
            // แสดง Loading UI เล็กน้อยที่ตัวเลขรวม
            const totalDisplay = document.getElementById('total-display');
            if (totalDisplay) totalDisplay.classList.add('opacity-50', 'animate-pulse');

            isUpdatingTotals = true;

            // สร้าง URL สำหรับดึง Totals
            const url = new URL("{{ route('cart.totals') }}");
            if (selectedIds.length > 0) {
                url.searchParams.set('selected_items', selectedIds.join(','));
            }

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // 1. อัปเดตตัวเลขรวมในหน้าเว็บ
                    document.getElementById('subtotal-display').innerText = numberWithCommas(data.subTotal);
                    document.getElementById('discount-display').innerText = numberWithCommas(data.totalDiscount);
                    document.getElementById('total-display').innerText = numberWithCommas(data.total);
                    document.getElementById('selected-count').innerText = data.selectedCount;

                    // 2. อัปเดต URL ใน Browser (เพื่อให้ Refresh แล้วคงสถานะเดิม)
                    const currentUrl = new URL(window.location.href);
                    if (selectedIds.length > 0) {
                        currentUrl.searchParams.set('selected_items', selectedIds.join(','));
                    } else {
                        currentUrl.searchParams.delete('selected_items');
                    }
                    window.history.replaceState({}, '', currentUrl);

                    // 3. ปรับสถานะปุ่ม Checkout
                    const checkoutBtn = document.getElementById('checkout-btn');
                    if (checkoutBtn) {
                        checkoutBtn.disabled = (data.selectedCount === 0);
                    }

                    // 4. Update โควตาและรายการของแถมแบบ Real-time
                    const giftSelectionArea = document.getElementById('gift-selection-area');
                    const giftLimitDisplay = document.getElementById('gift-limit-display');
                    const giftPool = document.getElementById('gift-pool');

                    if (data.freebieLimit > 0 && data.giftableProducts && data.giftableProducts.length > 0) {
                        // แสดงกล่องเลือกของแถม
                        if (giftSelectionArea) {
                            giftSelectionArea.style.display = 'block';
                            giftLimitDisplay.innerText = data.freebieLimit;
                        }

                        // วาดรายการของแถมใหม่ (Gift Pool)
                        if (giftPool) {
                            giftPool.innerHTML = '';
                            data.giftableProducts.forEach(gift => {
                                const giftHtml = `
                                    <div class="relative group flex flex-col items-center p-1.5 sm:p-2 rounded-lg sm:rounded-xl border-2 transition-all duration-300 cursor-pointer bg-white border-transparent shadow-sm hover:shadow-md hover:border-pink-300 hover:-translate-y-1"
                                        id="gift-card-${gift.id}"
                                        onclick="addCartGift('${gift.id}')">

                                        <div class="aspect-square w-full rounded-md sm:rounded-lg overflow-hidden bg-gray-50 mb-1.5 sm:mb-2 relative">
                                            <img src="${gift.image}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                            <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors"></div>
                                        </div>
                                        <p class="text-[9px] sm:text-[10px] font-bold text-gray-700 line-clamp-2 px-1 text-center w-full leading-tight">
                                            ${gift.name}
                                        </p>

                                        <button type="button"
                                            class="absolute -top-1.5 -left-1.5 sm:-top-2 sm:-left-2 w-5 h-5 sm:w-6 sm:h-6 bg-white border-2 border-gray-200 text-gray-600 rounded-full flex items-center justify-center shadow-md hover:bg-gray-100 hover:border-gray-400 z-10 hidden transition-all"
                                            id="gift-remove-${gift.id}"
                                            onclick="event.stopPropagation(); removeCartGift('${gift.id}')">
                                            <i class="fas fa-minus text-[8px] sm:text-[10px]"></i>
                                        </button>
                                        <div class="absolute top-1.5 right-1.5 sm:top-2 sm:right-2 bg-pink-600 text-white w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center shadow-md hidden border-2 border-white"
                                            id="gift-badge-${gift.id}">
                                            <span class="text-[10px] sm:text-xs font-black" id="gift-count-${gift.id}">0</span>
                                        </div>
                                    </div>
                                `;
                                giftPool.innerHTML += giftHtml;
                            });
                        }

                        // ถ้า Limit เปลี่ยน (เช่น ลดลงจนเกินที่เลือกไว้) ให้ล้างของแถมที่เกิน
                        if (data.freebieLimit < selectedFreebiesArray.length) {
                            selectedFreebiesArray = selectedFreebiesArray.slice(0, data.freebieLimit);
                        }
                        updateCartGiftUI();
                    } else {
                        // ถ้าไม่มีสิทธิ์แถม ให้ซ่อนกล่องเลือกของแถม
                        if (giftSelectionArea) giftSelectionArea.style.display = 'none';
                        selectedFreebiesArray = [];
                        updateCartGiftUI();
                    }
                }
            })
            .catch(err => console.error('Failed to update totals:', err))
            .finally(() => {
                isUpdatingTotals = false;
                if (totalDisplay) totalDisplay.classList.remove('opacity-50', 'animate-pulse');
                calculateTotal(); // เรียกเพื่อ Update สถานะปุ่มลบทิ้งและ Select All
            });
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
                el.classList.remove('border-pink-500', 'bg-pink-50', 'ring-2', 'ring-pink-300');
                el.classList.add('border-transparent', 'bg-white');
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
                    card.classList.remove('border-transparent', 'bg-white');
                    card.classList.add('border-pink-500', 'bg-pink-50', 'ring-2', 'ring-pink-300');
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

        function removeSelectedItems() {
            const selectedIds = Array.from(document.querySelectorAll('.item-checkbox:checked'))
                .map(cb => cb.value);
            
            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'คำเตือน',
                    text: 'กรุณาเลือกสินค้าที่ต้องการลบ',
                    confirmButtonColor: '#EF4444',
                });
                return;
            }

            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: `คุณต้องการลบสินค้าที่เลือกจำนวน ${selectedIds.length} รายการใช่หรือไม่?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'ใช่, ลบเลย',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('cart.removeBulk') }}";
                    form.style.display = 'none';

                    const inputCsrf = document.createElement('input');
                    inputCsrf.type = 'hidden';
                    inputCsrf.name = '_token';
                    inputCsrf.value = csrfToken;
                    form.appendChild(inputCsrf);

                    const inputMethod = document.createElement('input');
                    inputMethod.type = 'hidden';
                    inputMethod.name = '_method';
                    inputMethod.value = 'DELETE';
                    form.appendChild(inputMethod);

                    selectedIds.forEach(id => {
                        const inputId = document.createElement('input');
                        inputId.type = 'hidden';
                        inputId.name = 'ids[]';
                        inputId.value = id;
                        form.appendChild(inputId);
                    });

                    document.body.appendChild(form);
                    form.submit();
                }
            });
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

            const removeSelectedBtn = document.getElementById('remove-selected-btn');
            if (removeSelectedBtn) {
                if (count > 0) {
                    removeSelectedBtn.classList.remove('hidden');
                    removeSelectedBtn.classList.add('flex');
                } else {
                    removeSelectedBtn.classList.add('hidden');
                    removeSelectedBtn.classList.remove('flex');
                }
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
                btn.addEventListener('click', async function(e) {
                    e.preventDefault();
                    
                    const url = this.dataset.url;
                    const method = this.dataset.method;
                    const btnElement = this;
                    
                    // ป้องกันการกดซ้ำซ้อน
                    if (btnElement.disabled) return;
                    btnElement.disabled = true;

                    try {
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            if (method === 'PATCH') {
                                // 1. กรณีอัปเดตจำนวนสินค้า (+/-)
                                const quantitySpan = btnElement.parentElement.querySelector('span');
                                let currentQty = parseInt(quantitySpan.innerText);
                                if (url.includes('increase')) {
                                    quantitySpan.innerText = currentQty + 1;
                                } else if (currentQty > 1) {
                                    quantitySpan.innerText = currentQty - 1;
                                }
                                // เรียกฟังก์ชันคำนวณราคาใหม่
                                onItemSelectionChange();
                            } else if (method === 'DELETE') {
                                // 2. กรณีลบสินค้า
                                const cartRow = btnElement.closest('.bg-white');
                                cartRow.classList.add('opacity-0', 'scale-95');
                                setTimeout(() => {
                                    cartRow.remove();
                                    // ถ้าสินค้าในตะกร้าหมด ให้โหลดหน้าเพื่อแสดง Empty State
                                    if (document.querySelectorAll('.bg-white.p-3.sm\\:p-5').length === 0) {
                                        window.location.reload();
                                    } else {
                                        onItemSelectionChange();
                                    }
                                }, 300);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'ไม่สำเร็จ',
                                text: data.message || 'เกิดข้อผิดพลาดในการทำรายการ',
                                confirmButtonColor: '#dc2626',
                            });
                        }
                    } catch (err) {
                        console.error('Cart Action Error:', err);
                    } finally {
                        btnElement.disabled = false;
                    }
                });
            });

            // 🛠️ แก้ไขเพิ่ม: แสดง Popup แจ้งเตือนเมื่อทำรายการสำเร็จ หรือเกิดข้อผิดพลาด จาก Session
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#10B981',
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#EF4444',
                });
            @endif
        });
    </script>
@endsection