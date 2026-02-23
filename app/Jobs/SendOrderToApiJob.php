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

class SendOrderToApiJob // 👈 ลบ implements ShouldQueue ออกชั่วคราวเพื่อให้ dd() แสดงผลบนหน้าเว็บได้
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
        $apiUrl = 'https://demo.kawinbrothers.com/api/v1/create-order.php';
        $apiToken = 'cFVubW9zWUJyU3R4bDZhcXNiYjo1c21nNHJ1T1VDOVYzaHRabDNhdFNxVTcwN0RQVmpYUXUy';

        // 1. ดึงข้อมูล SKU สินค้าทั้งหมดในครั้งเดียว
        $productIds = $this->order->details->pluck('pd_id')->toArray();
        $products = DB::table('product_salepage')
            ->whereIn('pd_sp_id', $productIds)
            ->get()
            ->keyBy('pd_sp_id');

        $apiItems = [];
        foreach ($this->order->details as $detail) {

            // ดึงข้อมูลสินค้า
            $product = $products->get($detail->pd_id);

            $productSku = 'UNKNOWN';
            if ($product) {
                $productSku = $product->pd_sp_SKU ?? $product->pd_sp_code ?? 'UNKNOWN';
            }

            // 🌟 พระเอกอยู่ตรงนี้: บังคับเติม [1] ต่อท้าย SKU เสมอ เพื่อให้เหมือน Postman ที่เทสผ่าน!
            $productSku .= '[1]';

            $apiItems[] = [
                // ส่งรหัสที่เติม [1] แล้วไปให้ CRM
                'product_sku' => (string) $productSku,

                'price_per_item' => (float) $detail->ordd_price,

                // 🌟 บังคับจำนวนสินค้าเป็น 1 เสมอ เพื่อทดสอบโดยไม่ต้องสนหน้าเว็บ
                'quantity' => 1,
            ];
        }

        // ป้องกัน Error วันที่
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

                // 🌟 สมมุติข้อมูลขนส่งไปเลย เพื่อทดสอบ
                'shipping_method' => 'Shopee SPX Express',

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
                ->asJson()
                ->post($apiUrl, $payload);

            Log::channel('daily')->debug('CRM API Debug:', [
                'status' => $response->status(),
                'response' => $response->json(),
                'payload' => $payload,
            ]);

            // 🌟 แทรก dd() ตรงนี้ เพื่อดูผลลัพธ์ทันทีบนหน้าเว็บ
            // dd([
            //     'status' => $response->status(),
            //     'crm_response' => $response->json(),
            //     'sent_payload' => $payload,
            //     'message' => 'ตรวจสอบข้อมูลได้ที่นี่เลยครับคู่หู!',
            // ]);

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
            }
        } catch (\Exception $e) {
            Log::channel('daily')->critical('💥 Exception when sending order to CRM: '.$this->order->ord_code, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
