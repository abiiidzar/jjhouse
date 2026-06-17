<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentReminderMail;

#[Signature('app:send-payment-reminder')]
#[Description('Command description')]

class SendPaymentReminder extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = \App\Models\Order::where('payment_status', 'unpaid')
            ->where('created_at', '<=', now()->subHours(24))
            ->get();

        foreach ($orders as $order) {
            Mail::to($order->user->email)
                ->queue(new \App\Mail\PaymentReminderMail($order));
        }
    }
}
