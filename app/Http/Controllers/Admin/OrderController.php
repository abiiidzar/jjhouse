<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('user');

        // SEARCH
        if ($request->search) {
            $orders->where(function ($query) use ($request) {
                $query->where('order_number', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('email', 'like', '%' . $request->search . '%');
                    });
            });
        }

        // FILTER STATUS
        if ($request->status) {
            $orders->where('status', $request->status);
        } 

        // FILTER TANGGAL
        if ($request->date) {
            $orders->where('created_at', '>=', now()->subDays($request->date));
        }

        if ($request->payment_status) {
            $orders->where('payment_status', $request->payment_status);
        }

        $orders = $orders->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'user');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:'.implode(',', array_keys(Order::STATUSES)),
            'payment_status' => 'required|in:'.implode(',', array_keys(Order::PAYMENT_STATUSES)),
            'tracking_number' => 'nullable|string|max:255',
            'shipping_note' => 'nullable|string|max:2000',
        ]);

        $updates = [
            'status' => $validated['status'],
            'payment_status' => $validated['payment_status'],
        ];

        foreach (['tracking_number', 'shipping_note'] as $field) {
            if (array_key_exists($field, $validated)) {
                $updates[$field] = $validated[$field];
            }
        }

        $order->update($updates);

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    public function destroy(Order $order)
    {
        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }
}
