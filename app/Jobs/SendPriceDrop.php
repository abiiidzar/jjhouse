<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\Wishlist;
use App\Mail\PriceDropMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPriceDrop implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $product;
    public $oldPrice;

    public function __construct(Product $product, $oldPrice)
    {
        $this->product = $product;
        $this->oldPrice = $oldPrice;
    }

    public function handle()
    {
        // 🔥 Ambil semua user yang wishlist produk ini
        $wishlists = Wishlist::with('user')
            ->where('product_id', $this->product->id)
            ->get();

        foreach ($wishlists as $wishlist) {

            if (!$wishlist->user) continue;

            Mail::to($wishlist->user->email)
                ->queue(new PriceDropMail(
                    $this->product,
                    $this->oldPrice,
                    $this->product->price
                ));
        }
    }
}
