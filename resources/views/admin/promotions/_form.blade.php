@php
    $buyData = old('buy_items', $buy_items ?? [['product_id' => [], 'quantity' => 1]]);
    // Ensure array structure implies multi-select for products
    foreach ($buyData as &$item) {
        if (!is_array($item['product_id'])) {
            $item['product_id'] = [$item['product_id']];
        }
    }

    $getData = old('get_items', $get_items ?? [['product_id' => '', 'quantity' => 1]]);
@endphp

<style>
    /* Custom TomSelect Dark Theme - Refined */
    .ts-control {
        background-color: #111827 !important;
        /* gray-900 */
        border: 1px solid #374151 !important;
        /* gray-700 */
        color: #f3f4f6 !important;
        /* gray-100 */
        border-radius: 0.5rem;
        padding: 0.75rem 0.75rem !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        transition: all 0.2s;
    }

    .ts-control.focus {
        border-color: #10b981 !important;
        /* emerald-500 */
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2) !important;
    }

    .ts-dropdown {
        background-color: #1f2937 !important;
        /* gray-800 */
        border: 1px solid #374151 !important;
        color: #e5e7eb !important;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
        z-index: 50;
        margin-top: 4px;
    }

    .ts-dropdown .option {
        padding: 8px 12px;
    }

    .ts-dropdown .option:hover,
    .ts-dropdown .active {
        background-color: #374151 !important;
        color: #34d399 !important;
    }

    .ts-wrapper.multi .ts-control>div.item {
        background: rgba(16, 185, 129, 0.15) !important;
        border: 1px solid rgba(16, 185, 129, 0.3) !important;
        color: #6ee7b7 !important;
        border-radius: 0.25rem;
        padding: 2px 8px !important;
        font-size: 0.875rem;
    }

    .ts-wrapper.plugin-remove_button .item .remove {
        border-left: 1px solid rgba(16, 185, 129, 0.3) !important;
        color: #6ee7b7 !important;
    }

    .ts-wrapper.plugin-remove_button .item .remove:hover {
        background: rgba(16, 185, 129, 0.2) !important;
    }
</style>

