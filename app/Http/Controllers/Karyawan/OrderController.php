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
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $validated['status']]);

        return redirect()->route('karyawan.orders.show', $order)
            ->with('success', "Status pesanan diubah dari {$oldStatus} menjadi {$validated['status']}");
    }
}
