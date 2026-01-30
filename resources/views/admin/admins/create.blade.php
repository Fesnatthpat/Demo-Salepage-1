@extends('layouts.admin')

@section('title', 'Create Admin')

@section('content')
    <div class="container mx-auto px-4 sm:px-8 py-8">
        <div class="max-w-3xl mx-auto">
            {{-- Header & Back Button --}}
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-100">สร้างผู้ดูแลระบบใหม่</h2>
                    <p class="mt-2 text-sm text-gray-400">กรอกข้อมูลเพื่อเพิ่มบัญชีผู้ดูแลระบบเข้าสู่ระบบ</p>
                </div>
                <a href="{{ route('admin.admins.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-300 bg-gray-800 hover:bg-gray-700 hover:text-white focus:outline-none transition">
                    <i class="fas fa-arrow-left mr-2 -ml-1 text-gray-500 group-hover:text-white"></i> ย้อนกลับ
                </a>
            </div>

            <form action="{{ route('admin.admins.store') }}" method="POST">
                @csrf
                <div class="bg-gray-800 shadow-xl rounded-2xl overflow-hidden border border-gray-700">
                    <div class="p-8 space-y-8">

                        {{-- Grid Layout --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                            {{-- Name --}}
                            <div class="col-span-2 sm:col-span-1">
                                <label for="name" class="block text-sm font-semibold text-gray-300 mb-2">ชื่อ - นามสกุล
                                    <span class="text-red-400">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}"
                                    autocomplete="name"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100 text-base shadow-sm focus:bg-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 transition duration-150 ease-in-out placeholder-gray-500 @error('name') border-red-500 bg-red-900/20 text-red-200 focus:ring-red-500 focus:border-red-500 @enderror"
                                    placeholder="เช่น สมชาย ใจดี" required>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                                            class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Role --}}
                            <div class="col-span-2 sm:col-span-1">
                                <label for="role" class="block text-sm font-semibold text-gray-300 mb-2">ระดับสิทธิ์
                                    <span class="text-red-400">*</span></label>
                                {{-- หมายเหตุ: เปลี่ยนสีไอคอนลูกศรใน SVG เป็นสีเทาอ่อน (%239ca3af) --}}
                                <select id="role" name="role"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100 text-base shadow-sm focus:bg-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 transition duration-150 ease-in-out appearance-none bg-no-repeat bg-right pr-10 @error('role') border-red-500 bg-red-900/20 text-red-200 focus:ring-red-500 focus:border-red-500 @enderror"
                                    style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 20 20\'%3E%3Cpath stroke=\'%239ca3af\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M6 8l4 4 4-4\'/%3E%3C/svg%3E'); background-position: right 1rem center; background-size: 1.5em 1.5em;"
                                    required>
                                    <option value="" disabled selected class="bg-gray-800 text-gray-400">
                                        กรุณาเลือกตำแหน่ง</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}
                                        class="bg-gray-800">Admin
                                        (ผู้ดูแลทั่วไป)</option>
                                    <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}
                                        class="bg-gray-800">Super
                                        Admin (ผู้ดูแลสูงสุด)</option>
                                </select>
                                @error('role')
                                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                                            class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Username --}}
                            <div class="col-span-2">
                                <label for="username" class="block text-sm font-semibold text-gray-300 mb-2">ชื่อผู้ใช้
                                    (Username) <span class="text-red-400">*</span></label>
                                <div class="relative rounded-md shadow-sm">
                                    <input type="text" name="username" id="username" value="{{ old('username') }}"
                                        autocomplete="username"
                                        class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100 text-base shadow-sm focus:bg-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 transition duration-150 ease-in-out placeholder-gray-500 @error('username') border-red-500 bg-red-900/20 text-red-200 focus:ring-red-500 focus:border-red-500 @enderror"
                                        placeholder="ภาษาอังกฤษและตัวเลขเท่านั้น" required>
                                </div>
                                @error('username')
                                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                                            class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-2 border-b border-gray-700"></div>

                            {{-- Password --}}
                            <div class="col-span-2 sm:col-span-1">
                                <label for="password" class="block text-sm font-semibold text-gray-300 mb-2">รหัสผ่าน <span
                                        class="text-red-400">*</span></label>
                                <input type="password" name="password" id="password"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100 text-base shadow-sm focus:bg-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 transition duration-150 ease-in-out placeholder-gray-500 @error('password') border-red-500 bg-red-900/20 text-red-200 focus:ring-red-500 focus:border-red-500 @enderror"
                                    placeholder="อย่างน้อย 8 ตัวอักษร" required>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                                            class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div class="col-span-2 sm:col-span-1">
                                <label for="password_confirmation"
                                    class="block text-sm font-semibold text-gray-300 mb-2">ยืนยันรหัสผ่าน <span
                                        class="text-red-400">*</span></label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100 text-base shadow-sm focus:bg-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 transition duration-150 ease-in-out placeholder-gray-500"
                                    placeholder="กรอกรหัสผ่านอีกครั้ง" required>
                            </div>
                        </div>

                        {{-- Server Side Errors Display (General) --}}
                        @if (session('error'))
                            <div class="rounded-lg bg-red-900/50 p-4 border-l-4 border-red-500">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-times-circle text-red-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-300">พบข้อผิดพลาด</h3>
                                        <div class="mt-2 text-sm text-red-200">
                                            <p>{{ session('error') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                    {{-- Footer Buttons --}}
                    <div class="px-8 py-6 bg-gray-900/50 flex justify-end items-center space-x-4 border-t border-gray-700">
                        <a href="{{ route('admin.admins.index') }}"
                            class="px-6 py-3 bg-gray-700 border border-gray-600 rounded-lg text-gray-300 font-medium hover:bg-gray-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition shadow-sm">
                            ยกเลิก
                        </a>
                        <button type="submit"
                            class="px-6 py-3 bg-indigo-600 border border-transparent rounded-lg text-white font-bold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 shadow-md shadow-indigo-900/30 transition flex items-center">
                            <i class="fas fa-save mr-2"></i> บันทึกข้อมูล
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
