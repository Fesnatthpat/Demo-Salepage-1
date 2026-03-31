@php
    $buyData = old('buy_items', $buy_items ?? [['product_id' => [], 'quantity' => 1]]);
    foreach ($buyData as &$item) {
        if (!is_array($item['product_id'])) {
            $item['product_id'] = [$item['product_id']];
        }
    }
    $getData = old('get_items', $get_items ?? [['product_id' => '', 'quantity' => 1]]);

    // Determine the initial promo type based on data
    $initialType = 'auto';
    if (isset($promotion)) {
        if ($promotion->is_free_shipping) {
            $initialType = $promotion->code ? 'free_shipping_code' : 'free_shipping';
        } elseif ($promotion->is_selectable) {
            $initialType = 'coupon_selectable';
        } elseif ($promotion->code) {
            $initialType = 'code';
        } elseif ($promotion->rules->count() > 0) {
            $initialType = 'bxgy';
        }
    }
    $initialType = old('promo_type_selector', $initialType);
@endphp

<style>
    /* Premium Dark Theme for TomSelect */
    .ts-control {
        background-color: #1f2937 !important;
        border: 1px solid #374151 !important;
        color: #f3f4f6 !important;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem !important;
        transition: all 0.2s;
    }

    .ts-control.focus {
        border-color: #10b981 !important;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15) !important;
        background-color: #111827 !important;
    }

    .ts-dropdown {
        background-color: #1f2937 !important;
        border: 1px solid #374151 !important;
        border-radius: 0.75rem;
        color: #e5e7eb !important;
        overflow: hidden;
        margin-top: 4px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
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
        border-radius: 0.5rem;
        padding: 2px 8px;
        font-weight: 600;
    }
</style>

