@extends('layouts.admin')

@section('title', 'เพิ่มระดับสิทธิ์ใหม่')

@section('content')
    <div class="container mx-auto px-4 sm:px-8 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-extrabold text-gray-100">เพิ่มระดับสิทธิ์ใหม่</h2>
                <a href="{{ route('admin.roles.index') }}" class="text-gray-400 hover:text-white transition"><i class="fas fa-arrow-left mr-2"></i> ย้อนกลับ</a>
            </div>

            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="bg-gray-800 shadow-xl rounded-2xl border border-gray-700 p-8 space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">ชื่อระดับสิทธิ์ (ภาษาไทย)</label>
                            <input type="text" name="name" required placeholder="เช่น ผู้จัดการสินค้า" class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Role Key (ภาษาอังกฤษ/Slug)</label>
                            <input type="text" name="role_key" placeholder="เช่น product-manager" class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100">
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-emerald-400 mb-4 flex items-center">
                            <i class="fas fa-shield-alt mr-2"></i> เลือกสิทธิ์การเข้าถึงเมนู
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($availablePermissions as $key => $label)
                                <label class="flex items-center p-4 bg-gray-900/50 border border-gray-700 rounded-xl cursor-pointer hover:bg-gray-700 transition">
                                    <input type="checkbox" name="permissions[]" value="{{ $key }}" class="w-5 h-5 rounded text-indigo-600 border-gray-600 bg-gray-700 focus:ring-indigo-500">
                                    <span class="ml-3 text-gray-300 font-medium">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-700 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition"><i class="fas fa-save mr-2"></i> บันทึกระดับสิทธิ์</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
