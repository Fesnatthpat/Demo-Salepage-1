@extends('layouts.admin')

@section('title', 'Admin Activity Log')

@section('content')
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
            <div>
                <h2 class="text-2xl font-semibold leading-tight">Admin Activity Log</h2>
                <p class="text-sm text-gray-600">A log of all changes made to products and promotions by admins.</p>
            </div>

            @if($filter_admin_name)
                <div class="mt-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                    <p class="font-bold">Filtering by: {{ $filter_admin_name }}</p>
                    <a href="{{ route('admin.activity-log.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Clear Filter</a>
                </div>
            @endif

            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto mt-6">
                <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Admin
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Action
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Target
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Changes
                                </th>
                                 <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    IP Address
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Time
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activities as $activity)
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        @if($activity->admin)
                                        <a href="{{ route('admin.activity-log.index', ['admin_id' => $activity->admin->id]) }}" class="text-blue-600 hover:underline whitespace-no-wrap">
                                            {{ $activity->admin->name }}
                                        </a>
                                        @else
                                            <p class="text-gray-500 whitespace-no-wrap">N/A</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                             @if($activity->action === 'created')
                                                <span aria-hidden class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                                <span class="relative">Created</span>
                                            @elseif($activity->action === 'updated')
                                                <span aria-hidden class="absolute inset-0 bg-yellow-200 opacity-50 rounded-full"></span>
                                                <span class="relative">Updated</span>
                                            @elseif($activity->action === 'deleted')
                                                <span aria-hidden class="absolute inset-0 bg-red-200 opacity-50 rounded-full"></span>
                                                <span class="relative">Deleted</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">
                                            {{ Str::afterLast($activity->loggable_type, '\\') }}:
                                            {{ $activity->loggable->pd_sp_name ?? $activity->loggable->name ?? $activity->loggable_id }}
                                        </p>
                                        @if ($activity->loggable)
                                            @if ($activity->loggable_type === 'App\Models\ProductSalepage')
                                                <a href="{{ route('admin.products.edit', $activity->loggable_id) }}" class="text-xs text-blue-600 hover:underline">
                                                    View Details &rarr;
                                                </a>
                                            @elseif ($activity->loggable_type === 'App\Models\Promotion')
                                                <a href="{{ route('admin.promotions.edit', $activity->loggable_id) }}" class="text-xs text-blue-600 hover:underline">
                                                    View Details &rarr;
                                                </a>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-400">(Item has been deleted)</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                       @if($activity->changes)
                                            <div class="whitespace-pre-wrap text-xs bg-gray-50 p-2 rounded">
                                                @if(isset($activity->changes['original']) && isset($activity->changes['new']))
                                                    <p class="font-semibold mb-1">Changes:</p>
                                                    @foreach($activity->changes['new'] as $attribute => $newValue)
                                                        @if(isset($activity->changes['original'][$attribute]))
                                                            @if($activity->changes['original'][$attribute] != $newValue)
                                                                <p><strong>{{ $attribute }}:</strong> <span class="text-red-600">{{ $activity->changes['original'][$attribute] }}</span> &rarr; <span class="text-green-600">{{ $newValue }}</span></p>
                                                            @endif
                                                        @else
                                                            <p><strong>{{ $attribute }}:</strong> <span class="text-green-600">Added: {{ $newValue }}</span></p>
                                                        @endif
                                                    @endforeach
                                                @elseif(isset($activity->changes['new']))
                                                    <p class="font-semibold mb-1">New Data:</p>
                                                    <pre><code>{{ json_encode($activity->changes['new'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                @elseif(isset($activity->changes['original']))
                                                    <p class="font-semibold mb-1">Original Data (before deletion):</p>
                                                    <pre><code>{{ json_encode($activity->changes['original'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                @else
                                                    <pre><code>{{ json_encode($activity->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                @endif
                                            </div>
                                       @else
                                            <span class="text-gray-500">N/A</span>
                                       @endif
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">{{ $activity->ip_address }}</p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">{{ $activity->created_at->diffForHumans() }}</p>
                                        <p class="text-gray-600 whitespace-no-wrap text-xs">{{ $activity->created_at->format('Y-m-d H:i:s') }}</p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        No activities logged yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
                        {{ $activities->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
