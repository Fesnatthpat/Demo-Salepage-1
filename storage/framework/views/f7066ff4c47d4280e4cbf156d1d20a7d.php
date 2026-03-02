<?php $__env->startSection('title', 'Admin Activity Log'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
            
            <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-white tracking-tight">Admin Activity Log</h2>
                    <p class="text-gray-400 mt-1">ตรวจสอบประวัติการเข้าใช้งานและการเปลี่ยนแปลงข้อมูลในระบบ</p>
                </div>
                <div class="text-right">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-widest">Total Logs: <?php echo e($activities->total()); ?></span>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filter_admin_name): ?>
                <div class="mb-6 flex items-center justify-between rounded-xl bg-blue-500/10 border border-blue-500/20 p-4 text-blue-200 backdrop-blur-sm">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-500/20 p-2 rounded-lg">
                            <i class="fas fa-filter text-blue-400"></i>
                        </div>
                        <p>กำลังแสดงรายการของ: <span class="font-bold text-white text-lg ml-1"><?php echo e($filter_admin_name); ?></span></p>
                    </div>
                    <a href="<?php echo e(route('admin.activity-log.index')); ?>"
                        class="flex items-center gap-2 px-3 py-1.5 bg-blue-500/20 hover:bg-blue-500/40 rounded-lg text-sm transition-all duration-200">
                        <i class="fas fa-times"></i> ล้างตัวกรอง
                    </a>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="space-y-6">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <?php
                        $config = match ($activity->action) {
                            'created' => ['color' => 'green', 'icon' => 'fas fa-plus-circle', 'label' => 'สร้างใหม่'],
                            'updated' => ['color' => 'amber', 'icon' => 'fas fa-edit', 'label' => 'แก้ไขข้อมูล'],
                            'deleted' => ['color' => 'red', 'icon' => 'fas fa-trash-alt', 'label' => 'ลบข้อมูล'],
                            default => ['color' => 'gray', 'icon' => 'fas fa-info-circle', 'label' => $activity->action],
                        };
                        $color = $config['color'];
                    ?>

                    <div class="group overflow-hidden rounded-2xl bg-gray-800/50 border border-gray-700 shadow-sm transition-all duration-300 hover:shadow-<?php echo e($color); ?>-500/10 hover:border-<?php echo e($color); ?>-500/40">

                        
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gray-800/80 px-5 py-4 border-b border-gray-700/50">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-<?php echo e($color); ?>-500/10 border border-<?php echo e($color); ?>-500/20 text-<?php echo e($color); ?>-400 shadow-inner">
                                    <i class="<?php echo e($config['icon']); ?> text-xl"></i>
                                </div>

                                <div>
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="text-sm font-bold uppercase tracking-wide text-<?php echo e($color); ?>-400">
                                            <?php echo e($config['label']); ?>

                                        </span>
                                        <span class="px-2 py-0.5 rounded-md bg-gray-700 text-[10px] font-bold text-gray-300 uppercase">
                                            <?php echo e(class_basename($activity->loggable_type)); ?>

                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-400 flex items-center gap-2">
                                        <span class="flex items-center gap-1">
                                            <i class="far fa-user text-[10px]"></i>
                                            <span class="font-semibold text-blue-400"><?php echo e($activity->admin->name ?? 'Unknown'); ?></span>
                                        </span>
                                        <span class="text-gray-600">|</span>
                                        <span class="flex items-center gap-1" title="<?php echo e($activity->created_at); ?>">
                                            <i class="far fa-clock text-[10px]"></i>
                                            <?php echo e($activity->created_at->diffForHumans()); ?>

                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="hidden sm:block">
                                <span class="px-3 py-1.5 rounded-lg bg-gray-900/50 border border-gray-700 text-[11px] font-mono text-gray-500">
                                    <i class="fas fa-network-wired mr-1.5 opacity-50"></i><?php echo e($activity->ip_address); ?>

                                </span>
                            </div>
                        </div>

                        
                        <div class="p-5">
                            <div class="mb-5 flex flex-wrap items-center gap-3 p-3 rounded-xl bg-gray-900/30 border border-gray-700/30">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">เป้าหมาย:</span>
                                <span class="text-sm font-semibold text-gray-200">
                                    <?php echo e($activity->loggable->pd_sp_name ?? ($activity->loggable->name ?? 'ID: ' . $activity->loggable_id)); ?>

                                </span>
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activity->loggable && in_array($activity->loggable_type, ['App\Models\ProductSalepage', 'App\Models\Promotion'])): ?>
                                    <?php
                                        $route = $activity->loggable_type === 'App\Models\ProductSalepage' ? 'admin.products.edit' : 'admin.promotions.edit';
                                    ?>
                                    <a href="<?php echo e(route($route, $activity->loggable_id)); ?>"
                                        class="inline-flex items-center gap-1.5 ml-auto px-3 py-1 text-xs font-medium text-blue-400 hover:text-blue-300 hover:bg-blue-400/10 rounded-lg transition-colors">
                                        <i class="fas fa-external-link-alt"></i> ดูข้อมูลต้นทาง
                                    </a>
                                <?php elseif(!$activity->loggable): ?>
                                    <span class="ml-auto flex items-center gap-1 text-[11px] font-medium text-red-400/80 italic">
                                        <i class="fas fa-exclamation-triangle"></i> ข้อมูลนี้ถูกลบออกจากระบบแล้ว
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($activity->changes)): ?>
                                <div class="rounded-xl border border-gray-700/50 bg-gray-900/40 overflow-hidden">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activity->action === 'updated' && isset($activity->changes['new'])): ?>
                                        
                                        <div class="grid grid-cols-12 gap-2 px-4 py-2.5 bg-gray-900/80 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-700/50">
                                            <div class="col-span-4 sm:col-span-3">ชื่อหัวข้อ</div>
                                            <div class="col-span-4 sm:col-span-4 text-red-400">ข้อมูลเดิม</div>
                                            <div class="col-span-1 sm:col-span-1 text-center italic text-gray-600">แก้ไข</div>
                                            <div class="col-span-3 sm:col-span-4 text-green-400 text-right sm:text-left">ข้อมูลใหม่</div>
                                        </div>

                                        <div class="divide-y divide-gray-800">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $activity->changes['new']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $newValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                <?php 
                                                    $oldValue = $activity->changes['original'][$key] ?? '-';
                                                    // ส่วนการแปลภาษาไทย
                                                    $translatedKey = match ($key) {
                                                        'pd_sp_stock' => 'จำนวนสต็อก',
                                                        'updated_at'  => 'วันที่อัปเดต',
                                                        'pd_sp_name'  => 'ชื่อสินค้า/เซลเพจ',
                                                        'status'      => 'สถานะ',
                                                        'price'       => 'ราคา',
                                                        'description' => 'รายละเอียด',
                                                        default       => str_replace('_', ' ', ucfirst($key)),
                                                    };
                                                ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($oldValue != $newValue): ?>
                                                    <div class="grid grid-cols-12 gap-2 px-4 py-3 text-[13px] font-mono hover:bg-white/[0.02] transition-colors items-center">
                                                        <div class="col-span-4 sm:col-span-3 text-gray-400 font-medium truncate" title="<?php echo e($key); ?>">
                                                            <?php echo e($translatedKey); ?>

                                                        </div>
                                                        <div class="col-span-4 sm:col-span-4 text-red-300/70 line-through break-all decoration-red-500/50">
                                                            <?php echo e(is_array($oldValue) ? 'Array' : (Str::limit($oldValue, 40) ?: '-')); ?>

                                                        </div>
                                                        <div class="col-span-1 sm:col-span-1 text-center text-gray-600">
                                                            <i class="fas fa-chevron-right text-[10px]"></i>
                                                        </div>
                                                        <div class="col-span-3 sm:col-span-4 text-green-400 break-all font-medium text-right sm:text-left">
                                                            <?php echo e(is_array($newValue) ? 'Array' : (Str::limit($newValue, 40) ?: '-')); ?>

                                                        </div>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        
                                        <div class="px-4 py-2.5 bg-gray-900/80 border-b border-gray-700/50">
                                            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">ข้อมูลขณะทำรายการ (Snapshot):</p>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-0 divide-y md:divide-y-0 divide-gray-800 px-4 py-2">
                                            <?php
                                                $data = $activity->action === 'created' ? $activity->changes['new'] ?? [] : $activity->changes['original'] ?? [];
                                            ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                <?php
                                                    $translatedKey = match ($key) {
                                                        'pd_sp_stock' => 'จำนวนสต็อก',
                                                        'updated_at'  => 'วันที่อัปเดต',
                                                        'created_at'  => 'วันที่สร้าง',
                                                        default       => str_replace('_', ' ', ucfirst($key)),
                                                    };
                                                ?>
                                                <div class="flex justify-between py-2 border-b border-gray-800/50 last:border-0 text-[12px] font-mono">
                                                    <span class="text-gray-500"><?php echo e($translatedKey); ?></span>
                                                    <span class="text-gray-300 truncate ml-4 max-w-[60%]" title="<?php echo e(is_array($val) ? 'Array' : $val); ?>">
                                                        <?php echo e(is_array($val) ? '{...}' : ($val ?: '-')); ?>

                                                    </span>
                                                </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-700 bg-gray-800/20 py-20 text-center">
                        <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gray-800 text-gray-600">
                            <i class="fas fa-history text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-300">ยังไม่มีประวัติการใช้งาน</h3>
                        <p class="text-gray-500 mt-2 max-w-xs">เมื่อมีการเพิ่ม ลบ หรือแก้ไขข้อมูลในระบบ ประวัติจะมาปรากฏอยู่ที่นี่</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="mt-10">
                <?php echo e($activities->links()); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/activity_log/index.blade.php ENDPATH**/ ?>