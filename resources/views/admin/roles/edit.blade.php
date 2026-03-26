@extends('layouts.admin')

@section('title', 'แก้ไขระดับสิทธิ์')

@section('content')
    <div class="container mx-auto px-4 sm:px-8 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-extrabold text-gray-100">แก้ไขระดับสิทธิ์: {{ $role->name }}</h2>
                <a href="{{ route('admin.roles.index') }}" class="text-gray-400 hover:text-white transition"><i class="fas fa-arrow-left mr-2"></i> ย้อนกลับ</a>
            </div>

            <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="bg-gray-800 shadow-xl rounded-2xl border border-gray-700 p-8 space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">ชื่อระดับสิทธิ์ (ภาษาไทย)</label>
                            <input type="text" name="name" value="{{ $role->name }}" required class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Role Key</label>
                            <input type="text" name="role_key" value="{{ $role->role_key }}" @if($role->role_key === 'superadmin') readonly @endif class="block w-full px-4 py-3 rounded-lg border-gray-600 bg-gray-700 text-gray-100 {{ $role->role_key === 'superadmin' ? 'opacity-50' : '' }}">
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-emerald-400 mb-4 flex items-center">
                            <i class="fas fa-shield-alt mr-2"></i> จัดการสิทธิ์การเข้าถึงเมนู
                        </h3>
                        @if($role->role_key === 'superadmin')
                            <div class="p-4 bg-purple-900/20 border border-purple-500/30 rounded-xl text-purple-300">
                                <i class="fas fa-info-circle mr-2"></i> ระดับ Super Admin มีสิทธิ์เข้าถึงทุกเมนูโดยอัตโนมัติ ไม่สามารถจำกัดสิทธิ์ได้
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($availablePermissions as $key => $label)
                                    <label class="flex items-center p-4 bg-gray-900/50 border border-gray-700 rounded-xl cursor-pointer hover:bg-gray-700 transition">
                                        <input type="checkbox" name="permissions[]" value="{{ $key }}" 
                                            @if($role->permissions && in_array($key, $role->permissions)) checked @endif
                                            class="w-5 h-5 rounded text-indigo-600 border-gray-600 bg-gray-700 focus:ring-indigo-500">
                                        <span class="ml-3 text-gray-300 font-medium">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="pt-6 border-t border-gray-700 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition"><i class="fas fa-save mr-2"></i> บันทึกการแก้ไข</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
