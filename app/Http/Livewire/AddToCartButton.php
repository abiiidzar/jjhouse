<?php

namespace App\Http\Livewire;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;


class AddToCartButton extends Component
{
    public $product;
    public $selectedSize;
    public $quantity = 1;
    public $showSuccess = false;

    protected $listeners = ['resetSuccess' => 'resetSuccessMessage'];

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function addToCart()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->selectedSize) {
            session()->flash('error', 'Pilih ukuran terlebih dahulu');
            return;
        }

        // Check stock
        $size = $this->product->sizes()->find($this->selectedSize);
        if (!$size || $size->stock < $this->quantity) {
            session()->flash('error', 'Stok tidak mencukupi');
            return;
        }

        $cart = Auth::user()->cart;
        if (!$cart) {
            $cart = Cart::create(['user_id' => Auth::id()]);
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
        $this->showSuccess = true;
        session()->flash('success', 'Produk ditambahkan ke keranjang');

        // Auto hide success message after 3 seconds
        $this->dispatchBrowserEvent('hide-success');
    }

    public function resetSuccessMessage()
    {
        $this->showSuccess = false;
    }

    public function render()
    {
        return view('livewire.add-to-cart-button');
    }
}
