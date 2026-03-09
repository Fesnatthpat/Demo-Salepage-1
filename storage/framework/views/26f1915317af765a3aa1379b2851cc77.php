

<?php $__env->startSection('title', 'ประวัติการใช้โปรโมชั่น'); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <div class="flex items-center gap-3 mb-2 animate-fade-in-down">
            <div class="p-2 bg-gray-800 rounded-lg border border-gray-700 text-gray-400">
                <i class="fas fa-history"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">ประวัติการใช้คูปองและส่วนลด</h1>
        </div>

        
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-5 shadow-lg animate-fade-in-up">
            <form action="<?php echo e(route('admin.promotions.logs')); ?>" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5 ml-1">รหัสส่วนลด</label>
                    <div class="relative">
                        <input type="text" name="code" value="<?php echo e(request('code')); ?>" placeholder="ค้นหารหัส..."
                            class="w-full bg-gray-900 border-gray-600 rounded-lg pl-9 pr-3 py-2 text-gray-200 text-sm focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-600">
                        <i class="fas fa-ticket-alt absolute left-3 top-2.5 text-gray-500 text-xs"></i>
                    </div>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5 ml-1">ชื่อลูกค้า</label>
                    <div class="relative">
                        <input type="text" name="customer" value="<?php echo e(request('customer')); ?>" placeholder="ชื่อลูกค้า..."
                            class="w-full bg-gray-900 border-gray-600 rounded-lg pl-9 pr-3 py-2 text-gray-200 text-sm focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-600">
                        <i class="fas fa-user absolute left-3 top-2.5 text-gray-500 text-xs"></i>
                    </div>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5 ml-1">ชื่อโปรโมชั่น</label>
                    <div class="relative">
                        <input type="text" name="promotion" value="<?php echo e(request('promotion')); ?>" placeholder="โปรโมชั่น..."
                            class="w-full bg-gray-900 border-gray-600 rounded-lg pl-9 pr-3 py-2 text-gray-200 text-sm focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-600">
                        <i class="fas fa-tag absolute left-3 top-2.5 text-gray-500 text-xs"></i>
                    </div>
                </div>
                <div class="md:col-span-2 flex items-end">
                    <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2 px-4 rounded-lg transition-all shadow-lg shadow-emerald-900/20 flex items-center justify-center gap-2 transform active:scale-95">
                        <i class="fas fa-search"></i> ค้นหา
                    </button>
                </div>
            </form>
        </div>

        
        <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden shadow-xl animate-fade-in-up"
            style="animation-delay: 100ms;">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-900/80 text-gray-400 text-xs uppercase tracking-wider font-bold">
                            <th class="px-6 py-4 border-b border-gray-700">วันที่ใช้งาน</th>
                            <th class="px-6 py-4 border-b border-gray-700">โปรโมชั่น</th>
                            <th class="px-6 py-4 border-b border-gray-700 text-center">รหัสที่ใช้</th>
                            <th class="px-6 py-4 border-b border-gray-700">ลูกค้า</th>
                            <th class="px-6 py-4 border-b border-gray-700 text-center">ออเดอร์</th>
                            <th class="px-6 py-4 border-b border-gray-700 text-right">ส่วนลด</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-300 text-sm divide-y divide-gray-700/50">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <tr class="hover:bg-gray-700/30 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-medium text-gray-200"><?php echo e($log->created_at->format('d/m/Y')); ?></span>
                                        <span class="text-xs text-gray-500"><?php echo e($log->created_at->format('H:i')); ?> น.</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="font-bold text-white group-hover:text-emerald-400 transition-colors block"><?php echo e($log->promotion->name ?? 'Deleted Promotion'); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!isset($log->promotion)): ?>
                                        <span class="text-[10px] text-red-400 bg-red-900/20 px-1 rounded">Deleted</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->code_used): ?>
                                        <span
                                            class="px-2.5 py-1 bg-blue-500/10 text-blue-400 rounded-md border border-blue-500/20 font-mono text-xs font-bold uppercase shadow-sm">
                                            <?php echo e($log->code_used); ?>

                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="text-[10px] text-gray-500 uppercase tracking-wider border border-gray-600 rounded px-1.5 py-0.5">Auto</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 text-xs font-bold">
                                            <?php echo e(substr($log->user->name ?? '?', 0, 1)); ?>

                                        </div>
                                        <div class="flex flex-col">
                                            <span
                                                class="text-white text-sm font-medium"><?php echo e($log->user->name ?? 'Unknown'); ?></span>
                                            <span class="text-xs text-gray-500"><?php echo e($log->user->email ?? ''); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="<?php echo e(route('admin.orders.show', $log->order_id)); ?>"
                                        class="inline-block px-3 py-1 bg-gray-900 hover:bg-emerald-900/30 text-emerald-400 border border-gray-700 hover:border-emerald-500/30 rounded-lg transition-all text-xs font-mono">
                                        #<?php echo e($log->order->ord_code ?? $log->order_id); ?>

                                    </a>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <span
                                        class="text-red-400 font-bold text-base">-฿<?php echo e(number_format($log->discount_amount, 2)); ?></span>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-history text-3xl opacity-30"></i>
                                        </div>
                                        <p>ไม่พบข้อมูลประวัติการใช้งาน</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($logs->hasPages()): ?>
                <div class="px-6 py-4 bg-gray-900/30 border-t border-gray-700">
                    <?php echo e($logs->appends(request()->query())->links()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/promotions/logs.blade.php ENDPATH**/ ?>