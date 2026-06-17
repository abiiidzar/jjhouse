<?php

namespace App\Http\Livewire;

use App\Models\Wishlist;
use Livewire\Component;

class WishlistButton extends Component
{
    public $product;
    public $isWishlisted = false;

    public function mount($product)
    {
        if (auth()->check()) {
            $this->isWishlisted = Wishlist::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->exists();
        }
    }

    public function toggleWishlist()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if ($this->isWishlisted) {
            Wishlist::where('user_id', auth()->id())
                ->where('product_id', $this->product->id)
                ->delete();
            $this->isWishlisted = false;
            session()->flash('success', 'Dihapus dari wishlist');
        } else {
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $this->product->id,
            ]);
            $this->isWishlisted = true;
            session()->flash('success', 'Ditambahkan ke wishlist');
        }
    }

    public function render()
    {
        return view('livewire.wishlist-button');
    }
}
