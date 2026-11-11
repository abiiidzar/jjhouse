<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $cartItems = CartItem::with('product', 'size')
            ->where('user_id', auth()->id())
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function increase(int $item): RedirectResponse
    {
        $cartItem = $this->findOwnedCartItem($item);

        if ($cartItem->quantity < 99) {
            $cartItem->increment('quantity');
        }

        return redirect()->route('cart');
    }

    public function decrease(int $item): RedirectResponse
    {
        $cartItem = $this->findOwnedCartItem($item);

        if ($cartItem->quantity > 1) {
            $cartItem->decrement('quantity');
        }

        return redirect()->route('cart');
    }

    public function remove(int $item): RedirectResponse
    {
        $this->findOwnedCartItem($item)->delete();

        return redirect()->route('cart');
    }

    private function findOwnedCartItem(int $item): CartItem
    {
        return CartItem::query()
            ->whereKey($item)
            ->whereHas('cart', fn ($query) => $query->where('user_id', Auth::id()))
            ->firstOrFail();
    }
}
