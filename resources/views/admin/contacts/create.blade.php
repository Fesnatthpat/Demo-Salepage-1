@extends('layouts.admin')

@section('title', 'เพิ่มข้อมูลติดต่อ')

@section('page-title')
    <div class="text-2xl font-bold">เพิ่มข้อมูลติดต่อใหม่</div>
@endsection

@section('content')
<div class="bg-gray-800 rounded-lg shadow-lg p-6 max-w-3xl mx-auto">
    <form action="{{ route('admin.contacts.store') }}" method="POST">
        @csrf
        <div class="space-y-6">

            <div>
                <label for="title" class="block text-sm font-medium text-gray-300">หัวข้อ (Title)</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                       class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white focus:ring-emerald-500 focus:border-emerald-500">
                @error('title') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="content" class="block text-sm font-medium text-gray-300">เนื้อหา (Content)</label>
                <textarea name="content" id="content" rows="3"
                          class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white focus:ring-emerald-500 focus:border-emerald-500">{{ old('content') }}</textarea>
                @error('content') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>
            
            <div>
                <label for="address" class="block text-sm font-medium text-gray-300">ที่อยู่ (Address)</label>
                <textarea name="address" id="address" rows="3"
                          class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white focus:ring-emerald-500 focus:border-emerald-500">{{ old('address') }}</textarea>
                @error('address') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-300">เบอร์โทรศัพท์</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                           class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white focus:ring-emerald-500 focus:border-emerald-500">
                    @error('phone') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300">อีเมล</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white focus:ring-emerald-500 focus:border-emerald-500">
                    @error('email') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="map_url" class="block text-sm font-medium text-gray-300">ลิงก์แผนที่ (Google Maps URL)</label>
                <input type="url" name="map_url" id="map_url" value="{{ old('map_url') }}"
                       class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white focus:ring-emerald-500 focus:border-emerald-500">
                @error('map_url') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-300">ลำดับ</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                           class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white focus:ring-emerald-500 focus:border-emerald-500">
                    @error('sort_order') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center pt-6">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked
                           class="h-4 w-4 rounded border-gray-600 bg-gray-700 text-emerald-600 focus:ring-emerald-500">
                    <label for="is_active" class="ml-2 block text-sm text-gray-300">เปิดใช้งาน</label>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-4">
            <a href="{{ route('admin.contacts.index') }}" class="btn btn-ghost">ยกเลิก</a>
            <button type="submit" class="btn bg-emerald-600 hover:bg-emerald-700 text-white border-none">
                <i class="fas fa-save mr-2"></i> บันทึกข้อมูล
            </button>
        </div>
    </form>
</div>
@endsection
