@extends('layout')

@section('content')
    <div class="container mx-auto px-4">
        {{-- จัดกึ่งกลางหน้าจอ --}}
        <div class="flex justify-center items-center min-h-[calc(100vh-100px)] py-10">

            {{-- Card Container --}}
            <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg border border-gray-100 text-center"
                x-data="otpForm()">

                {{-- Icon --}}
                <div class="flex justify-center mb-6">
                    <div class="bg-blue-50 p-4 rounded-full">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Header --}}
                <h2 class="text-2xl font-bold text-gray-800 mb-2">ยืนยันรหัสความปลอดภัย</h2>
                <p class="text-gray-500 text-sm mb-8">
                    เราได้ส่งรหัสยืนยัน 6 หลักไปที่อีเมล<br>
                    <span class="font-medium text-gray-800">exa***@gmail.com</span>
                </p>

                {{-- OTP Inputs (6 หลัก) --}}
                <form action="/verify" method="POST">
                    @csrf
                    <div class="flex justify-center gap-2 sm:gap-3 mb-8">
                        {{-- วนลูปสร้าง input 6 ช่อง --}}
                        <template x-for="(val, index) in inputs" :key="index">
                            <input type="text" maxlength="1" x-model="inputs[index]" x-ref="input"
                                @input="handleInput($event, index)" @keydown.backspace="handleBackspace($event, index)"
                                @paste="handlePaste($event)"
                                class="w-10 h-12 sm:w-12 sm:h-14 text-center text-xl font-bold border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-gray-50 focus:bg-white text-gray-800"
                                required>
                        </template>

                        {{-- Hidden Input สำหรับส่งค่ารวมไป Backend --}}
                        <input type="hidden" name="otp_code" :value="inputs.join('')">
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition duration-200 shadow-md mb-6"
                        :class="{ 'opacity-50 cursor-not-allowed': inputs.join('').length < 6 }"
                        :disabled="inputs.join('').length < 6">
                        ยืนยันรหัส
                    </button>
                </form>

                {{-- Resend Code --}}
                <div class="text-sm text-gray-600">
                    ยังไม่ได้รับรหัส?
                    <button class="font-medium text-blue-600 hover:text-blue-500 underline ml-1">
                        ส่งรหัสอีกครั้ง
                    </button>
                </div>

                {{-- Back link --}}
                <div class="mt-6">
                    <a href="/login"
                        class="text-xs text-gray-400 hover:text-gray-600 flex items-center justify-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        กลับไปหน้าเข้าสู่ระบบ
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- Script สำหรับจัดการ OTP Input --}}
    <script>
        function otpForm() {
            return {
                inputs: ['', '', '', '', '', ''],

                handleInput(e, index) {
                    const input = e.target;
                    const value = input.value;

                    // ถ้าใส่ตัวเลขแล้ว ให้ขยับไปช่องถัดไป
                    if (value.length === 1 && index < 5) {
                        this.$nextTick(() => {
                            // หา input ถัดไปใน DOM
                            const nextInput = input.nextElementSibling;
                            if (nextInput) nextInput.focus();
                        });
                    }
                },

                handleBackspace(e, index) {
                    const input = e.target;
                    // ถ้าลบค่าในช่องปัจจุบัน แล้วเป็นค่าว่าง ให้ถอยไปช่องก่อนหน้า
                    if (input.value === '' && index > 0) {
                        this.$nextTick(() => {
                            const prevInput = input.previousElementSibling;
                            if (prevInput) prevInput.focus();
                        });
                    }
                },

                handlePaste(e) {
                    // รองรับการ Copy Paste รหัส 6 หลักทีเดียว
                    e.preventDefault();
                    const pasteData = e.clipboardData.getData('text').slice(0, 6).split('');
                    if (pasteData.length > 0) {
                        this.inputs = pasteData.concat(new Array(6 - pasteData.length).fill(''));
                        // Focus ช่องสุดท้ายที่กรอก
                        this.$nextTick(() => {
                            const inputs = document.querySelectorAll('input[type="text"]');
                            if (inputs[pasteData.length - 1]) inputs[pasteData.length - 1].focus();
                        });
                    }
                }
            }
        }
    </script>
@endsection
