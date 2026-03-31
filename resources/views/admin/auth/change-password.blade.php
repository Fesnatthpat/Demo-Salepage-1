@extends('layouts.admin')

@section('title', 'เปลี่ยนรหัสผ่าน | TIDJAI Admin')
@section('page-title', 'เปลี่ยนรหัสผ่านส่วนตัว')

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Success/Error Notifications --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center shadow-lg transition-all">
            <i class="fas fa-check-circle mr-3 text-lg"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-gray-800 rounded-2xl shadow-xl border border-gray-700 overflow-hidden">
        <div class="p-6 sm:p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-key mr-3 text-emerald-400"></i>
                    ความปลอดภัยของบัญชี
                </h2>
                <p class="text-gray-400 mt-2">รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 8 ตัวอักษร และประกอบด้วยตัวพิมพ์ใหญ่ ตัวพิมพ์เล็ก ตัวเลข และสัญลักษณ์</p>
            </div>

            <form action="{{ route('admin.change-password.update') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Current Password --}}
                <div class="space-y-2">
                    <label for="current_password" class="text-sm font-semibold text-gray-300 ml-1">รหัสผ่านปัจจุบัน</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                            <i class="fas fa-lock text-sm"></i>
                        </div>
                        <input type="password" name="current_password" id="current_password"
                               class="block w-full pl-11 pr-4 py-3 rounded-xl bg-gray-900/50 border border-gray-700 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all @error('current_password') border-red-500 @enderror"
                               placeholder="ป้อนรหัสผ่านที่คุณใช้อยู่ตอนนี้" required>
                    </div>
                    @error('current_password')
                        <p class="text-red-400 text-xs mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-gray-700/50 my-6"></div>

                {{-- New Password --}}
                <div class="space-y-2">
                    <label for="password" class="text-sm font-semibold text-gray-300 ml-1">รหัสผ่านใหม่</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                            <i class="fas fa-shield-alt text-sm"></i>
                        </div>
                        <input type="password" name="password" id="password"
                               class="block w-full pl-11 pr-4 py-3 rounded-xl bg-gray-900/50 border border-gray-700 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all @error('password') border-red-500 @enderror"
                               placeholder="ตัวอย่าง: P@ssw0rd2024!" required>
                    </div>
                    @error('password')
                        <p class="text-red-400 text-xs mt-1 ml-1 leading-relaxed">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm New Password --}}
                <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-semibold text-gray-300 ml-1">ยืนยันรหัสผ่านใหม่</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                            <i class="fas fa-check-double text-sm"></i>
                        </div>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="block w-full pl-11 pr-4 py-3 rounded-xl bg-gray-900/50 border border-gray-700 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all"
                               placeholder="พิมพ์รหัสผ่านใหม่อีกครั้ง" required>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit"
                            class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-4 px-6 rounded-xl transition duration-300 shadow-lg shadow-emerald-500/20 active:scale-[0.98] flex items-center justify-center">
                        <i class="fas fa-save mr-2"></i>
                        บันทึกการเปลี่ยนแปลง
                    </button>
                    <p class="text-center text-xs text-gray-500 mt-4">
                        <i class="fas fa-info-circle mr-1"></i>
                        หลังจากเปลี่ยนรหัสผ่านแล้ว คุณยังคงอยู่ในระบบได้ตามปกติ
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
