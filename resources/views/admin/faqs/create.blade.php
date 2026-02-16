@extends('layouts.admin')

@section('title', 'เพิ่มคำถามที่พบบ่อย')

@section('page-title')
    <div class="text-2xl font-bold">เพิ่มคำถามที่พบบ่อย (FAQ)</div>
@endsection

@section('content')
<div class="bg-gray-800 rounded-lg shadow-lg p-6 max-w-2xl mx-auto">
    <form action="{{ route('admin.faqs.store') }}" method="POST">
        @csrf
        <div class="space-y-6">
            {{-- Question --}}
            <div>
                <label for="question" class="block text-sm font-medium text-gray-300">คำถาม</label>
                <input type="text" name="question" id="question" value="{{ old('question') }}" required
                       class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white focus:ring-emerald-500 focus:border-emerald-500">
                @error('question')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Answer --}}
            <div>
                <label for="answer" class="block text-sm font-medium text-gray-300">คำตอบ</label>
                <textarea name="answer" id="answer" rows="5" required
                          class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white focus:ring-emerald-500 focus:border-emerald-500">{{ old('answer') }}</textarea>
                @error('answer')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Sort Order --}}
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-300">ลำดับการแสดงผล</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                       class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white focus:ring-emerald-500 focus:border-emerald-500">
                @error('sort_order')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Is Active --}}
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked
                       class="h-4 w-4 rounded border-gray-600 bg-gray-700 text-emerald-600 focus:ring-emerald-500">
                <label for="is_active" class="ml-2 block text-sm text-gray-300">เปิดใช้งาน</label>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-8 flex justify-end gap-4">
            <a href="{{ route('admin.faqs.index') }}" class="btn btn-ghost">ยกเลิก</a>
            <button type="submit" class="btn bg-emerald-600 hover:bg-emerald-700 text-white border-none">
                <i class="fas fa-save mr-2"></i> บันทึก
            </button>
        </div>
    </form>
</div>
@endsection
