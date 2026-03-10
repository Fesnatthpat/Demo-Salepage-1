@extends('layouts.admin')

@section('title', 'จัดการรูปรีวิว')

@section('page-title')
    <div class="flex items-center gap-2 text-sm text-gray-400 overflow-x-auto whitespace-nowrap pb-1">
        <a href="{{ route('admin.products.index') }}" class="hover:text-emerald-400 transition-colors">
            <i class="fas fa-box mr-1"></i> สินค้า
        </a>
        <span class="text-gray-600">/</span>
        <a href="{{ route('admin.products.edit', $product->pd_sp_id) }}" class="hover:text-emerald-400 transition-colors">
            แก้ไข: {{ $product->pd_sp_name }}
        </a>
        <span class="text-gray-600">/</span>
        <span class="text-gray-100 font-bold text-emerald-400">จัดการรูปรีวิว</span>
    </div>
@endsection

@section('content')
    <div class="max-w-6xl mx-auto pb-10">

        {{-- ☁️ Upload Form Section (Drag & Drop + Previews) --}}
        <div x-data="{
            imagePreviews: [],
            isDragging: false,
            previewFiles(event) {
                this.imagePreviews = []; // เคลียร์รูปเก่าออกก่อน
        
                // รองรับทั้งจากการกดเลือกไฟล์ และการลากวาง (Drag & Drop)
                const files = event.target.files || event.dataTransfer.files;
        
                if (files && files.length > 0) {
                    // เอาไฟล์ที่ได้จากการลากวาง ไปใส่ใน input file จริงๆ
                    if (event.dataTransfer) {
                        this.$refs.fileInput.files = files;
                    }
        
                    for (let i = 0; i < files.length; i++) {
                        this.imagePreviews.push(URL.createObjectURL(files[i]));
                    }
                }
            },
            clearPreviews() {
                this.imagePreviews = [];
                this.$refs.fileInput.value = '';
            }
        }"
            class="bg-gray-800/80 backdrop-blur-sm rounded-3xl shadow-xl border border-gray-700 p-6 md:p-10 mb-8 relative overflow-hidden">

            {{-- Background Decoration --}}
            <div
                class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="mb-8 relative z-10">
                <h2 class="text-2xl font-extrabold text-white flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 border border-emerald-500/30">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    อัปโหลดรูปรีวิวใหม่
                </h2>
                <p class="text-sm text-gray-400 mt-2 ml-14">
                    รองรับไฟล์ JPEG, PNG, JPG, GIF (สามารถกด <kbd
                        class="px-1.5 py-0.5 bg-gray-700 rounded-md text-gray-300 font-mono text-xs">Ctrl</kbd>
                    เพื่อเลือกหลายรูปพร้อมกันได้)
                </p>
            </div>

            <form action="{{ route('admin.products.review-images.store', $product->pd_sp_id) }}" method="POST"
                enctype="multipart/form-data" class="relative z-10">
                @csrf

                {{-- Dropzone Area --}}
                <div class="relative w-full border-2 border-dashed rounded-2xl p-8 md:p-12 text-center transition-all duration-300 group"
                    :class="isDragging ? 'border-emerald-400 bg-emerald-400/10 scale-[1.01]' : (imagePreviews.length > 0 ?
                        'border-gray-600 bg-gray-900/50' :
                        'border-gray-600 hover:border-emerald-500 hover:bg-gray-700/50')"
                    @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
                    @drop.prevent="isDragging = false; previewFiles($event)">

                    <input type="file" name="images[]" x-ref="fileInput" @change="previewFiles" required multiple
                        accept="image/jpeg, image/png, image/jpg, image/gif, image/svg+xml"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                    {{-- สถานะ: ยังไม่ได้เลือกไฟล์ --}}
                    <div x-show="imagePreviews.length === 0"
                        class="flex flex-col items-center justify-center space-y-4 pointer-events-none transition-transform duration-300 group-hover:-translate-y-2">
                        <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center text-gray-400 shadow-inner border border-gray-700 transition-colors duration-300"
                            :class="isDragging ?
                                'text-emerald-400 border-emerald-500/50 bg-emerald-500/10 shadow-[0_0_15px_rgba(16,185,129,0.2)]' :
                                'group-hover:text-emerald-400'">
                            <i class="fas fa-file-image text-3xl" :class="isDragging ? 'animate-bounce' : ''"></i>
                        </div>
                        <div>
                            <p class="text-gray-200 font-bold text-lg mb-1"
                                x-text="isDragging ? 'วางไฟล์ที่นี่ได้เลย!' : 'คลิก หรือ ลากไฟล์มาวางที่นี่'"></p>
                            <p class="text-sm text-gray-500">อัปโหลดพร้อมกันได้ไม่จำกัดจำนวนไฟล์</p>
                        </div>
                    </div>

                    {{-- สถานะ: เลือกไฟล์แล้ว (Grid Previews) --}}
                    <div x-show="imagePreviews.length > 0" style="display: none;" class="pointer-events-none">
                        <div class="mb-6 flex items-center justify-center gap-2">
                            <span
                                class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400">
                                <i class="fas fa-check"></i>
                            </span>
                            <span class="text-gray-200 font-bold">พร้อมอัปโหลด <span class="text-emerald-400 text-lg mx-1"
                                    x-text="imagePreviews.length"></span> รูปภาพ</span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            <template x-for="(src, index) in imagePreviews" :key="index">
                                <div
                                    class="relative aspect-square rounded-xl overflow-hidden shadow-lg border-2 border-emerald-500/30 bg-gray-800 transform transition-all duration-300 hover:scale-105 hover:border-emerald-500 hover:shadow-emerald-900/40">
                                    <img :src="src"
                                        class="w-full h-full object-cover opacity-90 hover:opacity-100">
                                    <div
                                        class="absolute top-2 right-2 bg-emerald-500 text-white text-[10px] font-black px-2 py-0.5 rounded shadow-sm">
                                        NEW</div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- แสดง Error หากมี --}}
                @error('images')
                    <div
                        class="mt-4 p-4 bg-red-900/30 border border-red-500/50 rounded-xl flex items-start gap-3 text-red-400 text-sm animate-fade-in-up">
                        <i class="fas fa-exclamation-circle mt-0.5 text-lg"></i>
                        <span class="font-medium">{{ $message }}</span>
                    </div>
                @enderror
                @error('images.*')
                    <div
                        class="mt-4 p-4 bg-red-900/30 border border-red-500/50 rounded-xl flex items-start gap-3 text-red-400 text-sm animate-fade-in-up">
                        <i class="fas fa-exclamation-circle mt-0.5 text-lg"></i>
                        <span class="font-medium">{{ $message }}</span>
                    </div>
                @enderror

                {{-- ปุ่ม Actions --}}
                <div class="mt-8 flex justify-end gap-4 z-20 relative border-t border-gray-700/50 pt-6">
                    <button type="button" x-show="imagePreviews.length > 0" @click="clearPreviews" style="display: none;"
                        class="btn btn-ghost hover:bg-gray-700 text-gray-400 hover:text-white rounded-xl h-12 px-6 font-bold transition-colors">
                        ยกเลิกทั้งหมด
                    </button>
                    <button type="submit"
                        class="btn bg-emerald-600 hover:bg-emerald-700 text-white border-none rounded-xl h-12 px-8 font-bold shadow-lg shadow-emerald-900/30 transition-all transform active:scale-95 flex items-center gap-2"
                        :class="imagePreviews.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                        :disabled="imagePreviews.length === 0">
                        <i class="fas fa-cloud-upload-alt"></i>
                        อัปโหลด <span x-show="imagePreviews.length > 0" x-text="imagePreviews.length"
                            class="text-white bg-emerald-800/50 px-2 py-0.5 rounded-md ml-1"></span>
                    </button>
                </div>
            </form>
        </div>

        {{-- 🖼️ Existing Images Section --}}
        <div class="bg-gray-800 rounded-3xl shadow-lg border border-gray-700 p-6 md:p-10">
            <div class="flex items-center justify-between mb-8 border-b border-gray-700/50 pb-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center text-blue-400 border border-blue-500/30">
                        <i class="fas fa-images"></i>
                    </div>
                    รูปรีวิวทั้งหมด
                    <span
                        class="badge bg-blue-500 border-none text-white font-bold ml-2">{{ $product->reviewImages->count() }}</span>
                </h2>
            </div>

            @if ($product->reviewImages->isEmpty())
                <div class="py-20 text-center border-2 border-dashed border-gray-700 rounded-2xl bg-gray-800/50">
                    <div
                        class="w-24 h-24 mx-auto bg-gray-900 rounded-full flex items-center justify-center mb-6 shadow-inner border border-gray-800">
                        <i class="fas fa-camera-retro text-4xl text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-300 mb-2">ยังไม่มีรูปรีวิวสำหรับสินค้านี้</h3>
                    <p class="text-gray-500 text-sm">รูปรีวิวจะช่วยเพิ่มความมั่นใจให้ลูกค้า ลองอัปโหลดรูปแรกดูสิครับ!</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
                    @foreach ($product->reviewImages->sortByDesc('sort_order') as $image)
                        <div
                            class="relative group rounded-2xl overflow-hidden bg-gray-900 aspect-square shadow-md border border-gray-700/80 hover:border-red-500/50 transition-all duration-500">

                            <img src="{{ filter_var($image->image_url, FILTER_VALIDATE_URL) ? $image->image_url : asset('storage/' . $image->image_url) }}"
                                alt="Review Image"
                                class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110 opacity-80 group-hover:opacity-100">

                            {{-- Overlay & Delete Button --}}
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-gray-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center pb-4">
                                <form action="{{ route('admin.products.review-images.destroy', $image->id) }}"
                                    method="POST"
                                    class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 w-full px-4">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรูปภาพรีวิวนี้?')"
                                        class="w-full flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white py-2 rounded-xl text-sm font-bold shadow-lg transition-colors">
                                        <i class="fas fa-trash-alt"></i> ลบรูปนี้
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- 🔙 Bottom Navigation --}}
        <div class="mt-8 text-center">
            <a href="{{ route('admin.products.edit', $product->pd_sp_id) }}"
                class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl text-gray-300 bg-gray-800 hover:text-white hover:bg-gray-700 border border-gray-700 shadow-sm transition-all hover:-translate-y-1 font-bold">
                <i class="fas fa-arrow-left"></i> เสร็จสิ้นและกลับไปหน้าแก้ไขสินค้า
            </a>
        </div>
    </div>
@endsection
