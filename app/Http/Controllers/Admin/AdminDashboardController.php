<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $stats = [
            'total_orders'    => Order::whereDate('created_at', $today)->count(),
            'pending_orders'  => Order::where('status', 'pending')->count(),
            'today_revenue'   => Order::whereDate('created_at', $today)
                                     ->where('payment_status', 'paid')
                                     ->sum('total_price'),
            'unpaid_orders'   => Order::where('payment_status', 'unpaid')->count(),
        ];

        $recentOrders = Order::with('orderItems.menuItem')
                             ->latest()
                             ->limit(5)
                             ->get();

        $unavailableMenus = MenuItem::where('is_available', false)->get();
 
        return Inertia::render('Admin/Dashboard', [
            'stats'            => $stats,
            'recentOrders'     => $recentOrders,
            'unavailableMenus' => $unavailableMenus
        ]);
    }
}
