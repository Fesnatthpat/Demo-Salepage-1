@extends('layout')

@section('title', 'ประวัติการสั่งซื้อ | Salepage Demo')

@section('content')
    <div class="container mx-auto p-4 lg:px-20 lg:py-10 max-w-7xl">
        <div class="bg-white border border-gray-200 rounded-lg p-6 lg:p-8 shadow-sm">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 border-b border-gray-200 pb-4">ประวัติการสั่งซื้อ</h1>

            @if ($orders->isEmpty())
                <div class="text-center py-20 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mx-auto text-gray-300 mb-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-400 mb-2">ไม่พบประวัติการสั่งซื้อ</h2>
                    <p class="text-gray-500 mb-6">คุณยังไม่มีคำสั่งซื้อใดๆ ในระบบ</p>
                    <a href="{{ route('allproducts') }}" class="btn btn-primary text-white px-8">ไปเลือกซื้อสินค้า</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    สินค้า
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    หมายเลขคำสั่งซื้อ
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    วันที่สั่งซื้อ
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    สถานะ
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    ยอดชำระทั้งหมด
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    จัดการ
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($orders as $order)
                                @php
                                    // 1. Status Logic
                                    $statusText = 'ไม่ระบุ';
                                    $statusClass = 'bg-gray-100 text-gray-800';
                                    switch ($order->status_id) {
                                        case 1: $statusText = 'รอชำระเงิน'; $statusClass = 'bg-yellow-100 text-yellow-800'; break;
                                        case 2: $statusText = 'กำลังดำเนินการ'; $statusClass = 'bg-blue-100 text-blue-800'; break;
                                        case 3: $statusText = 'จัดส่งแล้ว'; $statusClass = 'bg-green-100 text-green-800'; break;
                                        case 4: $statusText = 'สำเร็จ'; $statusClass = 'bg-emerald-100 text-emerald-800'; break;
                                        case 5: $statusText = 'ยกเลิก'; $statusClass = 'bg-red-100 text-red-800'; break;
                                    }

                                    // 2. Auto-Detect Image Logic (แก้ไขให้ค้นหาไฟล์จริง)
                                    $displayImage = 'https://via.placeholder.com/150?text=No+Image'; 
                                    $itemCount = 0;
                                    $debugInfo = 'Checking...'; 

                                    // ดึงรายการสินค้า
                                    if($order->relationLoaded('details')){
                                         $details = $order->details;
                                    } else {
                                         $details = \App\Models\OrderDetail::where('ord_id', $order->id)->get();
                                    }
                                    
                                    $itemCount = $details->count();
                                    $firstItem = $details->first();

                                    if ($firstItem) {
                                        // ดึง Product Model
                                        $productModel = \App\Models\ProductSalepage::with('images')->find($firstItem->pd_id);
                                        
                                        if ($productModel && $productModel->images->isNotEmpty()) {
                                            $dbImage = $productModel->images->sortBy('img_sort')->first();
                                            if(!$dbImage) $dbImage = $productModel->images->first();

                                            $rawPath = $dbImage->img_path; // ชื่อไฟล์จาก DB เช่น "image_abc.png"

                                            if ($rawPath) {
                                                if (filter_var($rawPath, FILTER_VALIDATE_URL)) {
                                                    $displayImage = $rawPath;
                                                } else {
                                                    // คลีนชื่อไฟล์ (เอา path เก่าออก) ให้เหลือแค่ชื่อไฟล์เพียวๆ
                                                    $cleanName = basename($rawPath); 

                                                    // รายชื่อห้องที่น่าสงสัย (Possible Paths)
                                                    $possiblePaths = [
                                                        'storage/' . $rawPath,              // กรณี DB เก็บ full path
                                                        'storage/' . $cleanName,            // กรณีอยู่ใน storage/ โดยตรง
                                                        'storage/uploads/' . $cleanName,    // กรณีอยู่ใน folder uploads
                                                        'storage/images/' . $cleanName,     // กรณีอยู่ใน folder images
                                                        'uploads/' . $cleanName,            // กรณีอยู่นอก storage (public/uploads)
                                                    ];

                                                    $found = false;
                                                    foreach ($possiblePaths as $path) {
                                                        // เช็คว่าไฟล์มีจริงไหมในเครื่อง (public_path คือ folder public)
                                                        if (file_exists(public_path($path))) {
                                                            $displayImage = asset($path);
                                                            $debugInfo = "Found in: $path";
                                                            $found = true;
                                                            break; 
                                                        }
                                                    }

                                                    if (!$found) {
                                                        // ถ้าหาไม่เจอจริงๆ ให้ลองใช้ path เดิมแบบวัดดวง
                                                        $displayImage = asset('storage/' . $rawPath);
                                                        $debugInfo = "Not Found (Try default)";
                                                    }
                                                }
                                            }
                                        }
                                    }
                                @endphp
                                <tr>
                                    {{-- ส่วนแสดงรูปภาพ --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-14 w-14 relative group">
                                                    <img class="h-14 w-14 rounded-md object-cover border border-gray-200 shadow-sm"
                                                        src="{{ $displayImage }}" 
                                                        alt="Product Image"
                                                        loading="lazy"
                                                        onerror="this.onerror=null;this.src='https://via.placeholder.com/150?text=Error';">
                                                    
                                                    @if ($itemCount > 1)
                                                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full border-2 border-white font-bold shadow-md z-10">
                                                            +{{ $itemCount - 1 }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- Debug Text: ถ้ายังไม่ขึ้น ให้ดูตรงนี้ว่ามันบอกว่าอะไร --}}
                                            {{-- <span class="text-[9px] text-red-500 mt-1">{{ $debugInfo }}</span> --}}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <span id="order-code-{{ $order->id }}"
                                                class="text-sm font-semibold text-gray-900">{{ $order->ord_code }}</span>
                                            <button onclick="copyToClipboard('order-code-{{ $order->id }}', this)"
                                                class="p-1 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 group relative transition-colors">
                                                <svg class="h-5 w-5 text-gray-400 group-hover:text-gray-600"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                                <span
                                                    class="absolute -top-8 left-1/2 -translate-x-1/2 w-max px-2 py-1 bg-gray-800 text-white text-xs rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                                                    Copy Code
                                                </span>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600">
                                            {{ $order->formatted_ord_date ?? $order->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm font-bold text-emerald-600">
                                            ฿{{ number_format($order->net_amount, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <a href="{{ route('order.show', ['orderCode' => $order->ord_code]) }}"
                                            class="text-indigo-600 hover:text-indigo-900 font-semibold hover:underline">ดูรายละเอียด</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function copyToClipboard(elementId, buttonElement) {
            const textToCopy = document.getElementById(elementId).innerText;
            const tooltip = buttonElement.querySelector('span');
            const originalTooltipText = "Copy Code";

            navigator.clipboard.writeText(textToCopy).then(() => {
                tooltip.innerText = 'Copied!';
                setTimeout(() => {
                    tooltip.innerText = originalTooltipText;
                }, 2000);
            }).catch(err => {
                tooltip.innerText = 'Failed';
                console.error('Failed to copy text: ', err);
                setTimeout(() => {
                    tooltip.innerText = originalTooltipText;
                }, 2000);
            });
        }
    </script>
@endpush