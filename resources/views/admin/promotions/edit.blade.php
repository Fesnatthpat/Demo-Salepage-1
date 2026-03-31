@extends('layouts.admin')

@section('title', 'แก้ไขโปรโมชั่น')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Header --}}
        <div class="mb-10 animate-fade-in-down">
            <a href="{{ route('admin.promotions.index') }}"
                class="inline-flex items-center text-sm font-bold text-gray-500 hover:text-white mb-6 transition-all transform hover:-translate-x-1 bg-gray-800/50 px-4 py-2 rounded-lg border border-gray-700/50">
                <i class="fas fa-arrow-left mr-2"></i> กลับไปหน้าจัดการ
            </a>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 p-8 bg-gradient-to-r from-gray-800 to-gray-800/30 rounded-3xl border border-gray-700/50 shadow-2xl relative overflow-hidden">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-500/10 blur-3xl rounded-full pointer-events-none"></div>
                
                <div class="flex items-center gap-6 relative z-10">
                    <div class="p-5 bg-indigo-500/10 rounded-2xl border border-indigo-500/20 shadow-[0_0_20px_rgba(99,102,241,0.15)]">
                        <i class="fas fa-edit text-indigo-400 text-4xl"></i>
                    </div>
                    <div>
                        <h1 class="text-4xl font-extrabold text-white tracking-tight">แก้ไขโปรโมชั่น</h1>
                        <div class="flex items-center gap-3 mt-3">
                            <span class="px-2.5 py-1 rounded-md bg-gray-900 border border-gray-700 text-gray-300 text-[10px] font-mono font-bold tracking-wider">ID: {{ $promotion->id }}</span>
                            <span class="text-gray-600 text-xs">•</span>
                            <span class="text-gray-400 text-xs font-medium"><i class="far fa-clock mr-1"></i> อัปเดตล่าสุด: {{ $promotion->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                {{-- Status Badge --}}
                <div class="flex items-center px-6 py-4 bg-gray-900/60 rounded-2xl border border-gray-700/50 backdrop-blur-sm relative z-10 shadow-inner">
                    <span class="text-[11px] font-black tracking-widest uppercase text-gray-500 mr-4">สถานะ:</span>
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $promotion->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 shadow-[0_0_10px_rgba(16,185,129,0.2)]' : 'bg-gray-700/50 text-gray-400 border border-gray-600/50' }}">
                        <span class="w-2 h-2 rounded-full {{ $promotion->is_active ? 'bg-emerald-400 animate-pulse' : 'bg-gray-500' }}"></span>
                        {{ $promotion->is_active ? 'Active' : 'Inactive' }}
                    </span>
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