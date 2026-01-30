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

            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto mt-6">
                <div class="inline-block min-w-full shadow-lg rounded-lg overflow-hidden border border-gray-700">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th
                                    class="px-5 py-3 border-b border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                                    Admin
                                </th>
                                <th
                                    class="px-5 py-3 border-b border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                                    Action
                                </th>
                                <th
                                    class="px-5 py-3 border-b border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                                    Target
                                </th>
                                <th
                                    class="px-5 py-3 border-b border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                                    Changes
                                </th>
                                <th
                                    class="px-5 py-3 border-b border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                                    IP Address
                                </th>
                                <th
                                    class="px-5 py-3 border-b border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                                    Time
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800">
                            @forelse ($activities as $activity)
                                <tr class="hover:bg-gray-700/50 transition-colors">
                                    <td class="px-5 py-5 border-b border-gray-700 text-sm">
                                        @if ($activity->admin)
                                            <a href="{{ route('admin.activity-log.index', ['admin_id' => $activity->admin->id]) }}"
                                                class="text-blue-400 hover:text-blue-300 whitespace-no-wrap font-medium">
                                                {{ $activity->admin->name }}
                                            </a>
                                        @else
                                            <p class="text-gray-500 whitespace-no-wrap">N/A</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-700 text-sm">
                                        <span
                                            class="relative inline-block px-3 py-1 font-semibold leading-tight text-xs rounded-full 
                                            @if ($activity->action === 'created') text-green-300 bg-green-900/50
                                            @elseif($activity->action === 'updated') text-yellow-300 bg-yellow-900/50
                                            @elseif($activity->action === 'deleted') text-red-300 bg-red-900/50 @endif">
                                            {{ ucfirst($activity->action) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-700 text-sm">
                                        <p class="text-gray-300 whitespace-no-wrap">
                                            <span
                                                class="text-gray-500">{{ Str::afterLast($activity->loggable_type, '\\') }}:</span>
                                            {{ $activity->loggable->pd_sp_name ?? ($activity->loggable->name ?? $activity->loggable_id) }}
                                        </p>
                                        @if ($activity->loggable)
                                            @if ($activity->loggable_type === 'App\Models\ProductSalepage')
                                                <a href="{{ route('admin.products.edit', $activity->loggable_id) }}"
                                                    class="text-xs text-blue-400 hover:underline mt-1 inline-block">
                                                    View Details &rarr;
                                                </a>
                                            @elseif ($activity->loggable_type === 'App\Models\Promotion')
                                                <a href="{{ route('admin.promotions.edit', $activity->loggable_id) }}"
                                                    class="text-xs text-blue-400 hover:underline mt-1 inline-block">
                                                    View Details &rarr;
                                                </a>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-500">(Item deleted)</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-700 text-sm">
                                        @if ($activity->changes)
                                            <div
                                                class="whitespace-pre-wrap text-xs bg-gray-900 p-3 rounded border border-gray-600 font-mono text-gray-300">
                                                @if (isset($activity->changes['original']) && isset($activity->changes['new']))
                                                    <p class="font-bold mb-2 text-gray-400">Changes:</p>
                                                    @foreach ($activity->changes['new'] as $attribute => $newValue)
                                                        @if (isset($activity->changes['original'][$attribute]))
                                                            @if ($activity->changes['original'][$attribute] != $newValue)
                                                                <div class="mb-1">
                                                                    <span class="text-gray-400">{{ $attribute }}:</span>
                                                                    <span
                                                                        class="text-red-400 line-through mx-1">{{ $activity->changes['original'][$attribute] }}</span>
                                                                    &rarr;
                                                                    <span class="text-green-400">{{ $newValue }}</span>
                                                                </div>
                                                            @endif
                                                        @else
                                                            <div class="mb-1">
                                                                <span class="text-gray-400">{{ $attribute }}:</span>
                                                                <span class="text-green-400">Added:
                                                                    {{ $newValue }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @elseif(isset($activity->changes['new']))
                                                    <p class="font-bold mb-1 text-gray-400">New Data:</p>
                                                    <div class="overflow-x-auto">
                                                        {{ json_encode($activity->changes['new'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                                                    </div>
                                                @else
                                                    <div class="overflow-x-auto">
                                                        {{ json_encode($activity->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-600">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-700 text-sm">
                                        <p class="text-gray-400 whitespace-no-wrap">{{ $activity->ip_address }}</p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-700 text-sm">
                                        <p class="text-gray-300 whitespace-no-wrap">
                                            {{ $activity->created_at->diffForHumans() }}</p>
                                        <p class="text-gray-500 whitespace-no-wrap text-xs">
                                            {{ $activity->created_at->format('Y-m-d H:i:s') }}</p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center px-5 py-8 border-b border-gray-700 text-gray-500">
                                        No activities logged yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div
                        class="px-5 py-5 bg-gray-800 border-t border-gray-700 flex flex-col xs:flex-row items-center xs:justify-between">
                        {{ $activities->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
