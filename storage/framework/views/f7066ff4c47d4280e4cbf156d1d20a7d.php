<?php $__env->startSection('title', 'Admin Activity Log'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
            <div>
                <h2 class="text-2xl font-semibold leading-tight">Admin Activity Log</h2>
                <p class="text-sm text-gray-600">A log of all changes made to products and promotions by admins.</p>
            </div>

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
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo e($activity->admin->name ?? 'N/A'); ?>

                                        </p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <span
                                            class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                            <?php if($activity->action === 'created'): ?>
                                                <span aria-hidden
                                                    class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                                <span class="relative">Created</span>
                                            <?php elseif($activity->action === 'updated'): ?>
                                                <span aria-hidden
                                                    class="absolute inset-0 bg-yellow-200 opacity-50 rounded-full"></span>
                                                <span class="relative">Updated</span>
                                            <?php elseif($activity->action === 'deleted'): ?>
                                                <span aria-hidden
                                                    class="absolute inset-0 bg-red-200 opacity-50 rounded-full"></span>
                                                <span class="relative">Deleted</span>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">
                                            <?php echo e(Str::afterLast($activity->loggable_type, '\\')); ?>:
                                            <?php echo e($activity->loggable->pd_sp_name ?? ($activity->loggable->name ?? $activity->loggable_id)); ?>

                                        </p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <?php if($activity->changes): ?>
                                            <pre class="whitespace-pre-wrap text-xs bg-gray-50 p-2 rounded"><code><?php echo e(json_encode($activity->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></code></pre>
                                        <?php else: ?>
                                            <span class="text-gray-500">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo e($activity->ip_address); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        
                                        <p class="text-gray-900 whitespace-no-wrap">
                                            <?php echo e($activity->created_at->diffForHumans()); ?></p>

                                        
                                        <p class="text-gray-600 whitespace-no-wrap text-xs">
                                            <?php echo e($activity->created_at->format('Y-m-d H:i:s')); ?></p>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6"
                                        class="text-center px-5 py-5 border-b border-gray-200 bg-white text-sm">
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