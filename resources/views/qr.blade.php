@extends('layout')

@section('title', 'ชำระเงิน ออเดอร์ ' . $order->ord_code . ' | Salepage Demo')

@section('content')
    <div class="min-h-screen py-6 sm:py-8 px-4 sm:px-6 lg:px-8 flex items-center justify-center font-sans">

        {{-- Main Container (ปรับเป็น flex-col สำหรับมือถือ และ lg:flex-row สำหรับจอใหญ่ พร้อมคุมความกว้างในแต่ละหน้าจอ) --}}
        <div
            class="w-full max-w-md md:max-w-2xl lg:max-w-5xl mx-auto bg-white rounded-[2rem] shadow-2xl overflow-hidden flex flex-col lg:flex-row border border-gray-200">

            {{-- ================= ฝั่งซ้าย: ข้อมูลออเดอร์ และ QR Code ================= --}}
            <div
                class="w-full lg:w-1/2 flex flex-col bg-gray-50 border-b lg:border-b-0 lg:border-r border-gray-200 relative">

                {{-- Header Section (ยอดเงินและเลขออเดอร์) --}}
                <div
                    class="bg-gradient-to-br from-red-600 to-red-800 p-6 sm:p-8 text-center text-white relative overflow-hidden shrink-0">
                    <div class="absolute inset-0 opacity-10 pointer-events-none">
                        <svg width="100%" height="100%">
                            <pattern id="pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                                <circle cx="20" cy="20" r="1.5" fill="white" />
                            </pattern>
                            <rect width="100%" height="100%" fill="url(#pattern)" />
                        </svg>
                    </div>

                    <div class="relative z-10">
                        <span
                            class="inline-block px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-medium tracking-wider mb-2 sm:mb-3 border border-white/30">
                            ออเดอร์ {{ $order->ord_code }}
                        </span>
                        <h1 class="text-xs sm:text-sm font-medium text-red-100 mb-1">ยอดชำระเงินทั้งหมด</h1>

                        <div class="flex items-center justify-center gap-3">
                            <span
                                class="text-3xl sm:text-4xl lg:text-5xl font-black tabular-nums tracking-tight">฿{{ number_format($order->net_amount, 2) }}</span>
                        </div>
                        <p class="text-[10px] sm:text-xs text-red-200 mt-2 font-medium">บริษัท กวินบราเทอร์</p>
                    </div>
                </div>

                {{-- QR Code Section --}}
                <div class="p-6 sm:p-8 flex-grow flex flex-col items-center justify-center">

                    {{-- Timer --}}
                    <div id="timer-container"
                        class="flex items-center justify-center gap-2 sm:gap-3 mb-5 sm:mb-6 bg-red-100/50 px-4 sm:px-6 py-2.5 sm:py-3 rounded-full border border-red-200 w-full max-w-sm">
                        <i class="fas fa-stopwatch text-red-500 text-base sm:text-lg animate-pulse"></i>
                        <div class="text-center flex items-baseline gap-1.5 sm:gap-2">
                            <span class="text-xs sm:text-sm font-bold text-red-500">กรุณาชำระภายใน</span>
                            <span id="countdown-timer"
                                class="font-mono font-black text-red-600 text-lg sm:text-xl tabular-nums">--:--</span>
                            <span class="text-xs sm:text-sm font-bold text-red-500">นาที</span>
                        </div>
                    </div>

                    <div id="expired-message"
                        class="hidden text-center mb-5 sm:mb-6 w-full max-w-sm bg-gray-100 py-4 rounded-2xl border-2 border-dashed border-gray-300">
                        <i class="fas fa-clock text-gray-400 text-2xl sm:text-3xl mb-2"></i>
                        <p class="text-xs sm:text-sm font-bold text-gray-600">QR Code หมดอายุแล้ว</p>
                        <p class="text-[10px] sm:text-xs text-gray-400 mt-1">กรุณาทำรายการสั่งซื้อใหม่อีกครั้ง</p>
                    </div>

                    {{-- QR Image Box --}}
                    <div class="text-center relative w-full flex flex-col items-center" id="qr-container">
                        <div
                            class="inline-block bg-white p-4 lg:p-5 rounded-3xl border-2 border-gray-200 shadow-sm relative group">
                            <img id="qr-code-image" src="data:image/svg+xml;base64,{{ $qrCodeBase64 }}"
                                alt="PromptPay QR Code" class="w-40 h-40 sm:w-48 sm:h-48 lg:w-56 lg:h-56 object-cover rounded-xl mx-auto">

                            <div id="qr-overlay"
                                class="absolute inset-0 bg-white/90 backdrop-blur-sm z-10 flex flex-col items-center justify-center opacity-0 pointer-events-none transition-all duration-300 rounded-3xl">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-100 rounded-full flex items-center justify-center mb-2">
                                    <i class="fas fa-times text-red-500 text-lg sm:text-xl"></i>
                                </div>
                                <p class="text-sm sm:text-base text-gray-800 font-bold">หมดเวลาชำระเงิน</p>
                            </div>

                            <div class="mt-3 sm:mt-4 flex flex-col items-center justify-center gap-1">
                                <img src="{{ asset('images/ci-qrpayment-img-01.png') }}" class="h-5 sm:h-6 lg:h-7 object-contain"
                                    alt="Thai QR Payment" onerror="this.style.display='none'">
                            </div>
                        </div>

                        <div id="save-btn-container" class="mt-5 sm:mt-6">
                            <button onclick="saveQRCode()"
                                class="text-xs sm:text-sm font-bold text-gray-600 hover:text-gray-900 bg-white hover:bg-gray-100 border border-gray-200 px-5 sm:px-6 py-2 sm:py-2.5 rounded-full transition-colors flex items-center justify-center gap-2 shadow-sm">
                                <i class="fas fa-download"></i> บันทึกรูป QR Code
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= ฝั่งขวา: โอนธนาคาร และ ปุ่มจัดการออเดอร์ ================= --}}
            <div class="w-full lg:w-1/2 p-6 sm:p-8 lg:p-12 flex flex-col justify-center bg-white">

                <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-1 sm:mb-2">หรือโอนผ่านบัญชีธนาคาร</h2>
                <p class="text-xs sm:text-sm text-gray-500 mb-5 sm:mb-6">คุณสามารถโอนเงินเข้าบัญชีด้านล่าง และแนบสลิปเพื่อยืนยัน</p>

                {{-- Bank Account Block --}}
                @php
                    $bankName = 'ธนาคารกสิกรไทย';
                    $accNumber = '123-4-56789-0';
                    $accName = 'บจก. กวินบราเทอร์';
                @endphp
                <div onclick="copyToClipboard('{{ $accNumber }}', 'เลขบัญชี')"
                    class="bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 sm:p-5 flex items-center justify-between cursor-pointer hover:border-red-300 hover:bg-red-50/50 transition-all duration-300 group mb-6 sm:mb-8 shadow-sm">
                    <div class="flex items-center gap-3 sm:gap-4 lg:gap-5">
                        <div
                            class="w-12 h-12 sm:w-14 sm:h-14 bg-green-600 rounded-2xl flex items-center justify-center text-white shadow-md">
                            <i class="fas fa-leaf text-xl sm:text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] sm:text-xs font-bold text-gray-500 mb-0.5">{{ $bankName }}</p>
                            <p class="text-lg sm:text-xl lg:text-2xl font-black text-gray-800 tracking-tight">{{ $accNumber }}</p>
                            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">{{ $accName }}</p>
                        </div>
                    </div>
                    <div
                        class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white border border-gray-200 text-gray-400 group-hover:text-red-500 group-hover:border-red-200 flex items-center justify-center shadow-sm shrink-0">
                        <i class="fas fa-copy text-sm sm:text-base"></i>
                    </div>
                </div>

                <hr class="border-gray-100 mb-6 sm:mb-8">

                {{-- Action Buttons --}}
                <div class="space-y-3 sm:space-y-4">
                    <button id="upload-slip-btn"
                        class="w-full h-14 sm:h-16 bg-red-600 hover:bg-red-700 text-white rounded-xl sm:rounded-2xl font-bold text-base sm:text-lg shadow-xl shadow-red-500/20 transition-all transform hover:-translate-y-1 active:translate-y-0 flex items-center justify-center gap-2 sm:gap-3">
                        <i class="fas fa-file-invoice-dollar text-lg sm:text-xl"></i>
                        แจ้งชำระเงิน / แนบสลิป
                    </button>

                    <form id="cancel-form" action="{{ route('payment.cancel', ['orderCode' => $order->ord_code]) }}"
                        method="POST">
                        @csrf
                        <button type="button" onclick="confirmCancel()"
                            class="w-full h-12 sm:h-14 bg-white border-2 border-gray-200 text-gray-500 hover:bg-red-50 hover:border-red-200 hover:text-red-600 rounded-xl sm:rounded-2xl text-sm sm:text-base font-bold transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-times"></i>
                            ยกเลิกคำสั่งซื้อนี้
                        </button>
                    </form>
                </div>

                <div class="mt-6 sm:mt-8 text-center">
                    <a href="{{ route('orders.index') }}"
                        class="text-xs sm:text-sm font-bold text-gray-400 hover:text-gray-700 transition-colors inline-flex items-center gap-1.5 sm:gap-2">
                        <i class="fas fa-arrow-left"></i> กลับไปหน้าประวัติการสั่งซื้อ
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- Hidden form for slip upload --}}
    <form id="slip-upload-form" action="{{ route('payment.slip.upload', ['orderCode' => $order->ord_code]) }}"
        method="POST" enctype="multipart/form-data" class="hidden">
        @csrf
        <input type="file" id="slip_image_input" name="slip_image" accept="image/*" onchange="previewSlip(event)">
    </form>
