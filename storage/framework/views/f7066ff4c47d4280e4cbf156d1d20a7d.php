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

            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto mt-6">
                <div class="inline-block min-w-full shadow-lg rounded-lg overflow-hidden border border-gray-700">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th
                                    class="px-5 py-3 border-b border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                                    Admin
                                </th>
                                <th
                                    class="px-5 py-3 border-b border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                                    Action
                                </th>
                                <th
                                    class="px-5 py-3 border-b border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                                    Target
                                </th>
                                <th
                                    class="px-5 py-3 border-b border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                                    Changes
                                </th>
                                <th
                                    class="px-5 py-3 border-b border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                                    IP Address
                                </th>
                                <th
                                    class="px-5 py-3 border-b border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                                    Time
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800">
                            <?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-700/50 transition-colors">
                                    <td class="px-5 py-5 border-b border-gray-700 text-sm">
                                        <?php if($activity->admin): ?>
                                            <a href="<?php echo e(route('admin.activity-log.index', ['admin_id' => $activity->admin->id])); ?>"
                                                class="text-blue-400 hover:text-blue-300 whitespace-no-wrap font-medium">
                                                <?php echo e($activity->admin->name); ?>

                                            </a>
                                        <?php else: ?>
                                            <p class="text-gray-500 whitespace-no-wrap">N/A</p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-700 text-sm">
                                        <span
                                            class="relative inline-block px-3 py-1 font-semibold leading-tight text-xs rounded-full 
                                            <?php if($activity->action === 'created'): ?> text-green-300 bg-green-900/50
                                            <?php elseif($activity->action === 'updated'): ?> text-yellow-300 bg-yellow-900/50
                                            <?php elseif($activity->action === 'deleted'): ?> text-red-300 bg-red-900/50 <?php endif; ?>">
                                            <?php echo e(ucfirst($activity->action)); ?>

                                        </span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-700 text-sm">
                                        <p class="text-gray-300 whitespace-no-wrap">
                                            <span
                                                class="text-gray-500"><?php echo e(Str::afterLast($activity->loggable_type, '\\')); ?>:</span>
                                            <?php echo e($activity->loggable->pd_sp_name ?? ($activity->loggable->name ?? $activity->loggable_id)); ?>

                                        </p>
                                        <?php if($activity->loggable): ?>
                                            <?php if($activity->loggable_type === 'App\Models\ProductSalepage'): ?>
                                                <a href="<?php echo e(route('admin.products.edit', $activity->loggable_id)); ?>"
                                                    class="text-xs text-blue-400 hover:underline mt-1 inline-block">
                                                    View Details &rarr;
                                                </a>
                                            <?php elseif($activity->loggable_type === 'App\Models\Promotion'): ?>
                                                <a href="<?php echo e(route('admin.promotions.edit', $activity->loggable_id)); ?>"
                                                    class="text-xs text-blue-400 hover:underline mt-1 inline-block">
                                                    View Details &rarr;
                                                </a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-500">(Item deleted)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-700 text-sm">
                                        <?php if($activity->changes): ?>
                                            <div
                                                class="whitespace-pre-wrap text-xs bg-gray-900 p-3 rounded border border-gray-600 font-mono text-gray-300">
                                                <?php if(isset($activity->changes['original']) && isset($activity->changes['new'])): ?>
                                                    <p class="font-bold mb-2 text-gray-400">Changes:</p>
                                                    <?php $__currentLoopData = $activity->changes['new']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute => $newValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if(isset($activity->changes['original'][$attribute])): ?>
                                                            <?php if($activity->changes['original'][$attribute] != $newValue): ?>
                                                                <div class="mb-1">
                                                                    <span class="text-gray-400"><?php echo e($attribute); ?>:</span>
                                                                    <span
                                                                        class="text-red-400 line-through mx-1"><?php echo e($activity->changes['original'][$attribute]); ?></span>
                                                                    &rarr;
                                                                    <span class="text-green-400"><?php echo e($newValue); ?></span>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <div class="mb-1">
                                                                <span class="text-gray-400"><?php echo e($attribute); ?>:</span>
                                                                <span class="text-green-400">Added:
                                                                    <?php echo e($newValue); ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php elseif(isset($activity->changes['new'])): ?>
                                                    <p class="font-bold mb-1 text-gray-400">New Data:</p>
                                                    <div class="overflow-x-auto">
                                                        <?php echo e(json_encode($activity->changes['new'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?>

                                                    </div>
                                                <?php else: ?>
                                                    <div class="overflow-x-auto">
                                                        <?php echo e(json_encode($activity->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?>

                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-600">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-700 text-sm">
                                        <p class="text-gray-400 whitespace-no-wrap"><?php echo e($activity->ip_address); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-700 text-sm">
                                        <p class="text-gray-300 whitespace-no-wrap">
                                            <?php echo e($activity->created_at->diffForHumans()); ?></p>
                                        <p class="text-gray-500 whitespace-no-wrap text-xs">
                                            <?php echo e($activity->created_at->format('Y-m-d H:i:s')); ?></p>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center px-5 py-8 border-b border-gray-700 text-gray-500">
                                        No activities logged yet.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div
                        class="px-5 py-5 bg-gray-800 border-t border-gray-700 flex flex-col xs:flex-row items-center xs:justify-between">
                        <?php echo e($activities->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/activity_log/index.blade.php ENDPATH**/ ?>