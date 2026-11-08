<?php

namespace App\Livewire;

use App\Models\CartItem;
use App\Models\Wishlist;
use App\Services\RajaOngkirService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CartPage extends Component
{
    public $cart;
    public $items;
    public $provinces = [];
    public $cities = [];
    public $selectedProvince;
    public $selectedCity;
    public $selectedCityName;
    public $shippingCost = 0;
    public $couriers = ['jne', 'tiki', 'pos'];
    public $selectedCourier = 'jne';
    public $originCity = 495; // Jakarta Selatan

    protected $listeners = ['cartUpdated' => 'loadCart'];

    public function mount()
    {
        $this->loadCart();
        $this->loadProvinces();
    }

    public function loadCart()
    {
        if (Auth::check()) {
            $this->cart = Auth::user()->cart;
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
        $this->selectedCityName = null;
        $this->shippingCost = 0;
    }

    public function updatedSelectedCity()
    {
        if ($this->selectedCity) {
            $city = collect($this->cities)->firstWhere('city_id', $this->selectedCity);
            $this->selectedCityName = $city['city_name'] ?? null;
            $this->calculateShipping();
        }
    }

    public function updatedSelectedCourier()
    {
        $this->calculateShipping();
    }

    public function calculateShipping()
    {
        if ($this->selectedCity && $this->selectedCourier) {
            $rajaOngkir = new RajaOngkirService();
            $weight = $this->getTotalWeight();
            $costs = $rajaOngkir->calculateCost($this->originCity, $this->selectedCity, $weight, $this->selectedCourier);
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
        return max($weight, 100); // Minimum 100g
    }

    public function updateQuantity($itemId, $quantity)
    {
        $item = CartItem::find($itemId);
        if ($item && $item->cart->user_id == Auth::id()) {
            $quantity = max(1, min($quantity, 99));
            $item->update(['quantity' => $quantity]);
            $this->dispatch('cartUpdated');
            $this->loadCart();
            $this->calculateShipping();
        }
    }

    public function removeItem($itemId)
    {
        $item = CartItem::find($itemId);
        if ($item && $item->cart->user_id == Auth::id()) {
            $item->delete();
            $this->dispatch('cartUpdated');
            $this->loadCart();
            $this->calculateShipping();
            session()->flash('message', 'Item removed from cart');
        }
    }

    public function moveToWishlist($itemId)
    {
        $item = CartItem::find($itemId);
        if ($item && $item->cart->user_id == Auth::id()) {
            // Add to wishlist
            Wishlist::firstOrCreate([
                'user_id' => Auth::id(),
                'product_id' => $item->product_id,
            ]);
            $item->delete();
            $this->dispatch('cartUpdated');
            $this->loadCart();
            $this->calculateShipping();
            session()->flash('message', 'Moved to wishlist');
        }
    }

    public function getSubtotalProperty()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->product->price * $item->quantity;
        }
        return $total;
    }

    public function getTotalProperty()
    {
        return $this->subtotal + $this->shippingCost;
    }

    public function proceedToCheckout()
    {
        if ($this->selectedCity && $this->shippingCost > 0) {
            session()->put('checkout', [
                'shipping_cost' => $this->shippingCost,
                'city_id' => $this->selectedCity,
                'city_name' => $this->selectedCityName,
                'courier' => $this->selectedCourier,
                'province' => $this->selectedProvince,
            ]);
            return redirect()->route('checkout');
        } else {
            session()->flash('error', 'Silakan pilih provinsi, kota, dan kurir terlebih dahulu');
        }
    }

    public function render()
    {
        return view('livewire.cart-page', [
            'cart' => $this->cart,
            'items' => $this->items,
            'provinces' => $this->provinces,
            'cities' => $this->cities,
            'selectedProvince' => $this->selectedProvince,
            'selectedCity' => $this->selectedCity,
            'selectedCityName' => $this->selectedCityName,
            'shippingCost' => $this->shippingCost,
            'couriers' => $this->couriers,
            'selectedCourier' => $this->selectedCourier,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
        ]);
    }
}
