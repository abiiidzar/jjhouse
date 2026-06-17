<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\ProductSize;
use App\Observers\OrderObserver;
use App\Observers\ProductSizeObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ProductSize::observe(ProductSizeObserver::class);
    }
    public function saved(ProductSize $productSize)
{
    $threshold = 5;
    $stock = $productSize->stock;

    $existingAlert = StockAlert::where('product_size_id', $productSize->id)->first();

    if ($stock <= $threshold) {

        if (!$existingAlert) {

            StockAlert::create([
                'product_id' => $productSize->product_id,
                'product_size_id' => $productSize->id,
                'current_stock' => $stock,
                'threshold' => $threshold,
                'status' => 'active',
                'first_triggered_at' => now(),
            ]);

        } else {

            $existingAlert->update([
                'current_stock' => $stock,
                'status' => 'active'
            ]);

        }

    } elseif ($existingAlert && $existingAlert->status !== 'resolved') {

        $existingAlert->resolve();
    }
}
}
