<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Role;
use App\Models\StockAlert;
use App\Models\User;
use App\Observers\StockAlertObserver;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class StockAlertTriggerTimestampTest extends TestCase
{
    use DatabaseMigrations;

    public function test_stock_alert_observer_sets_first_triggered_at_on_create(): void
    {
        $category = Category::query()->create([
            'name' => 'Tees',
            'slug' => 'tees',
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Essential Tee',
            'slug' => 'essential-tee',
            'description' => 'Cotton tee.',
            'price' => 150000,
            'stock' => 2,
            'is_active' => true,
        ]);

        $productSize = ProductSize::query()->create([
            'product_id' => $product->id,
            'size' => 'M',
            'stock' => 2,
        ]);

        StockAlert::query()->delete();

        (new StockAlertObserver())->saved($productSize);

        $alert = StockAlert::query()->where('product_size_id', $productSize->id)->first();

        $this->assertNotNull($alert);
        $this->assertNotNull($alert->first_triggered_at);
    }

    public function test_stock_alerts_views_fall_back_to_created_at_when_first_triggered_at_is_null(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::query()->create([
            'name' => 'admin',
            'display_name' => 'Admin',
        ]));

        $category = Category::query()->create([
            'name' => 'Tees',
            'slug' => 'tees',
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Fallback Tee',
            'slug' => 'fallback-tee',
            'description' => 'Cotton tee.',
            'price' => 150000,
            'stock' => 2,
            'is_active' => true,
        ]);

        $productSize = ProductSize::query()->create([
            'product_id' => $product->id,
            'size' => 'L',
            'stock' => 2,
        ]);

        StockAlert::query()->delete();

        $alert = StockAlert::query()->create([
            'product_id' => $product->id,
            'product_size_id' => $productSize->id,
            'current_stock' => 2,
            'threshold' => 5,
            'status' => 'active',
            'first_triggered_at' => null,
        ]);

        $expectedDate = $alert->created_at->format('M d, Y H:i');

        $this->actingAs($admin)
            ->get(route('admin.stock-alerts.index'))
            ->assertOk()
            ->assertSee($expectedDate);

        $this->actingAs($admin)
            ->get(route('admin.stock-alerts.show', $alert))
            ->assertOk()
            ->assertSee($expectedDate);
    }
}

