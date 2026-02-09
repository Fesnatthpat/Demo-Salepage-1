<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HomepageContent; // Don't forget to import the model

class HomepageContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        // ★★★ HERO SECTION (สไลด์หลัก) ★★★
        $heroSlides = [
            ['image' => 'images/th-1.png', 'link' => '/allproducts'],
            ['image' => 'images/th-2.png', 'link' => '/allproducts'],
            ['image' => 'images/th-3.png', 'link' => '/allproducts'],
            ['image' => 'images/th-4.png', 'link' => '/allproducts'],
            ['image' => 'images/th-5.png', 'link' => '/allproducts'],
        ];

        foreach ($heroSlides as $index => $slide) {
            HomepageContent::create([
                'section_name' => 'hero_slides',
                'item_key' => 'slide_' . ($index + 1),
                'type' => 'collection',
                'data' => [
                    'image' => $slide['image'],
                    'link' => $slide['link'],
                ],
                'order' => $index + 1,
            ]);
        }

        // ★★★ ส่วนข้อมูลแพ้อาหาร ★★★
        HomepageContent::create([
            'section_name' => 'allergen_info',
            'item_key' => 'main_image',
            'type' => 'image',
            'value' => 'images/image_27e610.png',
        ]);

        // ★★★ 6 REASONS SECTION ★★★
        HomepageContent::create([
            'section_name' => '6_reasons',
            'item_key' => 'main_title',
            'type' => 'text',
            'value' => '6 เหตุผลทำไมต้องเลือกเรา',
        ]);

        $reasons = [
            [
                'title' => 'เรารู้จริง',
                'description' => 'เรารู้ว่าคุณต้องการอะไร กังวลสิ่งไหน เราจึงตั้งใจส่งมอบสิ่งที่ดีที่สุดให้กับคุณและคนที่คุณรัก',
                'icon' => '<svg class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>',
            ],
            [
                'title' => 'พิถีพิถัน',
                'description' => 'เราใส่ใจทุกรายละเอียดอย่างแท้จริง ตั้งแต่การคัดเลือกวัตถุดิบคุณภาพสูง ผ่านกระบวนการผลิตที่มีมาตรฐานระดับสากล',
                'icon' => '<svg class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>',
            ],
            [
                'title' => 'ทุกเพศ ทุกวัย ทุกสไตล์',
                'description' => 'อร่อยแบบไม่จำกัด ด้วยผลิตภัณฑ์ที่มีหลากหลายชนิดหลายสูตร เพื่อตอบสนองต่อความต้องการที่หลากหลาย',
                'icon' => '<svg class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>',
            ],
            [
                'title' => 'คุณค่าที่มากกว่า',
                'description' => 'มากกว่าเครื่องปรุงเกาหลีคือสุขภาพ และอารมณ์ที่ดีของคุณและคนที่คุณรัก',
                'icon' => '<svg class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>',
            ],
            [
                'title' => 'ช่วงเวลาแห่งความสุขร่วมกัน',
                'description' => 'ให้สินค้าของเราเป็นสื่อกลาง สานสัมพันธ์ระหว่างคุณและคนที่คุณรัก เพื่อสร้างเวลาแห่งความสุขร่วมกัน',
                'icon' => '<svg class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>',
            ],
            [
                'title' => 'THE TRUE HAPPINESS',
                'description' => 'ที่สุด คือ ความสุขของคุณที่คุณรัก เพราะเรารู้ว่านั่นคือ ของสุขของคุณเช่นกัน',
                'icon' => '<svg class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
            ],
        ];

        foreach ($reasons as $index => $reason) {
            HomepageContent::create([
                'section_name' => '6_reasons',
                'item_key' => 'reason_' . ($index + 1),
                'type' => 'collection',
                'data' => $reason,
                'order' => $index + 1,
            ]);
        }

        // ★★★ สไลด์ตัวที่ 2 ★★★
        $secondSlides = [
            ['image' => 'images/th-a.png'],
            ['image' => 'images/th-b.png'],
            ['image' => 'images/th-c.png'],
        ];

        foreach ($secondSlides as $index => $slide) {
            HomepageContent::create([
                'section_name' => 'second_slides',
                'item_key' => 'slide_' . ($index + 1),
                'type' => 'image',
                'value' => $slide['image'],
                'order' => $index + 1,
            ]);
        }

        // SERVICE BAR
        $serviceBarItems = [
            [
                'icon' => '<svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                'text' => 'สูตรเด็ดต้นตำรับ',
            ],
            [
                'icon' => '<svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>',
                'text' => 'ส่งไว ทันใจ',
            ],
            [
                'icon' => '<svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>',
                'text' => 'ชำระเงินปลอดภัย',
            ],
            [
                'icon' => '<svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>',
                'text' => 'ทำด้วยใจทุกขั้นตอน',
            ],
        ];

        foreach ($serviceBarItems as $index => $item) {
            HomepageContent::create([
                'section_name' => 'service_bar',
                'item_key' => 'item_' . ($index + 1),
                'type' => 'collection',
                'data' => $item,
                'order' => $index + 1,
            ]);
        }

        // CATEGORY MENU SECTION
        $menuItems = [
            ['name' => 'กิมจิ', 'image' => 'images/menu-kimchi.png', 'link_prefix' => '/allproducts?category='],
            ['name' => 'ซอส<br>เกาหลี', 'image' => 'images/menu-korean-sauce.png', 'link_prefix' => '/allproducts?category='],
            ['name' => 'combo<br>set', 'image' => 'images/menu-combo.png', 'link_prefix' => '/allproducts?category='],
            ['name' => 'น้ำดอง<br>ผักดอง', 'image' => 'images/menu-pickle.png', 'link_prefix' => '/allproducts?category='],
            ['name' => 'เครื่องปรุง<br>เกาหลี', 'image' => 'images/menu-korean-seasoning.png', 'link_prefix' => '/allproducts?category='],
            ['name' => 'แป้ง/ข้าว/<br>เส้น', 'image' => 'images/menu-flour.png', 'link_prefix' => '/allproducts?category='],
            ['name' => 'สาหร่าย', 'image' => 'images/menu-seaweed.png', 'link_prefix' => '/allproducts?category='],
            ['name' => 'เครื่อง<br>ครัว', 'image' => 'images/menu-kitchenware.png', 'link_prefix' => '/allproducts?category='],
            ['name' => 'ซอส<br>ญี่ปุ่น', 'image' => 'images/menu-japan-sauce.png', 'link_prefix' => '/allproducts?category='],
            ['name' => 'เครื่องปรุง<br>ญี่ปุ่น', 'image' => 'images/menu-japan-seasoning.png', 'link_prefix' => '/allproducts?category='],
        ];

        foreach ($menuItems as $index => $menu) {
            HomepageContent::create([
                'section_name' => 'category_menu',
                'item_key' => 'item_' . ($index + 1),
                'type' => 'collection',
                'data' => [
                    'name' => $menu['name'],
                    'image' => $menu['image'],
                    'link' => $menu['link_prefix'] . strip_tags($menu['name']), // Generate link
                ],
                'order' => $index + 1,
            ]);
        }

        // PRODUCTS SECTION - Recommended Badge and Title
        HomepageContent::create([
            'section_name' => 'products_section',
            'item_key' => 'recommended_badge_text',
            'type' => 'text',
            'value' => 'Recommended',
        ]);

        HomepageContent::create([
            'section_name' => 'products_section',
            'item_key' => 'main_title',
            'type' => 'text',
            'value' => 'เมนูแนะนำ',
        ]);

        HomepageContent::create([
            'section_name' => 'products_section',
            'item_key' => 'main_title_span',
            'type' => 'text',
            'value' => 'ต้องลอง!',
        ]);

        HomepageContent::create([
            'section_name' => 'products_section',
            'item_key' => 'view_all_button_text',
            'type' => 'text',
            'value' => 'ดูทั้งหมด',
        ]);
    }
}


