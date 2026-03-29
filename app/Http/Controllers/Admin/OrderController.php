<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('orderItems.menuItem')
                       ->orderBy('created_at', 'desc')
                       ->get();

        return Inertia::render('Admin/Orders', [
            'orders' => $orders
        ]);
    }

    public function updateStatus(Order $order, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,done'
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status pesanan diperbarui.');
    }

    public function updatePayment(Order $order, Request $request)
    {
        $request->validate([
            'payment_status' => 'required|in:unpaid,paid',
            'payment_method' => 'nullable|in:qris,transfer,cash,pay_later'
        ]);

        $updateData = ['payment_status' => $request->payment_status];
        if ($request->has('payment_method')) {
            $updateData['payment_method'] = $request->payment_method;
        }

        $order->update($updateData);

        return redirect()->back()->with('success', 'Status pembayaran diperbarui.');
    }
}
