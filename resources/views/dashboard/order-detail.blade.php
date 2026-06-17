@extends('layouts.app')

@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        @include('dashboard.sidebar')

        <div class="lg:col-span-3">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Order #{{ $order->order_number }}</h1>
                <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-black">← Back to Orders</a>
            </div>

            <!-- Order Status Timeline -->
            <div class="bg-white border border-gray-100 rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-sm text-gray-500">Order Status</span>
                    <span class="px-3 py-1 text-sm rounded-full
                        @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($order->status == 'paid') bg-blue-100 text-blue-800
                        @elseif($order->status == 'processing') bg-indigo-100 text-indigo-800
                        @elseif($order->status == 'shipped') bg-purple-100 text-purple-800
                        @elseif($order->status == 'completed') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex-1">
                        <div class="h-2 bg-gray-200 rounded-full">
                            <div class="h-2 bg-black rounded-full" style="width:
                                @switch($order->status)
                                    @case('pending') 0% @break
                                    @case('paid') 25% @break
                                    @case('processing') 50% @break
                                    @case('shipped') 75% @break
                                    @case('completed') 100% @break
                                    @default 0%
                                @endswitch
                            "></div>
                        </div>
                        <div class="flex justify-between mt-2 text-xs text-gray-500">
                            <span>Order Placed</span>
                            <span>Paid</span>
                            <span>Processing</span>
                            <span>Shipped</span>
                            <span>Delivered</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white border border-gray-100 rounded-lg overflow-hidden mb-6">
                <div class="p-4 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold">Order Items</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                    <div class="p-4 flex flex-wrap gap-4 items-center">
                        <div class="w-20 h-20 bg-gray-100 rounded"></div>
                        <div class="flex-1">
                            <p class="font-medium">{{ $item->product->name }}</p>
                            <p class="text-sm text-gray-500">Size: {{ $item->size->size ?? '-' }} | Qty: {{ $item->quantity }}</p>
                            @if($item->is_preorder)
                            <span class="inline-block text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded mt-1">
                                Pre-order
                                @if($item->preorder_payment_type == 'dp')
                                (DP 50%)
                                @endif
                            </span>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="font-semibold">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                            @if($item->is_preorder && $item->remaining_payment > 0)
                            <a href="{{ route('order.complete-payment', [$order, $item]) }}" class="text-sm text-blue-600 hover:text-blue-800 mt-1 inline-block">
                                Complete Payment →
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Shipping Info -->
                <div class="bg-white border border-gray-100 rounded-lg p-6">
                    <h3 class="font-semibold mb-3">Shipping Information</h3>
                    <p class="text-sm text-gray-700">{{ $order->shipping_address }}</p>
                    <p class="text-sm text-gray-500 mt-2">Courier: {{ strtoupper($order->shipping_courier) }}</p>
                </div>

                <!-- Payment Summary -->
                <div class="bg-white border border-gray-100 rounded-lg p-6">
                    <h3 class="font-semibold mb-3">Payment Summary</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Subtotal</span>
                            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Shipping</span>
                            <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t pt-2 flex justify-between font-semibold">
                            <span>Total</span>
                            <span>Rp {{ number_format($order->total_amount + $order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Payment Status</span>
                            <span class="capitalize">{{ $order->payment_status }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Payment Method</span>
                            <span class="capitalize">{{ $order->payment_method }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Button -->
            <div class="mt-6 text-right">
                <a href="{{ route('dashboard.invoice', $order) }}" target="_blank" class="inline-flex items-center gap-2 text-gray-600 hover:text-black">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download Invoice
                </a>
            </div>
        </div>
    </div>
    
</div>
@endsection
