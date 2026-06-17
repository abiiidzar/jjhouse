<?php

namespace App\Mail;

use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PreOrderReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $orderItem;
    public $user;
    public $product;
    public $paymentLink;

    /**
     * Create a new message instance.
     */
    public function __construct(OrderItem $orderItem, User $user, $paymentLink = null)
    {
        $this->orderItem = $orderItem;
        $this->user = $user;
        $this->product = $orderItem->product;
        $this->paymentLink = $paymentLink;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pre-order Anda Siap Dikirim - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.preorder_ready',
            with: [
                'orderItem' => $this->orderItem,
                'user' => $this->user,
                'product' => $this->product,
                'paymentLink' => $this->paymentLink,
                'order' => $this->orderItem->order,
                'remainingPayment' => $this->orderItem->remaining_payment,
                'isDp' => $this->orderItem->preorder_payment_type === 'dp',
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
