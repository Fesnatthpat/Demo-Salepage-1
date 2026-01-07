<?php

namespace App\Http\Controllers;

use App\Models\Amphure;
use App\Models\DeliveryAddress;
use App\Models\District;
use App\Models\Province;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    // 1. แสดงหน้าฟอร์ม (แก้ไขใหม่)
    public function index()
    {
        // ดึงจังหวัดสำหรับ Dropdown
        $provinces = Province::orderBy('name_th', 'asc')->get();

        // ดึงที่อยู่จัดส่งทั้งหมดจาก Database (ล่าสุดขึ้นก่อน)
        $addresses = DeliveryAddress::orderBy('created_at', 'desc')->get();

        // ส่งตัวแปรไปทั้งคู่
        return view('payment', compact('provinces', 'addresses'));
    }

    // 2. API ดึงอำเภอ
    public function getAmphures($province_id)
    {
        $amphures = Amphure::where('province_id', $province_id)->orderBy('name_th', 'asc')->get();

        return response()->json($amphures);
    }

    // 3. API ดึงตำบล
    public function getDistricts($amphure_id)
    {
        $districts = District::where('amphure_id', $amphure_id)->orderBy('name_th', 'asc')->get();

        return response()->json($districts);
    }

    // 4. บันทึกข้อมูล
    public function saveAddress(Request $request)
    {
        $request->validate([
            'fullname' => 'required',
            'phone' => 'required',
            'address_line1' => 'required',
            'province_id' => 'required',
            'amphure_id' => 'required',
            'district_id' => 'required',
            'zipcode' => 'required',
        ]);

        DeliveryAddress::create([
            'fullname' => $request->fullname,
            'phone' => $request->phone,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'province_id' => $request->province_id,
            'amphure_id' => $request->amphure_id,
            'district_id' => $request->district_id,
            'zipcode' => $request->zipcode,
            'note' => $request->note,
        ]);

        return back()->with('success', 'บันทึกที่อยู่จัดส่งเรียบร้อยแล้ว!');
    }
}
