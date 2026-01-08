@extends('layouts.admin')

@section('title', $salePage->exists ? 'แก้ไขราคา SalePage' : 'เพิ่มราคา SalePage ใหม่')

@section('page-title')
    <a href="{{ route('admin.salepages.index') }}" class="text-gray-500 hover:text-gray-900">ราคาสินค้า SalePage</a> /
    <span class="text-gray-900">{{ $salePage->exists ? 'แก้ไขราคา' : 'เพิ่มราคาใหม่' }}</span>
@endsection

@section('content')
<form 
    action="{{ $salePage->exists ? route('admin.salepages.update', $salePage) : route('admin.salepages.store') }}" 
    method="POST">
    @csrf
    @if ($salePage->exists)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Form Fields --}}
        <div class="lg:col-span-2">
            <div class="card bg-white shadow-md">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-error shadow-sm">
                            <div>
                                <i class="fas fa-exclamation-triangle"></i>
                                <ul class="list-disc ml-4">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <div class="form-control">
                        <label for="pd_code" class="label"><span class="label-text">เลือกสินค้าหลัก*</span></label>
                        <select name="pd_code" id="pd_code" class="select select-bordered" {{ $salePage->exists ? 'disabled' : 'required' }}>
                            <option disabled {{ !$salePage->exists ? 'selected' : '' }}>เลือกสินค้า</option>
                            @foreach($products as $product)
                                <option value="{{ $product->pd_code }}" {{ old('pd_code', $salePage->pd_code) == $product->pd_code ? 'selected' : '' }}>
                                    {{ $product->pd_name }} ({{$product->pd_code}})
                                </option>
                            @endforeach
                        </select>
                         @if ($salePage->exists)
                            <input type="hidden" name="pd_code" value="{{ $salePage->pd_code }}">
                            <div class="text-xs text-gray-500 mt-1">ไม่สามารถเปลี่ยนสินค้าหลักได้ในหน้าแก้ไข</div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="form-control">
                            <label for="pd_sp_price" class="label"><span class="label-text">ราคา SalePage*</span></label>
                            <input type="number" step="0.01" id="pd_sp_price" name="pd_sp_price" value="{{ old('pd_sp_price', $salePage->pd_sp_price) }}" class="input input-bordered" required>
                        </div>
                         <div class="form-control">
                            <label for="pd_sp_discount" class="label"><span class="label-text">ส่วนลด (จำนวนเงิน)</span></label>
                            <input type="number" step="0.01" id="pd_sp_discount" name="pd_sp_discount" value="{{ old('pd_sp_discount', $salePage->pd_sp_discount) }}" class="input input-bordered">
                        </div>
                    </div>

                    <div class="form-control mt-4">
                        <label for="pd_sp_details" class="label"><span class="label-text">รายละเอียดสำหรับ SalePage</span></label>
                        <textarea id="pd_sp_details" name="pd_sp_details" class="textarea textarea-bordered h-24">{{ old('pd_sp_details', $salePage->pd_sp_details) }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        {{-- Sidebar for Status and Save --}}
        <div class="lg:col-span-1">
            <div class="space-y-8">
                <div class="card bg-white shadow-md">
                    <div class="card-body">
                        <h2 class="card-title">สถานะ</h2>
                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <span class="label-text">ไม่ใช้งาน</span> 
                                <input type="hidden" name="pd_sp_active" value="0">
                                <input type="checkbox" name="pd_sp_active" value="1" class="toggle toggle-success" {{ old('pd_sp_active', $salePage->pd_sp_active) == 1 ? 'checked' : '' }} />
                                <span class="label-text">ใช้งาน</span> 
                            </label>
                        </div>
                    </div>
                </div>

                 <div class="card bg-white shadow-md">
                    <div class="card-body">
                         <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save mr-2"></i>
                            {{ $salePage->exists ? 'บันทึกการเปลี่ยนแปลง' : 'สร้างราคาใหม่' }}
                        </button>
                        <a href="{{ route('admin.salepages.index') }}" class="btn btn-ghost btn-block mt-2">ยกเลิก</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
