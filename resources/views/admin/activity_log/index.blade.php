@extends('layouts.admin')

@section('title', 'Admin Activity Log')

@section('content')
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
            <div class="flex justify-between items-end">
                <div>
                    <h2 class="text-2xl font-semibold leading-tight text-gray-100">Admin Activity Log</h2>
                    <p class="text-sm text-gray-400 mt-1">ประวัติการทำงานของผู้ดูแลระบบ</p>
                </div>
            </div>

            {{-- Filter Alert --}}
            @if ($filter_admin_name)
                <div class="mt-6 bg-blue-900/20 border-l-4 border-blue-500 text-blue-200 p-4 rounded-r" role="alert">
                    <div class="flex items-center justify-between">
                        <p>แสดงรายการของ: <span class="font-bold">{{ $filter_admin_name }}</span></p>
                        <a href="{{ route('admin.activity-log.index') }}"
                            class="text-sm text-blue-400 hover:text-white underline transition">
                            <i class="fas fa-times mr-1"></i>ล้างตัวกรอง
                        </a>
                    </div>
                </div>
            @endif

            <div class="mt-6 space-y-4">
                @forelse ($activities as $activity)
                    @php
                        $action_color = match ($activity->action) {
                            'created' => 'green',
                            'updated' => 'yellow',
                            'deleted' => 'red',
                            default => 'gray',
                        };

                        $action_icon = match ($activity->action) {
                            'created' => 'fas fa-plus-circle',
                            'updated' => 'fas fa-pencil-alt',
                            'deleted' => 'fas fa-trash-alt',
                            default => 'fas fa-info-circle',
                        };
                    @endphp

                    <div
                        class="bg-gray-800 rounded-lg border border-gray-700 shadow-sm overflow-hidden hover:border-{{ $action_color }}-500/50 transition-all duration-200">
                        {{-- Header --}}
                        <div
                            class="px-4 py-3 border-b border-gray-700 bg-gray-900/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-{{ $action_color }}-900/50 flex items-center justify-center border border-{{ $action_color }}-500/30">
                                    <i class="{{ $action_icon }} text-{{ $action_color }}-400 text-sm"></i>
                                </div>
                                <div>
                                    <div class="flex items-baseline gap-2">
                                        <span
                                            class="font-bold text-gray-200 uppercase text-sm tracking-wide">{{ $activity->action }}</span>
                                        <span class="text-xs text-gray-400 font-mono bg-gray-700 px-1.5 py-0.5 rounded">
                                            {{ class_basename($activity->loggable_type) }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        โดย
                                        @if ($activity->admin)
                                            <a href="{{ route('admin.activity-log.index', ['admin_id' => $activity->admin->id]) }}"
                                                class="font-medium text-blue-400 hover:text-blue-300 hover:underline">
                                                {{ $activity->admin->name }}
                                            </a>
                                        @else
                                            <span class="italic">Unknown Admin</span>
                                        @endif
                                        <span class="mx-1 text-gray-600">•</span>
                                        <span title="{{ $activity->created_at->format('d/m/Y H:i:s') }}">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="text-[10px] text-gray-500 font-mono self-start sm:self-center bg-gray-800 px-2 py-1 rounded border border-gray-700">
                                IP: {{ $activity->ip_address }}
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="p-4 space-y-4">
                            {{-- Target Object --}}
                            <div class="flex items-start gap-2 text-sm">
                                <span
                                    class="text-gray-500 min-w-[60px] uppercase text-[10px] font-bold tracking-wider pt-1">Target:</span>
                                <div class="flex-1">
                                    <span class="text-gray-200 font-medium">
                                        {{ $activity->loggable->pd_sp_name ?? ($activity->loggable->name ?? 'ID: ' . $activity->loggable_id) }}
                                    </span>

                                    {{-- Dynamic Links --}}
                                    @if ($activity->loggable)
                                        @if ($activity->loggable_type === 'App\Models\ProductSalepage')
                                            <a href="{{ route('admin.products.edit', $activity->loggable_id) }}"
                                                class="ml-2 text-xs text-blue-400 hover:text-blue-300 hover:underline"><i
                                                    class="fas fa-external-link-alt mr-1"></i>ดูสินค้า</a>
                                        @elseif ($activity->loggable_type === 'App\Models\Promotion')
                                            <a href="{{ route('admin.promotions.edit', $activity->loggable_id) }}"
                                                class="ml-2 text-xs text-blue-400 hover:text-blue-300 hover:underline"><i
                                                    class="fas fa-external-link-alt mr-1"></i>ดูโปรโมชั่น</a>
                                        @endif
                                    @else
                                        <span class="ml-2 text-xs text-red-400 italic">(ข้อมูลถูกลบไปแล้ว)</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Changes Diff --}}
                            @if ($activity->changes)
                                <div class="flex items-start gap-2 text-sm">
                                    <span
                                        class="text-gray-500 min-w-[60px] uppercase text-[10px] font-bold tracking-wider pt-1">Diff:</span>
                                    <div
                                        class="flex-1 bg-gray-900 rounded border border-gray-700 p-3 font-mono text-xs overflow-x-auto">

                                        @if (isset($activity->changes['original']) && isset($activity->changes['new']))
                                            {{-- กรณี Updated: แสดงเฉพาะค่าที่เปลี่ยน --}}
                                            <table class="w-full text-left border-collapse">
                                                @php $hasChanges = false; @endphp
                                                @foreach ($activity->changes['new'] as $key => $newValue)
                                                    @php
                                                        $oldValue = $activity->changes['original'][$key] ?? null;
                                                    @endphp
                                                    @if ($oldValue != $newValue)
                                                        @php $hasChanges = true; @endphp
                                                        <tr class="border-b border-gray-800 last:border-0">
                                                            <td
                                                                class="py-1 pr-4 text-gray-500 font-semibold select-none w-1/4">
                                                                {{ $key }}</td>
                                                            <td class="py-1 px-2 text-red-400 line-through decoration-red-500/50 w-1/3 truncate max-w-[150px]"
                                                                title="{{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}">
                                                                {{ is_array($oldValue) ? json_encode($oldValue) : Str::limit($oldValue, 50) }}
                                                            </td>
                                                            <td class="py-1 px-2 text-gray-600 w-4">➜</td>
                                                            <td class="py-1 pl-2 text-green-400 w-1/3 truncate max-w-[150px]"
                                                                title="{{ is_array($newValue) ? json_encode($newValue) : $newValue }}">
                                                                {{ is_array($newValue) ? json_encode($newValue) : Str::limit($newValue, 50) }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach

                                                @if (!$hasChanges)
                                                    <tr>
                                                        <td colspan="4" class="text-gray-500 italic">No significant
                                                            changes detected (or fields are hidden).</td>
                                                    </tr>
                                                @endif
                                            </table>
                                        @else
                                            {{-- กรณี Created / Deleted: แสดง JSON เต็ม --}}
                                            <pre class="text-gray-300">{{ json_encode($activity->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        @endif

                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div
                        class="flex flex-col items-center justify-center py-16 border-2 border-dashed border-gray-700 rounded-xl bg-gray-800/30">
                        <div class="text-gray-600 text-5xl mb-4"><i class="far fa-clipboard"></i></div>
                        <p class="text-gray-400 text-lg font-medium">ยังไม่มีประวัติการใช้งาน</p>
                        <p class="text-gray-600 text-sm">การเปลี่ยนแปลงข้อมูลจะถูกบันทึกที่นี่</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
@endsection
