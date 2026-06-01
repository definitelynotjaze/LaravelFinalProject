<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['items', 'user'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('user.orders', compact('orders'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.id'         => 'required|integer',
            'items.*.qty'        => 'required|integer|min:1',
            'type'               => 'nullable|in:pickup,delivery',
            'notes'              => 'nullable|string|max:500',
            'payment_method'     => 'nullable|in:gcash,bank,cod',
            'payment_reference'  => 'nullable|string|max:100',
        ]);

        $delivery = $validated['type'] === 'delivery' ? 50 : 0;

        $subtotal = 0;
        $lines = [];

        foreach ($validated['items'] as $item) {

            $menuItem = MenuItem::find($item['id']);

            if (!$menuItem || !$menuItem->is_available) {
                continue;
            }

            $qty = (int) $item['qty'];
            $sub = $menuItem->price * $qty;

            $subtotal += $sub;

            $lines[] = [
                'menu_item_id'   => $menuItem->id,
                'menu_item_name' => $menuItem->name,
                'price'          => $menuItem->price,
                'quantity'       => $qty,
                'subtotal'       => $sub,
            ];
        }

        if (count($lines) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No valid items found'
            ], 422);
        }

        $paymentMethod = $validated['payment_method'] ?? 'cod';

        if (in_array($paymentMethod, ['gcash', 'bank']) && empty($validated['payment_reference'])) {
            return response()->json([
                'success' => false,
                'message' => 'Payment reference required'
            ], 422);
        }

        $order = Order::create([
            'user_id'           => auth()->id(),
            'status'            => 'pending',
            'type'              => $validated['type'] ?? 'pickup',
            'subtotal'          => $subtotal,
            'delivery_fee'      => $delivery,
            'total'             => $subtotal + $delivery,
            'notes'             => $validated['notes'] ?? null,
            'payment_method'    => $paymentMethod,
            'payment_reference' => $validated['payment_reference'] ?? null,
            'payment_status'    => 'pending',
        ]);

        $order->items()->createMany($lines);

        return response()->json([
            'success'  => true,
            'order_id' => $order->id
        ]);
    }

    public function cancel(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return redirect()->route('orders.index')
                ->with('error', 'This order can no longer be cancelled.');
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->route('orders.index')
            ->with('success', 'Order #' . str_pad($order->id, 4, '0', STR_PAD_LEFT) . ' has been cancelled.');
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,completed,cancelled'
        ]);

        $order->update([
            'status' => $validated['status']
        ]);

        return response()->json(['success' => true]);
    }
}