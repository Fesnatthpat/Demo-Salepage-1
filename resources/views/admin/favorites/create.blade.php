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

    <form action="{{ route('admin.favorites.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            
            {{-- ฝั่งซ้าย: ข้อมูลข้อความ --}}
            <div class="xl:col-span-2 space-y-6">
                {{-- คำแนะนำจากระบบ --}}
                <div class="bg-blue-900/30 border border-blue-800 rounded-lg p-4 flex gap-4 text-blue-200 text-sm">
                    <i class="fas fa-lightbulb text-xl text-blue-400 mt-1"></i>
                    <div>
                        <strong class="block text-blue-300 mb-1">💡 คำแนะนำการสร้างเนื้อหาให้ตรงกับหน้าเว็บ:</strong>
                        <ul class="list-disc ml-4 space-y-1">
                            <li><strong>ยินดีต้อนรับ:</strong> พิมพ์หัวข้อ "ยินดีต้อนรับ" ใส่ข้อความยาวๆ และแนบรูปภาพขนาดใหญ่</li>
                            <li><strong>วิสัยทัศน์:</strong> พิมพ์หัวข้อ "วิสัยทัศน์ของเรา" ใส่ข้อความคำคม (ไม่ต้องแนบรูป)</li>
                            <li><strong>ติดต่อเรา:</strong> พิมพ์หัวข้อ "ติดต่อเรา" และใส่รายละเอียดอีเมล/เบอร์โทร</li>
                        </ul>
                    </div>
                </div>

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-300 mb-2">หัวข้อ (Title) <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="เช่น ยินดีต้อนรับ, วิสัยทัศน์ของเรา, หรือ ติดต่อเรา"
                           class="block w-full bg-gray-900 border-gray-600 rounded-lg shadow-sm text-white focus:ring-red-500 focus:border-red-500 px-4 py-3">
                    @error('title') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Content --}}
                <div>
                    <label for="content" class="block text-sm font-semibold text-gray-300 mb-2">รายละเอียด (Content) <span class="text-red-500">*</span></label>
                    <textarea name="content" id="content" rows="8" required placeholder="ใส่รายละเอียดเนื้อหาที่นี่..."
                              class="block w-full bg-gray-900 border-gray-600 rounded-lg shadow-sm text-white focus:ring-red-500 focus:border-red-500 px-4 py-3 leading-relaxed">{{ old('content') }}</textarea>
                    @error('content') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- ฝั่งขวา: รูปภาพและตั้งค่า --}}
            <div class="space-y-6">
                {{-- Image Upload --}}
                <div class="bg-gray-900/50 p-6 rounded-xl border border-gray-700">
                    <label for="image" class="block text-sm font-semibold text-gray-300 mb-3">รูปภาพประกอบ (ถ้ามี)</label>
                    
                    {{-- กรอบแสดงตัวอย่างรูปภาพ --}}
                    <div class="mb-4 w-full h-48 bg-gray-800 rounded-lg border-2 border-dashed border-gray-600 flex items-center justify-center overflow-hidden relative group">
                        <img id="imagePreview" src="" class="hidden object-cover w-full h-full absolute inset-0 z-10 transition-transform duration-300 group-hover:scale-105">
                        <div id="imagePlaceholder" class="text-center text-gray-500 z-0">
                            <i class="fas fa-cloud-upload-alt text-4xl mb-2"></i>
                            <p class="text-xs">อัปโหลดรูปภาพที่นี่</p>
                        </div>
                    </div>

                    <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)"
                           class="block w-full text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-white hover:file:bg-gray-600 cursor-pointer transition-colors">
                    @error('image') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Settings --}}
                <div class="bg-gray-900/50 p-6 rounded-xl border border-gray-700 space-y-5">
                    {{-- Sort Order --}}
                    <div>
                        <label for="sort_order" class="block text-sm font-semibold text-gray-300 mb-2">ลำดับการแสดงผล</label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                               class="block w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm text-white focus:ring-red-500 focus:border-red-500 px-4 py-2">
                        <p class="text-xs text-gray-500 mt-2">1 = บนสุด, 2 = ถัดลงมา</p>
                        @error('sort_order') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <hr class="border-gray-700">

                    {{-- Is Active --}}
                    <div class="flex items-center pt-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked
                               class="h-6 w-6 rounded border-gray-500 bg-gray-800 text-red-600 focus:ring-red-500 focus:ring-offset-gray-900 cursor-pointer">
                        <label for="is_active" class="ml-3 block text-sm font-medium text-gray-200 cursor-pointer">เปิดแสดงผลที่หน้าเว็บ</label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-8 pt-6 border-t border-gray-700 flex justify-end gap-4">
            <a href="{{ route('admin.favorites.index') }}" class="btn btn-ghost text-gray-300 hover:bg-gray-700">ยกเลิก</a>
            <button type="submit" class="btn bg-red-600 hover:bg-red-700 text-white border-none shadow-lg px-8">
                <i class="fas fa-save mr-2"></i> บันทึกข้อมูล
            </button>
        </div>
    </form>
</div>

{{-- Script แสดงตัวอย่างรูปภาพ --}}
<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('imagePreview');
            var placeholder = document.getElementById('imagePlaceholder');
            output.src = reader.result;
            output.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        if(event.target.files[0]){
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
@endsection