<div x-data="promotionForm('{{ $initialType }}', '{{ old('discount_type', $promotion->discount_type ?? 'fixed') }}')" class="space-y-8 animate-fade-in-up">

    {{-- 1. Campaign Category --}}
    <div class="space-y-6">
        <div class="bg-gray-800/80 backdrop-blur-sm p-8 rounded-3xl border border-gray-700/80 shadow-xl">
            <div class="flex items-center gap-4 mb-8">
                <div
                    class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-400/20 to-teal-500/20 flex items-center justify-center border border-emerald-500/30 text-emerald-400 shadow-inner">
                    <i class="fas fa-layer-group text-xl"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-white text-xl">1. เลือกรูปแบบโปรโมชั่น</h3>
                    <p class="text-xs font-medium text-gray-400 mt-0.5">กำหนดลักษณะการให้ส่วนลดที่ต้องการ</p>
                </div>
            </div>

            <div class="space-y-8">
                {{-- Section A: ระบบจัดการให้ --}}
                <div class="space-y-4">
                    <label
                        class="text-[11px] font-black text-emerald-400 uppercase tracking-[0.2em] ml-2 flex items-center gap-2">
                        <span class="w-4 h-px bg-emerald-500/50"></span> ระบบจัดการอัตโนมัติ (ไม่ต้องกดรับ)
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <label class="cursor-pointer group relative">
                            <input type="radio" name="promo_type_selector" value="auto" x-model="promoType"
                                class="peer sr-only" />
                            <div
                                class="h-full p-5 rounded-2xl border-2 border-gray-700 bg-gray-900/50 hover:border-emerald-500/50 transition-all duration-300 peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10 peer-checked:shadow-[0_0_20px_rgba(16,185,129,0.15)] relative overflow-hidden">
                                <div
                                    class="absolute top-0 right-0 w-16 h-16 bg-emerald-500/10 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-150">
                                </div>
                                <div class="flex items-center gap-3 mb-3 relative z-10">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center text-gray-400 peer-checked:text-emerald-400 peer-checked:bg-emerald-500/20 transition-colors">
                                        <i class="fas fa-bolt"></i>
                                    </div>
                                    <h4 class="font-bold text-white text-sm">ลดราคาอัตโนมัติ</h4>
                                </div>
                                <p class="text-[11px] text-gray-400 leading-relaxed relative z-10">
                                    ลดเงินทันทีในตะกร้าเมื่อเข้าเงื่อนไข (ไม่ต้องกดใช้/ไม่ต้องกรอกโค้ด)</p>
                            </div>
                        </label>

                        <label class="cursor-pointer group relative">
                            <input type="radio" name="promo_type_selector" value="free_shipping" x-model="promoType"
                                class="peer sr-only" />
                            <div
                                class="h-full p-5 rounded-2xl border-2 border-gray-700 bg-gray-900/50 hover:border-orange-500/50 transition-all duration-300 peer-checked:border-orange-500 peer-checked:bg-orange-500/10 peer-checked:shadow-[0_0_20px_rgba(249,115,22,0.15)] relative overflow-hidden">
                                <div
                                    class="absolute top-0 right-0 w-16 h-16 bg-orange-500/10 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-150">
                                </div>
                                <div class="flex items-center gap-3 mb-3 relative z-10">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center text-gray-400 peer-checked:text-orange-400 peer-checked:bg-orange-500/20 transition-colors">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                    <h4 class="font-bold text-white text-sm">ส่งฟรีอัตโนมัติ</h4>
                                </div>
                                <p class="text-[11px] text-gray-400 leading-relaxed relative z-10">
                                    ยกเว้นค่าจัดส่งให้อัตโนมัติเมื่อยอดซื้อถึงเกณฑ์ที่กำหนด</p>
                            </div>
                        </label>

                        <label class="cursor-pointer group relative">
                            <input type="radio" name="promo_type_selector" value="bxgy" x-model="promoType"
                                class="peer sr-only" />
                            <div
                                class="h-full p-5 rounded-2xl border-2 border-gray-700 bg-gray-900/50 hover:border-pink-500/50 transition-all duration-300 peer-checked:border-pink-500 peer-checked:bg-pink-500/10 peer-checked:shadow-[0_0_20px_rgba(236,72,153,0.15)] relative overflow-hidden">
                                <div
                                    class="absolute top-0 right-0 w-16 h-16 bg-pink-500/10 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-150">
                                </div>
                                <div class="flex items-center gap-3 mb-3 relative z-10">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center text-gray-400 peer-checked:text-pink-400 peer-checked:bg-pink-500/20 transition-colors">
                                        <i class="fas fa-gifts"></i>
                                    </div>
                                    <h4 class="font-bold text-white text-sm">ซื้อ X แถม Y</h4>
                                </div>
                                <p class="text-[11px] text-gray-400 leading-relaxed relative z-10">
                                    แคมเปญแจกของแถมเมื่อซื้อสินค้าที่กำหนดครบตามจำนวน</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="w-full h-px bg-gray-700/50"></div>

                {{-- Section B: ลูกค้ามีส่วนร่วม --}}
                <div class="space-y-4">
                    <label
                        class="text-[11px] font-black text-blue-400 uppercase tracking-[0.2em] ml-2 flex items-center gap-2">
                        <span class="w-4 h-px bg-blue-500/50"></span> ลูกค้าต้องดำเนินการเอง
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <label class="cursor-pointer group relative">
                            <input type="radio" name="promo_type_selector" value="coupon_selectable"
                                x-model="promoType" class="peer sr-only" />
                            <div
                                class="h-full p-5 rounded-2xl border-2 border-gray-700 bg-gray-900/50 hover:border-teal-500/50 transition-all duration-300 peer-checked:border-teal-500 peer-checked:bg-teal-500/10 peer-checked:shadow-[0_0_20px_rgba(20,184,166,0.15)]">
                                <div class="flex items-center gap-3 mb-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center text-gray-400 peer-checked:text-teal-400 peer-checked:bg-teal-500/20">
                                        <i class="fas fa-hand-pointer"></i>
                                    </div>
                                    <h4 class="font-bold text-white text-sm">คูปอง (กดเลือกใช้)</h4>
                                </div>
                                <p class="text-[11px] text-gray-400 leading-relaxed">
                                    ลูกค้าสามารถกดคลิกใช้คูปองได้เลยจากหน้าชำระเงิน</p>
                            </div>
                        </label>

                        <label class="cursor-pointer group relative">
                            <input type="radio" name="promo_type_selector" value="code" x-model="promoType"
                                class="peer sr-only" />
                            <div
                                class="h-full p-5 rounded-2xl border-2 border-gray-700 bg-gray-900/50 hover:border-blue-500/50 transition-all duration-300 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 peer-checked:shadow-[0_0_20px_rgba(59,130,246,0.15)]">
                                <div class="flex items-center gap-3 mb-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center text-gray-400 peer-checked:text-blue-400 peer-checked:bg-blue-500/20">
                                        <i class="fas fa-keyboard"></i>
                                    </div>
                                    <h4 class="font-bold text-white text-sm">รหัสโค้ด (กรอกเอง)</h4>
                                </div>
                                <p class="text-[11px] text-gray-400 leading-relaxed">
                                    รหัสลับที่ลูกค้าต้องพิมพ์ให้ถูกต้องเพื่อรับส่วนลด</p>
                            </div>
                        </label>

                        <label class="cursor-pointer group relative">
                            <input type="radio" name="promo_type_selector" value="free_shipping_code"
                                x-model="promoType" class="peer sr-only" />
                            <div
                                class="h-full p-5 rounded-2xl border-2 border-gray-700 bg-gray-900/50 hover:border-purple-500/50 transition-all duration-300 peer-checked:border-purple-500 peer-checked:bg-purple-500/10 peer-checked:shadow-[0_0_20px_rgba(168,85,247,0.15)]">
                                <div class="flex items-center gap-3 mb-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center text-gray-400 peer-checked:text-purple-400 peer-checked:bg-purple-500/20">
                                        <i class="fas fa-shipping-fast"></i>
                                    </div>
                                    <h4 class="font-bold text-white text-sm">รหัสส่งฟรี (กรอกเอง)</h4>
                                </div>
                                <p class="text-[11px] text-gray-400 leading-relaxed">
                                    โค้ดสำหรับยกเว้นค่าจัดส่งที่ลูกค้าต้องพิมพ์รหัสด้วยตนเอง</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Main Config --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 items-start">
        <div class="xl:col-span-2 space-y-6">
            {{-- Discount Value Config --}}
            <div x-show="promoType !== 'bxgy'"
                class="bg-gray-800/90 backdrop-blur-sm rounded-3xl border border-gray-700/80 shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-700/50 bg-gray-800/50 flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-blue-500/20 flex items-center justify-center text-blue-400 shadow-inner border border-blue-500/20">
                        <i class="fas fa-calculator text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-white text-xl">2. ตั้งค่ามูลค่าส่วนลด</h3>
                        <p class="text-xs font-medium text-gray-400 mt-0.5">กำหนดสิ่งที่ลูกค้าจะได้รับจากแคมเปญนี้</p>
                    </div>
                </div>

                <div class="p-8 space-y-8">
                    {{-- ⌨️ Code Input --}}
                    <div x-show="promoType === 'code' || promoType === 'free_shipping_code'" x-transition
                        class="bg-blue-900/20 border border-blue-500/30 rounded-2xl p-6 relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 text-blue-500/10 text-6xl"><i
                                class="fas fa-ticket-alt"></i></div>
                        <label
                            class="block text-xs font-black text-blue-400 uppercase tracking-widest mb-3 relative z-10">รหัสโปรโมชั่น
                            (Coupon Code) <span class="text-red-400">*</span></label>
                        <input type="text" name="code" placeholder="เช่น SUMMER50"
                            class="block w-full bg-gray-900 border border-gray-700 rounded-xl py-4 px-5 text-white uppercase font-mono text-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-inner transition-all relative z-10"
                            value="{{ old('code', $promotion->code ?? '') }}"
                            :required="promoType === 'code' || promoType === 'free_shipping_code'" />
                        <p class="text-[11px] text-blue-300 mt-3 relative z-10 font-medium"><i
                                class="fas fa-info-circle mr-1"></i> ลูกค้าต้องกรอกรหัสนี้ให้ถูกต้องเพื่อรับสิทธิ์</p>
                    </div>

                    {{-- 🖱️ Selectable Info --}}
                    <div x-show="promoType === 'coupon_selectable'" x-transition
                        class="bg-teal-900/20 border border-teal-500/30 rounded-2xl p-6 flex items-start gap-4">
                        <div
                            class="w-12 h-12 rounded-full bg-teal-500/20 flex items-center justify-center text-teal-400 shrink-0 mt-1">
                            <i class="fas fa-mouse-pointer"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-teal-400 text-base mb-1">โหมดคูปองกดรับ</h4>
                            <p class="text-xs text-gray-300 leading-relaxed">คูปองนี้จะไปปรากฏในส่วน "คูปองแนะนำ"
                                ในหน้าชำระเงินโดยอัตโนมัติ โดยจะใช้ <span
                                    class="text-teal-300 underline font-bold px-1">ชื่อแคมเปญ</span>
                                เป็นข้อความแสดงผลให้ลูกค้าเห็นและกดใช้</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8"
                        x-show="promoType !== 'free_shipping' && promoType !== 'free_shipping_code'">
                        <div>
                            <label
                                class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">รูปแบบส่วนลด</label>
                            <select name="discount_type" x-model="discountType"
                                class="block w-full bg-gray-900 border border-gray-700 rounded-xl py-3.5 px-4 text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all font-bold">
                                <option value="fixed">ลดเป็นจำนวนเงิน (฿)</option>
                                <option value="percentage">ลดเป็นเปอร์เซ็นต์ (%)</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">มูลค่าที่ลด
                                <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <input type="number" name="discount_value" step="0.01" min="0"
                                    class="block w-full bg-gray-900 border border-gray-700 rounded-xl py-3.5 px-4 text-white font-black text-xl text-right pr-14 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-inner placeholder-gray-600"
                                    placeholder="0.00"
                                    value="{{ old('discount_value', $promotion->discount_value ?? '') }}"
                                    :required="promoType !== 'bxgy' && promoType !== 'free_shipping' &&
                                        promoType !== 'free_shipping_code'" />
                                <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none text-emerald-400 font-black text-lg"
                                    x-text="discountType === 'percentage' ? '%' : '฿'"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BXGY Config --}}
            <div x-show="promoType === 'bxgy'"
                class="bg-gray-800/90 backdrop-blur-sm rounded-3xl border border-gray-700/80 shadow-xl overflow-hidden"
                x-cloak>
                <div
                    class="p-6 border-b border-gray-700/50 bg-gray-800/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-pink-500/20 flex items-center justify-center text-pink-400 shadow-inner border border-pink-500/20">
                            <i class="fas fa-gift text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-white text-xl">2. ตั้งค่าเงื่อนไขของแถม</h3>
                            <p class="text-xs font-medium text-gray-400 mt-0.5">Buy X Get Y</p>
                        </div>
                    </div>
                    <div class="bg-gray-900 p-1.5 rounded-xl border border-gray-700 flex gap-1 shadow-inner">
                        <button type="button" @click="conditionType = 'any'"
                            :class="conditionType === 'any' ? 'bg-gray-700 text-white shadow-sm' :
                                'text-gray-500 hover:text-gray-300'"
                            class="px-4 py-2 rounded-lg text-xs font-bold transition-all">อย่างใดอย่างหนึ่ง</button>
                        <button type="button" @click="conditionType = 'all'"
                            :class="conditionType === 'all' ? 'bg-pink-600 text-white shadow-sm shadow-pink-900/50' :
                                'text-gray-500 hover:text-gray-300'"
                            class="px-4 py-2 rounded-lg text-xs font-bold transition-all">ซื้อครบทุกชิ้น</button>
                        <input type="hidden" name="condition_type" :value="conditionType">
                    </div>
                </div>
                <div class="p-8 space-y-6">
                    <div class="space-y-4">
                        <template x-for="(item, index) in buys" :key="'buy-' + index">
                            <div x-data="buyRow(item, index)" x-init="initSelect()" x-destroy="destroySelect()"
                                class="flex flex-col md:flex-row gap-5 p-5 bg-gray-900 rounded-2xl border border-gray-700 relative group">
                                <div class="flex-grow">
                                    <label
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">สินค้าที่ต้องซื้อ</label>
                                    <select x-ref="buySelect" :name="`buy_items[${index}][product_id][]`" multiple>
                                        @foreach ($products as $p)
                                            <option value="{{ $p->pd_sp_id }}">{{ $p->pd_sp_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:w-32 shrink-0">
                                    <label
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">จำนวน
                                        (ชิ้น)</label>
                                    <input type="number" :name="`buy_items[${index}][quantity]`"
                                        x-model="item.quantity" min="1"
                                        class="w-full bg-gray-800 border border-gray-700 focus:border-pink-500 focus:ring-1 focus:ring-pink-500 rounded-xl py-3 text-center font-black text-white transition-all shadow-inner" />
                                </div>
                                <button type="button" @click="$dispatch('remove-buy-item', { index: index })"
                                    class="absolute -top-3 -right-3 w-8 h-8 bg-gray-800 border border-gray-600 rounded-full text-red-400 flex items-center justify-center opacity-0 group-hover:opacity-100 hover:bg-red-500 hover:text-white transition-all shadow-lg"><i
                                        class="fas fa-times"></i></button>
                            </div>
                        </template>
                        <button type="button" @click="addItem('buy')"
                            class="w-full py-4 border-2 border-dashed border-gray-700 hover:border-gray-500 rounded-2xl text-sm font-bold text-gray-500 hover:text-gray-300 transition-all bg-gray-900/50 hover:bg-gray-800"><i
                                class="fas fa-plus mr-2"></i> เพิ่มเงื่อนไขสินค้าที่ต้องซื้อ</button>
                    </div>
                    <div class="flex justify-center">
                        <div
                            class="w-10 h-10 bg-gray-800 rounded-full border border-gray-700 flex items-center justify-center text-gray-500">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                    <div
                        class="space-y-5 bg-gradient-to-br from-pink-900/20 to-purple-900/20 p-6 rounded-3xl border border-pink-500/20">
                        <label class="text-xs font-black text-pink-400 uppercase block tracking-widest"><i
                                class="fas fa-gift mr-2"></i> ของแถมที่จะได้รับ</label>
                        <select id="giftable-products-select" name="giftable_product_ids[]" multiple>
                            @php $selectedGiftIds = collect(old('giftable_product_ids', isset($promotion) ? $promotion->actions->flatMap->giftableProducts->pluck('pd_sp_id') : []))->map(fn($id) => (string) $id); @endphp
                            @foreach ($products as $p)
                                <option value="{{ $p->pd_sp_id }}"
                                    {{ $selectedGiftIds->contains((string) $p->pd_sp_id) ? 'selected' : '' }}>
                                    {{ $p->pd_sp_name }}</option>
                            @endforeach
                        </select>
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between bg-gray-900 p-4 rounded-xl border border-gray-700 gap-4">
                            <span class="text-sm font-bold text-gray-300">จำนวนของแถมที่ได้รับ</span>
                            <div class="flex items-center gap-3">
                                <template x-for="(item, index) in gets" :key="index">
                                    <input type="number" :name="`get_items[${index}][quantity]`"
                                        x-model="item.quantity" min="1"
                                        class="w-24 bg-gray-800 border border-gray-600 focus:border-pink-500 focus:ring-1 focus:ring-pink-500 py-2.5 rounded-lg text-center font-black text-pink-400 text-lg transition-all shadow-inner" />
                                </template>
                                <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">ชิ้น</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: BASIC INFO --}}
        <div class="xl:col-span-1 space-y-6">
            <div
                class="bg-gray-800/90 backdrop-blur-sm rounded-3xl border border-gray-700/80 shadow-xl p-6 sticky top-6">
                <h3
                    class="text-lg font-extrabold text-white mb-6 flex items-center gap-3 pb-4 border-b border-gray-700/50">
                    <span class="w-1.5 h-6 bg-emerald-500 rounded-full shadow-[0_0_10px_#10b981]"></span> 3.
                    ข้อมูลและเงื่อนไข
                </h3>

                {{-- Name --}}
                <div class="mb-6">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2">ชื่อแคมเปญ
                        / ชื่อคูปอง <span class="text-red-400">*</span></label>
                    <input type="text" name="name"
                        class="block w-full bg-gray-900 border border-gray-700 rounded-xl py-3.5 px-4 text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-inner placeholder-gray-600 font-bold"
                        value="{{ old('name', $promotion->name ?? '') }}" required
                        placeholder="ระบุชื่อที่ลูกค้าจะเห็น" />
                </div>

                <div class="space-y-4">
                    {{-- Active Status Toggle --}}
                    <label
                        class="flex items-center justify-between p-4 bg-gray-900 rounded-2xl border border-gray-700 cursor-pointer hover:border-emerald-500/50 transition-all group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center text-gray-500 group-hover:text-emerald-400 transition-colors">
                                <i class="fas fa-power-off"></i>
                            </div>
                            <div>
                                <div
                                    class="text-sm font-bold text-white group-hover:text-emerald-400 transition-colors">
                                    เปิดใช้งาน</div>
                                <div class="text-[10px] text-gray-500">พร้อมใช้งานทันที</div>
                            </div>
                        </div>
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                            {{ old('is_active', $promotion->is_active ?? true) ? 'checked' : '' }}>
                        <div
                            class="w-11 h-6 bg-gray-700 rounded-full peer peer-checked:bg-emerald-500 transition-colors relative after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:w-5 after:h-5 after:rounded-full after:transition-transform peer-checked:after:translate-x-5 after:shadow-sm">
                        </div>
                    </label>

                    {{-- Stackable Toggle --}}
                    <label
                        class="flex items-center justify-between p-4 bg-gray-900 rounded-2xl border border-gray-700 cursor-pointer hover:border-blue-500/50 transition-all group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center text-gray-500 group-hover:text-blue-400 transition-colors">
                                <i class="fas fa-layer-group"></i></div>
                            <div>
                                <div class="text-sm font-bold text-white group-hover:text-blue-400 transition-colors">
                                    ใช้ร่วมโปรอื่นได้</div>
                                <div class="text-[10px] text-gray-500">ลดซ้อนลดได้</div>
                            </div>
                        </div>
                        <input type="hidden" name="is_stackable" value="0">
                        <input type="checkbox" name="is_stackable" value="1" class="sr-only peer"
                            {{ old('is_stackable', $promotion->is_stackable ?? true) ? 'checked' : '' }}>
                        <div
                            class="w-11 h-6 bg-gray-700 rounded-full peer peer-checked:bg-blue-500 transition-colors relative after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:w-5 after:h-5 after:rounded-full after:transition-transform peer-checked:after:translate-x-5 after:shadow-sm">
                        </div>
                    </label>

                    {{-- Priority --}}
                    <div class="p-4 bg-gray-900 rounded-2xl border border-gray-700">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">ลำดับ
                            (Priority)</label>
                        <div class="flex items-center gap-4">
                            <input type="number" name="priority" min="0"
                                class="w-24 bg-gray-800 border border-gray-600 focus:border-emerald-500 rounded-xl py-2.5 text-center font-black text-white transition-all shadow-inner"
                                value="{{ old('priority', $promotion->priority ?? 0) }}" />
                            <div class="text-xs text-gray-500 leading-tight">เลขน้อย = หักส่วนลดก่อน<br>(เช่น 0
                                หักเป็นอันดับแรก)</div>
                        </div>
                    </div>
                </div>

                <div class="my-6 border-t border-gray-700/50"></div>

                {{-- Limits & Dates --}}
                <div class="space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-900 p-3.5 rounded-2xl border border-gray-700">
                            <label
                                class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 text-center">ยอดซื้อขั้นต่ำ
                                (฿)</label>
                            <input type="number" name="min_order_value" step="0.01"
                                class="w-full bg-gray-800 border-none rounded-lg py-2 text-white text-center font-bold shadow-inner"
                                value="{{ old('min_order_value', $promotion->min_order_value ?? 0) }}" />
                        </div>
                        <div class="bg-gray-900 p-3.5 rounded-2xl border border-gray-700">
                            <label
                                class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 text-center">จำกัดสิทธิ์
                                (ครั้ง)</label>
                            <input type="number" name="usage_limit" placeholder="ไม่จำกัด"
                                class="w-full bg-gray-800 border-none rounded-lg py-2 text-white text-center font-bold shadow-inner placeholder-gray-600"
                                value="{{ old('usage_limit', $promotion->usage_limit ?? '') }}" />
                        </div>
                    </div>

                    <div class="bg-gray-900 p-5 rounded-2xl border border-gray-700 space-y-4">
                        <label
                            class="text-[11px] font-black text-emerald-400 uppercase tracking-widest flex justify-center gap-2"><i
                                class="far fa-calendar-alt"></i> ระยะเวลาแคมเปญ</label>
                        <div class="space-y-3">
                            <div class="relative">
                                <span
                                    class="absolute left-3 top-2 text-[10px] font-bold text-gray-500 uppercase">เริ่ม</span>
                                <input type="datetime-local" name="start_date"
                                    class="w-full bg-gray-800 border border-gray-600 rounded-xl pt-6 pb-2 px-3 text-white text-sm font-medium focus:border-emerald-500 transition-all"
                                    value="{{ old('start_date', isset($promotion->start_date) ? \Carbon\Carbon::parse($promotion->start_date)->format('Y-m-d\TH:i') : '') }}" />
                            </div>
                            <div class="relative">
                                <span
                                    class="absolute left-3 top-2 text-[10px] font-bold text-gray-500 uppercase">สิ้นสุด</span>
                                <input type="datetime-local" name="end_date"
                                    class="w-full bg-gray-800 border border-gray-600 rounded-xl pt-6 pb-2 px-3 text-white text-sm font-medium focus:border-emerald-500 transition-all"
                                    value="{{ old('end_date', isset($promotion->end_date) ? \Carbon\Carbon::parse($promotion->end_date)->format('Y-m-d\TH:i') : '') }}" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-4">
                    <button type="submit"
                        class="w-full py-4 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-extrabold rounded-2xl shadow-[0_0_20px_rgba(16,185,129,0.3)] transition-all transform active:scale-95 flex justify-center items-center gap-2 text-lg">
                        <i class="fas fa-save"></i> บันทึกข้อมูล
                    </button>
                    <a href="{{ route('admin.promotions.index') }}"
                        class="block w-full py-4 text-center text-gray-400 hover:text-white text-sm font-bold transition-colors mt-2">ยกเลิก</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('buyRow', (item, index) => ({
            tomSelectInstance: null,
            initSelect() {
                if (typeof TomSelect === 'undefined') return;
                const el = this.$refs.buySelect;
                if (!el) return;
                const selectedIds = Array.isArray(item.product_id) ? item.product_id.map(String) : (
                    item.product_id ? [String(item.product_id)] : []);
                this.tomSelectInstance = new TomSelect(el, {
                    plugins: ['remove_button', 'clear_button'],
                    items: selectedIds,
                    render: {
                        item: (data, escape) =>
                            `<div class="bg-gray-700 text-emerald-400 px-2 py-1 rounded text-xs font-bold mr-1 shadow-sm border border-gray-600">${escape(data.text)}</div>`,
                        option: (data, escape) =>
                            `<div class="py-2 px-3 text-sm font-medium text-gray-300 hover:text-white transition-colors">${escape(data.text)}</div>`
                    },
                    onChange: (values) => {
                        item.product_id = values;
                    }
                });
            },
            destroySelect() {
                if (this.tomSelectInstance) this.tomSelectInstance.destroy();
            }
        }));

        Alpine.data('promotionForm', (initialType, initialDiscountType) => ({
            // 🔥 เปลี่ยนมาใช้ json_encode เพื่อแก้ปัญหา Error ปีกกาชนกัน 🔥
            buys: {!! json_encode($buyData) !!},
            gets: {!! json_encode($getData) !!},
            discountType: initialDiscountType,
            conditionType: '{{ old('condition_type', $promotion->condition_type ?? 'any') }}',
            promoType: initialType,
            giftTomSelect: null,
            get isDiscountCode() {
                return this.promoType === 'code' || this.promoType === 'free_shipping_code';
            },
            get isSelectable() {
                return this.promoType === 'coupon_selectable';
            },
            get isBxGy() {
                return this.promoType === 'bxgy';
            },
            init() {
                this.$nextTick(() => {
                    this.initGiftSelect();
                });
                this.$el.addEventListener('remove-buy-item', (e) => {
                    this.removeItem('buy', e.detail.index);
                });
            },
            initGiftSelect() {
                const giftEl = document.getElementById('giftable-products-select');
                if (giftEl && !this.giftTomSelect) {
                    this.giftTomSelect = new TomSelect(giftEl, {
                        plugins: ['remove_button', 'clear_button'],
                        placeholder: 'คลิกเพื่อเลือกสินค้าของแถม...',
                        render: {
                            item: (data, escape) =>
                                `<div class="bg-pink-500/20 text-pink-300 px-2.5 py-1 rounded border border-pink-500/30 mr-1 text-xs font-bold flex items-center gap-1.5 shadow-sm"><i class="fas fa-gift text-[10px]"></i> ${escape(data.text)}</div>`,
                            option: (data, escape) =>
                                `<div class="py-2.5 px-3 text-sm font-medium text-gray-300 transition-colors">${escape(data.text)}</div>`
                        }
                    });
                }
            },
            addItem(type) {
                if (type === 'buy') this.buys.push({
                    product_id: [],
                    quantity: 1
                });
            },
            removeItem(type, index) {
                if (type === 'buy' && this.buys.length > 1) this.buys.splice(index, 1);
            },
        }));
    });
</script>
