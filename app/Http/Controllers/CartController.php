<?php
 
namespace App\Http\Controllers;
 
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
 
class CartController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'  => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'table_number'   => 'nullable|string|max:20',
            'payment_method' => 'required|in:qris,transfer,cash,pay_later',
            'payment_status' => 'required|in:unpaid,paid',
            'items'          => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.note'         => 'nullable|string',
        ]);
 
        DB::beginTransaction();
        try {
            $total = 0;
            $orderItemsData = [];
 
            foreach ($validated['items'] as $item) {
                $menuItem = MenuItem::findOrFail($item['menu_item_id']);
                $subtotal = $menuItem->price * $item['quantity'];
                $total += $subtotal;
                
                $orderItemsData[] = [
                    'menu_item_id' => $item['menu_item_id'],
                    'quantity'     => $item['quantity'],
                    'price'        => $menuItem->price,
                    'note'         => $item['note'] ?? null,
                ];
            }
 
            $order = Order::create([
                'customer_name'  => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'table_number'   => $validated['table_number'] ?? null,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_status'],
                'status'         => 'pending',
                'total_price'    => $total,
            ]);
 
            foreach ($orderItemsData as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);
            }
 
            DB::commit();

            return redirect()->route('order.status', ['id' => $order->id])
                ->with('success', 'Pesanan berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
