<?php
    $buyData = old('buy_items', $buy_items ?? [['product_id' => [], 'quantity' => 1]]);
    if (empty($buyData)) {
        $buyData = [['product_id' => [], 'quantity' => 1]];
    }

    $getData = old('get_items', $get_items ?? [['product_id' => '', 'quantity' => 1]]);
    if (empty($getData)) {
        $getData = [['product_id' => '', 'quantity' => 1]];
    }
?>

<style>
    /* --- TomSelect Custom Dark Theme --- */
    .ts-control {
        background-color: #111827 !important;
        /* gray-900 */
        border-color: #374151 !important;
        /* gray-700 */
        color: #e5e7eb !important;
        /* gray-200 */
        border-radius: 0.5rem;
        padding: 0.625rem 0.75rem !important;
        min-height: 46px;
    }

    .ts-control input {
        color: #e5e7eb !important;
    }

    /* Dropdown Styling */
    .ts-dropdown {
        background-color: #1f2937 !important;
        /* gray-800 */
        border: 1px solid #4b5563 !important;
        /* gray-600 */
        color: #e5e7eb !important;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
        z-index: 99999 !important;
        /* Layer สูงสุด */

        /* ตัดคำสั่ง width auto ออก เพื่อให้กว้างเท่ากับ Input */
        overflow: hidden;
    }

    /* จัดการข้อความยาวๆ ให้ขึ้นบรรทัดใหม่ ไม่ล้นจอ */
    .ts-dropdown .option {
        padding: 12px 14px;
        white-space: normal !important;
        /* หัวใจสำคัญ: ให้ตัดคำลงบรรทัดใหม่ */
        word-wrap: break-word;
        /* ตัดคำถ้ายาวเกิน */
        border-bottom: 1px solid #374151;
        line-height: 1.5;
        font-size: 0.875rem;
        /* text-sm */
    }

    .ts-dropdown .option:last-child {
        border-bottom: none;
    }

    .ts-dropdown .option:hover,
    .ts-dropdown .active {
        background-color: #374151 !important;
        color: #34d399 !important;
        /* emerald-400 */
    }

    /* Selected Items Badge */
    .ts-wrapper.multi .ts-control>div {
        background: rgba(52, 211, 153, 0.15) !important;
        border: 1px solid rgba(52, 211, 153, 0.3) !important;
        color: #34d399 !important;
        border-radius: 0.375rem;
        padding: 2px 8px;
        margin-right: 4px;
        margin-bottom: 4px;
    }
</style>

