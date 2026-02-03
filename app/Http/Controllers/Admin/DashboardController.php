<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. รับค่า Filter และกำหนดช่วงเวลา
        $period = $request->get('period', 'this_month');

        // ค่าเริ่มต้น (วันนี้)
        $startDate = now()->startOfDay();
        $endDate = now()->endOfDay();

        // Logic การคำนวณช่วงเวลา
        if ($period === 'today') {
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
        } elseif ($period === 'last_7_days') {
            $startDate = now()->subDays(6)->startOfDay();
            $endDate = now()->endOfDay();
        } elseif ($period === 'this_month') {
            $startDate = now()->startOfMonth()->startOfDay();
            $endDate = now()->endOfDay();
        } elseif ($period === 'last_30_days') {
            $startDate = now()->subDays(29)->startOfDay();
            $endDate = now()->endOfDay();
        } elseif ($period === 'custom') {
            // Logic สำหรับเลือกวันเอง
            if ($request->has('start_date') && $request->has('end_date')) {
                try {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                } catch (\Exception $e) {
                    // ถ้าวันที่ผิด format ให้กลับไปใช้ default
                }
            }
        }

        // 2. เตรียม Query หลัก
        $salesScope = Order::where('status_id', '>', 1)->whereBetween('created_at', [$startDate, $endDate]);
        $allOrdersScope = Order::whereBetween('created_at', [$startDate, $endDate]);

        // 3. คำนวณ Key Metrics
        $totalSales = (clone $salesScope)->sum('net_amount');
        $totalOrders = (clone $allOrdersScope)->count();
        $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        $newCustomers = User::whereBetween('created_at', [$startDate, $endDate])->count();

        // 4. กราฟยอดขาย
        $salesOverTimeData = (clone $salesScope)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(net_amount) as total_sales'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // [แก้ไข] สร้างตัวแปร salesChartRawDates เพื่อส่งวันที่แบบเต็ม (YYYY-MM-DD) ไปให้กราฟ
        $salesChartLabels = $salesOverTimeData->pluck('date')->map(fn ($date) => Carbon::parse($date)->format('d M'));
        $salesChartRawDates = $salesOverTimeData->pluck('date'); // ✅ เพิ่มบรรทัดนี้
        $salesChartValues = $salesOverTimeData->pluck('total_sales');

        // 5. สัดส่วนสถานะ
        $orderStatusBreakdown = (clone $allOrdersScope)
            ->select('status_id', DB::raw('count(*) as count'))
            ->groupBy('status_id')
            ->pluck('count', 'status_id');

        // 6. สินค้าขายดี (Top Selling Products)
        $topSellingProducts = OrderDetail::select('order_detail.pd_id', DB::raw('SUM(order_detail.ordd_count) as total_quantity'))
            ->join('orders', 'order_detail.ord_id', '=', 'orders.id')
            ->where('orders.status_id', '>', 1)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->with('productSalepage.images')
            ->groupBy('order_detail.pd_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        // คำนวณ % บาร์กราฟสินค้าขายดี
        $maxQty = $topSellingProducts->max('total_quantity');
        $topSellingProducts->transform(function ($item) use ($maxQty) {
            $item->percent = $maxQty > 0 ? ($item->total_quantity / $maxQty) * 100 : 0;

            return $item;
        });

        // 7. ออเดอร์ล่าสุด
        $recentOrders = Order::with('user')->latest()->take(10)->get();

        // ส่งตัวแปรวันที่กลับไปหน้า View เพื่อแสดงใน Input
        $currentStartDate = $startDate->format('Y-m-d');
        $currentEndDate = $endDate->format('Y-m-d');

        // [แก้ไข] อย่าลืมใส่ 'salesChartRawDates' ใน compact
        return view('admin.dashboard', compact(
            'period', 'currentStartDate', 'currentEndDate',
            'totalSales', 'totalOrders', 'avgOrderValue', 'newCustomers',
            'salesChartLabels', 'salesChartRawDates', 'salesChartValues', 'orderStatusBreakdown', // ✅ เพิ่มตรงนี้
            'topSellingProducts', 'recentOrders'
        ));
    }

    public function export()
    {
        $fileName = 'recent-orders-'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $orders = Order::with('user')->orderBy('ord_date', 'desc')->limit(100)->get();
        $columns = ['Order Code', 'Customer Name', 'Total Amount', 'Status', 'Order Date'];

        $callback = function () use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // Add BOM for Excel Thai support
            fputcsv($file, $columns);
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->ord_code,
                    $order->user->name ?? 'N/A',
                    $order->net_amount,
                    $order->status_id,
                    $order->ord_date,
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
