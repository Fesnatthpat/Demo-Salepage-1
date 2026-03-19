@extends('layouts.admin')

@section('title', 'แก้ไขเนื้อหา "เกี่ยวกับติดใจ"')

{{-- 1. เพิ่ม CSS ของ CodeMirror --}}
@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/theme/dracula.min.css">
    <style>
        .CodeMirror {
            height: 400px;
            border-radius: 0.5rem;
            font-family: 'Fira Code', 'Consolas', monospace;
            font-size: 14px;
            line-height: 1.6;
            padding: 10px;
        }
    </style>
@endsection

@section('page-title')
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.favorites.index') }}" class="text-gray-400 hover:text-white transition-colors">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">แก้ไขเนื้อหา</h1>
            <p class="text-sm text-gray-400">แก้ไขข้อมูล: {{ Str::limit($favorite->title, 40) }}</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700/50 overflow-hidden">
            <div class="p-1 bg-gradient-to-r from-yellow-500 to-orange-500"></div>

            <form action="{{ route('admin.favorites.update', $favorite->id) }}" method="POST" enctype="multipart/form-data"
                class="p-6 md:p-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    {{-- Left Column: Main Content --}}
                    <div class="lg:col-span-2 space-y-6">
                        {{-- Title --}}
                        <div class="space-y-2">
                            <label for="title" class="block text-sm font-medium text-gray-200">
                                หัวข้อ (Title) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                                    <i class="fas fa-heading"></i>
                                </span>
                                <input type="text" name="title" id="title"
                                    value="{{ old('title', $favorite->title) }}" required
                                    class="block w-full pl-10 bg-gray-900 border border-gray-600 rounded-lg shadow-sm py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                            </div>
                            @error('title')
                                <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Content (CodeMirror Editor) --}}
                        <div class="space-y-2">
                            <label for="content" class="block text-sm font-medium text-gray-200">
                                รายละเอียด (Content) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative border border-gray-600 rounded-lg overflow-hidden shadow-sm">
                                {{-- เอา required ออกแล้ว --}}
                                <textarea name="content" id="content">{{ old('content', $favorite->content) }}</textarea>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span><i class="fas fa-code mr-1"></i> รองรับ HTML & CSS Highlighting</span>
                                <span>Theme: Dracula</span>
                            </div>
                            @error('content')
                                <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Right Column: Settings & Images --}}
                    <div class="space-y-6">
                        {{-- Status Card --}}
                        <div class="bg-gray-900 rounded-xl p-5 border border-gray-700/50 space-y-4">
                            <h3 class="text-gray-100 font-semibold border-b border-gray-700 pb-2 mb-4">การตั้งค่า</h3>

                            <div class="space-y-2">
                                <label for="sort_order"
                                    class="block text-sm font-medium text-gray-400">ลำดับการแสดงผล</label>
                                <input type="number" name="sort_order" id="sort_order"
                                    value="{{ old('sort_order', $favorite->sort_order) }}"
                                    class="block w-full bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-yellow-500 focus:border-yellow-500">
                            </div>

                            <div class="space-y-2 pt-2">
                                <label class="block text-sm font-medium text-gray-400 mb-2">สถานะ</label>
                                <label class="inline-flex items-center cursor-pointer group">
                                    <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                                        @if (old('is_active', $favorite->is_active)) checked @endif>
                                    <div
                                        class="relative w-14 h-7 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-yellow-500">
                                    </div>
                                    <span
                                        class="ml-3 text-sm font-medium text-gray-300 group-hover:text-white transition-colors">
                                        @if ($favorite->is_active)
                                            เปิดแสดงผล
                                        @else
                                            ปิดการแสดงผล
                                        @endif
                                    </span>
                                </label>
                            </div>
                        </div>

                        {{-- Image Upload Card --}}
                        <div class="bg-gray-900 rounded-xl p-5 border border-gray-700/50">
                            <label class="block text-sm font-semibold text-gray-200 mb-3">รูปภาพประกอบ</label>

                            @if ((isset($favorite->images) && $favorite->images->count() > 0) || $favorite->image_path)
                                <div class="mb-4">
                                    <span class="text-xs text-gray-400 mb-2 block">รูปภาพปัจจุบัน:</span>
                                    <div class="flex flex-wrap gap-2">
                                        @if (isset($favorite->images) && $favorite->images->count() > 0)
                                            @foreach ($favorite->images as $img)
                                                <div
                                                    class="relative w-16 h-16 rounded-lg overflow-hidden border border-gray-600 group">
                                                    <img src="{{ asset('storage/' . $img->image_path) }}"
                                                        class="w-full h-full object-cover">
                                                </div>
                                            @endforeach
                                        @elseif($favorite->image_path)
                                            <div
                                                class="relative w-20 h-20 rounded-lg overflow-hidden border border-gray-600">
                                                <img src="{{ asset('storage/' . $favorite->image_path) }}"
                                                    class="w-full h-full object-cover">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="border-t border-gray-700 my-4"></div>

                            <label class="block text-xs text-yellow-500 mb-2">อัปโหลดเพิ่ม / แทนที่</label>
                            <div id="imagePreviewContainer"
                                class="mb-4 w-full min-h-[100px] bg-gray-800 rounded-lg border-2 border-dashed border-gray-600 flex flex-wrap gap-2 p-3 items-start justify-center relative hover:border-gray-500 transition-colors">
                                <div id="imagePlaceholder"
                                    class="absolute inset-0 flex flex-col items-center justify-center text-gray-500 pointer-events-none">
                                    <i class="fas fa-plus text-2xl mb-1"></i>
                                    <p class="text-xs">เลือกไฟล์ใหม่</p>
                                </div>
                            </div>

                            <input type="file" name="images[]" id="images" accept="image/*" multiple
                                onchange="previewImages(event)"
                                class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-yellow-600 file:text-white hover:file:bg-yellow-500 cursor-pointer">
                        </div>

                        {{-- Video Upload Card --}}
                        {{-- <div class="bg-gray-900 rounded-xl p-5 border border-gray-700/50 mt-6">
                            <label class="block text-sm font-semibold text-gray-200 mb-3">วิดีโอประกอบ</label>

                            @php 
                                // สมมติว่า DB ของคุณใช้ชื่อฟิลด์ video หรือ video_path
                                $videoPath = $favorite->video_path ?? $favorite->video ?? null; 
                            @endphp

                            @if($videoPath)
                            <div class="mb-4">
                                <span class="text-xs text-gray-400 mb-2 block">วิดีโอปัจจุบัน:</span>
                                <div class="relative w-full rounded-lg overflow-hidden border border-gray-600 bg-black flex justify-center">
                                    <video controls class="max-h-[200px] w-auto">
                                        <source src="{{ asset('storage/' . $videoPath) }}" type="video/mp4">
                                        เบราว์เซอร์ของคุณไม่รองรับวิดีโอ
                                    </video>
                                </div>
                            </div>
                            <div class="border-t border-gray-700 my-4"></div>
                            @endif

                            <label class="block text-xs text-blue-400 mb-2">อัปโหลดใหม่เพื่อแทนที่ (ถ้ามี)</label>
                            
                            <div id="videoPreviewContainer" class="mb-4 hidden w-full rounded-lg overflow-hidden border border-gray-600 shadow-md bg-black flex justify-center">
                                <video id="videoPreview" controls class="max-h-[200px] w-auto"></video>
                            </div>

                            <input type="file" name="video" id="video" accept="video/*"
                                onchange="previewVideo(event)"
                                class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer">
                        </div> --}}

                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-8 border-t border-gray-700 mt-8 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.favorites.index') }}"
                        class="px-5 py-2.5 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition-all font-medium">
                        ยกเลิก
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-yellow-600 hover:bg-yellow-500 text-white shadow-lg shadow-yellow-600/20 transition-all transform hover:-translate-y-0.5 font-medium flex items-center gap-2">
                        <i class="fas fa-save"></i> บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 2. เพิ่ม Script ของ CodeMirror และ Video Preview --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/htmlmixed/htmlmixed.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var editor = CodeMirror.fromTextArea(document.getElementById("content"), {
                mode: "htmlmixed",
                theme: "dracula",
                lineNumbers: true,
                lineWrapping: true,
                indentUnit: 4
            });
            editor.setSize("100%", "400px");
            
            // เพิ่มการอัปเดตค่ากลับไปที่ textarea อัตโนมัติเวลาที่มีการพิมพ์
            editor.on('change', function() {
                editor.save(); 
            });
        });

        // Preview Images Script
        function previewImages(event) {
            const container = document.getElementById('imagePreviewContainer');
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
                            'w-16 h-16 relative rounded-lg overflow-hidden border border-gray-600 shadow-md';
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

        // Preview Video Script
        function previewVideo(event) {
            const file = event.target.files[0];
            const container = document.getElementById('videoPreviewContainer');
            const video = document.getElementById('videoPreview');

            if (file) {
                container.classList.remove('hidden');
                const fileURL = URL.createObjectURL(file);
                video.src = fileURL;
            } else {
                container.classList.add('hidden');
                video.src = '';
            }
        }
    </script>
@endsection