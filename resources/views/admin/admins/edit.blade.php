@extends('layouts.admin')

@section('title', 'Edit Admin')

@section('content')
    <div class="container mx-auto px-4 sm:px-8 py-8">
        <div class="max-w-3xl mx-auto">
            {{-- Header & Back Button --}}
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900">แก้ไขข้อมูลผู้ดูแลระบบ</h2>
                    <p class="mt-2 text-sm text-gray-600">แก้ไขรายละเอียดหรือเปลี่ยนรหัสผ่านของ: <span
                            class="font-semibold text-indigo-600">{{ $admin->username }}</span></p>
                </div>
                <a href="{{ route('admin.admins.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition">
                    <i class="fas fa-arrow-left mr-2 -ml-1 text-gray-500"></i> ย้อนกลับ
                </a>
            </div>

            <form action="{{ route('admin.admins.update', $admin->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                    <div class="p-8 space-y-8">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                            {{-- Name --}}
                            <div class="col-span-2 sm:col-span-1">
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">ชื่อ - นามสกุล
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $admin->name) }}"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 text-gray-900 text-base shadow-sm focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 transition duration-150 ease-in-out @error('name') border-red-500 bg-red-50 text-red-900 focus:ring-red-500 focus:border-red-500 @enderror"
                                    required>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i
                                            class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Role --}}
                            <div class="col-span-2 sm:col-span-1">
                                <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">ระดับสิทธิ์
                                    <span class="text-red-500">*</span></label>
                                <select id="role" name="role"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 text-gray-900 text-base shadow-sm focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 transition duration-150 ease-in-out appearance-none bg-no-repeat bg-right pr-10 @error('role') border-red-500 bg-red-50 text-red-900 focus:ring-red-500 focus:border-red-500 @enderror"
                                    style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 20 20\'%3E%3Cpath stroke=\'%236b7280\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M6 8l4 4 4-4\'/%3E%3C/svg%3E'); background-position: right 1rem center; background-size: 1.5em 1.5em;"
                                    required>
                                    <option value="admin" {{ old('role', $admin->role) === 'admin' ? 'selected' : '' }}>
                                        Admin (ผู้ดูแลทั่วไป)</option>
                                    <option value="superadmin"
                                        {{ old('role', $admin->role) === 'superadmin' ? 'selected' : '' }}>Super Admin
                                        (ผู้ดูแลสูงสุด)</option>
                                </select>
                                @error('role')
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i
                                            class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Username --}}
                            <div class="col-span-2 sm:col-span-1">
                                <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">ชื่อผู้ใช้
                                    (Username) <span class="text-red-500">*</span></label>
                                <input type="text" name="username" id="username"
                                    value="{{ old('username', $admin->username) }}"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 text-gray-900 text-base shadow-sm focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 transition duration-150 ease-in-out @error('username') border-red-500 bg-red-50 text-red-900 focus:ring-red-500 focus:border-red-500 @enderror"
                                    required>
                                @error('username')
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i
                                            class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Admin Code --}}
                            <div class="col-span-2 sm:col-span-1">
                                <label for="admin_code" class="block text-sm font-semibold text-gray-700 mb-2">รหัสประจำตัว
                                </label>
                                <input type="text" name="admin_code" id="admin_code"
                                    value="{{ old('admin_code', $admin->admin_code) }}"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-100 text-gray-900 text-base shadow-sm"
                                    readonly>
                            </div>

                            <div class="col-span-2 border-b border-gray-100"></div>

                            {{-- Password Section Heading --}}
                            <div class="col-span-2">
                                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                    <i class="fas fa-lock mr-2 text-indigo-500"></i> เปลี่ยนรหัสผ่าน
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">เว้นว่างไว้ทั้งสองช่องหากไม่ต้องการเปลี่ยนรหัสผ่านเดิม
                                </p>
                            </div>

                            {{-- New Password --}}
                            <div class="col-span-2 sm:col-span-1">
                                <label for="password"
                                    class="block text-sm font-semibold text-gray-700 mb-2">รหัสผ่านใหม่</label>
                                <input type="password" name="password" id="password"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 text-gray-900 text-base shadow-sm focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 transition duration-150 ease-in-out @error('password') border-red-500 bg-red-50 text-red-900 focus:ring-red-500 focus:border-red-500 @enderror"
                                    placeholder="อย่างน้อย 8 ตัวอักษร">
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i
                                            class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Confirm New Password --}}
                            <div class="col-span-2 sm:col-span-1">
                                <label for="password_confirmation"
                                    class="block text-sm font-semibold text-gray-700 mb-2">ยืนยันรหัสผ่านใหม่</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 text-gray-900 text-base shadow-sm focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 transition duration-150 ease-in-out"
                                    placeholder="กรอกรหัสผ่านใหม่ซ้ำอีกครั้ง">
                            </div>
                        </div>

                        @if (session('error'))
                            <div class="rounded-lg bg-red-50 p-4 border-l-4 border-red-400">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-times-circle text-red-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">พบข้อผิดพลาด</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <p>{{ session('error') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Footer Buttons --}}
                    <div class="px-8 py-6 bg-gray-50 flex justify-end items-center space-x-4 border-t border-gray-100">
                        <a href="{{ route('admin.admins.index') }}"
                            class="px-6 py-3 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition shadow-sm">
                            ยกเลิก
                        </a>
                        <button type="submit"
                            class="px-6 py-3 bg-indigo-600 border border-transparent rounded-lg text-white font-bold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 shadow-md transition flex items-center">
                            <i class="fas fa-save mr-2"></i> บันทึกการแก้ไข
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
