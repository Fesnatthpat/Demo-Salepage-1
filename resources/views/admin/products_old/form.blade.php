@extends('layouts.admin')

@section('title', $product->exists ? 'แก้ไขสินค้า' : 'เพิ่มสินค้าใหม่')

@section('page-title')
    <a href="{{ route('admin.products.index') }}" class="text-gray-500 hover:text-gray-900">สินค้า</a> /
    <span class="text-gray-900">{{ $product->exists ? 'แก้ไขสินค้า' : 'เพิ่มสินค้าใหม่' }}</span>
@endsection

@section('content')
<form 
    action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" 
    method="POST" 
    enctype="multipart/form-data">
    @csrf
    @if ($product->exists)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Form Fields --}}
        <div class="lg:col-span-2">
            <div class="card bg-white shadow-md">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-control">
                        <label for="pd_name" class="label"><span class="label-text">ชื่อสินค้า*</span></label>
                        <input type="text" id="pd_name" name="pd_name" value="{{ old('pd_name', $product->pd_name) }}" class="input input-bordered" required>
                    </div>

                    <div class="form-control mt-4">
                        <label for="pd_details" class="label"><span class="label-text">รายละเอียดสินค้า</span></label>
                        <textarea id="pd_details" name="pd_details" class="textarea textarea-bordered h-32">{{ old('pd_details', $product->pd_details) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                         <div class="form-control">
                            <label for="pd_code" class="label"><span class="label-text">รหัสสินค้า (SKU)*</span></label>
                            <input type="text" id="pd_code" name="pd_code" value="{{ old('pd_code', $product->pd_code) }}" class="input input-bordered" required>
                        </div>
                        <div class="form-control">
                            <label for="pd_price" class="label"><span class="label-text">ราคาขาย*</span></label>
                            <input type="number" step="0.01" id="pd_price" name="pd_price" value="{{ old('pd_price', $product->pd_price) }}" class="input input-bordered" required>
                        </div>
                         <div class="form-control">
                            <label for="pd_full_price" class="label"><span class="label-text">ราคาเต็ม (สำหรับแสดงส่วนลด)</span></label>
                            <input type="number" step="0.01" id="pd_full_price" name="pd_full_price" value="{{ old('pd_full_price', $product->pd_full_price) }}" class="input input-bordered">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar for Status and Image --}}
        <div class="lg:col-span-1">
            <div class="space-y-8">
                <div class="card bg-white shadow-md">
                    <div class="card-body">
                        <h2 class="card-title">สถานะสินค้า</h2>
                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <span class="label-text">ฉบับร่าง</span> 
                                <input type="hidden" name="pd_status" value="0">
                                <input type="checkbox" name="pd_status" value="1" class="toggle toggle-success" {{ old('pd_status', $product->pd_status) == 1 ? 'checked' : '' }} />
                                <span class="label-text">เผยแพร่</span> 
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card bg-white shadow-md">
                    <div class="card-body">
                        <h2 class="card-title">รูปภาพสินค้า</h2>
                        @if($product->pd_img)
                        <img src="{{ asset('images/' . ($product->pd_img ?? 'img.png')) }}" alt="{{$product->pd_name}}" class="w-full h-auto rounded-lg mb-4">
                        @endif
                        <input type="file" name="pd_img_file" class="file-input file-input-bordered w-full" />
                        <p class="text-xs text-gray-500 mt-2">หากอัปโหลดไฟล์ใหม่ รูปภาพเดิมจะถูกแทนที่</p>
                    </div>
                </div>

                 <div class="card bg-white shadow-md">
                    <div class="card-body">
                         <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save mr-2"></i>
                            {{ $product->exists ? 'บันทึกการเปลี่ยนแปลง' : 'บันทึกสินค้าใหม่' }}
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-ghost btn-block mt-2">ยกเลิก</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
