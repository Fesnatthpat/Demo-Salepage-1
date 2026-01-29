<?php $__env->startSection('title', 'Admin Login | Salepage Demo'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4">
    <div class="flex justify-center items-center min-h-screen py-10">
        <div class="w-full max-w-sm bg-white p-8 rounded-xl shadow-lg border border-gray-100 text-center">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Admin Login</h2>
                <p class="text-gray-500 text-sm mt-2">Please log in to continue.</p>
            </div>

            <form method="POST" action="<?php echo e(route('admin.login')); ?>">
                <?php echo csrf_field(); ?>
                <div class="mb-4">
                    <input type="text" name="username" id="username" placeholder="Username"
                        class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required autofocus>
                    <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-6">
                    <input type="password" name="password" id="password" placeholder="Password"
                        class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                    Login
                </button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/auth/login.blade.php ENDPATH**/ ?>