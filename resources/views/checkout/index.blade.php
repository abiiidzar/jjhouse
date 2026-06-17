@extends('layouts.app2')

@section('title', 'Checkout')

@section('content')

<section data-theme="light" class="bg-white min-h-screen">

    <div class="max-w-7xl mx-auto grid md:grid-cols-2">

        <!-- ================= LEFT ================= -->
        <div class="px-10 py-16">

            <!-- CONTACT -->
            <div class="mb-10">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-medium">Contact</h2>
                    @guest
                        <a href="{{ route('login') }}" class="text-blue-600 text-sm">
                            Sign in
                        </a>
                    @endguest
                </div>

                <input type="email"
                       value="{{ auth()->user()->email ?? '' }}"
                       class="w-full border rounded-lg p-3 text-sm"
                       placeholder="Email">
            </div>

            <!-- DELIVERY -->
            <div class="mb-10">
                <h2 class="font-medium mb-4">Delivery</h2>

                <form action="{{ route('checkout.process') }}" method="POST">
                    @csrf

                    <select name="country"
                            class="w-full border rounded-lg p-3 text-sm mb-4">
                        <option>Indonesia</option>
                    </select>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <input type="text" name="name" id="name_field"
                               value="{{ auth()->user()->name ?? '' }}"
                               placeholder="Full Name"
                               class="border rounded-lg p-3 text-sm col-span-2" required>
                    </div>

                    <input type="text" name="shipping_address" id="address_field"
                           value="{{ auth()->user()->address ?? '' }}"
                           placeholder="Address"
                           class="w-full border rounded-lg p-3 text-sm mb-4" required>

                    <input type="text" name="address2" placeholder="Apartment, suite, etc. (optional)"
                           class="w-full border rounded-lg p-3 text-sm mb-4">

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <input type="text" name="city" id="city_field"
                               value="{{ auth()->user()->city ?? '' }}"
                               placeholder="City"
                               class="border rounded-lg p-3 text-sm">
                        <input type="text" name="province" id="province_field"
                               value="{{ auth()->user()->province ?? '' }}"
                               placeholder="Province"
                               class="border rounded-lg p-3 text-sm">
                        <input type="text" name="postal_code" id="postal_code_field"
                               value="{{ auth()->user()->postal_code ?? '' }}"
                               placeholder="Postal code"
                               class="border rounded-lg p-3 text-sm">
                    </div>

                    <input type="text" name="phone" id="phone_field"
                           value="{{ auth()->user()->phone ?? '' }}"
                           placeholder="Phone"
                           class="w-full border rounded-lg p-3 text-sm mb-6" required>

                    <!-- SHIPPING CALCULATOR -->
                    <livewire:shipping-calculator />

                    <!-- Hidden shipping fields for form submission -->
                    <input type="hidden" name="shipping_courier" id="shipping_courier" value="">
                    <input type="hidden" name="shipping_service" id="shipping_service" value="">
                    <input type="hidden" name="shipping_cost" id="shipping_cost_input" value="">

                    <!-- PAYMENT -->
                    <div class="mb-10">
                        <h2 class="font-medium mb-2">Payment</h2>
                        <p class="text-sm text-gray-500 mb-4">
                            All transactions are secure and encrypted.
                        </p>

                        <div class="border rounded-lg p-4 mb-3 flex items-center justify-between">
                            <label class="flex items-center gap-3">
                                <input type="radio" name="payment_method" value="midtrans" checked required>
                                Pay with Card
                            </label>
                            <span class="text-sm text-gray-500">Visa / Mastercard</span>
                        </div>

                        <div class="border rounded-lg p-4 flex items-center justify-between">
                            <label class="flex items-center gap-3">
                                <input type="radio" name="payment_method" value="bank_transfer" required>
                                Bank Transfer
                            </label>
                        </div>
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

                        <div class="border rounded-lg p-4">
                            <label class="flex items-center gap-3">
                                <input type="radio" name="billing">
                                Use a different billing address
                            </label>
                        </div>
                    </div>

                    <!-- SUBMIT -->
                    <button type="submit"
                            class="w-full bg-blue-600 text-white py-4 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                        Pay now
                    </button>

                </form>
            </div>

        </div>

        <!-- ================= RIGHT ================= -->
        <div class="bg-gray-50 px-10 py-16 border-l sticky top-0 h-screen overflow-y-auto pr-6">

            <!-- CART ITEMS -->
            <div class="space-y-6 mb-6">

                @foreach($cartItems as $item)
                <div class="flex gap-4">

                    <div class="w-16 h-16 bg-gray-200 rounded overflow-hidden relative">
                        @if($item->product->primaryImage)
                        <img src="{{ $item->product->primaryImage->image_path }}"
                             class="w-full h-full object-cover">
                        @endif

                        <span class="absolute -top-2 -right-2 bg-black text-white text-xs w-5 h-5 flex items-center justify-center rounded-full">
                            {{ $item->quantity }}
                        </span>
                    </div>

                    <div class="flex-1 text-sm">
                        <p>{{ $item->product->name }}</p>
                        <p class="text-gray-400">
                            {{ $item->size?->size }}
                        </p>
                    </div>

                    <div class="text-sm">
                        Rp {{ number_format($item->product->price, 0, ',', '.') }}
                    </div>

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
                    <span id="shipping-display">{{ $shippingCost > 0 ? 'Rp ' . number_format($shippingCost, 0, ',', '.') : 'Calculating...' }}</span>
                </div>

                <div class="flex justify-between text-gray-400">
                    <span>Discount</span>
                    <span id="discount-display">{{ number_format($discount, 0, ',', '.') }}</span>
                </div>
                <div class="border-t pt-4 flex justify-between font-medium text-base">
                    <span>Total</span>
                    <span id="total-display">Rp {{ number_format($subtotal + $shippingCost - $discount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- JavaScript to update totals when shipping is selected -->
            <script>
                // Listen for Livewire event
                document.addEventListener('shippingUpdated', function(event) {
                    const detail = event.detail;
                    if (detail && detail[0]) {
                        const shippingCost = detail[0].shipping_cost;
                        const total = detail[0].total;

                        document.getElementById('shipping-display').textContent =
                            'Rp ' + new Intl.NumberFormat('id-ID').format(shippingCost);
                        document.getElementById('discount-display').textContent =
                            'Rp ' + new Intl.NumberFormat('id-ID').format(result.discount);
                        document.getElementById('total-display').textContent =
                            'Rp ' + new Intl.NumberFormat('id-ID').format(total);

                        // Update hidden fields
                        document.getElementById('shipping_cost_input').value = shippingCost;
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
                            alert('Kode diskon berhasil diterapkan! Diskon: Rp ' +
                                new Intl.NumberFormat('id-ID').format(result.discount));
                            // Update total with discount
                            const subtotal = {{ $subtotal }};
                            const shipping = parseFloat(document.getElementById('shipping_cost_input').value) || 0;
                            const newTotal = subtotal + shipping - result.discount;
                            document.getElementById('total-display').textContent =
                                'Rp ' + new Intl.NumberFormat('id-ID').format(newTotal);
                        } else {
                            alert(result.message || 'Kode diskon tidak valid');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat memvalidasi kode diskon');
                    }
                }
            </script>

        </div>

    </div>

</section>

@endsection