@endsection

@section('scripts')
    {{-- สคริปต์ยังคงเหมือนเดิมทุกประการ เพื่อให้ระบบทำงานได้เสถียรครับ --}}
    <script>
        window.copyToClipboard = function(text, label = 'ข้อมูล') {
            navigator.clipboard.writeText(text).then(() => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                });
                Toast.fire({
                    icon: 'success',
                    title: `คัดลอก${label}เรียบร้อย`,
                });
            });
        }

        window.confirmCancel = function() {
            Swal.fire({
                title: 'ยกเลิกคำสั่งซื้อ?',
                text: "คุณแน่ใจหรือไม่ว่าต้องการยกเลิกออเดอร์ {{ $order->ord_code }}?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#e5e7eb',
                confirmButtonText: 'ใช่, ยกเลิกเลย',
                cancelButtonText: '<span class="text-gray-700">กลับไปชำระเงิน</span>',
                reverseButtons: true,
                borderRadius: '1.5rem',
                customClass: {
                    cancelButton: 'text-gray-800'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('cancel-form').submit();
                }
            });
        }

        window.triggerFileInput = function() {
            document.getElementById('slip_image_input').click();
        }

        window.previewSlip = function(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('slip-preview');
                const placeholder = document.getElementById('slip-preview-placeholder');
                if (output && placeholder) {
                    output.src = reader.result;
                    output.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
            };
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        window.saveQRCode = function() {
            const img = document.getElementById('qr-code-image');
            const link = document.createElement('a');
            link.href = img.src;
            link.download = 'QR-Payment-{{ $order->ord_code }}.svg';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        document.addEventListener('DOMContentLoaded', function() {
            let timeLeft = {{ $secondsRemaining }};
            const timerElement = document.getElementById('countdown-timer');
            const timerContainer = document.getElementById('timer-container');
            const expiredMessage = document.getElementById('expired-message');
            const qrOverlay = document.getElementById('qr-overlay');
            const saveBtnContainer = document.getElementById('save-btn-container');

            function updateTimerDisplay() {
                if (timeLeft <= 0) {
                    timerContainer.classList.add('hidden');
                    expiredMessage.classList.remove('hidden');
                    qrOverlay.classList.remove('opacity-0', 'pointer-events-none');
                    if (saveBtnContainer) saveBtnContainer.classList.add('hidden');

                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                    return;
                }
                const minutes = Math.floor(timeLeft / 60);
                let seconds = Math.floor(timeLeft % 60);
                seconds = seconds < 10 ? '0' + seconds : seconds;
                timerElement.innerHTML = `${minutes}:${seconds}`;
                timeLeft--;
            }

            if (timeLeft > 0) {
                setInterval(updateTimerDisplay, 1000);
                updateTimerDisplay();
            } else {
                updateTimerDisplay();
            }

            const uploadBtn = document.getElementById('upload-slip-btn');
            if (uploadBtn) {
                uploadBtn.addEventListener('click', function() {
                    Swal.fire({
                        title: 'ยืนยันการชำระเงิน',
                        html: `
                            <div class="p-2">
                                <p class="text-sm text-gray-600 mb-4">ยอดที่ต้องชำระ <span class="font-bold text-red-600 text-lg">฿{{ number_format($order->net_amount, 2) }}</span></p>
                                <div id="slip-preview-container" 
                                     class="w-full h-64 bg-gray-50 rounded-2xl flex flex-col items-center justify-center border-2 border-dashed border-gray-300 cursor-pointer hover:border-red-400 hover:bg-red-50 transition-all overflow-hidden" 
                                     onclick="window.triggerFileInput()">
                                    
                                    <div id="slip-preview-placeholder" class="text-center text-gray-400 p-4">
                                        <div class="w-14 h-14 bg-white rounded-full shadow-sm flex items-center justify-center mx-auto mb-3">
                                            <i class="fas fa-image text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="font-bold text-sm text-gray-600">แตะเพื่อเลือกรูปสลิป</p>
                                        <p class="text-xs mt-1">รองรับ JPG, PNG</p>
                                    </div>
                                    
                                    <img id="slip-preview" class="hidden h-full w-full object-contain bg-gray-900/5" />
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'อัปโหลดสลิป',
                        confirmButtonColor: '#dc2626',
                        cancelButtonText: 'ยกเลิก',
                        borderRadius: '1.5rem',
                        preConfirm: () => {
                            if (document.getElementById('slip_image_input').files.length ===
                                0) {
                                Swal.showValidationMessage(
                                    'กรุณาแนบรูปสลิปชำระเงินก่อนกดยืนยันครับ');
                                return false;
                            }
                            Swal.getConfirmButton().innerHTML =
                                '<i class="fas fa-spinner fa-spin mr-2"></i> กำลังอัปโหลด...';
                            Swal.getConfirmButton().disabled = true;
                            document.getElementById('slip-upload-form').submit();
                        }
                    });
                });
            }
        });
    </script>
@endsection