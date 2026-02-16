<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Faq::create([
            'question' => 'สั่งซื้อสินค้าอย่างไร?',
            'answer' => 'คุณสามารถเลือกสินค้าที่ต้องการและเพิ่มลงในตะกร้า จากนั้นไปที่หน้าชำระเงินและทำตามขั้นตอนได้เลยครับ',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        \App\Models\Faq::create([
            'question' => 'มีวิธีการชำระเงินแบบไหนบ้าง?',
            'answer' => 'เรารองรับการชำระเงินผ่านการโอนเงินผ่านธนาคาร และ QR Code ครับ',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        \App\Models\Faq::create([
            'question' => 'ใช้เวลาจัดส่งกี่วัน?',
            'answer' => 'โดยปกติจะใช้เวลาจัดส่งในกรุงเทพฯ และปริมณฑล 1-2 วันทำการ และต่างจังหวัด 2-3 วันทำการครับ',
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }
}
