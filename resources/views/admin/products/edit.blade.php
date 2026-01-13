@extends('layouts.admin')

@section('title', 'แก้ไขสินค้า')

@section('page-title')
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.products.index') }}" class="hover:text-primary transition-colors">
            <i class="fas fa-box mr-1"></i> สินค้า
        </a>
        <span>/</span>
        <span class="text-gray-900 font-medium">แก้ไข: {{ $productSalepage->pd_sp_name }}</span>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- สังเกตการส่ง ID ไปที่ update --}}
    <form action="{{ route('admin.products.update', $productSalepage->pd_sp_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        @include('admin.products._form')

        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 z-50 md:static md:bg-transparent md:border-0 md:p-0 md:mt-8">
            <div class="flex justify-end gap-3 max-w-7xl mx-auto">
                <a href="{{ route('admin.products.index') }}" class="btn btn-ghost text-gray-600">
                    ยกเลิก
                </a>
                <button type="submit" class="btn btn-primary px-8 shadow-lg shadow-primary/30">
                    <i class="fas fa-save mr-2"></i> บันทึกการแก้ไข
                </button>
            </div>
        </div>
        <div class="h-20 md:hidden"></div>
    </form>
</div>
@endsection