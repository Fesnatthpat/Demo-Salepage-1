<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. รับค่า Filter และกำหนดช่วงเวลา
        $period = request()->get('period', 'this_month');
        $startDate = now()->startOfDay();
        $endDate = now()->endOfDay();

        if ($period === 'last_7_days') {
            $startDate = now()->subDays(6)->startOfDay();
        } elseif ($period === 'this_month') {
            $startDate = now()->startOfMonth()->startOfDay();
        } elseif ($period === 'last_30_days') {
            $startDate = now()->subDays(29)->startOfDay();
        } elseif ($period === 'today') {
            // today uses default logic
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

        $salesChartLabels = $salesOverTimeData->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d M'));
        $salesChartValues = $salesOverTimeData->pluck('total_sales');

        // 5. สัดส่วนสถานะ
        $orderStatusBreakdown = (clone $allOrdersScope)
            ->select('status_id', DB::raw('count(*) as count'))
            ->groupBy('status_id')
            ->pluck('count', 'status_id');

        // 6. สินค้าขายดี (Top Selling Products) - ★★★ แก้ไขจุดนี้ ★★★
        // ใช้ join ตรงๆ เพื่อแก้ปัญหา Laravel หา column orders.ord_id ไม่เจอ
        $topSellingProducts = OrderDetail::select('order_detail.pd_id', DB::raw('SUM(order_detail.ordd_count) as total_quantity'))
            ->join('orders', 'order_detail.ord_id', '=', 'orders.id') // ระบุ FK = PK ให้ชัดเจน
            ->where('orders.status_id', '>', 1)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->with('productSalepage.images')
            ->groupBy('order_detail.pd_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        // 7. ออเดอร์ล่าสุด
        $recentOrders = Order::with('user')->latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'period', 'totalSales', 'totalOrders', 'avgOrderValue', 'newCustomers',
            'salesChartLabels', 'salesChartValues', 'orderStatusBreakdown',
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
            fputcsv($file, $columns);
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->ord_code,
                    $order->user->name ?? 'N/A',
                    $order->net_amount,
                    $order->status_id,
                    $order->ord_date
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}