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
use Illuminate\Support\Facades\Log;

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
        $this->order = $order->load('details');
        $this->addressData = $addressData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('--- Start CRM Sending Process: '.$this->order->ord_code.' ---');

        if (empty($this->order->slip_path)) {
            Log::warning('⏳ ระงับการส่ง CRM: ออเดอร์ '.$this->order->ord_code.' ยังไม่ได้แนบสลิป');
            return;
        }

        $apiUrl = env('CRM_API_URL');
        $apiToken = env('CRM_API_TOKEN');

        if (empty($apiUrl)) {
            Log::error('❌ CRM Error: CRM_API_URL is not defined in .env');
            return;
        }

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
                'quantity' => (int) $detail->ordd_count,
            ];
        }

        $orderDateFormatted = Carbon::parse($this->order->ord_date)->format('Y-m-d H:i:s');
        $realName = trim($this->order->shipping_name);
        $nameLength = mb_strlen($realName, 'UTF-8');

        if ($nameLength > 2) {
            $firstChar = mb_substr($realName, 0, 1, 'UTF-8');
            $lastChar = mb_substr($realName, -1, 1, 'UTF-8');
            $maskedName = $firstChar.'******'.$lastChar;
        } elseif ($nameLength > 0) {
            $firstChar = mb_substr($realName, 0, 1, 'UTF-8');
            $maskedName = $firstChar.'******';
        } else {
            $maskedName = 'ลูกค้าทั่วไป';
        }

        $payload = [
            [
                'address' => $this->order->shipping_address,
                'amphure' => $this->addressData['amphure'] ?? '',
                'channel_name' => 'Sale Page',
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
                'shipping_method' => 'Standard Delivery',
                'social_name' => '',
                'store_name' => 'Sale Page',
                'order_upload_status' => '',
                'comp_id' => 1,
                'items' => $apiItems,
            ],
        ];

        Log::info('Sending order to CRM API: '.$this->order->ord_code, ['payload' => $payload]);

        try {
            $response = Http::withoutVerifying()
                ->withToken($apiToken)
                ->timeout(30)
                ->asJson()
                ->post($apiUrl, $payload);

            Log::debug('CRM API Debug:', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            if ($response->successful()) {
                Log::info('✅ Successfully sent order to CRM: '.$this->order->ord_code);
            } else {
                Log::error('❌ Failed to send order to CRM: '.$this->order->ord_code, [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::critical('❌ Exception when sending order to CRM: '.$this->order->ord_code, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
