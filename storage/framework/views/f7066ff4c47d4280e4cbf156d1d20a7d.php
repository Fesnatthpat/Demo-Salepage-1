<?php $__env->startSection('title', 'Admin Activity Log'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
            <div>
                <h2 class="text-2xl font-semibold leading-tight">Admin Activity Log</h2>
                <p class="text-sm text-gray-600">A log of all changes made to products and promotions by admins.</p>
            </div>

            <?php if($filter_admin_name): ?>
                <div class="mt-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                    <p class="font-bold">Filtering by: <?php echo e($filter_admin_name); ?></p>
                    <a href="<?php echo e(route('admin.activity-log.index')); ?>" class="text-sm text-blue-600 hover:text-blue-800">Clear Filter</a>
                </div>
            <?php endif; ?>

            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto mt-6">
                <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Admin
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Action
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Target
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Changes
                                </th>
                                 <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    IP Address
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Time
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <?php if($activity->admin): ?>
                                        <a href="<?php echo e(route('admin.activity-log.index', ['admin_id' => $activity->admin->id])); ?>" class="text-blue-600 hover:underline whitespace-no-wrap">
                                            <?php echo e($activity->admin->name); ?>

                                        </a>
                                        <?php else: ?>
                                            <p class="text-gray-500 whitespace-no-wrap">N/A</p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                             <?php if($activity->action === 'created'): ?>
                                                <span aria-hidden class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                                <span class="relative">Created</span>
                                            <?php elseif($activity->action === 'updated'): ?>
                                                <span aria-hidden class="absolute inset-0 bg-yellow-200 opacity-50 rounded-full"></span>
                                                <span class="relative">Updated</span>
                                            <?php elseif($activity->action === 'deleted'): ?>
                                                <span aria-hidden class="absolute inset-0 bg-red-200 opacity-50 rounded-full"></span>
                                                <span class="relative">Deleted</span>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">
                                            <?php echo e(Str::afterLast($activity->loggable_type, '\\')); ?>:
                                            <?php echo e($activity->loggable->pd_sp_name ?? $activity->loggable->name ?? $activity->loggable_id); ?>

                                        </p>
                                        <?php if($activity->loggable): ?>
                                            <?php if($activity->loggable_type === 'App\Models\ProductSalepage'): ?>
                                                <a href="<?php echo e(route('admin.products.edit', $activity->loggable_id)); ?>" class="text-xs text-blue-600 hover:underline">
                                                    View Details &rarr;
                                                </a>
                                            <?php elseif($activity->loggable_type === 'App\Models\Promotion'): ?>
                                                <a href="<?php echo e(route('admin.promotions.edit', $activity->loggable_id)); ?>" class="text-xs text-blue-600 hover:underline">
                                                    View Details &rarr;
                                                </a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400">(Item has been deleted)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                       <?php if($activity->changes): ?>
                                            <div class="whitespace-pre-wrap text-xs bg-gray-50 p-2 rounded">
                                                <?php if(isset($activity->changes['original']) && isset($activity->changes['new'])): ?>
                                                    <p class="font-semibold mb-1">Changes:</p>
                                                    <?php $__currentLoopData = $activity->changes['new']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute => $newValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if(isset($activity->changes['original'][$attribute])): ?>
                                                            <?php if($activity->changes['original'][$attribute] != $newValue): ?>
                                                                <p><strong><?php echo e($attribute); ?>:</strong> <span class="text-red-600"><?php echo e($activity->changes['original'][$attribute]); ?></span> &rarr; <span class="text-green-600"><?php echo e($newValue); ?></span></p>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <p><strong><?php echo e($attribute); ?>:</strong> <span class="text-green-600">Added: <?php echo e($newValue); ?></span></p>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php elseif(isset($activity->changes['new'])): ?>
                                                    <p class="font-semibold mb-1">New Data:</p>
                                                    <pre><code><?php echo e(json_encode($activity->changes['new'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></code></pre>
                                                <?php elseif(isset($activity->changes['original'])): ?>
                                                    <p class="font-semibold mb-1">Original Data (before deletion):</p>
                                                    <pre><code><?php echo e(json_encode($activity->changes['original'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></code></pre>
                                                <?php else: ?>
                                                    <pre><code><?php echo e(json_encode($activity->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></code></pre>
                                                <?php endif; ?>
                                            </div>
                                       <?php else: ?>
                                            <span class="text-gray-500">N/A</span>
                                       <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo e($activity->ip_address); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo e($activity->created_at->diffForHumans()); ?></p>
                                        <p class="text-gray-600 whitespace-no-wrap text-xs"><?php echo e($activity->created_at->format('Y-m-d H:i:s')); ?></p>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        No activities logged yet.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
                        <?php echo e($activities->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/activity_log/index.blade.php ENDPATH**/ ?>