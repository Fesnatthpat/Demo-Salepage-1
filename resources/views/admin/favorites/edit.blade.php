@extends('layouts.admin')

@section('title', 'แก้ไขเนื้อหา "เกี่ยวกับติดใจ"')

@section('page-title')
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('admin.favorites.index') }}" class="hover:text-red-400 transition-colors">
            <i class="fas fa-heart mr-1"></i> เกี่ยวกับติดใจ
        </a>
        <span>/</span>
        <span class="text-gray-100 font-medium">แก้ไขข้อมูล</span>
    </div>
@endsection

@section('content')
    <div class="bg-gray-800 rounded-xl shadow-2xl p-6 border border-gray-700">
        <div class="mb-6 border-b border-gray-700 pb-4">
            <h2 class="text-2xl font-bold text-gray-100">แก้ไข: <span class="text-red-500">{{ $favorite->title }}</span></h2>
        </div>

        <form action="{{ route('admin.favorites.update', $favorite->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

                {{-- ฝั่งซ้าย --}}
                <div class="xl:col-span-2 space-y-6">
                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-300 mb-2">หัวข้อ (Title) <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $favorite->title) }}"
                            required
                            class="block w-full bg-gray-900 border-gray-600 rounded-lg shadow-sm text-white focus:ring-red-500 focus:border-red-500 px-4 py-3">
                        @error('title')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Content --}}
                    <div>
                        <label for="content" class="block text-sm font-semibold text-gray-300 mb-2">รายละเอียด (Content)
                            <span class="text-red-500">*</span></label>
                        <textarea name="content" id="content" rows="8" required
                            class="block w-full bg-gray-900 border-gray-600 rounded-lg shadow-sm text-white focus:ring-red-500 focus:border-red-500 px-4 py-3 leading-relaxed">{{ old('content', $favorite->content) }}</textarea>
                        @error('content')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- ฝั่งขวา: รูปภาพ --}}
                <div class="space-y-6">
                    <div class="bg-gray-900/50 p-6 rounded-xl border border-gray-700">
                        <label class="block text-sm font-semibold text-gray-300 mb-3">รูปภาพประกอบ</label>

                        {{-- แสดงรูปภาพเดิม (ถ้ามี) --}}
                        <div class="mb-2">
                            <span class="text-xs text-gray-400">รูปภาพปัจจุบัน:</span>
                            <div class="flex flex-wrap gap-2 mt-2">
                                {{-- 1. กรณีรองรับหลายรูป (Relation: images) --}}
                                @if (isset($favorite->images) && $favorite->images->count() > 0)
                                    @foreach ($favorite->images as $img)
                                        <div class="relative w-20 h-20 group">
                                            <img src="{{ asset('storage/' . $img->image_path) }}"
                                                class="w-full h-full object-cover rounded-lg border border-gray-600">
                                            {{-- ปุ่มลบรูปเก่า (ต้องเขียน Logic Backend รองรับ) --}}
                                            {{-- <button type="button" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 text-xs flex items-center justify-center">x</button> --}}
                                        </div>
                                    @endforeach
                                    {{-- 2. กรณีรูปเดียวแบบเดิม (Fallback) --}}
                                @elseif($favorite->image_path)
                                    <div class="w-24 h-24">
                                        <img src="{{ asset('storage/' . $favorite->image_path) }}"
                                            class="w-full h-full object-cover rounded-lg border border-gray-600">
                                    </div>
                                @else
                                    <span class="text-xs text-gray-500 italic">ไม่มีรูปภาพเดิม</span>
                                @endif
                            </div>
                        </div>

                        {{-- Preview รูปใหม่ --}}
                        <div id="newImagePreviewContainer"
                            class="mb-4 w-full min-h-[8rem] bg-gray-800 rounded-lg border-2 border-dashed border-gray-600 flex flex-wrap gap-2 p-2 items-start justify-center relative mt-4">
                            <div id="imagePlaceholder"
                                class="absolute inset-0 flex flex-col items-center justify-center text-gray-500 pointer-events-none">
                                <i class="fas fa-plus text-2xl mb-1"></i>
                                <p class="text-xs">อัปโหลดเพิ่ม</p>
                            </div>
                        </div>

                        <label class="text-xs text-yellow-400 mb-2 block">เลือกไฟล์เพื่ออัปโหลดเพิ่ม (หรือแทนที่)</label>
                        <input type="file" name="images[]" id="images" accept="image/*" multiple
                            onchange="previewImages(event)"
                            class="block w-full text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-white hover:file:bg-gray-600 cursor-pointer transition-colors">
                    </div>

                    {{-- Settings --}}
                    <div class="bg-gray-900/50 p-6 rounded-xl border border-gray-700 space-y-5">
                        <div>
                            <label for="sort_order"
                                class="block text-sm font-semibold text-gray-300 mb-2">ลำดับการแสดงผล</label>
                            <input type="number" name="sort_order" id="sort_order"
                                value="{{ old('sort_order', $favorite->sort_order) }}"
                                class="block w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm text-white focus:ring-red-500 focus:border-red-500 px-4 py-2">
                        </div>
                        <hr class="border-gray-700">
                        <div class="flex items-center pt-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                @if (old('is_active', $favorite->is_active)) checked @endif
                                class="h-6 w-6 rounded border-gray-500 bg-gray-800 text-yellow-500 focus:ring-yellow-500 focus:ring-offset-gray-900 cursor-pointer">
                            <label for="is_active"
                                class="ml-3 block text-sm font-medium text-gray-200 cursor-pointer">เปิดแสดงผลที่หน้าเว็บ</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-700 flex justify-end gap-4">
                <a href="{{ route('admin.favorites.index') }}"
                    class="btn btn-ghost text-gray-300 hover:bg-gray-700">ยกเลิก</a>
                <button type="submit" class="btn bg-yellow-600 hover:bg-yellow-700 text-white border-none shadow-lg px-8">
                    <i class="fas fa-save mr-2"></i> บันทึกการเปลี่ยนแปลง
                </button>
            </div>
        </form>
    </div>

    <script>
        function previewImages(event) {
            const container = document.getElementById('newImagePreviewContainer');
            const placeholder = document.getElementById('imagePlaceholder');
            const files = event.target.files;

            container.innerHTML = '';
            container.appendChild(placeholder);

            if (files.length > 0) {
                placeholder.classList.add('hidden');
                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imgDiv = document.createElement('div');
                        imgDiv.className =
                            'w-16 h-16 relative rounded overflow-hidden border border-gray-600 shadow-sm';
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
