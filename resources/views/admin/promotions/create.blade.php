@extends('layouts.admin')

@section('title', 'สร้างโปรโมชั่นใหม่')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Header --}}
        <div class="mb-10 animate-fade-in-down">
            <a href="{{ route('admin.promotions.index') }}"
                class="inline-flex items-center text-sm font-bold text-gray-500 hover:text-white mb-6 transition-all transform hover:-translate-x-1 bg-gray-800/50 px-4 py-2 rounded-lg border border-gray-700/50">
                <i class="fas fa-arrow-left mr-2"></i> กลับไปหน้าจัดการ
            </a>
            
            <div class="flex items-center gap-6 p-8 bg-gradient-to-r from-gray-800 to-gray-800/30 rounded-3xl border border-gray-700/50 shadow-2xl relative overflow-hidden">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/10 blur-3xl rounded-full pointer-events-none"></div>
                <div class="p-5 bg-emerald-500/10 rounded-2xl border border-emerald-500/20 shadow-[0_0_20px_rgba(16,185,129,0.15)] relative z-10">
                    <i class="fas fa-bullhorn text-emerald-400 text-4xl"></i>
                </div>
                <div class="relative z-10">
                    <h1 class="text-4xl font-extrabold text-white tracking-tight">สร้างแคมเปญใหม่</h1>
                    <p class="text-gray-400 text-base mt-2 font-medium">กำหนดเงื่อนไข ส่วนลด และของแถม เพื่อกระตุ้นยอดขายของคุณ</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.promotions.store') }}" method="POST" autocomplete="off">
            @csrf
            @include('admin.promotions._form')
        </form>
    </div>
@endsection