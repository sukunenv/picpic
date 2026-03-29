<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'  => 'required|string|max:100',
            'table_number'   => 'nullable|string|max:20',
            'payment_method' => 'required|in:qris,transfer,cash,pay_later',
            'items'          => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.note'         => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $total = 0;
            $orderItems = [];

            foreach ($validated['items'] as $item) {
                $menuItem = MenuItem::findOrFail($item['menu_item_id']);
                $subtotal = $menuItem->price * $item['quantity'];
                $total += $subtotal;
                $orderItems[] = [
                    'menu_item_id' => $item['menu_item_id'],
                    'quantity'     => $item['quantity'],
                    'price'        => $menuItem->price,
                    'note'         => $item['note'] ?? null,
                ];
            }

            $order = Order::create([
                'customer_name'  => $validated['customer_name'],
                'table_number'   => $validated['table_number'] ?? null,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_method'] === 'pay_later' ? 'unpaid' : 'unpaid',
                'status'         => 'pending',
                'total_price'    => $total,
            ]);

            foreach ($orderItems as &$oi) {
                $oi['order_id'] = $order->id;
            }
            OrderItem::insert($orderItems);

            DB::commit();

            return response()->json([
                'success' => true,
                'order'   => $order->load('orderItems.menuItem'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $order = Order::with('orderItems.menuItem')->findOrFail($id);
        return response()->json(['data' => $order]);
    }
}
