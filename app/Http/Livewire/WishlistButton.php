<?php

namespace App\Http\Livewire;

use App\Models\Wishlist;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;


class WishlistButton extends Component
{
    public $product;
    public $isWishlisted = false;
    public $wishlistCount = 0;

    public function mount($product)
    {
        $this->product = $product;
        if (Auth::check()) {
            $this->isWishlisted = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->exists();
            $this->wishlistCount = Wishlist::where('product_id', $product->id)->count();
        }
    }

    public function toggleWishlist()
    {
        if (!Auth::check()) {
            $this->dispatchBrowserEvent('show-login-modal');
            return;
        }

        if ($this->isWishlisted) {
            Wishlist::where('user_id', Auth::id())
                ->where('product_id', $this->product->id)
                ->delete();
            $this->isWishlisted = false;
            $this->wishlistCount--;
            $this->dispatchBrowserEvent('show-toast', ['message' => 'Dihapus dari wishlist', 'type' => 'info']);
        } else {
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $this->product->id,
            ]);
            $this->isWishlisted = true;
            $this->wishlistCount++;
            $this->dispatchBrowserEvent('show-toast', ['message' => 'Ditambahkan ke wishlist', 'type' => 'success']);
        }

        $this->emit('wishlistUpdated');
    }

    public function render()
    {
        return view('livewire.wishlist-button');
    }
}
