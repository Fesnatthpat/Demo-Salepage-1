

<?php $__env->startSection('title', 'จัดการโปรโมชั่น'); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-100 tracking-tight">รายการโปรโมชั่น</h1>
                <p class="text-gray-400 mt-1">จัดการแคมเปญและเงื่อนไขการส่งเสริมการขายทั้งหมด</p>
            </div>
            <a href="<?php echo e(route('admin.promotions.create')); ?>"
                class="btn btn-primary btn-md bg-emerald-600 hover:bg-emerald-700 border-none text-white shadow-lg shadow-emerald-900/20 gap-2 font-medium transition-transform hover:scale-105">
                <i class="fas fa-plus"></i> เพิ่มโปรโมชั่นใหม่
            </a>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div role="alert"
                class="alert alert-success bg-green-900/50 border border-green-800 text-green-200 shadow-sm mb-6 flex items-center">
                <i class="fas fa-check-circle text-xl text-emerald-500"></i>
                <span class="font-medium"><?php echo e(session('success')); ?></span>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="bg-gray-800 rounded-2xl shadow-lg border border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full text-gray-300">
                    <thead
                        class="bg-gray-900/50 text-gray-400 text-xs uppercase font-bold tracking-wider border-b border-gray-700">
                        <tr>
                            <th class="py-4 pl-6 w-[15%]">รายละเอียดแคมเปญ</th>
                            <th class="py-4 text-center w-[10%]">รหัสส่วนลด</th>
                            <th class="py-4 text-center w-[10%]">ประเภทส่วนลด</th>
                            <th class="py-4 text-right w-[10%]">มูลค่าส่วนลด</th>
                            <th class="py-4 text-center w-[40%]">เงื่อนไข (ซื้อ <i
                                    class="fas fa-arrow-right text-xs mx-1"></i> แถม)</th>
                            <th class="py-4 text-center">ระยะเวลา</th>
                            <th class="py-4 text-center">สถานะ</th>
                            <th class="py-4 text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $promotions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <tr
                                class="hover:bg-gray-700/50 transition-colors duration-200 group border-b border-gray-700 last:border-0">
                                
                                <td class="pl-6 align-top py-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-bold text-gray-200 text-base group-hover:text-emerald-400 transition-colors">
                                            <?php echo e($promo->name); ?>

                                        </span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($promo->description): ?>
                                            <span class="text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed">
                                                <?php echo e($promo->description); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-600 italic mt-1">- ไม่มีรายละเอียด -</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>

                                
                                <td class="text-center align-middle py-4">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($promo->code): ?>
                                        <span class="font-mono text-xs text-gray-200 bg-gray-700 px-2 py-1 rounded"><?php echo e($promo->code); ?></span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td class="text-center align-middle py-4">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($promo->discount_type): ?>
                                        <span class="badge badge-outline border-emerald-500 text-emerald-400 font-bold"><?php echo e($promo->discount_type === 'fixed' ? 'ลดคงที่' : ($promo->discount_type === 'percentage' ? 'ลด %' : '-')); ?></span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td class="text-right align-middle py-4">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($promo->discount_value)): ?>
                                        <span class="font-bold text-gray-200"><?php echo e(number_format($promo->discount_value, 0)); ?><?php echo e($promo->discount_type === 'percentage' ? '%' : '฿'); ?></span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td class="align-middle py-4">
                                    <div
                                        class="flex items-center justify-center gap-3 bg-gray-900/50 rounded-xl p-3 border border-dashed border-gray-600">
                                        <div class="flex flex-col gap-1 items-end min-w-[40%] text-right">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $promo->rules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                <div class="text-xs text-gray-400 flex items-center justify-end gap-2">
                                                    <span class="truncate max-w-[120px]"
                                                        title="<?php echo e($products[$rule->product_id]->pd_sp_name ?? 'Unknown'); ?>">
                                                        <?php echo e($products[$rule->product_id]->pd_sp_name ?? 'สินค้าถูกลบ'); ?>

                                                    </span>
                                                    <span
                                                        class="badge badge-sm badge-outline border-emerald-500 text-emerald-400 font-mono">x<?php echo e($rule->quantity); ?></span>
                                                </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </div>

                                        <div class="text-gray-500">
                                            <i class="fas fa-arrow-circle-right text-lg"></i>
                                        </div>

                                        <div class="flex flex-col gap-1 items-start min-w-[40%] text-left">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $promo->actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                <?php
                                                    if ($action->product_id) {
                                                        $getName =
                                                            $products[$action->product_id]->pd_sp_name ?? 'สินค้าถูกลบ';
                                                    } else {
                                                        $count = $action->giftableProducts->count();
                                                        $getName = "เลือกได้ ($count รายการ)";
                                                    }
                                                ?>
                                                <div class="text-xs text-gray-400 flex items-center gap-2">
                                                    <span
                                                        class="badge badge-sm border-none bg-pink-600 text-white font-mono">ฟรี
                                                        <?php echo e($action->quantity); ?></span>
                                                    <span class="truncate max-w-[120px]" title="<?php echo e($getName); ?>">
                                                        <?php echo e($getName); ?>

                                                    </span>
                                                </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </div>
                                    </div>
                                </td>

                                
                                <td class="text-center align-middle py-4">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($promo->start_date): ?>
                                        <div
                                            class="flex flex-col text-xs font-medium text-gray-400 bg-gray-900 border border-gray-600 rounded-lg px-2 py-1 inline-block text-left w-fit mx-auto shadow-sm">
                                            <div class="flex items-center gap-2 border-b border-gray-700 pb-1 mb-1">
                                                <span class="text-emerald-500 w-4 text-center"><i
                                                        class="fas fa-play text-[10px]"></i></span>
                                                <span
                                                    class="font-mono"><?php echo e(\Carbon\Carbon::parse($promo->start_date)->format('d/m/y H:i')); ?></span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-red-400 w-4 text-center"><i
                                                        class="fas fa-stop text-[10px]"></i></span>
                                                <span
                                                    class="font-mono"><?php echo e(\Carbon\Carbon::parse($promo->end_date)->format('d/m/y H:i')); ?></span>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="badge badge-ghost text-xs text-gray-400 bg-gray-700 border-gray-600">
                                            ตลอดไป</div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td class="text-center align-middle py-4">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($promo->is_active): ?>
                                        <span
                                            class="badge badge-success gap-1 text-white shadow-sm px-3 py-2 border-none bg-emerald-600">
                                            <i class="fas fa-check"></i> ใช้งาน
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="badge badge-ghost gap-1 text-gray-400 bg-gray-700 px-3 py-2 border-gray-600">
                                            <i class="fas fa-times"></i> ปิด
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td class="text-center align-middle py-4">
                                    <div class="join shadow-sm border border-gray-600 rounded-lg bg-gray-700">
                                        <a href="<?php echo e(route('admin.promotions.edit', $promo->id)); ?>"
                                            class="btn btn-sm btn-ghost join-item text-yellow-500 hover:bg-yellow-900/30 hover:text-yellow-400 tooltip tooltip-bottom"
                                            data-tip="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo e(route('admin.promotions.destroy', $promo->id)); ?>" method="POST"
                                            class="join-item"
                                            onsubmit="return confirm('ยืนยันลบโปรโมชั่นนี้? ข้อมูลจะไม่สามารถกู้คืนได้');">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit"
                                                class="btn btn-sm btn-ghost text-red-400 hover:bg-red-900/30 hover:text-red-300 tooltip tooltip-bottom"
                                                data-tip="ลบ">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="8" class="py-20 text-center">
                                    <div class="flex flex-col items-center justify-center opacity-60">
                                        <div class="bg-gray-700 rounded-full p-4 mb-4">
                                            <i class="fas fa-tag text-4xl text-gray-500"></i>
                                        </div>
                                        <h3 class="font-bold text-lg text-gray-400">ยังไม่มีโปรโมชั่น</h3>
                                        <p class="text-sm text-gray-500 mb-4">เริ่มต้นสร้างแคมเปญแรกของคุณได้เลย</p>
                                        <a href="<?php echo e(route('admin.promotions.create')); ?>"
                                            class="btn btn-sm btn-primary bg-emerald-600 border-none text-white">สร้างโปรโมชั่น</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($promotions->hasPages()): ?>
                <div class="px-6 py-4 border-t border-gray-700 bg-gray-900/30">
                    <?php echo e($promotions->links()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/promotions/index.blade.php ENDPATH**/ ?>