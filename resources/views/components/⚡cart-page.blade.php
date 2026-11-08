<?php

namespace App\Http\Livewire;

use App\Models\CartItem;
use App\Models\ProductSize;
use App\Services\RajaOngkirService;
use Livewire\Component;

class CartPage extends Component
{
    public $cart;
    public $items;
    public $provinces = [];
    public $cities = [];
    public $selectedProvince;
    public $selectedCity;
    public $shippingCost = 0;
    public $couriers = [];
    public $selectedCourier = 'jne';

    protected $listeners = ['cartUpdated' => 'loadCart'];

    public function mount()
    {
        $this->couriers = config('shipping.couriers', []);
        $this->loadCart();
        $this->loadProvinces();
    }

    public function loadCart()
    {
        if (auth()->check()) {
            $this->cart = auth()->user()->cart;
            if ($this->cart) {
                $this->items = $this->cart->items()->with('product', 'size')->get();
            } else {
                $this->items = collect();
            }
        }
    }

    public function loadProvinces()
    {
        $rajaOngkir = new RajaOngkirService();
        $this->provinces = $rajaOngkir->getProvinces();
    }

    public function updatedSelectedProvince($provinceId)
    {
        $rajaOngkir = new RajaOngkirService();
        $this->cities = $rajaOngkir->getCities($provinceId);
        $this->selectedCity = null;
        $this->calculateShipping();
    }

    public function updatedSelectedCity()
    {
        $this->calculateShipping();
    }

    public function updatedSelectedCourier()
    {
        $this->calculateShipping();
    }

    public function calculateShipping()
    {
        if ($this->selectedCity && $this->selectedCourier) {
            $rajaOngkir = new RajaOngkirService();
            // Assuming origin is Bukittinggi (city id 495)
            $origin = 495;
            $weight = $this->getTotalWeight();
            $costs = $rajaOngkir->calculateCost($origin, $this->selectedCity, $weight, $this->selectedCourier);
            if (!empty($costs)) {
                $this->shippingCost = $costs[0]['cost'][0]['value'];
            }
        }
    }

    public function getTotalWeight()
    {
        $weight = 0;
        foreach ($this->items as $item) {
            // Assume each product weight 500g
            $weight += $item->quantity * 500;
        }
        return $weight;
    }

    public function updateQuantity($itemId, $quantity)
    {
        $item = CartItem::find($itemId);
        if ($item && $item->cart->user_id == auth()->id()) {
            $quantity = max(1, $quantity);
            $item->update(['quantity' => $quantity]);
            $this->emit('cartUpdated');
            $this->loadCart();
            $this->calculateShipping();
        }
    }

    public function removeItem($itemId)
    {
        $item = CartItem::find($itemId);
        if ($item && $item->cart->user_id == auth()->id()) {
            $item->delete();
            $this->emit('cartUpdated');
            $this->loadCart();
            $this->calculateShipping();
        }
    }

    public function moveToWishlist($itemId)
    {
        $item = CartItem::find($itemId);
        if ($item && $item->cart->user_id == auth()->id()) {
            // Add to wishlist
            auth()->user()->wishlists()->firstOrCreate(['product_id' => $item->product_id]);
            $item->delete();
            $this->emit('cartUpdated');
            $this->loadCart();
            session()->flash('message', 'Moved to wishlist');
        }
    }

    public function proceedToCheckout()
    {
        if ($this->selectedCity && $this->shippingCost > 0) {
            session()->put('checkout', [
                'shipping_cost' => $this->shippingCost,
                'city_id' => $this->selectedCity,
                'courier' => $this->selectedCourier,
            ]);
            return redirect()->route('checkout');
        } else {
            session()->flash('error', 'Please select shipping destination');
        }
    }

    public function render()
    {
        return view('livewire.cart-page');
    }
}
