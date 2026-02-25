<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * ฟังก์ชันหลักสำหรับแสดงหน้าติดตามพัสดุและค้นหาข้อมูล
     */
    public function index(Request $request)
    {
        $trackingData = null;

        // ถ้ามีการค้นหา (มีการกรอก order_code เข้ามา)
        if ($request->has('order_code')) {
            $orderCode = $request->order_code;

            // 1. ดึงข้อมูล JSON จำลอง
            $mockupJsonResponse = $this->getMockupData();

            // 2. แปลง JSON string เป็น Array ของ PHP
            $responseData = json_decode($mockupJsonResponse, true);

            // 3. ค้นหาพัสดุที่ตรงกับรหัสที่กรอกเข้ามา
            $foundShipment = null;
            if (isset($responseData['trackItemResponse']['bd']['shipmentItems'])) {
                foreach ($responseData['trackItemResponse']['bd']['shipmentItems'] as $item) {
                    if ($item['shipmentID'] == $orderCode || $item['trackingID'] == $orderCode) {
                        $foundShipment = $item;
                        break;
                    }
                }
            }

            // 4. ถ้าเจอข้อมูล ให้จัดรูปแบบเพื่อส่งไปที่ View
            if ($foundShipment) {

                // จัดการ Timeline Events
                $events = collect($foundShipment['events'])->map(function ($event, $index) {
                    $date = Carbon::parse($event['dateTime']);

                    return [
                        // จัดรูปแบบวันที่: เช่น MONDAY ก.พ. 24, 2026
                        'date' => $index === 0 ? strtoupper($date->format('l')).' '.$date->locale('th')->translatedFormat('M d, Y') : '',
                        'time' => $date->format('H:i'),
                        'status' => $event['description'], // จะเป็นภาษาไทยตามที่แก้ใน mockup
                        'location' => $this->formatAddress($event['address']),
                        'is_latest' => $index === 0,
                    ];
                })->toArray();

                // เช็คสถานะจัดส่งสำเร็จ (อิงจาก code 77093 ของ DHL)
                $latestEvent = $foundShipment['events'][0] ?? null;
                $isDelivered = $latestEvent && $latestEvent['status'] == '77093';

                // แมปปิ้งข้อมูลทั้งหมดส่งให้ View
                $trackingData = [
                    'trackingNumber' => $foundShipment['shipmentID'],
                    'referenceId' => $foundShipment['trackingID'] ?? '-',
                    'weight' => $foundShipment['weight'] ?? '-',
                    'service' => $foundShipment['shippingService']['productName'] ?? '-',
                    'origin' => $this->formatAddress(end($foundShipment['events'])['address'] ?? []),
                    'destination' => $this->formatAddress($foundShipment['events'][0]['address'] ?? []),
                    'deliveredAt' => $latestEvent ? Carbon::parse($latestEvent['dateTime'])->locale('th')->translatedFormat('d M Y เวลา H:i น.') : '-',
                    'status_text' => $latestEvent['description'] ?? 'ไม่ทราบสถานะ',
                    'timelineSteps' => [
                        ['label' => 'ได้รับข้อมูลพัสดุ', 'active' => true],
                        ['label' => 'เข้ารับพัสดุสำเร็จ', 'active' => count($events) > 1],
                        ['label' => 'ดำเนินการจัดส่ง', 'active' => count($events) > 2],
                        // ปรับแก้ตรงนี้ให้เช็คคำภาษาไทย:
                        ['label' => 'อยู่ระหว่างการจัดส่ง', 'active' => collect($events)->contains('status', 'พัสดุอยู่ระหว่างการจัดส่ง') || $isDelivered],
                        ['label' => 'จัดส่งสำเร็จ', 'active' => $isDelivered, 'is_truck' => true],
                    ],
                    'events' => $events,
                ];

            } else {
                return back()->with('error', 'ไม่พบข้อมูลพัสดุ รหัสการจัดส่งนี้ในระบบ');
            }
        }

        return view('ordertracking', compact('trackingData'));
    }

    /**
     * ฟังก์ชันตัวช่วยสำหรับจัดรูปแบบที่อยู่
     */
    private function formatAddress($addressData)
    {
        if (empty($addressData)) {
            return 'ไม่ระบุพื้นที่';
        }

        $parts = [];
        if (! empty($addressData['city'])) {
            $parts[] = $addressData['city'];
        }
        if (! empty($addressData['state']) && $addressData['state'] !== '-') {
            $parts[] = $addressData['state'];
        }
        if (! empty($addressData['postCode'])) {
            $parts[] = $addressData['postCode'];
        }
        if (! empty($addressData['country'])) {
            // แปลตัวย่อประเทศ
            $parts[] = $addressData['country'] === 'TH' ? 'ประเทศไทย' : $addressData['country'];
        }

        return implode(', ', $parts);
    }

    /**
     * ฟังก์ชันเก็บข้อมูล JSON จำลอง (Mockup) - แปลเป็นภาษาไทยแล้ว
     */
    private function getMockupData()
    {
        return '{
            "trackItemResponse": {
                "hdr": {
                    "messageType": "TRACKITEM",
                    "messageDateTime": "2026-02-24T11:26:32+08:00",
                    "messageVersion": "1.0",
                    "messageLanguage": "th"
                },
                "bd": {
                    "shipmentItems": [
                        {
                            "shipmentID": "THIUHKWB0001028032",
                            "trackingID": "7127028494860786",
                            "shippingService": {
                                "productCode": "PDO",
                                "productName": "ส่งพัสดุภายในประเทศ"
                            },
                            "weight": "180",
                            "events": [
                                {
                                    "status": "77222",
                                    "description": "โอนเงินค่าพัสดุปลายทาง (COD) เรียบร้อยแล้ว",
                                    "dateTime": "2026-02-20 15:21:53",
                                    "address": { "country": "TH" }
                                },
                                {
                                    "status": "77223",
                                    "description": "นำเงินค่าพัสดุปลายทางเข้าบัญชีเรียบร้อยแล้ว",
                                    "dateTime": "2026-02-20 12:01:10",
                                    "address": { "country": "TH" }
                                },
                                {
                                    "status": "77093",
                                    "description": "จัดส่งพัสดุสำเร็จ",
                                    "dateTime": "2026-02-19 15:02:47",
                                    "address": { "city": "สามพราน", "postCode": "73110", "state": "นครปฐม", "country": "TH" }
                                },
                                {
                                    "status": "77702",
                                    "description": "พัสดุพร้อมสำหรับการจัดส่ง",
                                    "dateTime": "2026-02-19 09:04:53",
                                    "address": { "city": "สามพราน", "state": "นครปฐม", "country": "TH" }
                                },
                                {
                                    "status": "77090",
                                    "description": "พัสดุอยู่ระหว่างการจัดส่ง",
                                    "dateTime": "2026-02-19 09:04:53",
                                    "address": { "city": "สามพราน", "state": "นครปฐม", "country": "TH" }
                                },
                                {
                                    "status": "77184",
                                    "description": "พัสดุถูกดำเนินการที่ศูนย์กระจายสินค้าปลายทาง",
                                    "dateTime": "2026-02-19 08:29:29",
                                    "address": { "city": "สามพราน", "state": "นครปฐม", "country": "TH" }
                                },
                                {
                                    "status": "77178",
                                    "description": "พัสดุถึงศูนย์กระจายสินค้าแล้ว",
                                    "dateTime": "2026-02-19 07:49:17",
                                    "address": { "city": "สามพราน", "state": "นครปฐม", "country": "TH" }
                                },
                                {
                                    "status": "77169",
                                    "description": "พัสดุออกจากศูนย์กระจายสินค้ากลาง",
                                    "dateTime": "2026-02-19 02:18:50",
                                    "address": { "city": "สมุทรปราการ", "postCode": "10540", "country": "TH" }
                                },
                                {
                                    "status": "77206",
                                    "description": "เข้ารับพัสดุสำเร็จ",
                                    "dateTime": "2026-02-18 16:11:57",
                                    "address": { "city": "บางแค", "postCode": "10160", "state": "กรุงเทพมหานคร", "country": "TH" }
                                },
                                {
                                    "status": "71005",
                                    "description": "ได้รับข้อมูลพัสดุเรียบร้อยแล้ว",
                                    "dateTime": "2026-02-18 10:22:27",
                                    "address": { "city": "บางแค", "state": "กรุงเทพมหานคร", "country": "TH" }
                                }
                            ]
                        }
                    ]
                }
            }
        }';
    }
}
