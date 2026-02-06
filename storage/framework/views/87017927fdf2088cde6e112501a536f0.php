<?php $__env->startSection('title', 'Admin Management'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto px-4 sm:px-8 py-8">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-100">ผู้ดูแลระบบ (Admins)</h2>
                <p class="text-gray-400 text-sm mt-1">จัดการรายชื่อและสิทธิ์การเข้าใช้งานของผู้ดูแลระบบ</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="<?php echo e(route('admin.admins.create')); ?>"
                    class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg shadow-indigo-900/20 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i> เพิ่มผู้ดูแลระบบ
                </a>
            </div>
        </div>

        
        <?php if(session('success')): ?>
            <div class="mb-4 bg-green-900/50 border-l-4 border-green-500 p-4 rounded-md shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-200"><?php echo e(session('success')); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="mb-4 bg-red-900/50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-200"><?php echo e(session('error')); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        
        <div class="bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal text-gray-300">
                    <thead>
                        <tr
                            class="bg-gray-900/50 border-b border-gray-700 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            <th class="px-5 py-3">ชื่อ - นามสกุล</th>
                            <th class="px-5 py-3">ชื่อผู้ใช้ (Username)</th>
                            <th class="px-5 py-3">รหัสประจำตัว</th>
                            <th class="px-5 py-3 text-center">ระดับสิทธิ์ (Role)</th>
                            <th class="px-5 py-3">วันที่สร้าง</th>
                            <th class="px-5 py-3 text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php $__empty_1 = true; $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-700/50 transition duration-150 border-b border-gray-700 last:border-0">
                                <td class="px-5 py-4 text-sm">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-indigo-900/50 border border-indigo-700 flex items-center justify-center text-indigo-300 font-bold text-lg uppercase">
                                                <?php echo e(substr($admin->name, 0, 1)); ?>

                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-gray-100 font-medium whitespace-no-wrap">
                                                <?php echo e($admin->name); ?>

                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-300">
                                    <?php echo e($admin->username); ?>

                                </td>
                                <td class="px-5 py-4 text-sm text-gray-400 font-mono">
                                    <?php echo e($admin->admin_code); ?>

                                </td>
                                <td class="px-5 py-4 text-sm text-center">
                                    <?php if($admin->role === 'superadmin'): ?>
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-900/50 text-purple-200 border border-purple-800">
                                            Super Admin
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-900/50 text-blue-200 border border-blue-800">
                                            Admin
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500">
                                    <?php echo e($admin->created_at->format('d M Y')); ?>

                                </td>
                                <td class="px-5 py-4 text-sm text-center">
                                    <div class="flex justify-center space-x-3">
                                        <a href="<?php echo e(route('admin.admins.edit', $admin->id)); ?>"
                                            class="text-indigo-400 hover:text-indigo-300 transition" title="Edit">
                                            <i class="fas fa-edit text-lg"></i>
                                        </a>
                                        <form action="<?php echo e(route('admin.admins.destroy', $admin->id)); ?>" method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('ยืนยันการลบผู้ดูแลระบบรายนี้?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit"
                                                class="text-red-400 hover:text-red-300 transition focus:outline-none"
                                                title="Delete">
                                                <i class="fas fa-trash-alt text-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-5 py-12 border-b border-gray-700 text-center text-gray-500">
                                    <div class="flex flex-col items-center opacity-60">
                                        <i class="fas fa-user-shield text-4xl mb-3 text-gray-600"></i>
                                        ไม่พบข้อมูลผู้ดูแลระบบ
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/admins/index.blade.php ENDPATH**/ ?>