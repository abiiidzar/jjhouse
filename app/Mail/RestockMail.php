<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RestockMail extends Mailable
{
    public $productSize;

    public function __construct($productSize)
    {
        $this->productSize = $productSize;
    }

    public function build()
    {
        return $this->subject('Produk Kembali Tersedia 🎉')
            ->view('emails.restock');
    }
}
