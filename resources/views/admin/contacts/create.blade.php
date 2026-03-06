@extends('layouts.admin')

@section('title', 'เพิ่มข้อมูลติดต่อ')

@section('page-title')
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.contacts.index') }}" class="text-gray-400 hover:text-white transition-colors">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">เพิ่มข้อมูลติดต่อใหม่</h1>
            <p class="text-sm text-gray-400">กรอกรายละเอียดเพื่อสร้างช่องทางการติดต่อใหม่</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700/50 overflow-hidden">
            <div class="p-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>

            <form action="{{ route('admin.contacts.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
                @csrf

                {{-- Title --}}
                <div class="space-y-2">
                    <label for="title" class="block text-sm font-medium text-gray-200">หัวข้อ (Title) <span
                            class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                            <i class="fas fa-heading"></i>
                        </span>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            placeholder="เช่น สำนักงานใหญ่, ฝ่ายบริการลูกค้า"
                            class="block w-full pl-10 bg-gray-900/50 border border-gray-600 rounded-lg shadow-sm py-2.5 text-white placeholder-gray-500 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                    </div>
                    @error('title')
                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Content & Address Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Content --}}
                    <div class="space-y-2">
                        <label for="content" class="block text-sm font-medium text-gray-200">รายละเอียดเพิ่มเติม</label>
                        <div class="relative">
                            <textarea name="content" id="content" rows="4" placeholder="รายละเอียดอื่นๆ..."
                                class="block w-full bg-gray-900/50 border border-gray-600 rounded-lg shadow-sm p-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">{{ old('content') }}</textarea>
                        </div>
                        @error('content')
                            <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div class="space-y-2">
                        <label for="address" class="block text-sm font-medium text-gray-200">ที่อยู่ (Address)</label>
                        <div class="relative">
                            <textarea name="address" id="address" rows="4" placeholder="บ้านเลขที่, ถนน, แขวง/ตำบล..."
                                class="block w-full bg-gray-900/50 border border-gray-600 rounded-lg shadow-sm p-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">{{ old('address') }}</textarea>
                        </div>
                        @error('address')
                            <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Phone & Email Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="phone" class="block text-sm font-medium text-gray-200">เบอร์โทรศัพท์</label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                                <i class="fas fa-phone"></i>
                            </span>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                placeholder="02-XXX-XXXX"
                                class="block w-full pl-10 bg-gray-900/50 border border-gray-600 rounded-lg shadow-sm py-2.5 text-white placeholder-gray-500 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                        @error('phone')
                            <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-gray-200">อีเมล</label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                placeholder="contact@example.com"
                                class="block w-full pl-10 bg-gray-900/50 border border-gray-600 rounded-lg shadow-sm py-2.5 text-white placeholder-gray-500 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                        @error('email')
                            <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Map URL --}}
                <div class="space-y-2">
                    <label for="map_url" class="block text-sm font-medium text-gray-200">ลิงก์ Google Maps</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                            <i class="fas fa-map-marked-alt"></i>
                        </span>
                        <input type="url" name="map_url" id="map_url" value="{{ old('map_url') }}"
                            placeholder="https://maps.google.com/..."
                            class="block w-full pl-10 bg-gray-900/50 border border-gray-600 rounded-lg shadow-sm py-2.5 text-white placeholder-gray-500 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                    </div>
                    @error('map_url')
                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Sort & Active --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <div class="space-y-2">
                        <label for="sort_order" class="block text-sm font-medium text-gray-200">ลำดับการแสดงผล</label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                            class="block w-full bg-gray-900/50 border border-gray-600 rounded-lg shadow-sm py-2.5 px-3 text-white focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        @error('sort_order')
                            <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-200 mb-3">สถานะ</label>
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                            <div
                                class="relative w-14 h-7 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-600">
                            </div>
                            <span
                                class="ml-3 text-sm font-medium text-gray-300 group-hover:text-white transition-colors">เปิดใช้งาน</span>
                        </label>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="pt-6 border-t border-gray-700 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.contacts.index') }}"
                        class="px-5 py-2.5 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition-all font-medium">
                        ยกเลิก
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all transform hover:-translate-y-0.5 font-medium flex items-center gap-2">
                        <i class="fas fa-save"></i> บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
