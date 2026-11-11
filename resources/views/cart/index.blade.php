@extends('layouts.app2')

@section('title', 'Your Cart')

@section('content')

<section data-theme="light" class="px-10 py-24">

    <div class="max-w-6xl mx-auto">

        <!-- TITLE -->
        <h1 class="text-2xl md:text-3xl uppercase tracking-[0.2em] mb-16">
            Your Cart
        </h1>


        @if($cartItems->count() > 0)

        <!-- CART TABLE -->
        <div class="grid md:grid-cols-3 gap-12">

            <!-- LEFT: ITEMS -->
            <div class="md:col-span-2 space-y-10">

                @foreach($cartItems as $item)

                <div class="flex gap-6 border-b pb-6">

                    <!-- IMAGE -->
                    <div class="w-28 h-28 bg-gray-100 overflow-hidden">
                        @if($item->product->primaryImage)
                            <img src="{{ $item->product->primaryImage->image_path }}"
                                 alt="{{ $item->product->name }}"
                                 width="112"
                                 height="112"
                                 loading="lazy"
                                 decoding="async"
                                 class="w-full h-full object-cover">
                        @endif
                    </div>

                    <!-- INFO -->
                    <div class="flex-1 flex flex-col justify-between">

                        <div>
                            <p class="text-sm uppercase tracking-widest">
                                {{ $item->product->name }}
                            </p>

                            <p class="text-gray-400 text-sm mt-1">
                                Size: {{ $item->size->size ?? '-' }}
                            </p>

                            <p class="text-gray-400 text-sm mt-1">
                                Rp {{ number_format($item->product->price, 0, ',', '.') }}
                            </p>
                        </div>

                        <!-- ACTION -->
                        <div class="flex items-center justify-between mt-4">

                            <!-- QTY -->
                            <div class="flex items-center border">
                                <form method="POST" action="{{ route('cart.items.decrease', $item->id) }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        aria-label="Decrease quantity of {{ $item->product->name }}"
                                        @disabled($item->quantity <= 1)
                                        class="px-3 py-2 hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-black disabled:cursor-not-allowed disabled:opacity-40"
                                    >−</button>
                                </form>

                                <span class="min-w-12 px-4 text-center text-sm tabular-nums" aria-live="polite">
                                    {{ $item->quantity }}
                                </span>

                                <form method="POST" action="{{ route('cart.items.increase', $item->id) }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        aria-label="Increase quantity of {{ $item->product->name }}"
                                        @disabled($item->quantity >= 99)
                                        class="px-3 py-2 hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-black disabled:cursor-not-allowed disabled:opacity-40"
                                    >+</button>
                                </form>

                            </div>

                            <!-- REMOVE -->
                            <form method="POST" action="{{ route('cart.items.remove', $item->id) }}">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="text-xs uppercase tracking-widest text-gray-400 hover:text-black focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black"
                                >
                                    Remove
                                </button>
                            </form>

                        </div>

                    </div>

                    <!-- SUBTOTAL -->
                    <div class="text-sm">
                        Rp {{ number_format($item->quantity * $item->product->price, 0, ',', '.') }}
                    </div>

                </div>

                @endforeach

            </div>


            <!-- RIGHT: SUMMARY -->
            <div class="border p-6 h-fit sticky top-32">

                <p class="text-xs uppercase tracking-widest mb-6">
                    Summary
                </p>

                <div class="flex justify-between text-sm mb-4">
                    <span>Subtotal</span>
                    <span>
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex justify-between text-sm mb-6">
                    <span>Shipping</span>
                    <span class="text-gray-400">
                        Calculated at checkout
                    </span>
                </div>

                <div class="border-t pt-4 flex justify-between text-sm font-medium">
                    <span>Total</span>
                    <span>
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </span>
                </div>

                <!-- ACTION -->
                <div class="mt-8 space-y-3">

                    <a href="{{ route('checkout') }}"
                       class="block text-center bg-black text-white py-3 uppercase tracking-widest text-xs hover:opacity-80 transition">
                        Checkout
                    </a>

                    <a href="{{ route('shop') }}"
                       class="block text-center border py-3 uppercase tracking-widest text-xs hover:bg-black hover:text-white transition">
                        Continue Shopping
                    </a>

                </div>

            </div>

        </div>


        @else

        <!-- EMPTY -->
        <div class="text-center py-40">

            <p class="text-gray-400 mb-6">
                Your cart is empty
            </p>

            <a href="{{ route('shop') }}"
               class="border px-6 py-3 uppercase tracking-widest text-xs hover:bg-black hover:text-white transition">
                Shop Now
            </a>

        </div>

        @endif

    </div>

</section>

@endsection
