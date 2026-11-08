<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of orders for fulfillment.
     */
    public function index()
    {
        $orders = Order::whereIn('status', ['pending', 'processing', 'ready_to_ship'])
            ->with('user', 'items')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('karyawan.orders.index', compact('orders'));
    }

    /**
     * Display a specific order.
     */
    public function show(Order $order)
    {
        $order->load('user', 'items.product');
        return view('karyawan.orders.show', compact('order'));
    }

    /**
     * Update order fulfillment status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,ready_to_ship,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:255',
            'shipping_note' => 'nullable|string|max:2000',
        ]);

        $oldStatus = $order->status;
        $updates = [
            'status' => $validated['status'],
            'cancellation_source' => $validated['status'] === 'cancelled' ? 'staff' : null,
        ];

        foreach (['tracking_number', 'shipping_note'] as $field) {
            if (array_key_exists($field, $validated)) {
                $updates[$field] = $validated[$field];
            }
        }

        if ($validated['status'] === 'cancelled') {
            $order->releaseStock('Order cancelled by staff');
        }

        $order->update($updates);

        return redirect()->route('karyawan.orders.show', $order)
            ->with('success', "Status pesanan diubah dari {$oldStatus} menjadi {$validated['status']}");
    }
}
