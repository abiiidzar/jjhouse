<?php

namespace App\Http\Livewire;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Livewire\Component;

class AddToCartButton extends Component
{
    public $product;
    public $selectedSize;
    public $quantity = 1;

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function addToCart()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!$this->selectedSize) {
            session()->flash('error', 'Pilih ukuran terlebih dahulu');
            return;
        }

        $cart = auth()->user()->cart;
        if (!$cart) {
            $cart = Cart::create(['user_id' => auth()->id()]);
        }

        // Check if item already in cart
        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $this->product->id)
            ->where('product_size_id', $this->selectedSize)
            ->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $this->quantity);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $this->product->id,
                'product_size_id' => $this->selectedSize,
                'quantity' => $this->quantity,
                'is_preorder' => $this->product->is_preorder,
            ]);
        }

        $this->emit('cartUpdated');
        session()->flash('success', 'Produk ditambahkan ke keranjang');
    }

    public function render()
    {
        return view('livewire.add-to-cart-button');
    }
}
