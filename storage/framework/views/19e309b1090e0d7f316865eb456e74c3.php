<?php $__env->startSection('title', 'Admin Login | Salepage Demo'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto px-4 bg-gray-900 min-h-screen">
        <div class="flex justify-center items-center min-h-screen py-10">
            <div class="w-full max-w-sm bg-gray-800 p-8 rounded-2xl shadow-2xl border border-gray-700 text-center">
                <div class="mb-8">
                    <h2 class="text-3xl font-extrabold text-white">Admin Login</h2>
                    <p class="text-gray-400 text-sm mt-2">Sign in to manage your dashboard</p>
                </div>

                <form method="POST" action="<?php echo e(route('admin.login')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-4 text-left">
                        <label class="text-gray-300 text-xs mb-1 ml-1 block">Username</label>
                        <input type="text" name="username" id="username" placeholder="Enter username"
                            class="w-full px-4 py-3 rounded-xl bg-gray-700 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            required autofocus>
                        <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-6 text-left">
                        <label class="text-gray-300 text-xs mb-1 ml-1 block">Password</label>
                        <input type="password" name="password" id="password" placeholder="••••••••"
                            class="w-full px-4 py-3 rounded-xl bg-gray-700 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            required>
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded-xl transition duration-300 shadow-lg shadow-indigo-500/20">
                        Sign In
                    </button>
                </form>

                <div class="mt-8">
                    <p class="text-gray-500 text-xs">© <?php echo e(date('Y')); ?> Salepage Demo System</p>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/auth/login.blade.php ENDPATH**/ ?>