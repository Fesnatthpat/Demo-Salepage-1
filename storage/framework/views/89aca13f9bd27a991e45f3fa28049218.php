<?php
    $buyData = old('buy_items', $buy_items ?? [['product_id' => '', 'quantity' => 1]]);
    if (empty($buyData)) {
        $buyData = [['product_id' => '', 'quantity' => 1]];
    }

    $getData = old('get_items', $get_items ?? [['product_id' => '', 'quantity' => 1]]);
    if (empty($getData)) {
        $getData = [['product_id' => '', 'quantity' => 1]];
    }
?>


<style>
    .ts-control {
        background-color: #374151 !important;
        /* gray-700 */
        border-color: #4b5563 !important;
        /* gray-600 */
        color: #f3f4f6 !important;
        /* gray-100 */
        border-radius: 0.5rem;
    }

    .ts-control input {
        color: #f3f4f6 !important;
    }

    .ts-dropdown {
        background-color: #374151 !important;
        border-color: #4b5563 !important;
        color: #f3f4f6 !important;
    }

    .ts-dropdown .option:hover,
    .ts-dropdown .active {
        background-color: #4b5563 !important;
        /* gray-600 */
        color: #fff !important;
    }

    .ts-control .item {
        background-color: #1f2937 !important;
        /* gray-800 */
        color: #fff !important;
        border: 1px solid #4b5563 !important;
    }

    .animate-fade-in-down {
        animation: fadeInDown 0.3s ease-out;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div x-data="promotionForm(
    <?php echo e(old('is_discount_code', isset($promotion) && $promotion->code ? 'true' : 'false')); ?>,
    '<?php echo e(old('discount_type', $promotion->discount_type ?? '')); ?>'
)">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="alert alert-error bg-red-900/50 border border-red-800 text-red-200 shadow-sm mb-8">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <h3 class="font-bold">ข้อมูลไม่ถูกต้อง</h3>
                <ul class="text-xs mt-1 list-disc list-inside opacity-80">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <li><?php echo e($error); ?></li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        
        <div class="lg:col-span-4 space-y-6">
            <div class="card bg-gray-800 shadow-lg border border-gray-700">
                <div class="card-body p-6 gap-5">
                    <div class="flex items-center gap-2 text-gray-100 pb-2 border-b border-gray-700">
                        <i class="fas fa-info-circle text-emerald-500"></i>
                        <h2 class="font-bold text-lg">ข้อมูลพื้นฐาน</h2>
                    </div>

                    <div class="form-control w-full">
                        <label class="label pt-0"><span class="label-text font-semibold text-gray-300">ชื่อแคมเปญ <span
                                    class="text-red-400">*</span></span></label>
                        <input type="text" name="name"
                            class="input input-bordered w-full bg-gray-700 border-gray-600 text-gray-100 focus:border-emerald-500"
                            value="<?php echo e(old('name', $promotion->name ?? '')); ?>" required />
                    </div>

                    
                    <div class="form-control w-full">
                        <label class="label"><span
                                class="label-text font-semibold text-gray-300">รูปแบบเงื่อนไขการซื้อ</span></label>
                        <div class="flex flex-col gap-2">
                            <label
                                class="flex items-center gap-3 p-3 border border-gray-600 rounded-lg cursor-pointer hover:bg-gray-700 transition-colors <?php echo e(old('condition_type', $promotion->condition_type ?? 'any') == 'any' ? 'border-emerald-500 bg-emerald-900/20' : 'bg-gray-700'); ?>">
                                <input type="radio" name="condition_type" value="any"
                                    class="radio radio-success radio-sm"
                                    <?php echo e(old('condition_type', $promotion->condition_type ?? 'any') == 'any' ? 'checked' : ''); ?> />
                                <div>
                                    <span class="font-bold text-sm text-gray-200">อย่างใดอย่างหนึ่ง (OR)</span>
                                    <p class="text-xs text-gray-400">ซื้อสินค้า A ครบ หรือ สินค้า B ครบ ก็ได้รับสิทธิ์
                                    </p>
                                </div>
                            </label>

                            <label
                                class="flex items-center gap-3 p-3 border border-gray-600 rounded-lg cursor-pointer hover:bg-gray-700 transition-colors <?php echo e(old('condition_type', $promotion->condition_type ?? 'any') == 'all' ? 'border-emerald-500 bg-emerald-900/20' : 'bg-gray-700'); ?>">
                                <input type="radio" name="condition_type" value="all"
                                    class="radio radio-success radio-sm"
                                    <?php echo e(old('condition_type', $promotion->condition_type ?? 'any') == 'all' ? 'checked' : ''); ?> />
                                <div>
                                    <span class="font-bold text-sm text-gray-200">ต้องครบทุกข้อ (AND)</span>
                                    <p class="text-xs text-gray-400">ต้องซื้อทั้งสินค้า A และ สินค้า B ให้ครบตามจำนวน
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span
                                class="label-text font-semibold text-gray-300">รายละเอียด</span></label>
                        <textarea name="description"
                            class="textarea textarea-bordered h-24 resize-none bg-gray-700 border-gray-600 text-gray-100 focus:border-emerald-500"><?php echo e(old('description', $promotion->description ?? '')); ?></textarea>
                    </div>

                    <div class="bg-gray-900/50 p-4 rounded-lg border border-gray-700 space-y-3">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">ระยะเวลา</span>
                        <div class="form-control w-full">
                            <label class="label py-0"><span
                                    class="label-text text-xs text-gray-400">วันเริ่มต้น</span></label>
                            <input type="datetime-local" name="start_date"
                                class="input input-bordered input-sm w-full bg-gray-700 border-gray-600 text-gray-100"
                                value="<?php echo e(old('start_date', isset($promotion->start_date) ? \Carbon\Carbon::parse($promotion->start_date)->format('Y-m-d\TH:i') : '')); ?>" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label py-0"><span
                                    class="label-text text-xs text-gray-400">วันสิ้นสุด</span></label>
                            <input type="datetime-local" name="end_date"
                                class="input input-bordered input-sm w-full bg-gray-700 border-gray-600 text-gray-100"
                                value="<?php echo e(old('end_date', isset($promotion->end_date) ? \Carbon\Carbon::parse($promotion->end_date)->format('Y-m-d\TH:i') : '')); ?>" />
                        </div>
                    </div>

                    <div class="form-control mt-2">
                        <label class="label cursor-pointer justify-between">
                            <span class="label-text font-semibold text-gray-300">เปิดใช้งานโปรโมชั่น</span>
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="toggle toggle-success"
                                <?php echo e(old('is_active', $promotion->is_active ?? true) ? 'checked' : ''); ?> />
                        </label>
                    </div>
                    
                    
                    <div class="form-control mt-4">
                        <label class="label cursor-pointer justify-between">
                            <span class="label-text font-semibold text-gray-300">ใช้เป็นรหัสส่วนลด</span>
                            <input type="hidden" name="is_discount_code" value="0"> 
                            <input type="checkbox" name="is_discount_code" x-model="isDiscountCode" class="toggle toggle-info" value="1"
                                <?php echo e(old('is_discount_code', isset($promotion) && $promotion->code ? '1' : '0') == '1' ? 'checked' : ''); ?> />
                        </label>
                        <p class="text-xs text-gray-400 mt-1">หากเปิดใช้งาน, โปรโมชั่นนี้จะใช้เป็นรหัสส่วนลดแทนเงื่อนไขซื้อ X แถม Y</p>
                    </div>

                    
                    <template x-if="isDiscountCode">
                        <div class="space-y-4 bg-gray-900/50 p-4 rounded-lg border border-gray-700 animate-fade-in-down">
                            <h3 class="font-bold text-lg text-gray-100">ตั้งค่ารหัสส่วนลด</h3>
                            <div class="form-control w-full">
                                <label class="label pt-0"><span class="label-text font-semibold text-gray-300">รหัสส่วนลด <span class="text-red-400">*</span></span></label>
                                <input type="text" name="code"
                                    class="input input-bordered w-full bg-gray-700 border-gray-600 text-gray-100 focus:border-emerald-500"
                                    value="<?php echo e(old('code', $promotion->code ?? '')); ?>" x-bind:required="isDiscountCode" />
                                <p class="text-xs text-gray-400 mt-1">เช่น NEWUSER, SAVE100 (ใช้ตัวอักษรและตัวเลขเท่านั้น)</p>
                            </div>
                            <div class="form-control w-full">
                                <label class="label pt-0"><span class="label-text font-semibold text-gray-300">ประเภทส่วนลด <span class="text-red-400">*</span></span></label>
                                <select name="discount_type" x-model="discountType" x-bind:required="isDiscountCode"
                                    class="select select-bordered w-full bg-gray-700 border-gray-600 text-gray-100 focus:border-emerald-500">
                                    <option value="">-- เลือกประเภท --</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $discountTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <option value="<?php echo e($key); ?>" <?php echo e(old('discount_type', $promotion->discount_type ?? '') == $key ? 'selected' : ''); ?>><?php echo e($value); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </select>
                            </div>
                            <div class="form-control w-full">
                                <label class="label pt-0"><span class="label-text font-semibold text-gray-300">มูลค่าส่วนลด <span class="text-red-400">*</span></span></label>
                                <input type="number" name="discount_value"
                                    class="input input-bordered w-full bg-gray-700 border-gray-600 text-gray-100 focus:border-emerald-500"
                                    value="<?php echo e(old('discount_value', $promotion->discount_value ?? '')); ?>" x-bind:required="isDiscountCode" step="0.01" min="0" />
                                <p class="text-xs text-gray-400 mt-1" x-text="discountType === 'fixed' ? 'เช่น 100 สำหรับลด 100 บาท' : 'เช่น 10 สำหรับลด 10%'"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="hidden lg:flex flex-col gap-3">
                <button type="submit"
                    class="btn btn-primary bg-emerald-600 hover:bg-emerald-700 border-none w-full shadow-lg font-bold text-lg text-white">
                    <i class="fas fa-save"></i> บันทึกข้อมูล
                </button>
                <a href="<?php echo e(route('admin.promotions.index')); ?>"
                    class="btn btn-ghost w-full text-gray-400 hover:text-white hover:bg-gray-700">ยกเลิก</a>
            </div>
        </div>

        
        <div class="lg:col-span-8 space-y-6" x-show="!isDiscountCode">
            <div class="card bg-gray-800 shadow-lg border border-gray-700 overflow-visible">
                <div class="card-body p-0">
                    <div class="p-6 border-b border-gray-700 flex items-center gap-2">
                        <i class="fas fa-cogs text-pink-500"></i>
                        <h2 class="font-bold text-lg text-gray-100">เครื่องมือสร้างเงื่อนไข</h2>
                    </div>

                    <div class="p-6 bg-gray-900/50">
                        <div class="flex flex-col xl:flex-row gap-4 items-stretch relative">

                            
                            <div
                                class="flex-1 w-full card bg-gray-800 border border-emerald-500/30 shadow-sm relative overflow-visible group hover:shadow-md transition-shadow">
                                <div class="absolute top-0 left-0 w-full h-1 bg-emerald-500 rounded-t-lg"></div>
                                <div class="card-body p-4">
                                    <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-700">
                                        <div
                                            class="badge badge-outline border-emerald-500 text-emerald-400 font-bold gap-1">
                                            <i class="fas fa-shopping-cart text-[10px]"></i> เงื่อนไข (ซื้อ)
                                        </div>
                                        <button type="button" @click="addItem('buy')"
                                            class="btn btn-xs btn-circle bg-emerald-600 border-none text-white shadow-md hover:scale-110 transition"
                                            title="เพิ่มเงื่อนไข">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>

                                    <div class="space-y-3">
                                        <template x-for="(item, index) in buys" :key="index">
                                            <div
                                                class="p-3 bg-gray-700 rounded-lg border border-gray-600 relative group animate-fade-in-down">
                                                <button type="button" x-show="buys.length > 1"
                                                    @click="removeItem('buy', index)"
                                                    class="absolute -top-2 -right-2 btn btn-xs btn-circle btn-error bg-red-500 border-none text-white shadow-sm z-20 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <div class="grid grid-cols-12 gap-2 items-end">
                                                    <div class="col-span-8">
                                                        <label class="label p-0 mb-1" x-show="index === 0"><span
                                                                class="label-text text-[10px] uppercase font-bold text-gray-400">สินค้า</span></label>
                                                        <select :name="`buy_items[${index}][product_id]`"
                                                            x-model="item.product_id"
                                                            class="select select-bordered select-sm w-full bg-gray-800 border-gray-500 text-gray-200"
                                                            required
                                                            x-bind:disabled="isDiscountCode">
                                                            <option value="" disabled>-- เลือกสินค้า --</option>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                                <option value="<?php echo e($p->pd_sp_id); ?>">
                                                                    <?php echo e($p->pd_sp_name); ?></option>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-span-4">
                                                        <label class="label p-0 mb-1" x-show="index === 0"><span
                                                                class="label-text text-[10px] uppercase font-bold text-gray-400">จำนวน</span></label>
                                                        <input type="number" :name="`buy_items[${index}][quantity]`"
                                                            x-model="item.quantity" min="1"
                                                            class="input input-bordered input-sm w-full text-center font-bold px-0 bg-gray-800 border-gray-500 text-emerald-400"
                                                            required
                                                            x-bind:disabled="isDiscountCode" />
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="flex items-center justify-center py-2 xl:py-0">
                                <div class="bg-gray-700 p-2 rounded-full shadow border border-gray-600 z-10">
                                    <i class="fas fa-arrow-down xl:fa-arrow-right text-2xl text-gray-400"></i>
                                </div>
                            </div>

                            
                            <div
                                class="flex-1 w-full card bg-gray-800 border border-pink-500/30 shadow-sm relative overflow-visible group hover:shadow-md transition-shadow">
                                <div class="absolute top-0 left-0 w-full h-1 bg-pink-500 rounded-t-lg"></div>
                                <div class="card-body p-4">
                                    <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-700">
                                        <div class="badge badge-outline border-pink-500 text-pink-400 font-bold gap-1">
                                            <i class="fas fa-gift text-[10px]"></i> ผลลัพธ์ (แถมฟรี)
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <template x-for="(item, index) in gets" :key="index">
                                            <div class="p-3 bg-gray-700 rounded-lg border border-gray-600">
                                                <input type="hidden" :name="`get_items[${index}][product_id]`"
                                                    :value="item.product_id">
                                                <div class="form-control w-full text-center">
                                                    <label class="label p-0 mb-1 justify-center"><span
                                                            class="label-text text-[10px] uppercase font-bold text-gray-400">จำนวนที่ได้รับ</span></label>
                                                    <div class="join w-full justify-center">
                                                        <button type="button"
                                                            class="btn btn-sm join-item bg-gray-600 border-gray-500 text-gray-200 hover:bg-gray-500"
                                                            @click="item.quantity = Math.max(1, parseInt(item.quantity)-1)">-</button>
                                                        <input type="number" :name="`get_items[${index}][quantity]`"
                                                            x-model="item.quantity" min="1"
                                                            class="input input-bordered input-sm w-20 join-item text-center font-bold text-lg text-pink-400 bg-gray-800 border-gray-500"
                                                            required
                                                            x-bind:disabled="isDiscountCode" />
                                                        <button type="button"
                                                            class="btn btn-sm join-item bg-gray-600 border-gray-500 text-gray-200 hover:bg-gray-500"
                                                            @click="item.quantity = parseInt(item.quantity)+1"
                                                            x-bind:disabled="isDiscountCode">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <div class="divider text-xs text-gray-500 my-3">เลือกจาก</div>

                                    
                                    <div class="form-control w-full">
                                        <label class="label pt-0 pb-1"><span
                                                class="label-text text-[10px] font-bold text-gray-400 uppercase">สินค้าของแถม
                                                (Pool)</span></label>
                                        <select id="giftable-products-select" name="giftable_product_ids[]" multiple
                                            placeholder="ค้นหาของแถม..." autocomplete="off"
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
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            
            <div class="lg:hidden flex justify-end items-center gap-3 pt-4 border-t border-gray-700 mt-4">
                <a href="<?php echo e(route('admin.promotions.index')); ?>"
                    class="btn btn-ghost text-gray-400 hover:text-white">ยกเลิก</a>
                <button type="submit"
                    class="btn btn-primary bg-emerald-600 hover:bg-emerald-700 text-white px-8 shadow-lg border-none">บันทึกข้อมูล</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('promotionForm', (initialIsDiscountCode, initialDiscountType) => ({
            buys: <?php echo json_encode($buyData, 15, 512) ?>,
            gets: <?php echo json_encode($getData, 15, 512) ?>,
            isDiscountCode: initialIsDiscountCode,
            discountType: initialDiscountType,
            init() {
                if (typeof TomSelect !== 'undefined') {
                    new TomSelect('#giftable-products-select', {
                        plugins: ['remove_button', 'clear_button'],
                        create: false,
                        maxOptions: null,
                        render: {
                            item: function(data, escape) {
                                return '<div class="item badge badge-secondary badge-outline m-1 pl-2 pr-1 py-3 font-medium bg-gray-700 text-gray-200 border-gray-500">' +
                                    escape(data.text) + '</div>';
                            }
                        }
                    });
                }
            },
            addItem(type) {
                if (type === 'buy') this.buys.push({
                    product_id: '',
                    quantity: 1
                });
                if (type === 'get') this.gets.push({
                    product_id: '',
                    quantity: 1
                });
            },
            removeItem(type, index) {
                if (type === 'buy' && this.buys.length > 1) this.buys.splice(index, 1);
                if (type === 'get' && this.gets.length > 1) this.gets.splice(index, 1);
            },
        }))
    });
</script>
<?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/promotions/_form.blade.php ENDPATH**/ ?>