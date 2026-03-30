@extends('layout')

@section('title', 'QR Code สำหรับออเดอร์ ' . $order->ord_code . ' | Salepage Demo')

@section('content')
    <div class="container mx-auto p-4 min-h-screen flex items-center justify-center ">

        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-red-600 to-red-800 p-6 text-center text-white relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                    <svg width="100%" height="100%">
                        <pattern id="pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                            <circle cx="20" cy="20" r="1" fill="white" />
                        </pattern>
                        <rect width="100%" height="100%" fill="url(#pattern)" />
                    </svg>
                </div>
                <h1 class="font-bold text-2xl relative z-10 tracking-tight">ชำระเงิน</h1>
                <p class="text-white/90 font-medium text-base mt-1 relative z-10">บริษัท กวินบราเทอร์</p>
                <div class="flex items-center justify-center gap-2 mt-1 relative z-10 opacity-75">
                    <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-full border border-white/30">ออเดอร์
                        {{ $order->ord_code }}</span>
                </div>

                <div
                    class="mt-5 bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 inline-block relative z-10 shadow-inner">
                    <span
                        class="block text-[10px] text-white/70 uppercase tracking-widest font-bold mb-1">ยอดชำระทั้งหมด</span>
                    <div class="flex items-center justify-center gap-2">
                        <span class="text-3xl font-black tabular-nums">฿{{ number_format($order->net_amount, 2) }}</span>
                        <button onclick="copyToClipboard('{{ $order->net_amount }}', 'ยอดเงิน')"
                            class="w-6 h-6 bg-white/20 rounded-lg flex items-center justify-center hover:bg-white/40 transition-colors"
                            title="คัดลอกยอดเงิน">
                            <i class="fas fa-copy text-[10px]"></i>
                        </button>
                    </div>
                </div>
            </div>

            

            {{-- ส่วนแสดงเลขบัญชีธนาคาร --}}
            <div class="px-6 pt-6 pb-2">
                @php
                    $bankName = 'ธนาคารกสิกรไทย';
                    $accNumber = '123-4-56789-0';
                    $accName = 'บจก. กวินบราเทอร์';
                @endphp

                <div onclick="copyToClipboard('{{ $accNumber }}', 'เลขบัญชี')"
                    class="bg-white border-2 border-gray-100 rounded-2xl p-4 flex items-center justify-between cursor-pointer hover:border-red-200 hover:bg-red-50/30 transition-all duration-300 group shadow-sm">

                    <div class="flex items-center gap-4">
                        <div
                            class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center text-white shadow-md">
                            <i class="fas fa-university text-lg"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $bankName }}</p>
                            <p class="text-lg font-black text-gray-800 font-mono tracking-tighter">{{ $accNumber }}</p>
                            <p class="text-[10px] text-gray-500 font-medium">{{ $accName }}</p>
                        </div>
                    </div>

                    <div
                        class="w-8 h-8 rounded-lg bg-gray-100 text-gray-400 group-hover:bg-red-100 group-hover:text-red-600 flex items-center justify-center transition-all">
                        <i class="fas fa-copy text-sm"></i>
                    </div>
                </div>
                <div class="relative flex items-center justify-center my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <span
                        class="relative px-3 bg-white text-[10px] font-bold text-gray-400 uppercase tracking-widest">หรือสแกนคิวอาร์</span>
                </div>
            </div>

            <div class="p-6 pt-0">
                {{-- Countdown Timer --}}
                <div id="timer-container"
                    class="flex items-center justify-center gap-3 mb-6 bg-red-50/50 py-3 rounded-2xl border border-red-100 shadow-inner group">
                    <div
                        class="flex items-center justify-center w-8 h-8 bg-white rounded-full shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fas fa-hourglass-half text-red-500 text-xs animate-pulse"></i>
                    </div>
                    <div class="flex flex-col">
                        <span
                            class="text-[10px] font-bold text-red-400 uppercase tracking-wider leading-none mb-1">จะหมดอายุใน</span>
                        <span id="countdown-timer"
                            class="font-mono font-black text-red-600 text-xl leading-none">--:--</span>
                    </div>
                    <span class="text-xs font-bold text-red-400">นาที</span>
                </div>

                <div id="expired-message"
                    class="hidden text-center mb-6 bg-gray-100 py-4 rounded-2xl border-2 border-dashed border-gray-300">
                    <div class="flex flex-col items-center gap-2">
                        <i class="fas fa-clock text-gray-400 text-2xl"></i>
                        <p class="text-sm font-bold text-gray-500">QR Code หมดอายุแล้ว</p>
                    </div>
                </div>

                <div class="text-center animate-fade-in relative">
                    <div class="bg-white p-5 rounded-3xl border-2 border-gray-100 inline-block mb-6 relative group transition-all duration-500 shadow-sm"
                        id="qr-container">
                        <div class="relative">
                            <img id="qr-code-image" src="data:image/svg+xml;base64,{{ $qrCodeBase64 }}"
                                alt="PromptPay QR Code"
                                class="w-52 h-52 object-cover rounded-xl mx-auto transition-all duration-500 group-hover:scale-[1.02]">
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-10">
                                <i class="fas fa-qrcode text-6xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <div class="flex items-center justify-center gap-2 mb-1">
                                <img src="{{ asset('images/ci-qrpayment-img-01.png') }}" class="h-4 object-contain"
                                    onerror="this.style.display='none'">
                                <p class="font-black text-gray-800 text-sm tracking-tight">Thai QR Payment</p>
                            </div>
                            <p class="text-[10px] text-gray-400 font-medium">บริษัท กวินบราเทอร์</p>
                        </div>
                        <div id="qr-overlay"
                            class="absolute inset-0 bg-white/95 backdrop-blur-md z-10 flex flex-col items-center justify-center opacity-0 pointer-events-none transition-all duration-500 rounded-3xl">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-calendar-times text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 font-black text-base">QR หมดอายุ</p>
                            <p class="text-[10px] text-gray-400 mt-1">กรุณาทำรายการสั่งซื้อใหม่</p>
                        </div>
                    </div>

                    <div id="save-btn-container" class="flex justify-center gap-3 mb-8 transition-all duration-300">
                        <button onclick="saveQRCode()"
                            class="flex items-center gap-2 px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-all shadow-sm active:scale-95">
                            <i class="fas fa-download"></i>
                            บันทึกรูป QR
                        </button>
                    </div>
                </div>

                <div class="space-y-3">
                    <button id="upload-slip-btn"
                        class="w-full h-14 bg-gradient-to-r from-red-600 to-red-800 text-white rounded-2xl font-black text-lg shadow-lg shadow-red-500/30 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3 group">
                        <i class="fas fa-file-invoice-dollar opacity-70 group-hover:scale-110 transition-transform"></i>
                        <span>แจ้งชำระเงิน / แนบสลิป</span>
                    </button>

                    <div id="cancel-order-container" class="transition-all duration-300">
                        <form id="cancel-form" action="{{ route('payment.cancel', ['orderCode' => $order->ord_code]) }}"
                            method="POST">
                            @csrf
                            <button type="button" onclick="confirmCancel()"
                                class="w-full h-11 border-2 border-gray-200 text-gray-400 hover:border-red-200 hover:text-red-500 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-times-circle"></i>
                                ยกเลิกคำสั่งซื้อนี้
                            </button>
                        </form>
                    </div>
                </div>

                <a href="{{ route('orders.index') }}"
                    class="block text-center mt-6 text-[11px] font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">
                    <i class="fas fa-history mr-1"></i> ประวัติการสั่งซื้อ
                </a>
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
    <script>
        // --- ฟังก์ชัน Copy to Clipboard (ปรับปรุงใหม่ให้รองรับการแจ้งชื่อฟิลด์) ---
        window.copyToClipboard = function(text, label = 'ข้อมูล') {
            navigator.clipboard.writeText(text).then(() => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
                Toast.fire({
                    icon: 'success',
                    title: `คัดลอก ${label} แล้ว`,
                    text: text
                });
            });
        }

        // --- ส่วนการทำงานอื่นๆ คงเดิม แต่ปรับแต่ง UI นิดหน่อย ---
        window.confirmCancel = function() {
            Swal.fire({
                title: 'ยืนยันการยกเลิก?',
                text: "คุณต้องการยกเลิกคำสั่งซื้อ {{ $order->ord_code }} ใช่หรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'ใช่, ยกเลิกออเดอร์',
                cancelButtonText: 'ไม่ยกเลิก',
                reverseButtons: true,
                borderRadius: '1.5rem'
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
                if (output) {
                    output.src = reader.result;
                    output.classList.remove('hidden');
                    document.getElementById('slip-preview-placeholder').classList.add('hidden');
                }
            };
            reader.readAsDataURL(event.target.files[0]);
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
                    if (saveBtnContainer) saveBtnContainer.classList.add('opacity-50', 'pointer-events-none');
                    
                    // 🔄 เมื่อเวลาหมด ให้รีเฟรชหน้าจอเพื่อแจ้งเตือนและยกเลิกออเดอร์ใน DB
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                    return;
                }
                const minutes = Math.floor(timeLeft / 60);
                let seconds = Math.floor(timeLeft % 60);
                seconds = seconds < 10 ? '0' + seconds : seconds;
                timerElement.innerHTML = `${minutes}:${seconds}`;
                timeLeft--;
            }

            setInterval(updateTimerDisplay, 1000);
            updateTimerDisplay(); // Initial call

            const uploadBtn = document.getElementById('upload-slip-btn');
            if (uploadBtn) {
                uploadBtn.addEventListener('click', function() {
                    Swal.fire({
                        title: 'แนบสลิปชำระเงิน',
                        html: `<div class="p-2">
                                <p class="text-xs text-gray-500 mb-6 font-medium">กรุณาอัปโหลดหลักฐานการโอนเงินจำนวน <br><span class="text-lg font-black text-red-600">฿{{ number_format($order->net_amount, 2) }}</span></p>
                                <div id="slip-preview-container" class="w-full h-64 bg-gray-50 rounded-2xl flex flex-col items-center justify-center border-2 border-dashed border-gray-200 cursor-pointer hover:border-red-300 hover:bg-red-50/30 transition-all" onclick="window.triggerFileInput()">
                                     <div id="slip-preview-placeholder" class="text-center text-gray-300">
                                        <div class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center mx-auto mb-3">
                                            <i class="fas fa-cloud-upload-alt text-xl"></i>
                                        </div>
                                        <p class="font-bold text-xs uppercase tracking-widest">คลิกเพื่ออัปโหลดสลิป</p>
                                        <p class="text-[10px] mt-1 font-medium">รองรับ JPG, PNG, PDF</p>
                                    </div>
                                    <img id="slip-preview" class="hidden h-full w-full object-contain rounded-2xl" />
                                </div>
                            </div>`,
                        showCancelButton: true,
                        confirmButtonText: 'ยืนยันการแจ้งชำระ',
                        confirmButtonColor: '#dc2626',
                        cancelButtonText: 'ยกเลิก',
                        borderRadius: '2rem',
                        preConfirm: () => {
                            if (document.getElementById('slip_image_input').files.length ===
                                0) {
                                Swal.showValidationMessage('กรุณาเลือกไฟล์สลิป');
                                return false;
                            }
                            // Show loading state on the button
                            Swal.getConfirmButton().innerHTML =
                                '<i class="fas fa-spinner fa-spin mr-2"></i> กำลังส่งข้อมูล...';
                            Swal.getConfirmButton().disabled = true;
                            document.getElementById('slip-upload-form').submit();
                        }
                    });
                });
            }
        });
    </script>
@endsection