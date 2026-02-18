@extends('layouts.admin')

@section('title', 'จัดการติดต่อเรา')

@section('page-title')
    <div class="text-2xl font-bold">จัดการข้อมูลติดต่อเรา</div>
@endsection

@section('content')
<div class="bg-gray-800 rounded-lg shadow-lg p-6">
    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.contacts.create') }}" class="btn bg-emerald-600 hover:bg-emerald-700 text-white border-none">
            <i class="fas fa-plus mr-2"></i> เพิ่มข้อมูลติดต่อ
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr class="text-gray-300">
                    <th class="w-10">#</th>
                    <th>หัวข้อ</th>
                    <th>เบอร์โทร</th>
                    <th>อีเมล</th>
                    <th>สถานะ</th>
                    <th class="text-right">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($contacts as $contact)
                    <tr class="hover:bg-gray-700">
                        <td>{{ $contact->sort_order }}</td>
                        <td>{{ $contact->title }}</td>
                        <td>{{ $contact->phone }}</td>
                        <td>{{ $contact->email }}</td>
                        <td>
                            @if ($contact->is_active)
                                <span class="badge badge-success">เปิดใช้งาน</span>
                            @else
                                <span class="badge badge-error">ปิดใช้งาน</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('admin.contacts.edit', $contact->id) }}" class="btn btn-sm btn-warning text-white">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST" class="inline-block" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลนี้?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-error text-white">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-400">
                            ยังไม่มีข้อมูลติดต่อ
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
