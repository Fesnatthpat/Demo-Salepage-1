<?php

namespace App\Console\Commands;

use App\Models\BirthdayPromotion;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendBirthdayPromotions extends Command
{
    protected $signature = 'botnoi:send-birthday';

    protected $description = 'ส่งโปรโมชันวันเกิดแบบ Flex Message ผ่าน LINE OA โดยใช้แคมเปญที่เปิดใช้งานอยู่';

    public function handle()
    {
        $today = now();
        $this->info("🔍 กำลังค้นหาลูกค้าที่เกิดวันที่: {$today->format('d/m')}");

        // 1. ดึงแคมเปญที่เปิดใช้งานอยู่
        $activeCampaign = BirthdayPromotion::where('is_active', true)->first();

        if (! $activeCampaign) {
            $this->error('❌ ไม่พบแคมเปญวันเกิดที่เปิดใช้งานอยู่ในระบบ');

            return;
        }

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

        $title = $activeCampaign->title ?? 'HAPPY BIRTHDAY';
        $message = $activeCampaign->message;
        $link = $activeCampaign->link_url ?? config('app.url');

        if (empty($link)) {
            $link = config('app.url');
        }
        if (! str_starts_with($link, 'http')) {
            $link = 'https://'.ltrim($link, '/');
        }

        $imagePath = $activeCampaign->image_path;
        $appUrl = config('app.url');

        // ตรวจสอบว่ามีรูปภาพหรือไม่
        if (! $imagePath) {
            $this->error('❌ แคมเปญไม่มีรูปภาพประกอบ: กรุณาอัปโหลดรูปภาพในหน้า Admin ก่อนส่งครับ');

            return;
        }

        // สร้าง Full Image URL
        $imageUrl = asset('storage/'.$imagePath);

        // ★★★ แก้ไขจุดสำคัญ: LINE บังคับต้องเป็น HTTPS เท่านั้น ★★★
        if (str_starts_with($imageUrl, 'http://')) {
            $imageUrl = str_replace('http://', 'https://', $imageUrl);
        }

        // สำหรับ LINE URL ต้องเข้าถึงได้จากภายนอก
        if (str_contains($imageUrl, 'localhost') || str_contains($imageUrl, '127.0.0.1')) {
            $this->warn('⚠️ คำเตือน: คุณกำลังรันบน Localhost รูปภาพอาจไม่แสดงใน LINE (LINE ต้องการ URL ที่เข้าถึงได้จากอินเทอร์เน็ต)');
        }

        foreach ($users as $user) {
            $flexMessage = [
                'type' => 'flex',
                'altText' => "🎂 สุขสันต์วันเกิดครับคุณ {$user->name} มีของขวัญมาให้!",
                'contents' => [
                    'type' => 'bubble',
                    'hero' => [
                        'type' => 'image',
                        'url' => $imageUrl,
                        'size' => 'full',
                        'aspectRatio' => '20:13',
                        'aspectMode' => 'cover',
                    ],
                    'body' => [
                        'type' => 'box',
                        'layout' => 'vertical',
                        'contents' => [
                            [
                                'type' => 'text',
                                'text' => $title,
                                'weight' => 'bold',
                                'color' => '#ff3377',
                                'size' => 'sm',
                            ],
                            [
                                'type' => 'text',
                                'text' => "คุณ {$user->name}",
                                'weight' => 'bold',
                                'size' => 'xl',
                                'margin' => 'md',
                            ],
                            [
                                'type' => 'text',
                                'text' => $message,
                                'size' => 'sm',
                                'color' => '#666666',
                                'wrap' => true,
                                'margin' => 'md',
                            ],
                        ],
                    ],
                    'footer' => [
                        'type' => 'box',
                        'layout' => 'vertical',
                        'contents' => [
                            [
                                'type' => 'button',
                                'action' => [
                                    'type' => 'uri',
                                    'label' => '🎁 กดรับสิทธิ์เลย',
                                    'uri' => $link,
                                ],
                                'style' => 'primary',
                                'color' => '#ff3344',
                            ],
                        ],
                    ],
                ],
            ];

            $response = Http::withToken($token)->post('https://api.line.me/v2/bot/message/push', [
                'to' => $user->line_id,
                'messages' => [
                    $flexMessage,
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
