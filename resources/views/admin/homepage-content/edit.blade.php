@extends('layouts.admin')

@section('title', 'แก้ไขเนื้อหาหน้าหลัก')
@section('page-title', 'แก้ไขเนื้อหาหน้าหลัก')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">แก้ไขเนื้อหาหน้าหลัก</h1>

        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('admin.homepage-content.update', $homepageContent->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="section_name" class="block text-gray-700 text-sm font-bold mb-2">ชื่อส่วนเนื้อหา:</label>
                    <input type="text"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('section_name') border-red-500 @enderror"
                        id="section_name" name="section_name"
                        value="{{ old('section_name', $homepageContent->section_name) }}" required>
                    @error('section_name')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="item_key" class="block text-gray-700 text-sm font-bold mb-2">คีย์รายการ (ไม่บังคับ):</label>
                    <input type="text"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('item_key') border-red-500 @enderror"
                        id="item_key" name="item_key" value="{{ old('item_key', $homepageContent->item_key) }}">
                    @error('item_key')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="type" class="block text-gray-700 text-sm font-bold mb-2">ประเภท:</label>
                    <select
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('type') border-red-500 @enderror"
                        id="type" name="type" required>
                        <option value="text" {{ old('type', $homepageContent->type) == 'text' ? 'selected' : '' }}>
                            ข้อความ</option>
                        <option value="image" {{ old('type', $homepageContent->type) == 'image' ? 'selected' : '' }}>
                            รูปภาพ</option>
                        <option value="icon" {{ old('type', $homepageContent->type) == 'icon' ? 'selected' : '' }}>
                            ไอคอน (SVG)</option>
                        <option value="link" {{ old('type', $homepageContent->type) == 'link' ? 'selected' : '' }}>
                            ลิงก์</option>
                        <option value="collection"
                            {{ old('type', $homepageContent->type) == 'collection' ? 'selected' : '' }}>คอลเลกชัน (JSON)
                        </option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="value" class="block text-gray-700 text-sm font-bold mb-2">ค่า:</label>
                    <textarea
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('value') border-red-500 @enderror"
                        id="value" name="value" rows="3">{{ old('value', $homepageContent->value) }}</textarea>
                    <p class="text-gray-600 text-xs italic mt-1">ใช้สำหรับประเภทข้อความ, รูปภาพ (path), ไอคอน (SVG Code)
                        หรือลิงก์</p>
                    @error('value')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="data" class="block text-gray-700 text-sm font-bold mb-2">ข้อมูล (JSON):</label>
                    <textarea
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('data') border-red-500 @enderror"
                        id="data" name="data" rows="5">{{ old('data', json_encode($homepageContent->data, JSON_PRETTY_PRINT)) }}</textarea>
                    <p class="text-gray-600 text-xs italic mt-1">ใช้สำหรับประเภทคอลเลกชัน (เช่น {"title": "...",
                        "description": "..."})</p>
                    @error('data')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="order" class="block text-gray-700 text-sm font-bold mb-2">ลำดับ:</label>
                    <input type="number"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('order') border-red-500 @enderror"
                        id="order" name="order" value="{{ old('order', $homepageContent->order) }}">
                    @error('order')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="is_active" class="block text-gray-700 text-sm font-bold mb-2">สถานะใช้งาน:</label>
                    <input type="checkbox" id="is_active" name="is_active" class="toggle toggle-success"
                        {{ old('is_active', $homepageContent->is_active) ? 'checked' : '' }}>
                    @error('is_active')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit"
                        class="btn btn-primary bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        บันทึกการเปลี่ยนแปลง
                    </button>
                    <a href="{{ route('admin.homepage-content.index') }}"
                        class="btn btn-ghost text-gray-600 hover:text-gray-800">ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
@endsection
