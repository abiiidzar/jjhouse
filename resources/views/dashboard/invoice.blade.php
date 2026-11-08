@extends('layouts.app')

@section('title', 'Invoice')

@section('content')

<section data-theme="light" class="px-10 py-20 bg-white min-h-screen">

    <div class="max-w-4xl mx-auto border p-10">

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-10">

            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    JJHOUSE
                </h1>
                <p class="text-sm text-gray-400">
                    Fashion Store
                </p>
            </div>

            <div class="text-right text-sm">
                <p class="font-medium">Invoice #{{ $order->id }}</p>
                <p class="text-gray-400">
                    {{ $order->created_at->format('d M Y') }}
                </p>
            </div>

        </div>

        <!-- CUSTOMER -->
        <div class="grid md:grid-cols-2 gap-10 mb-10 text-sm">

            <div>
                <p class="uppercase tracking-widest text-xs mb-2 text-gray-400">Bill To</p>
                <p class="font-medium">{{ $order->user->name }}</p>
                <p>{{ $order->user->email }}</p>
            </div>

            <div>
                <p class="uppercase tracking-widest text-xs mb-2 text-gray-400">Shipping Address</p>
                <p>{{ $order->shipping_address ?? '-' }}</p>
            </div>

        </div>

        <!-- ITEMS -->
        <div class="border-t pt-6">

            <div class="grid grid-cols-4 text-xs uppercase tracking-widest text-gray-400 mb-4">
                <span>Product</span>
                <span>Size</span>
                <span class="text-center">Qty</span>
                <span class="text-right">Total</span>
            </div>

            @foreach($order->items as $item)
            <div class="grid grid-cols-4 text-sm py-4 border-b">

                <div>
                    {{ $item->product->name }}
                </div>

                <div>
                    {{ $item->size?->size ?? '-' }}
                </div>

                <div class="text-center">
                    {{ $item->quantity }}
                </div>

                <div class="text-right">
                    Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                </div>

            </div>
            @endforeach

        </div>

        <!-- TOTAL -->
        <div class="mt-10 text-sm">

            <div class="flex justify-between mb-2">
                <span>Subtotal</span>
                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>

            <div class="flex justify-between mb-2 text-gray-400">
                <span>Shipping</span>
                <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
            </div>

            <div class="flex justify-between border-t pt-4 font-medium text-base">
                <span>Total</span>
                <span>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
            </div>

        </div>

        <!-- STATUS -->
        <div class="mt-10 text-sm">
            <p>
                Status:
                <span class="px-3 py-1 rounded-full text-xs
                    {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </p>
        </div>

        <!-- ACTION -->
        <div class="mt-10 flex justify-between">

            <a href="{{ route('dashboard') }}"
               class="text-xs uppercase tracking-widest border px-6 py-3 hover:bg-black hover:text-white transition">
                Back
            </a>

            <button onclick="window.print()"
                    class="text-xs uppercase tracking-widest bg-black text-white px-6 py-3 hover:opacity-80 transition">
                Print Invoice
            </button>

        </div>

    </div>

</section>

@endsection