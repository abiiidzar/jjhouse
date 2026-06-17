<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbandonedCartMail extends Mailable
{
    public $cart;

    public function __construct($cart)
    {
        $this->cart = $cart;
    }

    public function build()
    {
        return $this->subject('Keranjang Kamu Menunggu 🛒')
            ->view('emails.abandoned-cart');
    }
}
