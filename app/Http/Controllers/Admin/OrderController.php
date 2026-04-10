<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('orderItems.menuItem')->orderBy('created_at', 'desc');

        // Filter by search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $cleanId = ltrim(str_replace(['#', 'PIC-'], '', $request->search), '0');
                if (is_numeric($cleanId)) {
                    $q->orWhere('id', (int)$cleanId);
                }
                $q->orWhere('customer_name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->status && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->only_unpaid === 'true') {
            $query->where('payment_status', 'unpaid');
        }

        // Pagination
        $perPage = $request->input('per_page', 9);
        $orders = $query->paginate($perPage)->withQueryString();

        // Status counts for Tabs (ignoring current filters to keep badges globally absolute)
        $counts = [
            'semua' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'done' => Order::where('status', 'done')->count(),
        ];

        return Inertia::render('Admin/Orders', [
            'orders' => $orders,
            'filters' => $request->only('search', 'status', 'only_unpaid', 'per_page'),
            'counts' => $counts
        ]);
    }

    public function kasir()
    {
        $menuItems = MenuItem::available()->orderBy('name')->get();

        return Inertia::render('Admin/Kasir', [
            'menuItems' => $menuItems
        ]);
    }

    public function quickConfirm(Order $order)
    {
        $order->update([
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'status' => 'done'
        ]);

        return redirect()->back()->with('success', 'Pesanan berhasil dikonfirmasi (Lunas & Selesai).');
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