<div x-data="promotionForm(
    {{ old('is_discount_code', isset($promotion) && $promotion->code ? 'true' : 'false') }},
    '{{ old('discount_type', $promotion->discount_type ?? '') }}'
)" class="space-y-8 animate-fade-in-up">

    {{-- 1. Campaign Category & Type Selector --}}
    <div class="space-y-6">
        {{-- Section A: คูปอง (อัตโนมัติ - ไม่ต้องกรอกโค้ด) --}}
        <div class="space-y-3">
            <div class="flex items-center gap-2 ml-1">
                <span class="flex h-2 w-2 rounded-full bg-emerald-500"></span>
                <label class="text-sm font-bold text-gray-100 uppercase tracking-wider">หมวดหมู่: คูปอง (ระบบใช้ให้อัตโนมัติ)</label>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Auto Discount --}}
                <label class="cursor-pointer group relative">
                    <input type="radio" name="promo_type_selector" value="auto" x-model="promoType" class="peer sr-only" />
                    <div class="h-full p-4 rounded-2xl border border-gray-700 bg-gray-800 hover:border-emerald-500/50 transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-900/10 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-700/50 flex items-center justify-center text-gray-400 peer-checked:bg-emerald-500 peer-checked:text-white transition-all">
                            <i class="fas fa-bolt text-xl"></i>
                        </div>
                        <div class="text-left">
                            <h4 class="font-bold text-white text-sm">ส่วนลดอัตโนมัติ</h4>
                            <p class="text-[10px] text-gray-500 leading-tight mt-0.5">ลดราคาให้ทันทีเมื่อเข้าเงื่อนไข</p>
                        </div>
                    </div>
                </label>

                {{-- Auto Free Shipping --}}
                <label class="cursor-pointer group relative">
                    <input type="radio" name="promo_type_selector" value="free_shipping" x-model="promoType" class="peer sr-only" />
                    <div class="h-full p-4 rounded-2xl border border-gray-700 bg-gray-800 hover:border-orange-500/50 transition-all peer-checked:border-orange-500 peer-checked:bg-orange-900/10 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-700/50 flex items-center justify-center text-gray-400 peer-checked:bg-orange-500 peer-checked:text-white transition-all">
                            <i class="fas fa-truck text-xl"></i>
                        </div>
                        <div class="text-left">
                            <h4 class="font-bold text-white text-sm">ส่งฟรีอัตโนมัติ</h4>
                            <p class="text-[10px] text-gray-500 leading-tight mt-0.5">ยกเว้นค่าส่งให้ทันทีเมื่อเข้าเงื่อนไข</p>
                        </div>
                    </div>
                </label>

                {{-- Buy X Get Y --}}
                <label class="cursor-pointer group relative">
                    <input type="radio" name="promo_type_selector" value="bxgy" x-model="promoType" class="peer sr-only" />
                    <div class="h-full p-4 rounded-2xl border border-gray-700 bg-gray-800 hover:border-pink-500/50 transition-all peer-checked:border-pink-500 peer-checked:bg-pink-900/10 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-700/50 flex items-center justify-center text-gray-400 peer-checked:bg-pink-500 peer-checked:text-white transition-all">
                            <i class="fas fa-gifts text-xl"></i>
                        </div>
                        <div class="text-left">
                            <h4 class="font-bold text-white text-sm">ซื้อ X แถม Y</h4>
                            <p class="text-[10px] text-gray-500 leading-tight mt-0.5">แถมสินค้าฟรีเมื่อซื้อครบตามกำหนด</p>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        {{-- Section B: รหัสโค้ด (ต้องกรอกรหัส - Manual Code) --}}
        <div class="space-y-3">
            <div class="flex items-center gap-2 ml-1">
                <span class="flex h-2 w-2 rounded-full bg-blue-500"></span>
                <label class="text-sm font-bold text-gray-100 uppercase tracking-wider">หมวดหมู่: รหัสโค้ด (ลูกค้าต้องกรอกเอง)</label>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Code Discount --}}
                <label class="cursor-pointer group relative">
                    <input type="radio" name="promo_type_selector" value="code" x-model="promoType" class="peer sr-only" />
                    <div class="h-full p-4 rounded-2xl border border-gray-700 bg-gray-800 hover:border-blue-500/50 transition-all peer-checked:border-blue-500 peer-checked:bg-blue-900/10 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-700/50 flex items-center justify-center text-gray-400 peer-checked:bg-blue-500 peer-checked:text-white transition-all">
                            <i class="fas fa-ticket-alt text-xl"></i>
                        </div>
                        <div class="text-left">
                            <h4 class="font-bold text-white text-sm">รหัสส่วนลด</h4>
                            <p class="text-[10px] text-gray-500 leading-tight mt-0.5">ใช้ส่วนลดด้วยการกรอก Code</p>
                        </div>
                    </div>
                </label>

                {{-- Code Free Shipping --}}
                <label class="cursor-pointer group relative">
                    <input type="radio" name="promo_type_selector" value="free_shipping_code" x-model="promoType" class="peer sr-only" />
                    <div class="h-full p-4 rounded-2xl border border-gray-700 bg-gray-800 hover:border-purple-500/50 transition-all peer-checked:border-purple-500 peer-checked:bg-purple-900/10 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-700/50 flex items-center justify-center text-gray-400 peer-checked:bg-purple-500 peer-checked:text-white transition-all">
                            <i class="fas fa-shipping-fast text-xl"></i>
                        </div>
                        <div class="text-left">
                            <h4 class="font-bold text-white text-sm">รหัสส่งฟรี</h4>
                            <p class="text-[10px] text-gray-500 leading-tight mt-0.5">ใช้สิทธิ์ส่งฟรีด้วยการกรอก Code</p>
                        </div>
                    </div>
                </label>
            </div>
        </div>
    </div>
    <input type="hidden" name="is_discount_code" :value="isDiscountCode ? 1 : 0">

    {{-- Error Alert --}}
    @if ($errors->any())
        <div class="bg-red-500/10 border border-red-500/50 rounded-xl p-4 flex items-start gap-4 animate-shake">
            <div class="p-2 bg-red-500/20 rounded-lg text-red-500">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <h3 class="font-bold text-red-400">พบข้อผิดพลาด กรุณาตรวจสอบ</h3>
                <ul class="text-sm text-red-300 mt-1 list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- MAIN FORM GRID --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 items-start">

        {{-- LEFT COLUMN: RULES & CONFIGURATION --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- A. Discount Configuration (Auto/Code/Free Shipping) --}}
            <div x-show="promoType === 'auto' || promoType === 'code' || promoType === 'free_shipping' || promoType === 'free_shipping_code'"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="bg-gray-800 rounded-2xl border border-gray-700 shadow-xl overflow-hidden relative">

                {{-- Decorative bg --}}
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-500/5 to-transparent rounded-bl-full -z-0">
                </div>

                <div
                    class="p-5 border-b border-gray-700 bg-gray-800/50 backdrop-blur-sm flex items-center gap-3 relative z-10">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-lg">ตั้งค่ามูลค่าส่วนลด / เงื่อนไข</h3>
                        <p class="text-xs text-gray-400">กำหนดรหัสและมูลค่าที่จะลดราคาหรือส่งฟรี</p>
                    </div>
                </div>

                <div class="p-6 space-y-8 relative z-10">
                    {{-- Coupon Code Field --}}
                    <div x-show="promoType === 'code' || promoType === 'free_shipping_code'"
                        class="bg-blue-900/20 border border-blue-500/30 rounded-xl p-6 relative group transition-all hover:border-blue-500/50"
                        :class="promoType === 'free_shipping_code' ? 'bg-purple-900/20 border-purple-500/30 hover:border-purple-500/50' : ''">
                        <label class="block text-sm font-bold text-blue-300 mb-2 uppercase tracking-wide"
                            :class="promoType === 'free_shipping_code' ? 'text-purple-300' : ''">รหัสส่วนลด
                            (Coupon Code) <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <input type="text" name="code" placeholder="เช่น SALE2024"
                                class="block w-full bg-gray-900 border-gray-600 rounded-lg py-4 px-5 text-white placeholder-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 uppercase tracking-widest font-mono text-xl shadow-inner transition-all"
                                :class="promoType === 'free_shipping_code' ? 'focus:ring-purple-500 focus:border-purple-500' : ''"
                                value="{{ old('code', $promotion->code ?? '') }}"
                                :required="promoType === 'code' || promoType === 'free_shipping_code'" />
                            <div
                                class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none"
                                :class="promoType === 'free_shipping_code' ? 'text-purple-500' : 'text-blue-500'">
                                <i class="fas fa-ticket-alt text-xl"></i>
                            </div>
                        </div>
                        <p class="text-xs mt-2" :class="promoType === 'free_shipping_code' ? 'text-purple-400/70' : 'text-blue-400/70'">
                            <i class="fas fa-info-circle mr-1"></i>
                            เฉพาะภาษาอังกฤษตัวพิมพ์ใหญ่และตัวเลข</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-show="promoType === 'auto' || promoType === 'code'">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">รูปแบบส่วนลด</label>
                            <div class="relative">
                                <select name="discount_type" x-model="discountType"
                                    class="block w-full bg-gray-900 border-gray-600 rounded-lg py-3 px-4 text-white appearance-none focus:ring-emerald-500 focus:border-emerald-500 cursor-pointer">
                                    <option value="">-- เลือกรูปแบบ --</option>
                                    @foreach ($discountTypes as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ old('discount_type', $promotion->discount_type ?? '') == $key ? 'selected' : '' }}>
                                            {{ $key === 'fixed' ? 'ลดเป็นจำนวนเงิน (Fixed Amount)' : 'ลดเป็นเปอร์เซ็นต์ (Percentage %)' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">มูลค่าที่ลด <span
                                    class="text-red-400">*</span></label>
                            <div class="relative">
                                <input type="number" name="discount_value" step="0.01" min="0"
                                    placeholder="0"
                                    class="block w-full bg-gray-900 border-gray-600 rounded-lg py-3 px-4 text-white focus:ring-emerald-500 focus:border-emerald-500 font-bold text-xl text-right pr-12 shadow-inner"
                                    value="{{ old('discount_value', $promotion->discount_value ?? '') }}"
                                    :required="promoType === 'auto' || promoType === 'code'" />
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <span class="text-gray-400 font-bold text-lg"
                                        x-text="discountType === 'percentage' ? '%' : '฿'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="promoType === 'free_shipping' || promoType === 'free_shipping_code'" 
                        class="border rounded-xl p-4 text-sm flex items-center gap-3"
                        :class="promoType === 'free_shipping' ? 'bg-orange-500/10 border-orange-500/20 text-orange-300' : 'bg-purple-500/10 border-purple-500/20 text-purple-300'">
                        <i class="fas fa-truck text-lg"></i>
                        <span x-text="promoType === 'free_shipping' ? 'แคมเปญส่งฟรีอัตโนมัติจะยกเว้นค่าจัดส่งทั้งหมดให้กับทุกคำสั่งซื้อที่เข้าเงื่อนไข' : 'แคมเปญส่งฟรีจะยกเว้นค่าจัดส่งทั้งหมดให้กับคำสั่งซื้อที่ใช้รหัสนี้'"></span>
                    </div>
                </div>
            </div>

            {{-- B. Buy X Get Y Builder --}}
            <div x-show="isBxGy" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                class="bg-gray-800 rounded-2xl border border-gray-700 shadow-xl overflow-hidden relative">

                {{-- Decorative bg --}}
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-pink-500/5 to-transparent rounded-bl-full -z-0">
                </div>

                <div
                    class="p-5 border-b border-gray-700 bg-gray-800/50 backdrop-blur-sm flex justify-between items-center relative z-10">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center text-white shadow-lg">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-white text-lg">เงื่อนไขของแถม</h3>
                            <p class="text-xs text-gray-400">Buy X Get Y Configuration</p>
                        </div>
                    </div>

                    {{-- AND/OR Switch --}}
                    <div class="bg-gray-900 p-1 rounded-lg border border-gray-600 inline-flex shadow-inner">
                        <label
                            class="cursor-pointer px-4 py-1.5 rounded-md text-xs font-bold transition-all duration-200"
                            :class="conditionType === 'any' ? 'bg-gray-700 text-white shadow ring-1 ring-gray-500' :
                                'text-gray-500 hover:text-gray-300'">
                            <input type="radio" name="condition_type" value="any" x-model="conditionType"
                                class="hidden">
                            อย่างใดอย่างหนึ่ง (OR)
                        </label>
                        <label
                            class="cursor-pointer px-4 py-1.5 rounded-md text-xs font-bold transition-all duration-200"
                            :class="conditionType === 'all' ? 'bg-pink-600 text-white shadow ring-1 ring-pink-400' :
                                'text-gray-500 hover:text-gray-300'">
                            <input type="radio" name="condition_type" value="all" x-model="conditionType"
                                class="hidden">
                            ครบทุกข้อ (AND)
                        </label>
                    </div>
                </div>

                <div class="p-6 relative z-10">
                    <div class="flex flex-col gap-6">

                        {{-- 1. Buy Condition --}}
                        <div
                            class="relative border-2 border-dashed border-gray-600 rounded-xl p-6 bg-gray-900/30 hover:border-gray-500 transition-colors">
                            <div
                                class="absolute -top-3 left-4 px-3 bg-gray-800 text-xs font-bold text-emerald-400 border border-gray-600 rounded-full uppercase tracking-wider shadow-sm">
                                <i class="fas fa-shopping-cart mr-1"></i> เมื่อลูกค้าซื้อ (Buy)
                            </div>

                            <div class="space-y-4 pt-2">
                                {{--
                                    FIX: ใช้ x-data wrapper ใน template แต่ละ row เพื่อให้ TomSelect
                                    init/destroy ได้อย่างถูกต้องตาม lifecycle ของ Alpine
                                --}}
                                <template x-for="(item, index) in buys" :key="'buy-row-' + index">
                                    <div
                                        x-data="buyRow(item, index)"
                                        x-init="initSelect()"
                                        x-destroy="destroySelect()"
                                        class="flex flex-col md:flex-row gap-3 items-start md:items-center bg-gray-800 p-4 rounded-xl border border-gray-700 relative group transition-all hover:border-gray-500">
                                        {{-- Remove Button --}}
                                        <button type="button" x-show="buys.length > 1"
                                            @click="$dispatch('remove-buy-item', { index: index })"
                                            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 rounded-full text-white text-[10px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all hover:bg-red-600 hover:scale-110 shadow-lg z-20">
                                            <i class="fas fa-times"></i>
                                        </button>

                                        <div class="flex-grow w-full">
                                            <label
                                                class="text-[10px] uppercase text-gray-500 font-bold mb-1.5 block tracking-wider">เลือกสินค้าที่ต้องซื้อ</label>
                                            {{--
                                                FIX: ใช้ x-ref แทน dynamic id เพื่อหลีกเลี่ยงปัญหา
                                                ID ซ้ำหลังจาก splice
                                            --}}
                                            <select x-ref="buySelect"
                                                :name="`buy_items[${index}][product_id][]`" multiple
                                                class="buy-products-select" x-bind:disabled="isDiscountCode">
                                                @foreach ($products as $p)
                                                    <option value="{{ $p->pd_sp_id }}">
                                                        {{ $p->pd_sp_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="w-full md:w-32 flex-shrink-0">
                                            <label
                                                class="text-[10px] uppercase text-gray-500 font-bold mb-1.5 block tracking-wider">จำนวน
                                                (ชิ้น)</label>
                                            <div
                                                class="flex items-center bg-gray-900 rounded-lg border border-gray-600 overflow-hidden">
                                                <button type="button"
                                                    class="w-8 py-2 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
                                                    @click="item.quantity = Math.max(1, parseInt(item.quantity)-1)">-</button>
                                                <input type="number" :name="`buy_items[${index}][quantity]`"
                                                    x-model="item.quantity" min="1"
                                                    class="w-full bg-transparent text-center border-none p-0 text-white font-bold focus:ring-0 appearance-none">
                                                <button type="button"
                                                    class="w-8 py-2 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
                                                    @click="item.quantity = parseInt(item.quantity)+1">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <button type="button" @click="addItem('buy')"
                                    class="w-full py-3 border border-dashed border-gray-600 rounded-xl text-sm text-gray-400 hover:text-emerald-400 hover:border-emerald-500 hover:bg-emerald-500/5 transition-all flex items-center justify-center gap-2 group">
                                    <div
                                        class="w-5 h-5 rounded-full border border-gray-500 group-hover:border-emerald-400 flex items-center justify-center">
                                        <i class="fas fa-plus text-[10px]"></i>
                                    </div>
                                    เพิ่มเงื่อนไขสินค้า
                                </button>
                            </div>
                        </div>

                        {{-- Connector Arrow --}}
                        <div class="flex justify-center -my-4 z-10">
                            <div
                                class="bg-gray-700 text-gray-300 rounded-full w-10 h-10 flex items-center justify-center border-4 border-gray-800 shadow-xl">
                                <i class="fas fa-arrow-down text-sm"></i>
                            </div>
                        </div>

                        {{-- 2. Get Result --}}
                        <div
                            class="relative border-2 border-dashed border-pink-500/30 rounded-xl p-6 bg-pink-900/5 hover:border-pink-500/50 transition-colors">
                            <div
                                class="absolute -top-3 left-4 px-3 bg-gray-800 text-xs font-bold text-pink-400 border border-pink-500/30 rounded-full uppercase tracking-wider shadow-sm">
                                <i class="fas fa-gift mr-1"></i> ลูกค้าจะได้รับ (Get)
                            </div>

                            <div class="space-y-4 pt-2">
                                <div class="bg-gray-800 p-4 rounded-xl border border-gray-700 shadow-sm">
                                    <label class="text-xs uppercase text-gray-400 font-bold mb-2 block tracking-wider">
                                        <i class="fas fa-box-open mr-1"></i> รายการของแถมที่เลือกได้ (Gift Pool)
                                    </label>
                                    <select id="giftable-products-select" name="giftable_product_ids[]" multiple
                                        x-bind:disabled="isDiscountCode">
                                        @php
                                            $selectedGiftIds = collect(
                                                old(
                                                    'giftable_product_ids',
                                                    isset($promotion)
                                                        ? $promotion->actions->flatMap->giftableProducts->pluck(
                                                            'pd_sp_id',
                                                        )
                                                        : [],
                                                ),
                                            )->map(fn($id) => (string) $id);
                                        @endphp
                                        @foreach ($products as $p)
                                            <option value="{{ $p->pd_sp_id }}"
                                                {{ $selectedGiftIds->contains((string) $p->pd_sp_id) ? 'selected' : '' }}>
                                                {{ $p->pd_sp_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p
                                        class="text-[10px] text-gray-500 mt-2 bg-gray-900/50 p-2 rounded border border-gray-700/50">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        ลูกค้าจะสามารถเลือกรับของแถมจากรายการเหล่านี้ได้ในหน้าตะกร้าสินค้า
                                    </p>
                                </div>

                                <template x-for="(item, index) in gets" :key="index">
                                    <div
                                        class="flex items-center justify-between bg-gray-800 p-4 rounded-xl border border-gray-700 shadow-sm">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded bg-pink-500/10 flex items-center justify-center text-pink-400">
                                                <i class="fas fa-hand-holding-heart"></i>
                                            </div>
                                            <span class="text-sm text-gray-300 font-medium">จำนวนชิ้นที่แถมฟรี</span>
                                        </div>
                                        <div
                                            class="flex items-center bg-gray-900 rounded-lg border border-gray-600 overflow-hidden">
                                            <input type="hidden" :name="`get_items[${index}][product_id]`"
                                                :value="item.product_id">
                                            <button type="button"
                                                class="w-8 py-2 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
                                                @click="item.quantity = Math.max(1, parseInt(item.quantity)-1)">-</button>
                                            <input type="number" :name="`get_items[${index}][quantity]`"
                                                x-model="item.quantity" min="1"
                                                class="w-16 bg-transparent text-center border-none p-0 text-pink-400 font-bold text-lg focus:ring-0 appearance-none">
                                            <button type="button"
                                                class="w-8 py-2 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
                                                @click="item.quantity = parseInt(item.quantity)+1">+</button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: BASIC INFO --}}
        <div class="xl:col-span-1 space-y-6">
            <div class="bg-gray-800 rounded-2xl border border-gray-700 shadow-xl p-6 sticky top-6">
                <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2 pb-4 border-b border-gray-700">
                    <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span> ข้อมูลทั่วไป
                </h3>

                {{-- Name --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-2">ชื่อแคมเปญ <span
                            class="text-red-400">*</span></label>
                    <input type="text" name="name"
                        class="block w-full bg-gray-900 border-gray-600 rounded-lg py-3 px-4 text-white focus:ring-emerald-500 focus:border-emerald-500 transition-all placeholder-gray-500"
                        value="{{ old('name', $promotion->name ?? '') }}" required
                        placeholder="ตั้งชื่อให้จำง่าย (Admin Only)" />
                </div>

                {{-- Active Status --}}
                <div class="mb-6">
                    <label
                        class="flex items-center justify-between p-4 bg-gray-900 rounded-xl border border-gray-600 cursor-pointer hover:border-emerald-500/50 transition-all group">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 group-hover:text-emerald-400 transition-colors">
                                <i class="fas fa-power-off"></i>
                            </div>
                            <div>
                                <span class="block text-sm font-bold text-white">สถานะการใช้งาน</span>
                                <span
                                    class="block text-xs text-gray-500 group-hover:text-emerald-400/70 transition-colors">เปิด/ปิด
                                    แคมเปญนี้</span>
                            </div>
                        </div>
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                                {{ old('is_active', $promotion->is_active ?? true) ? 'checked' : '' }}>
                            <div
                                class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 shadow-inner">
                            </div>
                        </div>
                    </label>
                </div>

                {{-- Description --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-2">รายละเอียด (Internal Note)</label>
                    <textarea name="description" rows="3"
                        class="block w-full bg-gray-900 border-gray-600 rounded-lg py-3 px-4 text-white focus:ring-emerald-500 focus:border-emerald-500 text-sm placeholder-gray-500"
                        placeholder="โน้ตสำหรับแอดมิน... (ลูกค้าไม่เห็น)">{{ old('description', $promotion->description ?? '') }}</textarea>
                </div>

                {{-- Dates --}}
                <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700 mb-5 space-y-4">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">ระยะเวลาแคมเปญ</h4>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">วันเริ่มต้น</label>
                        <div class="relative">
                            <input type="datetime-local" name="start_date"
                                class="block w-full bg-gray-900 border-gray-600 rounded-lg py-2 pl-3 pr-2 text-white text-sm focus:ring-emerald-500 focus:border-emerald-500"
                                value="{{ old('start_date', isset($promotion->start_date) ? \Carbon\Carbon::parse($promotion->start_date)->format('Y-m-d\TH:i') : '') }}" />

                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">วันสิ้นสุด</label>
                        <div class="relative">
                            <input type="datetime-local" name="end_date"
                                class="block w-full bg-gray-900 border-gray-600 rounded-lg py-2 pl-3 pr-2 text-white text-sm focus:ring-emerald-500 focus:border-emerald-500"
                                value="{{ old('end_date', isset($promotion->end_date) ? \Carbon\Carbon::parse($promotion->end_date)->format('Y-m-d\TH:i') : '') }}" />
                        </div>
                    </div>
                </div>

                {{-- Limits --}}
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">จำกัดสิทธิ์
                            (ครั้ง)</label>
                        <input type="number" name="usage_limit" min="1" placeholder="ไม่จำกัด"
                            class="block w-full bg-gray-900 border-gray-600 rounded-lg py-2.5 px-3 text-white text-sm focus:ring-emerald-500 focus:border-emerald-500 text-center"
                            value="{{ old('usage_limit', $promotion->usage_limit ?? '') }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">ยอดขั้นต่ำ (บาท)</label>
                        <input type="number" name="min_order_value" min="0" step="0.01" placeholder="0"
                            class="block w-full bg-gray-900 border-gray-600 rounded-lg py-2.5 px-3 text-white text-sm focus:ring-emerald-500 focus:border-emerald-500 text-center"
                            value="{{ old('min_order_value', $promotion->min_order_value ?? '') }}" />
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col gap-3">
                    <button type="submit"
                        class="w-full py-3.5 px-4 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-bold rounded-xl shadow-lg shadow-emerald-900/50 transition-all transform hover:-translate-y-0.5 flex justify-center items-center gap-2">
                        <i class="fas fa-save"></i> บันทึกข้อมูล
                    </button>
                    <a href="{{ route('admin.promotions.index') }}"
                        class="w-full py-3 px-4 bg-transparent border border-gray-600 text-gray-400 hover:text-white hover:bg-gray-700 font-medium rounded-xl text-center transition-colors">
                        ยกเลิก / ย้อนกลับ
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Alpine Logic --}}
<script>
    document.addEventListener('alpine:init', () => {

        /**
         * FIX: แยก component ย่อย buyRow สำหรับแต่ละ row ใน template x-for
         *
         * วิธีนี้ใช้ประโยชน์จาก x-init และ x-destroy ของ Alpine เพื่อให้ TomSelect
         * ถูก init เมื่อ row ถูกสร้าง และถูก destroy เมื่อ row ถูกลบอย่างถูกต้อง
         * โดยไม่ต้องจัดการ lifecycle ด้วยตัวเองใน parent component
         */
        Alpine.data('buyRow', (item, index) => ({
            tomSelectInstance: null,

            initSelect() {
                if (typeof TomSelect === 'undefined') return;

                const el = this.$refs.buySelect;
                if (!el) return;

                // กำหนด selected values จาก item.product_id
                const selectedIds = Array.isArray(item.product_id)
                    ? item.product_id.map(String)
                    : (item.product_id ? [String(item.product_id)] : []);

                this.tomSelectInstance = new TomSelect(el, {
                    plugins: ['remove_button', 'clear_button'],
                    create: false,
                    items: selectedIds, // ← set selected items ตอน init
                    render: {
                        item: (data, escape) =>
                            `<div class="bg-gray-700 text-emerald-400 border border-gray-600 px-2 py-0.5 rounded mr-1 mb-1 text-xs font-medium">${escape(data.text)}</div>`,
                        option: (data, escape) =>
                            `<div class="py-2 px-1"><div class="font-medium text-gray-200">${escape(data.text)}</div></div>`
                    },
                    onDropdownOpen: function() {
                        this.dropdown.classList.add('opacity-100');
                    },
                    onDropdownClose: function() {
                        this.dropdown.classList.remove('opacity-100');
                    },
                    // sync ค่ากลับไปยัง item.product_id เมื่อมีการเปลี่ยนแปลง
                    onChange: (values) => {
                        item.product_id = values;
                    }
                });
            },

            destroySelect() {
                if (this.tomSelectInstance) {
                    this.tomSelectInstance.destroy();
                    this.tomSelectInstance = null;
                }
            }
        }));

        Alpine.data('promotionForm', (initialIsDiscountCode, initialDiscountType) => ({
            buys: @json($buyData),
            gets: @json($getData),
            discountType: initialDiscountType,
            conditionType: '{{ old('condition_type', $promotion->condition_type ?? 'any') }}',
            promoType: '{{ old('promo_type_selector', isset($promotion) ? ($promotion->is_free_shipping ? ($promotion->code ? 'free_shipping_code' : 'free_shipping') : ($promotion->code ? 'code' : ($promotion->rules->count() > 0 ? 'bxgy' : 'auto'))) : 'auto') }}',
            giftTomSelect: null,

            get isDiscountCode() {
                return this.promoType === 'code' || this.promoType === 'free_shipping_code';
            },
            get isBxGy() {
                return this.promoType === 'bxgy';
            },

            init() {
                this.$nextTick(() => {
                    this.initGiftSelect();
                });

                // Listen for remove event จาก buyRow เพื่อหลีกเลี่ยงปัญหา scope
                this.$el.addEventListener('remove-buy-item', (e) => {
                    this.removeItem('buy', e.detail.index);
                });
            },

            initGiftSelect() {
                if (typeof TomSelect === 'undefined') return;

                const giftEl = document.getElementById('giftable-products-select');
                if (giftEl && !this.giftTomSelect) {
                    this.giftTomSelect = new TomSelect(giftEl, {
                        plugins: ['remove_button', 'clear_button'],
                        create: false,
                        placeholder: 'เลือกสินค้าของแถม...',
                        valueField: 'value',
                        labelField: 'text',
                        searchField: 'text',
                        render: {
                            item: (data, escape) =>
                                `<div class="bg-pink-500/20 text-pink-300 border border-pink-500/30 px-2 py-0.5 rounded mr-1 mb-1 text-xs font-medium flex items-center gap-1"><i class="fas fa-gift text-[10px]"></i> ${escape(data.text)}</div>`,
                            option: (data, escape) =>
                                `<div class="py-2 px-1"><div class="font-medium text-gray-200">${escape(data.text)}</div></div>`
                        },
                        onDropdownOpen: function() {
                            this.dropdown.classList.add('opacity-100');
                        },
                        onDropdownClose: function() {
                            this.dropdown.classList.remove('opacity-100');
                        }
                    });
                }
            },

            addItem(type) {
                // FIX: ไม่ต้องการ $watch หรือ initAllSelects อีกต่อไป
                // buyRow component จะ init TomSelect ใหม่ผ่าน x-init อัตโนมัติ
                if (type === 'buy') {
                    this.buys.push({ product_id: [], quantity: 1 });
                }
            },

            removeItem(type, index) {
                // FIX: ไม่ต้อง destroy TomSelect ที่นี่อีกต่อไป
                // buyRow component จัดการผ่าน x-destroy อัตโนมัติ
                if (type === 'buy' && this.buys.length > 1) {
                    this.buys.splice(index, 1);
                }
            },
        }));
    });
</script>