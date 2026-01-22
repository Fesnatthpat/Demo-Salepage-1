<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Order::with('user')->orderBy('ord_date', 'desc');

        if ($request->filled('search')) {
            $searchTerm = '%'.$request->search.'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('ord_code', 'like', $searchTerm)
                    ->orWhere('shipping_name', 'like', $searchTerm);
            });
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status_id', $request->status);
        }

        $orders = $query->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $order->load('user', 'details.productSalepage.images');

        // This could be moved to a config file or a model constant
        $statuses = [
            1 => 'รอชำระเงิน',
            2 => 'แจ้งชำระเงินแล้ว',
            3 => 'กำลังเตรียมจัดส่ง',
            4 => 'จัดส่งแล้ว',
            5 => 'ยกเลิก',
        ];

        return view('admin.orders.show', compact('order', 'statuses'));
    }

    /**
     * Update the status of the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status_id' => 'required|integer|in:1,2,3,4,5', // Validate against the available statuses
        ]);

        $order->status_id = $request->input('status_id');
        $order->save();

        return back()->with('success', 'อัปเดตสถานะออเดอร์เรียบร้อยแล้ว');
    }
}
