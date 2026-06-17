<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'category_id' => 1,
                'name' => 'Basic Cotton T-Shirt',
                'slug' => 'basic-cotton-t-shirt',
                'description' => 'Kaos katun nyaman untuk sehari-hari.',
                'short_description' => 'Kaos katun premium',
                'price' => 150000,
                'cost_price' => 50000,
                'stock' => 50,
                'is_preorder' => false,
                'is_active' => true,
                'sizes' => ['S', 'M', 'L', 'XL'],
                'stock_per_size' => [10, 20, 15, 5],
            ],
            [
                'category_id' => 1,
                'name' => 'Oversized Hoodie',
                'slug' => 'oversized-hoodie',
                'description' => 'Hoodie oversized dengan bahan fleece tebal.',
                'short_description' => 'Hoodie trendy oversized',
                'price' => 350000,
                'cost_price' => 120000,
                'stock' => 30,
                'is_preorder' => true,
                'preorder_end_date' => now()->addDays(7),
                'preorder_estimated_days' => 14,
                'is_active' => true,
                'sizes' => ['M', 'L', 'XL'],
                'stock_per_size' => [10, 10, 10],
            ],
            // Tambahkan 8 produk lainnya dengan data dummy
        ];

        foreach ($products as $productData) {
            $sizesData = $productData['sizes'];
            $stockPerSize = $productData['stock_per_size'];
            unset($productData['sizes'], $productData['stock_per_size']);

            $product = Product::create($productData);

            // Add sizes
            foreach ($sizesData as $index => $size) {
                ProductSize::create([
                    'product_id' => $product->id,
                    'size' => $size,
                    'stock' => $stockPerSize[$index],
                ]);
            }

            // Add dummy image
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => 'https://placehold.co/600x800?text=' . urlencode($product->name),
                'is_primary' => true,
            ]);
        }
    }
}
