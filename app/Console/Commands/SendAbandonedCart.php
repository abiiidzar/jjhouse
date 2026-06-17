<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\AbandonedCartMail;


#[Signature('app:send-abandoned-cart')]
#[Description('Command description')]
class SendAbandonedCart extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $carts = \App\Models\Cart::whereHas('items')
            ->where('updated_at', '<=', now()->subHours(24))
            ->get();

        foreach ($carts as $cart) {
            Mail::to($cart->user->email)
                ->queue(new \App\Mail\AbandonedCartMail($cart));
        }
    }
}
