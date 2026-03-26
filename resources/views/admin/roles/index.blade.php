@extends('layouts.admin')

@section('title', 'จัดการระดับสิทธิ์')

@section('content')
    <div class="container mx-auto px-4 sm:px-8 py-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-100">จัดการระดับสิทธิ์ (Roles & Permissions)</h2>
                <p class="text-gray-400 text-sm mt-1">กำหนดบทบาทและสิทธิ์การเข้าถึงเมนูต่างๆ ของระบบ</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('admin.roles.create') }}"
                    class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg shadow-indigo-900/20 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i> เพิ่มระดับสิทธิ์ใหม่
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 bg-green-900/50 border-l-4 border-green-500 p-4 rounded-md shadow-sm">
                <p class="text-sm text-green-200">{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 bg-red-900/50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
                <p class="text-sm text-red-200">{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-700">
            <table class="min-w-full leading-normal text-gray-300">
                <thead>
                    <tr class="bg-gray-900/50 border-b border-gray-700 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="px-5 py-3">ชื่อระดับสิทธิ์</th>
                        <th class="px-5 py-3">Role Key</th>
                        <th class="px-5 py-3">สิทธิ์การเข้าถึง (Menus)</th>
                        <th class="px-5 py-3 text-center">จำนวนแอดมิน</th>
                        <th class="px-5 py-3 text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach ($roles as $role)
                        <tr class="hover:bg-gray-700/50 transition duration-150">
                            <td class="px-5 py-4 text-sm font-medium text-gray-100">{{ $role->name }}</td>
                            <td class="px-5 py-4 text-sm font-mono text-gray-400">{{ $role->role_key }}</td>
                            <td class="px-5 py-4 text-xs">
                                <div class="flex flex-wrap gap-1">
                                    @if($role->role_key === 'superadmin')
                                        <span class="px-2 py-0.5 bg-purple-900/30 text-purple-400 border border-purple-500/20 rounded-full">ALL PERMISSIONS</span>
                                    @elseif($role->permissions)
                                        @foreach($role->permissions as $perm)
                                            <span class="px-2 py-0.5 bg-gray-700 text-gray-300 rounded-full border border-gray-600">
                                                {{ \App\Models\Role::getAvailablePermissions()[$perm] ?? $perm }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-gray-500 italic">ไม่มีสิทธิ์เข้าถึง</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-center font-bold text-emerald-400">{{ $role->admins()->count() }}</td>
                            <td class="px-5 py-4 text-sm text-center">
                                <div class="flex justify-center space-x-3">
                                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="text-indigo-400 hover:text-indigo-300 transition"><i class="fas fa-edit"></i></a>
                                    @if($role->role_key !== 'superadmin')
                                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('ยืนยันการลบระดับสิทธิ์นี้?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300 transition"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
