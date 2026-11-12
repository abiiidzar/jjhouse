<div>

    @if($cartItems->count() > 0)

        <!-- HEADER -->
        <div class="p-4 border-b text-xs uppercase tracking-widest text-gray-500">
            Cart ({{ $itemCount }})
        </div>

        <!-- ITEMS -->
        <div class="max-h-80 overflow-y-auto">

            @foreach($cartItems as $item)
                <div class="flex items-center gap-3 p-4 border-b">

                    <div class="w-14 h-14 bg-gray-100">
                        @if($item->product->primaryImage)
                            <img src="{{ $item->product->primaryImage->image_path }}"
                                 loading="lazy"
                                 decoding="async"
                                 class="w-full h-full object-cover">
                        @endif
                    </div>

                    <div class="flex-1 text-sm">
                        <p>{{ $item->product->name }}</p>
                        <p class="text-gray-400 text-xs">
                            Qty {{ $item->quantity }} × Rp {{ number_format($item->product->price, 0, ',', '.') }}
                        </p>
                    </div>

                    <button wire:click="removeItem({{ $item->id }})"
                            aria-label="Hapus item"
                            class="text-gray-400 hover:text-black inline-flex items-center justify-center">
                        <x-icon name="x-mark" class="w-4 h-4" />
                    </button>

                </div>
            @endforeach

        </div>

        <!-- FOOTER -->
        <div class="p-4 space-y-4">

            <div class="flex justify-between text-sm">
                <span>Subtotal</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>

            <a href="{{ route('cart') }}"
               class="block text-center border py-3 text-xs uppercase tracking-widest hover:bg-black hover:text-white">
                View Cart
            </a>

            {{-- <a href="{{ route('checkout') }}"
               class="block text-center bg-black text-white py-3 text-xs uppercase tracking-widest">
                Checkout
            </a> --}}
            <a href="{{ route('checkout') }}"
               class="block text-center border py-3 text-xs uppercase tracking-widest hover:bg-black hover:text-white">
                Checkout
            </a>

        </div>

    @else

        <div class="p-10 text-center text-gray-400">
            Your cart is empty
        </div>

    @endif

</div>
