@extends('layouts.admin')

@section('title', 'สร้างผู้ดูแลระบบใหม่ | TIDJAI Admin')
@section('page-title', 'สร้างผู้ดูแลระบบใหม่')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.admins.index') }}" class="text-gray-400 hover:text-white transition-colors flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> กลับหน้าจัดการแอดมิน
            </a>
        </div>

        <div class="bg-gray-800 rounded-2xl shadow-xl border border-gray-700 overflow-hidden">
            <div class="p-6 sm:p-8">
                <form action="{{ route('admin.admins.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Name --}}
                        <div class="col-span-2 sm:col-span-1">
                            <label for="name" class="block text-sm font-semibold text-gray-300 mb-2">ชื่อ-นามสกุล <span class="text-red-400">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100 text-base shadow-sm focus:bg-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 transition duration-150 ease-in-out placeholder-gray-500 @error('name') border-red-500 bg-red-900/20 text-red-200 focus:ring-red-500 focus:border-red-500 @enderror"
                                placeholder="เช่น นายสมชาย ใจดี" required autofocus>
                            @error('name')
                                <p class="mt-2 text-sm text-red-400 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Username --}}
                        <div class="col-span-2 sm:col-span-1">
                            <label for="username" class="block text-sm font-semibold text-gray-300 mb-2">ชื่อผู้ใช้ (Username) <span class="text-red-400">*</span></label>
                            <input type="text" name="username" id="username" value="{{ old('username') }}"
                                class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100 text-base shadow-sm focus:bg-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 transition duration-150 ease-in-out placeholder-gray-500 @error('username') border-red-500 bg-red-900/20 text-red-200 focus:ring-red-500 focus:border-red-500 @enderror"
                                placeholder="เช่น somchai_admin" required>
                            @error('username')
                                <p class="mt-2 text-sm text-red-400 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Role --}}
                        <div class="col-span-2 sm:col-span-1">
                            <label for="role_id" class="block text-sm font-semibold text-gray-300 mb-2">ระดับสิทธิ์ (Role) <span class="text-red-400">*</span></label>
                            <select name="role_id" id="role_id"
                                class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100 text-base shadow-sm focus:bg-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 transition duration-150 ease-in-out @error('role_id') border-red-500 bg-red-900/20 text-red-200 focus:ring-red-500 focus:border-red-500 @enderror"
                                required>
                                <option value="" disabled selected>เลือกสิทธิ์การใช้งาน</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <p class="mt-2 text-sm text-red-400 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-span-2 sm:col-span-1">
                            <label for="is_active" class="block text-sm font-semibold text-gray-300 mb-2">สถานะบัญชี <span class="text-red-400">*</span></label>
                            <select name="is_active" id="is_active"
                                class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100 text-base shadow-sm focus:bg-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 transition duration-150 ease-in-out @error('is_active') border-red-500 bg-red-900/20 text-red-200 focus:ring-red-500 focus:border-red-500 @enderror"
                                required>
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>ใช้งานได้ปกติ (Active)</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>ระงับการใช้งาน (Disabled)</option>
                            </select>
                            @error('is_active')
                                <p class="mt-2 text-sm text-red-400 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-2 border-b border-gray-700 my-4"></div>

                        {{-- Password --}}
                        <div class="col-span-2 sm:col-span-1">
                            <label for="password" class="block text-sm font-semibold text-gray-300 mb-2">รหัสผ่าน <span class="text-red-400">*</span></label>
                            <input type="password" name="password" id="password"
                                class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100 text-base shadow-sm focus:bg-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 transition duration-150 ease-in-out placeholder-gray-500 @error('password') border-red-500 bg-red-900/20 text-red-200 focus:ring-red-500 focus:border-red-500 @enderror"
                                placeholder="อย่างน้อย 8 ตัวอักษร" required>
                            
                            {{-- Strength Meter --}}
                            <div class="mt-3 bg-gray-900/50 p-2 rounded-lg border border-gray-700">
                                <div class="h-1.5 w-full bg-gray-700 rounded-full overflow-hidden">
                                    <div id="strength-bar" class="h-full transition-all duration-300 bg-gray-500" style="width: 0%"></div>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <p id="strength-text" class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">ระดับความปลอดภัย: <span>ว่าง</span></p>
                                    <div id="strength-icon" class="text-gray-600"><i class="fas fa-shield-alt text-xs"></i></div>
                                </div>
                            </div>

                            @error('password')
                                <p class="mt-2 text-sm text-red-400 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="col-span-2 sm:col-span-1">
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-300 mb-2">ยืนยันรหัสผ่าน <span class="text-red-400">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100 text-base shadow-sm focus:bg-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 transition duration-150 ease-in-out placeholder-gray-500"
                                placeholder="กรอกรหัสผ่านอีกครั้ง" required>
                        </div>
                    </div>

                    <div class="mt-10 flex items-center justify-end space-x-4">
                        <button type="reset" class="px-6 py-3 rounded-lg text-sm font-semibold text-gray-300 hover:text-white transition-colors">ล้างค่าที่กรอก</button>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-8 rounded-lg transition-all shadow-lg shadow-indigo-500/20 active:scale-95 flex items-center">
                            <i class="fas fa-save mr-2"></i> สร้างบัญชีแอดมิน
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const bar = document.getElementById('strength-bar');
        const textSpan = document.getElementById('strength-text').querySelector('span');
        const iconDiv = document.getElementById('strength-icon');

        passwordInput.addEventListener('input', function() {
            const val = this.value;
            let strength = 0;

            if (val.length === 0) {
                strength = 0;
            } else {
                if (val.length >= 8) strength += 25;
                if (val.match(/[a-z]/) && val.match(/[A-Z]/)) strength += 25;
                if (val.match(/[0-9]/)) strength += 25;
                if (val.match(/[^a-zA-Z0-9]/)) strength += 25;
            }

            // Update UI
            bar.style.width = strength + '%';
            
            if (strength === 0) {
                bar.style.backgroundColor = '#4b5563'; // gray-600
                textSpan.textContent = 'ว่าง';
                textSpan.className = 'text-gray-500';
                iconDiv.className = 'text-gray-600';
            } else if (strength <= 25) {
                bar.style.backgroundColor = '#ef4444'; // red-500
                textSpan.textContent = 'ง่ายมาก (ไม่ปลอดภัย)';
                textSpan.className = 'text-red-500';
                iconDiv.className = 'text-red-500';
            } else if (strength <= 50) {
                bar.style.backgroundColor = '#f97316'; // orange-500
                textSpan.textContent = 'ปานกลาง (ควรเพิ่มสัญลักษณ์)';
                textSpan.className = 'text-orange-500';
                iconDiv.className = 'text-orange-500';
            } else if (strength <= 75) {
                bar.style.backgroundColor = '#eab308'; // yellow-500
                textSpan.textContent = 'ดี (ค่อนข้างปลอดภัย)';
                textSpan.className = 'text-yellow-500';
                iconDiv.className = 'text-yellow-500';
            } else {
                bar.style.backgroundColor = '#10b981'; // emerald-500
                textSpan.textContent = 'ยอดเยี่ยม (ปลอดภัยสูง)';
                textSpan.className = 'text-emerald-500 font-black';
                iconDiv.className = 'text-emerald-500';
            }
        });
    });
</script>
@endpush
