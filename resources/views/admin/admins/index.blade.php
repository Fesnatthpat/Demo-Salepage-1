@extends('layouts.admin')

@section('title', 'Admin Management')

@section('content')
    <div class="container mx-auto px-4 sm:px-8 py-8">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">ผู้ดูแลระบบ (Admins)</h2>
                <p class="text-gray-500 text-sm mt-1">จัดการรายชื่อและสิทธิ์การเข้าใช้งานของผู้ดูแลระบบ</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('admin.admins.create') }}"
                    class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i> เพิ่มผู้ดูแลระบบ
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-md shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Table Section --}}
        <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr
                            class="bg-gray-50 border-b border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="px-5 py-3">ชื่อ - นามสกุล</th>
                            <th class="px-5 py-3">ชื่อผู้ใช้ (Username)</th>
                            <th class="px-5 py-3 text-center">ระดับสิทธิ์ (Role)</th>
                            <th class="px-5 py-3">วันที่สร้าง</th>
                            <th class="px-5 py-3 text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($admins as $admin)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-5 py-4 text-sm">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 font-bold text-lg uppercase">
                                                {{ substr($admin->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-gray-900 font-medium whitespace-no-wrap">
                                                {{ $admin->name }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-700">
                                    {{ $admin->username }}
                                </td>
                                <td class="px-5 py-4 text-sm text-center">
                                    @if ($admin->role === 'superadmin')
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            Super Admin
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Admin
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500">
                                    {{ $admin->created_at->format('d M Y') }}
                                </td>
                                <td class="px-5 py-4 text-sm text-center">
                                    <div class="flex justify-center space-x-3">
                                        <a href="{{ route('admin.admins.edit', $admin->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900 transition" title="Edit">
                                            <i class="fas fa-edit text-lg"></i>
                                        </a>
                                        <form action="{{ route('admin.admins.destroy', $admin->id) }}" method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('ยืนยันการลบผู้ดูแลระบบรายนี้?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 transition focus:outline-none"
                                                title="Delete">
                                                <i class="fas fa-trash-alt text-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                                    ไม่พบข้อมูลผู้ดูแลระบบ
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
