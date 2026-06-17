<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class SyncProductStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:sync-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync product stock dengan total stok dari semua product sizes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Syncing product stock...');

        $products = Product::with('sizes')->get();
        $count = 0;

        foreach ($products as $product) {
            $totalStock = $product->sizes()->sum('stock');
            $product->update(['stock' => $totalStock]);
            $count++;
        }

        $this->info("✓ Successfully synced $count products");
    }
}
