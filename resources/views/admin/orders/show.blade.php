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
                <option value="ready_to_ship" {{ $order->status == 'ready_to_ship' ? 'selected' : '' }}>Ready to Ship</option>
                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <select name="payment_status" class="border border-gray-300 rounded-md px-3 py-2">
                <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="dp" {{ $order->payment_status == 'dp' ? 'selected' : '' }}>DP</option>
                <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
            <div class="grid w-full grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="tracking_number" class="mb-1 block text-sm font-medium text-gray-700">Nomor resi <span class="font-normal text-gray-500">(opsional)</span></label>
                    <input id="tracking_number" name="tracking_number" type="text" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="Contoh: JNE123456789" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                    @error('tracking_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="shipping_note" class="mb-1 block text-sm font-medium text-gray-700">Catatan pengiriman <span class="font-normal text-gray-500">(opsional)</span></label>
                    <input id="shipping_note" name="shipping_note" type="text" value="{{ old('shipping_note', $order->shipping_note) }}" placeholder="Contoh: Paket sudah diserahkan ke kurir" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                    @error('shipping_note')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
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
                    <div class="w-16 h-16 flex-shrink-0 overflow-hidden rounded bg-gray-100">
                        @if($item->product->primaryImage)
                            <img
                                src="{{ $item->product->primaryImage->image_path }}"
                                alt="{{ $item->product->name }}"
                                loading="lazy"
                                decoding="async"
                                class="h-full w-full object-cover"
                            >
                        @else
                            <div class="flex h-full w-full items-center justify-center text-gray-400" aria-label="Gambar produk tidak tersedia">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5 8.25 11.25a2.25 2.25 0 0 1 3.182 0L16.5 16.5m-2.25-2.25 1.318-1.318a2.25 2.25 0 0 1 3.182 0L21 16.5M5.25 4.5h13.5A2.25 2.25 0 0 1 21 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 17.25V6.75A2.25 2.25 0 0 1 5.25 4.5Zm4.5 4.125h.008v.008H9.75v-.008Z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
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
                    <div class="border-t pt-2 font-semibold flex justify-between"><span>Total</span><span>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span></div>
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
                    @if ($order->tracking_number)
                        <p class="mt-1 text-sm text-gray-600">Nomor resi: <span class="font-medium text-gray-800">{{ $order->tracking_number }}</span></p>
                    @endif
                    @if ($order->shipping_note)
                        <p class="mt-1 text-sm text-gray-600">Catatan pengiriman: {{ $order->shipping_note }}</p>
                    @endif
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
