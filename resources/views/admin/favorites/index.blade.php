@extends('layouts.admin')

@section('title', 'จัดการ "เกี่ยวกับติดใจ"')

@section('page-title')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">เกี่ยวกับติดใจ (About Us)</h1>
            <p class="text-sm text-gray-400 mt-1">ระบบจำลองหน้าเว็บ (Visual Editor) จัดการเนื้อหาแบบ Real-time</p>
        </div>
        <a href="{{ route('admin.favorites.create') }}"
            class="group flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2.5 rounded-lg shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 font-medium">
            <i class="fas fa-plus transition-transform group-hover:rotate-90"></i>
            เพิ่มเนื้อหาใหม่
        </a>
    </div>
@endsection

@section('content')

    @php
        $aboutTitle = \App\Models\SiteSetting::get('about_title') ?? 'เกี่ยวกับติดใจ';
        $aboutSub = \App\Models\SiteSetting::get('about_subtitle') ?? 'ความตั้งใจของเรา...เพื่อส่งมอบความสุขให้คุณ';
        // ข้อมูลอื่นๆ...
        $siteLogoPath = \App\Models\SiteSetting::get('site_logo');
        $siteLogoUrl = $siteLogoPath ? asset('storage/' . $siteLogoPath) : asset('images/logo/logo1.png');
    @endphp

    {{-- Browser Simulation Container --}}
    <div class="bg-gray-900 rounded-xl shadow-2xl overflow-hidden border border-gray-700 flex flex-col h-[850px]">

        {{-- Browser Toolbar (Mockup) --}}
        <div class="bg-gray-800 px-4 py-3 flex items-center gap-4 border-b border-gray-700 shrink-0">
            {{-- Window Controls --}}
            <div class="flex gap-2">
                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
            </div>

            {{-- Fake Address Bar --}}
            <div class="flex-1 bg-gray-900 rounded-md px-4 py-1.5 flex items-center text-sm text-gray-500">
                <i class="fas fa-lock text-emerald-500 mr-2 text-xs"></i>
                <span class="truncate">tidjai.com/about-us</span>
            </div>

            <div class="text-gray-500 text-xs hidden sm:block">
                <i class="fas fa-eye mr-1"></i> Preview Mode
            </div>
        </div>

        {{-- Scrollable Content Area --}}
        <div class="flex-1 overflow-y-auto bg-gray-100 relative font-sans scrollbar-hide">

            {{-- 1. HERO SECTION --}}
            <div class="relative bg-gradient-to-br from-red-700 to-red-600 text-white pt-20 pb-24 overflow-hidden group">
                {{-- Edit Trigger --}}
                <div
                    class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-all duration-300 z-50 transform group-hover:translate-y-0 -translate-y-2">
                    <button onclick="openSettingsModal('hero')"
                        class="bg-white/10 backdrop-blur-md hover:bg-white/20 border border-white/30 text-white px-4 py-2 rounded-full shadow-lg text-sm font-medium transition-colors">
                        <i class="fas fa-pen mr-2"></i> แก้ไขส่วนหัว
                    </button>
                </div>

                {{-- Border Dash Indicator --}}
                <div
                    class="absolute inset-2 border-2 border-transparent group-hover:border-white/30 border-dashed rounded-lg pointer-events-none transition-colors">
                </div>

                <div class="container mx-auto px-4 text-center relative z-10">
                    <div class="mb-6 flex justify-center">
                        <div
                            class="p-4 bg-white rounded-full shadow-xl w-28 h-28 flex items-center justify-center transform transition-transform group-hover:scale-110">
                            <img src="{{ $siteLogoUrl }}" class="max-w-full max-h-full object-contain" />
                        </div>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-extrabold mb-4 tracking-tight drop-shadow-md">{{ $aboutTitle }}
                    </h1>
                    <p class="text-white/90 text-lg font-light max-w-2xl mx-auto leading-relaxed">{{ $aboutSub }}</p>
                </div>
            </div>

            {{-- 2. MAIN CONTENT --}}
            <div class="container mx-auto px-4 max-w-5xl -mt-16 relative z-20 pb-20 space-y-12">
                @forelse($favorites as $index => $fav)
                    <div
                        class="bg-white rounded-2xl shadow-lg overflow-hidden relative group transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">

                        {{-- Hover Edit Controls --}}
                        <div
                            class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-all duration-300 z-50 flex gap-2 transform group-hover:translate-y-0 -translate-y-2">
                            <a href="{{ route('admin.favorites.edit', $fav->id) }}"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white p-2.5 rounded-full shadow-lg transition-colors"
                                title="แก้ไข">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.favorites.destroy', $fav->id) }}" method="POST"
                                onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบรายการนี้?');">
                                @csrf @method('DELETE')
                                <button
                                    class="bg-red-500 hover:bg-red-600 text-white p-2.5 rounded-full shadow-lg transition-colors"
                                    title="ลบ">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>

                        {{-- Border Dash Indicator --}}
                        <div
                            class="absolute inset-0 border-2 border-transparent group-hover:border-emerald-500 border-dashed rounded-2xl pointer-events-none z-40 transition-colors">
                        </div>

                        {{-- Layout Content & Images --}}
                        <div class="flex flex-col md:flex-row h-full">
                            {{-- Text Column --}}
                            <div
                                class="w-full md:w-1/2 p-8 md:p-10 flex flex-col justify-center {{ $index % 2 != 0 ? 'md:order-2' : '' }}">
                                <div class="flex items-start gap-3 mb-4">
                                    <span
                                        class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100 text-red-600 font-bold text-sm shrink-0">
                                        {{ $loop->iteration }}
                                    </span>
                                    <h2 class="text-2xl md:text-3xl font-bold text-gray-800 leading-tight">
                                        {{ $fav->title }}</h2>
                                </div>
                                <div class="text-gray-600 text-base leading-relaxed space-y-4 font-light pl-11">
                                    {!! nl2br($fav->content) !!}
                                </div>
                            </div>

                            {{-- Image Column --}}
                            @php
                                $images = isset($fav->images) && count($fav->images) > 0 ? $fav->images : null;
                                $singleImage = $fav->image_path;
                            @endphp

                            <div
                                class="w-full md:w-1/2 min-h-[350px] bg-gray-50 relative {{ $index % 2 != 0 ? 'md:order-1' : '' }}">
                                @if ($images && count($images) > 1)
                                    <div class="absolute inset-0 grid grid-cols-2 gap-0.5">
                                        @foreach ($images->take(4) as $img)
                                            <div class="relative w-full h-full overflow-hidden group/img">
                                                <img src="{{ asset('storage/' . $img->image_path) }}"
                                                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover/img:scale-110">
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($images && count($images) == 1)
                                    <img src="{{ asset('storage/' . $images[0]->image_path) }}"
                                        class="absolute inset-0 w-full h-full object-cover">
                                @elseif($singleImage)
                                    <img src="{{ asset('storage/' . $singleImage) }}"
                                        class="absolute inset-0 w-full h-full object-cover">
                                @else
                                    <div class="flex flex-col items-center justify-center h-full text-gray-300 bg-gray-200">
                                        <i class="fas fa-image text-4xl mb-2"></i>
                                        <span class="text-sm">ไม่มีรูปภาพ</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="text-center py-20 bg-white/50 backdrop-blur rounded-3xl border-2 border-dashed border-gray-300 mx-auto max-w-2xl">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-layer-group text-6xl opacity-30"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-600">ยังไม่มีเนื้อหา</h3>
                        <p class="text-sm text-gray-500 mt-1">เริ่มต้นด้วยการเพิ่มเนื้อหาแรกของคุณ</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Simple Settings Modal for Header --}}
    <div id="settingsEditModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4 animate-fade-in">
        <div
            class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-700 overflow-hidden transform transition-all scale-100">
            <div class="bg-gray-900 px-6 py-4 flex justify-between items-center border-b border-gray-700">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fas fa-cog text-emerald-500"></i> ตั้งค่าส่วนหัว
                </h3>
                <button onclick="closeModal('settingsEditModal')" class="text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6 space-y-4" id="headerSettingsForm">
                @csrf
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-300">หัวข้อหลัก (Main Title)</label>
                    <input type="text" name="settings[about_title]" value="{{ \App\Models\SiteSetting::get('about_title', 'เกี่ยวกับติดใจ') }}"
                        class="w-full bg-gray-900/50 border border-gray-600 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-300">คำโปรย (Subtitle)</label>
                    <textarea name="settings[about_subtitle]" rows="3"
                        class="w-full bg-gray-900/50 border border-gray-600 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">{{ \App\Models\SiteSetting::get('about_subtitle', 'ความตั้งใจของเรา...เพื่อส่งมอบความสุขให้คุณ') }}</textarea>
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-gray-700/50 mt-4">
                    <button type="button" onclick="closeModal('settingsEditModal')"
                        class="px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-700 font-medium text-sm">ยกเลิก</button>
                    <button type="submit" id="saveHeaderBtn"
                        class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-medium text-sm shadow-lg flex items-center gap-2">
                        <span>บันทึกการเปลี่ยนแปลง</span>
                    </button>
                </div>
            </form>
            
            <script>
                document.getElementById('headerSettingsForm')?.addEventListener('submit', function() {
                    const btn = document.getElementById('saveHeaderBtn');
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังบันทึก...';
                    }
                });
            </script>
        </div>
    </div>

    <script>
        function openSettingsModal(type) {
            const modal = document.getElementById('settingsEditModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>

    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .animate-fade-in {
            animation: fade-in 0.2s ease-out;
        }
    </style>
@endsection
