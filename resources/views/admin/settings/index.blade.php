@extends('layouts.admin')

@section('title', 'ตั้งค่าเว็บไซต์')

@section('content')
    <div class="container mx-auto pb-24">

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="bg-red-900/50 border-l-4 border-red-500 text-red-200 p-4 mb-6 rounded shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0"><i class="fas fa-exclamation-circle text-red-400"></i></div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium">พบข้อผิดพลาด</h3>
                        <ul class="mt-2 text-sm text-red-300 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Success Message --}}
        @if (session('success'))
            <div class="bg-emerald-900/50 border-l-4 border-emerald-500 text-emerald-200 p-4 mb-6 rounded shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0"><i class="fas fa-check-circle text-emerald-400"></i></div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Left Column: Content Builders --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- 1. Hero Slider --}}
                    <div class="bg-gray-800 rounded-lg shadow-lg border border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-700 bg-gray-800/50 flex items-center gap-2">
                            <i class="fas fa-images text-red-400"></i>
                            <h3 class="text-lg font-medium text-gray-100">Hero Slider (สไลด์หลัก)</h3>
                        </div>
                        <div class="p-6">
                            <input type="hidden" name="hero_slider_items" id="hero_slider_input"
                                value="{{ old('hero_slider_items', json_encode($settings['hero_slider_items'] ?? [])) }}">
                            <div id="hero_slider_list" class="space-y-3 mb-4"></div>

                            <div
                                class="grid grid-cols-1 md:grid-cols-12 gap-2 bg-gray-900/50 p-3 rounded border border-gray-700">
                                <div class="md:col-span-4"><input type="text" id="new_hero_img" placeholder="URL รูปภาพ"
                                        class="w-full text-sm bg-gray-700 border-gray-600 text-gray-100 rounded focus:ring-red-500 focus:border-red-500 placeholder-gray-400">
                                </div>
                                <div class="md:col-span-3"><input type="text" id="new_hero_title"
                                        placeholder="หัวข้อ (ถ้ามี)"
                                        class="w-full text-sm bg-gray-700 border-gray-600 text-gray-100 rounded focus:ring-red-500 focus:border-red-500 placeholder-gray-400">
                                </div>
                                <div class="md:col-span-4"><input type="text" id="new_hero_desc"
                                        placeholder="คำอธิบาย (ถ้ามี)"
                                        class="w-full text-sm bg-gray-700 border-gray-600 text-gray-100 rounded focus:ring-red-500 focus:border-red-500 placeholder-gray-400">
                                </div>
                                <div class="md:col-span-1"><button type="button" onclick="addItem('hero_slider')"
                                        class="w-full bg-emerald-600 text-white rounded hover:bg-emerald-700 py-1.5 h-full"><i
                                            class="fas fa-plus"></i></button></div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. 6 Reasons --}}
                    <div class="bg-gray-800 rounded-lg shadow-lg border border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-700 bg-gray-800/50 flex items-center gap-2">
                            <i class="fas fa-list-ul text-emerald-400"></i>
                            <h3 class="text-lg font-medium text-gray-100">6 Reasons Section</h3>
                        </div>
                        <div class="p-6">
                            <input type="hidden" name="reasons_section_items" id="reasons_input"
                                value="{{ old('reasons_section_items', json_encode($settings['reasons_section_items'] ?? [])) }}">
                            <div id="reasons_list" class="space-y-3 mb-4"></div>

                            <div
                                class="grid grid-cols-1 md:grid-cols-12 gap-2 bg-gray-900/50 p-3 rounded border border-gray-700">
                                <div class="md:col-span-3"><input type="text" id="new_reason_img"
                                        placeholder="URL รูปภาพ"
                                        class="w-full text-sm bg-gray-700 border-gray-600 text-gray-100 rounded focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400">
                                </div>
                                <div class="md:col-span-4"><input type="text" id="new_reason_title" placeholder="หัวข้อ"
                                        class="w-full text-sm bg-gray-700 border-gray-600 text-gray-100 rounded focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400">
                                </div>
                                <div class="md:col-span-4"><input type="text" id="new_reason_desc" placeholder="คำอธิบาย"
                                        class="w-full text-sm bg-gray-700 border-gray-600 text-gray-100 rounded focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400">
                                </div>
                                <div class="md:col-span-1"><button type="button" onclick="addItem('reasons')"
                                        class="w-full bg-emerald-600 text-white rounded hover:bg-emerald-700 py-1.5 h-full"><i
                                            class="fas fa-plus"></i></button></div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Service Bar --}}
                    <div class="bg-gray-800 rounded-lg shadow-lg border border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-700 bg-gray-800/50 flex items-center gap-2">
                            <i class="fas fa-check-circle text-blue-400"></i>
                            <h3 class="text-lg font-medium text-gray-100">Service Bar Items</h3>
                        </div>
                        <div class="p-6">
                            <input type="hidden" name="service_bar_items" id="service_input"
                                value="{{ old('service_bar_items', json_encode($settings['service_bar_items'] ?? [])) }}">
                            <div id="service_list" class="space-y-3 mb-4"></div>

                            <div
                                class="grid grid-cols-1 md:grid-cols-12 gap-2 bg-gray-900/50 p-3 rounded border border-gray-700">
                                <div class="md:col-span-4"><input type="text" id="new_service_icon"
                                        placeholder="Icon Class (เช่น fas fa-star)"
                                        class="w-full text-sm bg-gray-700 border-gray-600 text-gray-100 rounded focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400">
                                </div>
                                <div class="md:col-span-7"><input type="text" id="new_service_text"
                                        placeholder="ข้อความ"
                                        class="w-full text-sm bg-gray-700 border-gray-600 text-gray-100 rounded focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400">
                                </div>
                                <div class="md:col-span-1"><button type="button" onclick="addItem('service')"
                                        class="w-full bg-emerald-600 text-white rounded hover:bg-emerald-700 py-1.5 h-full"><i
                                            class="fas fa-plus"></i></button></div>
                            </div>
                            <small class="text-gray-500 mt-2 block">ดูรายชื่อไอคอนได้ที่ <a
                                    href="https://fontawesome.com/v5/search" target="_blank"
                                    class="text-blue-400 hover:underline">FontAwesome 5</a></small>
                        </div>
                    </div>

                    {{-- 4. Text Content --}}
                    <div class="bg-gray-800 rounded-lg shadow-lg border border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-700 bg-gray-800/50 flex items-center gap-2">
                            <i class="fas fa-file-alt text-yellow-400"></i>
                            <h3 class="text-lg font-medium text-gray-100">เนื้อหาข้อความ</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">ข้อมูลแพ้อาหาร</label>
                                <textarea name="allergy_info_content" rows="3"
                                    class="w-full bg-gray-700 border-gray-600 text-gray-100 rounded shadow-sm focus:ring-yellow-500 focus:border-yellow-500">{{ old('allergy_info_content', $settings['allergy_info_content'] ?? '') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">คำอธิบายเว็บไซต์ (SEO)</label>
                                <textarea name="site_description" rows="3"
                                    class="w-full bg-gray-700 border-gray-600 text-gray-100 rounded shadow-sm focus:ring-yellow-500 focus:border-yellow-500">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Right Column --}}
                <div class="lg:col-span-1 space-y-8">
                    <div class="bg-gray-800 rounded-lg shadow-lg border border-gray-700 overflow-hidden sticky top-6">
                        <div class="px-6 py-4 border-b border-gray-700 bg-gray-800/50 flex items-center gap-2">
                            <i class="fas fa-camera text-purple-400"></i>
                            <h3 class="text-lg font-medium text-gray-100">รูปภาพหลัก</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            {{-- Logo --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">โลโก้ (Logo)</label>
                                @if (!empty($settings['site_logo']))
                                    <div
                                        class="mb-3 p-4 border border-dashed border-gray-600 rounded bg-gray-900/50 text-center">
                                        <img src="{{ Storage::url($settings['site_logo']) }}" alt="Logo"
                                            class="h-16 mx-auto object-contain">
                                    </div>
                                @endif
                                <input type="file" name="site_logo"
                                    class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-gray-200 hover:file:bg-gray-600">
                            </div>

                            <hr class="border-gray-700">

                            {{-- Cover Image --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">รูปปก (Cover)</label>
                                @if (!empty($settings['site_cover_image']))
                                    <div class="mb-3 rounded border border-gray-600 overflow-hidden">
                                        <img src="{{ Storage::url($settings['site_cover_image']) }}" alt="Cover"
                                            class="w-full h-32 object-cover">
                                    </div>
                                @endif
                                <input type="file" name="site_cover_image"
                                    class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-gray-200 hover:file:bg-gray-600">
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-gray-900/30 border-t border-gray-700">
                            <button type="submit"
                                class="w-full bg-emerald-600 text-white font-bold py-3 px-4 rounded shadow-lg hover:bg-emerald-700 transition duration-150 flex items-center justify-center gap-2">
                                <i class="fas fa-save"></i> บันทึกการตั้งค่า
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const builders = {
            'hero_slider': {
                fields: ['image', 'title', 'description']
            },
            'reasons': {
                fields: ['image', 'title', 'description']
            },
            'service': {
                fields: ['icon', 'text']
            }
        };

        function getItems(key) {
            try {
                const val = document.getElementById(key + '_input').value;
                return val ? JSON.parse(val.startsWith('"') ? JSON.parse(val) : val) : [];
            } catch (e) {
                return [];
            }
        }

        function saveItems(key, items) {
            document.getElementById(key + '_input').value = JSON.stringify(items);
            renderList(key);
        }

        function renderList(key) {
            const listEl = document.getElementById(key + '_list');
            const items = getItems(key);
            listEl.innerHTML = '';

            if (items.length === 0) {
                listEl.innerHTML =
                    '<div class="text-center text-gray-500 py-4 text-sm bg-gray-900/50 rounded border border-dashed border-gray-700">ยังไม่มีรายการ</div>';
                return;
            }

            items.forEach((item, index) => {
                const fieldsHtml = builders[key].fields.map(field => {
                    let val = item[field] || '';
                    let placeholder = field.charAt(0).toUpperCase() + field.slice(1);
                    // Dark theme inputs
                    return `<input type="text" value="${val.replace(/"/g, '&quot;')}" onchange="updateItem('${key}', ${index}, '${field}', this.value)" class="flex-1 min-w-0 block w-full px-3 py-1.5 rounded text-sm bg-gray-700 border-gray-600 text-gray-100 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400" placeholder="${placeholder}">`;
                }).join('');

                const row = document.createElement('div');
                // Dark theme row item
                row.className =
                    'flex gap-2 items-center bg-gray-900/50 p-2 rounded border border-gray-700 animate-fade-in';
                row.innerHTML = `
                <div class="grid grid-cols-1 sm:grid-cols-${builders[key].fields.length} gap-2 flex-grow">
                    ${fieldsHtml}
                </div>
                <button type="button" onclick="removeItem('${key}', ${index})" class="text-red-400 hover:text-red-300 p-2 rounded hover:bg-red-900/30 transition"><i class="fas fa-trash"></i></button>
            `;
                listEl.appendChild(row);
            });
        }

        function addItem(key) {
            const newItem = {};
            let valid = false;

            const idMap = {
                'hero_slider': {
                    image: 'new_hero_img',
                    title: 'new_hero_title',
                    description: 'new_hero_desc'
                },
                'reasons': {
                    image: 'new_reason_img',
                    title: 'new_reason_title',
                    description: 'new_reason_desc'
                },
                'service': {
                    icon: 'new_service_icon',
                    text: 'new_service_text'
                }
            };

            builders[key].fields.forEach(field => {
                const inputId = idMap[key][field];
                const el = document.getElementById(inputId);
                const val = el.value.trim();
                newItem[field] = val;
                if (val) valid = true;
                el.value = '';
            });

            if (valid) {
                const items = getItems(key);
                items.push(newItem);
                saveItems(key, items);
            } else {
                alert('กรุณากรอกข้อมูลอย่างน้อย 1 ช่อง');
            }
        }

        function updateItem(key, index, field, value) {
            const items = getItems(key);
            items[index][field] = value;
            document.getElementById(key + '_input').value = JSON.stringify(items);
        }

        function removeItem(key, index) {
            if (!confirm('ต้องการลบรายการนี้ใช่หรือไม่?')) return;
            const items = getItems(key);
            items.splice(index, 1);
            saveItems(key, items);
        }

        document.addEventListener('DOMContentLoaded', () => {
            Object.keys(builders).forEach(key => renderList(key));
        });
    </script>

    <style>
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection
