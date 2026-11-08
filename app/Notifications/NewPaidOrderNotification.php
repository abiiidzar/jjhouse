<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPaidOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array{
     *     order_id: int,
     *     order_number: string,
     *     customer_name: string,
     *     total_amount: numeric-string|float|int,
     *     payment_method: ?string
     * }  $order
     */
    public function __construct(private readonly array $order)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $notifiable->email ? ['database', 'mail'] : ['database'];
    }

    /**
     * Keep the in-app notification available immediately while email is sent by a worker.
     *
     * @return array<string, string>
     */
    public function viaConnections(): array
    {
        return [
            'database' => 'sync',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Pesanan baru telah dibayar #{$this->order['order_number']}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Pesanan {$this->order['order_number']} dari {$this->order['customer_name']} telah dibayar.")
            ->line('Total pembayaran: '.$this->formattedTotal())
            ->when(
                filled($this->order['payment_method']),
                fn (MailMessage $message) => $message->line('Metode pembayaran: '.str($this->order['payment_method'])->replace('_', ' ')->title())
            )
            ->action('Lihat Pesanan', $this->actionUrlFor($notifiable));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pesanan baru telah dibayar',
            'message' => "Pesanan {$this->order['order_number']} dari {$this->order['customer_name']} telah dibayar.",
            'icon' => 'shopping-bag',
            'type' => 'order_paid',
            'order_id' => $this->order['order_id'],
            'order_number' => $this->order['order_number'],
            'customer_name' => $this->order['customer_name'],
            'total_amount' => $this->order['total_amount'],
            'payment_method' => $this->order['payment_method'],
            'action_url' => $this->actionUrlFor($notifiable),
        ];
    }

    private function actionUrlFor(object $notifiable): string
    {
        if (method_exists($notifiable, 'hasRole')) {
            if ($notifiable->hasRole('admin')) {
                return route('admin.orders.show', $this->order['order_id']);
            }

            if ($notifiable->hasRole('karyawan')) {
                return route('karyawan.orders.show', $this->order['order_id']);
            }
        }

        return route('admin.orders.show', $this->order['order_id']);
    }

    private function formattedTotal(): string
    {
        return 'Rp '.number_format((float) $this->order['total_amount'], 0, ',', '.');
    }
}
