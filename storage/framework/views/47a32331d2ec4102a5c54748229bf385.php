<?php $__env->startSection('title', 'แก้ไขข้อมูลส่วนตัว'); ?>

<?php $__env->startSection('content'); ?>
    <div class="min-h-[80vh] bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center mb-6">
            <h2 class="text-3xl font-extrabold text-gray-900">
                แก้ไขข้อมูลส่วนตัว
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                อัปเดตข้อมูลส่วนตัวของคุณ
            </p>
        </div>

        
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-xl rounded-xl sm:px-10 border border-gray-100">

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <span class="block sm:inline"><?php echo e(session('success')); ?></span>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <form class="space-y-6" action="<?php echo e(route('profile.update')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            ชื่อ-นามสกุล (Full Name)
                        </label>
                        <div class="relative">
                            <input type="text" name="name" id="name" required
                                value="<?php echo e(old('name', auth()->user()->name)); ?>"
                                class="block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm transition duration-200 ease-in-out"
                                placeholder="กรอกชื่อ-นามสกุล">
                        </div>
                    </div>

                    
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">
                            วันเดือนปีเกิด (Date of Birth)
                        </label>
                        <div class="relative">
                            <?php
                                $dob = auth()->user()->date_of_birth;
                                // ตรวจสอบว่า $dob เป็น Object (Carbon) หรือ String แล้วแปลงให้เป็น Y-m-d
                                $dobValue = $dob ? \Carbon\Carbon::parse($dob)->format('Y-m-d') : '';
                            ?>
                            <input type="date" name="date_of_birth" id="date_of_birth" max="<?php echo e(date('Y-m-d')); ?>"
                                value="<?php echo e(old('date_of_birth', $dobValue)); ?>"
                                class="block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm transition duration-200 ease-in-out"
                                placeholder="Select date">
                        </div>
                    </div>

                    
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">
                            เพศ (Gender)
                        </label>
                        <select id="gender" name="gender"
                            class="block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm bg-white transition duration-200 ease-in-out">
                            <option value="" disabled <?php echo e(!auth()->user()->gender ? 'selected' : ''); ?>>กรุณาเลือกเพศ
                            </option>
                            <option value="male" <?php echo e(old('gender', auth()->user()->gender) == 'male' ? 'selected' : ''); ?>>
                                ชาย (Male)</option>
                            <option value="female"
                                <?php echo e(old('gender', auth()->user()->gender) == 'female' ? 'selected' : ''); ?>>หญิง (Female)
                            </option>
                            <option value="other" <?php echo e(old('gender', auth()->user()->gender) == 'other' ? 'selected' : ''); ?>>
                                อื่นๆ (Other)</option>
                        </select>
                    </div>

                    
                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700 mb-1">
                            อายุ (Age)
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <input type="number" name="age" id="age" readonly
                                value="<?php echo e(old('age', auth()->user()->age)); ?>"
                                class="block w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-100 text-gray-500 sm:text-sm cursor-not-allowed focus:outline-none"
                                placeholder="ระบบคำนวณอัตโนมัติ">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">*อายุจะถูกคำนวณอัตโนมัติจากวันเกิด</p>
                    </div>

                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            เบอร์โทรศัพท์ (Phone Number)
                        </label>
                        <div class="relative">
                            <input type="tel" name="phone" id="phone" required
                                value="<?php echo e(old('phone', auth()->user()->phone)); ?>"
                                class="block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm transition duration-200 ease-in-out"
                                placeholder="กรอกเบอร์โทรศัพท์">
                        </div>
                    </div>

                    
                    <div class="pt-2">
                        <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-300 transform hover:-translate-y-0.5">
                            บันทึกข้อมูล (Save Profile)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dobInput = document.getElementById('date_of_birth');
            const ageInput = document.getElementById('age');

            const calculateAge = () => {
                if (!dobInput.value) {
                    // ถ้าไม่มีค่าวันเกิด ให้ใช้ค่าเดิมจาก PHP (ถ้ามี) หรือปล่อยว่าง
                    if (!ageInput.value) ageInput.value = '';
                    return;
                };

                const dob = new Date(dobInput.value);
                const today = new Date();

                if (dob > today) {
                    dobInput.value = '';
                    ageInput.value = '';
                    return;
                }

                let age = today.getFullYear() - dob.getFullYear();
                const monthDiff = today.getMonth() - dob.getMonth();

                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }

                ageInput.value = age > 0 ? age : 0;
            }

            dobInput.addEventListener('change', calculateAge);

            // คำนวณทันทีเมื่อโหลดหน้า (กรณีมีข้อมูลเดิมอยู่แล้ว)
            if (dobInput.value) {
                calculateAge();
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/profile/edit.blade.php ENDPATH**/ ?>