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

    public function handle()
    {
        // 1. หาเวลาที่ผ่านมาแล้ว 1 นาที
        $expireTime = now()->subMinutes(1);

        // 2. ดึงออเดอร์ที่สถานะ STATUS_PENDING (รอชำระเงิน) และเวลา created_at เก่ากว่า 1 นาที
        $expiredOrders = Order::with('details')
            ->where('status_id', Order::STATUS_PENDING)
            ->where('created_at', '<=', $expireTime)
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('ไม่พบคำสั่งซื้อที่หมดเวลา');

            return;
        }

        $count = 0;

        foreach ($expiredOrders as $order) {
            DB::beginTransaction();
            try {
                // 3. เปลี่ยนสถานะออเดอร์เป็น STATUS_CANCELLED (ยกเลิก)
                $order->status_id = Order::STATUS_CANCELLED;
                $order->save();

                // 4. วนลูปคืนค่าสต็อก
                foreach ($order->details as $detail) {
                    $stockRecord = StockProduct::where('pd_sp_id', $detail->pd_id)
                        ->where('option_id', $detail->option_id)
                        ->lockForUpdate() // ล็อคป้องกันการชนกันตอนคืนสต็อก
                        ->first();

                    if ($stockRecord) {
                        // ลดยอดจอง (reserved_qty) คืนระบบ
                        // เช็คไม่ให้ยอดจองติดลบ (ป้องกันข้อผิดพลาด)
                        $reserveToSubtract = min($stockRecord->reserved_qty, $detail->ordd_count);
                        $stockRecord->decrement('reserved_qty', $reserveToSubtract);
                    }
                }

                DB::commit();
                $count++;
                $this->info("ยกเลิกออเดอร์ {$order->ord_code} และคืนสต็อกสำเร็จ");

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("เกิดข้อผิดพลาดในการยกเลิกออเดอร์ {$order->ord_code}: ".$e->getMessage());
                $this->error("ข้อผิดพลาดออเดอร์ {$order->ord_code}: ".$e->getMessage());
            }
        }

        $this->info("เสร็จสิ้น! ทำการยกเลิกไปทั้งหมด {$count} รายการ");
    }
}
