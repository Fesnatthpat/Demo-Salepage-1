@extends('layouts.admin')

@section('title', 'แก้ไขคำถามที่พบบ่อย')

@section('page-title')
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.faqs.index') }}" class="text-gray-400 hover:text-white transition-colors">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">แก้ไขคำถามที่พบบ่อย (FAQ)</h1>
            <p class="text-sm text-gray-400">แก้ไขข้อมูล: {{ Str::limit($faq->question, 50) }}</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700/50 overflow-hidden">
            <div class="p-1 bg-gradient-to-r from-yellow-500 to-orange-500"></div> {{-- Warning/Edit color theme --}}

            <form action="{{ route('admin.faqs.update', $faq->id) }}" method="POST" class="p-6 md:p-8 space-y-6">
                @csrf
                @method('PUT')

                {{-- Question --}}
                <div class="space-y-2">
                    <label for="question" class="block text-sm font-medium text-gray-200">
                        หัวข้อคำถาม <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                            <i class="fas fa-question-circle"></i>
                        </span>
                        <input type="text" name="question" id="question" value="{{ old('question', $faq->question) }}"
                            required
                            class="block w-full pl-10 bg-gray-900/50 border border-gray-600 rounded-lg shadow-sm py-2.5 text-white placeholder-gray-500 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                    </div>
                    @error('question')
                        <p class="flex items-center gap-1 text-sm text-red-400 mt-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Answer --}}
                <div class="space-y-2">
                    <label for="answer" class="block text-sm font-medium text-gray-200">
                        คำตอบ <span class="text-red-500">*</span>
                    </label>
                    <textarea name="answer" id="answer" rows="6" required
                        class="block w-full bg-gray-900/50 border border-gray-600 rounded-lg shadow-sm p-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all leading-relaxed">{{ old('answer', $faq->answer) }}</textarea>
                    @error('answer')
                        <p class="flex items-center gap-1 text-sm text-red-400 mt-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    {{-- Sort Order --}}
                    <div class="space-y-2">
                        <label for="sort_order" class="block text-sm font-medium text-gray-200">
                            ลำดับการแสดงผล
                        </label>
                        <input type="number" name="sort_order" id="sort_order"
                            value="{{ old('sort_order', $faq->sort_order) }}" min="0"
                            class="block w-full bg-gray-900/50 border border-gray-600 rounded-lg shadow-sm py-2.5 px-3 text-white focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        @error('sort_order')
                            <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Is Active Toggle --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-200 mb-3">สถานะการใช้งาน</label>
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                                @if (old('is_active', $faq->is_active)) checked @endif>
                            <div
                                class="relative w-14 h-7 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-600">
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-300 group-hover:text-white transition-colors">
                                @if ($faq->is_active)
                                    เปิดใช้งานอยู่
                                @else
                                    ปิดการใช้งาน
                                @endif
                            </span>
                        </label>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-6 border-t border-gray-700 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.faqs.index') }}"
                        class="px-5 py-2.5 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition-all font-medium">
                        ยกเลิก
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all transform hover:-translate-y-0.5 font-medium flex items-center gap-2">
                        <i class="fas fa-save"></i> บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
