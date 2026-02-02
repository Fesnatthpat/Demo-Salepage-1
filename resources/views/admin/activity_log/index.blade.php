@extends('layouts.admin')

@section('title', 'Admin Activity Log')

@section('content')
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
            <div>
                <h2 class="text-2xl font-semibold leading-tight text-gray-100">Admin Activity Log</h2>
                <p class="text-sm text-gray-400">บันทึกการเปลี่ยนแปลงข้อมูลสินค้าและโปรโมชั่นโดยแอดมิน</p>
            </div>

            @if ($filter_admin_name)
                <div class="mt-4 bg-blue-900/30 border-l-4 border-blue-500 text-blue-200 p-4" role="alert">
                    <p class="font-bold">Filtering by: {{ $filter_admin_name }}</p>
                    <a href="{{ route('admin.activity-log.index') }}" class="text-sm text-blue-400 hover:text-blue-300">Clear
                        Filter</a>
                </div>
            @endif

            <div class="mt-6 space-y-6">
                @forelse ($activities as $activity)
                    @php
                        $action_color = '';
                        $action_icon = '';
                        switch ($activity->action) {
                            case 'created':
                                $action_color = 'green';
                                $action_icon = 'fas fa-plus-circle';
                                break;
                            case 'updated':
                                $action_color = 'yellow';
                                $action_icon = 'fas fa-pencil-alt';
                                break;
                            case 'deleted':
                                $action_color = 'red';
                                $action_icon = 'fas fa-trash-alt';
                                break;
                        }
                    @endphp
                    <div
                        class="bg-gray-800 rounded-xl border border-gray-700 shadow-md overflow-hidden hover:border-{{ $action_color }}-500/50 transition-all">
                        <div
                            class="p-4 border-b border-gray-700 bg-gray-900/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <span class="text-{{ $action_color }}-400 text-xl"><i class="{{ $action_icon }}"></i></span>
                                <div>
                                    <p class="font-bold text-gray-100">
                                        {{ ucfirst($activity->action) }}
                                        <span class="font-normal text-gray-400">a
                                            {{ Str::afterLast($activity->loggable_type, '\\') }}</span>
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        by
                                        @if ($activity->admin)
                                            <a href="{{ route('admin.activity-log.index', ['admin_id' => $activity->admin->id]) }}"
                                                class="font-semibold text-blue-400 hover:text-blue-300">{{ $activity->admin->name }}</a>
                                        @else
                                            <span class="text-gray-500">N/A</span>
                                        @endif
                                        <span class="mx-1">&bull;</span>
                                        <span title="{{ $activity->created_at->format('Y-m-d H:i:s') }}">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 font-mono self-end sm:self-center">
                                IP: {{ $activity->ip_address }}
                            </div>
                        </div>

                        <div class="p-5 space-y-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Target</p>
                                <div class="flex items-center gap-3">
                                    <p class="text-gray-200">
                                        {{ $activity->loggable->pd_sp_name ?? ($activity->loggable->name ?? $activity->loggable_id) }}
                                    </p>
                                    @if ($activity->loggable)
                                        @if ($activity->loggable_type === 'App\Models\ProductSalepage')
                                            <a href="{{ route('admin.products.edit', $activity->loggable_id) }}"
                                                class="text-xs text-blue-400 hover:underline">
                                                View &rarr;
                                            </a>
                                        @elseif ($activity->loggable_type === 'App\Models\Promotion')
                                            <a href="{{ route('admin.promotions.edit', $activity->loggable_id) }}"
                                                class="text-xs text-blue-400 hover:underline">
                                                View &rarr;
                                            </a>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-500">(Item since deleted)</span>
                                    @endif
                                </div>
                            </div>

                            @if ($activity->changes)
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Changes</p>
                                    <div
                                        class="text-xs bg-gray-900 p-4 rounded-lg border border-gray-700 font-mono text-gray-300 space-y-2">
                                        @if (isset($activity->changes['original']) && isset($activity->changes['new']))
                                            @foreach ($activity->changes['new'] as $attribute => $newValue)
                                                @if (
                                                    isset($activity->changes['original'][$attribute]) &&
                                                        $activity->changes['original'][$attribute] != $newValue)
                                                    <div>
                                                        <span
                                                            class="text-gray-500 select-none">{{ $attribute }}:</span>
                                                        <div class="flex flex-col sm:flex-row sm:gap-2">
                                                            <span
                                                                class="text-red-400/80 line-through truncate">{{ Str::limit($activity->changes['original'][$attribute], 100) }}</span>
                                                            <span class="text-gray-500 select-none sm:block hidden">&rarr;</span>
                                                            <span
                                                                class="text-green-400 truncate">{{ Str::limit($newValue, 100) }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <pre class="whitespace-pre-wrap text-xs">{{ json_encode($activity->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 border-2 border-dashed border-gray-700 rounded-xl bg-gray-800/50">
                        <p class="text-gray-500">No activities logged yet.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
@endsection
