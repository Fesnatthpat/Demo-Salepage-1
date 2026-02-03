<?php $__env->startSection('title', 'Admin Activity Log'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
            <div class="flex justify-between items-end">
                <div>
                    <h2 class="text-2xl font-semibold leading-tight text-gray-100">Admin Activity Log</h2>
                    <p class="text-sm text-gray-400 mt-1">ประวัติการทำงานของผู้ดูแลระบบ</p>
                </div>
            </div>

            
            <?php if($filter_admin_name): ?>
                <div class="mt-6 bg-blue-900/20 border-l-4 border-blue-500 text-blue-200 p-4 rounded-r" role="alert">
                    <div class="flex items-center justify-between">
                        <p>แสดงรายการของ: <span class="font-bold"><?php echo e($filter_admin_name); ?></span></p>
                        <a href="<?php echo e(route('admin.activity-log.index')); ?>"
                            class="text-sm text-blue-400 hover:text-white underline transition">
                            <i class="fas fa-times mr-1"></i>ล้างตัวกรอง
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mt-6 space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $action_color = match ($activity->action) {
                            'created' => 'green',
                            'updated' => 'yellow',
                            'deleted' => 'red',
                            default => 'gray',
                        };

                        $action_icon = match ($activity->action) {
                            'created' => 'fas fa-plus-circle',
                            'updated' => 'fas fa-pencil-alt',
                            'deleted' => 'fas fa-trash-alt',
                            default => 'fas fa-info-circle',
                        };
                    ?>

                    <div
                        class="bg-gray-800 rounded-lg border border-gray-700 shadow-sm overflow-hidden hover:border-<?php echo e($action_color); ?>-500/50 transition-all duration-200">
                        
                        <div
                            class="px-4 py-3 border-b border-gray-700 bg-gray-900/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-<?php echo e($action_color); ?>-900/50 flex items-center justify-center border border-<?php echo e($action_color); ?>-500/30">
                                    <i class="<?php echo e($action_icon); ?> text-<?php echo e($action_color); ?>-400 text-sm"></i>
                                </div>
                                <div>
                                    <div class="flex items-baseline gap-2">
                                        <span
                                            class="font-bold text-gray-200 uppercase text-sm tracking-wide"><?php echo e($activity->action); ?></span>
                                        <span class="text-xs text-gray-400 font-mono bg-gray-700 px-1.5 py-0.5 rounded">
                                            <?php echo e(class_basename($activity->loggable_type)); ?>

                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        โดย
                                        <?php if($activity->admin): ?>
                                            <a href="<?php echo e(route('admin.activity-log.index', ['admin_id' => $activity->admin->id])); ?>"
                                                class="font-medium text-blue-400 hover:text-blue-300 hover:underline">
                                                <?php echo e($activity->admin->name); ?>

                                            </a>
                                        <?php else: ?>
                                            <span class="italic">Unknown Admin</span>
                                        <?php endif; ?>
                                        <span class="mx-1 text-gray-600">•</span>
                                        <span title="<?php echo e($activity->created_at->format('d/m/Y H:i:s')); ?>">
                                            <?php echo e($activity->created_at->diffForHumans()); ?>

                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="text-[10px] text-gray-500 font-mono self-start sm:self-center bg-gray-800 px-2 py-1 rounded border border-gray-700">
                                IP: <?php echo e($activity->ip_address); ?>

                            </div>
                        </div>

                        
                        <div class="p-4 space-y-4">
                            
                            <div class="flex items-start gap-2 text-sm">
                                <span
                                    class="text-gray-500 min-w-[60px] uppercase text-[10px] font-bold tracking-wider pt-1">Target:</span>
                                <div class="flex-1">
                                    <span class="text-gray-200 font-medium">
                                        <?php echo e($activity->loggable->pd_sp_name ?? ($activity->loggable->name ?? 'ID: ' . $activity->loggable_id)); ?>

                                    </span>

                                    
                                    <?php if($activity->loggable): ?>
                                        <?php if($activity->loggable_type === 'App\Models\ProductSalepage'): ?>
                                            <a href="<?php echo e(route('admin.products.edit', $activity->loggable_id)); ?>"
                                                class="ml-2 text-xs text-blue-400 hover:text-blue-300 hover:underline"><i
                                                    class="fas fa-external-link-alt mr-1"></i>ดูสินค้า</a>
                                        <?php elseif($activity->loggable_type === 'App\Models\Promotion'): ?>
                                            <a href="<?php echo e(route('admin.promotions.edit', $activity->loggable_id)); ?>"
                                                class="ml-2 text-xs text-blue-400 hover:text-blue-300 hover:underline"><i
                                                    class="fas fa-external-link-alt mr-1"></i>ดูโปรโมชั่น</a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="ml-2 text-xs text-red-400 italic">(ข้อมูลถูกลบไปแล้ว)</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            
                            <?php if($activity->changes): ?>
                                <div class="flex items-start gap-2 text-sm">
                                    <span
                                        class="text-gray-500 min-w-[60px] uppercase text-[10px] font-bold tracking-wider pt-1">Diff:</span>
                                    <div
                                        class="flex-1 bg-gray-900 rounded border border-gray-700 p-3 font-mono text-xs overflow-x-auto">

                                        <?php if(isset($activity->changes['original']) && isset($activity->changes['new'])): ?>
                                            
                                            <table class="w-full text-left border-collapse">
                                                <?php $hasChanges = false; ?>
                                                <?php $__currentLoopData = $activity->changes['new']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $newValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $oldValue = $activity->changes['original'][$key] ?? null;
                                                    ?>
                                                    <?php if($oldValue != $newValue): ?>
                                                        <?php $hasChanges = true; ?>
                                                        <tr class="border-b border-gray-800 last:border-0">
                                                            <td
                                                                class="py-1 pr-4 text-gray-500 font-semibold select-none w-1/4">
                                                                <?php echo e($key); ?></td>
                                                            <td class="py-1 px-2 text-red-400 line-through decoration-red-500/50 w-1/3 truncate max-w-[150px]"
                                                                title="<?php echo e(is_array($oldValue) ? json_encode($oldValue) : $oldValue); ?>">
                                                                <?php echo e(is_array($oldValue) ? json_encode($oldValue) : Str::limit($oldValue, 50)); ?>

                                                            </td>
                                                            <td class="py-1 px-2 text-gray-600 w-4">➜</td>
                                                            <td class="py-1 pl-2 text-green-400 w-1/3 truncate max-w-[150px]"
                                                                title="<?php echo e(is_array($newValue) ? json_encode($newValue) : $newValue); ?>">
                                                                <?php echo e(is_array($newValue) ? json_encode($newValue) : Str::limit($newValue, 50)); ?>

                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                <?php if(!$hasChanges): ?>
                                                    <tr>
                                                        <td colspan="4" class="text-gray-500 italic">No significant
                                                            changes detected (or fields are hidden).</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </table>
                                        <?php else: ?>
                                            
                                            <pre class="text-gray-300"><?php echo e(json_encode($activity->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div
                        class="flex flex-col items-center justify-center py-16 border-2 border-dashed border-gray-700 rounded-xl bg-gray-800/30">
                        <div class="text-gray-600 text-5xl mb-4"><i class="far fa-clipboard"></i></div>
                        <p class="text-gray-400 text-lg font-medium">ยังไม่มีประวัติการใช้งาน</p>
                        <p class="text-gray-600 text-sm">การเปลี่ยนแปลงข้อมูลจะถูกบันทึกที่นี่</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mt-8">
                <?php echo e($activities->links()); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/activity_log/index.blade.php ENDPATH**/ ?>