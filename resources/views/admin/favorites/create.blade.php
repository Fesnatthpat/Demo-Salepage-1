@extends('layouts.admin')

@section('title', 'สร้างเนื้อหา "เกี่ยวกับติดใจ"')

{{-- 1. เพิ่ม CSS ของ CodeMirror --}}
@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/theme/dracula.min.css">
    <style>
        /* ปรับแต่ง CodeMirror ให้เข้ากับดีไซน์ */
        .CodeMirror {
            height: 400px;
            /* ความสูงของ Editor */
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
            <h1 class="text-2xl font-bold text-white">สร้างเนื้อหาใหม่</h1>
            <p class="text-sm text-gray-400">เพิ่มเรื่องราวหรือไฮไลท์ใหม่ในหน้าเกี่ยวกับเรา</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700/50 overflow-hidden">
            <div class="p-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>

            <form action="{{ route('admin.favorites.store') }}" method="POST" enctype="multipart/form-data"
                class="p-6 md:p-8">
                @csrf

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
                                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                    placeholder="เช่น จุดเริ่มต้นของเรา..."
                                    class="block w-full pl-10 bg-gray-900 border border-gray-600 rounded-lg shadow-sm py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
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
                                {{-- Textarea ปกติจะถูกซ่อนและแทนที่ด้วย CodeMirror --}}
                                <textarea name="content" id="content" required>{{ old('content') }}</textarea>
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
                                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                                    class="block w-full bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-emerald-500 focus:border-emerald-500">
                            </div>

                            <div class="space-y-2 pt-2">
                                <label class="block text-sm font-medium text-gray-400 mb-2">สถานะ</label>
                                <label class="inline-flex items-center cursor-pointer group">
                                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                                    <div
                                        class="relative w-14 h-7 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-600">
                                    </div>
                                    <span
                                        class="ml-3 text-sm font-medium text-gray-300 group-hover:text-white transition-colors">เปิดแสดงผล</span>
                                </label>
                            </div>
                        </div>

                        {{-- Image Upload Card --}}
                        <div class="bg-gray-900 rounded-xl p-5 border border-gray-700/50">
                            <label class="block text-sm font-semibold text-gray-200 mb-3">
                                รูปภาพประกอบ <span class="text-xs text-gray-400 font-normal">(เลือกได้หลายรูป)</span>
                            </label>

                            <div id="imagePreviewContainer"
                                class="mb-4 w-full min-h-[140px] bg-gray-800 rounded-lg border-2 border-dashed border-gray-600 flex flex-wrap gap-2 p-3 items-start justify-center relative hover:border-gray-500 transition-colors">
                                <div id="imagePlaceholder"
                                    class="absolute inset-0 flex flex-col items-center justify-center text-gray-500 pointer-events-none">
                                    <i class="fas fa-cloud-upload-alt text-3xl mb-2"></i>
                                    <p class="text-xs">คลิกเลือกรูปภาพ</p>
                                </div>
                            </div>

                            <input type="file" name="images[]" id="images" accept="image/*" multiple
                                onchange="previewImages(event)"
                                class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500 cursor-pointer">
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-8 border-t border-gray-700 mt-8 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.favorites.index') }}"
                        class="px-5 py-2.5 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition-all font-medium">
                        ยกเลิก
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all transform hover:-translate-y-0.5 font-medium flex items-center gap-2">
                        <i class="fas fa-save"></i> บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 2. เพิ่ม Script ของ CodeMirror --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/htmlmixed/htmlmixed.min.js"></script>

    <script>
        // Initialize CodeMirror
        document.addEventListener('DOMContentLoaded', function() {
            var editor = CodeMirror.fromTextArea(document.getElementById("content"), {
                mode: "htmlmixed", // รองรับ HTML, CSS, JS ผสมกัน
                theme: "dracula", // ธีมสีมืด
                lineNumbers: true, // แสดงเลขบรรทัด
                lineWrapping: true, // ตัดบรรทัดอัตโนมัติ
                indentUnit: 4, // ย่อหน้า 4 ช่อง
                autoCloseTags: true
            });

            // บังคับให้ขนาดพอดีกับ Container
            editor.setSize("100%", "400px");
        });

        // Preview Images Script (เหมือนเดิม)
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
                            'w-20 h-20 relative rounded-lg overflow-hidden border border-gray-600 shadow-md group';
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-full h-full object-cover transition-transform group-hover:scale-110';
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
