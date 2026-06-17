<div x-data="{ showLoginModal: false }" class="mt-10 space-y-6">

    <!-- SUCCESS MESSAGE -->
    @if($showSuccess)
        <div class="text-sm text-green-600 tracking-wide">
            ✓ Product added to cart
        </div>
    @endif


    <!-- SIZE SELECT -->
    <div>
        <p class="text-xs uppercase tracking-widest mb-3">
            Size
        </p>

        <div class="flex gap-3 flex-wrap">

            @foreach($product->sizes as $size)
                <div class="flex flex-col items-center">

            <button
                wire:click="$set('selectedSize', {{ $size->id }})"
                class="px-4 py-2 border text-sm transition
                    {{ $selectedSize == $size->id ? 'bg-black text-white' : 'hover:bg-black hover:text-white' }}
                "
            >
                {{ $size->size }}
            </button>

            <!-- STOCK -->
            <span class="text-xs text-gray-400 mt-1">
                Stok: {{ $size->stock }}
            </span>

        </div>
            @endforeach

        </div>
    </div>


    <!-- QUANTITY -->
    <div class="flex items-center gap-4">

        <p class="text-xs uppercase tracking-widest">
            Qty
        </p>

        <div class="flex items-center border">

            <button
                type="button"
                wire:click="$set('quantity', {{ max(1, $quantity - 1) }})"
                class="px-3 py-2 hover:bg-gray-100"
            >
                −
            </button>

            <input
                type="number"
                wire:model="quantity"
                class="w-12 text-center border-x outline-none"
                min="1"
            >

            <button
                type="button"
                wire:click="$set('quantity', {{ $quantity + 1 }})"
                class="px-3 py-2 hover:bg-gray-100"
            >
                +
            </button>

        </div>

    </div>


    <!-- ADD TO CART BUTTON -->
    <button
        @auth
            wire:click="addToCart"
        @else
            @click="showLoginModal = true"
        @endauth
        wire:loading.attr="disabled"
        class="w-full border py-4 uppercase tracking-widest text-sm transition
               hover:bg-black hover:text-white"
    >

        <span wire:loading.remove>
            Add to Cart
        </span>

        <span wire:loading>
            Adding...
        </span>

    </button>
    <!-- 🔥 LOGIN MODAL -->
    <div
        x-show="showLoginModal"
        x-transition
        x-cloak
        class="fixed inset-0 bg-black/60 flex items-center justify-center z-50"
    >
    <div
            @click.away="showLoginModal = false"
            class="bg-white p-10 w-[350px] text-center space-y-6"
        >

            <p class="text-sm uppercase tracking-widest">
                Login Required
            </p>

            <p class="text-gray-500 text-sm">
                Please login to add items to your cart
            </p>

            <div class="space-y-3">

                <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                   class="block bg-black text-white py-3 uppercase text-xs tracking-widest">
                    Login
                </a>

                <button
                    @click="showLoginModal = false"
                    class="text-xs uppercase tracking-widest text-gray-400 hover:text-black"
                >
                    Cancel
                </button>
                <p class="mt-8 text-xs text-gray-500">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-black hover:underline">
                        Register
                    </a>
                </p>


            </div>

        </div>

    </div>

</div>
