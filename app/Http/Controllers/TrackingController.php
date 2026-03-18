<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrackingController extends Controller
{
    /**
     * ฟังก์ชันหลักสำหรับแสดงหน้าติดตามพัสดุและค้นหาข้อมูล
     */
    public function index(Request $request)
    {
        $trackingData = null;
        $searchValue = trim($request->search ?? $request->order_code);

        if ($searchValue) {
            // เพิ่ม ->latest() เพื่อดึงออเดอร์ล่าสุด กรณีค้นหาด้วยเบอร์โทรที่มีหลายออเดอร์
            $order = Order::where('ord_code', $searchValue)
                ->orWhere('tracking_number', $searchValue)
                ->orWhere('shipping_phone', $searchValue)
                ->latest()
                ->first();

            $searchCode = $searchValue;
            if ($order) {
                // เอารหัส tracking หรือ ord_code ล่าสุดไปค้นหาใน API
                $searchCode = $order->tracking_number ?? $order->ord_code;
            }

            try {

                $apiToken = env('CRM_API_TOKEN');

                $response = Http::withoutVerifying()
                    ->withToken($apiToken)
                    ->timeout(15)
                    ->asJson()
                    ->post('https://test.kawinbrothers.com/api/v1/get-tracking.php', [
                        'keyword' => $searchCode,
                    ]);

                if ($response->successful()) {
                    $responseData = $response->json();

                    if (isset($responseData['code']) && $responseData['code'] == 200) {
                        $trackingResults = [];

                        if (isset($responseData['data_list_order'])) {
                            foreach ($responseData['data_list_order'] as $customer) {
                                foreach ($customer['order'] as $item) {
                                    $result = null;

                                    if (! empty($item['link'])) {
                                        $result = [
                                            'is_external' => true,
                                            'external_url' => $item['link'],
                                            'carrier_name' => $item['logistics_name'] ?? 'ขนส่งพาร์ทเนอร์',
                                            'tracking_number' => $item['od_ref'] ?? $item['od_code'],
                                            'order_code' => $item['od_code'],
                                            'order_date' => $item['od_date'] ?? '-',
                                        ];
                                    } elseif (! empty($item['data']) && is_array($item['data'])) {
                                        $shipmentData = $item['data'][0] ?? [];
                                        $eventsData = $shipmentData['events'] ?? [];

                                        // 🔴 แปลภาษา Status ในลูปของเหตุการณ์ (Events)
                                        foreach ($eventsData as $key => $event) {
                                            $eventsData[$key]['description'] = $this->translateStatus($event['description'] ?? '');
                                        }

                                        // ดึงสถานะล่าสุด (ก้อนแรกของ events ที่แปลแล้ว)
                                        $latestStatus = (count($eventsData) > 0) ? $eventsData[0]['description'] : 'อยู่ระหว่างการจัดส่ง';

                                        $result = [
                                            'is_external' => false,
                                            'carrier_name' => $item['logistics_name'] ?? ($shipmentData['shippingService']['productName'] ?? 'Internal Service'),
                                            'tracking_number' => $item['od_ref'] ?? ($shipmentData['trackingID'] ?? $item['od_code']),
                                            'order_code' => $item['od_code'],
                                            'status_text' => $latestStatus,
                                            'order_date' => $item['od_date'] ?? '-',
                                            'timeline_data' => $eventsData,
                                        ];
                                    } else {
                                        $result = [
                                            'is_external' => false,
                                            'carrier_name' => $item['logistics_name'] ?? 'Internal Service',
                                            'tracking_number' => $item['od_ref'] ?? $item['od_code'],
                                            'order_code' => $item['od_code'],
                                            'status_text' => 'กำลังเตรียมการจัดส่ง',
                                            'order_date' => $item['od_date'] ?? '-',
                                            'timeline_data' => [
                                                [
                                                    'description' => 'รับคำสั่งซื้อแล้ว',
                                                    'dateTime' => $item['od_date'] ?? Carbon::now()->format('Y-m-d'),
                                                    'address' => null,
                                                    'is_system_generated' => true,
                                                ],
                                            ],
                                        ];
                                    }

                                    if ($result) {
                                        $trackingResults[] = $result;
                                    }
                                    }
                                    }
                                    }

                                    if (count($trackingResults) > 0) {
                                    $trackingData = $trackingResults;
                                    } else {
                                    return back()->with('error', 'ไม่พบข้อมูลการจัดส่งสำหรับรหัส: '.$searchValue);
                                    }
                                    } else {
                                    return back()->with('error', 'ไม่พบรายการคำสั่งซื้อในระบบ CRM');
                                    }
                                    } else {
                                    Log::error('Tracking API Failed');

                                    return back()->with('error', 'ระบบติดตามพัสดุขัดข้อง');
                                    }
                                    } catch (\Exception $e) {
                                    Log::error('Tracking API Error: '.$e->getMessage());

                                    return back()->with('error', 'ไม่สามารถเชื่อมต่อระบบติดตามพัสดุได้ในขณะนี้');
                                    }
                                    }

                                    return view('ordertracking', compact('trackingData'));
                                    }

                                    /**
                                    * ดิกชันนารี แปลข้อความสถานะการจัดส่ง
                                    */
                                    private function translateStatus($text)
                                    {
                                    $dictionary = [
                                    'Successfully delivered' => 'พัสดุจัดส่งสำเร็จ',
                                    'Available for Delivery' => 'พัสดุเตรียมนำจ่าย',
                                    'Out for Delivery' => 'พนักงานกำลังนำจ่ายพัสดุ',
                                    'Processed at delivery facility' => 'ประมวลผลที่ศูนย์กระจายสินค้าปลายทาง',
                                    'Arrived at facility' => 'พัสดุถึงศูนย์คัดแยก',
                                    'Departed from facility' => 'พัสดุออกจากศูนย์คัดแยก',
                                    'Sorted to delivery facility' => 'คัดแยกเพื่อส่งไปยังศูนย์กระจายสินค้า',
                                    'Processed at facility' => 'ประมวลผลพัสดุเรียบร้อย',
                                    'Arrival at Facility' => 'พัสดุถึงศูนย์คัดแยก',
                                    'Shipment picked up' => 'บริษัทขนส่งเข้ารับพัสดุแล้ว',
                                    'Shipment data received - Awaiting Parcel Handover to DHL' => 'ได้รับข้อมูลพัสดุแล้ว - รอการส่งมอบให้ขนส่ง',
                                    'Data Submitted - Awaiting Parcel Handover to DHL' => 'ระบบได้รับข้อมูลแล้ว - รอการจัดส่ง',
                                    ];

                                    // ถ้ามีคำในดิกชันนารี ให้คืนค่าเป็นภาษาไทย ถ้าไม่มีให้ใช้ภาษาอังกฤษเหมือนเดิม
                                    return $dictionary[$text] ?? $text;
                                    }
                                    }
