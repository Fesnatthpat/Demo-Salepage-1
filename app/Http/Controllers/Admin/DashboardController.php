<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            ->with('product:pd_id,pd_name,pd_img') // Eager load product name and image
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
}
