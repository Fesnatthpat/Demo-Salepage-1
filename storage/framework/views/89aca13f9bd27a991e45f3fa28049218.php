<?php
    $buyData = old('buy_items', $buy_items ?? [['product_id' => '', 'quantity' => 1]]);
    if (empty($buyData)) $buyData = [['product_id' => '', 'quantity' => 1]];

    $getData = old('get_items', $get_items ?? [['product_id' => '', 'quantity' => 1]]);
    if (empty($getData)) $getData = [['product_id' => '', 'quantity' => 1]];
?>

<div x-data="promotionForm">

    <?php if($errors->any()): ?>
        <div class="alert alert-error bg-red-50 border border-red-200 text-red-700 shadow-sm mb-8">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <h3 class="font-bold">ข้อมูลไม่ถูกต้อง</h3>
                <ul class="text-xs mt-1 list-disc list-inside opacity-80">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        
        <div class="lg:col-span-4 space-y-6">
            <div class="card bg-white shadow-sm border border-gray-200">
                <div class="card-body p-6 gap-5">
                    <div class="flex items-center gap-2 text-gray-800 pb-2 border-b border-gray-100">
                        <i class="fas fa-info-circle text-primary"></i>
                        <h2 class="font-bold text-lg">ข้อมูลพื้นฐาน</h2>
                    </div>

                    <div class="form-control w-full">
                        <label class="label pt-0"><span class="label-text font-semibold">ชื่อแคมเปญ <span class="text-error">*</span></span></label>
                        <input type="text" name="name" class="input input-bordered w-full" value="<?php echo e(old('name', $promotion->name ?? '')); ?>" required />
                    </div>

                    
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-semibold">รูปแบบเงื่อนไขการซื้อ</span></label>
                        <div class="flex flex-col gap-2">
                            <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors <?php echo e(old('condition_type', $promotion->condition_type ?? 'any') == 'any' ? 'border-primary bg-primary/5' : ''); ?>">
                                <input type="radio" name="condition_type" value="any" class="radio radio-primary radio-sm" <?php echo e(old('condition_type', $promotion->condition_type ?? 'any') == 'any' ? 'checked' : ''); ?> />
                                <div>
                                    <span class="font-bold text-sm text-gray-800">อย่างใดอย่างหนึ่ง (OR)</span>
                                    <p class="text-xs text-gray-500">ซื้อสินค้า A ครบ หรือ สินค้า B ครบ ก็ได้รับสิทธิ์</p>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors <?php echo e(old('condition_type', $promotion->condition_type ?? 'any') == 'all' ? 'border-primary bg-primary/5' : ''); ?>">
                                <input type="radio" name="condition_type" value="all" class="radio radio-primary radio-sm" <?php echo e(old('condition_type', $promotion->condition_type ?? 'any') == 'all' ? 'checked' : ''); ?> />
                                <div>
                                    <span class="font-bold text-sm text-gray-800">ต้องครบทุกข้อ (AND)</span>
                                    <p class="text-xs text-gray-500">ต้องซื้อทั้งสินค้า A และ สินค้า B ให้ครบตามจำนวน</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-semibold">รายละเอียด</span></label>
                        <textarea name="description" class="textarea textarea-bordered h-24 resize-none"><?php echo e(old('description', $promotion->description ?? '')); ?></textarea>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 space-y-3">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">ระยะเวลา</span>
                        <div class="form-control w-full">
                            <label class="label py-0"><span class="label-text text-xs text-gray-500">วันเริ่มต้น</span></label>
                            <input type="datetime-local" name="start_date" class="input input-bordered input-sm w-full"
                                value="<?php echo e(old('start_date', isset($promotion->start_date) ? \Carbon\Carbon::parse($promotion->start_date)->format('Y-m-d\TH:i') : '')); ?>" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label py-0"><span class="label-text text-xs text-gray-500">วันสิ้นสุด</span></label>
                            <input type="datetime-local" name="end_date" class="input input-bordered input-sm w-full"
                                value="<?php echo e(old('end_date', isset($promotion->end_date) ? \Carbon\Carbon::parse($promotion->end_date)->format('Y-m-d\TH:i') : '')); ?>" />
                        </div>
                    </div>

                    <div class="form-control mt-2">
                        <label class="label cursor-pointer justify-between">
                            <span class="label-text font-semibold">เปิดใช้งานโปรโมชั่น</span>
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="toggle toggle-success"
                                <?php echo e(old('is_active', $promotion->is_active ?? true) ? 'checked' : ''); ?> />
                        </label>
                    </div>
                </div>
            </div>

            <div class="hidden lg:flex flex-col gap-3">
                <button type="submit" class="btn btn-primary w-full shadow-lg font-bold text-lg">
                    <i class="fas fa-save"></i> บันทึกข้อมูล
                </button>
                <a href="<?php echo e(route('admin.promotions.index')); ?>" class="btn btn-ghost w-full text-gray-500">ยกเลิก</a>
            </div>
        </div>

        
        <div class="lg:col-span-8 space-y-6">
            <div class="card bg-white shadow-sm border border-gray-200 overflow-visible">
                <div class="card-body p-0">
                    <div class="p-6 border-b border-gray-100 flex items-center gap-2">
                        <i class="fas fa-cogs text-secondary"></i>
                        <h2 class="font-bold text-lg text-gray-800">เครื่องมือสร้างเงื่อนไข</h2>
                    </div>

                    <div class="p-6 bg-gray-50/50">
                        <div class="flex flex-col xl:flex-row gap-4 items-stretch relative">

                            
                            <div class="flex-1 w-full card bg-white border border-primary/20 shadow-sm relative overflow-visible group hover:shadow-md transition-shadow">
                                <div class="absolute top-0 left-0 w-full h-1 bg-primary rounded-t-lg"></div>
                                <div class="card-body p-4">
                                    <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-100">
                                        <div class="badge badge-primary badge-outline font-bold gap-1">
                                            <i class="fas fa-shopping-cart text-[10px]"></i> เงื่อนไข (ซื้อ)
                                        </div>
                                        <button type="button" @click="addItem('buy')" class="btn btn-xs btn-circle btn-primary text-white shadow-md hover:scale-110 transition" title="เพิ่มเงื่อนไข">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>

                                    <div class="space-y-3">
                                        <template x-for="(item, index) in buys" :key="index">
                                            <div class="p-3 bg-base-50 rounded-lg border border-base-200 relative group animate-fade-in-down">
                                                <button type="button" x-show="buys.length > 1" @click="removeItem('buy', index)" class="absolute -top-2 -right-2 btn btn-xs btn-circle btn-error text-white shadow-sm z-20 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <div class="grid grid-cols-12 gap-2 items-end">
                                                    <div class="col-span-8">
                                                        <label class="label p-0 mb-1" x-show="index === 0"><span class="label-text text-[10px] uppercase font-bold text-gray-400">สินค้า</span></label>
                                                        <select :name="`buy_items[${index}][product_id]`" x-model="item.product_id" class="select select-bordered select-sm w-full bg-white" required>
                                                            <option value="" disabled>-- เลือกสินค้า --</option>
                                                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($p->pd_sp_id); ?>"><?php echo e($p->pd_sp_name); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-span-4">
                                                        <label class="label p-0 mb-1" x-show="index === 0"><span class="label-text text-[10px] uppercase font-bold text-gray-400">จำนวน</span></label>
                                                        <input type="number" :name="`buy_items[${index}][quantity]`" x-model="item.quantity" min="1" class="input input-bordered input-sm w-full text-center font-bold px-0" required />
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="flex items-center justify-center py-2 xl:py-0">
                                <div class="bg-white p-2 rounded-full shadow border border-gray-100 z-10">
                                    <i class="fas fa-arrow-down xl:fa-arrow-right text-2xl text-gray-300"></i>
                                </div>
                            </div>

                            
                            <div class="flex-1 w-full card bg-white border border-secondary/20 shadow-sm relative overflow-visible group hover:shadow-md transition-shadow">
                                <div class="absolute top-0 left-0 w-full h-1 bg-secondary rounded-t-lg"></div>
                                <div class="card-body p-4">
                                    <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-100">
                                        <div class="badge badge-secondary badge-outline font-bold gap-1">
                                            <i class="fas fa-gift text-[10px]"></i> ผลลัพธ์ (แถมฟรี)
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <template x-for="(item, index) in gets" :key="index">
                                            <div class="p-3 bg-base-50 rounded-lg border border-base-200">
                                                <input type="hidden" :name="`get_items[${index}][product_id]`" :value="item.product_id">
                                                <div class="form-control w-full text-center">
                                                    <label class="label p-0 mb-1 justify-center"><span class="label-text text-[10px] uppercase font-bold text-gray-400">จำนวนที่ได้รับ</span></label>
                                                    <div class="join w-full justify-center">
                                                        <button type="button" class="btn btn-sm join-item" @click="item.quantity = Math.max(1, parseInt(item.quantity)-1)">-</button>
                                                        <input type="number" :name="`get_items[${index}][quantity]`" x-model="item.quantity" min="1" class="input input-bordered input-sm w-20 join-item text-center font-bold text-lg text-secondary" required />
                                                        <button type="button" class="btn btn-sm join-item" @click="item.quantity = parseInt(item.quantity)+1">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <div class="divider text-xs text-gray-300 my-3">เลือกจาก</div>

                                    
                                    <div class="form-control w-full">
                                        <label class="label pt-0 pb-1"><span class="label-text text-[10px] font-bold text-gray-400 uppercase">สินค้าของแถม (Pool)</span></label>
                                        <select id="giftable-products-select" name="giftable_product_ids[]" multiple placeholder="ค้นหาของแถม..." autocomplete="off">
                                            <?php
                                                $selectedGiftIds = collect(old('giftable_product_ids', isset($promotion) ? $promotion->actions->flatMap->giftableProducts->pluck('pd_sp_id') : []))->map(fn($id) => (string) $id);
                                            ?>
                                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($p->pd_sp_id); ?>" <?php echo e($selectedGiftIds->contains((string) $p->pd_sp_id) ? 'selected' : ''); ?>>
                                                    <?php echo e($p->pd_sp_name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            
            <div class="lg:hidden flex justify-end items-center gap-3 pt-4 border-t border-gray-200 mt-4">
                <a href="<?php echo e(route('admin.promotions.index')); ?>" class="btn btn-ghost text-gray-500">ยกเลิก</a>
                <button type="submit" class="btn btn-primary px-8 shadow-lg">บันทึกข้อมูล</button>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in-down { animation: fadeInDown 0.3s ease-out; }
    @keyframes fadeInDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .ts-control { border-radius: 0.5rem; padding: 0.5rem; border-color: #d1d5db; background-color: white; }
    .ts-control.focus { border-color: var(--p); box-shadow: 0 0 0 2px var(--pf); }
    .ts-dropdown { border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('promotionForm', () => ({
            buys: <?php echo json_encode($buyData, 15, 512) ?>,
            gets: <?php echo json_encode($getData, 15, 512) ?>,
            init() {
                if (typeof TomSelect !== 'undefined') {
                    new TomSelect('#giftable-products-select', {
                        plugins: ['remove_button', 'clear_button'],
                        create: false,
                        maxOptions: null,
                        render: { item: function(data, escape) { return '<div class="item badge badge-secondary badge-outline m-1 pl-2 pr-1 py-3 font-medium">' + escape(data.text) + '</div>'; } }
                    });
                }
            },
            addItem(type) {
                if (type === 'buy') this.buys.push({ product_id: '', quantity: 1 });
                if (type === 'get') this.gets.push({ product_id: '', quantity: 1 });
            },
            removeItem(type, index) {
                if (type === 'buy' && this.buys.length > 1) this.buys.splice(index, 1);
                if (type === 'get' && this.gets.length > 1) this.gets.splice(index, 1);
            },
        }))
    });
</script><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/promotions/_form.blade.php ENDPATH**/ ?>