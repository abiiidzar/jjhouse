<?php

namespace App\Livewire;

use App\Models\CartItem;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;


class CartDropdown extends Component
{
    public $isOpen = false;

    protected $listeners = ['cartUpdated' => '$refresh', 'toggleCart' => 'toggle'];

    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function removeItem($itemId)
    {
        $item = CartItem::find($itemId);
        if ($item && $item->cart->user_id == Auth::id()) {
            $item->delete();
            $this->dispatch('cartUpdated');
            session()->flash('message', 'Item removed from cart');
        }
    }

    public function getCartItemsProperty()
    {
        if (Auth::check()) {
            $cart = Auth::user()->cart;
            if ($cart) {
                return $cart->items()->with('product', 'size')->take(5)->get();
            }
        }
        return collect();
    }

    public function getTotalProperty()
    {
        $total = 0;
        foreach ($this->cartItems as $item) {
            $total += $item->product->price * $item->quantity;
        }
        return $total;
    }

    public function getItemCountProperty()
    {
        $count = 0;
        foreach ($this->cartItems as $item) {
            $count += $item->quantity;
        }
        return $count;
    }

    public function render()
    {
        return view('livewire.cart-dropdown', [
            'cartItems' => $this->cartItems,
            'total' => $this->total,
            'itemCount' => $this->itemCount,
        ]);
    }
}
