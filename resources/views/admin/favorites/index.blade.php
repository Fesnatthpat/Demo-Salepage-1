@extends('layouts.admin')

@section('title', 'จัดการ "เกี่ยวกับติดใจ" (Visual Editor)')

@section('page-title')
    <div class="text-2xl font-bold text-gray-100 flex items-center">
        <i class="fas fa-desktop text-emerald-500 mr-3"></i> ระบบจำลองหน้าเว็บ (Visual Editor)
    </div>
@endsection

@section('content')

    {{-- แอบดึง Setting มาใช้ในหน้าแอดมินด้วย --}}
    @php
        $aboutTitle = \App\Models\SiteSetting::get('about_title') ?? 'เกี่ยวกับติดใจ';
        $aboutSub = \App\Models\SiteSetting::get('about_subtitle') ?? 'ความตั้งใจของเรา...เพื่อส่งมอบความสุขให้คุณ';
        $email = \App\Models\SiteSetting::get('contact_email') ?? 'support@saledemo.com';
        $phone = \App\Models\SiteSetting::get('contact_phone') ?? '012-345-6789';

        // 🟢 ดึงข้อมูลรูปโลโก้จากระบบ Setting
        $siteLogoPath = \App\Models\SiteSetting::get('site_logo');
        $siteLogoUrl = $siteLogoPath ? asset('storage/' . $siteLogoPath) : asset('images/logo/logo1.png');
    @endphp

    <div class="bg-gray-800 rounded-2xl shadow-2xl overflow-hidden border-4 border-gray-700">

        <div
            class="bg-gray-900 px-6 py-4 flex flex-col sm:flex-row justify-between items-center border-b border-gray-700 gap-4">
            <div class="text-emerald-400 font-medium text-sm flex items-center">
                <i class="fas fa-mouse-pointer mr-2 animate-bounce"></i> เอาเมาส์ไปชี้ที่เนื้อหา หรือส่วนหัว/ท้าย
                เพื่อทำการแก้ไข
            </div>
            <button onclick="openCreateModal()"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg font-bold shadow-lg transition-transform hover:scale-105 border-none">
                <i class="fas fa-plus-circle mr-2"></i> เพิ่มเนื้อหาใหม่
            </button>
        </div>

        <div class="bg-gray-50 h-[700px] overflow-y-auto relative font-sans">

            {{-- ============================================== --}}
            {{-- 🟢 ส่วนจำลอง HERO SECTION (ชี้แล้วแก้ได้) --}}
            {{-- ============================================== --}}
            <div
                class="relative bg-gradient-to-br from-red-700 via-red-600 to-red-500 text-white pt-16 pb-24 overflow-hidden group">

                {{-- ปุ่มแก้ไขส่วนหัว --}}
                <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-50">
                    <button onclick="openSettingsModal('hero')"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg shadow-md font-bold text-sm">
                        <i class="fas fa-edit mr-1"></i> แก้ไขส่วนหัว & โลโก้
                    </button>
                </div>
                <div
                    class="absolute inset-0 border-4 border-transparent group-hover:border-yellow-400 border-dashed pointer-events-none z-40">
                </div>

                <div class="container mx-auto px-4 relative z-10 text-center">
                    <div class="mb-6 flex justify-center">
                        <div class="p-4 bg-white/10 backdrop-blur-md rounded-full shadow-xl ring-4 ring-white/20">
                            {{-- 🟢 แสดงโลโก้ที่ดึงมา --}}
                            <img src="{{ $siteLogoUrl }}" class="h-16 w-auto drop-shadow-lg object-contain"
                                onerror="this.src='https://via.placeholder.com/150x150?text=LOGO';" />
                        </div>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-extrabold mb-3 tracking-wide text-shadow-lg">{{ $aboutTitle }}
                    </h1>
                    <p class="text-red-100 text-base md:text-lg font-light max-w-2xl mx-auto">{{ $aboutSub }}</p>
                </div>
            </div>

            {{-- ============================================== --}}
            {{-- 🟢 ส่วนจำลอง บล็อกเนื้อหา (ชี้แล้วแก้ได้) --}}
            {{-- ============================================== --}}
            <div class="container mx-auto px-4 -mt-16 relative z-20 pb-16">
                @forelse($favorites as $index => $fav)
                    <div
                        class="bg-white rounded-3xl shadow-lg p-8 mb-8 border-b-4 {{ $fav->is_active ? 'border-red-500' : 'border-gray-400 opacity-60' }} relative group transition-all duration-300">
                        <div
                            class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-50 flex gap-2">
                            <button onclick="openEditModal(this)" data-id="{{ $fav->id }}"
                                data-title="{{ $fav->title }}" data-content="{{ $fav->content }}"
                                data-image="{{ $fav->image_path ? asset('storage/' . $fav->image_path) : '' }}"
                                data-sort="{{ $fav->sort_order }}" data-active="{{ $fav->is_active }}"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-bold text-sm">
                                <i class="fas fa-edit mr-1"></i> แก้ไข
                            </button>
                            <form action="{{ route('admin.favorites.destroy', $fav->id) }}" method="POST"
                                onsubmit="return confirm('ลบเนื้อหานี้ใช่หรือไม่?');" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-bold text-sm">
                                    <i class="fas fa-trash-alt mr-1"></i> ลบ
                                </button>
                            </form>
                        </div>
                        <div
                            class="absolute inset-0 border-2 border-transparent group-hover:border-blue-400 border-dashed rounded-3xl pointer-events-none">
                        </div>

                        <div class="flex flex-col md:flex-row items-center gap-8">
                            <div
                                class="w-full {{ $fav->image_path ? 'md:w-1/2' : '' }} {{ $index % 2 == 0 ? 'order-2 md:order-1' : 'order-2 md:order-2' }}">
                                <div class="flex items-center gap-3 mb-4"><span
                                        class="w-1.5 h-6 bg-red-600 rounded-full"></span>
                                    <h2 class="text-2xl font-bold text-gray-800">{{ $fav->title }}</h2>
                                </div>
                                <p class="text-gray-600 leading-relaxed whitespace-pre-line">{{ $fav->content }}</p>
                            </div>
                            @if ($fav->image_path)
                                <div
                                    class="w-full md:w-1/2 {{ $index % 2 == 0 ? 'order-1 md:order-2' : 'order-1 md:order-1' }}">
                                    <img src="{{ asset('storage/' . $fav->image_path) }}"
                                        class="rounded-2xl shadow-md w-full h-[250px] object-cover border-4 border-white">
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 bg-white rounded-3xl shadow-sm border-dashed border-4 border-gray-300">
                        <i class="fas fa-magic text-5xl text-gray-300 mb-4 animate-pulse"></i>
                        <p class="text-gray-500 font-bold text-xl">พื้นที่สำหรับเนื้อหาของคุณ</p>
                    </div>
                @endforelse

                {{-- ============================================== --}}
                {{-- 🟢 ส่วนจำลอง Contact (ชี้แล้วแก้ได้) --}}
                {{-- ============================================== --}}
                <div
                    class="bg-gradient-to-br from-red-600 to-red-800 rounded-2xl shadow-lg p-6 text-white max-w-lg mx-auto mt-8 relative group">

                    {{-- ปุ่มแก้ไขส่วนติดต่อ --}}
                    <div
                        class="absolute -top-4 -right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-50">
                        <button onclick="openSettingsModal('contact')"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg shadow-xl font-bold text-sm border-2 border-white">
                            <i class="fas fa-edit mr-1"></i> แก้ไขช่องติดต่อ
                        </button>
                    </div>
                    <div
                        class="absolute inset-0 border-4 border-transparent group-hover:border-yellow-400 border-dashed rounded-2xl pointer-events-none z-40">
                    </div>

                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2 border-b border-red-400 pb-3">
                        <i class="fas fa-envelope"></i> ติดต่อเรา
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 bg-white/10 p-3 rounded-xl">
                            <div class="bg-white rounded-full p-2 text-red-700"><i class="fas fa-envelope"></i></div>
                            <div>
                                <p class="font-bold">{{ $email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-white/10 p-3 rounded-xl">
                            <div class="bg-white rounded-full p-2 text-red-700"><i class="fas fa-phone"></i></div>
                            <div>
                                <p class="font-bold">{{ $phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- 🛠️ Modal สำหรับแก้ไข "เนื้อหาบล็อก" (Favorites) --}}
    {{-- ========================================================================= --}}
    <div id="frontendEditModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
        <div
            class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh] border border-gray-600">
            <div class="bg-gray-900 text-white px-6 py-4 flex justify-between items-center border-b border-gray-700">
                <h3 id="modalTitle" class="text-xl font-bold text-emerald-400">จัดการเนื้อหา</h3>
                <button onclick="closeModal('frontendEditModal')" class="text-gray-400 hover:text-white"><i
                        class="fas fa-times text-2xl"></i></button>
            </div>
            <form id="frontendEditForm" method="POST" enctype="multipart/form-data"
                class="flex-1 overflow-y-auto p-6 md:p-8">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="PUT">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
                    <div class="md:col-span-3 space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">หัวข้อ (Title)</label>
                            <input type="text" name="title" id="m_title" required
                                class="w-full bg-gray-900 border-gray-600 text-white rounded-lg p-3 border">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">รายละเอียด (Content)</label>
                            <textarea name="content" id="m_content" rows="7" required
                                class="w-full bg-gray-900 border-gray-600 text-white rounded-lg p-3 border"></textarea>
                        </div>
                    </div>
                    <div class="md:col-span-2 space-y-5">
                        <div class="bg-gray-900/50 p-5 rounded-xl border border-gray-700">
                            <label class="block text-sm font-semibold text-gray-300 mb-2">รูปภาพประกอบ</label>
                            <div
                                class="mb-4 h-40 bg-gray-800 rounded-lg flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-600 relative">
                                <img id="m_img_preview" src=""
                                    class="hidden object-cover w-full h-full absolute inset-0 z-10">
                                <span id="m_img_placeholder"
                                    class="text-gray-500 text-sm z-0 flex flex-col items-center"><i
                                        class="fas fa-image text-3xl mb-1"></i>อัปโหลดรูป</span>
                            </div>
                            <input type="file" name="image" accept="image/*" onchange="previewModalImage(event)"
                                class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:bg-gray-700 file:text-white">
                        </div>
                        <div class="bg-gray-900/50 p-5 rounded-xl border border-gray-700 space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">ลำดับ</label>
                                <input type="number" name="sort_order" id="m_sort"
                                    class="w-full bg-gray-800 border-gray-600 text-white rounded-lg p-2.5 border">
                            </div>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" name="is_active" id="m_active" value="1"
                                    class="w-5 h-5 rounded border-gray-500 bg-gray-800 text-emerald-500">
                                <span class="text-sm font-semibold text-gray-300">เปิดแสดงผลหน้าเว็บ</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mt-8 pt-5 border-t border-gray-700 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('frontendEditModal')"
                        class="px-6 py-2.5 bg-gray-700 text-gray-200 rounded-lg">ยกเลิก</button>
                    <button type="submit"
                        class="px-8 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold"><i
                            class="fas fa-save mr-2"></i>บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- 🛠️ Modal สำหรับแก้ไข "การตั้งค่าเว็บ (Settings)" ส่วนหัวและส่วนท้าย --}}
    {{-- ========================================================================= --}}
    <div id="settingsEditModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
        <div
            class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col border border-gray-600">
            <div class="bg-gray-900 text-white px-6 py-4 flex justify-between items-center border-b border-gray-700">
                <h3 class="text-xl font-bold text-yellow-400"><i class="fas fa-cog mr-2"></i>แก้ไขข้อความระบบ</h3>
                <button onclick="closeModal('settingsEditModal')" class="text-gray-400 hover:text-white"><i
                        class="fas fa-times text-2xl"></i></button>
            </div>

            {{-- 🟢 เพิ่ม enctype="multipart/form-data" เพื่อให้ฟอร์มนี้ส่งไฟล์ได้ --}}
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data"
                class="p-6">
                @csrf

                {{-- ชุดข้อมูลส่วนหัว (Hero) --}}
                <div id="setting_hero_fields" class="space-y-4 hidden">
                    <div class="bg-blue-900/30 p-3 rounded text-blue-300 text-sm mb-4"><i class="fas fa-info-circle"></i>
                        แก้ไขข้อความส่วนบนสุดของเพจ</div>

                    {{-- 🟢 ช่องอัปโหลดโลโก้ --}}
                    <div class="bg-gray-900/50 p-4 rounded-lg border border-gray-700">
                        <label class="block text-sm font-semibold text-gray-300 mb-2">โลโก้เว็บไซต์ (Site Logo)</label>
                        <input type="file" name="site_logo" accept="image/*"
                            class="w-full text-sm text-gray-400 file:mr-4 file:py-1.5 file:px-4 file:rounded file:border-0 file:bg-gray-700 file:text-white hover:file:bg-gray-600 cursor-pointer">
                        <p class="text-xs text-gray-500 mt-2">หากไม่เลือกไฟล์ใหม่ ระบบจะใช้โลโก้เดิม</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-1">หัวข้อหลัก (Title)</label>
                        <input type="text" name="settings[about_title]" value="{{ $aboutTitle }}"
                            class="w-full bg-gray-900 border-gray-600 text-white rounded-lg p-3 border">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-1">คำโปรย (Subtitle)</label>
                        <input type="text" name="settings[about_subtitle]" value="{{ $aboutSub }}"
                            class="w-full bg-gray-900 border-gray-600 text-white rounded-lg p-3 border">
                    </div>
                </div>

                {{-- ชุดข้อมูลส่วนติดต่อ (Contact) --}}
                <div id="setting_contact_fields" class="space-y-4 hidden">
                    <div class="bg-blue-900/30 p-3 rounded text-blue-300 text-sm mb-4"><i class="fas fa-info-circle"></i>
                        แก้ไขข้อมูลการติดต่อด้านล่างสุด</div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-1">อีเมลติดต่อ</label>
                        <input type="email" name="settings[contact_email]" value="{{ $email }}"
                            class="w-full bg-gray-900 border-gray-600 text-white rounded-lg p-3 border">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-1">เบอร์โทรศัพท์</label>
                        <input type="text" name="settings[contact_phone]" value="{{ $phone }}"
                            class="w-full bg-gray-900 border-gray-600 text-white rounded-lg p-3 border">
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('settingsEditModal')"
                        class="px-6 py-2.5 bg-gray-700 text-gray-200 rounded-lg">ยกเลิก</button>
                    <button type="submit"
                        class="px-8 py-2.5 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-semibold"><i
                            class="fas fa-save mr-2"></i>บันทึกการตั้งค่า</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(btn) {
            document.getElementById('frontendEditModal').classList.remove('hidden');
            document.getElementById('frontendEditModal').classList.add('flex');
            document.getElementById('frontendEditForm').action = `/admin/favorites/${btn.dataset.id}`;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('modalTitle').innerHTML =
            '<i class="fas fa-edit mr-2 text-yellow-400"></i>แก้ไขเนื้อหา';
            document.getElementById('m_title').value = btn.dataset.title;
            document.getElementById('m_content').value = btn.dataset.content;
            document.getElementById('m_sort').value = btn.dataset.sort;
            document.getElementById('m_active').checked = btn.dataset.active === "1";

            const imgP = document.getElementById('m_img_preview'),
                imgPh = document.getElementById('m_img_placeholder');
            if (btn.dataset.image) {
                imgP.src = btn.dataset.image;
                imgP.classList.remove('hidden');
                imgPh.classList.add('hidden');
            } else {
                imgP.src = '';
                imgP.classList.add('hidden');
                imgPh.classList.remove('hidden');
            }
        }

        function openCreateModal() {
            document.getElementById('frontendEditModal').classList.remove('hidden');
            document.getElementById('frontendEditModal').classList.add('flex');
            document.getElementById('frontendEditForm').action = `{{ route('admin.favorites.store') }}`;
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('modalTitle').innerHTML =
                '<i class="fas fa-plus-circle mr-2 text-emerald-400"></i>เพิ่มเนื้อหาใหม่';
            document.getElementById('frontendEditForm').reset();
            document.getElementById('m_sort').value = '0';
            document.getElementById('m_active').checked = true;
            document.getElementById('m_img_preview').classList.add('hidden');
            document.getElementById('m_img_placeholder').classList.remove('hidden');
        }

        function openSettingsModal(type) {
            document.getElementById('settingsEditModal').classList.remove('hidden');
            document.getElementById('settingsEditModal').classList.add('flex');

            if (type === 'hero') {
                document.getElementById('setting_hero_fields').classList.remove('hidden');
                document.getElementById('setting_contact_fields').classList.add('hidden');
            } else {
                document.getElementById('setting_hero_fields').classList.add('hidden');
                document.getElementById('setting_contact_fields').classList.remove('hidden');
            }
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.getElementById(modalId).classList.remove('flex');
        }

        function previewModalImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var o = document.getElementById('m_img_preview'),
                    p = document.getElementById('m_img_placeholder');
                o.src = reader.result;
                o.classList.remove('hidden');
                p.classList.add('hidden');
            };
            if (event.target.files[0]) reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
