@extends('layouts.admin')

@section('title', 'Admin Activity Log')

@section('content')
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
            {{-- Header --}}
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-100">Admin Activity Log</h2>
                <p class="text-sm text-gray-400">ประวัติการทำงานและการแก้ไขข้อมูลในระบบ</p>
            </div>

            {{-- Filter Alert --}}
            @if ($filter_admin_name)
                <div
                    class="mb-6 flex items-center justify-between rounded-lg bg-blue-900/30 border-l-4 border-blue-500 p-4 text-blue-200">
                    <p>กำลังแสดงรายการของ: <span class="font-bold text-white">{{ $filter_admin_name }}</span></p>
                    <a href="{{ route('admin.activity-log.index') }}"
                        class="text-sm hover:text-white hover:underline transition">
                        <i class="fas fa-times mr-1"></i>ล้างตัวกรอง
                    </a>
                </div>
            @endif

            {{-- Activities List --}}
            <div class="space-y-4">
                @forelse ($activities as $activity)
                    @php
                        // กำหนดสีและไอคอนให้จบในตัวแปรเดียว เพื่อให้ HTML ด้านล่างสะอาด
                        $config = match ($activity->action) {
                            'created' => ['color' => 'green', 'icon' => 'fas fa-plus', 'label' => 'สร้างใหม่'],
                            'updated' => ['color' => 'yellow', 'icon' => 'fas fa-pen', 'label' => 'แก้ไข'],
                            'deleted' => ['color' => 'red', 'icon' => 'fas fa-trash', 'label' => 'ลบข้อมูล'],
                            default => ['color' => 'gray', 'icon' => 'fas fa-info', 'label' => $activity->action],
                        };
                        $color = $config['color'];
                    @endphp

                    <div
                        class="overflow-hidden rounded-lg bg-gray-800 border border-gray-700 shadow transition hover:border-{{ $color }}-500/50">

                        {{-- Card Header --}}
                        <div
                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-gray-900/40 px-4 py-3 border-b border-gray-700">
                            <div class="flex items-center gap-3">
                                {{-- Icon Badge --}}
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-{{ $color }}-500/10 border border-{{ $color }}-500/20 text-{{ $color }}-400">
                                    <i class="{{ $config['icon'] }}"></i>
                                </div>

                                {{-- Action & Admin Info --}}
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="text-sm font-bold uppercase text-gray-200">{{ $config['label'] }}</span>
                                        <span class="rounded bg-gray-700 px-2 py-0.5 text-[10px] font-mono text-gray-400">
                                            {{ class_basename($activity->loggable_type) }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        โดย <span
                                            class="font-medium text-blue-400">{{ $activity->admin->name ?? 'Unknown' }}</span>
                                        <span class="mx-1">•</span>
                                        <span
                                            title="{{ $activity->created_at }}">{{ $activity->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- IP Address --}}
                            <div class="self-start sm:self-center">
                                <span
                                    class="rounded bg-gray-900 border border-gray-700 px-2 py-1 font-mono text-[10px] text-gray-500">
                                    IP: {{ $activity->ip_address }}
                                </span>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-4">
                            {{-- Target Info --}}
                            <div class="mb-4 flex items-center gap-2 text-sm">
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Target:</span>
                                <span class="font-medium text-gray-200">
                                    {{ $activity->loggable->pd_sp_name ?? ($activity->loggable->name ?? 'ID: ' . $activity->loggable_id) }}
                                </span>
                                {{-- View Button Logic --}}
                                @if ($activity->loggable && in_array($activity->loggable_type, ['App\Models\ProductSalepage', 'App\Models\Promotion']))
                                    @php
                                        $route =
                                            $activity->loggable_type === 'App\Models\ProductSalepage'
                                                ? 'admin.products.edit'
                                                : 'admin.promotions.edit';
                                    @endphp
                                    <a href="{{ route($route, $activity->loggable_id) }}"
                                        class="ml-2 text-xs text-blue-400 hover:underline">
                                        <i class="fas fa-external-link-alt mr-1"></i>ดูข้อมูล
                                    </a>
                                @elseif (!$activity->loggable)
                                    <span class="ml-2 text-xs italic text-red-400">(ข้อมูลถูกลบแล้ว)</span>
                                @endif
                            </div>

                            {{-- Changes Display --}}
                            @if (!empty($activity->changes))
                                <div class="rounded-md border border-gray-700 bg-gray-900/50 p-4">
                                    @if ($activity->action === 'updated' && isset($activity->changes['new']))
                                        {{-- View แบบ Compare (Old vs New) --}}
                                        <div
                                            class="grid grid-cols-12 gap-y-2 text-xs font-mono border-b border-gray-700 pb-2 mb-2 font-bold text-gray-500 uppercase tracking-wide">
                                            <div class="col-span-4 sm:col-span-3">Field</div>
                                            <div class="col-span-4 sm:col-span-4 text-red-400">Original</div>
                                            <div class="col-span-1 sm:col-span-1 text-center text-gray-600">➜</div>
                                            <div class="col-span-3 sm:col-span-4 text-green-400">New</div>
                                        </div>

                                        @foreach ($activity->changes['new'] as $key => $newValue)
                                            @php $oldValue = $activity->changes['original'][$key] ?? '-'; @endphp
                                            @if ($oldValue != $newValue)
                                                <div
                                                    class="grid grid-cols-12 gap-y-1 text-xs font-mono border-b border-gray-800 py-2 last:border-0 hover:bg-white/5 transition">
                                                    <div class="col-span-4 sm:col-span-3 text-gray-400 break-words pr-2">
                                                        {{ $key }}</div>
                                                    <div
                                                        class="col-span-4 sm:col-span-4 text-red-300/80 line-through break-all">
                                                        {{ is_array($oldValue) ? json_encode($oldValue) : Str::limit($oldValue, 40) }}
                                                    </div>
                                                    <div class="col-span-1 sm:col-span-1 text-center text-gray-600">➜</div>
                                                    <div
                                                        class="col-span-3 sm:col-span-4 text-green-400 break-all font-semibold">
                                                        {{ is_array($newValue) ? json_encode($newValue) : Str::limit($newValue, 40) }}
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @else
                                        {{-- View แบบ List (Created / Deleted) --}}
                                        <p class="mb-2 text-xs font-bold text-gray-500 uppercase">Data Snapshot:</p>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-xs font-mono">
                                            @php
                                                $data =
                                                    $activity->action === 'created'
                                                        ? $activity->changes['new'] ?? []
                                                        : $activity->changes['original'] ?? [];
                                            @endphp
                                            @foreach ($data as $key => $val)
                                                <div class="flex justify-between border-b border-gray-800 pb-1">
                                                    <span class="text-gray-500">{{ $key }}:</span>
                                                    <span class="text-gray-300 truncate max-w-[60%]">
                                                        {{ is_array($val) ? 'Array(...)' : Str::limit($val, 50) }}
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
                    {{-- Empty State --}}
                    <div
                        class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-700 bg-gray-800/30 py-16 text-center">
                        <div class="mb-4 text-5xl text-gray-600"><i class="far fa-clipboard"></i></div>
                        <p class="text-lg font-medium text-gray-400">ยังไม่มีประวัติการใช้งาน</p>
                        <p class="text-sm text-gray-500">ข้อมูลจะปรากฏเมื่อ Admin มีการแก้ไขข้อมูล</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
@endsection
