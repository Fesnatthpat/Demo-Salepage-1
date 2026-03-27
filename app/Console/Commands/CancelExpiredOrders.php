<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\StockProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelExpiredOrders extends Command
{
    // ชื่อคำสั่งเวลาเราเรียกใช้ผ่าน Terminal
    protected $signature = 'orders:cancel-expired';

    // คำอธิบายคำสั่ง
    protected $description = 'ยกเลิกคำสั่งซื้อที่หมดเวลา 15 นาที และคืนค่า reserved_qty กลับสู่ระบบ';

    public function handle(\App\Services\OrderService $orderService)
    {
        // 1. หาออเดอร์ที่หมดเวลา (เกิน 15 นาทีหลังจากอัปเดตล่าสุด)
        $expireTime = now()->subMinutes(1);

        // 2. ดึงออเดอร์ที่สถานะ STATUS_PENDING (รอชำระเงิน)
        $expiredOrders = Order::where('status_id', Order::STATUS_PENDING)
            ->where('updated_at', '<=', $expireTime)
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('ไม่พบคำสั่งซื้อที่หมดเวลา');

            return;
        }

        $count = 0;

        foreach ($expiredOrders as $order) {
            try {
                $orderService->cancelOrder($order);
                $count++;
                $this->info("ยกเลิกออเดอร์ {$order->ord_code} และคืนสต็อกสำเร็จ");

            } catch (\Exception $e) {
                Log::error("เกิดข้อผิดพลาดในการยกเลิกออเดอร์ {$order->ord_code}: ".$e->getMessage());
                $this->error("ข้อผิดพลาดออเดอร์ {$order->ord_code}: ".$e->getMessage());
            }
        }

        $this->info("เสร็จสิ้น! ทำการยกเลิกไปทั้งหมด {$count} รายการ");
    }
}
