@extends('layouts.admin')

@section('title', 'จัดการสินค้า')
@section('page-title', 'รายการสินค้าทั้งหมด')

@section('styles')
    <style>
        .slip-thumbnail {
            width: 50px;
            height: 50px;
            object-fit: cover;
            cursor: pointer;
            border-radius: 4px;
            transition: transform 0.2s;
            background-color: #374151;
            /* gray-700 */
            border: 1px solid #4b5563;
            /* gray-600 */
        }

        .slip-thumbnail:hover {
            transform: scale(1.1);
            border-color: #10b981;
            /* emerald-500 */
        }

        /* ปรับปรุง Scrollbar สำหรับปุ่ม Filter */
        .filter-scroll::-webkit-scrollbar {
            height: 6px;
        }

        .filter-scroll::-webkit-scrollbar-track {
            background: #1f2937;
            /* gray-900 */
            border-radius: 4px;
        }

        .filter-scroll::-webkit-scrollbar-thumb {
            background: #4b5563;
            /* gray-600 */
            border-radius: 4px;
        }

        .filter-scroll::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
            /* gray-500 */
        }
    </style>
@endsection

@section('content')
    <div class="card bg-gray-800 shadow-lg border border-gray-700">
        <div class="card-body">
            {{-- Header & Search Section --}}
            <div class="flex flex-col sm:flex-row items-center gap-4 mb-6">
                <div class="flex-1">
                    <h2 class="card-title text-gray-100">สินค้าทั้งหมด <span
                            class="text-sm font-normal text-gray-500">({{ $products->total() }})</span></h2>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto justify-end">
                    {{-- Search Form --}}
                    <form action="{{ route('admin.products.index') }}" method="GET" class="w-full sm:w-auto">
                        {{-- เก็บค่า filter ปัจจุบันไว้เวลาค้นหา --}}
                        @if (request('status') !== null)
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        @if (request('type'))
                            <input type="hidden" name="type" value="{{ request('type') }}">
                        @endif

                        <div class="flex w-full sm:w-auto relative">
                            <input type="text" name="search" placeholder="ค้นหาชื่อ หรือรหัสสินค้า..."
                                class="input input-bordered w-full sm:w-64 pr-12 bg-gray-700 border-gray-600 text-gray-200 placeholder-gray-400 focus:border-emerald-500 focus:ring-emerald-500"
                                value="{{ request('search') }}">

                            <button type="submit"
                                class="absolute right-0 top-0 bottom-0 btn btn-square btn-ghost text-gray-400 hover:text-emerald-400 hover:bg-transparent">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    {{-- Create Button --}}
                    <a href="{{ route('admin.products.create') }}"
                        class="btn btn-primary bg-emerald-600 hover:bg-emerald-700 border-none text-white w-full sm:w-auto shadow-md">
                        <i class="fas fa-plus mr-2"></i>
                        เพิ่มสินค้าใหม่
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success bg-green-900/50 border-green-800 text-green-200 shadow-sm mb-6">
                    <div>
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            {{-- 🔥 ส่วนตัวกรอง (Filter Groups) แยก 2 แถว 🔥 --}}
            <div
                class="flex flex-col gap-4 mb-6 bg-gray-900/50 p-4 rounded-xl border border-gray-700 overflow-x-auto filter-scroll">

                {{-- แถวที่ 1: สถานะ (Status) --}}
                <div class="flex items-center gap-3 min-w-max">
                    <span class="text-sm font-bold text-gray-400 whitespace-nowrap w-20">
                        <i class="fas fa-toggle-on mr-1"></i> สถานะ:
                    </span>
                    <div class="join">
                        {{-- ทั้งหมด --}}
                        <a href="{{ route('admin.products.index', array_merge(request()->query(), ['status' => null, 'page' => null])) }}"
                            class="join-item btn btn-sm border-gray-600 {{ request('status') === null ? 'bg-gray-600 text-white border-gray-500' : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            ทั้งหมด
                        </a>
                        {{-- ใช้งาน --}}
                        <a href="{{ route('admin.products.index', array_merge(request()->query(), ['status' => '1', 'page' => null])) }}"
                            class="join-item btn btn-sm border-gray-600 {{ request('status') === '1' ? 'bg-emerald-600 text-white border-emerald-500 hover:bg-emerald-700' : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            ใช้งาน
                        </a>
                        {{-- ไม่ใช้งาน --}}
                        <a href="{{ route('admin.products.index', array_merge(request()->query(), ['status' => '0', 'page' => null])) }}"
                            class="join-item btn btn-sm border-gray-600 {{ request('status') === '0' ? 'bg-red-600 text-white border-red-500 hover:bg-red-700' : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            ไม่ใช้งาน
                        </a>
                    </div>
                </div>

                {{-- แถวที่ 2: ประเภท/เงื่อนไข (Type) --}}
                <div class="flex items-center gap-3 min-w-max">
                    <span class="text-sm font-bold text-gray-400 whitespace-nowrap w-20">
                        <i class="fas fa-filter mr-1"></i> ประเภท:
                    </span>
                    <div class="join">
                        {{-- ทั้งหมด --}}
                        <a href="{{ route('admin.products.index', array_merge(request()->query(), ['type' => null, 'page' => null])) }}"
                            class="join-item btn btn-sm border-gray-600 {{ !request('type') ? 'bg-gray-600 text-white border-gray-500' : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            ทั้งหมด
                        </a>

                        {{-- แนะนำ --}}
                        <a href="{{ route('admin.products.index', array_merge(request()->query(), ['type' => 'recommended', 'page' => null])) }}"
                            class="join-item btn btn-sm border-gray-600 {{ request('type') == 'recommended' ? 'bg-yellow-600 text-white border-yellow-500 hover:bg-yellow-700' : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="fas fa-star text-xs mr-1"></i> แนะนำ
                        </a>

                        {{-- โปรโมชั่น --}}
                        <a href="{{ route('admin.products.index', array_merge(request()->query(), ['type' => 'promotion', 'page' => null])) }}"
                            class="join-item btn btn-sm border-gray-600 {{ request('type') == 'promotion' ? 'bg-purple-600 text-white border-purple-500 hover:bg-purple-700' : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="fas fa-tags text-xs mr-1"></i> โปรโมชั่น
                        </a>

                        {{-- ทั่วไป --}}
                        <a href="{{ route('admin.products.index', array_merge(request()->query(), ['type' => 'general', 'page' => null])) }}"
                            class="join-item btn btn-sm border-gray-600 {{ request('type') == 'general' ? 'bg-blue-600 text-white border-blue-500 hover:bg-blue-700' : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            ทั่วไป
                        </a>

                        {{-- สินค้าหมด --}}
                        <a href="{{ route('admin.products.index', array_merge(request()->query(), ['type' => 'out_of_stock', 'page' => null])) }}"
                            class="join-item btn btn-sm border-gray-600 {{ request('type') == 'out_of_stock' ? 'bg-orange-600 text-white border-orange-500 hover:bg-orange-700' : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="fas fa-box-open text-xs mr-1"></i> สินค้าหมด
                        </a>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="table w-full text-gray-300">
                    <thead>
                        <tr class="border-b border-gray-700 bg-gray-900/50 text-gray-400">
                            <th class="text-center">รูปภาพ</th>
                            <th>สินค้า</th>
                            <th class="text-right">ราคา</th>
                            <th class="text-right">ส่วนลด</th>
                            <th class="text-right">คลัง (Stock)</th>
                            <th>รายละเอียด</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr class="hover:bg-gray-700/50 transition-colors border-b border-gray-700 last:border-0">
                                <td class="align-middle text-center">
                                    @php
                                        // Logic รูปภาพ
                                        $imagePath = 'https://via.placeholder.com/150?text=No+Image';

                                        if ($product->images->isNotEmpty()) {
                                            // หาภาพปก
                                            $primaryImage = $product->images->sortByDesc('img_sort')->first();

                                            if ($primaryImage) {
                                                $path = $primaryImage->img_path ?? $primaryImage->image_path;
                                                $imagePath = \Illuminate\Support\Str::startsWith($path, 'http')
                                                    ? $path
                                                    : asset('storage/' . $path);
                                            }
                                        }
                                    @endphp
                                    <img src="{{ $imagePath }}" alt="{{ $product->pd_sp_name ?? 'Product Image' }}"
                                        class="slip-thumbnail" data-slip-src="{{ $imagePath }}"
                                        onerror="this.src='https://via.placeholder.com/150?text=Error'">
                                </td>
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <div>
                                            <div class="font-bold w-64 truncate flex items-center gap-2 text-gray-200">
                                                {{ $product->pd_sp_name ?? 'ไม่พบสินค้าหลัก' }}

                                                {{-- Badge สถานะพิเศษ --}}
                                                @if ($product->is_recommended)
                                                    <span class="badge badge-warning badge-xs text-yellow-900"
                                                        title="สินค้าแนะนำ">แนะนำ</span>
                                                @endif
                                                @if ($product->pd_sp_discount > 0)
                                                    <span class="badge badge-secondary badge-xs text-white"
                                                        title="ลดราคา">Sale</span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $product->pd_sp_code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right text-gray-300">฿{{ number_format($product->pd_sp_price, 2) }}</td>
                                <td class="text-right text-red-400">
                                    @if ($product->pd_sp_discount > 0)
                                        - ฿{{ number_format($product->pd_sp_discount, 2) }}
                                    @else
                                        <span class="text-gray-600">-</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if ($product->pd_sp_stock > 0)
                                        <span class="text-emerald-400">{{ number_format($product->pd_sp_stock) }}</span>
                                    @else
                                        <span class="font-bold text-red-500">สินค้าหมด</span>
                                    @endif
                                </td>
                                <td>
                                    <span
                                        class="text-sm text-gray-500 line-clamp-2 max-w-xs">{{ $product->pd_sp_description ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($product->pd_sp_active == 1)
                                        <span class="badge badge-success text-white">ใช้งาน</span>
                                    @else
                                        <span class="badge badge-ghost text-gray-400 bg-gray-700">ไม่ใช้งาน</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex justify-center items-center gap-2">
                                        <a href="{{ route('admin.products.edit', $product->pd_sp_id) }}"
                                            class="btn btn-sm btn-warning bg-yellow-600 hover:bg-yellow-700 border-none text-white">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <label for="delete-modal-{{ $product->pd_sp_id }}"
                                            class="btn btn-sm btn-error bg-red-600 hover:bg-red-700 border-none text-white">
                                            <i class="fas fa-trash-alt"></i>
                                        </label>

                                        {{-- Modal --}}
                                        <input type="checkbox" id="delete-modal-{{ $product->pd_sp_id }}"
                                            class="modal-toggle" />
                                        <div class="modal">
                                            <div class="modal-box bg-gray-800 border border-gray-700 text-gray-200">
                                                <h3 class="font-bold text-lg text-white">ยืนยันการลบ</h3>
                                                <p class="py-4 text-gray-400">คุณแน่ใจหรือไม่ว่าต้องการลบสินค้า <span
                                                        class="font-bold text-white">"{{ $product->pd_sp_name }}"</span>?<br>การกระทำนี้ไม่สามารถย้อนกลับได้
                                                </p>
                                                <div class="modal-action">
                                                    <form
                                                        action="{{ route('admin.products.destroy', $product->pd_sp_id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-error bg-red-600 border-none text-white">ลบสินค้า</button>
                                                    </form>
                                                    <label for="delete-modal-{{ $product->pd_sp_id }}"
                                                        class="btn btn-ghost text-gray-400 hover:text-white hover:bg-gray-700">ยกเลิก</label>
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
                                <td colspan="8" class="text-center py-12 text-gray-500">
                                    <div class="flex flex-col items-center opacity-60">
                                        <i class="fas fa-box-open text-4xl mb-3 text-gray-600"></i>
                                        ยังไม่มีข้อมูลสินค้าในระบบ หรือไม่พบข้อมูลตามเงื่อนไข
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-8">
                {{ $products->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    {{-- Image Preview Modal --}}
    <div id="slip-preview-modal" style="display: none; position: fixed; z-index: 1000; transition: opacity 0.2s;">
        <img src="" alt="Preview"
            style="max-width: 350px; height: auto; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); background-color: #1f2937; border: 2px solid #374151;">
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('slip-preview-modal');
            if (!modal) return;

            const modalImage = modal.querySelector('img');
            const thumbnails = document.querySelectorAll('.slip-thumbnail');
            let hideTimeout;

            thumbnails.forEach(thumb => {
                thumb.addEventListener('mouseenter', (e) => {
                    clearTimeout(hideTimeout);
                    const rect = e.target.getBoundingClientRect();
                    modalImage.src = e.target.dataset.slipSrc;
                    modal.style.opacity = 0;
                    modal.style.display = 'block';

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
                        setTimeout(() => modal.style.display = 'none', 200);
                    }, 100);
                });
            });
        });
    </script>
@endpush
