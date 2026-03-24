@extends('layouts.admin')

@section('title', 'จัดการสินค้า')
@section('page-title', 'รายการสินค้าทั้งหมด')

@section('styles')
    <style>
        .slip-thumbnail {
            width: 48px;
            height: 48px;
            object-fit: cover;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s ease;
            background-color: #1f2937;
            border: 2px solid #374151;
        }

        .slip-thumbnail:hover {
            transform: scale(1.15) rotate(2deg);
            border-color: #10b981;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
            z-index: 10;
        }

        .custom-scroll::-webkit-scrollbar {
            height: 6px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: #1f2937;
            border-radius: 4px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 4px;
        }

        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
    </style>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header & Actions --}}
        <div
            class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 bg-gray-800/80 backdrop-blur-sm p-6 rounded-3xl shadow-lg border border-gray-700/50">
            <div>
                <h2 class="text-2xl font-extrabold text-white flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 border border-emerald-500/30">
                        <i class="fas fa-box-open"></i>
                    </div>
                    คลังสินค้าทั้งหมด
                    <span
                        class="text-xs font-bold text-emerald-400 bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20 ml-2">
                        {{ $products->total() }} รายการ
                    </span>
                </h2>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto">
                <form action="{{ route('admin.products.index') }}" method="GET"
                    class="w-full lg:w-auto flex flex-col sm:flex-row gap-3">
                    @if (request('status') !== null)
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    @if (request('type'))
                        <input type="hidden" name="type" value="{{ request('type') }}">
                    @endif

                    {{-- Dropdown หมวดหมู่ --}}
                    <div class="relative group w-full sm:w-48">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i
                                class="fas fa-th-large text-gray-500 group-focus-within:text-emerald-400 transition-colors text-sm"></i>
                        </div>
                        <select name="category_id" onchange="this.form.submit()"
                            class="select w-full pl-11 pr-4 bg-gray-900/50 border border-gray-600 text-gray-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-2xl h-12 transition-all appearance-none">
                            <option value="">ทุกหมวดหมู่</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search Input --}}
                    <div class="relative group w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-500 group-focus-within:text-emerald-400 transition-colors"></i>
                        </div>
                        <input type="text" name="search" placeholder="ค้นหาชื่อ หรือรหัสสินค้า..."
                            class="input w-full pl-11 pr-4 bg-gray-900/50 border border-gray-600 text-gray-200 placeholder-gray-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-2xl h-12 transition-all"
                            value="{{ request('search') }}">
                    </div>
                </form>

                <a href="{{ route('admin.products.create') }}"
                    class="btn bg-emerald-600 hover:bg-emerald-700 border-none text-white w-full sm:w-auto h-12 rounded-2xl shadow-lg shadow-emerald-900/20 transition-all transform active:scale-95">
                    <i class="fas fa-plus mr-1"></i> เพิ่มสินค้าใหม่
                </a>
            </div>
        </div>

        @if (session('success'))
            <div
                class="alert alert-success bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 shadow-sm rounded-2xl flex items-center gap-3 animate-fade-in-up">
                <i class="fas fa-check-circle text-xl"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Filters --}}
        <div class="bg-gray-800/50 p-2 border border-gray-700/50 rounded-2xl">
            <div class="flex overflow-x-auto custom-scroll gap-2 p-2">
                {{-- Status Filters --}}
                <div class="flex bg-gray-900/50 p-1.5 rounded-xl border border-gray-700">
                    <a href="{{ route('admin.products.index', array_merge(request()->query(), ['status' => null, 'page' => null])) }}"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition-all {{ request('status') === null ? 'bg-gray-700 text-white shadow' : 'text-gray-400 hover:text-gray-200' }}">
                        ทั้งหมด
                    </a>
                    <a href="{{ route('admin.products.index', array_merge(request()->query(), ['status' => '1', 'page' => null])) }}"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition-all {{ request('status') === '1' ? 'bg-emerald-500/20 text-emerald-400 shadow' : 'text-gray-400 hover:text-emerald-400' }}">
                        ใช้งานอยู่
                    </a>
                    <a href="{{ route('admin.products.index', array_merge(request()->query(), ['status' => '0', 'page' => null])) }}"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition-all {{ request('status') === '0' ? 'bg-red-500/20 text-red-400 shadow' : 'text-gray-400 hover:text-red-400' }}">
                        ระงับการขาย
                    </a>
                </div>

                {{-- Type Filters --}}
                <div class="flex bg-gray-900/50 p-1.5 rounded-xl border border-gray-700">
                    <a href="{{ route('admin.products.index', array_merge(request()->query(), ['type' => 'recommended', 'page' => null])) }}"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold transition-all {{ request('type') == 'recommended' ? 'bg-yellow-500/20 text-yellow-400 shadow' : 'text-gray-400 hover:text-yellow-400' }}">
                        <i class="fas fa-star"></i> แนะนำ
                    </a>
                    <a href="{{ route('admin.products.index', array_merge(request()->query(), ['type' => 'promotion', 'page' => null])) }}"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold transition-all {{ request('type') == 'promotion' ? 'bg-purple-500/20 text-purple-400 shadow' : 'text-gray-400 hover:text-purple-400' }}">
                        <i class="fas fa-tags"></i> มีส่วนลด
                    </a>
                    <a href="{{ route('admin.products.index', array_merge(request()->query(), ['type' => 'out_of_stock', 'page' => null])) }}"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold transition-all {{ request('type') == 'out_of_stock' ? 'bg-orange-500/20 text-orange-400 shadow' : 'text-gray-400 hover:text-orange-400' }}">
                        <i class="fas fa-box-open"></i> สินค้าหมด
                    </a>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-gray-800 rounded-3xl shadow-xl border border-gray-700 overflow-hidden">
            <div class="overflow-x-auto custom-scroll">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-900/80 text-gray-400 text-xs uppercase tracking-wider border-b border-gray-700">
                            <th class="px-6 py-4 font-bold text-center">รูปภาพ</th>
                            <th class="px-6 py-4 font-bold">ข้อมูลสินค้า</th>
                            <th class="px-6 py-4 font-bold text-right">ราคา/ส่วนลด</th>
                            <th class="px-6 py-4 font-bold text-center">คลัง (Stock)</th>
                            <th class="px-6 py-4 font-bold text-center">สถานะ</th>
                            <th class="px-6 py-4 font-bold text-right">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        @forelse ($products as $product)
                            <tr class="hover:bg-gray-700/30 transition-colors group">
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $imagePath = 'https://via.placeholder.com/150?text=No+Image';
                                        if ($product->images->isNotEmpty()) {
                                            $primaryImage = $product->images->sortByDesc('img_sort')->first();
                                            if ($primaryImage) {
                                                $path = $primaryImage->img_path ?? $primaryImage->image_path;
                                                $imagePath = \Illuminate\Support\Str::startsWith($path, 'http')
                                                    ? $path
                                                    : asset('storage/' . $path);
                                            }
                                        }
                                    @endphp
                                    <img src="{{ $imagePath }}" alt="{{ $product->pd_sp_name }}"
                                        class="mx-auto slip-thumbnail" data-slip-src="{{ $imagePath }}"
                                        onerror="this.src='https://via.placeholder.com/150?text=Error'">
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col justify-center min-w-[250px]">
                                        <div
                                            class="font-bold text-gray-100 text-base whitespace-normal line-clamp-2 leading-tight group-hover:text-emerald-400 transition-colors">
                                            {{ $product->pd_sp_name ?? 'ไม่พบสินค้าหลัก' }}
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2 mt-2">
                                            <span
                                                class="text-[10px] font-mono text-gray-400 bg-gray-900 px-2 py-0.5 rounded border border-gray-700">SKU:
                                                {{ $product->pd_sp_code }}</span>

                                            @if ($product->category)
                                                <span
                                                    class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">
                                                    <i class="fas fa-folder-open mr-1"></i> {{ $product->category->name }}
                                                </span>
                                            @endif

                                            <button type="button"
                                                class="toggle-recommended-btn outline-none transition-transform active:scale-95"
                                                data-id="{{ $product->pd_sp_id }}"
                                                data-url="{{ route('admin.products.toggleRecommended', $product->pd_sp_id) }}">
                                                @if ($product->is_recommended)
                                                    <span
                                                        class="inline-flex items-center gap-1 text-[10px] font-bold text-yellow-500 bg-yellow-500/10 px-2 py-0.5 rounded border border-yellow-500/20"
                                                        title="คลิกเพื่อยกเลิก">
                                                        <i class="fas fa-star"></i> แนะนำ
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1 text-[10px] font-bold text-gray-500 bg-gray-800 px-2 py-0.5 rounded border border-gray-600 hover:text-white"
                                                        title="คลิกเพื่อตั้งเป็นสินค้าแนะนำ">
                                                        <i class="far fa-star"></i> ทั่วไป
                                                    </span>
                                                @endif
                                            </button>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right align-middle">
                                    <div class="font-bold text-gray-200 text-base">
                                        ฿{{ number_format($product->pd_sp_price, 2) }}</div>
                                    @if ($product->pd_sp_discount > 0)
                                        <div
                                            class="inline-flex items-center gap-1 text-xs font-bold text-red-400 bg-red-500/10 px-2 py-0.5 rounded border border-red-500/20 mt-1">
                                            <i class="fas fa-arrow-down text-[10px]"></i>
                                            ฿{{ number_format($product->pd_sp_discount, 2) }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center align-middle">
                                    @if ($product->pd_sp_stock > 0)
                                        <span
                                            class="inline-flex items-center justify-center px-3 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-full font-bold text-sm">
                                            {{ number_format($product->pd_sp_stock) }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center justify-center px-3 py-1 bg-red-500/10 text-red-400 border border-red-500/20 rounded-full font-bold text-sm animate-pulse">
                                            สินค้าหมด
                                        </span>
                                    @endif
                                </td>

                                {{-- Status Toggle --}}
                                <td class="px-6 py-4 text-center align-middle" x-data="{ isActive: {{ $product->pd_sp_active ? 'true' : 'false' }}, isToggling: false }">
                                    <button type="button"
                                        @click="async () => {
                                            if(isToggling) return;
                                            isToggling = true;
                                            try {
                                                const res = await fetch(`/admin/products/{{ $product->pd_sp_id }}/toggle-status`, {
                                                    method: 'POST',
                                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                                });
                                                if(res.ok) {
                                                    const data = await res.json();
                                                    isActive = data.is_active;
                                                    showNotification('success', 'อัปเดตสถานะสำเร็จ');
                                                } else throw new Error('Server error');
                                            } catch(e) {
                                                showNotification('error', 'ข้อผิดพลาด', 'อัปเดตสถานะไม่สำเร็จ');
                                            } finally {
                                                isToggling = false;
                                            }
                                        }"
                                        :disabled="isToggling"
                                        class="relative inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold transition-all duration-300 cursor-pointer focus:outline-none hover:scale-105"
                                        :class="isActive ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' :
                                            'bg-gray-800 text-gray-400 border border-gray-600'">

                                        <div x-show="isToggling"
                                            class="absolute inset-0 flex items-center justify-center rounded-full bg-gray-900/80"
                                            style="display: none;">
                                            <i class="fas fa-circle-notch fa-spin text-white"></i>
                                        </div>
                                        <div class="w-2 h-2 rounded-full mr-2 transition-all duration-300"
                                            :class="isActive ? 'bg-emerald-400 shadow-[0_0_5px_rgba(16,185,129,0.8)]' :
                                                'bg-gray-500'">
                                        </div>
                                        <span x-text="isActive ? 'กำลังขาย' : 'ปิดการขาย'"></span>
                                    </button>
                                </td>

                                <td class="px-6 py-4 text-right align-middle">
                                    <div
                                        class="flex items-center justify-end gap-2 opacity-50 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('admin.products.edit', $product->pd_sp_id) }}"
                                            class="w-9 h-9 flex items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500 hover:text-white border border-indigo-500/20 transition-all"
                                            title="แก้ไข">
                                            <i class="fas fa-pen text-sm"></i>
                                        </a>

                                        <label for="delete-modal-{{ $product->pd_sp_id }}"
                                            class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white border border-red-500/20 transition-all cursor-pointer"
                                            title="ลบ">
                                            <i class="fas fa-trash text-sm"></i>
                                        </label>

                                        {{-- Modal สำหรับลบ --}}
                                        <input type="checkbox" id="delete-modal-{{ $product->pd_sp_id }}"
                                            class="modal-toggle" />
                                        <div class="modal modal-bottom sm:modal-middle">
                                            <div
                                                class="modal-box bg-gray-800 border border-gray-700 text-left rounded-3xl">
                                                <div class="flex items-center gap-4 mb-4 border-b border-gray-700 pb-4">
                                                    <div
                                                        class="w-12 h-12 rounded-full bg-red-500/20 flex items-center justify-center text-red-500 border border-red-500/30">
                                                        <i class="fas fa-exclamation-triangle text-xl"></i>
                                                    </div>
                                                    <h3 class="font-bold text-xl text-white">ยืนยันการลบสินค้า</h3>
                                                </div>
                                                <p class="text-gray-300 text-base whitespace-normal">คุณต้องการลบสินค้า
                                                    <span
                                                        class="font-bold text-white bg-gray-700 px-2 py-0.5 rounded">"{{ $product->pd_sp_name }}"</span>
                                                    ใช่หรือไม่?</p>
                                                <p
                                                    class="text-sm text-red-400 mt-4 bg-red-900/30 p-3 rounded-xl border border-red-500/30 whitespace-normal">
                                                    <i class="fas fa-info-circle mr-1"></i> การกระทำนี้ไม่สามารถย้อนกลับได้
                                                    ข้อมูลรูปภาพและตัวเลือกทั้งหมดจะถูกลบด้วย
                                                </p>

                                                <div class="modal-action border-t border-gray-700 pt-4 mt-6">
                                                    <form
                                                        action="{{ route('admin.products.destroy', $product->pd_sp_id) }}"
                                                        method="POST" class="m-0 w-full sm:w-auto">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="btn bg-red-600 hover:bg-red-700 border-none text-white w-full sm:w-auto rounded-xl">ลบสินค้าถาวร</button>
                                                    </form>
                                                    <label for="delete-modal-{{ $product->pd_sp_id }}"
                                                        class="btn btn-ghost text-gray-400 hover:text-white hover:bg-gray-700 w-full sm:w-auto mt-2 sm:mt-0 rounded-xl">ยกเลิก</label>
                                                </div>
                                            </div>
                                            <label class="modal-backdrop"
                                                for="delete-modal-{{ $product->pd_sp_id }}">Close</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-20">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <div
                                            class="w-24 h-24 bg-gray-900 rounded-full flex items-center justify-center mb-4 border border-gray-800 shadow-inner">
                                            <i class="fas fa-box-open text-5xl opacity-50"></i>
                                        </div>
                                        <p class="text-xl font-bold text-gray-400">ไม่พบข้อมูลสินค้า</p>
                                        <p class="text-sm mt-2">ลองเปลี่ยนเงื่อนไขการค้นหา หรือคลิกปุ่ม "เพิ่มสินค้าใหม่"
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($products->hasPages())
                <div class="bg-gray-900/50 px-6 py-4 border-t border-gray-700">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Image Preview Modal --}}
    <div id="slip-preview-modal" class="hidden fixed z-[1000] transition-opacity duration-200 pointer-events-none">
        <img src="" alt="Preview"
            class="max-w-[250px] sm:max-w-[350px] h-auto rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.5)] bg-gray-800 border-4 border-gray-700">
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ระบบแสดงรูปภาพขยาย (ปรับให้ฉลาดขึ้นบนมือถือไม่บังจอ)
            const modal = document.getElementById('slip-preview-modal');
            if (!modal) return;

            const modalImage = modal.querySelector('img');
            const thumbnails = document.querySelectorAll('.slip-thumbnail');
            let hideTimeout;

            thumbnails.forEach(thumb => {
                thumb.addEventListener('mouseenter', (e) => {
                    if (window.innerWidth < 768) return; // ไม่แสดง Hover บนมือถือ

                    clearTimeout(hideTimeout);
                    const rect = e.target.getBoundingClientRect();
                    modalImage.src = e.target.dataset.slipSrc;
                    modal.style.opacity = 0;
                    modal.classList.remove('hidden');

                    setTimeout(() => {
                        const modalRect = modal.getBoundingClientRect();
                        const viewportWidth = window.innerWidth;
                        const viewportHeight = window.innerHeight;
                        const margin = 15;

                        let top = rect.top;
                        let left = rect.right + margin;

                        if (left + modalRect.width > viewportWidth - margin) {
                            left = rect.left - modalRect.width - margin;
                        }
                        if (top + modalRect.height > viewportHeight - margin) {
                            top = viewportHeight - modalRect.height - margin;
                        }
                        if (top < margin) top = margin;
                        if (left < margin) left = margin;

                        modal.style.top = `${top}px`;
                        modal.style.left = `${left}px`;
                        modal.style.opacity = 1;
                    }, 50);
                });

                thumb.addEventListener('mouseleave', () => {
                    hideTimeout = setTimeout(() => {
                        modal.style.opacity = 0;
                        setTimeout(() => modal.classList.add('hidden'), 200);
                    }, 100);
                });
            });

            // Toggle Recommended AJAX
            document.querySelectorAll('.toggle-recommended-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.dataset.url;
                    const button = this;
                    const badge = button.querySelector('span');

                    badge.classList.add('opacity-50');
                    button.disabled = true;

                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (data.is_recommended) {
                                    badge.className =
                                        'inline-flex items-center gap-1 text-[10px] font-bold text-yellow-500 bg-yellow-500/10 px-2 py-0.5 rounded border border-yellow-500/20';
                                    badge.innerHTML = '<i class="fas fa-star"></i> แนะนำ';
                                } else {
                                    badge.className =
                                        'inline-flex items-center gap-1 text-[10px] font-bold text-gray-500 bg-gray-800 px-2 py-0.5 rounded border border-gray-600 hover:text-white';
                                    badge.innerHTML = '<i class="far fa-star"></i> ทั่วไป';
                                }
                                showNotification('success', 'อัปเดตสถานะแนะนำสำเร็จ');
                            } else {
                                showNotification('error', 'ข้อผิดพลาด', 'ไม่สามารถอัปเดตสถานะได้');
                            }
                        })
                        .catch(error => console.error('Error:', error))
                        .finally(() => {
                            badge.classList.remove('opacity-50');
                            button.disabled = false;
                        });
                });
            });
        });
    </script>
@endpush
