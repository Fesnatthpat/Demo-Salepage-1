@extends('layouts.admin')

@section('title', 'จัดการ "เกี่ยวกับติดใจ"')

@section('page-title')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">เกี่ยวกับติดใจ (About Us)</h1>
            <p class="text-sm text-gray-400 mt-1">จัดการเนื้อหาทั้งหมดในหน้าเกี่ยวกับเราในที่เดียว</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.favorites.create') }}"
                class="group flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2.5 rounded-lg shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 font-medium">
                <i class="fas fa-plus transition-transform group-hover:rotate-90"></i>
                เพิ่มเนื้อหาหลัก
            </a>
        </div>
    </div>
@endsection

@section('content')

    @php
        $aboutTitle = $settings['about_title'] ?? 'เกี่ยวกับติดใจ';
        $aboutSub = $settings['about_subtitle'] ?? 'ความตั้งใจของเรา...เพื่อส่งมอบความสุขให้คุณ';
        $siteLogoPath = $settings['site_logo'] ?? null;
        $siteLogoUrl = $siteLogoPath ? asset('storage/' . $siteLogoPath) : asset('images/logo/logo1.png');
        
        $lifeTitle = $settings['life_title'] ?? '🧋 Life with ติดใจ 💖';
        $lifeSub = $settings['life_subtitle'] ?? 'รับชมภาพกิจกรรมต่างๆ ของพนักงานติดใจ ที่ไม่เหมือนใครแน่นอน🤩';
        
        $teamTitle = $settings['team_title'] ?? '💗 Team People';
        $teamSub = $settings['team_subtitle'] ?? 'หากต้องการสอบถามรายละเอียดเพิ่มเติมเกี่ยวกับเรา';
        $teamPhone = $settings['team_phone'] ?? '09X-XXX-XXXX / 09X-XXX-XXXX (Team People)';
        $teamEmail = $settings['team_email'] ?? 'hr@tidjai.com';
        
        $socialTitle = $settings['social_title'] ?? '🫶 สามารถรับชมเพิ่มเติมได้ตามช่องทางด้านล่างนี้ 🫶';
    @endphp

    {{-- Browser Simulation Container --}}
    <div class="bg-gray-900 rounded-xl shadow-2xl overflow-hidden border border-gray-700 flex flex-col h-[850px]">

        {{-- Browser Toolbar (Mockup) --}}
        <div class="bg-gray-800 px-4 py-3 flex items-center gap-4 border-b border-gray-700 shrink-0">
            <div class="flex gap-2">
                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
            </div>

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
                <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-all duration-300 z-50 transform group-hover:translate-y-0 -translate-y-2">
                    <button onclick="openModal('settingsHeroModal')"
                        class="bg-white/10 backdrop-blur-md hover:bg-white/20 border border-white/30 text-white px-4 py-2 rounded-full shadow-lg text-sm font-medium transition-colors">
                        <i class="fas fa-pen mr-2"></i> แก้ไขส่วนหัว
                    </button>
                </div>
                <div class="absolute inset-2 border-2 border-transparent group-hover:border-white/30 border-dashed rounded-lg pointer-events-none transition-colors"></div>
                <div class="container mx-auto px-4 text-center relative z-10">
                    <div class="mb-6 flex justify-center">
                        <div class="p-4 bg-white rounded-full shadow-xl w-28 h-28 flex items-center justify-center transform transition-transform group-hover:scale-110">
                            <img src="{{ $siteLogoUrl }}" class="max-w-full max-h-full object-contain" />
                        </div>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-extrabold mb-4 tracking-tight drop-shadow-md">{{ $aboutTitle }}</h1>
                    <p class="text-white/90 text-lg font-light max-w-2xl mx-auto leading-relaxed">{{ $aboutSub }}</p>
                </div>
            </div>

            {{-- 2. MAIN CONTENT (Favorites) --}}
            <div class="container mx-auto px-4 max-w-5xl -mt-16 relative z-20 pb-10 space-y-12">
                @forelse($favorites as $index => $fav)
                    <div id="fav-item-{{ $fav->id }}" class="bg-white rounded-2xl shadow-lg overflow-hidden relative group transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                        <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-all duration-300 z-50 flex gap-2 transform group-hover:translate-y-0 -translate-y-2">
                            <a href="{{ route('admin.favorites.edit', $fav->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white p-2.5 rounded-full shadow-lg transition-colors" title="แก้ไข"><i class="fas fa-edit"></i></a>
                            <button type="button" onclick="confirmDelete('{{ route('admin.favorites.destroy', $fav->id) }}', document.getElementById('fav-item-{{ $fav->id }}'))" 
                                class="bg-red-500 hover:bg-red-600 text-white p-2.5 rounded-full shadow-lg transition-colors" title="ลบ">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        <div class="absolute inset-0 border-2 border-transparent group-hover:border-emerald-500 border-dashed rounded-2xl pointer-events-none z-40 transition-colors"></div>
                        <div class="flex flex-col md:flex-row h-full">
                            <div class="w-full md:w-1/2 p-8 md:p-10 flex flex-col justify-center {{ $index % 2 != 0 ? 'md:order-2' : '' }}">
                                <div class="flex items-start gap-3 mb-4">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100 text-red-600 font-bold text-sm shrink-0">{{ $loop->iteration }}</span>
                                    <h2 class="text-2xl md:text-3xl font-bold text-gray-800 leading-tight">{{ $fav->title }}</h2>
                                </div>
                                <div class="text-gray-600 text-base leading-relaxed space-y-4 font-light pl-11">{!! nl2br($fav->content) !!}</div>
                            </div>
                            <div class="w-full md:w-1/2 min-h-[350px] bg-gray-900 relative overflow-hidden {{ $index % 2 != 0 ? 'md:order-1' : '' }}">
                                @php
                                    $images = isset($fav->images) && count($fav->images) > 0 ? $fav->images : null;
                                    $imagePath = $fav->image_path;
                                    $videoPath = $fav->video_path ?? $fav->video ?? null;
                                @endphp
                                @if ($videoPath)
                                    <div class="absolute inset-0 flex items-center justify-center bg-black">
                                        <video controls class="w-full h-full object-cover"><source src="{{ asset('storage/' . $videoPath) }}" type="video/mp4"></video>
                                    </div>
                                @elseif ($images)
                                    <img src="{{ asset('storage/' . $images[0]->image_path) }}" class="absolute inset-0 w-full h-full object-cover">
                                @elseif ($imagePath)
                                    <img src="{{ asset('storage/' . $imagePath) }}" class="absolute inset-0 w-full h-full object-cover">
                                @else
                                    <div class="flex flex-col items-center justify-center h-full text-gray-400 bg-gray-100"><i class="fas fa-image text-4xl mb-2"></i><span class="text-sm">ไม่มีรูปภาพหรือวิดีโอ</span></div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 bg-white/50 backdrop-blur rounded-3xl border-2 border-dashed border-gray-300 mx-auto max-w-2xl">
                        <i class="fas fa-layer-group text-6xl opacity-30 text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-600">ยังไม่มีเนื้อหาหลัก</h3>
                    </div>
                @endforelse
            </div>

            {{-- 3. LIFE WITH ติดใจ --}}
            <div class="container mx-auto px-4 max-w-5xl py-10 group relative">
                <div class="absolute top-0 right-4 opacity-0 group-hover:opacity-100 transition-all duration-300 z-50 flex gap-2">
                    <button onclick="openModal('settingsLifeModal')" class="bg-white/80 backdrop-blur-sm hover:bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-full shadow-lg text-sm font-medium transition-colors"><i class="fas fa-pen mr-2"></i> แก้ไขข้อความส่วนนี้</button>
                    <button onclick="openAddVideoModal()" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-full shadow-lg text-sm font-medium transition-colors"><i class="fas fa-plus mr-2"></i> เพิ่มวิดีโอ</button>
                </div>
                <div class="mb-8 border-l-4 border-red-500 pl-4">
                    <h2 class="text-2xl font-bold text-gray-800 tracking-wide">{{ $lifeTitle }}</h2>
                    <p class="text-[15px] text-gray-500 mt-2 font-light">{{ $lifeSub }}</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @forelse($videos as $video)
                        <div id="video-item-{{ $video->id }}" class="relative rounded-2xl overflow-hidden aspect-[9/16] bg-gray-900 group/item cursor-pointer shadow-lg border-2 border-transparent hover:border-emerald-500 transition-colors"
                            onclick="playVideoPreview({{ $video->toJson() }})">
                            @php
                                $thumb = $video->thumbnail_path ? asset('storage/' . $video->thumbnail_path) : ($video->thumbnail_url ?? '');
                            @endphp
                            @if($thumb)
                                <img src="{{ $thumb }}" class="absolute inset-0 w-full h-full object-cover opacity-80 group-hover/item:scale-105 transition-transform duration-500">
                            @else
                                <div class="absolute inset-0 flex items-center justify-center text-gray-600 bg-gray-800"><i class="fas fa-video text-5xl"></i></div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/30"></div>
                            <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover/item:opacity-100 transition-opacity z-50">
                                <button onclick="event.stopPropagation(); editVideo({{ $video }})" class="bg-yellow-500 text-white p-2 rounded-full text-xs hover:bg-yellow-600"><i class="fas fa-edit"></i></button>
                                <button type="button" onclick="event.stopPropagation(); confirmDelete('{{ route('admin.about-videos.destroy', $video->id) }}', document.getElementById('video-item-{{ $video->id }}'))" 
                                    class="bg-red-500 text-white p-2 rounded-full text-xs hover:bg-red-600" title="ลบ">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="w-12 h-12 bg-white/30 backdrop-blur-sm rounded-full flex items-center justify-center text-white"><i class="fas fa-play ml-1"></i></div>
                            </div>
                            <div class="absolute bottom-4 left-4 right-4 text-white">
                                <h3 class="font-bold text-sm drop-shadow-md line-clamp-2">{{ $video->title }}</h3>
                            </div>
                            @if($video->duration)
                                <div class="absolute bottom-4 right-4 bg-black/60 text-white text-[10px] px-2 py-1 rounded">{{ $video->duration }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full py-10 text-center bg-white rounded-2xl border-2 border-dashed border-gray-300 text-gray-400">ยังไม่มีวิดีโอ</div>
                    @endforelse
                </div>
            </div>

            {{-- 4. GALLERY --}}
            <div class="container mx-auto px-4 max-w-5xl py-10 group relative">
                <div class="absolute top-0 right-4 opacity-0 group-hover:opacity-100 transition-all duration-300 z-50 flex gap-2">
                    <button onclick="openAddGalleryModal()" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-full shadow-lg text-sm font-medium transition-colors"><i class="fas fa-plus mr-2"></i> เพิ่มอัลบั้มภาพ</button>
                </div>
                <div class="mt-8 mb-6 border-b border-gray-200 pb-3 flex items-center gap-2 text-gray-700 font-bold">
                    <i class="fas fa-th-large"></i> <span>รวมภาพบรรยากาศ</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @forelse($galleries as $gallery)
                        <div id="gallery-item-{{ $gallery->id }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group/gal cursor-pointer relative hover:shadow-lg transition-all">
                            <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover/gal:opacity-100 transition-opacity z-10">
                                <button onclick="editGallery({{ $gallery->load('images') }})" class="bg-yellow-500 text-white p-2 rounded-full text-xs hover:bg-yellow-600"><i class="fas fa-edit"></i></button>
                                <button type="button" onclick="confirmDelete('{{ route('admin.about-galleries.destroy', $gallery->id) }}', document.getElementById('gallery-item-{{ $gallery->id }}'))" 
                                    class="bg-red-500 text-white p-2 rounded-full text-xs hover:bg-red-600" title="ลบ">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="relative aspect-[4/3] w-full overflow-hidden bg-gray-100">
                                @if($gallery->images->count() > 0)
                                    <img src="{{ asset('storage/' . $gallery->images[0]->image_path) }}" class="w-full h-full object-cover group-hover/gal:scale-105 transition-transform duration-500">
                                    <div class="absolute bottom-2 left-0 right-0 flex justify-center gap-1">
                                        @foreach($gallery->images->take(3) as $idx => $img)
                                            <div class="w-1.5 h-1.5 rounded-full {{ $idx == 0 ? 'bg-white' : 'bg-white/50' }}"></div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="flex items-center justify-center h-full text-gray-400"><i class="fas fa-images text-4xl"></i></div>
                                @endif
                            </div>
                            <div class="p-4"><h4 class="text-[15px] font-medium text-gray-800">{{ $gallery->title }}</h4></div>
                        </div>
                    @empty
                        <div class="col-span-full py-10 text-center bg-white rounded-2xl border-2 border-dashed border-gray-300 text-gray-400">ยังไม่มีอัลบั้มภาพ</div>
                    @endforelse
                </div>
            </div>

            {{-- 5. SOCIAL & TEAM (ปรับแก้ให้เหมือนหน้าบ้าน: แสดงแค่ไอคอนเพียวๆ) --}}
            <div class="container mx-auto px-4 max-w-5xl py-20 border-t border-gray-200 group relative">
                <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-all duration-300 z-50 flex gap-2">
                    <button onclick="openModal('settingsSocialTeamModal')" class="bg-white/80 backdrop-blur-sm hover:bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-full shadow-lg text-sm font-medium transition-colors"><i class="fas fa-pen mr-2"></i> แก้ไขข้อความส่วนทีม</button>
                    <button onclick="openAddSocialModal()" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-full shadow-lg text-sm font-medium transition-colors"><i class="fas fa-plus mr-2"></i> เพิ่มโซเชียล</button>
                </div>
                
                <div class="text-center mb-16">
                    <h2 class="text-xl font-bold text-gray-800 mb-8">{{ $socialTitle }}</h2>
                    <div class="flex flex-wrap justify-center gap-8 md:gap-12">
                        @forelse($socialLinks as $link)
                            {{-- ส่วนนี้ปรับแต่งให้แสดงแค่ไอคอนและมีปุ่มแก้ไขแสดงเมื่อ Hover เหมือน User side แต่มี Action buttons --}}
                            <div id="social-item-{{ $link->id }}" class="relative group/soc cursor-pointer">
                                {{-- Action Buttons: แสดงเฉพาะเมื่อ Hover ที่ไอคอน --}}
                                <div class="absolute -top-3 -right-3 flex gap-1 opacity-0 group-hover/soc:opacity-100 transition-opacity z-10">
                                    <button onclick="editSocial({{ $link }})" class="bg-yellow-500 text-white p-1 rounded-full text-[10px] shadow-sm"><i class="fas fa-edit"></i></button>
                                    <button type="button" onclick="confirmDelete('{{ route('admin.about-social-links.destroy', $link->id) }}', document.getElementById('social-item-{{ $link->id }}'))" 
                                        class="bg-red-500 text-white p-1 rounded-full text-[10px] shadow-sm" title="ลบ">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                {{-- แสดงไอคอนเพียวๆ สีตามแบรนด์ (ดึงค่า bg_color มาใช้เป็นสีตัวอักษร) --}}
                                <div class="flex items-center justify-center text-4xl md:text-5xl">
                                    @if($link->image_path)
                                        <img src="{{ asset('storage/' . $link->image_path) }}" class="w-12 h-12 object-contain">
                                    @else
                                        <div style="color: {{ $link->bg_color ?? '#333' }};">
                                            <i class="{{ $link->icon_class }}"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-gray-400 text-sm italic">ยังไม่มีข้อมูลโซเชียลมีเดีย (เพิ่มได้ที่ปุ่มด้านบน)</div>
                        @endforelse
                    </div>
                </div>

                <div class="text-center mb-16 space-y-3">
                    <h2 class="text-xl font-extrabold text-gray-800">{{ $teamTitle }}</h2>
                    <p class="text-sm text-gray-600 font-light">{{ $teamSub }}</p>
                    <p class="text-sm font-bold text-gray-700">โทร & Line : <span class="text-gray-600 font-normal">{{ $teamPhone }}</span></p>
                    <p class="text-sm font-bold text-gray-700">E-mail : <span class="text-blue-500 font-normal">{{ $teamEmail }}</span></p>
                </div>
            </div>

            {{-- 6. CONTACT LOCATIONS --}}
            {{-- <div class="container mx-auto px-4 max-w-5xl py-20 border-t border-gray-200 group relative">
                <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-all duration-300 z-50">
                    <button onclick="openAddContactModal()" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-full shadow-lg text-sm font-medium transition-colors"><i class="fas fa-plus mr-2"></i> เพิ่มสาขา/ที่อยู่</button>
                </div>
                <div class="text-center mb-10"><h2 class="text-2xl font-extrabold text-gray-800">🏢 ติดต่อเรา 🏢</h2></div>
                <div class="space-y-16">
                    @forelse($contacts as $contact)
                        <div id="contact-item-{{ $contact->id }}" class="flex flex-col md:flex-row items-center gap-8 group/loc relative">
                            <div class="absolute top-0 right-0 opacity-0 group-hover/loc:opacity-100 transition-opacity flex gap-1">
                                <button onclick="editContact({{ $contact }})" class="bg-yellow-500 text-white p-2 rounded-full text-xs shadow-lg hover:bg-yellow-600"><i class="fas fa-edit"></i></button>
                                <button type="button" onclick="confirmDelete('{{ route('admin.about-contacts.destroy', $contact->id) }}', document.getElementById('contact-item-{{ $contact->id }}'))" 
                                    class="bg-red-500 text-white p-2 rounded-full text-xs shadow-lg hover:bg-red-600" title="ลบ">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="w-full md:w-1/2 text-center space-y-4 {{ $loop->index % 2 != 0 ? 'md:order-2' : '' }}">
                                <h3 class="font-bold text-lg text-gray-800 underline decoration-2 underline-offset-8">{{ $contact->title }}</h3>
                                <p class="text-[14px] text-gray-600 leading-relaxed font-light mt-4">{!! nl2br(e($contact->address)) !!}</p>
                                @if($contact->map_link)
                                    <div class="text-[13px] flex items-center justify-center gap-2"><i class="fas fa-map text-emerald-500"></i><span class="font-bold text-gray-700">Map :</span> <span class="text-blue-500 truncate max-w-[200px]">{{ $contact->map_link }}</span></div>
                                @endif
                            </div>
                            <div class="w-full md:w-1/2 flex justify-center {{ $loop->index % 2 != 0 ? 'md:order-1' : '' }}">
                                <div class="w-full max-w-sm aspect-[4/3] bg-gray-200 overflow-hidden shadow-lg border-4 border-white">
                                    @if($contact->image_path)
                                        <img src="{{ asset('storage/' . $contact->image_path) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-building text-5xl"></i></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 bg-white rounded-2xl border-2 border-dashed border-gray-300 text-gray-400">ยังไม่มีข้อมูลติดต่อสาขา</div>
                    @endforelse
                </div>
            </div> --}}
        </div>
    </div>

    {{-- --- MODALS --- --}}

    {{-- 1. Hero Settings Modal --}}
    <div id="settingsHeroModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-700 overflow-hidden">
            <div class="bg-gray-900 px-6 py-4 flex justify-between items-center border-b border-gray-700">
                <h3 class="text-lg font-bold text-white">ตั้งค่าส่วนหัว (Hero)</h3>
                <button onclick="closeModal('settingsHeroModal')" class="text-gray-400 hover:text-white"><i class="fas fa-times text-xl"></i></button>
            </div>
            <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6 space-y-4" onsubmit="handleAjaxFormSubmit(event, 'settingsHeroModal')">
                @csrf
                <div><label class="block text-sm font-medium text-gray-300 mb-1">หัวข้อหลัก</label><input type="text" name="settings[about_title]" value="{{ $aboutTitle }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1">คำโปรย</label><textarea name="settings[about_subtitle]" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white">{{ $aboutSub }}</textarea></div>
                <div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeModal('settingsHeroModal')" class="px-4 py-2 text-gray-300">ยกเลิก</button><button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg">บันทึก</button></div>
            </form>
        </div>
    </div>

    {{-- 2. Life Content Settings Modal --}}
    <div id="settingsLifeModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-700 overflow-hidden">
            <div class="bg-gray-900 px-6 py-4 flex justify-between items-center border-b border-gray-700">
                <h3 class="text-lg font-bold text-white">ตั้งค่าข้อความ Life with ติดใจ</h3>
                <button onclick="closeModal('settingsLifeModal')" class="text-gray-400 hover:text-white"><i class="fas fa-times text-xl"></i></button>
            </div>
            <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6 space-y-4" onsubmit="handleAjaxFormSubmit(event, 'settingsLifeModal')">
                @csrf
                <div><label class="block text-sm font-medium text-gray-300 mb-1">หัวข้อ</label><input type="text" name="settings[life_title]" value="{{ $lifeTitle }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1">คำบรรยาย</label><textarea name="settings[life_subtitle]" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white">{{ $lifeSub }}</textarea></div>
                <div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeModal('settingsLifeModal')" class="px-4 py-2 text-gray-300">ยกเลิก</button><button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg">บันทึก</button></div>
            </form>
        </div>
    </div>

    {{-- 3. Add/Edit Video Modal --}}
    <div id="videoModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-700 overflow-hidden">
            <div class="bg-gray-900 px-6 py-4 flex justify-between items-center border-b border-gray-700">
                <h3 class="text-lg font-bold text-white" id="videoModalTitle">เพิ่มวิดีโอ</h3>
                <button onclick="closeModal('videoModal')" class="text-gray-400 hover:text-white"><i class="fas fa-times text-xl"></i></button>
            </div>
            <form action="{{ route('admin.about-videos.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4" id="videoForm" onsubmit="handleAjaxFormSubmit(event, 'videoModal')">
                @csrf
                <div id="videoMethod"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1">ชื่อวิดีโอ</label><input type="text" name="title" id="video_title" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1">ลิงก์วิดีโอ (TikTok/URL)</label><input type="text" name="video_url" id="video_url" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-300 mb-1">ความยาว (เช่น 01:30)</label><input type="text" name="duration" id="video_duration" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white"></div>
                    <div><label class="block text-sm font-medium text-gray-300 mb-1">ลำดับการแสดง</label><input type="number" name="sort_order" id="video_sort" value="0" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white"></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">รูปหน้าปก</label>
                    <input type="file" name="thumbnail" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm">
                    <div id="video_thumbnail_preview" class="mt-2 hidden relative w-fit group">
                        <img src="" class="h-20 rounded border border-gray-600">
                        <button type="button" id="btn_delete_video_thumbnail" onclick="deleteVideoThumbnail()" 
                                class="absolute -top-2 -right-2 bg-red-500 text-white p-1 rounded-full text-[10px] opacity-0 group-hover:opacity-100 transition-opacity shadow-lg">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeModal('videoModal')" class="px-4 py-2 text-gray-300">ยกเลิก</button><button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg">บันทึก</button></div>
            </form>
        </div>
    </div>

    {{-- 4. Add/Edit Gallery Modal --}}
    <div id="galleryModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl border border-gray-700 overflow-hidden">
            <div class="bg-gray-900 px-6 py-4 flex justify-between items-center border-b border-gray-700">
                <h3 class="text-lg font-bold text-white" id="galleryModalTitle">เพิ่มอัลบั้ม</h3>
                <button onclick="closeModal('galleryModal')" class="text-gray-400 hover:text-white"><i class="fas fa-times text-xl"></i></button>
            </div>
            <form action="{{ route('admin.about-galleries.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4" id="galleryForm" onsubmit="handleAjaxFormSubmit(event, 'galleryModal')">
                @csrf
                <div id="galleryMethod"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1">ชื่ออัลบั้ม</label><input type="text" name="title" id="gallery_title" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1">ลำดับ</label><input type="number" name="sort_order" id="gallery_sort" value="0" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white"></div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">เพิ่มรูปภาพ (เลือกได้หลายรูป)</label>
                    <input type="file" name="images[]" multiple class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm">
                </div>
                <div id="gallery_images_preview" class="grid grid-cols-4 gap-2 mt-2 overflow-y-auto max-h-40"></div>
                <div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeModal('galleryModal')" class="px-4 py-2 text-gray-300">ยกเลิก</button><button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg">บันทึก</button></div>
            </form>
        </div>
    </div>

    {{-- 5. Social & Team Settings Modal --}}
    <div id="settingsSocialTeamModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-700 overflow-hidden">
            <div class="bg-gray-900 px-6 py-4 flex justify-between items-center border-b border-gray-700">
                <h3 class="text-lg font-bold text-white">ตั้งค่าข้อความส่วนทีม</h3>
                <button onclick="closeModal('settingsSocialTeamModal')" class="text-gray-400 hover:text-white"><i class="fas fa-times text-xl"></i></button>
            </div>
            <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6 space-y-4" onsubmit="handleAjaxFormSubmit(event, 'settingsSocialTeamModal')">
                @csrf
                <div><label class="block text-sm font-medium text-gray-300 mb-1">หัวข้อส่วนโซเชียล</label><input type="text" name="settings[social_title]" value="{{ $socialTitle }}" class="w-full bg-gray-900 border border-gray-700 rounded px-3 py-1.5 text-white text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1">หัวข้อส่วนทีม</label><input type="text" name="settings[team_title]" value="{{ $teamTitle }}" class="w-full bg-gray-900 border border-gray-700 rounded px-3 py-1.5 text-white text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1">คำบรรยาย</label><input type="text" name="settings[team_subtitle]" value="{{ $teamSub }}" class="w-full bg-gray-900 border border-gray-700 rounded px-3 py-1.5 text-white text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1">โทร & Line</label><input type="text" name="settings[team_phone]" value="{{ $teamPhone }}" class="w-full bg-gray-900 border border-gray-700 rounded px-3 py-1.5 text-white text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1">E-mail</label><input type="text" name="settings[team_email]" value="{{ $teamEmail }}" class="w-full bg-gray-900 border border-gray-700 rounded px-3 py-1.5 text-white text-sm"></div>
                <div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeModal('settingsSocialTeamModal')" class="px-4 py-2 text-gray-300">ยกเลิก</button><button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg">บันทึก</button></div>
            </form>
        </div>
    </div>

    {{-- 5.1 Add/Edit Social Link Modal (มี Icon Picker ที่ปรับแก้แล้ว) --}}
    <div id="socialModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-700 overflow-hidden">
            <div class="bg-gray-900 px-6 py-4 flex justify-between items-center border-b border-gray-700">
                <h3 class="text-lg font-bold text-white" id="socialModalTitle">เพิ่มโซเชียลมีเดีย</h3>
                <button onclick="closeModal('socialModal')" class="text-gray-400 hover:text-white"><i class="fas fa-times text-xl"></i></button>
            </div>
            <form action="{{ route('admin.about-social-links.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4" id="socialForm" onsubmit="handleAjaxFormSubmit(event, 'socialModal')">
                @csrf
                <div id="socialMethod"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-300 mb-1">ชื่อเรียก (เช่น Facebook)</label><input type="text" name="title" id="social_title" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white"></div>
                    <div><label class="block text-sm font-medium text-gray-300 mb-1">ลำดับ</label><input type="number" name="sort_order" id="social_sort" value="0" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white"></div>
                </div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1">ลิงก์ URL</label><input type="text" name="url" id="social_url" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white"></div>
                
                <div class="flex gap-4 p-1 bg-gray-900 rounded-lg border border-gray-700">
                    <button type="button" onclick="toggleIconType('font')" id="btn-type-font" class="flex-1 py-2 text-sm font-medium rounded-md transition-all bg-emerald-600 text-white">FontAwesome</button>
                    <button type="button" onclick="toggleIconType('image')" id="btn-type-image" class="flex-1 py-2 text-sm font-medium rounded-md transition-all text-gray-400 hover:text-white">Image Icon</button>
                </div>

                {{-- ส่วนที่เพิ่ม Icon Picker --}}
                <div id="section-font-icon" class="bg-gray-800/50 p-3 rounded-lg border border-gray-700">
                    <label class="block text-sm font-medium text-gray-300 mb-2">เลือกไอคอน หรือกรอก Icon Class (FontAwesome)</label>
                    
                    {{-- Icon Picker Grid --}}
                    <div class="grid grid-cols-6 sm:grid-cols-8 gap-2 mb-3 max-h-32 overflow-y-auto p-2 bg-gray-900 border border-gray-700 rounded-lg" id="iconPickerGrid">
                        @php
                            $commonIcons = [
                                'fab fa-facebook-f', 'fab fa-line', 'fab fa-instagram', 'fab fa-tiktok', 
                                'fab fa-twitter', 'fab fa-x-twitter', 'fab fa-youtube', 'fab fa-whatsapp',
                                'fab fa-linkedin-in', 'fab fa-github', 'fab fa-discord', 'fab fa-telegram-plane',
                                'fas fa-globe', 'fas fa-envelope', 'fas fa-phone', 'fas fa-map-marker-alt'
                            ];
                        @endphp
                        @foreach($commonIcons as $icon)
                            <button type="button" onclick="selectIcon('{{ $icon }}')" class="icon-btn flex items-center justify-center w-8 h-8 rounded bg-gray-800 hover:bg-emerald-600 text-gray-300 hover:text-white transition-colors border border-transparent focus:outline-none" data-icon="{{ $icon }}" title="{{ $icon }}">
                                <i class="{{ $icon }}"></i>
                            </button>
                        @endforeach
                    </div>

                    {{-- Manual Input & Preview --}}
                    <div class="flex gap-3 items-center">
                        <div class="w-10 h-10 flex-shrink-0 bg-gray-800 border border-gray-600 rounded-lg flex items-center justify-center text-white text-lg" id="currentIconPreview">
                            <i class="fas fa-question"></i>
                        </div>
                        <input type="text" name="icon_class" id="social_icon" placeholder="เช่น fab fa-facebook-f" oninput="updateIconPreview(this.value)" class="flex-1 bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white font-mono text-sm">
                    </div>
                    <p class="text-[10px] text-gray-500 mt-2">คุณสามารถคลิกเลือกจากด้านบน หรือพิมพ์เพิ่มเองโดยดูจาก <a href="https://fontawesome.com/search?o=r&m=free" target="_blank" class="text-emerald-400 hover:underline">fontawesome.com</a></p>
                </div>

                {{-- ส่วนอัปโหลดรูปภาพ --}}
                <div id="section-image-icon" class="hidden bg-gray-800/50 p-3 rounded-lg border border-gray-700">
                    <label class="block text-sm font-medium text-gray-300 mb-2">อัปโหลดรูปไอคอน (PNG, SVG แนะนำ)</label>
                    <input type="file" name="image" id="social_image" onchange="previewSocialImage(this)" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm">
                    <div id="social_image_preview" class="mt-3 hidden">
                        <p class="text-[10px] text-gray-400 mb-1">ตัวอย่างการแสดงผล:</p>
                        <div class="w-16 h-16 bg-gray-900 rounded-lg border border-gray-700 flex items-center justify-center overflow-hidden">
                            <img src="" class="max-w-full max-h-full object-contain">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- ใช้สีแบรนด์ (ในระบบเดิมคือสีพื้นหลังไอคอน) มาเป็นสีของตัวไอคอนเพียวๆ --}}
                    <div><label class="block text-sm font-medium text-gray-300 mb-1">สีของไอคอน</label><input type="color" name="bg_color" id="social_bg" value="#1877F2" class="w-full h-10 bg-gray-900 border border-gray-700 rounded-lg px-1 py-1 cursor-pointer"></div>
                </div>
                <div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeModal('socialModal')" class="px-4 py-2 text-gray-300">ยกเลิก</button><button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg">บันทึก</button></div>
            </form>
        </div>
    </div>

    {{-- 6. Add/Edit Contact Location Modal --}}
    <div id="contactModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-700 overflow-hidden">
            <div class="bg-gray-900 px-6 py-4 flex justify-between items-center border-b border-gray-700">
                <h3 class="text-lg font-bold text-white" id="contactModalTitle">เพิ่มสาขา/ที่อยู่</h3>
                <button onclick="closeModal('contactModal')" class="text-gray-400 hover:text-white"><i class="fas fa-times text-xl"></i></button>
            </div>
            <form action="{{ route('admin.about-contacts.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4" id="contactForm" onsubmit="handleAjaxFormSubmit(event, 'contactModal')">
                @csrf
                <div id="contactMethod"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1">ชื่อสาขา/หัวข้อ</label><input type="text" name="title" id="contact_title" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1">ที่อยู่</label><textarea name="address" id="contact_address" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white"></textarea></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1">ข้อมูลการเดินทาง (Transport Info)</label><textarea name="transport_info" id="contact_transport" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white"></textarea></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1">Map Link (Google Maps)</label><input type="text" name="map_link" id="contact_map" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1">รูปภาพสถานที่</label><input type="file" name="image" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1">ลำดับ</label><input type="number" name="sort_order" id="contact_sort" value="0" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white"></div>
                <div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeModal('contactModal')" class="px-4 py-2 text-gray-300">ยกเลิก</button><button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg">บันทึก</button></div>
            </form>
        </div>
    </div>

    {{-- ★ VIDEO PLAYER MODAL ★ --}}
    <div id="videoPlayerModal" class="fixed inset-0 z-[1000] bg-black/90 hidden items-center justify-center opacity-0 transition-opacity duration-300">
        <button onclick="closeVideoPlayer()" class="absolute top-4 right-4 text-white hover:text-red-500 transition-colors z-[1010] p-4"><i class="fas fa-times text-4xl"></i></button>
        <div class="w-full max-w-md aspect-[9/16] relative bg-black flex items-center justify-center overflow-hidden rounded-2xl shadow-2xl mx-4" id="videoContainer">
        </div>
    </div>

    <script>
        let currentVideoId = null;

        // Toast Helper
        function showToast(title, icon = 'success') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            Toast.fire({ icon: icon, title: title });
        }

        // AJAX Form Handler
        async function handleAjaxFormSubmit(event, modalId) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> กำลังบันทึก...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    showToast(result.message || 'บันทึกข้อมูลเรียบร้อยแล้ว');
                    if (modalId) closeModal(modalId);
                    
                    // หน้านี้มีความซับซ้อนในการอัปเดต UI เฉพาะจุดแบบ Real-time 
                    // จึงขอใช้วิธีโหลดข้อมูลใหม่แบบนุ่มนวล หรือ reload เพื่อให้ข้อมูลถูกต้องที่สุด
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    Swal.fire('ข้อผิดพลาด', result.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        }

        // AJAX Delete Handler
        async function confirmDelete(url, elementToRemove = null, message = 'คุณแน่ใจหรือไม่ที่จะลบรายการนี้?') {
            const result = await Swal.fire({
                title: 'ยืนยันการลบ',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก',
                background: '#1f2937',
                color: '#fff'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });

                    const data = await response.json();

                    if (response.ok) {
                        showToast(data.message || 'ลบข้อมูลเรียบร้อยแล้ว');
                        if (elementToRemove) {
                            elementToRemove.classList.add('scale-95', 'opacity-0');
                            setTimeout(() => elementToRemove.remove(), 300);
                        } else {
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    } else {
                        Swal.fire('ผิดพลาด', data.message || 'ไม่สามารถลบข้อมูลได้', 'error');
                    }
                } catch (error) {
                    Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                }
            }
        }

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('flex');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
        }

        // Video Preview
        function playVideoPreview(video) {
            const modal = document.getElementById('videoPlayerModal');
            const container = document.getElementById('videoContainer');
            let embedHtml = video.embed_html;
            if (!embedHtml && video.video_url && video.video_url.includes('tiktok.com')) {
                // ปรับปรุงการดึง Video ID ให้รองรับ URL หลายรูปแบบ
                let videoId = '';
                const parts = video.video_url.split('?')[0].split('/');
                videoId = parts.filter(p => p !== '').pop();
                
                embedHtml = `<blockquote class="tiktok-embed" cite="${video.video_url}" data-video-id="${videoId}" style="max-width: 605px;min-width: 325px;" > <section> </section> </blockquote>`;
            }
            if (embedHtml) {
                container.innerHTML = embedHtml;
                
                if (window.tiktok && window.tiktok.embed) {
                    window.tiktok.embed.render();
                } else {
                    const script = document.createElement('script');
                    script.id = 'tiktok-embed-script';
                    script.src = "https://www.tiktok.com/embed.js";
                    script.async = true;
                    script.onload = () => {
                        if (window.tiktok && window.tiktok.embed) window.tiktok.embed.render();
                    };
                    document.body.appendChild(script);
                }
            } else {
                container.innerHTML = `<div class="text-white text-center p-10"><i class="fas fa-video-slash text-5xl mb-4"></i><br>ไม่สามารถโหลดวิดีโอได้</div>`;
            }
            modal.classList.remove('hidden'); modal.classList.add('flex');
            setTimeout(() => modal.classList.remove('opacity-0'), 10);
        }
        function closeVideoPlayer() {
            const modal = document.getElementById('videoPlayerModal');
            modal.classList.add('opacity-0');
            setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); document.getElementById('videoContainer').innerHTML = ''; }, 300);
        }

        // Video Management
        function openAddVideoModal() {
            currentVideoId = null;
            document.getElementById('videoForm').action = "{{ route('admin.about-videos.store') }}";
            document.getElementById('videoMethod').innerHTML = "";
            document.getElementById('videoModalTitle').innerText = "เพิ่มวิดีโอ";
            document.getElementById('videoForm').reset();
            document.getElementById('video_thumbnail_preview').classList.add('hidden');
            openModal('videoModal');
        }
        function editVideo(video) {
            currentVideoId = video.id;
            document.getElementById('videoForm').action = "/admin/about-videos/" + video.id;
            document.getElementById('videoMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('videoModalTitle').innerText = "แก้ไขวิดีโอ";
            document.getElementById('video_title').value = video.title || '';
            document.getElementById('video_url').value = video.video_url;
            document.getElementById('video_duration').value = video.duration || '';
            document.getElementById('video_sort').value = video.sort_order;
            if(video.thumbnail_path) {
                document.getElementById('video_thumbnail_preview').classList.remove('hidden');
                document.getElementById('video_thumbnail_preview').querySelector('img').src = "/storage/" + video.thumbnail_path;
            } else {
                document.getElementById('video_thumbnail_preview').classList.add('hidden');
            }
            openModal('videoModal');
        }

        function deleteVideoThumbnail() {
            if (currentVideoId && confirm('ลบรูปหน้าปกนี้?')) {
                fetch(`/admin/about-videos-thumbnail/${currentVideoId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(res => res.json()).then(data => {
                    if (data.success) {
                        document.getElementById('video_thumbnail_preview').classList.add('hidden');
                        document.getElementById('video_thumbnail_preview').querySelector('img').src = "";
                    }
                });
            }
        }

        // --- Social Management with Icon Picker & Image Upload ---
        function toggleIconType(type) {
            const fontSection = document.getElementById('section-font-icon');
            const imageSection = document.getElementById('section-image-icon');
            const btnFont = document.getElementById('btn-type-font');
            const btnImage = document.getElementById('btn-type-image');
            const iconInput = document.getElementById('social_icon');

            if (type === 'font') {
                fontSection.classList.remove('hidden');
                imageSection.classList.add('hidden');
                btnFont.classList.add('bg-emerald-600', 'text-white');
                btnFont.classList.remove('text-gray-400', 'hover:text-white');
                btnImage.classList.remove('bg-emerald-600', 'text-white');
                btnImage.classList.add('text-gray-400', 'hover:text-white');
                iconInput.setAttribute('required', 'required');
            } else {
                fontSection.classList.add('hidden');
                imageSection.classList.remove('hidden');
                btnImage.classList.add('bg-emerald-600', 'text-white');
                btnImage.classList.remove('text-gray-400', 'hover:text-white');
                btnFont.classList.remove('bg-emerald-600', 'text-white');
                btnFont.classList.add('text-gray-400', 'hover:text-white');
                iconInput.removeAttribute('required');
            }
        }

        function previewSocialImage(input) {
            const previewContainer = document.getElementById('social_image_preview');
            const previewImg = previewContainer.querySelector('img');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function openAddSocialModal() {
            document.getElementById('socialForm').action = "{{ route('admin.about-social-links.store') }}";
            document.getElementById('socialMethod').innerHTML = "";
            document.getElementById('socialModalTitle').innerText = "เพิ่มโซเชียลมีเดีย";
            document.getElementById('socialForm').reset();
            document.getElementById('social_image_preview').classList.add('hidden');
            toggleIconType('font');
            updateIconPreview(''); // เคลียร์ Preview
            openModal('socialModal');
        }
        
        function editSocial(link) {
            document.getElementById('socialForm').action = "/admin/about-social-links/" + link.id;
            document.getElementById('socialMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('socialModalTitle').innerText = "แก้ไขโซเชียลมีเดีย";
            document.getElementById('social_title').value = link.title;
            document.getElementById('social_url').value = link.url;
            document.getElementById('social_icon').value = link.icon_class || '';
            document.getElementById('social_bg').value = link.bg_color;
            document.getElementById('social_sort').value = link.sort_order;
            
            if (link.image_path) {
                toggleIconType('image');
                const previewContainer = document.getElementById('social_image_preview');
                previewContainer.querySelector('img').src = "/storage/" + link.image_path;
                previewContainer.classList.remove('hidden');
            } else {
                toggleIconType('font');
                document.getElementById('social_image_preview').classList.add('hidden');
            }
            
            updateIconPreview(link.icon_class); // โหลดพรีวิวของเก่าให้
            openModal('socialModal');
        }

        // ฟังก์ชันใหม่สำหรับ Icon Picker
        function selectIcon(iconClass) {
            document.getElementById('social_icon').value = iconClass;
            updateIconPreview(iconClass);
        }

        function updateIconPreview(iconClass) {
            const previewDiv = document.getElementById('currentIconPreview');
            // แสดงไอคอนในกล่องพรีวิว
            previewDiv.innerHTML = `<i class="${iconClass || 'fas fa-question'}"></i>`;
            
            // ทำให้ปุ่มใน Grid สว่างขึ้นถ้าตรงกับที่เลือก
            document.querySelectorAll('.icon-btn').forEach(btn => {
                if(btn.dataset.icon === iconClass) {
                    btn.classList.add('bg-emerald-600', 'border-emerald-400', 'text-white');
                    btn.classList.remove('bg-gray-800', 'text-gray-300', 'border-transparent');
                } else {
                    btn.classList.remove('bg-emerald-600', 'border-emerald-400', 'text-white');
                    btn.classList.add('bg-gray-800', 'text-gray-300', 'border-transparent');
                }
            });
        }
        // ----------------------------------------

        // Gallery Management
        function openAddGalleryModal() {
            document.getElementById('galleryForm').action = "{{ route('admin.about-galleries.store') }}";
            document.getElementById('galleryMethod').innerHTML = "";
            document.getElementById('galleryModalTitle').innerText = "เพิ่มอัลบั้ม";
            document.getElementById('galleryForm').reset();
            document.getElementById('gallery_images_preview').innerHTML = "";
            openModal('galleryModal');
        }
        function editGallery(gallery) {
            document.getElementById('galleryForm').action = "/admin/about-galleries/" + gallery.id;
            document.getElementById('galleryMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('galleryModalTitle').innerText = "แก้ไขอัลบั้ม";
            document.getElementById('gallery_title').value = gallery.title;
            document.getElementById('gallery_sort').value = gallery.sort_order;
            const preview = document.getElementById('gallery_images_preview');
            preview.innerHTML = "";
            gallery.images.forEach(img => {
                const div = document.createElement('div'); div.className = "relative h-20 group";
                div.innerHTML = `<img src="/storage/${img.image_path}" class="w-full h-full object-cover rounded border border-gray-700"><button type="button" onclick="deleteGalleryImage(${img.id}, this)" class="absolute top-0 right-0 bg-red-500 text-white p-1 rounded-full text-[10px] opacity-0 group-hover:opacity-100 transition-opacity"><i class="fas fa-times"></i></button>`;
                preview.appendChild(div);
            });
            openModal('galleryModal');
        }
        function deleteGalleryImage(id, btn) {
            if(confirm('ลบรูปภาพนี้?')) {
                fetch(`/admin/about-gallery-images/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(res => res.json()).then(data => { if(data.success) btn.closest('div').remove(); });
            }
        }

        // Contact Management
        function openAddContactModal() {
            document.getElementById('contactForm').action = "{{ route('admin.about-contacts.store') }}";
            document.getElementById('contactMethod').innerHTML = "";
            document.getElementById('contactModalTitle').innerText = "เพิ่มสาขา/ที่อยู่";
            document.getElementById('contactForm').reset();
            openModal('contactModal');
        }
        function editContact(contact) {
            document.getElementById('contactForm').action = "/admin/about-contacts/" + contact.id;
            document.getElementById('contactMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('contactModalTitle').innerText = "แก้ไขสาขา/ที่อยู่";
            document.getElementById('contact_title').value = contact.title;
            document.getElementById('contact_address').value = contact.address;
            document.getElementById('contact_transport').value = contact.transport_info || '';
            document.getElementById('contact_map').value = contact.map_link;
            document.getElementById('contact_sort').value = contact.sort_order;
            openModal('contactModal');
        }
    </script>

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
@endsection