@extends('layouts.admin')

@section('title', 'จัดการเนื้อหาหน้าหลัก')
@section('page-title', 'จัดการเนื้อหาหน้าหลัก')

@section('content')
    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">จัดการเนื้อหาหน้าหลัก</h1>
            <a href="{{ route('admin.homepage-content.create') }}"
                class="btn btn-primary bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg">
                <i class="fas fa-plus mr-2"></i> เพิ่มเนื้อหาใหม่
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($homepageContents->isEmpty())
            <div class="text-center py-10 bg-white rounded-lg shadow">
                <p class="text-gray-600">ยังไม่มีเนื้อหาสำหรับหน้าหลัก</p>
                <p class="text-gray-500 text-sm mt-2">กรุณาเพิ่มเนื้อหาใหม่เพื่อแสดงบนหน้าเว็บไซต์</p>
            </div>
        @else
            @foreach ($homepageContents as $sectionName => $items)
                <div class="bg-white shadow rounded-lg mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-700 capitalize">{{ str_replace('_', ' ', $sectionName) }}</h2>
                    </div>
                    <div class="p-4">
                        <div class="overflow-x-auto">
                            <table class="table w-full">
                                <thead>
                                    <tr>
                                        <th>ลำดับ</th>
                                        <th>คีย์</th>
                                        <th>ประเภท</th>
                                        <th>ค่า/ข้อมูล</th>
                                        <th>สถานะ</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr>
                                            <td>{{ $item->order ?? '-' }}</td>
                                            <td>{{ $item->item_key ?? '-' }}</td>
                                            <td>{{ $item->type }}</td>
                                            <td>
                                                @if ($item->type === 'image')
                                                    <img src="{{ asset($item->value) }}" alt="Image"
                                                        class="w-16 h-16 object-cover rounded-md">
                                                @elseif ($item->type === 'text' || $item->type === 'link')
                                                    {{ Str::limit($item->value, 50) }}
                                                @elseif ($item->type === 'icon')
                                                    {!! $item->value !!}
                                                @elseif ($item->type === 'collection' && is_array($item->data))
                                                    <pre class="text-xs bg-gray-100 p-2 rounded-md">{{ json_encode($item->data, JSON_PRETTY_PRINT) }}</pre>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ $item->is_active ? 'badge-success' : 'badge-error' }}">{{ $item->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.homepage-content.edit', $item->id) }}"
                                                    class="btn btn-sm btn-info text-white mr-2">แก้ไข</a>
                                                <form
                                                    action="{{ route('admin.homepage-content.destroy', $item->id) }}"
                                                    method="POST" class="inline-block"
                                                    onsubmit="return confirm('คุณแน่ใจหรือไม่ที่ต้องการลบเนื้อหานี้?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-error text-white">ลบ</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
