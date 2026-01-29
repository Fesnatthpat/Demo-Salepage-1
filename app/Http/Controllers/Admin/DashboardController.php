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
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // For stats, we'll consider completed orders. Let's assume a status_id > 2 means paid/shipped/complete
        $completedOrdersScope = Order::where('status_id', '>', 1);

        // 1. Total Revenue
        $totalRevenue = $completedOrdersScope->sum('net_amount');

        // 2. Total Orders
        $totalOrders = Order::count();

        // 3. New Customers (in the last 30 days)
        $newCustomersCount = User::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        // 4. Average Order Value
        $totalCompletedOrders = $completedOrdersScope->count();
        $averageOrderValue = $totalCompletedOrders > 0 ? $totalRevenue / $totalCompletedOrders : 0;

        // 5. Recent Orders (last 10)
        $recentOrders = Order::with('user')->orderBy('ord_date', 'desc')->limit(10)->get();

        // 6. Top Selling Products
        $topSellingProducts = OrderDetail::select('pd_id', DB::raw('SUM(ordd_count) as total_sold'))
            ->with('productSalepage.images')
            ->groupBy('pd_id')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        $stats = [
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'newCustomers' => $newCustomersCount,
            'averageOrderValue' => $averageOrderValue,
        ];

        return view('admin.dashboard', compact('stats', 'recentOrders', 'topSellingProducts'));
    }

    /**
     * Export recent orders to a CSV file.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export()
    {
        $fileName = 'recent-orders-'.date('Y-m-d').'.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $orders = Order::with('user')->orderBy('ord_date', 'desc')->limit(100)->get(); // Get more for export
        $columns = ['Order Code', 'Customer Name', 'Total Amount', 'Status', 'Order Date'];

        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                $row['Order Code']    = $order->ord_code;
                $row['Customer Name'] = $order->user->name ?? 'N/A';
                $row['Total Amount']  = $order->net_amount;
                $row['Status']        = $order->status_id; // You might want a more descriptive status
                $row['Order Date']    = $order->ord_date;

                fputcsv($file, array_values($row));
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