<div x-data="promotionForm(
    <?php echo e(old('is_discount_code', isset($promotion) && $promotion->code ? 'true' : 'false')); ?>,
    '<?php echo e(old('discount_type', $promotion->discount_type ?? '')); ?>'
)" class="space-y-8">

    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        
        <label class="cursor-pointer group relative">
            <input type="radio" name="promo_type_selector" value="auto" x-model="promoType" class="peer sr-only" />
            <div
                class="h-full p-5 rounded-2xl border-2 border-gray-700 bg-gray-800/50 hover:bg-gray-800 hover:border-emerald-500/50 transition-all duration-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-900/10 flex flex-col items-center text-center">
                <div
                    class="w-14 h-14 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 mb-4 peer-checked:bg-emerald-500 peer-checked:text-white transition-colors shadow-lg">
                    <i class="fas fa-bolt text-2xl"></i>
                </div>
                <h4 class="font-bold text-white text-lg peer-checked:text-emerald-400">ส่วนลดอัตโนมัติ</h4>
                <p class="text-xs text-gray-400 mt-2 leading-relaxed">ลดทันทีเมื่อยอดถึง (ไม่ต้องใช้โค้ด)</p>
            </div>
            <div class="absolute top-3 right-3 text-emerald-500 opacity-0 peer-checked:opacity-100 transition-opacity">
                <i class="fas fa-check-circle text-xl"></i></div>
        </label>

        
        <label class="cursor-pointer group relative">
            <input type="radio" name="promo_type_selector" value="code" x-model="promoType" class="peer sr-only" />
            <div
                class="h-full p-5 rounded-2xl border-2 border-gray-700 bg-gray-800/50 hover:bg-gray-800 hover:border-blue-500/50 transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-900/10 flex flex-col items-center text-center">
                <div
                    class="w-14 h-14 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 mb-4 peer-checked:bg-blue-500 peer-checked:text-white transition-colors shadow-lg">
                    <i class="fas fa-ticket-alt text-2xl"></i>
                </div>
                <h4 class="font-bold text-white text-lg peer-checked:text-blue-400">ใช้รหัสส่วนลด</h4>
                <p class="text-xs text-gray-400 mt-2 leading-relaxed">ลูกค้าต้องกรอกโค้ดเพื่อรับสิทธิ์</p>
            </div>
            <div class="absolute top-3 right-3 text-blue-500 opacity-0 peer-checked:opacity-100 transition-opacity"><i
                    class="fas fa-check-circle text-xl"></i></div>
        </label>

        
        <label class="cursor-pointer group relative">
            <input type="radio" name="promo_type_selector" value="bxgy" x-model="promoType" class="peer sr-only" />
            <div
                class="h-full p-5 rounded-2xl border-2 border-gray-700 bg-gray-800/50 hover:bg-gray-800 hover:border-pink-500/50 transition-all duration-200 peer-checked:border-pink-500 peer-checked:bg-pink-900/10 flex flex-col items-center text-center">
                <div
                    class="w-14 h-14 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 mb-4 peer-checked:bg-pink-500 peer-checked:text-white transition-colors shadow-lg">
                    <i class="fas fa-gifts text-2xl"></i>
                </div>
                <h4 class="font-bold text-white text-lg peer-checked:text-pink-400">ซื้อ X แถม Y</h4>
                <p class="text-xs text-gray-400 mt-2 leading-relaxed">ซื้อสินค้าครบตามเงื่อนไข แถมฟรี</p>
            </div>
            <div class="absolute top-3 right-3 text-pink-500 opacity-0 peer-checked:opacity-100 transition-opacity"><i
                    class="fas fa-check-circle text-xl"></i></div>
        </label>
    </div>
    <input type="hidden" name="is_discount_code" :value="isDiscountCode ? 1 : 0">

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="bg-red-900/20 border border-red-500/50 rounded-xl p-4 flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-red-500 mt-1"></i>
            <div>
                <h3 class="font-bold text-red-400">พบข้อผิดพลาด</h3>
                <ul class="text-sm text-red-300 mt-1 list-disc list-inside">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <li><?php echo e($error); ?></li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 items-start">

        
        <div class="xl:col-span-2 space-y-6">

            
            <div x-show="promoType === 'auto' || promoType === 'code'"
                x-transition:enter="transition ease-out duration-300"
                class="bg-gray-800 rounded-xl border border-gray-700 shadow-xl overflow-visible">

                <div class="p-4 border-b border-gray-700 bg-gray-800/50 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3 class="font-bold text-white">ตั้งค่ามูลค่าส่วนลด</h3>
                </div>

                <div class="p-6 space-y-6">
                    
                    <div x-show="promoType === 'code'" class="bg-blue-900/10 border border-blue-500/20 rounded-xl p-5">
                        <label class="block text-sm font-medium text-blue-300 mb-2">รหัสส่วนลด (Coupon Code) <span
                                class="text-red-400">*</span></label>
                        <div class="relative">
                            <input type="text" name="code" placeholder="เช่น SALE2024"
                                class="block w-full bg-gray-900 border-gray-600 rounded-lg py-3 px-4 text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 uppercase tracking-widest font-mono text-lg"
                                value="<?php echo e(old('code', $promotion->code ?? '')); ?>"
                                :required="promoType === 'code'" />
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <i class="fas fa-ticket-alt text-gray-500"></i>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">รูปแบบส่วนลด</label>
                            <select name="discount_type" x-model="discountType"
                                class="block w-full bg-gray-900 border-gray-600 rounded-lg py-2.5 px-3 text-white focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">-- เลือกรูปแบบ --</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $discountTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <option value="<?php echo e($key); ?>"
                                        <?php echo e(old('discount_type', $promotion->discount_type ?? '') == $key ? 'selected' : ''); ?>>
                                        <?php echo e($key === 'fixed' ? 'ลดเป็นจำนวนเงิน (บาท)' : 'ลดเป็นเปอร์เซ็นต์ (%)'); ?>

                                    </option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">มูลค่าที่ลด <span
                                    class="text-red-400">*</span></label>
                            <div class="relative">
                                <input type="number" name="discount_value" step="0.01" min="0"
                                    placeholder="0"
                                    class="block w-full bg-gray-900 border-gray-600 rounded-lg py-2.5 px-3 text-white focus:ring-emerald-500 focus:border-emerald-500 font-bold text-lg text-right pr-10"
                                    value="<?php echo e(old('discount_value', $promotion->discount_value ?? '')); ?>"
                                    :required="promoType === 'auto' || promoType === 'code'" />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-400 font-bold"
                                        x-text="discountType === 'percentage' ? '%' : '฿'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div x-show="isBxGy" x-transition:enter="transition ease-out duration-300"
                class="bg-gray-800 rounded-xl border border-gray-700 shadow-xl overflow-visible">

                <div class="p-4 border-b border-gray-700 bg-gray-800/50 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-pink-500/20 flex items-center justify-center text-pink-400">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <h3 class="font-bold text-white">ตั้งค่าเงื่อนไขของแถม</h3>
                    </div>

                    
                    <div class="bg-gray-900 p-1 rounded-lg border border-gray-600 inline-flex">
                        <label class="cursor-pointer px-3 py-1.5 rounded-md text-xs font-bold transition-all"
                            :class="conditionType === 'any' ? 'bg-gray-700 text-white shadow' :
                                'text-gray-500 hover:text-gray-300'">
                            <input type="radio" name="condition_type" value="any" x-model="conditionType"
                                class="hidden">
                            อย่างใดอย่างหนึ่ง (OR)
                        </label>
                        <label class="cursor-pointer px-3 py-1.5 rounded-md text-xs font-bold transition-all"
                            :class="conditionType === 'all' ? 'bg-pink-600 text-white shadow' :
                                'text-gray-500 hover:text-gray-300'">
                            <input type="radio" name="condition_type" value="all" x-model="conditionType"
                                class="hidden">
                            ครบทุกข้อ (AND)
                        </label>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex flex-col gap-6">

                        
                        <div class="relative border border-dashed border-gray-600 rounded-xl p-5 bg-gray-900/30">
                            <div
                                class="absolute -top-3 left-4 px-2 bg-gray-800 text-xs font-bold text-emerald-400 border border-gray-600 rounded uppercase">
                                <i class="fas fa-shopping-cart mr-1"></i> เมื่อลูกค้าซื้อ (Buy)
                            </div>

                            <div class="space-y-4 pt-2">
                                <template x-for="(item, index) in buys" :key="item.id">
                                    
                                    <div
                                        class="flex flex-col md:flex-row gap-3 items-start md:items-center bg-gray-800 p-3 rounded-lg border border-gray-700 relative group">
                                        
                                        <button type="button" x-show="buys.length > 1"
                                            @click="removeBuyItem(index, item.id)"
                                            class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 rounded-full text-white text-[10px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600 z-10 cursor-pointer">
                                            <i class="fas fa-times"></i>
                                        </button>

                                        
                                        <div class="flex-grow w-full min-w-0">
                                            <label
                                                class="text-[10px] uppercase text-gray-500 font-bold mb-1 block">เลือกสินค้า</label>
                                            <select :id="'buy-products-select-' + item.id"
                                                :name="`buy_items[${index}][product_id][]`" multiple
                                                class="buy-products-select w-full" x-bind:disabled="isDiscountCode">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                    <option value="<?php echo e($p->pd_sp_id); ?>"
                                                        :selected="Array.isArray(item.product_id) ? item.product_id.map(String)
                                                            .includes('<?php echo e($p->pd_sp_id); ?>') : String(item
                                                            .product_id) === '<?php echo e($p->pd_sp_id); ?>'">
                                                        <?php echo e($p->pd_sp_name); ?>

                                                    </option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="w-full md:w-32 flex-shrink-0">
                                            <label
                                                class="text-[10px] uppercase text-gray-500 font-bold mb-1 block">จำนวนชิ้น</label>
                                            <div class="flex items-center bg-gray-900 rounded border border-gray-600">
                                                <button type="button"
                                                    class="px-3 py-1.5 text-gray-400 hover:text-white hover:bg-gray-700 rounded-l transition-colors"
                                                    @click="item.quantity = Math.max(1, parseInt(item.quantity)-1)">-</button>
                                                <input type="number" :name="`buy_items[${index}][quantity]`"
                                                    x-model="item.quantity" min="1"
                                                    class="w-full bg-transparent text-center border-none p-0 text-white font-bold focus:ring-0 appearance-none">
                                                <button type="button"
                                                    class="px-3 py-1.5 text-gray-400 hover:text-white hover:bg-gray-700 rounded-r transition-colors"
                                                    @click="item.quantity = parseInt(item.quantity)+1">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <button type="button" @click="addBuyItem()"
                                    class="w-full py-2 border border-dashed border-gray-600 rounded-lg text-sm text-gray-400 hover:text-emerald-400 hover:border-emerald-500 hover:bg-emerald-500/5 transition-all">
                                    <i class="fas fa-plus mr-1"></i> เพิ่มเงื่อนไขสินค้า
                                </button>
                            </div>
                        </div>

                        
                        <div class="flex justify-center -my-3 z-10">
                            <div
                                class="bg-gray-700 text-gray-300 rounded-full w-8 h-8 flex items-center justify-center border border-gray-600 shadow-lg">
                                <i class="fas fa-arrow-down text-sm"></i>
                            </div>
                        </div>

                        
                        <div class="relative border border-dashed border-pink-500/30 rounded-xl p-5 bg-pink-900/5">
                            <div
                                class="absolute -top-3 left-4 px-2 bg-gray-800 text-xs font-bold text-pink-400 border border-pink-500/30 rounded uppercase">
                                <i class="fas fa-gift mr-1"></i> ลูกค้าจะได้รับ (Get)
                            </div>

                            <div class="space-y-4 pt-2">
                                <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
                                    <label
                                        class="text-xs uppercase text-gray-500 font-bold mb-2 block">รายการของแถมที่เลือกได้
                                        (Pool)</label>
                                    <div class="w-full min-w-0">
                                        <select id="giftable-products-select" name="giftable_product_ids[]" multiple
                                            x-bind:disabled="isDiscountCode">
                                            <?php
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
                                            ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                <option value="<?php echo e($p->pd_sp_id); ?>"
                                                    <?php echo e($selectedGiftIds->contains((string) $p->pd_sp_id) ? 'selected' : ''); ?>>
                                                    <?php echo e($p->pd_sp_name); ?>

                                                </option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </select>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-2">*
                                        ลูกค้าจะสามารถเลือกของแถมได้จากรายการเหล่านี้</p>
                                </div>

                                <template x-for="(item, index) in gets" :key="item.id">
                                    <div
                                        class="flex items-center justify-between bg-gray-800 p-3 rounded-lg border border-gray-700">
                                        <span class="text-sm text-gray-300 font-medium">จำนวนชิ้นที่แถมฟรี</span>
                                        <div class="flex items-center bg-gray-900 rounded border border-gray-600">
                                            <input type="hidden" :name="`get_items[${index}][product_id]`"
                                                :value="item.product_id">
                                            <button type="button"
                                                class="px-3 py-1.5 text-gray-400 hover:text-white hover:bg-gray-700 rounded-l"
                                                @click="item.quantity = Math.max(1, parseInt(item.quantity)-1)">-</button>
                                            <input type="number" :name="`get_items[${index}][quantity]`"
                                                x-model="item.quantity" min="1"
                                                class="w-16 bg-transparent text-center border-none p-0 text-pink-400 font-bold text-lg focus:ring-0 appearance-none">
                                            <button type="button"
                                                class="px-3 py-1.5 text-gray-400 hover:text-white hover:bg-gray-700 rounded-r"
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

        
        <div class="xl:col-span-1 space-y-6">
            <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-xl p-6 sticky top-6">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-6 bg-emerald-500 rounded-full"></span> ข้อมูลทั่วไป
                </h3>

                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-1">ชื่อแคมเปญ <span
                            class="text-red-400">*</span></label>
                    <input type="text" name="name"
                        class="block w-full bg-gray-900 border-gray-600 rounded-lg py-2.5 px-3 text-white focus:ring-emerald-500 focus:border-emerald-500"
                        value="<?php echo e(old('name', $promotion->name ?? '')); ?>" required placeholder="ตั้งชื่อให้จำง่าย" />
                </div>

                
                <div class="mb-6">
                    <label
                        class="flex items-center justify-between p-3 bg-gray-900 rounded-lg border border-gray-600 cursor-pointer hover:border-gray-500 transition-colors">
                        <span class="text-sm font-medium text-gray-300">เปิดใช้งานทันที</span>
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                                <?php echo e(old('is_active', $promotion->is_active ?? true) ? 'checked' : ''); ?>>
                            <div
                                class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500">
                            </div>
                        </div>
                    </label>
                </div>

                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-1">รายละเอียด (ภายใน)</label>
                    <textarea name="description" rows="3"
                        class="block w-full bg-gray-900 border-gray-600 rounded-lg py-2.5 px-3 text-white focus:ring-emerald-500 focus:border-emerald-500 text-sm"
                        placeholder="โน้ตสำหรับแอดมิน..."><?php echo e(old('description', $promotion->description ?? '')); ?></textarea>
                </div>

                <hr class="border-gray-700 my-4">

                
                <div class="space-y-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">วันเริ่มแคมเปญ</label>
                        <input type="datetime-local" name="start_date"
                            class="block w-full bg-gray-900 border-gray-600 rounded-lg py-2 px-3 text-white text-sm focus:ring-emerald-500 focus:border-emerald-500"
                            value="<?php echo e(old('start_date', isset($promotion->start_date) ? \Carbon\Carbon::parse($promotion->start_date)->format('Y-m-d\TH:i') : '')); ?>" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">วันสิ้นสุด</label>
                        <input type="datetime-local" name="end_date"
                            class="block w-full bg-gray-900 border-gray-600 rounded-lg py-2 px-3 text-white text-sm focus:ring-emerald-500 focus:border-emerald-500"
                            value="<?php echo e(old('end_date', isset($promotion->end_date) ? \Carbon\Carbon::parse($promotion->end_date)->format('Y-m-d\TH:i') : '')); ?>" />
                    </div>
                </div>

                <hr class="border-gray-700 my-4">

                
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">จำกัดสิทธิ์ (ครั้ง)</label>
                        <input type="number" name="usage_limit" min="1" placeholder="ไม่จำกัด"
                            class="block w-full bg-gray-900 border-gray-600 rounded-lg py-2 px-3 text-white text-sm focus:ring-emerald-500 focus:border-emerald-500"
                            value="<?php echo e(old('usage_limit', $promotion->usage_limit ?? '')); ?>" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">ยอดขั้นต่ำ (บาท)</label>
                        <input type="number" name="min_order_value" min="0" step="0.01" placeholder="0"
                            class="block w-full bg-gray-900 border-gray-600 rounded-lg py-2 px-3 text-white text-sm focus:ring-emerald-500 focus:border-emerald-500"
                            value="<?php echo e(old('min_order_value', $promotion->min_order_value ?? '')); ?>" />
                    </div>
                </div>

                
                <div class="flex flex-col gap-3">
                    <button type="submit"
                        class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg shadow-lg shadow-emerald-900/50 transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i> บันทึกข้อมูล
                    </button>
                    <a href="<?php echo e(route('admin.promotions.index')); ?>"
                        class="w-full py-2.5 px-4 bg-transparent border border-gray-600 text-gray-400 hover:text-white hover:bg-gray-700 font-medium rounded-lg text-center transition-colors">
                        ยกเลิก
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>


<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('promotionForm', (initialIsDiscountCode, initialDiscountType) => ({
            // สร้าง Unique ID ให้แต่ละแถวตอนโหลดครั้งแรก
            buys: <?php echo json_encode($buyData, 15, 512) ?>.map((item, index) => ({
                ...item,
                id: Date.now() + Math.random() + index
            })),
            gets: <?php echo json_encode($getData, 15, 512) ?>.map((item, index) => ({
                ...item,
                id: Date.now() + Math.random() + index
            })),

            discountType: initialDiscountType,
            conditionType: '<?php echo e(old('condition_type', $promotion->condition_type ?? 'any')); ?>',
            promoType: '<?php echo e(old('promo_type_selector', isset($promotion) ? ($promotion->code ? 'code' : ($promotion->rules->count() > 0 ? 'bxgy' : 'auto')) : 'auto')); ?>',
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

                // Gift Pool (Static)
                const giftEl = document.getElementById('giftable-products-select');
                if (giftEl && !this.tomSelects['gift-pool']) {
                    this.tomSelects['gift-pool'] = new TomSelect(giftEl, {
                        plugins: ['remove_button', 'clear_button'],
                        create: false,
                        placeholder: 'เลือกสินค้า...',
                        valueField: 'value',
                        labelField: 'text',
                        searchField: 'text',
                        dropdownParent: document.body,
                        closeAfterSelect: true,
                        onItemAdd: function() {
                            this.close();
                        }, // บังคับปิดทันทีที่เลือก
                        render: {
                            item: (data, escape) =>
                                `<div class="bg-pink-900/30 text-pink-300 border border-pink-500/30 px-2 py-0.5 rounded mr-1 mb-1 text-xs">${escape(data.text)}</div>`
                        }
                    });
                }

                // Buy Items (Dynamic)
                document.querySelectorAll('.buy-products-select').forEach((el) => {
                    if (!el.tomselect && !this.tomSelects[el.id]) {
                        this.tomSelects[el.id] = new TomSelect(el, {
                            plugins: ['remove_button'],
                            create: false,
                            placeholder: 'เลือกสินค้า...',
                            dropdownParent: document.body,
                            closeAfterSelect: true,
                            onItemAdd: function() {
                                this.close();
                            }, // บังคับปิดทันทีที่เลือก
                            render: {
                                item: (data, escape) =>
                                    `<div class="bg-gray-700 text-emerald-400 border border-gray-600 px-2 py-0.5 rounded mr-1 mb-1 text-xs">${escape(data.text)}</div>`
                            }
                        });
                    }
                });
            },

            addBuyItem() {
                this.buys.push({
                    id: Date.now() + Math.random(),
                    product_id: [],
                    quantity: 1
                });
            },

            removeBuyItem(index, id) {
                if (this.buys.length > 1) {
                    const domId = 'buy-products-select-' + id;
                    if (this.tomSelects[domId]) {
                        this.tomSelects[domId].destroy();
                        delete this.tomSelects[domId];
                    } else {
                        const el = document.getElementById(domId);
                        if (el && el.tomselect) el.tomselect.destroy();
                    }
                    this.buys.splice(index, 1);
                }
            },
        }))
    });
</script>
<?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/promotions/_form.blade.php ENDPATH**/ ?>