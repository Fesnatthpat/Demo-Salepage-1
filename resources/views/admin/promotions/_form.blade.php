@php
    $buyData = old('buy_items', $buy_items ?? [['product_id' => [], 'quantity' => 1]]);
    if (empty($buyData)) {
        $buyData = [['product_id' => [], 'quantity' => 1]];
    }

    $getData = old('get_items', $get_items ?? [['product_id' => '', 'quantity' => 1]]);
    if (empty($getData)) {
        $getData = [['product_id' => '', 'quantity' => 1]];
    }
@endphp

{{-- TomSelect Custom Styles --}}
<style>
    .ts-wrapper.multi .ts-control>div {
        background: rgba(16, 185, 129, 0.2) !important;
        border: 1px solid rgba(16, 185, 129, 0.4) !important;
        color: #6ee7b7 !important;
        border-radius: 4px;
    }

    .ts-control {
        background-color: #1f2937 !important;
        /* gray-800 */
        border-color: #374151 !important;
        /* gray-700 */
        color: #f3f4f6 !important;
        border-radius: 0.5rem;
        padding: 10px 12px !important;
    }

    .ts-control input {
        color: #f3f4f6 !important;
    }

    .ts-dropdown {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
        color: #f3f4f6 !important;
        z-index: 50 !important;
    }

    .ts-dropdown .option:hover,
    .ts-dropdown .active {
        background-color: #374151 !important;
        color: #10b981 !important;
    }

    /* Smooth Toggle Transition */
    .slide-enter-active,
    .slide-leave-active {
        transition: all 0.3s ease;
    }

    .slide-enter-from,
    .slide-leave-to {
        opacity: 0;
        transform: translateY(-10px);
    }
</style>

