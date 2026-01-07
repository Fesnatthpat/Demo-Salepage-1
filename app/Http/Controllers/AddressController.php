<?php

namespace App\Http\Controllers;

use App\Models\Amphure;
use App\Models\DeliveryAddress;
use App\Models\District;
use App\Models\Province;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    // 1. แสดงหน้าฟอร์มชำระเงิน
    public function index(Request $request) // [แก้ไข] เพิ่ม Request เพื่อรับค่าจากตะกร้า
    {
        $userId = Auth::id();

        // 1.1 ดึงข้อมูลจังหวัด (สำหรับ Dropdown)
        $provinces = Province::orderBy('name_th', 'asc')->get();

        // 1.2 ดึงข้อมูลที่อยู่
        $addresses = DeliveryAddress::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // ==========================================================
        // [แก้ไขใหม่] ส่วนการกรองสินค้าที่เลือกและคำนวณราคา
        // ==========================================================
        
        // 1. รับ ID สินค้าที่ถูกติ๊กเลือกมาจากหน้าตะกร้า
        $selectedIds = $request->input('selected_items', []);

        // 2. ดึงสินค้าทั้งหมดในตะกร้า
        $allItems = Cart::session($userId)->getContent();

        // 3. กรอง (Filter) เอาเฉพาะตัวที่ ID ตรงกับที่เลือกมา
        if (!empty($selectedIds)) {
            $cartItems = $allItems->filter(function($item) use ($selectedIds) {
                return in_array($item->id, $selectedIds);
            });
        } else {
            // กรณีไม่ได้เลือกอะไรมาเลย (หรือเข้าหน้า payment ตรงๆ)
            // ให้แสดงทั้งหมด หรือ จะ Redirect กลับไปหน้า cart ก็ได้
            $cartItems = $allItems;
        }

        // 4. คำนวณยอดรวมใหม่ (เฉพาะของที่เลือก)
        $total = $cartItems->sum(function($item) {
            return $item->getPriceSum();
        });

        // 5. [สำคัญ] สร้าง Query String เพื่อส่งต่อไปยังปุ่มชำระเงิน (QR/Order)
        // เพื่อให้ขั้นตอนต่อไปรู้ว่าเราจ่ายเงินค่าสินค้าตัวไหนบ้าง
        $queryString = http_build_query(['selected_items' => $selectedIds]);

        // ส่งตัวแปรทั้งหมดไปที่หน้า View
        return view('payment', compact('provinces', 'addresses', 'cartItems', 'total', 'queryString'));
    }

    // --- ส่วน API และ Function อื่นๆ คงเดิม ไม่ต้องแก้ ---

    public function getAmphures($province_id)
    {
        $amphures = Amphure::where('province_id', $province_id)->orderBy('name_th', 'asc')->get();
        return response()->json($amphures);
    }

    public function getDistricts($amphure_id)
    {
        $districts = District::where('amphure_id', $amphure_id)->orderBy('name_th', 'asc')->get();
        return response()->json($districts);
    }

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
            'user_id' => Auth::id(),
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

    public function update(Request $request, $id)
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

        $address = DeliveryAddress::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $address->update([
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

        return back()->with('success', 'แก้ไขที่อยู่เรียบร้อยแล้ว!');
    }

    public function destroy($id)
    {
        $address = DeliveryAddress::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $address->delete();
        return back()->with('success', 'ลบที่อยู่เรียบร้อยแล้ว!');
    }
}