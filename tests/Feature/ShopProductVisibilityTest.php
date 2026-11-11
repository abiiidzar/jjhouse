<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ShopProductVisibilityTest extends TestCase
{
    use DatabaseMigrations;

    public function test_shop_only_lists_products_active_in_admin(): void
    {
        $category = Category::query()->create([
            'name' => 'T-Shirts',
            'slug' => 't-shirts',
        ]);

        Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Active Shop Product',
            'slug' => 'active-shop-product',
            'description' => 'Visible product',
            'price' => 100000,
            'stock' => 5,
            'is_active' => true,
        ]);

        Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Inactive Shop Product',
            'slug' => 'inactive-shop-product',
            'description' => 'Hidden product',
            'price' => 100000,
            'stock' => 5,
            'is_active' => false,
        ]);

        $this->get(route('shop'))
            ->assertOk()
            ->assertSee('Active Shop Product')
            ->assertDontSee('Inactive Shop Product');
    }
}
