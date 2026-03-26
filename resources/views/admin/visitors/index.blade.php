@extends('layouts.admin')

@section('title', 'สถิติผู้เข้าชมเว็บไซต์')

@section('content')
    <div class="container mx-auto px-4 sm:px-8 py-8">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-100 flex items-center">
                    <i class="fas fa-chart-line mr-3 text-indigo-500"></i>
                    สถิติผู้เข้าชมเว็บไซต์ (Visitor Insights)
                </h2>
                <p class="text-gray-400 text-sm mt-1">ติดตามข้อมูลการเข้าชมเว็บไซต์แบบ Real-time และวิเคราะห์พฤติกรรมผู้ใช้</p>
            </div>
            <div class="mt-4 md:mt-0">
                <form action="{{ route('admin.visitors.clearAll') }}" method="POST" onsubmit="return confirm('ยืนยันการล้างข้อมูลการเข้าชมทั้งหมด?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-900/30 text-red-400 border border-red-500/20 rounded-lg hover:bg-red-500 hover:text-white transition text-sm">
                        <i class="fas fa-trash-alt mr-2"></i> ล้างข้อมูลทั้งหมด
                    </button>
                </form>
            </div>
        </div>

        {{-- Stats Summary --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-lg">
                <p class="text-gray-400 text-xs uppercase font-bold tracking-widest mb-2">เข้าชมวันนี้ (Unique IP)</p>
                <h3 class="text-3xl font-black text-emerald-400">{{ number_format($stats['today_unique']) }} <span class="text-sm font-normal text-gray-500">ครั้ง</span></h3>
            </div>
            <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-lg">
                <p class="text-gray-400 text-xs uppercase font-bold tracking-widest mb-2">สมาชิกทั้งหมด (Logged-in)</p>
                <h3 class="text-3xl font-black text-blue-400">{{ number_format($stats['logged_in']) }} <span class="text-sm font-normal text-gray-500">ราย</span></h3>
            </div>
            <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-lg">
                <p class="text-gray-400 text-xs uppercase font-bold tracking-widest mb-2">เข้าชมสะสมทั้งหมด</p>
                <h3 class="text-3xl font-black text-purple-400">{{ number_format($stats['total_unique']) }} <span class="text-sm font-normal text-gray-500">ครั้ง</span></h3>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-gray-800 p-4 rounded-xl border border-gray-700 mb-6">
            <form action="{{ route('admin.visitors.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="text-[10px] text-gray-500 font-bold uppercase mb-1 block">ค้นหา IP / Path</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="เช่น 127.0.0.1 หรือ /cart" 
                        class="w-full bg-gray-900 border-gray-700 text-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 font-bold uppercase mb-1 block">เลือกวันที่</label>
                    <input type="date" name="date" value="{{ request('date') }}" 
                        class="w-full bg-gray-900 border-gray-700 text-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 font-bold uppercase mb-1 block">ประเภทผู้เข้าชม</label>
                    <select name="type" class="w-full bg-gray-900 border-gray-700 text-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">ทั้งหมด</option>
                        <option value="user" {{ request('type') == 'user' ? 'selected' : '' }}>สมาชิก (User)</option>
                        <option value="guest" {{ request('type') == 'guest' ? 'selected' : '' }}>บุคคลทั่วไป (Guest)</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition text-sm">
                        <i class="fas fa-filter mr-2"></i> กรองข้อมูล
                    </button>
                    @if(request()->anyFilled(['search', 'date', 'type']))
                        <a href="{{ route('admin.visitors.index') }}" class="ml-2 bg-gray-700 hover:bg-gray-600 text-gray-300 font-bold py-2 px-4 rounded-lg transition text-sm">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-gray-800 shadow-xl rounded-2xl overflow-hidden border border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal text-gray-300">
                    <thead>
                        <tr class="bg-gray-900/50 border-b border-gray-700 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            <th class="px-6 py-4">สถานะ</th>
                            <th class="px-6 py-4">ผู้เข้าชม / IP Address</th>
                            <th class="px-6 py-4">หน้าที่เข้าชม (Path)</th>
                            <th class="px-6 py-4">อุปกรณ์ (User Agent)</th>
                            <th class="px-6 py-4 text-right">วันเวลาที่เข้าชม</th>
                            <th class="px-6 py-4 text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        @forelse ($visitors as $visitor)
                            <tr class="hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    @if($visitor->user_id)
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-bold border border-emerald-500/20">
                                            <i class="fas fa-user-check mr-1"></i> LOGGED IN
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-gray-700 text-gray-400 text-[10px] font-bold border border-gray-600">
                                            <i class="fas fa-user-secret mr-1"></i> GUEST
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-gray-200 font-medium">{{ $visitor->user->name ?? 'Guest Visitor' }}</span>
                                        <span class="text-[10px] text-gray-500 font-mono">{{ $visitor->ip_address }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-gray-900/50 text-indigo-300 rounded text-xs border border-indigo-500/10">
                                        /{{ $visitor->path ?: 'หน้าหลัก' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs text-gray-500 truncate max-w-[250px] block" title="{{ $visitor->user_agent }}">
                                        {{ $visitor->user_agent }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex flex-col items-end">
                                        <span class="text-gray-300 font-medium">{{ $visitor->created_at->diffForHumans() }}</span>
                                        <span class="text-[10px] text-gray-500">{{ $visitor->created_at->format('d/m/Y H:i:s') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('admin.visitors.destroy', $visitor->id) }}" method="POST" onsubmit="return confirm('ลบบันทึกนี้?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-400 transition">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-users-slash text-4xl mb-3 opacity-20"></i>
                                    <p>ไม่พบข้อมูลผู้เข้าชมตามเงื่อนไขที่ระบุ</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($visitors->hasPages())
                <div class="px-6 py-4 bg-gray-900/30 border-t border-gray-700">
                    {{ $visitors->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
