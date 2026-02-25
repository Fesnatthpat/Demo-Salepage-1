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
                        'date' => $index === 0 ? strtoupper($date->format('l')).''.$date->locale('th')->translatedFormat('M d, Y') : '',
                        'time' => $date->format('H:i'),
                        'status' => $event['description'],
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
                    'deliveredAt' => $latestEvent ? Carbon::parse($latestEvent['dateTime'])->locale('th')->translatedFormat('d-M-Y \a\t H:i') : '-',
                    'timelineSteps' => [
                        ['label' => 'ได้รับข้อมูลพัสดุ', 'active' => true],
                        ['label' => 'เข้ารับพัสดุสำเร็จ', 'active' => count($events) > 1],
                        ['label' => 'ดำเนินการจัดส่ง', 'active' => count($events) > 2],
                        ['label' => 'อยู่ระหว่างการจัดส่ง', 'active' => collect($events)->contains('status', 'Out for Delivery') || $isDelivered],
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
            return 'N/A';
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
            $parts[] = $addressData['country'];
        }

        return implode(', ', $parts);
    }

    /**
     * ฟังก์ชันเก็บข้อมูล JSON จำลอง (Mockup)
     */
    private function getMockupData()
    {
        return '{
            "trackItemResponse": {
                "hdr": {
                    "messageType": "TRACKITEM",
                    "messageDateTime": "2026-02-24T11:26:32+08:00",
                    "messageVersion": "1.0",
                    "messageLanguage": "en"
                },
                "bd": {
                    "shipmentItems": [
                        {
                            "masterShipmentID": null,
                            "shipmentID": "THIUHKWB0001028032",
                            "trackingID": "7127028494860786",
                            "deliveryImage": null,
                            "enhancedPod": null,
                            "orderNumber": null,
                            "handoverID": null,
                            "shippingService": {
                                "productCode": "PDO",
                                "productName": "Parcel Domestic"
                            },
                            "consigneeAddress": {
                                "country": "TH"
                            },
                            "weight": "180",
                            "dimensionalWeight": "216",
                            "weightUnit": "G",
                            "events": [
                                {
                                    "status": "77222",
                                    "description": "Cod amount has been remitted",
                                    "dateTime": "2026-02-20 15:21:53",
                                    "timezone": "LT",
                                    "address": {
                                        "city": null,
                                        "postCode": null,
                                        "state": null,
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77223",
                                    "description": "Cod amount has been deposited",
                                    "dateTime": "2026-02-20 12:01:10",
                                    "timezone": "LT",
                                    "address": {
                                        "city": null,
                                        "postCode": null,
                                        "state": null,
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77093",
                                    "description": "Successfully delivered",
                                    "dateTime": "2026-02-19 15:02:47",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "A.Sampran-Nakhonphatom",
                                        "postCode": "73110",
                                        "state": "นครปฐม",
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77702",
                                    "description": "Available for Delivery",
                                    "secondaryStatus": "77706",
                                    "secondaryEvent": "DHL will deliver your parcel approximately 17:00 ",
                                    "dateTime": "2026-02-19 09:04:53",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "A.Sampran-Nakhonphatom",
                                        "postCode": "73110",
                                        "state": "นครปฐม",
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77090",
                                    "description": "Out for Delivery",
                                    "dateTime": "2026-02-19 09:04:53",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "A.Sampran-Nakhonphatom",
                                        "postCode": "73110",
                                        "state": "นครปฐม",
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77184",
                                    "description": "Processed at delivery facility",
                                    "dateTime": "2026-02-19 08:29:29",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "A.Sampran-Nakhonphatom",
                                        "postCode": "73110",
                                        "state": "Nakhonphatom",
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77178",
                                    "description": "Arrived at facility",
                                    "dateTime": "2026-02-19 07:49:17",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "A.Sampran-Nakhonphatom",
                                        "postCode": "73110",
                                        "state": "Nakhonphatom",
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77169",
                                    "description": "Departed from facility",
                                    "dateTime": "2026-02-19 02:18:50",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "Hub-Samut Prakarn",
                                        "postCode": "10540",
                                        "state": null,
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77027",
                                    "description": "Sorted to delivery facility",
                                    "dateTime": "2026-02-18 17:46:39",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "Hub-Samut Prakarn",
                                        "postCode": "10540",
                                        "state": null,
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77015",
                                    "description": "Processed at facility",
                                    "dateTime": "2026-02-18 17:34:17",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "Hub-Samut Prakarn",
                                        "postCode": "10540",
                                        "state": null,
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77013",
                                    "description": "Arrival at Facility",
                                    "dateTime": "2026-02-18 17:23:29",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "Hub-Samut Prakarn",
                                        "postCode": "10540",
                                        "state": null,
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77206",
                                    "description": "Shipment picked up",
                                    "dateTime": "2026-02-18 16:11:57",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "Hub-Samut Prakarn",
                                        "postCode": "10160",
                                        "state": "OTHER",
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77123",
                                    "description": "Shipment data received - Awaiting Parcel Handover to DHL",
                                    "dateTime": "2026-02-18 10:22:54",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "Hub-Samut Prakarn",
                                        "postCode": "10540",
                                        "state": null,
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "71005",
                                    "description": "Data Submitted - Awaiting Parcel Handover to DHL",
                                    "dateTime": "2026-02-18 10:22:27",
                                    "timezone": "Thailand",
                                    "address": {
                                        "city": "Bang Khae, Bang Khae, Bangkok",
                                        "postCode": "10160",
                                        "state": "-",
                                        "country": "TH"
                                    }
                                }
                            ],
                            "dimensions": {
                                "length": null,
                                "width": null,
                                "height": null
                            },
                            "dimensionsUnit": null
                        },
                        {
                            "masterShipmentID": null,
                            "shipmentID": "THIUHKWB0001028033",
                            "trackingID": "7127028546623586",
                            "deliveryImage": null,
                            "enhancedPod": null,
                            "orderNumber": null,
                            "handoverID": null,
                            "shippingService": {
                                "productCode": "PDO",
                                "productName": "Parcel Domestic"
                            },
                            "consigneeAddress": {
                                "country": "TH"
                            },
                            "weight": "550",
                            "dimensionalWeight": "371",
                            "weightUnit": "G",
                            "events": [
                                {
                                    "status": "77093",
                                    "description": "Successfully delivered",
                                    "dateTime": "2026-02-19 11:46:49",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "Yannawa-Bangkok",
                                        "postCode": "10500",
                                        "state": "กรุงเทพมหานคร",
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77702",
                                    "description": "Available for Delivery",
                                    "secondaryStatus": "77706",
                                    "secondaryEvent": "DHL will deliver your parcel approximately 17:00 ",
                                    "dateTime": "2026-02-19 09:02:18",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "Yannawa-Bangkok",
                                        "postCode": "10500",
                                        "state": "กรุงเทพมหานคร",
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77090",
                                    "description": "Out for Delivery",
                                    "dateTime": "2026-02-19 09:02:18",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "Yannawa-Bangkok",
                                        "postCode": "10500",
                                        "state": "กรุงเทพมหานคร",
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77184",
                                    "description": "Processed at delivery facility",
                                    "dateTime": "2026-02-19 07:56:45",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "Yannawa-Bangkok",
                                        "postCode": "10120",
                                        "state": null,
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77178",
                                    "description": "Arrived at facility",
                                    "dateTime": "2026-02-19 07:49:43",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "Yannawa-Bangkok",
                                        "postCode": "10120",
                                        "state": null,
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77169",
                                    "description": "Departed from facility",
                                    "dateTime": "2026-02-18 19:03:16",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "Hub-Samut Prakarn",
                                        "postCode": "10540",
                                        "state": null,
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77027",
                                    "description": "Sorted to delivery facility",
                                    "dateTime": "2026-02-18 18:40:06",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "Hub-Samut Prakarn",
                                        "postCode": "10540",
                                        "state": null,
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77015",
                                    "description": "Processed at facility",
                                    "dateTime": "2026-02-18 17:18:28",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "Hub-Samut Prakarn",
                                        "postCode": "10540",
                                        "state": null,
                                        "country": "TH"
                                    }
                                },
                                
                                {
                                    "status": "77206",
                                    "description": "Shipment picked up",
                                    "dateTime": "2026-02-18 16:11:57",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "Hub-Samut Prakarn",
                                        "postCode": "10160",
                                        "state": "OTHER",
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "77123",
                                    "description": "Shipment data received - Awaiting Parcel Handover to DHL",
                                    "dateTime": "2026-02-18 10:31:53",
                                    "timezone": "LT",
                                    "address": {
                                        "city": "Hub-Samut Prakarn",
                                        "postCode": "10540",
                                        "state": null,
                                        "country": "TH"
                                    }
                                },
                                {
                                    "status": "71005",
                                    "description": "Data Submitted - Awaiting Parcel Handover to DHL",
                                    "dateTime": "2026-02-18 10:31:05",
                                    "timezone": "Thailand",
                                    "address": {
                                        "city": "Bang Khae, Bang Khae, Bangkok",
                                        "postCode": "10160",
                                        "state": "-",
                                        "country": "TH"
                                    }
                                }
                            ],
                            "dimensions": {
                                "length": 16.7,
                                "width": 11,
                                "height": 10.1
                            },
                            "dimensionsUnit": "CM"
                        }
                    ]
                },
                "responseStatus": {
                    "code": "200",
                    "message": "SUCCESS",
                    "messageDetails": [
                        {
                            "messageDetail": "2 tracking reference(s) tracked, 2 tracking reference(s) found."
                        }
                    ]
                }
            }
        }';
    }
}
