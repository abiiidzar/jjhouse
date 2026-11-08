<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array{
     *     order_id: int,
     *     order_number: string,
     *     status: string,
     *     previous_status: ?string,
     *     tracking_number: ?string,
     *     shipping_courier: ?string,
     *     shipping_service: ?string,
     *     shipping_note: ?string,
     *     status_changed: bool,
     *     shipping_details_changed: bool
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
     * Keep the bell badge up-to-date even when the mail queue is processed later.
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
        $message = (new MailMessage)
            ->subject("Pembaruan pesanan #{$this->order['order_number']}")
            ->greeting("Halo {$notifiable->name},")
            ->line($this->message())
            ->line('Status saat ini: '.$this->statusLabel());

        if (filled($this->order['tracking_number'])) {
            $message->line('Nomor resi: '.$this->order['tracking_number']);
        }

        $shippingService = $this->shippingServiceLabel();
        if ($shippingService !== null) {
            $message->line('Pengiriman: '.$shippingService);
        }

        if (filled($this->order['shipping_note'])) {
            $message->line('Catatan pengiriman: '.$this->order['shipping_note']);
        }

        return $message->action('Lihat Pesanan', $this->actionUrl());
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title(),
            'message' => $this->message(),
            'icon' => $this->icon(),
            'type' => $this->order['status_changed'] ? 'order_status_changed' : 'shipment_updated',
            'order_id' => $this->order['order_id'],
            'order_number' => $this->order['order_number'],
            'status' => $this->order['status'],
            'status_label' => $this->statusLabel(),
            'previous_status' => $this->order['previous_status'],
            'tracking_number' => $this->order['tracking_number'],
            'shipping_courier' => $this->order['shipping_courier'],
            'shipping_service' => $this->order['shipping_service'],
            'shipping_note' => $this->order['shipping_note'],
            'action_url' => $this->actionUrl(),
        ];
    }

    private function title(): string
    {
        return $this->order['status_changed']
            ? 'Status pesanan diperbarui'
            : 'Informasi pengiriman diperbarui';
    }

    private function message(): string
    {
        if (! $this->order['status_changed']) {
            return "Informasi pengiriman untuk pesanan {$this->order['order_number']} telah diperbarui.";
        }

        return match ($this->order['status']) {
            'paid' => "Pembayaran untuk pesanan {$this->order['order_number']} telah diterima.",
            'processing' => "Pesanan {$this->order['order_number']} sedang diproses.",
            'ready_to_ship' => "Pesanan {$this->order['order_number']} siap diserahkan ke kurir.",
            'shipped' => "Pesanan {$this->order['order_number']} telah dikirim.",
            'delivered' => "Pesanan {$this->order['order_number']} telah diterima.",
            'completed' => "Pesanan {$this->order['order_number']} telah selesai.",
            'cancelled' => "Pesanan {$this->order['order_number']} telah dibatalkan.",
            default => "Status pesanan {$this->order['order_number']} telah diperbarui menjadi {$this->statusLabel()}.",
        };
    }

    private function icon(): string
    {
        return match ($this->order['status']) {
            'processing' => 'package',
            'ready_to_ship' => 'package-check',
            'shipped' => 'truck',
            'delivered', 'completed' => 'circle-check',
            'cancelled' => 'circle-x',
            default => 'receipt-text',
        };
    }

    private function statusLabel(): string
    {
        return Order::STATUSES[$this->order['status']] ?? $this->order['status'];
    }

    private function shippingServiceLabel(): ?string
    {
        return collect([
            $this->order['shipping_courier'],
            $this->order['shipping_service'],
        ])->filter()->implode(' - ') ?: null;
    }

    private function actionUrl(): string
    {
        return route('dashboard.order', $this->order['order_id']);
    }
}
