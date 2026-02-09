<?php $__env->startSection('title', 'Edit Admin'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto px-4 sm:px-8 py-8">
        <div class="max-w-3xl mx-auto">
            
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-100">แก้ไขข้อมูลผู้ดูแลระบบ</h2>
                    <p class="mt-2 text-sm text-gray-400">แก้ไขรายละเอียดหรือเปลี่ยนรหัสผ่านของ: <span
                            class="font-semibold text-indigo-400"><?php echo e($admin->username); ?></span></p>
                </div>
                <a href="<?php echo e(route('admin.admins.index')); ?>"
                    class="inline-flex items-center px-4 py-2 border border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-300 bg-gray-800 hover:bg-gray-700 hover:text-white focus:outline-none transition">
                    <i class="fas fa-arrow-left mr-2 -ml-1 text-gray-500 group-hover:text-white"></i> ย้อนกลับ
                </a>
            </div>

            <form action="<?php echo e(route('admin.admins.update', $admin->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="bg-gray-800 shadow-xl rounded-2xl overflow-hidden border border-gray-700">
                    <div class="p-8 space-y-8">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                            
                            <div class="col-span-2 sm:col-span-1">
                                <label for="name" class="block text-sm font-semibold text-gray-300 mb-2">ชื่อ - นามสกุล
                                    <span class="text-red-400">*</span></label>
                                <input type="text" name="name" id="name" value="<?php echo e(old('name', $admin->name)); ?>"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100 text-base shadow-sm focus:bg-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 transition duration-150 ease-in-out placeholder-gray-500 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 bg-red-900/20 text-red-200 focus:ring-red-500 focus:border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                                            class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div class="col-span-2 sm:col-span-1">
                                <label for="role" class="block text-sm font-semibold text-gray-300 mb-2">ระดับสิทธิ์
                                    <span class="text-red-400">*</span></label>
                                <select id="role" name="role"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100 text-base shadow-sm focus:bg-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 transition duration-150 ease-in-out appearance-none bg-no-repeat bg-right pr-10 <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 bg-red-900/20 text-red-200 focus:ring-red-500 focus:border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 20 20\'%3E%3Cpath stroke=\'%239ca3af\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M6 8l4 4 4-4\'/%3E%3C/svg%3E'); background-position: right 1rem center; background-size: 1.5em 1.5em;"
                                    required>
                                    <option value="admin" <?php echo e(old('role', $admin->role) === 'admin' ? 'selected' : ''); ?>

                                        class="bg-gray-800">
                                        Admin (ผู้ดูแลทั่วไป)</option>
                                    <option value="superadmin"
                                        <?php echo e(old('role', $admin->role) === 'superadmin' ? 'selected' : ''); ?>

                                        class="bg-gray-800">Super Admin
                                        (ผู้ดูแลสูงสุด)</option>
                                </select>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                                            class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div class="col-span-2 sm:col-span-1">
                                <label for="username" class="block text-sm font-semibold text-gray-300 mb-2">ชื่อผู้ใช้
                                    (Username) <span class="text-red-400">*</span></label>
                                <input type="text" name="username" id="username"
                                    value="<?php echo e(old('username', $admin->username)); ?>"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100 text-base shadow-sm focus:bg-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 transition duration-150 ease-in-out placeholder-gray-500 <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 bg-red-900/20 text-red-200 focus:ring-red-500 focus:border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                                            class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div class="col-span-2 sm:col-span-1">
                                <label for="admin_code" class="block text-sm font-semibold text-gray-300 mb-2">รหัสประจำตัว
                                </label>
                                <input type="text" name="admin_code" id="admin_code"
                                    value="<?php echo e(old('admin_code', $admin->admin_code)); ?>"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-600 text-gray-400 text-base shadow-sm cursor-not-allowed"
                                    readonly>
                            </div>

                            <div class="col-span-2 border-b border-gray-700"></div>

                            
                            <div class="col-span-2">
                                <h3 class="text-lg font-bold text-gray-100 flex items-center">
                                    <i class="fas fa-lock mr-2 text-indigo-400"></i> เปลี่ยนรหัสผ่าน
                                </h3>
                                <p class="mt-1 text-sm text-gray-400">เว้นว่างไว้ทั้งสองช่องหากไม่ต้องการเปลี่ยนรหัสผ่านเดิม
                                </p>
                            </div>

                            
                            <div class="col-span-2 sm:col-span-1">
                                <label for="password"
                                    class="block text-sm font-semibold text-gray-300 mb-2">รหัสผ่านใหม่</label>
                                <input type="password" name="password" id="password"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100 text-base shadow-sm focus:bg-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 transition duration-150 ease-in-out placeholder-gray-500 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 bg-red-900/20 text-red-200 focus:ring-red-500 focus:border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="อย่างน้อย 8 ตัวอักษร">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                                            class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div class="col-span-2 sm:col-span-1">
                                <label for="password_confirmation"
                                    class="block text-sm font-semibold text-gray-300 mb-2">ยืนยันรหัสผ่านใหม่</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100 text-base shadow-sm focus:bg-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 transition duration-150 ease-in-out placeholder-gray-500"
                                    placeholder="กรอกรหัสผ่านใหม่ซ้ำอีกครั้ง">
                            </div>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
                            <div class="rounded-lg bg-red-900/50 p-4 border-l-4 border-red-500">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-times-circle text-red-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-300">พบข้อผิดพลาด</h3>
                                        <div class="mt-2 text-sm text-red-200">
                                            <p><?php echo e(session('error')); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="px-8 py-6 bg-gray-900/50 flex justify-end items-center space-x-4 border-t border-gray-700">
                        <a href="<?php echo e(route('admin.admins.index')); ?>"
                            class="px-6 py-3 bg-gray-700 border border-gray-600 rounded-lg text-gray-300 font-medium hover:bg-gray-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition shadow-sm">
                            ยกเลิก
                        </a>
                        <button type="submit"
                            class="px-6 py-3 bg-indigo-600 border border-transparent rounded-lg text-white font-bold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 shadow-md shadow-indigo-900/30 transition flex items-center">
                            <i class="fas fa-save mr-2"></i> บันทึกการแก้ไข
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/admins/edit.blade.php ENDPATH**/ ?>