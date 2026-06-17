<?php

namespace App\Observers;

use App\Models\ProductSize;
use App\Models\StockHistory;
use App\Models\StockAlert;
use App\Notifications\LowStockAlert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ProductSizeObserver
{
    /**
     * Handle the ProductSize "created" event.
     */
    public function created(ProductSize $productSize): void
    {
        $this->syncProductStock($productSize);
    }

    /**
     * Handle the ProductSize "updated" event.
     */
    public function updated(ProductSize $productSize): void
    {
        // Track stock changes
        $originalStock = $productSize->getOriginal('stock');
        $currentStock = $productSize->stock;

        if ($originalStock != $currentStock) {
            $this->trackStockChange($productSize, $originalStock, $currentStock);
        }

        $this->syncProductStock($productSize);
    }

    /**
     * Handle the ProductSize "deleted" event.
     */
    public function deleted(ProductSize $productSize): void
    {
        $product = $productSize->product;
        if ($product) {
            // Update product stock dengan total stok sizes yang tersisa
            $totalStock = $product->sizes()->sum('stock');
            $product->update(['stock' => $totalStock]);
        }
    }

    /**
     * Handle the ProductSize "restored" event.
     */
    public function restored(ProductSize $productSize): void
    {
        $this->syncProductStock($productSize);
    }

    /**
     * Handle the ProductSize "force deleted" event.
     */
    public function forceDeleted(ProductSize $productSize): void
    {
        //
    }

    /**
     * Sync product stock dengan total stok semua sizes
     */
    private function syncProductStock(ProductSize $productSize): void
    {
        $product = $productSize->product;
        if ($product) {
            $totalStock = $product->sizes()->sum('stock');
            $product->update(['stock' => $totalStock]);
        }
    }

    /**
     * Track stock changes in history
     */
    private function trackStockChange(ProductSize $productSize, $before, $after): void
    {
        $stockThreshold = (int) cache('setting_stock_threshold', 5);

        // Create stock history record
        StockHistory::createHistory($productSize, [
            'user_id' => Auth::id(),
            'quantity_before' => $before,
            'quantity_after' => $after,
            'action' => 'update',
            'notes' => 'Stock updated',
        ]);

        // Check for low stock and create alert if needed
        $alert = StockAlert::checkAndCreateAlert($productSize, $stockThreshold);

        // Send notification to admins if new alert created
        if ($alert && $alert->wasRecentlyCreated) {
            $this->notifyAdminsOfLowStock($productSize, $alert);
        }
    }

    /**
     * Send notification to admins about low stock
     */
    private function notifyAdminsOfLowStock(ProductSize $productSize, StockAlert $alert): void
    {
        try {
            $admins = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->get();

            Notification::send($admins, new LowStockAlert($productSize, $alert));
        } catch (\Exception $e) {
            \Log::error('Failed to send low stock notification: ' . $e->getMessage());
        }
    }
    public function updating(ProductSize $productSize)
{
    $original = $productSize->getOriginal('stock');
    $new = $productSize->stock;

    if ($original != $new) {

        StockHistory::create([
            'product_id' => $productSize->product_id,
            'product_size_id' => $productSize->id,
            'action' => 'update',
            'quantity_before' => $original,
            'quantity_after' => $new,
            'quantity_changed' => $new - $original,
            'user_id' => Auth::id(),
            'notes' => 'Stock diubah manual'
        ]);
    }
}
}
