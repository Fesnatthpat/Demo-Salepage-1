<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DhlService
{
    protected $baseUrl;
    protected $clientId;
    protected $password;

    public function __construct()
    {
        // แนะนำให้ตั้งค่าในไฟล์ .env
        $this->baseUrl = env('DHL_API_URL', 'https://api.dhlecommerce.dhl.com/rest/v2');
        $this->clientId = env('DHL_CLIENT_ID');
        $this->password = env('DHL_PASSWORD');
    }

    /**
     * ดึงข้อมูลการติดตามพัสดุ (Tracking)
     */
    public function trackShipment($trackingNumber)
    {
        // ตรวจสอบว่ามีการตั้งค่า Credentials หรือยัง
        if (empty($this->clientId) || empty($this->password)) {
            Log::warning('DHL API Credentials are missing. Please check your .env file.');
            return null;
        }

        try {
            $response = Http::withBasicAuth($this->clientId, $this->password)
                ->get("{$this->baseUrl}/Tracking", [
                    'trackingNumber' => $trackingNumber
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('DHL Tracking API Error: ' . $response->status() . ' - ' . $response->body());
        } catch (\Exception $e) {
            Log::error('DHL Tracking Connection Failed: ' . $e->getMessage());
        }

        return null;
    }
}