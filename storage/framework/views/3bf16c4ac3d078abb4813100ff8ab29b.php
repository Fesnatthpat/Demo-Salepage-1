

<?php $__env->startSection('title', 'จัดการโปรโมชั่น'); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Campaign Manager</h1>
                <p class="text-gray-400 text-sm mt-1">จัดการแคมเปญส่วนลดและของแถม</p>
            </div>
            <a href="<?php echo e(route('admin.promotions.create')); ?>"
                class="group relative inline-flex items-center justify-center px-6 py-2.5 text-sm font-medium text-white transition-all duration-200 bg-emerald-600 rounded-lg hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 shadow-lg shadow-emerald-900/30">
                <i class="fas fa-plus mr-2 transition-transform group-hover:rotate-90"></i> สร้างโปรโมชั่น
            </a>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-800/50 rounded-xl p-4 border border-gray-700/50 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-400">ใช้งานอยู่ (Active)</p>
                    <p class="text-2xl font-bold text-white"><?php echo e($promotions->where('is_active', true)->count()); ?></p>
                </div>
            </div>
            <div class="bg-gray-800/50 rounded-xl p-4 border border-gray-700/50 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-400">
                    <i class="fas fa-ticket-alt text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-400">แบบใช้โค้ด (Coupon)</p>
                    <p class="text-2xl font-bold text-white"><?php echo e($promotions->whereNotNull('code')->count()); ?></p>
                </div>
            </div>
            <div class="bg-gray-800/50 rounded-xl p-4 border border-gray-700/50 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-pink-500/10 flex items-center justify-center text-pink-400">
                    <i class="fas fa-gifts text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-400">โปรฯ ของแถม (BxGy)</p>
                    <p class="text-2xl font-bold text-white">
                        <?php echo e($promotions->filter(fn($p) => $p->rules->count() > 0)->count()); ?></p>
                </div>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                class="flex items-center p-4 mb-4 text-emerald-400 rounded-lg bg-emerald-900/20 border border-emerald-500/20"
                role="alert">
                <i class="fas fa-check-circle flex-shrink-0 w-5 h-5"></i>
                <div class="ml-3 text-sm font-medium"><?php echo e(session('success')); ?></div>
                <button @click="show = false" type="button"
                    class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-emerald-400 rounded-lg p-1.5 hover:bg-emerald-900/40 inline-flex h-8 w-8">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="bg-gray-800 rounded-xl shadow-sm border border-gray-700/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-gray-900/50 border-b border-gray-700 text-xs uppercase text-gray-400 font-semibold tracking-wider">
                            <th class="px-6 py-4">แคมเปญ</th>
                            <th class="px-6 py-4 text-center">ประเภท</th>
                            <th class="px-6 py-4 text-center">เงื่อนไข</th>
                            <th class="px-6 py-4 text-center">การใช้งาน</th>
                            <th class="px-6 py-4 text-center">สถานะ</th>
                            <th class="px-6 py-4 text-right">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $promotions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <tr class="hover:bg-gray-700/30 transition-colors duration-150 group">
                                
                                <td class="px-6 py-4 align-top">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-bold text-white group-hover:text-emerald-400 transition-colors mb-1">
                                            <?php echo e($promo->name); ?>

                                        </span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($promo->description): ?>
                                            <span class="text-xs text-gray-500 line-clamp-1"
                                                title="<?php echo e($promo->description); ?>"><?php echo e($promo->description); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <div
                                            class="flex items-center gap-2 mt-2 text-[10px] text-gray-400 bg-gray-900/50 w-fit px-2 py-1 rounded">
                                            <i class="far fa-clock"></i>
                                            <span>
                                                <?php echo e($promo->start_date ? \Carbon\Carbon::parse($promo->start_date)->format('d M y') : 'Now'); ?>

                                                -
                                                <?php echo e($promo->end_date ? \Carbon\Carbon::parse($promo->end_date)->format('d M y') : '∞'); ?>

                                            </span>
                                        </div>
                                    </div>
                                </td>

                                
                                <td class="px-6 py-4 text-center align-middle">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($promo->code): ?>
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                            <i class="fas fa-tag"></i> Code: <?php echo e($promo->code); ?>

                                        </span>
                                    <?php elseif($promo->rules->count() > 0): ?>
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-pink-500/10 text-pink-400 border border-pink-500/20">
                                            <i class="fas fa-gift"></i> Buy X Get Y
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            <i class="fas fa-bolt"></i> Auto Discount
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td class="px-6 py-4 text-center align-middle">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($promo->discount_value)): ?>
                                        <div class="text-white font-mono text-lg font-bold">
                                            <?php echo e(number_format($promo->discount_value, 0)); ?><span
                                                class="text-sm text-gray-500 ml-0.5"><?php echo e($promo->discount_type === 'percentage' ? '%' : '฿'); ?></span>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($promo->min_order_value > 0): ?>
                                            <div class="text-[10px] text-gray-500 mt-1">ขั้นต่ำ
                                                ฿<?php echo e(number_format($promo->min_order_value)); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php elseif($promo->rules->count() > 0): ?>
                                        <div class="flex items-center justify-center gap-2 text-xs">
                                            <span class="bg-gray-700 text-gray-300 px-2 py-1 rounded">Buy
                                                <?php echo e($promo->rules->sum('quantity')); ?></span>
                                            <i class="fas fa-arrow-right text-gray-500 text-[10px]"></i>
                                            <span class="bg-pink-900/30 text-pink-300 px-2 py-1 rounded">Get
                                                <?php echo e($promo->actions->sum('quantity')); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-600">-</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td class="px-6 py-4 align-middle">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($promo->usage_limit): ?>
                                        <div class="w-full max-w-[100px] mx-auto">
                                            <div class="flex justify-between text-[10px] text-gray-400 mb-1">
                                                <span><?php echo e(number_format($promo->used_count)); ?></span>
                                                <span><?php echo e(number_format($promo->usage_limit)); ?></span>
                                            </div>
                                            <div class="w-full bg-gray-700 rounded-full h-1.5">
                                                <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500"
                                                    style="width: <?php echo e(min(100, ($promo->used_count / $promo->usage_limit) * 100)); ?>%">
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center text-xs text-gray-500">
                                            <i class="fas fa-infinity text-[10px] mr-1"></i> ไม่จำกัด
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td class="px-6 py-4 text-center align-middle">
                                    <div class="flex items-center justify-center">
                                        <div
                                            class="w-2 h-2 rounded-full mr-2 <?php echo e($promo->is_active ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-gray-600'); ?>">
                                        </div>
                                        <span
                                            class="text-xs font-medium <?php echo e($promo->is_active ? 'text-gray-200' : 'text-gray-500'); ?>">
                                            <?php echo e($promo->is_active ? 'Active' : 'Inactive'); ?>

                                        </span>
                                    </div>
                                </td>

                                
                                <td class="px-6 py-4 text-right align-middle">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="<?php echo e(route('admin.promotions.edit', $promo->id)); ?>"
                                            class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-all"
                                            title="แก้ไข">
                                            <i class="fas fa-pen text-xs"></i>
                                        </a>
                                        <form action="<?php echo e(route('admin.promotions.destroy', $promo->id)); ?>" method="POST"
                                            onsubmit="return confirm('ยืนยันการลบ? ข้อมูลนี้ไม่สามารถกู้คืนได้');">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit"
                                                class="p-2 text-gray-400 hover:text-red-400 hover:bg-red-900/20 rounded-lg transition-all"
                                                title="ลบ">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-inbox text-3xl opacity-50"></i>
                                        </div>
                                        <p class="text-base font-medium text-gray-400">ยังไม่มีข้อมูลโปรโมชั่น</p>
                                        <p class="text-sm mt-1">เริ่มต้นสร้างแคมเปญใหม่ได้เลย</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($promotions->hasPages()): ?>
                <div class="px-6 py-4 border-t border-gray-700 bg-gray-800">
                    <?php echo e($promotions->links()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/promotions/index.blade.php ENDPATH**/ ?>