<div x-data="promotionForm(
    {{ old('is_discount_code', isset($promotion) && $promotion->code ? 'true' : 'false') }},
    '{{ old('discount_type', $promotion->discount_type ?? '') }}'
)" class="space-y-6">

    {{-- Error Alert --}}
    @if ($errors->any())
        <div class="alert alert-error bg-red-900/20 border border-red-500/50 text-red-200 shadow-lg rounded-xl">
            <i class="fas fa-exclamation-circle text-xl"></i>
            <div>
                <h3 class="font-bold">พบข้อผิดพลาด</h3>
                <ul class="text-sm mt-1 list-disc list-inside opacity-90">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">

        {{-- LEFT COLUMN: Basic Info --}}
        <div class="xl:col-span-4 space-y-6">

            {{-- Card 1: Main Details --}}
            <div class="card bg-gray-800 shadow-xl border border-gray-700/50">
                <div class="card-body p-6">
                    <h3 class="card-title text-gray-100 text-lg mb-4 flex items-center gap-2">
                        <span class="w-2 h-6 bg-emerald-500 rounded-full"></span> ข้อมูลแคมเปญ
                    </h3>

                    {{-- Active Switch --}}
                    <div class="form-control mb-2">
                        <label
                            class="label cursor-pointer justify-between p-3 bg-gray-900/50 rounded-lg border border-gray-700 hover:border-gray-600 transition">
                            <span class="label-text font-medium text-gray-300">สถานะเปิดใช้งาน</span>
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="toggle toggle-success"
                                {{ old('is_active', $promotion->is_active ?? true) ? 'checked' : '' }} />
                        </label>
                    </div>

                    {{-- Name --}}
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-medium text-gray-400">ชื่อแคมเปญ <span
                                    class="text-red-400">*</span></span></label>
                        <input type="text" name="name" placeholder="เช่น โปรโมชั่นปีใหม่, ซื้อ 1 แถม 1"
                            class="input input-bordered bg-gray-900/50 border-gray-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 w-full"
                            value="{{ old('name', $promotion->name ?? '') }}" required />
                    </div>

                    {{-- Description --}}
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-medium text-gray-400">รายละเอียด
                                (Optional)</span></label>
                        <textarea name="description" placeholder="รายละเอียดโปรโมชั่นสำหรับแสดงให้ลูกค้าเห็น..."
                            class="textarea textarea-bordered h-24 bg-gray-900/50 border-gray-700 focus:border-emerald-500 w-full resize-none">{{ old('description', $promotion->description ?? '') }}</textarea>
                    </div>

                    {{-- Date Range --}}
                    <div class="grid grid-cols-2 gap-3 mt-2">
                        <div class="form-control w-full">
                            <label class="label"><span
                                    class="label-text text-xs text-gray-500 uppercase font-bold">เริ่มวันที่</span></label>
                            <input type="datetime-local" name="start_date"
                                class="input input-sm input-bordered bg-gray-900/50 border-gray-700 text-gray-300 w-full"
                                value="{{ old('start_date', isset($promotion->start_date) ? \Carbon\Carbon::parse($promotion->start_date)->format('Y-m-d\TH:i') : '') }}" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span
                                    class="label-text text-xs text-gray-500 uppercase font-bold">สิ้นสุดวันที่</span></label>
                            <input type="datetime-local" name="end_date"
                                class="input input-sm input-bordered bg-gray-900/50 border-gray-700 text-gray-300 w-full"
                                value="{{ old('end_date', isset($promotion->end_date) ? \Carbon\Carbon::parse($promotion->end_date)->format('Y-m-d\TH:i') : '') }}" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Limits --}}
            <div class="card bg-gray-800 shadow-xl border border-gray-700/50">
                <div class="card-body p-6">
                    <h3 class="card-title text-gray-100 text-lg mb-4 flex items-center gap-2">
                        <span class="w-2 h-6 bg-blue-500 rounded-full"></span> ข้อกำหนด
                    </h3>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-medium text-gray-400">จำกัดสิทธิ์ทั้งหมด
                                (ครั้ง)</span></label>
                        <div class="relative">
                            <input type="number" name="usage_limit"
                                class="input input-bordered bg-gray-900/50 border-gray-700 pl-10 w-full"
                                value="{{ old('usage_limit', $promotion->usage_limit ?? '') }}" min="1"
                                placeholder="ไม่จำกัด" />
                            <i class="fas fa-users absolute left-4 top-3.5 text-gray-500"></i>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 pl-1">ปล่อยว่างหากไม่ต้องการจำกัดจำนวน</p>
                    </div>

                    <div class="form-control w-full mt-2">
                        <label class="label"><span class="label-text font-medium text-gray-400">ยอดซื้อขั้นต่ำ
                                (บาท)</span></label>
                        <div class="relative">
                            <input type="number" name="min_order_value"
                                class="input input-bordered bg-gray-900/50 border-gray-700 pl-10 w-full"
                                value="{{ old('min_order_value', $promotion->min_order_value ?? '') }}" step="0.01"
                                min="0" placeholder="0.00" />
                            <i class="fas fa-coins absolute left-4 top-3.5 text-gray-500"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Desktop Actions --}}
            <div class="hidden xl:flex flex-col gap-3 sticky top-4">
                <button type="submit"
                    class="btn btn-primary bg-emerald-600 hover:bg-emerald-700 border-none w-full text-lg shadow-lg shadow-emerald-900/20">
                    <i class="fas fa-save mr-2"></i> บันทึกโปรโมชั่น
                </button>
                <a href="{{ route('admin.promotions.index') }}"
                    class="btn btn-ghost w-full text-gray-400 hover:bg-gray-800">ยกเลิก</a>
            </div>
        </div>

        {{-- RIGHT COLUMN: Strategy & Rules --}}
        <div class="xl:col-span-8 space-y-6">

            {{-- Strategy Selector --}}
            <div class="card bg-gray-800 shadow-xl border border-gray-700/50">
                <div class="card-body p-6">
                    <h3 class="card-title text-gray-100 text-lg mb-6 flex items-center gap-2">
                        <i class="fas fa-chess-queen text-purple-400"></i> เลือกประเภทโปรโมชั่น
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Type: Auto Discount --}}
                        <label class="cursor-pointer group relative">
                            <input type="radio" name="promo_type_selector" value="auto" x-model="promoType"
                                class="peer sr-only" />
                            <div
                                class="p-4 rounded-xl border-2 border-gray-700 bg-gray-900/30 hover:bg-gray-700/50 transition-all duration-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-900/10 h-full flex flex-col items-center text-center">
                                <div
                                    class="w-12 h-12 rounded-full bg-emerald-900/30 flex items-center justify-center text-emerald-400 mb-3 peer-checked:bg-emerald-500 peer-checked:text-white transition-colors">
                                    <i class="fas fa-bolt text-xl"></i>
                                </div>
                                <h4 class="font-bold text-gray-200 peer-checked:text-emerald-400">ส่วนลดอัตโนมัติ</h4>
                                <p class="text-xs text-gray-500 mt-2">ลดทันทีเมื่อถึงยอด (ไม่ต้องใช้โค้ด)</p>
                            </div>
                            <div
                                class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 text-emerald-500 transition-opacity">
                                <i class="fas fa-check-circle"></i></div>
                        </label>

                        {{-- Type: Coupon Code --}}
                        <label class="cursor-pointer group relative">
                            <input type="radio" name="promo_type_selector" value="code" x-model="promoType"
                                class="peer sr-only" />
                            <div
                                class="p-4 rounded-xl border-2 border-gray-700 bg-gray-900/30 hover:bg-gray-700/50 transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-900/10 h-full flex flex-col items-center text-center">
                                <div
                                    class="w-12 h-12 rounded-full bg-blue-900/30 flex items-center justify-center text-blue-400 mb-3 peer-checked:bg-blue-500 peer-checked:text-white transition-colors">
                                    <i class="fas fa-ticket-alt text-xl"></i>
                                </div>
                                <h4 class="font-bold text-gray-200 peer-checked:text-blue-400">ใช้รหัสส่วนลด</h4>
                                <p class="text-xs text-gray-500 mt-2">ลูกค้าต้องกรอกรหัสเพื่อรับส่วนลด</p>
                            </div>
                            <div
                                class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 text-blue-500 transition-opacity">
                                <i class="fas fa-check-circle"></i></div>
                        </label>

                        {{-- Type: Buy X Get Y --}}
                        <label class="cursor-pointer group relative">
                            <input type="radio" name="promo_type_selector" value="bxgy" x-model="promoType"
                                class="peer sr-only" />
                            <div
                                class="p-4 rounded-xl border-2 border-gray-700 bg-gray-900/30 hover:bg-gray-700/50 transition-all duration-200 peer-checked:border-pink-500 peer-checked:bg-pink-900/10 h-full flex flex-col items-center text-center">
                                <div
                                    class="w-12 h-12 rounded-full bg-pink-900/30 flex items-center justify-center text-pink-400 mb-3 peer-checked:bg-pink-500 peer-checked:text-white transition-colors">
                                    <i class="fas fa-gifts text-xl"></i>
                                </div>
                                <h4 class="font-bold text-gray-200 peer-checked:text-pink-400">ซื้อ X แถม Y</h4>
                                <p class="text-xs text-gray-500 mt-2">ซื้อสินค้าครบตามเงื่อนไข แถมฟรี</p>
                            </div>
                            <div
                                class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 text-pink-500 transition-opacity">
                                <i class="fas fa-check-circle"></i></div>
                        </label>
                    </div>
                    <input type="hidden" name="is_discount_code" :value="isDiscountCode ? 1 : 0">
                </div>
            </div>

            {{-- DYNAMIC SECTION 1: Discount Logic (Auto/Code) --}}
            <div x-show="promoType === 'auto' || promoType === 'code'"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                class="card bg-gray-800 shadow-xl border border-gray-700/50">
                <div class="card-body p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="fas fa-calculator text-blue-400"></i>
                        <h3 class="font-bold text-gray-200">ตั้งค่าส่วนลด</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-control w-full" x-show="promoType === 'code'">
                            <label class="label"><span class="label-text font-medium text-gray-400">รหัสส่วนลด
                                    (Coupon Code) <span class="text-red-400">*</span></span></label>
                            <input type="text" name="code" placeholder="เช่น NEWYEAR2024"
                                class="input input-bordered bg-gray-900 border-gray-600 focus:border-blue-500 text-lg font-mono uppercase tracking-wider text-white"
                                value="{{ old('code', $promotion->code ?? '') }}"
                                :required="promoType === 'code'" />
                        </div>

                        <div class="md:col-span-2 grid grid-cols-2 gap-4">
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text font-medium text-gray-400">ประเภทส่วนลด
                                        <span class="text-red-400">*</span></span></label>
                                <select name="discount_type" x-model="discountType"
                                    :required="promoType === 'auto' || promoType === 'code'"
                                    class="select select-bordered w-full bg-gray-900 border-gray-600 focus:border-blue-500 text-gray-200">
                                    <option value="">-- กรุณาเลือก --</option>
                                    @foreach ($discountTypes as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ old('discount_type', $promotion->discount_type ?? '') == $key ? 'selected' : '' }}>
                                            {{ $key === 'fixed' ? 'ลดเป็นบาท (THB)' : 'ลดเป็นเปอร์เซ็นต์ (%)' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text font-medium text-gray-400">มูลค่าส่วนลด
                                        <span class="text-red-400">*</span></span></label>
                                <div class="relative">
                                    <input type="number" name="discount_value" placeholder="0"
                                        class="input input-bordered w-full bg-gray-900 border-gray-600 focus:border-blue-500 pr-10 text-right text-xl font-bold text-white"
                                        value="{{ old('discount_value', $promotion->discount_value ?? '') }}"
                                        :required="promoType === 'auto' || promoType === 'code'"
                                        step="0.01" min="0" />
                                    <span class="absolute right-4 top-3 text-gray-500 font-bold"
                                        x-text="discountType === 'percentage' ? '%' : '฿'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DYNAMIC SECTION 2: Buy X Get Y Builder --}}
            <div x-show="isBxGy" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                class="card bg-gray-800 shadow-xl border border-gray-700/50">
                <div class="card-body p-0 overflow-hidden">
                    {{-- Header --}}
                    <div class="p-4 bg-gray-900/50 border-b border-gray-700 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-layer-group text-pink-400"></i>
                            <h3 class="font-bold text-gray-200">สร้างเงื่อนไขแถมสินค้า</h3>
                        </div>

                        {{-- Condition Logic Switch --}}
                        <div class="flex bg-gray-800 rounded-lg p-1 border border-gray-600">
                            <label class="cursor-pointer px-3 py-1 rounded-md text-xs font-bold transition-colors"
                                :class="conditionType === 'any' ? 'bg-gray-600 text-white shadow-sm' :
                                    'text-gray-400 hover:text-gray-200'">
                                <input type="radio" name="condition_type" value="any" x-model="conditionType"
                                    class="hidden" :checked="conditionType === 'any'"> OR (อย่างใดอย่างหนึ่ง)
                            </label>
                            <label class="cursor-pointer px-3 py-1 rounded-md text-xs font-bold transition-colors"
                                :class="conditionType === 'all' ? 'bg-emerald-600 text-white shadow-sm' :
                                    'text-gray-400 hover:text-gray-200'">
                                <input type="radio" name="condition_type" value="all" x-model="conditionType"
                                    class="hidden" :checked="conditionType === 'all'"> AND (ครบทุกข้อ)
                            </label>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="flex flex-col xl:flex-row gap-6 items-stretch">

                            {{-- BUY SECTION --}}
                            <div class="flex-1 border border-emerald-500/30 rounded-xl bg-gray-900/30 relative">
                                <div
                                    class="absolute -top-3 left-4 bg-gray-800 px-2 text-emerald-400 text-xs font-bold border border-emerald-500/30 rounded">
                                    <i class="fas fa-shopping-cart mr-1"></i> เงื่อนไข (ซื้อ)
                                </div>
                                <div class="p-4 pt-6 space-y-4">
                                    <template x-for="(item, index) in buys" :key="index">
                                        <div
                                            class="bg-gray-800 rounded-lg p-3 border border-gray-700 relative group transition-all hover:border-emerald-500/50">
                                            <button type="button" x-show="buys.length > 1"
                                                @click="removeItem('buy', index)"
                                                class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-red-500 text-white text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-10 hover:bg-red-600">
                                                <i class="fas fa-times"></i>
                                            </button>

                                            <div class="mb-2">
                                                <label
                                                    class="text-[10px] text-gray-500 uppercase font-bold">สินค้า</label>
                                                <select :id="'buy-products-select-' + index"
                                                    :name="`buy_items[${index}][product_id][]`" multiple
                                                    class="buy-products-select w-full"
                                                    x-bind:disabled="isDiscountCode">
                                                    @foreach ($products as $p)
                                                        <option value="{{ $p->pd_sp_id }}"
                                                            :selected="Array.isArray(item.product_id) ? item.product_id.map(String)
                                                                .includes('{{ $p->pd_sp_id }}') : String(item
                                                                    .product_id) === '{{ $p->pd_sp_id }}'">
                                                            {{ $p->pd_sp_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs text-gray-400">จำนวนที่ต้องซื้อ</span>
                                                <div
                                                    class="flex items-center bg-gray-900 rounded border border-gray-600">
                                                    <button type="button"
                                                        class="px-2 py-1 text-gray-400 hover:text-white"
                                                        @click="item.quantity = Math.max(1, parseInt(item.quantity)-1)">-</button>
                                                    <input type="number" :name="`buy_items[${index}][quantity]`"
                                                        x-model="item.quantity" min="1"
                                                        class="w-12 bg-transparent text-center text-sm font-bold text-emerald-400 border-none p-0 focus:ring-0">
                                                    <button type="button"
                                                        class="px-2 py-1 text-gray-400 hover:text-white"
                                                        @click="item.quantity = parseInt(item.quantity)+1">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <button type="button" @click="addItem('buy')"
                                        class="btn btn-xs btn-outline border-dashed w-full border-gray-600 text-gray-400 hover:bg-emerald-900/20 hover:text-emerald-400 hover:border-emerald-500">
                                        <i class="fas fa-plus mr-1"></i> เพิ่มเงื่อนไข
                                    </button>
                                </div>
                            </div>

                            {{-- ARROW --}}
                            <div class="flex items-center justify-center">
                                <div class="bg-gray-700 text-gray-400 p-2 rounded-full shadow-lg">
                                    <i class="fas fa-arrow-down xl:fa-arrow-right text-xl"></i>
                                </div>
                            </div>

                            {{-- GET SECTION --}}
                            <div class="flex-1 border border-pink-500/30 rounded-xl bg-gray-900/30 relative">
                                <div
                                    class="absolute -top-3 left-4 bg-gray-800 px-2 text-pink-400 text-xs font-bold border border-pink-500/30 rounded">
                                    <i class="fas fa-gift mr-1"></i> ผลลัพธ์ (แถมฟรี)
                                </div>
                                <div class="p-4 pt-6 space-y-4">
                                    <template x-for="(item, index) in gets" :key="index">
                                        <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                                            <input type="hidden" :name="`get_items[${index}][product_id]`"
                                                :value="item.product_id">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs text-gray-300">จำนวนของแถมที่ได้รับ</span>
                                                <div
                                                    class="flex items-center bg-gray-900 rounded border border-gray-600">
                                                    <button type="button"
                                                        class="px-3 py-1 text-gray-400 hover:text-white"
                                                        @click="item.quantity = Math.max(1, parseInt(item.quantity)-1)">-</button>
                                                    <input type="number" :name="`get_items[${index}][quantity]`"
                                                        x-model="item.quantity" min="1"
                                                        class="w-16 bg-transparent text-center text-lg font-bold text-pink-400 border-none p-0 focus:ring-0">
                                                    <button type="button"
                                                        class="px-3 py-1 text-gray-400 hover:text-white"
                                                        @click="item.quantity = parseInt(item.quantity)+1">+</button>
                                                </div>
                                            </div>
                                            <p class="text-[10px] text-gray-500 mt-2 text-center">
                                                (ลูกค้าเลือกจากรายการด้านล่าง)</p>
                                        </div>
                                    </template>

                                    <div class="border-t border-gray-700 pt-3">
                                        <label
                                            class="text-[10px] text-gray-500 uppercase font-bold mb-1 block">รายการของแถม
                                            (Pool)</label>
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
                                                    {{ $p->pd_sp_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile Actions --}}
            <div class="xl:hidden grid grid-cols-2 gap-3 mt-6">
                <a href="{{ route('admin.promotions.index') }}"
                    class="btn btn-ghost bg-gray-800 text-gray-400">ยกเลิก</a>
                <button type="submit" class="btn btn-primary bg-emerald-600 border-none text-white">บันทึก</button>
            </div>
        </div>
    </div>
</div>

{{-- Alpine Logic --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('promotionForm', (initialIsDiscountCode, initialDiscountType) => ({
            buys: @json($buyData),
            gets: @json($getData),
            discountType: initialDiscountType,
            conditionType: '{{ old('condition_type', $promotion->condition_type ?? 'any') }}',
            promoType: '{{ old('promo_type_selector', isset($promotion) ? ($promotion->code ? 'code' : ($promotion->rules->count() > 0 ? 'bxgy' : 'auto')) : 'auto') }}',
            tomSelects: {},

            get isDiscountCode() {
                return this.promoType === 'code';
            },
            get isBxGy() {
                return this.promoType === 'bxgy';
            },

            init() {
                this.$nextTick(() => {
                    this.initAllSelects();
                });
                this.$watch('buys', () => {
                    this.$nextTick(() => {
                        this.initAllSelects();
                    });
                });
            },

            initAllSelects() {
                if (typeof TomSelect === 'undefined') return;

                // Gift Pool
                if (!this.tomSelects['gift-pool']) {
                    const giftEl = document.getElementById('giftable-products-select');
                    if (giftEl) {
                        this.tomSelects['gift-pool'] = new TomSelect(giftEl, {
                            plugins: ['remove_button', 'clear_button'],
                            create: false,
                            placeholder: 'เลือกสินค้าของแถม...',
                            render: {
                                item: (data, escape) =>
                                    `<div class="bg-pink-900/30 text-pink-300 border border-pink-500/30 px-2 py-1 rounded mr-1 mb-1 text-xs">${escape(data.text)}</div>`
                            }
                        });
                    }
                }

                // Buy Items
                document.querySelectorAll('.buy-products-select').forEach((el) => {
                    if (!el.tomselect) {
                        this.tomSelects[el.id] = new TomSelect(el, {
                            plugins: ['remove_button'],
                            create: false,
                            placeholder: 'เลือกสินค้า...',
                            render: {
                                item: (data, escape) =>
                                    `<div class="bg-emerald-900/30 text-emerald-300 border border-emerald-500/30 px-2 py-1 rounded mr-1 mb-1 text-xs">${escape(data.text)}</div>`
                            }
                        });
                    }
                });
            },

            addItem(type) {
                if (type === 'buy') this.buys.push({
                    product_id: [],
                    quantity: 1
                });
                // Note: Get items logic handles single pool usually, but keeping struct for compatibility
            },

            removeItem(type, index) {
                if (type === 'buy' && this.buys.length > 1) {
                    const id = 'buy-products-select-' + index;
                    if (this.tomSelects[id]) {
                        this.tomSelects[id].destroy();
                        delete this.tomSelects[id];
                    }
                    this.buys.splice(index, 1);
                }
            },
        }))
    });
</script>
