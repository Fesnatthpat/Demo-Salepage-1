@extends('layouts.admin')

@section('title', 'จัดการคำถามที่พบบ่อย')

@section('page-title')
    <div class="text-2xl font-bold">จัดการคำถามที่พบบ่อย (FAQ)</div>
@endsection

@section('content')
<div class="bg-gray-800 rounded-lg shadow-lg p-6">
    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.faqs.create') }}" class="btn bg-emerald-600 hover:bg-emerald-700 text-white border-none">
            <i class="fas fa-plus mr-2"></i> เพิ่มคำถามใหม่
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr class="text-gray-300">
                    <th class="w-10">#</th>
                    <th>คำถาม</th>
                    <th>สถานะ</th>
                    <th class="text-right">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($faqs as $faq)
                    <tr class="hover:bg-gray-700">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $faq->question }}</td>
                        <td>
                            @if ($faq->is_active)
                                <span class="badge badge-success">เปิดใช้งาน</span>
                            @else
                                <span class="badge badge-error">ปิดใช้งาน</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('admin.faqs.edit', $faq->id) }}" class="btn btn-sm btn-warning text-white">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" class="inline-block" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบคำถามนี้?')">
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
                        <td colspan="4" class="text-center py-8 text-gray-400">
                            ยังไม่มีคำถามที่พบบ่อย
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
