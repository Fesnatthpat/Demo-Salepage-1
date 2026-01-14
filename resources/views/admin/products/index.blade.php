@extends('layouts.admin')

@section('title', 'จัดการสินค้า')
@section('page-title', 'รายการสินค้าทั้งหมด')

@section('styles')
<style>
    .slip-thumbnail { /* Reusing slip-thumbnail class for product images for consistency */
        width: 50px;
        height: 50px;
        object-fit: cover;
        cursor: pointer;
        border-radius: 4px;
        transition: transform 0.2s;
    }
    .slip-thumbnail:hover {
        transform: scale(1.1);
    }
</style>
@endsection

@section('content')
    <div class="card bg-white shadow-md">
        <div class="card-body">
            <div class="flex flex-col sm:flex-row items-center gap-4 mb-6">
                <div class="flex-1">
                    <h2 class="card-title">สินค้าทั้งหมด ({{ $products->total() }})</h2>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto justify-end">
                    {{-- Search Form - Updated route to admin.products.index --}}
                    <form action="{{ route('admin.products.index') }}" method="GET" class="w-full sm:w-auto">
                        <div class="flex w-full sm:w-auto">
                            <input type="text" name="search" placeholder="ค้นหาชื่อ หรือรหัสสินค้า..."
                                class="input input-bordered w-full sm:w-64 rounded-r-none focus:outline-none"
                                value="{{ request('search') }}">

                            <button type="submit" class="btn btn-square btn-primary rounded-l-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    {{-- Create Button - Updated route to admin.products.create --}}
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary w-full sm:w-auto">
                        <i class="fas fa-plus mr-2"></i>
                        เพิ่มสินค้าใหม่
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success shadow-sm mb-4">
                    <div>
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Status Filter - Updated route to admin.products.index -->
            <div class="mb-4">
                <div class="join">
                    <a href="{{ route('admin.products.index', ['search' => request('search')]) }}"
                        class="join-item btn btn-sm {{ !request()->has('status') ? 'btn-active' : '' }}">ทั้งหมด</a>
                    <a href="{{ route('admin.products.index', ['status' => 1, 'search' => request('search')]) }}"
                        class="join-item btn btn-sm {{ request('status') == '1' ? 'btn-active' : '' }}">ใช้งาน</a>
                    <a href="{{ route('admin.products.index', ['status' => 0, 'search' => request('search')]) }}"
                        class="join-item btn btn-sm {{ request('status') == '0' ? 'btn-active' : '' }}">ไม่ใช้งาน</a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th class="text-center">รูปภาพ</th>
                            <th>สินค้า</th>
                            <th class="text-right">ราคา</th>
                            <th class="text-right">ส่วนลด</th>
                            <th>รายละเอียด</th>
                            <th class="text-center">สถานะ</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr class="hover">
                                <td class="align-middle text-center">
                                    @php
                                        $imagePath = 'img.png'; // Default placeholder
                                        if ($product->images->isNotEmpty()) {
                                            $primaryImage = $product->images->firstWhere('is_primary', true);
                                            if ($primaryImage) {
                                                $imagePath = $primaryImage->image_path;
                                            } else {
                                                $imagePath = $product->images->first()->image_path; // Fallback to first image
                                            }
                                        }
                                    @endphp
                                    <img src="{{ asset('storage/' . $imagePath) }}"
                                         alt="{{ $product->pd_sp_name ?? 'Product Image' }}"
                                         class="slip-thumbnail"
                                         data-slip-src="{{ asset('storage/' . $imagePath) }}">
                                </td>
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <div>
                                            <div class="font-bold w-64 truncate">
                                                {{ $product->pd_sp_name ?? 'ไม่พบสินค้าหลัก' }}</div>
                                            <div class="text-sm opacity-50">{{ $product->pd_code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right">฿{{ number_format($product->pd_sp_price, 2) }}</td>
                                <td class="text-right text-red-500">- ฿{{ number_format($product->pd_sp_discount, 2) }}
                                </td>
                                <td>
                                    <span
                                        class="text-sm text-gray-600 line-clamp-2 max-w-xs">{{ $product->pd_sp_details ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($product->pd_sp_active == 1)
                                        <span class="btn bg-green-500 text-white btn-xs">ใช้งาน</span>
                                    @else
                                        <span class="btn bg-gray-500 text-white btn-xs">ไม่ใช้งาน</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex justify-center items-center gap-2">
                                        {{-- Updated route to admin.products.edit --}}
                                        <a href="{{ route('admin.products.edit', $product->pd_sp_id) }}"
                                            class="btn btn-sm btn-warning text-white">
                                            <i class="fas fa-edit"></i> แก้ไข
                                        </a>

                                        {{-- Updated modal ID and route to admin.products.destroy --}}
                                        <label for="delete-modal-{{ $product->pd_sp_id }}"
                                            class="btn btn-sm btn-error text-white">
                                            <i class="fas fa-trash-alt"></i> ลบ
                                        </label>
                                    </div>

                                    {{-- Updated modal ID and route to admin.products.destroy --}}
                                    <input type="checkbox" id="delete-modal-{{ $product->pd_sp_id }}" class="modal-toggle" />
                                    <div class="modal">
                                        <div class="modal-box">
                                            <h3 class="font-bold text-lg">ยืนยันการลบ</h3>
                                            <p class="py-4">คุณแน่ใจหรือไม่ว่าต้องการลบสินค้า <span class="font-bold">"{{ $product->pd_sp_name }}"</span>?<br>การกระทำนี้ไม่สามารถย้อนกลับได้</p>
                                            <div class="modal-action">
                                                <form action="{{ route('admin.products.destroy', $product->pd_sp_id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-error">ลบสินค้า</button>
                                                </form>
                                                <label for="delete-modal-{{ $product->pd_sp_id }}" class="btn">ยกเลิก</label>
                                            </div>
                                        </div>
                                        <label class="modal-backdrop" for="delete-modal-{{ $product->pd_sp_id }}">Close</label>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-gray-500">
                                    ยังไม่มีข้อมูลสินค้าในระบบ
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

<!-- Slip Preview Modal -->
<div id="slip-preview-modal" style="display: none; position: fixed; z-index: 1000; transition: opacity 0.2s;">
    <img src="" alt="Slip Preview" style="max-width: 350px; height: auto; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); background-color: white;">
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
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

            // Use a short timeout to allow the image to load and get its dimensions
            setTimeout(() => {
                const modalRect = modal.getBoundingClientRect();
                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;
                const margin = 15; // Margin from viewport edges and cursor

                let top = rect.top;
                let left = rect.right + margin;

                // If it goes off-screen right, position it to the left
                if (left + modalRect.width > viewportWidth - margin) {
                    left = rect.left - modalRect.width - margin;
                }

                // If it goes off-screen bottom, align bottom of modal with viewport bottom
                if (top + modalRect.height > viewportHeight - margin) {
                    top = viewportHeight - modalRect.height - margin;
                }
                
                // Ensure it doesn't go off-screen top
                if (top < margin) {
                    top = margin;
                }

                // Ensure it doesn't go off-screen left
                 if (left < margin) {
                    left = margin;
                }

                modal.style.top = `${top}px`;
                modal.style.left = `${left}px`;
                modal.style.opacity = 1;
            }, 50);
        });

        thumb.addEventListener('mouseleave', () => {
            hideTimeout = setTimeout(() => {
                modal.style.opacity = 0;
                setTimeout(() => modal.style.display = 'none', 200); // Hide after transition
            }, 100);
        });
    });

    // Also hide the modal if the mouse enters the modal itself and then leaves
    modal.addEventListener('mouseenter', () => {
         clearTimeout(hideTimeout);
    });
     modal.addEventListener('mouseleave', () => {
        hideTimeout = setTimeout(() => {
            modal.style.opacity = 0;
            setTimeout(() => modal.style.display = 'none', 200);
        }, 100);
    });

});
</script>
@endpush