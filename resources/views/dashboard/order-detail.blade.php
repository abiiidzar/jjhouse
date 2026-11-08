@extends('layouts.app')

@section('title', 'Order #' . $order->order_number)

@section('content')
<div data-theme="light" class="container mx-auto max-w-7xl px-4 pt-24 pb-8 md:pt-28 md:pb-12">
    <div data-sidebar-layout data-customer-sidebar-layout data-sidebar-key="customer-dashboard" data-sidebar-desktop-breakpoint="1024" data-sidebar-lock-scroll class="relative flex flex-col items-start lg:flex-row">
        <div data-sidebar-overlay aria-hidden="true"></div>
        @include('dashboard.sidebar')

        <div class="w-full min-w-0 flex-1 lg:w-auto">
            <!-- Header -->
            <div class="flex flex-wrap justify-between items-center gap-4 mb-8">
                <div class="flex items-start gap-3">
                    <x-dashboard-sidebar-toggle />
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Order #{{ $order->order_number }}</h1>
                        <p class="text-gray-500 text-sm mt-1">
                            Placed on {{ $order->created_at->format('F d, Y') }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-black transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Orders
                </a>
            </div>

            @if(session('error'))
                <div role="alert" class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('warning'))
                <div role="status" class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    {{ session('warning') }}
                </div>
            @endif
            @if(session('success'))
                <div role="status" class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900">
                    {{ session('success') }}
                </div>
            @endif

            <!-- 🔥 PAYMENT STATUS & RETRY BUTTON -->
            @if($order->payment_status == 'unpaid' || $order->payment_status == 'failed' || $order->status == 'pending')
            <div class="mb-8 rounded-xl overflow-hidden border
                {{ $order->payment_status == 'failed' ? 'border-red-200 bg-red-50' : 'border-yellow-200 bg-yellow-50' }}">
                <div class="p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                {{ $order->payment_status == 'failed' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-600' }}">
                                @if($order->payment_status == 'failed')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">
                                    @if($order->payment_status == 'failed')
                                        Pembayaran Gagal
                                    @else
                                        Menunggu Pembayaran
                                    @endif
                                </h3>
                                <p class="text-sm text-gray-600 mt-0.5">
                                    @if($order->payment_status == 'failed')
                                        Pembayaran Anda gagal diproses. Silakan coba lagi.
                                    @else
                                        Selesaikan pembayaran Anda untuk memproses pesanan.
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3 flex-shrink-0">
                            <!-- Tombol Bayar Ulang -->
                            <form action="{{ route('order.retry-payment', $order) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 transition-all duration-200 text-sm font-medium shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Bayar Sekarang
                                </button>
                            </form>

                            <!-- Tombol Batal Order -->
                            @if($order->status == 'pending')
                            <form action="{{ route('order.cancel', $order) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                @csrf @method('PUT')
                                <button type="submit"
                                        class="inline-flex items-center gap-2 border border-red-300 text-red-600 px-4 py-2.5 rounded-lg hover:bg-red-50 transition-all duration-200 text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Batalkan Pesanan
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-yellow-200/50">
                        <div class="flex flex-wrap items-center gap-4 text-sm">
                            <span class="text-gray-600">Total yang harus dibayar:</span>
                            <span class="font-bold text-gray-900 text-lg">
                                Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                            </span>
                            <span class="text-xs text-gray-400 bg-white/50 px-3 py-1 rounded-full">
                                {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- 🔥 PRE-ORDER REMAINING PAYMENT -->
            @if($order->items->where('is_preorder', true)->where('remaining_payment', '>', 0)->count() > 0)
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-800">Pelunasan Pre-order</h3>
                        <p class="text-sm text-gray-600 mt-0.5">
                            Terdapat pre-order yang memerlukan pelunasan untuk proses pengiriman.
                        </p>
                    </div>
                </div>
                <div class="mt-4 space-y-3">
                    @foreach($order->items as $item)
                        @if($item->is_preorder && $item->remaining_payment > 0)
                        <div class="flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-xl border border-blue-100">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                    @if($item->product->primaryImage)
                                        <img src="{{ $item->product->primaryImage->image_path }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-200"></div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $item->product->name }}</p>
                                    <p class="text-sm text-gray-500">
                                        Sisa pembayaran:
                                        <strong class="text-gray-700">Rp {{ number_format($item->remaining_payment, 0, ',', '.') }}</strong>
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('order.complete-payment', [$order, $item]) }}"
                               class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition-all duration-200 whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Lunasi Sekarang
                            </a>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Order Status Timeline -->
            <div class="bg-white border border-gray-100 rounded-xl p-6 mb-8 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-sm font-medium text-gray-500">Order Status</span>
                    <span class="px-3 py-1 text-sm rounded-full font-medium
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
                <div class="relative">
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-black rounded-full transition-all duration-500" style="width:
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
                    <div class="grid grid-cols-5 gap-2 mt-3 text-center">
                        <div>
                            <span class="text-xs font-medium text-gray-500">Placed</span>
                            <div class="w-2 h-2 mx-auto mt-1 rounded-full {{ $order->status != 'pending' ? 'bg-black' : 'bg-gray-300' }}"></div>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-gray-500">Paid</span>
                            <div class="w-2 h-2 mx-auto mt-1 rounded-full {{ in_array($order->status, ['paid', 'processing', 'shipped', 'completed']) ? 'bg-black' : 'bg-gray-300' }}"></div>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-gray-500">Processing</span>
                            <div class="w-2 h-2 mx-auto mt-1 rounded-full {{ in_array($order->status, ['processing', 'shipped', 'completed']) ? 'bg-black' : 'bg-gray-300' }}"></div>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-gray-500">Shipped</span>
                            <div class="w-2 h-2 mx-auto mt-1 rounded-full {{ in_array($order->status, ['shipped', 'completed']) ? 'bg-black' : 'bg-gray-300' }}"></div>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-gray-500">Delivered</span>
                            <div class="w-2 h-2 mx-auto mt-1 rounded-full {{ $order->status == 'completed' ? 'bg-black' : 'bg-gray-300' }}"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white border border-gray-100 rounded-xl overflow-hidden mb-8 shadow-sm">
                <div class="p-4 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Order Items</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                    <div class="p-4 flex flex-wrap gap-4 items-center hover:bg-gray-50 transition-colors">
                        <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                            @if($item->product->primaryImage)
                                <img src="{{ $item->product->primaryImage->image_path }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400 text-xs">
                                    No image
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-[150px]">
                            <p class="font-medium text-gray-800">{{ $item->product->name }}</p>
                            <p class="text-sm text-gray-500">
                                Size: {{ $item->size->size ?? '-' }}
                                <span class="mx-1">•</span>
                                Qty: {{ $item->quantity }}
                            </p>
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
                            <p class="font-semibold text-gray-800">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                            @if($item->is_preorder && $item->remaining_payment > 0)
                            <a href="{{ route('order.complete-payment', [$order, $item]) }}"
                               class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 mt-1 transition-colors">
                                Complete Payment
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Shipping Info -->
                <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-800">Shipping Information</h3>
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $order->shipping_address }}</p>
                    <p class="text-sm text-gray-500 mt-3">
                        <span class="font-medium">Courier:</span> {{ strtoupper($order->shipping_courier ?? '-') }}
                    </p>
                    @if($order->tracking_number)
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="font-medium">Tracking:</span> {{ $order->tracking_number }}
                    </p>
                    @endif
                    @if($order->shipping_note)
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="font-medium">Catatan pengiriman:</span> {{ $order->shipping_note }}
                    </p>
                    @endif
                </div>

                <!-- Payment Summary -->
                <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-800">Payment Summary</h3>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="text-gray-700">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Shipping</span>
                            <span class="text-gray-700">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        @if($order->discount_amount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Discount</span>
                            <span>-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="border-t pt-2 flex justify-between font-semibold text-gray-900">
                            <span>Total</span>
                            <span>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Payment Status</span>
                            <span class="capitalize font-medium {{ $order->payment_status == 'paid' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $order->payment_status }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Payment Method</span>
                            <span class="capitalize text-gray-700">{{ $order->payment_method }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex flex-wrap justify-between items-center gap-4">
                <a href="{{ route('dashboard.invoice', $order) }}" target="_blank"
                   class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-black transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download Invoice
                </a>

                @if($order->status == 'pending' || $order->payment_status == 'unpaid')
                <form action="{{ route('order.cancel', $order) }}" method="POST"
                      onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                    @csrf @method('PUT')
                    <button type="submit"
                            class="text-sm text-red-500 hover:text-red-700 transition-colors">
                        Batalkan Pesanan
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
