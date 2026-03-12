<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendBirthdayPromotions extends Command
{
    protected $signature = 'botnoi:send-birthday';

    protected $description = 'ส่งโปรโมชันวันเกิดแบบ Flex Message ผ่านบอท Kawin';

    public function handle()
    {
        $today = now();
        $this->info("🔍 กำลังค้นหาลูกค้าที่เกิดวันที่: {$today->format('d/m')}");

        $users = User::whereNotNull('line_id')
            ->whereMonth('date_of_birth', $today->month)
            ->whereDay('date_of_birth', $today->day)
            ->get();

        if ($users->isEmpty()) {
            $this->info('❌ วันนี้ไม่มีลูกค้าที่ตรงกับวันเกิดครับ');

            return;
        }

        $token = env('LINE_BOT_ACCESS_TOKEN');

        if (empty($token)) {
            $this->error('❌ ไม่พบ LINE_BOT_ACCESS_TOKEN ในไฟล์ .env ครับ');

            return;
        }

        foreach ($users as $user) {

            // ==========================================
            // โครงสร้างของ Flex Message (การ์ดวันเกิด)
            // ==========================================
            $flexMessage = [
                'type' => 'flex',
                'altText' => "🎂 สุขสันต์วันเกิดครับคุณ {$user->name} มีของขวัญมาให้!", // ข้อความที่จะโชว์ตอนแจ้งเตือน (Push Notification)
                'contents' => [
                    'type' => 'bubble',
                    // 1. ส่วนหัว: รูปภาพแบนเนอร์
                    'hero' => [
                        'type' => 'image',
                        'url' => 'https://img.freepik.com/free-vector/happy-birthday-background-with-realistic-balloons_1361-2301.jpg', // 📌 เปลี่ยนเป็น URL รูปโปรโมชันของคุณได้
                        'size' => 'full',
                        'aspectRatio' => '20:13',
                        'aspectMode' => 'cover',
                    ],
                    // 2. ส่วนตัว: ข้อความอวยพร
                    'body' => [
                        'type' => 'box',
                        'layout' => 'vertical',
                        'contents' => [
                            [
                                'type' => 'text',
                                'text' => 'HAPPY BIRTHDAY',
                                'weight' => 'bold',
                                'color' => '#ff3344',
                                'size' => 'sm',
                            ],
                            [
                                'type' => 'text',
                                'text' => "คุณ {$user->name}", // ดึงชื่อลูกค้ามาใส่ตรงนี้
                                'weight' => 'bold',
                                'size' => 'xl',
                                'margin' => 'md',
                            ],
                            [
                                'type' => 'text',
                                'text' => 'ทางเราขอมอบของขวัญพิเศษ เป็นส่วนลด 50% สำหรับการสั่งซื้อในเดือนเกิดของคุณ เพียงกดปุ่มด้านล่างนี้ครับ! 🎂🎉',
                                'size' => 'sm',
                                'color' => '#666666',
                                'wrap' => true,
                                'margin' => 'md',
                            ],
                        ],
                    ],
                    // 3. ส่วนท้าย: ปุ่มกดรับสิทธิ์
                    'footer' => [
                        'type' => 'box',
                        'layout' => 'vertical',
                        'contents' => [
                            [
                                'type' => 'button',
                                'action' => [
                                    'type' => 'uri',
                                    'label' => '🎁 กดรับสิทธิ์เลย',
                                    'uri' => 'http://127.0.0.1:8000/product/9', // 📌 เปลี่ยนเป็นลิงก์หน้าเว็บที่คุณต้องการให้ลูกค้าไป
                                ],
                                'style' => 'primary',
                                'color' => '#ff3344',
                            ],
                        ],
                    ],
                ],
            ];

            // ==========================================
            // ยิงข้อมูลไปที่ LINE API
            // ==========================================
            $response = Http::withToken($token)->post('https://api.line.me/v2/bot/message/push', [
                'to' => $user->line_id,
                'messages' => [
                    $flexMessage, // เอา Flex Message ใส่เข้าไปแทนข้อความ Text ธรรมดา
                ],
            ]);

            if ($response->successful()) {
                $this->info("✅ ส่ง Flex Message ให้คุณ {$user->name} สำเร็จ!");
            } else {
                Log::error("ส่ง LINE ให้ {$user->name} พลาด: ".$response->body());
                $this->error("❌ ส่งให้คุณ {$user->name} ไม่สำเร็จ");
            }
        }

        $this->info('🎉 ทำงานเสร็จสิ้น!');
    }
}
