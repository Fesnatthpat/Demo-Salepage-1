

<?php $__env->startSection('title', 'จัดการคำถามที่พบบ่อย'); ?>

<?php $__env->startSection('page-title'); ?>
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">จัดการคำถามที่พบบ่อย (FAQ)</h1>
            <p class="text-sm text-gray-400 mt-1">บริหารจัดการรายการคำถามและคำตอบทั้งหมดในระบบ</p>
        </div>
        <a href="<?php echo e(route('admin.faqs.create')); ?>"
            class="group flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2.5 rounded-lg shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 font-medium">
            <i class="fas fa-plus transition-transform group-hover:rotate-90"></i>
            เพิ่มคำถามใหม่
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        
        <div class="bg-gray-800 rounded-xl shadow-sm border border-gray-700/50 p-4">
            <form action="<?php echo e(route('admin.faqs.index')); ?>" method="GET" class="relative">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-600 rounded-lg leading-5 bg-gray-700/50 text-gray-300 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm transition-colors"
                        placeholder="ค้นหาคำถาม หรือคำตอบ...">
                </div>
            </form>
        </div>

        
        <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-400">
                    <thead class="bg-gray-900/50 text-xs uppercase font-semibold text-gray-300">
                        <tr>
                            <th class="px-6 py-4 w-16 text-center">#</th>
                            <th class="px-6 py-4 w-24 text-center">ลำดับ</th>
                            <th class="px-6 py-4 min-w-[200px]">คำถาม</th>
                            <th class="px-6 py-4 min-w-[300px]">คำตอบ (โดยย่อ)</th>
                            <th class="px-6 py-4 text-center w-32">สถานะ</th>
                            <th class="px-6 py-4 text-right w-32">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <tr class="hover:bg-gray-700/50 transition-colors duration-150">
                                <td class="px-6 py-4 text-center font-medium"><?php echo e($loop->iteration); ?></td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-700 text-gray-300 font-mono text-xs">
                                        <?php echo e($faq->sort_order); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-white font-medium text-base truncate max-w-xs"
                                        title="<?php echo e($faq->question); ?>">
                                        <?php echo e($faq->question); ?>

                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-400 truncate max-w-sm" title="<?php echo e($faq->answer); ?>">
                                        <?php echo e(Str::limit($faq->answer, 60)); ?>

                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($faq->is_active): ?>
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> เปิดใช้งาน
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> ปิดใช้งาน
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="<?php echo e(route('admin.faqs.edit', $faq->id)); ?>"
                                            class="p-2 text-yellow-400 hover:text-white hover:bg-yellow-500 rounded-lg transition-all duration-200"
                                            title="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo e(route('admin.faqs.destroy', $faq->id)); ?>" method="POST"
                                            onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบคำถามนี้? การกระทำนี้ไม่สามารถเรียกคืนได้');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit"
                                                class="p-2 text-red-400 hover:text-white hover:bg-red-500 rounded-lg transition-all duration-200"
                                                title="ลบ">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <i class="fas fa-folder-open text-4xl mb-3 opacity-50"></i>
                                        <p class="text-lg font-medium">ยังไม่มีข้อมูลคำถามที่พบบ่อย</p>
                                        <p class="text-sm mt-1">เริ่มต้นด้วยการสร้างคำถามใหม่</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(method_exists($faqs, 'links')): ?>
                <div class="px-6 py-4 border-t border-gray-700 bg-gray-800">
                    <?php echo e($faqs->links()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/faqs/index.blade.php ENDPATH**/ ?>