@extends('layouts.admin')

@section('title', 'แก้ไขโปรโมชั่น')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8 flex justify-between items-end">
            <div>
                <a href="{{ route('admin.promotions.index') }}"
                    class="btn btn-ghost btn-sm gap-2 pl-0 text-gray-400 hover:text-emerald-400 mb-2">
                    <i class="fas fa-arrow-left"></i> กลับหน้ารายการ
                </a>
                <h1 class="text-3xl font-bold text-gray-100">แก้ไขโปรโมชั่น</h1>
                <div class="flex items-center gap-2 mt-1">
                    <span class="badge badge-neutral badge-sm bg-gray-700 text-gray-300 border-none">ID:
                        {{ $promotion->id }}</span>
                    <span class="text-sm text-gray-400">{{ $promotion->name }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.promotions.update', $promotion->id) }}" method="POST" autocomplete="off">
            @csrf
            @method('PUT')
            @include('admin.promotions._form')
        </form>
    </div>
@endsection
