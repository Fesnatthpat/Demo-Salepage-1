@extends('layouts.admin')

@section('title', 'สร้างโปรโมชั่นใหม่')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumb & Title --}}
        <div class="mb-8">
            <nav class="flex text-sm text-gray-500 mb-2">
                <a href="{{ route('admin.promotions.index') }}" class="hover:text-emerald-400 transition-colors">โปรโมชั่น</a>
                <span class="mx-2">/</span>
                <span class="text-gray-300">สร้างใหม่</span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-100">สร้างโปรโมชั่นใหม่</h1>
            <p class="text-gray-400 mt-1 font-light">กำหนดเงื่อนไขส่วนลดหรือของแถมเพื่อกระตุ้นยอดขาย</p>
        </div>

        <form action="{{ route('admin.promotions.store') }}" method="POST" autocomplete="off">
            @csrf
            @include('admin.promotions._form')
        </form>
    </div>
@endsection
