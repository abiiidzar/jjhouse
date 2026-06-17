<div class="cart-page">
    @if(session('message'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <h4 class="mb-4">Shopping Cart</h4>

            @if($items->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product->name }}</strong><br>
                                        <small class="text-muted">Size: {{ $item->size->size ?? 'N/A' }}</small>
                                    </td>
                                    <td>${{ number_format($item->product->price, 2) }}</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            class="form-control form-control-sm" 
                                            style="width: 70px;"
                                            value="{{ $item->quantity }}"
                                            wire:change="updateQuantity({{ $item->id }}, $event.target.value)"
                                            min="1"
                                            max="99"
                                        >
                                    </td>
                                    <td>${{ number_format($item->product->price * $item->quantity, 2) }}</td>
                                    <td>
                                        <button 
                                            class="btn btn-sm btn-outline-secondary"
                                            wire:click="moveToWishlist({{ $item->id }})"
                                            title="Move to Wishlist"
                                        >
                                            <i class="fas fa-heart"></i>
                                        </button>
                                        <button 
                                            class="btn btn-sm btn-outline-danger"
                                            wire:click="removeItem({{ $item->id }})"
                                            title="Remove"
                                        >
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    Your cart is empty. <a href="{{ route('products') }}">Continue shopping</a>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Order Summary</h5>

                    <div class="mb-3">
                        <label class="form-label">Province</label>
                        <select class="form-select" wire:model.live="selectedProvince">
                            <option value="">Select Province</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province['province_id'] }}">{{ $province['province'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($cities)
                        <div class="mb-3">
                            <label class="form-label">City</label>
                            <select class="form-select" wire:model.live="selectedCity">
                                <option value="">Select City</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city['city_id'] }}">{{ $city['city_name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Courier</label>
                        <select class="form-select" wire:model.live="selectedCourier">
                            @foreach($couriers as $courier)
                                <option value="{{ $courier }}">{{ strtoupper($courier) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <hr>

                    <div class="mb-2 d-flex justify-content-between">
                        <span>Subtotal:</span>
                        <strong>${{ number_format($subtotal, 2) }}</strong>
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span>Shipping:</span>
                        <strong>${{ number_format($shippingCost, 2) }}</strong>
                    </div>

                    <hr>

                    <div class="mb-4 d-flex justify-content-between h5">
                        <span>Total:</span>
                        <strong>${{ number_format($total, 2) }}</strong>
                    </div>

                    <button 
                        class="btn btn-primary w-100"
                        wire:click="proceedToCheckout"
                        @if($items->count() === 0) disabled @endif
                    >
                        Proceed to Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
