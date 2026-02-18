@extends('layouts.admin')

@section('title', 'สร้างเนื้อหา "เกี่ยวกับติดใจ"')

@section('page-title')
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('admin.favorites.index') }}" class="hover:text-red-400 transition-colors">
            <i class="fas fa-heart mr-1"></i> เกี่ยวกับติดใจ
        </a>
        <span>/</span>
        <span class="text-gray-100 font-medium">สร้างเนื้อหาใหม่</span>
    </div>
@endsection

@section('content')
    <div class="bg-gray-800 rounded-xl shadow-2xl p-6 border border-gray-700">
        <div class="mb-6 border-b border-gray-700 pb-4">
            <h2 class="text-2xl font-bold text-gray-100">สร้างเนื้อหาใหม่</h2>
        </div>

        {{-- สังเกต enctype ต้องมี --}}
        <form action="{{ route('admin.favorites.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

                {{-- ฝั่งซ้าย: ข้อมูลข้อความ --}}
                <div class="xl:col-span-2 space-y-6">
                    {{-- คำแนะนำจากระบบ --}}
                    <div class="bg-blue-900/30 border border-blue-800 rounded-lg p-4 flex gap-4 text-blue-200 text-sm">
                        <i class="fas fa-lightbulb text-xl text-blue-400 mt-1"></i>
                        <div>
                            <strong class="block text-blue-300 mb-1">💡 คำแนะนำ:</strong>
                            <ul class="list-disc ml-4 space-y-1">
                                <li>สามารถเลือกรูปภาพได้มากกว่า 1 รูป (กด Ctrl ค้างไว้ตอนเลือกรูป)</li>
                                <li>รูปจะแสดงผลเรียงกันสวยงาม</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-300 mb-2">หัวข้อ (Title) <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            placeholder="ใส่หัวข้อเรื่อง..."
                            class="block w-full bg-gray-900 border-gray-600 rounded-lg shadow-sm text-white focus:ring-red-500 focus:border-red-500 px-4 py-3">
                        @error('title')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Content --}}
                    <div>
                        <label for="content" class="block text-sm font-semibold text-gray-300 mb-2">รายละเอียด (Content)
                            <span class="text-red-500">*</span></label>
                        <textarea name="content" id="content" rows="8" required placeholder="ใส่รายละเอียดเนื้อหา..."
                            class="block w-full bg-gray-900 border-gray-600 rounded-lg shadow-sm text-white focus:ring-red-500 focus:border-red-500 px-4 py-3 leading-relaxed">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- ฝั่งขวา: รูปภาพและตั้งค่า --}}
                <div class="space-y-6">
                    {{-- Image Upload (Multiple) --}}
                    <div class="bg-gray-900/50 p-6 rounded-xl border border-gray-700">
                        <label class="block text-sm font-semibold text-gray-300 mb-3">รูปภาพประกอบ (เลือกได้หลายรูป)</label>

                        {{-- พื้นที่แสดงตัวอย่างรูปภาพ (Grid Layout) --}}
                        <div id="imagePreviewContainer"
                            class="mb-4 w-full min-h-[12rem] bg-gray-800 rounded-lg border-2 border-dashed border-gray-600 flex flex-wrap gap-2 p-2 items-start justify-center relative">
                            {{-- Placeholder --}}
                            <div id="imagePlaceholder"
                                class="absolute inset-0 flex flex-col items-center justify-center text-gray-500 pointer-events-none">
                                <i class="fas fa-images text-4xl mb-2"></i>
                                <p class="text-xs">อัปโหลดรูปภาพที่นี่</p>
                            </div>
                            {{-- รูปจะถูก Append เข้ามาที่นี่ด้วย JS --}}
                        </div>

                        {{-- Input File แบบ Multiple --}}
                        <input type="file" name="images[]" id="images" accept="image/*" multiple
                            onchange="previewImages(event)"
                            class="block w-full text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-white hover:file:bg-gray-600 cursor-pointer transition-colors">
                        @error('images')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        @error('images.*')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Settings --}}
                    <div class="bg-gray-900/50 p-6 rounded-xl border border-gray-700 space-y-5">
                        {{-- Sort Order --}}
                        <div>
                            <label for="sort_order"
                                class="block text-sm font-semibold text-gray-300 mb-2">ลำดับการแสดงผล</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                                class="block w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm text-white focus:ring-red-500 focus:border-red-500 px-4 py-2">
                        </div>

                        <hr class="border-gray-700">

                        {{-- Is Active --}}
                        <div class="flex items-center pt-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked
                                class="h-6 w-6 rounded border-gray-500 bg-gray-800 text-red-600 focus:ring-red-500 focus:ring-offset-gray-900 cursor-pointer">
                            <label for="is_active"
                                class="ml-3 block text-sm font-medium text-gray-200 cursor-pointer">เปิดแสดงผลที่หน้าเว็บ</label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-8 pt-6 border-t border-gray-700 flex justify-end gap-4">
                <a href="{{ route('admin.favorites.index') }}"
                    class="btn btn-ghost text-gray-300 hover:bg-gray-700">ยกเลิก</a>
                <button type="submit" class="btn bg-red-600 hover:bg-red-700 text-white border-none shadow-lg px-8">
                    <i class="fas fa-save mr-2"></i> บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>

    {{-- Script แสดงตัวอย่างรูปภาพหลายรูป --}}
    <script>
        function previewImages(event) {
            const container = document.getElementById('imagePreviewContainer');
            const placeholder = document.getElementById('imagePlaceholder');
            const files = event.target.files;

            // เคลียร์รูปเก่าออกก่อน (ถ้าเลือกใหม่)
            // ยกเว้น placeholder แต่ซ่อนมันไว้
            container.innerHTML = '';
            container.appendChild(placeholder);

            if (files.length > 0) {
                placeholder.classList.add('hidden');

                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imgDiv = document.createElement('div');
                        imgDiv.className =
                            'w-24 h-24 relative rounded-lg overflow-hidden border border-gray-600 shadow-sm';

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-full h-full object-cover';

                        imgDiv.appendChild(img);
                        container.appendChild(imgDiv);
                    }
                    reader.readAsDataURL(file);
                });
            } else {
                placeholder.classList.remove('hidden');
            }
        }
    </script>
@endsection
