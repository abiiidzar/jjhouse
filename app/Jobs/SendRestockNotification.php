<?php

namespace App\Jobs;

use App\Models\ProductSize;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendRestockNotification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $productSize;

    /**
     * Create a new job instance.
     */
    public function __construct(ProductSize $productSize)
    {
        $this->productSize = $productSize;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $users = \App\Models\Wishlist::where('product_id', $this->productSize->product_id)
        ->with('user')
        ->get();

    foreach ($users as $wishlist) {
        Mail::to($wishlist->user->email)
            ->queue(new \App\Mail\RestockMail($this->productSize));
    }
}
}
