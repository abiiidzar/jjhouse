@extends('layouts.admin')

@section('title', 'Order #' . $order->order_number)
@section('header', 'Order Details')
@section('subheader', 'Order #' . $order->order_number)

@section('content')
<div class="space-y-6">
    <!-- Status Update -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="flex flex-wrap items-center gap-4">
            @csrf
            <select name="status" class="border border-gray-300 rounded-md px-3 py-2">
                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <select name="payment_status" class="border border-gray-300 rounded-md px-3 py-2">
                <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                <option value="dp" {{ $order->payment_status == 'dp' ? 'selected' : '' }}>DP</option>
                <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>
            <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-800">Update Status</button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Items -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-semibold">Order Items</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($order->items as $item)
                <div class="p-6 flex items-center gap-4">
                    <div class="w-16 h-16 bg-gray-100 rounded"></div>
                    <div class="flex-1">
                        <p class="font-medium">{{ $item->product->name }}</p>
                        <p class="text-sm text-gray-500">Size: {{ $item->size->size ?? '-' }} | Qty: {{ $item->quantity }}</p>
                        @if($item->is_preorder)
                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">Pre-order</span>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                        @if($item->preorder_payment_type == 'dp')
                        <p class="text-xs text-gray-500">DP: Rp {{ number_format($item->down_payment_amount, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">Remaining: Rp {{ number_format($item->remaining_payment, 0, ',', '.') }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Order Summary -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold mb-4">Order Summary</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span>Subtotal</span><span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span>Shipping</span><span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
                    <div class="border-t pt-2 font-semibold flex justify-between"><span>Total</span><span>Rp {{ number_format($order->total_amount + $order->shipping_cost, 0, ',', '.') }}</span></div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold mb-4">Customer Information</h3>
                <p class="text-sm"><strong>{{ $order->user->name }}</strong></p>
                <p class="text-sm text-gray-600">{{ $order->user->email }}</p>
                <p class="text-sm text-gray-600">{{ $order->user->phone ?? '-' }}</p>
                <div class="mt-3 pt-3 border-t">
                    <p class="text-sm font-medium">Shipping Address</p>
                    <p class="text-sm text-gray-600">{{ $order->shipping_address }}</p>
                    <p class="text-sm text-gray-600">{{ $order->city }}, {{ $order->province }}</p>
                    <p class="text-sm text-gray-600">Courier: {{ strtoupper($order->shipping_courier) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold mb-4">Payment Information</h3>
                <p class="text-sm">Method: {{ ucfirst($order->payment_method) }}</p>
                <p class="text-sm">Status: <span class="px-2 py-0.5 rounded text-xs {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $order->payment_status }}</span></p>
            </div>
        </div>
    </div>
</div>
@endsection
