@extends('layouts.admin')

@section('title', 'สถิติผู้เข้าชมเว็บไซต์')

@section('content')
    <div class="container mx-auto px-4 sm:px-8 py-8">
        
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-100 flex items-center">
                    <div class="p-2 bg-indigo-500/20 rounded-lg mr-3">
                        <i class="fas fa-chart-line text-indigo-400"></i>
                    </div>
                    สถิติผู้เข้าชมเว็บไซต์ (Visitor Insights)
                </h2>
                <p class="text-gray-400 text-sm mt-2 ml-12">ติดตามข้อมูลการเข้าชมเว็บไซต์แบบ Real-time และวิเคราะห์พฤติกรรมผู้ใช้</p>
            </div>
            <div>
                <form action="{{ route('admin.visitors.clearAll') }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะล้างข้อมูลการเข้าชมทั้งหมด? การกระทำนี้ไม่สามารถย้อนกลับได้');">
                    @csrf @method('DELETE')
                    <button type="submit" class="group flex items-center px-4 py-2.5 bg-red-500/10 text-red-400 border border-red-500/20 rounded-xl hover:bg-red-500 hover:text-white transition-all duration-300 text-sm font-medium shadow-sm hover:shadow-red-500/20">
                        <i class="fas fa-trash-alt mr-2 group-hover:scale-110 transition-transform"></i> ล้างข้อมูลทั้งหมด
                    </button>
                </form>
            </div>
        </div>

        {{-- Stats Summary --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Stat Card 1 --}}
            <div class="relative overflow-hidden bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-lg group hover:border-emerald-500/50 transition-colors">
                <div class="absolute -right-6 -top-6 opacity-5 group-hover:opacity-10 transition-opacity">
                    <i class="fas fa-users text-9xl text-emerald-500"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-gray-400 text-xs uppercase font-bold tracking-widest mb-2 flex items-center">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span> เข้าชมวันนี้ (Unique IP)
                    </p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-4xl font-black text-emerald-400">{{ number_format($stats['today_unique']) }}</h3>
                        <span class="text-sm font-medium text-gray-500">ครั้ง</span>
                    </div>
                </div>
            </div>

            {{-- Stat Card 2 --}}
            <div class="relative overflow-hidden bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-lg group hover:border-blue-500/50 transition-colors">
                <div class="absolute -right-6 -top-6 opacity-5 group-hover:opacity-10 transition-opacity">
                    <i class="fas fa-user-check text-9xl text-blue-500"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-gray-400 text-xs uppercase font-bold tracking-widest mb-2 flex items-center">
                        <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span> สมาชิกทั้งหมด (Logged-in)
                    </p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-4xl font-black text-blue-400">{{ number_format($stats['logged_in']) }}</h3>
                        <span class="text-sm font-medium text-gray-500">ราย</span>
                    </div>
                </div>
            </div>

            {{-- Stat Card 3 --}}
            <div class="relative overflow-hidden bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-lg group hover:border-purple-500/50 transition-colors">
                <div class="absolute -right-6 -top-6 opacity-5 group-hover:opacity-10 transition-opacity">
                    <i class="fas fa-globe text-9xl text-purple-500"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-gray-400 text-xs uppercase font-bold tracking-widest mb-2 flex items-center">
                        <span class="w-2 h-2 rounded-full bg-purple-500 mr-2"></span> เข้าชมสะสมทั้งหมด
                    </p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-4xl font-black text-purple-400">{{ number_format($stats['total_unique']) }}</h3>
                        <span class="text-sm font-medium text-gray-500">ครั้ง</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters Form --}}
        <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 mb-6 shadow-md">
            <form action="{{ route('admin.visitors.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-4">
                    <label class="text-[11px] text-gray-400 font-bold uppercase mb-2 block">ค้นหา IP / Path</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-500 text-sm"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="เช่น 127.0.0.1 หรือ /cart" 
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-900/50 border border-gray-600 text-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow placeholder-gray-600">
                    </div>
                </div>
                
                <div class="md:col-span-3">
                    <label class="text-[11px] text-gray-400 font-bold uppercase mb-2 block">เลือกวันที่</label>
                    <input type="date" name="date" value="{{ request('date') }}" 
                        class="w-full px-4 py-2.5 bg-gray-900/50 border border-gray-600 text-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow [&::-webkit-calendar-picker-indicator]:filter [&::-webkit-calendar-picker-indicator]:invert">
                </div>
                
                <div class="md:col-span-3">
                    <label class="text-[11px] text-gray-400 font-bold uppercase mb-2 block">ประเภทผู้เข้าชม</label>
                    <select name="type" class="w-full px-4 py-2.5 bg-gray-900/50 border border-gray-600 text-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow appearance-none">
                        <option value="">ทั้งหมด (All)</option>
                        <option value="user" {{ request('type') == 'user' ? 'selected' : '' }}>สมาชิก (Logged In)</option>
                        <option value="guest" {{ request('type') == 'guest' ? 'selected' : '' }}>บุคคลทั่วไป (Guest)</option>
                    </select>
                </div>
                
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white font-medium py-2.5 px-4 rounded-xl transition-all shadow-lg shadow-indigo-500/20 text-sm flex items-center justify-center">
                        <i class="fas fa-filter mr-2"></i> กรอง
                    </button>
                    @if(request()->anyFilled(['search', 'date', 'type']))
                        <a href="{{ route('admin.visitors.index') }}" title="ล้างตัวกรอง" class="flex-none bg-gray-700 hover:bg-gray-600 text-gray-300 font-medium py-2.5 px-4 rounded-xl transition-all text-sm flex items-center justify-center">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Data Table --}}
        <div class="bg-gray-800 shadow-xl rounded-2xl border border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal text-gray-300 whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-900/80 border-b border-gray-700 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
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
                            <tr class="hover:bg-gray-750/50 transition-colors group">
                                <td class="px-6 py-4">
                                    @if($visitor->user_id)
                                        <div class="inline-flex items-center px-2.5 py-1 rounded-md bg-emerald-500/10 text-emerald-400 text-xs font-semibold border border-emerald-500/20">
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-2 animate-pulse"></div>
                                            LOGGED IN
                                        </div>
                                    @else
                                        <div class="inline-flex items-center px-2.5 py-1 rounded-md bg-gray-700/50 text-gray-400 text-xs font-semibold border border-gray-600">
                                            <div class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-2"></div>
                                            GUEST
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-gray-100 font-semibold">{{ $visitor->user->name ?? 'Guest Visitor' }}</span>
                                        <span class="text-xs text-indigo-300 font-mono mt-0.5"><i class="fas fa-network-wired text-[10px] mr-1 opacity-50"></i>{{ $visitor->ip_address }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="inline-flex items-center px-3 py-1 bg-gray-900 text-gray-300 rounded-lg text-xs border border-gray-700 font-mono">
                                        <i class="fas fa-link text-[10px] mr-2 text-gray-500"></i>
                                        /{{ $visitor->path ?: 'หน้าหลัก' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-400 truncate max-w-[200px] xl:max-w-[300px] cursor-help" title="{{ $visitor->user_agent }}">
                                        {{ $visitor->user_agent }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex flex-col items-end">
                                        <span class="text-gray-200 text-sm font-medium">{{ $visitor->created_at->diffForHumans() }}</span>
                                        <span class="text-xs text-gray-500 mt-0.5">{{ $visitor->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('admin.visitors.destroy', $visitor->id) }}" method="POST" onsubmit="return confirm('ยืนยันการลบบันทึกการเข้าชมนี้?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center mx-auto text-gray-500 hover:text-red-400 hover:bg-red-400/10 transition-colors focus:outline-none">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <div class="w-20 h-20 bg-gray-900/50 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-search text-3xl opacity-50"></i>
                                        </div>
                                        <p class="text-lg font-medium text-gray-400">ไม่พบข้อมูลผู้เข้าชม</p>
                                        <p class="text-sm mt-1">ลองปรับเปลี่ยนเงื่อนไขการค้นหา หรือเลือกวันที่อื่นดูนะครับ</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            @if($visitors->hasPages())
                <div class="px-6 py-4 bg-gray-900/50 border-t border-gray-700">
                    {{ $visitors->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Custom Style for hover if your tailwind config doesn't have bg-gray-750 --}}
    <style>
        .hover\:bg-gray-750:hover {
            background-color: rgba(55, 65, 81, 0.4);
        }
    </style>
@endsection