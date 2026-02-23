<?php

namespace App\Jobs;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // นำเข้า Carbon สำหรับจัดการวันที่

class SendOrderToApiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    protected $addressData;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, array $addressData = [])
    {
        // โหลดรายละเอียดออเดอร์มาด้วย
        $this->order = $order->load('details');
        $this->addressData = $addressData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 💡 คำแนะนำ: ในอนาคตควรย้าย URL กับ Token ไปไว้ในไฟล์ .env
        // เช่น env('CRM_API_URL') เป็นต้น
        $apiUrl = 'https://demo.kawinbrothers.com/api/v1/create-order.php';
        $apiToken = 'cFVubW9zWUJyU3R4bDZhcXNiYjo1c21nNHJ1T1VDOVYzaHRabDNhdFNxVTcwN0RQVmpYUXUy';

        // 1. ดึงข้อมูล SKU สินค้าทั้งหมดในครั้งเดียว (แก้ปัญหา N+1 Query)
        $productIds = $this->order->details->pluck('pd_id')->toArray();
        $products = DB::table('product_salepage')
            ->whereIn('pd_sp_id', $productIds)
            ->get()
            ->keyBy('pd_sp_id'); // จัดกลุ่มด้วย id เพื่อให้ค้นหาง่ายขึ้น

        $apiItems = [];
        foreach ($this->order->details as $detail) {

            // ดึงข้อมูลสินค้าที่ Query มารอไว้แล้ว
            $product = $products->get($detail->pd_id);

            $productSku = 'UNKNOWN';
            if ($product) {
                $productSku = $product->pd_sp_SKU ?? $product->pd_sp_code ?? 'UNKNOWN';
            }

            if ($detail->option_name) {
                $productSku .= '['.$detail->option_name.']';
            }

            $apiItems[] = [
                'product_sku' => (string) $productSku,
                'price_per_item' => (float) $detail->ordd_price,
                'quantity' => (int) $detail->ordd_count,
            ];
        }

        // ป้องกัน Error กรณี ord_date ไม่ได้ถูก Cast เป็น Datetime
        $orderDateFormatted = Carbon::parse($this->order->ord_date)->format('Y-m-d H:i:s');

        // 2. จัดรูปแบบ Payload
        $payload = [
            [
                'address' => $this->order->shipping_address,
                'amphure' => $this->addressData['amphure'] ?? '',
                'channel_name' => 'Sale Page',
                'customer_name' => $this->order->shipping_name,
                'district' => $this->addressData['district'] ?? '',
                'net_amount' => (float) $this->order->net_amount,
                'order_date' => $orderDateFormatted,
                'order_id' => $this->order->ord_code,
                'tracking_number' => '',
                'payment_date' => $orderDateFormatted,
                'payment_method' => $this->addressData['payment_method'] ?? 'Prepaid',
                'phone_number1' => $this->order->shipping_phone,
                'phone_number2' => '',
                'postal_code' => $this->addressData['postal_code'] ?? '',
                'province' => $this->addressData['province'] ?? '',
                'shipping_method' => $this->addressData['shipping_method'] ?? 'Standard Delivery',
                'social_name' => '',
                'store_name' => 'Sale Page',
                'order_upload_status' => '',
                'comp_id' => 1,
                'items' => $apiItems,
            ],
        ];

        Log::channel('daily')->info('Sending order to CRM API: '.$this->order->ord_code, ['payload' => $payload]);

        try {
            $response = Http::withoutVerifying()
                ->withToken($apiToken)
                ->timeout(30)
                ->asJson() // บังคับให้ส่งเป็น application/json
                ->post($apiUrl, $payload);

            if ($response->successful()) {
                Log::channel('daily')->info('✅ Successfully sent order to CRM: '.$this->order->ord_code, [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);
            } else {
                Log::channel('daily')->error('❌ Failed to send order to CRM: '.$this->order->ord_code, [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                // หากต้องการให้ Job นำกลับไปทำใหม่ เมื่อ API ปลายทางมีปัญหา
                // $this->release(60); // ลองใหม่ในอีก 60 วินาที
            }
        } catch (\Exception $e) {
            Log::channel('daily')->critical('💥 Exception when sending order to CRM: '.$this->order->ord_code, [
                'error' => $e->getMessage(),
            ]);

            // ถ้าระบบพังเลย (เช่น เน็ตตัด) อาจจะให้ลองทำใหม่
            // $this->release(60);
        }
    }
}
