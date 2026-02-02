<?php $__env->startSection('title', 'Admin Activity Log'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
            <div>
                <h2 class="text-2xl font-semibold leading-tight text-gray-100">Admin Activity Log</h2>
                <p class="text-sm text-gray-400">บันทึกการเปลี่ยนแปลงข้อมูลสินค้าและโปรโมชั่นโดยแอดมิน</p>
            </div>

            <?php if($filter_admin_name): ?>
                <div class="mt-4 bg-blue-900/30 border-l-4 border-blue-500 text-blue-200 p-4" role="alert">
                    <p class="font-bold">Filtering by: <?php echo e($filter_admin_name); ?></p>
                    <a href="<?php echo e(route('admin.activity-log.index')); ?>" class="text-sm text-blue-400 hover:text-blue-300">Clear
                        Filter</a>
                </div>
            <?php endif; ?>

            <div class="mt-6 space-y-6">
                <?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $action_color = '';
                        $action_icon = '';
                        switch ($activity->action) {
                            case 'created':
                                $action_color = 'green';
                                $action_icon = 'fas fa-plus-circle';
                                break;
                            case 'updated':
                                $action_color = 'yellow';
                                $action_icon = 'fas fa-pencil-alt';
                                break;
                            case 'deleted':
                                $action_color = 'red';
                                $action_icon = 'fas fa-trash-alt';
                                break;
                        }
                    ?>
                    <div
                        class="bg-gray-800 rounded-xl border border-gray-700 shadow-md overflow-hidden hover:border-<?php echo e($action_color); ?>-500/50 transition-all">
                        <div
                            class="p-4 border-b border-gray-700 bg-gray-900/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <span class="text-<?php echo e($action_color); ?>-400 text-xl"><i class="<?php echo e($action_icon); ?>"></i></span>
                                <div>
                                    <p class="font-bold text-gray-100">
                                        <?php echo e(ucfirst($activity->action)); ?>

                                        <span class="font-normal text-gray-400">a
                                            <?php echo e(Str::afterLast($activity->loggable_type, '\\')); ?></span>
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        by
                                        <?php if($activity->admin): ?>
                                            <a href="<?php echo e(route('admin.activity-log.index', ['admin_id' => $activity->admin->id])); ?>"
                                                class="font-semibold text-blue-400 hover:text-blue-300"><?php echo e($activity->admin->name); ?></a>
                                        <?php else: ?>
                                            <span class="text-gray-500">N/A</span>
                                        <?php endif; ?>
                                        <span class="mx-1">&bull;</span>
                                        <span title="<?php echo e($activity->created_at->format('Y-m-d H:i:s')); ?>">
                                            <?php echo e($activity->created_at->diffForHumans()); ?>

                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 font-mono self-end sm:self-center">
                                IP: <?php echo e($activity->ip_address); ?>

                            </div>
                        </div>

                        <div class="p-5 space-y-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Target</p>
                                <div class="flex items-center gap-3">
                                    <p class="text-gray-200">
                                        <?php echo e($activity->loggable->pd_sp_name ?? ($activity->loggable->name ?? $activity->loggable_id)); ?>

                                    </p>
                                    <?php if($activity->loggable): ?>
                                        <?php if($activity->loggable_type === 'App\Models\ProductSalepage'): ?>
                                            <a href="<?php echo e(route('admin.products.edit', $activity->loggable_id)); ?>"
                                                class="text-xs text-blue-400 hover:underline">
                                                View &rarr;
                                            </a>
                                        <?php elseif($activity->loggable_type === 'App\Models\Promotion'): ?>
                                            <a href="<?php echo e(route('admin.promotions.edit', $activity->loggable_id)); ?>"
                                                class="text-xs text-blue-400 hover:underline">
                                                View &rarr;
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-500">(Item since deleted)</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if($activity->changes): ?>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Changes</p>
                                    <div
                                        class="text-xs bg-gray-900 p-4 rounded-lg border border-gray-700 font-mono text-gray-300 space-y-2">
                                        <?php if(isset($activity->changes['original']) && isset($activity->changes['new'])): ?>
                                            <?php $__currentLoopData = $activity->changes['new']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute => $newValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(
                                                    isset($activity->changes['original'][$attribute]) &&
                                                        $activity->changes['original'][$attribute] != $newValue): ?>
                                                    <div>
                                                        <span
                                                            class="text-gray-500 select-none"><?php echo e($attribute); ?>:</span>
                                                        <div class="flex flex-col sm:flex-row sm:gap-2">
                                                            <span
                                                                class="text-red-400/80 line-through truncate"><?php echo e(Str::limit($activity->changes['original'][$attribute], 100)); ?></span>
                                                            <span class="text-gray-500 select-none sm:block hidden">&rarr;</span>
                                                            <span
                                                                class="text-green-400 truncate"><?php echo e(Str::limit($newValue, 100)); ?></span>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <pre class="whitespace-pre-wrap text-xs"><?php echo e(json_encode($activity->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-20 border-2 border-dashed border-gray-700 rounded-xl bg-gray-800/50">
                        <p class="text-gray-500">No activities logged yet.</p>
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