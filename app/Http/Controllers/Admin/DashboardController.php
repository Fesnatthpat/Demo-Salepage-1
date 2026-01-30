<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductSalepage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. กำหนดช่วงเวลา (Period)
        $period = request()->get('period', 'this_month');
        $startDate = now()->startOfDay();
        $endDate = now()->endOfDay();

        if ($period === 'last_7_days') {
            $startDate = now()->subDays(6)->startOfDay();
        } elseif ($period === 'this_month') {
            $startDate = now()->startOfMonth()->startOfDay();
        } elseif ($period === 'last_30_days') {
            $startDate = now()->subDays(29)->startOfDay();
        }

        // 2. ข้อมูลสรุปตัวเลข (Key Metrics)
        $totalSales = Order::where('status_id', '>', 1)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('net_amount');

        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        $newCustomers = User::whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // 3. กราฟยอดขาย (Sales Chart)
        $salesOverTimeData = DB::table('orders')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(net_amount) as total_sales'))
            ->where('status_id', '>', 1)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $salesChartLabels = $salesOverTimeData->pluck('date')->map(function ($date) {
            return Carbon::parse($date)->format('d M');
        });
        $salesChartValues = $salesOverTimeData->pluck('total_sales');

        // 4. สัดส่วนสถานะ (Order Status)
        $orderStatusBreakdown = DB::table('orders')
            ->select('status_id', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status_id')
            ->pluck('count', 'status_id');

        // 5. สินค้าขายดี (Top Selling) - Optimized Logic
        $topStats = DB::table('order_detail')
            ->join('orders', 'order_detail.ord_id', '=', 'orders.id')
            ->where('orders.status_id', '>', 1)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('order_detail.pd_id', DB::raw('SUM(order_detail.ordd_count) as total_quantity'))
            ->groupBy('order_detail.pd_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        $productIds = $topStats->pluck('pd_id')->toArray();
        $products = ProductSalepage::with('images')
            ->whereIn('pd_sp_id', $productIds)
            ->get()
            ->keyBy('pd_sp_id');

        // คำนวณยอดขายสูงสุดเพื่อทำ Progress Bar
        $maxQuantity = $topStats->max('total_quantity') ?? 1;

        $topSellingProducts = $topStats->map(function ($stat) use ($products, $maxQuantity) {
            $product = $products->get($stat->pd_id);

            return (object) [
                'pd_id' => $stat->pd_id,
                'total_quantity' => $stat->total_quantity,
                'percent' => ($stat->total_quantity / $maxQuantity) * 100, // คำนวณ %
                'productSalepage' => $product,
            ];
        });

        // 6. ออเดอร์ล่าสุด
        $recentOrders = Order::with('user')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'period',
            'totalSales',
            'totalOrders',
            'avgOrderValue',
            'newCustomers',
            'salesChartLabels',
            'salesChartValues',
            'orderStatusBreakdown',
            'topSellingProducts',
            'recentOrders'
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

        $orders = Order::with('user')->orderBy('ord_date', 'desc')->limit(500);

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Order Code', 'Customer Name', 'Total Amount', 'Status', 'Order Date']);

            foreach ($orders->cursor() as $order) {
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
