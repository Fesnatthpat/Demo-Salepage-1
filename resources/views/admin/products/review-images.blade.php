@extends('layouts.admin')

@section('title', 'จัดการรูปรีวิว')

@section('page-title')
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('admin.products.index') }}" class="hover:text-emerald-400 transition-colors">
            <i class="fas fa-box mr-1"></i> สินค้า
        </a>
        <span>/</span>
        <a href="{{ route('admin.products.edit', $product->pd_sp_id) }}" class="hover:text-emerald-400 transition-colors">
            แก้ไข: {{ $product->pd_sp_name }}
        </a>
        <span>/</span>
        <span class="text-gray-100 font-medium">จัดการรูปรีวิว</span>
    </div>
@endsection

@section('content')
    <div class="max-w-5xl mx-auto">

        {{-- Upload Form Section (รองรับ Multiple Files & Previews) --}}
        <div x-data="{
            imagePreviews: [],
            previewFiles(event) {
                this.imagePreviews = []; // เคลียร์รูปเก่าออกก่อน
                const files = event.target.files;
                if (files) {
                    for (let i = 0; i < files.length; i++) {
                        this.imagePreviews.push(URL.createObjectURL(files[i]));
                    }
                }
            },
            clearPreviews() {
                this.imagePreviews = [];
                this.$refs.fileInput.value = '';
            }
        }" class="bg-gray-800 rounded-2xl shadow-xl border border-gray-700 p-6 md:p-8 mb-8">

            <div class="mb-6 flex justify-between items-end">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-images text-emerald-500"></i> อัปโหลดรูปรีวิวใหม่ (เลือกได้หลายรูป)
                    </h2>
                    <p class="text-sm text-gray-400 mt-1">กด Ctrl (หรือ Cmd) ค้างไว้ตอนเลือกไฟล์ เพื่อเลือกหลายๆ รูปพร้อมกัน
                    </p>
                </div>
            </div>

            <form action="{{ route('admin.products.review-images.store', $product->pd_sp_id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                {{-- Dropzone Area --}}
                <div class="relative w-full border-2 border-dashed rounded-xl p-8 text-center transition-all duration-300 hover:bg-gray-700/50"
                    :class="imagePreviews.length > 0 ? 'border-emerald-500 bg-gray-700/30' : 'border-gray-600'">

                    {{-- เพิ่ม multiple และเปลี่ยน name="image" เป็น name="images[]" --}}
                    <input type="file" name="images[]" x-ref="fileInput" @change="previewFiles" required multiple
                        accept="image/jpeg, image/png, image/jpg, image/gif, image/svg+xml"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                    {{-- สถานะยังไม่ได้เลือกไฟล์ --}}
                    <div x-show="imagePreviews.length === 0"
                        class="flex flex-col items-center justify-center space-y-3 pointer-events-none">
                        <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center text-gray-400 mb-2">
                            <i class="fas fa-file-image text-2xl"></i>
                        </div>
                        <p class="text-gray-300 font-medium text-lg">คลิกเพื่อเลือกไฟล์ หรือลากไฟล์มาวางที่นี่</p>
                        <p class="text-sm text-gray-500">รองรับไฟล์: JPEG, PNG, JPG, GIF, SVG (อัปโหลดพร้อมกันได้หลายไฟล์)
                        </p>
                    </div>

                    {{-- สถานะเลือกไฟล์แล้ว (แสดงรูปพรีวิวแบบ Grid) --}}
                    <div x-show="imagePreviews.length > 0" style="display: none;" class="pointer-events-none">
                        <div class="mb-4">
                            <span
                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-sm font-semibold">
                                <i class="fas fa-check-circle"></i> เลือกแล้ว <span x-text="imagePreviews.length"></span>
                                รูป
                            </span>
                        </div>
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                            <template x-for="(src, index) in imagePreviews" :key="index">
                                <div
                                    class="aspect-square rounded-lg overflow-hidden border border-gray-600 shadow-sm bg-gray-800">
                                    <img :src="src" class="w-full h-full object-cover">
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                @error('images')
                    <div
                        class="mt-3 p-3 bg-red-900/30 border border-red-500/50 rounded-lg flex items-start gap-2 text-red-400 text-sm">
                        <i class="fas fa-exclamation-circle mt-0.5"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
                @error('images.*')
                    <div
                        class="mt-3 p-3 bg-red-900/30 border border-red-500/50 rounded-lg flex items-start gap-2 text-red-400 text-sm">
                        <i class="fas fa-exclamation-circle mt-0.5"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror

                {{-- ปุ่ม Action --}}
                <div class="mt-6 flex justify-end gap-3 z-20 relative">
                    <button type="button" x-show="imagePreviews.length > 0" @click="clearPreviews" style="display: none;"
                        class="btn btn-ghost hover:bg-gray-700 text-gray-300 cursor-pointer">
                        ยกเลิก
                    </button>
                    <button type="submit"
                        class="btn bg-emerald-600 hover:bg-emerald-700 text-white border-none shadow-lg shadow-emerald-900/20 px-8"
                        :class="imagePreviews.length === 0 ? 'opacity-50 cursor-not-allowed' : ''">
                        <i class="fas fa-cloud-upload-alt mr-2"></i> อัปโหลด <span x-show="imagePreviews.length > 0"
                            x-text="imagePreviews.length" class="ml-1"></span> รูป
                    </button>
                </div>
            </form>
        </div>

        {{-- Existing Images Section (เหมือนเดิมที่คุณมี) --}}
        <div class="bg-gray-800 rounded-2xl shadow-xl border border-gray-700 p-6 md:p-8">
            <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fas fa-images text-blue-400"></i> รูปรีวิวทั้งหมด
                <span class="badge badge-primary badge-sm ml-2">{{ $product->reviewImages->count() }}</span>
            </h2>

            @if ($product->reviewImages->isEmpty())
                <div class="py-16 text-center border-2 border-dashed border-gray-700 rounded-xl bg-gray-800/50">
                    <div class="w-20 h-20 mx-auto bg-gray-700 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-camera-retro text-3xl text-gray-500"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-300 mb-1">ยังไม่มีรูปรีวิว</h3>
                    <p class="text-gray-500 text-sm">อัปโหลดรูปภาพแรกเพื่อแสดงในหน้าสินค้าของคุณ</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
                    @foreach ($product->reviewImages as $image)
                        <div
                            class="relative group rounded-xl overflow-hidden bg-gray-900 aspect-square shadow-md border border-gray-700">
                            <img src="{{ filter_var($image->image_url, FILTER_VALIDATE_URL) ? $image->image_url : asset('storage/' . $image->image_url) }}"
                                alt="Review Image"
                                class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-110">

                            <div
                                class="absolute inset-0 bg-black/60 backdrop-blur-[2px] opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
                                <form action="{{ route('admin.products.review-images.destroy', $image->id) }}"
                                    method="POST"
                                    class="transform scale-75 group-hover:scale-100 transition-transform duration-300 delay-75">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรูปภาพรีวิวนี้?')"
                                        class="btn btn-error btn-circle border-none shadow-lg shadow-red-900/50 text-white hover:scale-110 transition-transform">
                                        <i class="fas fa-trash-alt text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mt-8 text-center pb-8">
            <a href="{{ route('admin.products.edit', $product->pd_sp_id) }}"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-gray-400 hover:text-white hover:bg-gray-800 transition-colors font-medium">
                <i class="fas fa-arrow-left"></i> กลับไปหน้าแก้ไขสินค้า
            </a>
        </div>
    </div>
@endsection
