@extends('layouts.admin')

@section('title', 'Admin Activity Log')

@section('content')
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
            {{-- Header --}}
            <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-white tracking-tight">Admin Activity Log</h2>
                    <p class="text-gray-400 mt-1">ตรวจสอบประวัติการเข้าใช้งานและการเปลี่ยนแปลงข้อมูลในระบบ</p>
                </div>
                <div class="text-right">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-widest">Total Logs:
                        {{ $activities->total() }}</span>
                </div>
            </div>

            {{-- Filter Alert --}}
            @if ($filter_admin_name)
                <div
                    class="mb-6 flex items-center justify-between rounded-xl bg-blue-500/10 border border-blue-500/20 p-4 text-blue-200 backdrop-blur-sm">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-500/20 p-2 rounded-lg">
                            <i class="fas fa-filter text-blue-400"></i>
                        </div>
                        <p>กำลังแสดงรายการของ: <span
                                class="font-bold text-white text-lg ml-1">{{ $filter_admin_name }}</span></p>
                    </div>
                    <a href="{{ route('admin.activity-log.index') }}"
                        class="flex items-center gap-2 px-3 py-1.5 bg-blue-500/20 hover:bg-blue-500/40 rounded-lg text-sm transition-all duration-200">
                        <i class="fas fa-times"></i> ล้างตัวกรอง
                    </a>
                </div>
            @endif

            {{-- Activities List --}}
            <div class="space-y-6">
                @forelse ($activities as $activity)
                    @php
                        $config = match ($activity->action) {
                            'created' => ['color' => 'green', 'icon' => 'fas fa-plus-circle', 'label' => 'สร้างใหม่'],
                            'updated' => ['color' => 'amber', 'icon' => 'fas fa-edit', 'label' => 'แก้ไขข้อมูล'],
                            'deleted' => ['color' => 'red', 'icon' => 'fas fa-trash-alt', 'label' => 'ลบข้อมูล'],
                            'updated_settings' => [
                                'color' => 'indigo',
                                'icon' => 'fas fa-cogs',
                                'label' => 'ตั้งค่าเว็บไซต์',
                            ],
                            'toggle_status' => [
                                'color' => 'blue',
                                'icon' => 'fas fa-toggle-on',
                                'label' => 'เปลี่ยนสถานะ',
                            ],
                            'logged_in' => [
                                'color' => 'emerald',
                                'icon' => 'fas fa-sign-in-alt',
                                'label' => 'เข้าสู่ระบบ',
                            ],
                            'failed_login' => [
                                'color' => 'rose',
                                'icon' => 'fas fa-exclamation-triangle',
                                'label' => 'พยายามเข้าระบบล้มเหลว',
                            ],
                            default => [
                                'color' => 'gray',
                                'icon' => 'fas fa-info-circle',
                                'label' => $activity->action,
                            ],
                        };
                        $color = $config['color'];
                    @endphp

                    <div
                        class="group overflow-hidden rounded-2xl bg-gray-800/50 border border-gray-700 shadow-sm transition-all duration-300 hover:shadow-{{ $color }}-500/10 hover:border-{{ $color }}-500/40">

                        {{-- Card Header --}}
                        <div
                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gray-800/80 px-5 py-4 border-b border-gray-700/50">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-{{ $color }}-500/10 border border-{{ $color }}-500/20 text-{{ $color }}-400 shadow-inner">
                                    <i class="{{ $config['icon'] }} text-xl"></i>
                                </div>

                                <div>
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span
                                            class="text-sm font-bold uppercase tracking-wide text-{{ $color }}-400">
                                            {{ $config['label'] }}
                                        </span>
                                        <span
                                            class="px-2 py-0.5 rounded-md bg-gray-700 text-[10px] font-bold text-gray-300 uppercase">
                                            {{ class_basename($activity->loggable_type) }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-400 flex items-center gap-2">
                                        <span class="flex items-center gap-1">
                                            <i class="far fa-user text-[10px]"></i>
                                            <span
                                                class="font-semibold {{ $activity->action === 'failed_login' ? 'text-rose-400' : 'text-blue-400' }}">
                                                {{ $activity->admin->name ?? ($activity->action === 'failed_login' ? 'บุคคลภายนอก (ผู้บุกรุก)' : 'Unknown') }}
                                            </span>
                                        </span>
                                        <span class="text-gray-600">|</span>
                                        <span class="flex items-center gap-1" title="{{ $activity->created_at }}">
                                            <i class="far fa-clock text-[10px]"></i>
                                            {{ $activity->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="hidden sm:block">
                                <span
                                    class="px-3 py-1.5 rounded-lg bg-gray-900/50 border border-gray-700 text-[11px] font-mono text-gray-500">
                                    <i class="fas fa-network-wired mr-1.5 opacity-50"></i>{{ $activity->ip_address }}
                                </span>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-5">
                            <div
                                class="mb-5 flex flex-wrap items-center gap-3 p-3 rounded-xl bg-gray-900/30 border border-gray-700/30">
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-gray-500">เป้าหมาย:</span>
                                <span class="text-sm font-semibold text-gray-200">
                                    @if ($activity->loggable)
                                        {{ $activity->loggable->pd_sp_name ?? ($activity->loggable->name ?? ($activity->loggable->title ?? 'ID: ' . $activity->loggable_id)) }}
                                    @else
                                        ID: {{ $activity->loggable_id }} (ข้อมูลถูกลบ/ไม่มีเป้าหมาย)
                                    @endif
                                </span>

                                @if (
                                    $activity->loggable &&
                                        in_array($activity->loggable_type, [
                                            'App\Models\ProductSalepage',
                                            'App\Models\Promotion',
                                            'App\Models\HomepagePopup',
                                            'App\Models\BirthdayPromotion',
                                        ]))
                                    @php
                                        $route = match ($activity->loggable_type) {
                                            'App\Models\ProductSalepage' => 'admin.products.edit',
                                            'App\Models\Promotion' => 'admin.promotions.edit',
                                            'App\Models\HomepagePopup' => 'admin.popups.edit',
                                            'App\Models\BirthdayPromotion' => 'admin.birthday-promotion.edit',
                                            default => null,
                                        };
                                    @endphp
                                    @if ($route)
                                        <a href="{{ route($route, $activity->loggable_id) }}"
                                            class="inline-flex items-center gap-1.5 ml-auto px-3 py-1 text-xs font-medium text-blue-400 hover:text-blue-300 hover:bg-blue-400/10 rounded-lg transition-colors">
                                            <i class="fas fa-external-link-alt"></i> ดูข้อมูลต้นทาง
                                        </a>
                                    @endif
                                @elseif (
                                    !$activity->loggable &&
                                        $activity->action !== 'deleted' &&
                                        !in_array($activity->action, ['logged_in', 'failed_login']))
                                    <span
                                        class="ml-auto flex items-center gap-1 text-[11px] font-medium text-red-400/80 italic">
                                        <i class="fas fa-exclamation-triangle"></i> ข้อมูลนี้ถูกลบออกจากระบบแล้ว
                                    </span>
                                @endif
                            </div>

                            @if (!empty($activity->changes))
                                <div class="rounded-xl border border-gray-700/50 bg-gray-900/40 overflow-hidden">
                                    @if ($activity->action === 'updated' && isset($activity->changes['new']))
                                        {{-- Header สำหรับ Compare --}}
                                        <div
                                            class="grid grid-cols-12 gap-2 px-4 py-2.5 bg-gray-900/80 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-700/50">
                                            <div class="col-span-4 sm:col-span-3">ชื่อหัวข้อ</div>
                                            <div class="col-span-4 sm:col-span-4 text-red-400">ข้อมูลเดิม</div>
                                            <div class="col-span-1 sm:col-span-1 text-center italic text-gray-600">แก้ไข
                                            </div>
                                            <div class="col-span-3 sm:col-span-4 text-green-400 text-right sm:text-left">
                                                ข้อมูลใหม่</div>
                                        </div>

                                        <div class="divide-y divide-gray-800">
                                            @foreach ($activity->changes['new'] as $key => $newValue)
                                                @php
                                                    $oldValue = $activity->changes['original'][$key] ?? '-';
                                                    // ส่วนการแปลภาษาไทย
                                                    $translatedKey = match ($key) {
                                                        'pd_sp_stock' => 'จำนวนสต็อก',
                                                        'updated_at' => 'วันที่อัปเดต',
                                                        'pd_sp_name' => 'ชื่อสินค้า/เซลเพจ',
                                                        'status' => 'สถานะ',
                                                        'price' => 'ราคา',
                                                        'description' => 'รายละเอียด',
                                                        default => str_replace('_', ' ', ucfirst($key)),
                                                    };
                                                @endphp
                                                @if ($oldValue != $newValue)
                                                    <div
                                                        class="grid grid-cols-12 gap-2 px-4 py-3 text-[13px] font-mono hover:bg-white/[0.02] transition-colors items-center">
                                                        <div class="col-span-4 sm:col-span-3 text-gray-400 font-medium truncate"
                                                            title="{{ $key }}">
                                                            {{ $translatedKey }}
                                                        </div>
                                                        <div
                                                            class="col-span-4 sm:col-span-4 text-red-300/70 line-through break-all decoration-red-500/50">
                                                            {{ is_array($oldValue) ? 'Array' : (Str::limit($oldValue, 40) ?: '-') }}
                                                        </div>
                                                        <div class="col-span-1 sm:col-span-1 text-center text-gray-600">
                                                            <i class="fas fa-chevron-right text-[10px]"></i>
                                                        </div>
                                                        <div
                                                            class="col-span-3 sm:col-span-4 text-green-400 break-all font-medium text-right sm:text-left">
                                                            {{ is_array($newValue) ? 'Array' : (Str::limit($newValue, 40) ?: '-') }}
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        {{-- View แบบ Snapshot List --}}
                                        <div class="px-4 py-2.5 bg-gray-900/80 border-b border-gray-700/50">
                                            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">
                                                ข้อมูลขณะทำรายการ (Snapshot):</p>
                                        </div>
                                        <div
                                            class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-0 divide-y md:divide-y-0 divide-gray-800 px-4 py-2">
                                            @php
                                                $data = in_array($activity->action, [
                                                    'created',
                                                    'failed_login',
                                                    'logged_in',
                                                ])
                                                    ? $activity->changes['new'] ?? ($activity->changes ?? [])
                                                    : $activity->changes['original'] ?? ($activity->changes ?? []);
                                            @endphp
                                            @foreach ($data as $key => $val)
                                                @php
                                                    $translatedKey = match ($key) {
                                                        'pd_sp_stock' => 'จำนวนสต็อก',
                                                        'updated_at' => 'วันที่อัปเดต',
                                                        'created_at' => 'วันที่สร้าง',
                                                        'username' => 'พยายามใช้ Username',
                                                        'ip' => 'ไอพีแอดเดรส',
                                                        'user_agent' => 'อุปกรณ์ / เบราว์เซอร์',
                                                        default => str_replace('_', ' ', ucfirst($key)),
                                                    };
                                                @endphp
                                                <div
                                                    class="flex justify-between py-2 border-b border-gray-800/50 last:border-0 text-[12px] font-mono">
                                                    <span class="text-gray-500">{{ $translatedKey }}</span>
                                                    <span class="text-gray-300 truncate ml-4 max-w-[60%]"
                                                        title="{{ is_array($val) ? 'Array' : $val }}">
                                                        {{ is_array($val) ? '{...}' : ($val ?: '-') }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div
                        class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-700 bg-gray-800/20 py-20 text-center">
                        <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gray-800 text-gray-600">
                            <i class="fas fa-history text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-300">ยังไม่มีประวัติการใช้งาน</h3>
                        <p class="text-gray-500 mt-2 max-w-xs">เมื่อมีการเพิ่ม ลบ หรือแก้ไขข้อมูลในระบบ
                            ประวัติจะมาปรากฏอยู่ที่นี่</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-10">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
@endsection
