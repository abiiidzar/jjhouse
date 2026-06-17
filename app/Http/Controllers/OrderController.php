<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->orders()->with('items.product')->latest()->paginate(10);
        return view('dashboard.orders', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id != Auth::user()->id && !Auth::user()->email == 'admin@jjhouse.com') {
            abort(403);
        }
        $order->load('items.product', 'items.size');
        return view('dashboard.order-detail', compact('order'));
    }

    public function cancel(Order $order)
    {
        if ($order->user_id != Auth::user()->id) {
            abort(403);
        }

        if ($order->status == 'pending') {
            $order->update(['status' => 'cancelled', 'payment_status' => 'failed']);
            return redirect()->back()->with('success', 'Pesanan dibatalkan.');
        }

        return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan.');
    }

    public function completePayment(Request $request, Order $order, OrderItem $orderItem)
    {
        if ($order->user_id != Auth::user()->id) {
            abort(403);
        }

        if ($orderItem->order_id != $order->id || $orderItem->remaining_payment <= 0) {
            abort(400);
        }

        // Create Midtrans payment for remaining amount
        $midtrans = new MidtransService();
        $items = [[
            'id' => (string) $orderItem->product_id,
            'price' => (int) $orderItem->remaining_payment,
            'quantity' => 1,
            'name' => 'Pelunasan Pre-order - ' . $orderItem->product->name,
        ]];

        $customerDetails = [
            'first_name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'phone' => Auth::user()->phone,
        ];

        $tempOrder = clone $order;
        $tempOrder->order_number = $order->order_number . '-LUNAS-' . $orderItem->id;
        $tempOrder->total_amount = $orderItem->remaining_payment;

        $snapToken = $midtrans->createTransaction($tempOrder, $items, $customerDetails);

        if ($snapToken) {
            return view('payment-lunas', compact('snapToken', 'order', 'orderItem'));
        }

        return redirect()->back()->with('error', 'Gagal memproses pembayaran.');
    }

    public function invoice(Order $order)
    {
        if ($order->user_id != Auth::user()->id && !Auth::user()->email == 'admin@jjhouse.com') {
            abort(403);
        }

        $order->load('items.product', 'user');
        return view('dashboard.invoice', compact('order'));
    }
}
