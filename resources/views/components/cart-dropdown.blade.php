<?php

namespace App\Http\Livewire;

use App\Models\Cart;
use App\Models\CartItem;
use Livewire\Component;

class CartDropdown extends Component
{
    protected $listeners = ['cartUpdated' => '$refresh'];

    public function removeItem($itemId)
    {
        $item = CartItem::find($itemId);
        if ($item && $item->cart->user_id == auth()->id()) {
            $item->delete();
            $this->emit('cartUpdated');
            session()->flash('message', 'Item removed from cart');
        }
    }

    public function getCartItemsProperty()
    {
        if (auth()->check()) {
            $cart = auth()->user()->cart;
            if ($cart) {
                return $cart->items()->with('product', 'size')->get();
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

    public function render()
    {
        return view('livewire.cart-dropdown');
    }
}
