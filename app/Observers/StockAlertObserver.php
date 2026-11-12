<?php

namespace App\Observers;

use App\Models\ProductSize;
use App\Models\StockAlert;

class StockAlertObserver
{
    public function saved(ProductSize $productSize)
    {
        $this->handleStockAlert($productSize);
    }

    private function handleStockAlert(ProductSize $productSize)
    {
        $threshold = config('jjhouse.stock_alert_threshold', 5);
        $stock = $productSize->stock;
        $existingAlert = StockAlert::where('product_size_id', $productSize->id)->first();

        if ($stock <= $threshold) {
            if (!$existingAlert) {
                // Create a new alert
                StockAlert::create([
                    'product_id' => $productSize->product_id,
                    'product_size_id' => $productSize->id,
                    'current_stock' => $stock,
                    'threshold' => $threshold,
                    'status' => 'active',
                    'first_triggered_at' => now(),
                ]);
            } elseif ($existingAlert->status === 'resolved') {
                // Re-activate a resolved alert if stock drops again
                $existingAlert->update([
                    'current_stock' => $stock,
                    'status' => 'active',
                    'first_triggered_at' => $existingAlert->first_triggered_at ?? now(),
                    'acknowledged_by' => null,
                    'acknowledged_at' => null,
                    'acknowledgment_notes' => null,
                ]);
            } else {
                // Update existing active/acknowledged alert and backfill null trigger timestamp
                $existingAlert->update([
                    'current_stock' => $stock,
                    'first_triggered_at' => $existingAlert->first_triggered_at ?? now(),
                ]);
            }
        } elseif ($existingAlert && $existingAlert->status !== 'resolved') {
            // Resolve the alert if stock is back above the threshold
            $existingAlert->resolve();
        }
    }
}

