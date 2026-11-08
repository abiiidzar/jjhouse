@extends('layouts.app2')

@section('title', 'Checkout')

@section('content')
<style>
    #checkout-form [data-payment-option]:has(input:checked) {
        border-color: #111827;
        background-color: #f9fafb;
    }
</style>

<section data-theme="light" class="bg-white min-h-screen">
    <div class="max-w-7xl mx-auto grid md:grid-cols-2">
        <!-- LEFT -->
        <div class="px-10 py-16">
            <!-- CONTACT -->
            <div class="mb-10">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-medium">Contact</h2>
                    @guest
                        <a href="{{ route('login') }}" class="text-blue-600 text-sm">Sign in</a>
                    @endguest
                </div>
                <input type="email" value="{{ auth()->user()->email ?? '' }}"
                       class="w-full border rounded-lg p-3 text-sm bg-gray-50" readonly>
            </div>

            <!-- DELIVERY -->
            <div class="mb-10">
                <h2 class="font-medium mb-4">Delivery</h2>

                <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
                    @csrf

                    <select name="country" class="w-full border rounded-lg p-3 text-sm mb-4">
                        <option>Indonesia</option>
                    </select>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <input type="text" name="name" value="{{ auth()->user()->name ?? '' }}"
                               placeholder="Full Name" required
                               class="border rounded-lg p-3 text-sm col-span-2">
                    </div>

                    <input type="text" name="shipping_address" value="{{ auth()->user()->address ?? '' }}"
                           placeholder="Address" required
                           class="w-full border rounded-lg p-3 text-sm mb-4">

                    <input type="text" name="address2" placeholder="Apartment, suite, etc. (optional)"
                           class="w-full border rounded-lg p-3 text-sm mb-4">

                    <input type="text" name="phone" value="{{ auth()->user()->phone ?? '' }}"
                           placeholder="Phone" required
                           class="w-full border rounded-lg p-3 text-sm mb-6">

                    <!-- SHIPPING CALCULATOR -->
                    <livewire:shipping-calculator />

                    <!-- Hidden fields for shipping -->
                    <input type="hidden" name="shipping_courier" id="shipping_courier" value="">
                    <input type="hidden" name="shipping_service" id="shipping_service" value="">
                    <input type="hidden" name="shipping_cost" id="shipping_cost_input" value="0">
                    <input type="hidden" name="city" id="city_hidden" value="">
                    <input type="hidden" name="province" id="province_hidden" value="">
                    <input type="hidden" name="postal_code" id="postal_code_hidden" value="">

                    <!-- PAYMENT -->
                    <div class="mb-10">

                        {{-- Payment Header --}}
                        <h2 class="font-medium mb-2">
                            Payment
                        </h2>

                        <p class="text-sm text-gray-500 mb-4">
                            All transactions are secure and encrypted.
                        </p>


                        <label
                            data-payment-option
                            class="border border-gray-200 bg-white rounded-lg p-4 flex cursor-pointer transition hover:border-gray-400"
                        >
                            <span class="flex items-start gap-3">
                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="midtrans"
                                    {{ old('payment_method', 'midtrans') == 'midtrans' ? 'checked' : '' }}
                                    class="mt-1 h-4 w-4 shrink-0 accent-black focus:ring-gray-900"
                                >
                                <span>
                                    <span class="block font-medium text-sm sm:text-base">All available payment methods</span>
                                    <span class="mt-1 block text-xs sm:text-sm text-gray-600">
                                        Choose from the methods enabled for this store in Midtrans.
                                    </span>
                                    <span class="mt-3 flex flex-wrap gap-2 text-xs text-gray-700">
                                        <span class="rounded border border-gray-200 px-2 py-1">Cards</span>
                                        <span class="rounded border border-gray-200 px-2 py-1">Bank transfer &amp; virtual account</span>
                                        <span class="rounded border border-gray-200 px-2 py-1">E-wallet &amp; QRIS</span>
                                        <span class="rounded border border-gray-200 px-2 py-1">Retail outlets</span>
                                        <span class="rounded border border-gray-200 px-2 py-1">PayLater</span>
                                    </span>
                                </span>
                            </span>
                        </label>


                        {{-- ========================================================= --}}
                        {{-- PRE-ORDER PAYMENT --}}
                        {{-- ========================================================= --}}

                        @if($preorderItems->count() > 0)

                            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">

                                <h4 class="font-medium text-sm text-yellow-800 mb-2">
                                    Pre-order Payment Options
                                </h4>

                                <div class="space-y-3">

                                    {{-- Full Payment --}}
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">

                                        <input
                                            type="radio"
                                            name="preorder_payment_type"
                                            value="full"
                                            {{ old('preorder_payment_type', 'full') == 'full'
                                                ? 'checked'
                                                : '' }}
                                            class="w-4 h-4 text-yellow-600
                                                focus:ring-yellow-500"
                                        >

                                        <span>
                                            Full Payment
                                            <span class="font-medium">
                                                (Rp {{ number_format($subtotal, 0, ',', '.') }})
                                            </span>
                                        </span>

                                    </label>


                                    {{-- DP --}}
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">

                                        <input
                                            type="radio"
                                            name="preorder_payment_type"
                                            value="dp"
                                            {{ old('preorder_payment_type') == 'dp'
                                                ? 'checked'
                                                : '' }}
                                            class="w-4 h-4 text-yellow-600
                                                focus:ring-yellow-500"
                                        >

                                        <span>
                                            DP 50%
                                            <span class="font-medium">
                                                (Rp {{ number_format($subtotal * 0.5, 0, ',', '.') }})
                                            </span>
                                        </span>

                                    </label>


                                    <p class="text-xs text-gray-500">
                                        * Sisa pembayaran akan ditagih saat pre-order ready.
                                    </p>

                                </div>

                            </div>

                        @endif

                    </div>

                    <!-- BILLING -->
                    <div class="mb-10">
                        <h2 class="font-medium mb-4">Billing address</h2>
                        <div class="border rounded-lg p-4 mb-3">
                            <label class="flex items-center gap-3">
                                <input type="radio" name="billing" checked>
                                Same as shipping address
                            </label>
                        </div>
                    </div>

                    <!-- SUBMIT -->
                    <button type="submit" id="submit-btn"
                            class="w-full bg-black text-white py-4 rounded-lg text-sm font-medium hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition">
                        Pay now
                    </button>
                </form>
            </div>
        </div>

        <!-- RIGHT -->
        <div class="bg-gray-50 px-10 py-16 border-l sticky top-0 h-screen overflow-y-auto pr-6">
            <!-- CART ITEMS -->
            <div class="space-y-6 mb-6">
                @foreach($cartItems as $item)
                <div class="flex gap-4">
                    <div class="w-16 h-16 bg-gray-200 rounded overflow-hidden relative">
                        @if($item->product->primaryImage)
                        <img src="{{ $item->product->primaryImage->image_path }}" class="w-full h-full object-cover">
                        @endif
                        <span class="absolute -top-2 -right-2 bg-black text-white text-xs w-5 h-5 flex items-center justify-center rounded-full">
                            {{ $item->quantity }}
                        </span>
                    </div>
                    <div class="flex-1 text-sm">
                        <p>{{ $item->product->name }}</p>
                        <p class="text-gray-400">{{ $item->size?->size }}</p>
                    </div>
                    <div class="text-sm">Rp {{ number_format($item->product->price, 0, ',', '.') }}</div>
                </div>
                @endforeach
            </div>

            <!-- DISCOUNT -->
            <div class="flex gap-2 mb-6">
                <input type="text" id="discount_code" placeholder="Discount code"
                       class="flex-1 border rounded-lg p-3 text-sm">
                <button type="button" onclick="applyDiscount()" class="bg-blue-600 text-white px-4 rounded-lg text-sm hover:bg-blue-700 transition">
                    Apply
                </button>
            </div>

            <!-- TOTAL -->
            <div class="text-sm space-y-2">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-400">
                    <span>Shipping</span>
                    <span id="shipping-display">Rp 0</span>
                </div>
                <div class="flex justify-between text-gray-400">
                    <span>Discount</span>
                    <span id="discount-display">Rp 0</span>
                </div>
                <div class="border-t pt-4 flex justify-between font-medium text-base">
                    <span>Total</span>
                    <span id="total-display">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Listen for shipping updates from Livewire
    document.addEventListener('shippingUpdated', function(event) {
        const detail = event.detail;
        if (detail && detail[0]) {
            const shippingCost = detail[0].shipping_cost || 0;
            const total = detail[0].total || 0;
            const city = detail[0].city || '';
            const province = detail[0].province || '';

            // Update UI
            document.getElementById('shipping-display').textContent =
                'Rp ' + new Intl.NumberFormat('id-ID').format(shippingCost);
            document.getElementById('total-display').textContent =
                'Rp ' + new Intl.NumberFormat('id-ID').format(total);

            // Update hidden fields
            document.getElementById('shipping_cost_input').value = shippingCost;
            document.getElementById('city_hidden').value = city;
            document.getElementById('province_hidden').value = province;
        }
    });

    // Apply discount function
    async function applyDiscount() {
        const code = document.getElementById('discount_code').value.trim();
        if (!code) {
            alert('Masukkan kode diskon');
            return;
        }

        try {
            const response = await fetch('{{ route("discount.validate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ code: code })
            });

            const result = await response.json();
            if (result.success) {
                alert('Kode diskon berhasil diterapkan!');
                const subtotal = {{ $subtotal }};
                const shipping = parseFloat(document.getElementById('shipping_cost_input').value) || 0;
                const newTotal = subtotal + shipping - result.discount;
                document.getElementById('total-display').textContent =
                    'Rp ' + new Intl.NumberFormat('id-ID').format(newTotal);
                document.getElementById('discount-display').textContent =
                    'Rp ' + new Intl.NumberFormat('id-ID').format(result.discount);
            } else {
                alert(result.message || 'Kode diskon tidak valid');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memvalidasi kode diskon');
        }
    }
</script>
@endsection
