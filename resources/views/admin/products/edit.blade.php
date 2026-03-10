@extends('layouts.admin')

@section('title', 'แก้ไขสินค้า')

@section('page-title')
    <div class="flex items-center gap-2 text-sm text-gray-400 overflow-x-auto whitespace-nowrap pb-1">
        <a href="{{ route('admin.products.index') }}"
            class="hover:text-emerald-400 transition-colors flex items-center gap-1">
            <i class="fas fa-box"></i> สินค้าทั้งหมด
        </a>
        <span class="text-gray-600">/</span>
        <span class="text-gray-100 font-bold text-emerald-400 truncate max-w-[150px] sm:max-w-[300px]">
            {{ $productSalepage->pd_sp_name }}
        </span>
    </div>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto pb-36 md:pb-0">

        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 animate-fade-in-down">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-2xl bg-indigo-500/20 flex items-center justify-center text-indigo-400 border border-indigo-500/30">
                    <i class="fas fa-pen text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-white tracking-tight">แก้ไขข้อมูลสินค้า</h1>
                    <p class="text-sm text-gray-400 mt-1 font-mono">ID: {{ $productSalepage->pd_sp_code }}</p>
                </div>
            </div>

            {{-- ปุ่มจัดการรูปรีวิวแบบสวยๆ --}}
            <a href="{{ route('admin.products.review-images.show', $productSalepage->pd_sp_id) }}"
                class="btn bg-blue-600/20 hover:bg-blue-600 border border-blue-500/50 hover:border-transparent text-blue-400 hover:text-white rounded-xl shadow-lg w-full sm:w-auto h-12 flex items-center justify-center gap-2 font-bold transition-all group">
                <i class="fas fa-camera-retro group-hover:scale-110 transition-transform"></i> จัดการรูปรีวิวสินค้า
            </a>
        </div>

        <form action="{{ route('admin.products.update', $productSalepage->pd_sp_id) }}" method="POST"
            enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            @include('admin.products._form')

            {{-- Sticky Bottom Action Bar --}}
            <div
                class="fixed bottom-0 left-0 right-0 bg-gray-900/90 backdrop-blur-md border-t border-gray-700 p-4 z-50 md:static md:bg-transparent md:border-0 md:p-0 md:mt-8 shadow-[0_-10px_20px_rgba(0,0,0,0.3)] md:shadow-none transition-all">
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 max-w-7xl mx-auto w-full">
                    <a href="{{ route('admin.products.index') }}"
                        class="btn bg-gray-800 hover:bg-gray-700 text-gray-300 border-none w-full sm:w-auto h-14 sm:h-12 rounded-xl font-bold transition-colors">
                        <i class="fas fa-times mr-1"></i> ยกเลิก
                    </a>
                    <button type="submit"
                        class="btn bg-emerald-600 hover:bg-emerald-700 border-none text-white px-8 shadow-lg shadow-emerald-900/30 w-full sm:w-auto h-14 sm:h-12 rounded-xl font-bold text-base transition-transform active:scale-95">
                        <i class="fas fa-save mr-2"></i> บันทึกการอัปเดต
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
