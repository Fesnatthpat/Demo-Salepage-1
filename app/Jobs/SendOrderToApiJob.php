<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendOrderToApiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order->load('details.product');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $apiUrl = 'https://demo.kawinbrothers.com/api/v1/create-order.php'; // Updated API URL

        // The API expects an array containing one or more order objects.
        // We construct a single order object first, then wrap it in an array.
        $payload_object = [
            'address' => $this->order->shipping_address,
            'amphure' => '', // Requires granular address data not currently in Order model
            'channel_name' => 'Sale Page', // Static value based on sample
            'customer_name' => $this->order->shipping_name,
            'district' => '', // Requires granular address data
            'net_amount' => (float) $this->order->net_amount,
            'order_date' => $this->order->ord_date->format('Y-m-d H:i:s'),
            'order_id' => $this->order->ord_code,
            'tracking_number' => '', // Placeholder, or needs to come from a tracking system
            'payment_date' => $this->order->ord_date->format('Y-m-d H:i:s'), // Placeholder, use order date for now
            'payment_method' => 'Prepaid', // Static value based on sample, should come from actual payment method
            'phone_number1' => $this->order->shipping_phone,
            'phone_number2' => '', // Not available in current Order model
            'postal_code' => '', // Requires granular address data
            'province' => '', // Requires granular address data
            'shipping_method' => 'Standard Shipping', // Static value based on sample, should come from actual shipping method
            'social_name' => '', // Not available in current Order model
            'store_name' => 'Sale Page', // Static value based on sample
            'order_upload_status' => '', // Not available in current Order model
            'comp_id' => 1, // Static value based on sample, should be configurable
            'items' => $this->order->details->map(function ($detail) {
                // Construct product_sku with option if present, based on sample format
                $productSku = $detail->product->pd_sp_SKU ?? $detail->product->pd_sp_code;
                if ($detail->option_name) {
                    $productSku .= '[' . $detail->option_name . ']';
                }
                return [
                    'product_sku' => $productSku,
                    'price_per_item' => (float) $detail->ordd_price,
                    'quantity' => (int) $detail->ordd_count,
                ];
            })->toArray(),
        ];

        // The API expects an array of orders, so wrap the single order object in an array
        $payload = [$payload_object];

        Log::info('Sending order to external API', ['order_code' => $this->order->ord_code, 'payload' => $payload]);

        try {
            $response = Http::asJson()->post($apiUrl, $payload);

            if ($response->successful()) {
                Log::info('Successfully sent order to external API', [
                    'order_code' => $this->order->ord_code,
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
            } else {
                Log::error('Failed to send order to external API', [
                    'order_code' => $this->order->ord_code,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::critical('Exception when sending order to external API', [
                'order_code' => $this->order->ord_code,
                'error' => $e->getMessage()
            ]);
        }
    }
}
