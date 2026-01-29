<?php $__env->startSection('title', 'Create Admin'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto px-4 sm:px-8 py-8">
        <div class="max-w-3xl mx-auto">
            
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900">สร้างผู้ดูแลระบบใหม่</h2>
                    <p class="mt-2 text-sm text-gray-600">กรอกข้อมูลเพื่อเพิ่มบัญชีผู้ดูแลระบบเข้าสู่ระบบ</p>
                </div>
                <a href="<?php echo e(route('admin.admins.index')); ?>"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition">
                    <i class="fas fa-arrow-left mr-2 -ml-1 text-gray-500"></i> ย้อนกลับ
                </a>
            </div>

            <form action="<?php echo e(route('admin.admins.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                    <div class="p-8 space-y-8">

                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                            
                            <div class="col-span-2 sm:col-span-1">
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">ชื่อ - นามสกุล
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>"
                                    autocomplete="name"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 text-gray-900 text-base shadow-sm focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 transition duration-150 ease-in-out <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 bg-red-50 text-red-900 focus:ring-red-500 focus:border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="เช่น สมชาย ใจดี" required>
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i
                                            class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            <div class="col-span-2 sm:col-span-1">
                                <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">ระดับสิทธิ์
                                    <span class="text-red-500">*</span></label>
                                <select id="role" name="role"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 text-gray-900 text-base shadow-sm focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 transition duration-150 ease-in-out appearance-none bg-no-repeat bg-right pr-10 <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 bg-red-50 text-red-900 focus:ring-red-500 focus:border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 20 20\'%3E%3Cpath stroke=\'%236b7280\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M6 8l4 4 4-4\'/%3E%3C/svg%3E'); background-position: right 1rem center; background-size: 1.5em 1.5em;"
                                    required>
                                    <option value="" disabled selected>กรุณาเลือกตำแหน่ง</option>
                                    <option value="admin" <?php echo e(old('role') == 'admin' ? 'selected' : ''); ?>>Admin
                                        (ผู้ดูแลทั่วไป)</option>
                                    <option value="superadmin" <?php echo e(old('role') == 'superadmin' ? 'selected' : ''); ?>>Super
                                        Admin (ผู้ดูแลสูงสุด)</option>
                                </select>
                                <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i
                                            class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            <div class="col-span-2">
                                <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">ชื่อผู้ใช้
                                    (Username) <span class="text-red-500">*</span></label>
                                <div class="relative rounded-md shadow-sm">
                                    <input type="text" name="username" id="username" value="<?php echo e(old('username')); ?>"
                                        autocomplete="username"
                                        class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 text-gray-900 text-base shadow-sm focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 transition duration-150 ease-in-out <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 bg-red-50 text-red-900 focus:ring-red-500 focus:border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="ภาษาอังกฤษและตัวเลขเท่านั้น" required>
                                </div>
                                <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i
                                            class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-span-2 border-b border-gray-100"></div>

                            
                            <div class="col-span-2 sm:col-span-1">
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">รหัสผ่าน <span
                                        class="text-red-500">*</span></label>
                                <input type="password" name="password" id="password"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 text-gray-900 text-base shadow-sm focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 transition duration-150 ease-in-out <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 bg-red-50 text-red-900 focus:ring-red-500 focus:border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="อย่างน้อย 8 ตัวอักษร" required>
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i
                                            class="fas fa-exclamation-circle mr-1"></i> <?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            <div class="col-span-2 sm:col-span-1">
                                <label for="password_confirmation"
                                    class="block text-sm font-semibold text-gray-700 mb-2">ยืนยันรหัสผ่าน <span
                                        class="text-red-500">*</span></label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 text-gray-900 text-base shadow-sm focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 transition duration-150 ease-in-out"
                                    placeholder="กรอกรหัสผ่านอีกครั้ง" required>
                            </div>
                        </div>

                        
                        <?php if(session('error')): ?>
                            <div class="rounded-lg bg-red-50 p-4 border-l-4 border-red-400">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-times-circle text-red-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">พบข้อผิดพลาด</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <p><?php echo e(session('error')); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>

                    
                    <div class="px-8 py-6 bg-gray-50 flex justify-end items-center space-x-4 border-t border-gray-100">
                        <a href="<?php echo e(route('admin.admins.index')); ?>"
                            class="px-6 py-3 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition shadow-sm">
                            ยกเลิก
                        </a>
                        <button type="submit"
                            class="px-6 py-3 bg-indigo-600 border border-transparent rounded-lg text-white font-bold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 shadow-md transition flex items-center">
                            <i class="fas fa-save mr-2"></i> บันทึกข้อมูล
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/admins/create.blade.php ENDPATH**/ ?>