@extends('layouts.admin')

@section('title', 'สร้างโปรโมชั่นใหม่')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <a href="{{ route('admin.promotions.index') }}"
                class="btn btn-ghost btn-sm gap-2 pl-0 text-gray-400 hover:text-emerald-400 mb-2">
                <i class="fas fa-arrow-left"></i> กลับหน้ารายการ
            </a>
            <h1 class="text-3xl font-bold text-gray-100">สร้างโปรโมชั่นใหม่</h1>
            <p class="text-sm text-gray-400 mt-1">กำหนดเงื่อนไข ซื้อ X แถม Y เพื่อกระตุ้นยอดขาย</p>
        </div>

        <form action="{{ route('admin.promotions.store') }}" method="POST" autocomplete="off">
            @csrf
            @include('admin.promotions._form')
        </form>
    </div>
@endsection
