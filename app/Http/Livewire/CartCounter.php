<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;


class CartCounter extends Component
{
    protected $listeners = ['cartUpdated' => '$refresh'];

    public function getCartCountProperty()
    {
        if (Auth::check()) {
            $cart = Auth::user()->cart;
            if ($cart) {
                return $cart->items->sum('quantity');
            }
        }
        return 0;
    }

    public function getCartTotalProperty()
    {
        if (Auth::check()) {
            $cart = Auth::user()->cart;
            if ($cart) {
                $total = 0;
                foreach ($cart->items as $item) {
                    $total += $item->product->price * $item->quantity;
                }
                return $total;
            }
        }
        return 0;
    }

    public function render()
    {
        return view('livewire.cart-counter', [
            'cartCount' => $this->cartCount,
            'cartTotal' => $this->cartTotal,
        ]);
    }
}
