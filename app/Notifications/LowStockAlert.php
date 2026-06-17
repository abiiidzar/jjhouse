<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ProductSize;
use App\Models\StockAlert;

class LowStockAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public ProductSize $productSize;
    public StockAlert $alert;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProductSize $productSize, StockAlert $alert)
    {
        $this->productSize = $productSize;
        $this->alert = $alert;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $product = $this->productSize->product;
        $storeName = cache('setting_store_name', config('app.name'));

        return (new MailMessage)
            ->subject("🔔 Low Stock Alert - {$product->name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Low stock alert for product: **{$product->name}**")
            ->line("Size: **{$this->productSize->size}**")
            ->line("Current stock: **{$this->productSize->stock}** units")
            ->line("Alert threshold: **{$this->alert->threshold}** units")
            ->line("Please restock this product to avoid stockouts.")
            ->action('View Product', route('admin.products.edit', $product->id))
            ->action('View Stock Alerts', route('admin.stock-alerts.index'))
            ->line("Regards,")
            ->line("{$storeName} Inventory System");
    }

    /**
     * Get the array representation of the notification (for database).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $product = $this->productSize->product;

        return [
            'title' => "Low stock alert: {$product->name}",
            'message' => "Stock level ({$this->productSize->stock}) is below threshold ({$this->alert->threshold})",
            'icon' => 'warning',
            'type' => 'low_stock',
            'product_id' => $product->id,
            'product_name' => $product->name,
            'size' => $this->productSize->size,
            'current_stock' => $this->productSize->stock,
            'threshold' => $this->alert->threshold,
            'alert_id' => $this->alert->id,
            'action_url' => route('admin.stock-alerts.show', $this->alert->id),
        ];
    }
}
