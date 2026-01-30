

<?php $__env->startSection('title', 'QR Code สำหรับออเดอร์ ' . $order->ord_code . ' | Salepage Demo'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto p-4 min-h-screen flex items-center justify-center bg-gray-50">

        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

            
            <div class="bg-gradient-to-r from-[#00B900] to-[#06C755] p-6 text-center text-white relative overflow-hidden">
                <h1 class="font-bold text-2xl relative z-10">ชำระเงิน</h1>

                
                <p class="text-white font-medium text-lg mt-1 relative z-10">บริษัท กวินบราเทอร์</p>
                <p class="text-white/80 text-xs mt-0.5 relative z-10">ออเดอร์ <?php echo e($order->ord_code); ?></p>

                <div class="mt-4 bg-white/20 backdrop-blur-sm rounded-lg p-3 inline-block relative z-10">
                    <span class="block text-xs text-white/80">ยอดชำระทั้งหมด</span>
                    <span class="block text-3xl font-bold">฿<?php echo e(number_format($order->net_amount, 2)); ?></span>
                </div>
            </div>

            
            <div class="px-6 pt-6 pb-2">
                <?php
                    $bankName = 'ธนาคารกสิกรไทย';
                    $accNumber = '123-4-56789-0';
                    $accName = 'บจก. กวินบราเทอร์';
                ?>

                <div onclick="copyToClipboard('<?php echo e($accNumber); ?>')"
                    class="bg-blue-50 border border-blue-100 rounded-xl p-3 flex items-center justify-between cursor-pointer hover:bg-blue-100 hover:shadow-sm transition-all duration-200 group relative">

                    <div class="flex items-center gap-3">
                        <div class="bg-white p-2 rounded-full shadow-sm text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <p class="text-xs text-gray-500"><?php echo e($bankName); ?></p>
                            <p class="text-lg font-bold text-blue-900 font-mono tracking-wide"><?php echo e($accNumber); ?></p>
                            <p class="text-[10px] text-gray-400"><?php echo e($accName); ?></p>
                        </div>
                    </div>

                    <div class="text-gray-400 group-hover:text-blue-600 flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mb-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                        <span
                            class="text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity">คัดลอก</span>
                    </div>
                </div>
                <div class="text-center mt-2">
                    <p class="text-xs text-gray-400">หรือสแกน QR Code ด้านล่าง</p>
                </div>
            </div>

            <div class="p-6 pt-2">
                
                <div id="timer-container"
                    class="flex items-center justify-center gap-2 mb-6 text-gray-500 text-sm bg-red-50 py-2 rounded-full border border-red-100 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>QR Code จะหมดอายุใน</span>
                    <span id="countdown-timer" class="font-mono font-bold text-red-500 text-base">--:--</span>
                    <span>นาที</span>
                </div>

                
                <div id="expired-message"
                    class="hidden text-center mb-6 bg-gray-100 py-3 rounded-full text-gray-500 text-sm">
                    <span class="flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z"
                                clip-rule="evenodd" />
                        </svg>
                        หมดเวลาชำระเงินแล้ว
                    </span>
                </div>

                <div class="text-center animate-fade-in relative">
                    
                    <div class="bg-white p-4 rounded-xl border-2 border-dashed border-gray-300 inline-block mb-4 relative group transition-all duration-500"
                        id="qr-container">

                        
                        <img id="qr-code-image" src="data:image/svg+xml;base64,<?php echo e($qrCodeBase64); ?>" alt="PromptPay QR Code"
                            class="w-48 h-48 object-cover rounded-lg mx-auto transition-all duration-500">

                        <div class="mt-3 text-center">
                            
                            <p class="font-bold text-gray-800 text-lg">บริษัท กวินบราเทอร์</p>
                            <p class="text-xs text-gray-400 mt-1">สแกนเพื่อชำระเงิน</p>
                        </div>

                        
                        <div id="qr-overlay"
                            class="absolute inset-0 bg-white/90 backdrop-blur-sm z-10 flex flex-col items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
                            <p class="text-gray-500 font-bold mb-2">QR Code หมดอายุ</p>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mb-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        </div>
                    </div>

                    
                    <div id="save-btn-container" class="flex justify-center gap-3 mb-6 transition-all duration-300">
                        <button onclick="saveQRCode()"
                            class="btn btn-sm btn-outline gap-2 text-gray-600 border-gray-300 hover:bg-gray-50 hover:text-gray-800 hover:border-gray-400 font-normal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            บันทึกรูป
                        </button>
                    </div>

                    
                    <div id="refresh-btn-container" class="hidden mb-6">
                        <form action="<?php echo e(route('payment.refresh', $order->ord_code)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-outline btn-primary gap-2 w-full max-w-[200px]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                ขอ QR Code ใหม่
                            </button>
                        </form>
                        <p class="text-xs text-gray-400 mt-2">กดเพื่อเริ่มนับเวลาใหม่อีกครั้ง</p>
                    </div>
                </div>

                
                <button id="upload-slip-btn" onclick="showUploadSlipPopup()"
                    class="btn w-full bg-[#00B900] hover:bg-[#009900] text-white border-none text-lg h-12 shadow-md shadow-emerald-200 mb-3 transition-all duration-300">
                    แจ้งชำระเงิน / แนบสลิป
                </button>

                <a href="<?php echo e(route('order.history')); ?>"
                    class="btn btn-ghost btn-sm w-full text-gray-400 font-normal hover:bg-transparent hover:text-gray-600">
                    กลับไปที่ประวัติการสั่งซื้อ
                </a>
            </div>
        </div>
    </div>

    
    <form id="slip-upload-form" action="<?php echo e(route('payment.slip.upload', ['orderCode' => $order->ord_code])); ?>"
        method="POST" enctype="multipart/form-data" class="hidden">
        <?php echo csrf_field(); ?>
        <input type="file" id="slip_image_input" name="slip_image" accept="image/*" onchange="previewSlip(event)">
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        // รับค่าเวลาที่เหลือมาจาก Controller (วินาที)
        let timeLeft = <?php echo e($secondsRemaining); ?>;

        const timerElement = document.getElementById('countdown-timer');
        const timerContainer = document.getElementById('timer-container');
        const expiredMessage = document.getElementById('expired-message');
        const qrOverlay = document.getElementById('qr-overlay');
        const saveBtnContainer = document.getElementById('save-btn-container');
        const refreshBtnContainer = document.getElementById('refresh-btn-container');
        const uploadBtn = document.getElementById('upload-slip-btn');

        function updateTimerDisplay() {
            if (timeLeft <= 0) {
                handleExpired();
                return;
            }

            const minutes = Math.floor(timeLeft / 60);
            let seconds = Math.floor(timeLeft % 60);
            seconds = seconds < 10 ? '0' + seconds : seconds;

            timerElement.innerHTML = `${minutes}:${seconds}`;
            timeLeft--;
        }

        function handleExpired() {
            timerContainer.classList.add('hidden');
            expiredMessage.classList.remove('hidden');
            qrOverlay.classList.remove('opacity-0', 'pointer-events-none');
            saveBtnContainer.classList.add('hidden');
            refreshBtnContainer.classList.remove('hidden');

            uploadBtn.disabled = true;
            uploadBtn.classList.add('btn-disabled', 'bg-gray-300', 'text-gray-500');
            uploadBtn.classList.remove('bg-[#00B900]', 'hover:bg-[#009900]', 'shadow-md');
            uploadBtn.innerHTML = 'หมดเวลาดำเนินการ';
        }

        updateTimerDisplay();
        const countdown = setInterval(updateTimerDisplay, 1000);

        function saveQRCode() {
            const img = document.getElementById('qr-code-image');
            const link = document.createElement('a');
            link.href = img.src;
            link.download = 'QR-Payment-<?php echo e($order->ord_code); ?>.svg';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function copyToClipboard(text) {
            const cleanText = text;
            navigator.clipboard.writeText(cleanText).then(() => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: false,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: 'success',
                    title: 'คัดลอกเลขบัญชีแล้ว'
                });
            }).catch(err => {
                console.error('ไม่สามารถคัดลอกได้: ', err);
            });
        }

        function triggerFileInput() {
            document.getElementById('slip_image_input').click();
        }

        function previewSlip(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('slip-preview');
                if (output) {
                    output.src = reader.result;
                    output.classList.remove('hidden');
                    document.getElementById('slip-preview-placeholder').classList.add('hidden');
                }
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function showUploadSlipPopup() {
            Swal.fire({
                title: 'แนบสลิปชำระเงิน',
                html: `
                <div class="p-2">
                    <p class="text-sm text-gray-500 mb-4">กรุณาอัปโหลดหลักฐานการโอนเงินสำหรับออเดอร์ <br><strong class="text-gray-700"><?php echo e($order->ord_code); ?></strong></p>
                    <div id="slip-preview-container" class="w-full h-48 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed cursor-pointer" onclick="triggerFileInput()">
                         <div id="slip-preview-placeholder" class="text-center text-gray-400">
                            <svg class="mx-auto h-12 w-12" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                            <p class="mt-1 text-sm">คลิกเพื่อเลือกไฟล์</p>
                        </div>
                        <img id="slip-preview" class="hidden h-full w-full object-contain rounded-lg" />
                    </div>
                </div>`,
                showCancelButton: true,
                confirmButtonText: 'ยืนยันการแจ้งชำระ',
                confirmButtonColor: '#00B900',
                cancelButtonText: 'ยกเลิก',
                allowOutsideClick: false,
                preConfirm: () => {
                    const slipInput = document.getElementById('slip_image_input');
                    if (slipInput.files.length === 0) {
                        Swal.showValidationMessage('กรุณาเลือกไฟล์สลิป');
                        return false;
                    }
                    document.getElementById('slip-upload-form').submit();
                }
            });
        }
    </script>
    <style>
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/qr.blade.php ENDPATH**/ ?>