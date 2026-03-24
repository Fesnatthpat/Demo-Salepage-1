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
        // 🌟 1. ด่านตรวจสลิป: ตรวจสอบก่อนเลยว่ามีการแนบสลิปมาหรือไม่
        if (empty($this->order->slip_path)) {
            // ถ้าไม่มีสลิป ให้บันทึก Log แจ้งเตือนไว้ แล้วหยุดการทำงานทันที
            Log::channel('daily')->info('⏳ ระงับการส่ง CRM: ออเดอร์ '.$this->order->ord_code.' ยังไม่ได้แนบสลิป');

            return;
        }

        // ดึงข้อมูล URL และ Token มาจากไฟล์ .env
        $apiUrl = env('CRM_API_URL');
        $apiToken = env('CRM_API_TOKEN');

        // 2. ดึงข้อมูล SKU สินค้าทั้งหมดในครั้งเดียว
        $productIds = $this->order->details->pluck('pd_id')->toArray();
        $optionIds = $this->order->details->pluck('option_id')->filter()->toArray();

        $products = DB::table('product_salepage')
            ->whereIn('pd_sp_id', $productIds)
            ->get()
            ->keyBy('pd_sp_id');

        $options = DB::table('product_options')
            ->whereIn('option_id', $optionIds)
            ->get()
            ->keyBy('option_id');

        $apiItems = [];
        foreach ($this->order->details as $detail) {

            // ดึงข้อมูลสินค้า
            $product = $products->get($detail->pd_id);
            $option = $detail->option_id ? $options->get($detail->option_id) : null;

            $productSku = 'UNKNOWN';
            if ($option && $option->option_SKU) {
                $productSku = $option->option_SKU;
            } elseif ($product) {
                $productSku = $product->pd_sp_SKU ?? $product->pd_sp_code ?? 'UNKNOWN';
            }

            $apiItems[] = [
                'product_sku' => (string) $productSku,

                'price_per_item' => (float) $detail->ordd_price,

                // ใช้จำนวนจริงจากออเดอร์
                'quantity' => (int) $detail->ordd_count,
            ];
        }

        // ป้องกัน Error วันที่
        $orderDateFormatted = Carbon::parse($this->order->ord_date)->format('Y-m-d H:i:s');

        // 🌟 ระบบเซ็นเซอร์ชื่อลูกค้า (Masking Name)
        $realName = trim($this->order->shipping_name);
        $nameLength = mb_strlen($realName, 'UTF-8');

        if ($nameLength > 2) {
            // ดึงตัวอักษรแรก และ ตัวอักษรสุดท้าย คั่นด้วย ******
            $firstChar = mb_substr($realName, 0, 1, 'UTF-8');
            $lastChar = mb_substr($realName, -1, 1, 'UTF-8');
            $maskedName = $firstChar.'******'.$lastChar;
        } elseif ($nameLength > 0) {
            // ถ้าชื่อสั้นมาก ให้เอาดาวต่อท้ายเลย
            $firstChar = mb_substr($realName, 0, 1, 'UTF-8');
            $maskedName = $firstChar.'******';
        } else {
            $maskedName = 'ลูกค้าทั่วไป';
        }

        // 3. จัดรูปแบบ Payload
        $payload = [
            [
                'address' => $this->order->shipping_address,
                'amphure' => $this->addressData['amphure'] ?? '',
                'channel_name' => 'Sale Page',

                // ✅ ส่งชื่อที่ถูกเซ็นเซอร์ไปให้ CRM
                'customer_name' => $maskedName,

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

            // 🌟 แทรก dd() ตรงนี้ เพื่อดูผลลัพธ์ทันทีบนหน้าเว็บ (เอาคอมเมนต์ออกถ้าอยากให้โชว์จอดำ)
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
                Log::channel('daily')->error('Failed to send order to CRM: '.$this->order->ord_code, [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::channel('daily')->critical('Exception when sending order to CRM: '.$this->order->ord_code, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
    