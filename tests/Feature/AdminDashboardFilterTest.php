<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AdminDashboardFilterTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2026, 6, 15, 14, 0, 0));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_dashboard_can_be_filtered_by_presets_month_and_year(): void
    {
        $admin = $this->createAdminUser();
        $category = Category::query()->create([
            'name' => 'Hoodie',
            'slug' => 'hoodie',
        ]);

        $productA = $this->createProduct($category, 'Product Today', 100000, 60000);
        $productB = $this->createProduct($category, 'Product 3 Days', 200000, 110000);
        $productC = $this->createProduct($category, 'Product April', 300000, 150000);
        $productD = $this->createProduct($category, 'Product 8 Months', 400000, 200000);
        $productE = $this->createProduct($category, 'Product Last Year', 500000, 250000);

        $customerToday = $this->createCustomerAt(Carbon::create(2026, 6, 15, 9, 0, 0));
        $customerApril = $this->createCustomerAt(Carbon::create(2026, 4, 10, 10, 0, 0));
        $customerOldYear = $this->createCustomerAt(Carbon::create(2025, 3, 5, 10, 0, 0));

        // 1. Today (2026-06-15): sales 100k, cost 60k, profit 40k, qty 1
        $this->createPaidOrder($customerToday, $productA, 1, 100000, Carbon::create(2026, 6, 15, 10, 30, 0));
        // Unpaid order today -> counted in totalOrders/recentOrders, excluded from sales/cost/profit
        $this->createOrderAt($customerToday, 999999, 'unpaid', Carbon::create(2026, 6, 15, 11, 0, 0));

        // 2. 3 days ago (2026-06-12): sales 400k (2x200k), cost 220k, profit 180k, qty 2
        $this->createPaidOrder($customerToday, $productB, 2, 200000, Carbon::create(2026, 6, 12, 15, 0, 0));

        // 3. 2 months ago (2026-04-10): sales 300k, cost 150k, profit 150k, qty 1 (active preorder)
        $this->createPaidOrder($customerApril, $productC, 1, 300000, Carbon::create(2026, 4, 10, 12, 0, 0), true);

        // 4. 8 months ago (2025-10-20, within 1_year from 2026-06-15): sales 400k, cost 200k, profit 200k, qty 1
        $this->createPaidOrder($customerOldYear, $productD, 1, 400000, Carbon::create(2025, 10, 20, 16, 0, 0));

        // 5. Over 1 year ago (2025-03-05): sales 500k, cost 250k, profit 250k, qty 1
        $this->createPaidOrder($customerOldYear, $productE, 1, 500000, Carbon::create(2025, 3, 5, 8, 0, 0));

        // Hari Ini
        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['period' => 'today']))
            ->assertOk()
            ->assertViewHas('period', 'today')
            ->assertViewHas('totalSales', 100000.0)
            ->assertViewHas('totalCost', 60000.0)
            ->assertViewHas('totalProfit', 40000.0)
            ->assertViewHas('totalOrders', 2)
            ->assertViewHas('paidOrdersCount', 1)
            ->assertViewHas('newUsers', 1)
            ->assertViewHas('monthlySalesData', fn (array $data): bool => count($data['labels']) === 24 && $data['data'][10] === 100000);

        // 7 Hari
        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['period' => '7_days']))
            ->assertOk()
            ->assertViewHas('period', '7_days')
            ->assertViewHas('totalSales', 500000.0)
            ->assertViewHas('totalCost', 280000.0)
            ->assertViewHas('totalProfit', 220000.0)
            ->assertViewHas('totalOrders', 3)
            ->assertViewHas('monthlySalesData', fn (array $data): bool => count($data['labels']) === 7);

        // 3 Bulan (2026-03-15 .. 2026-06-15)
        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['period' => '3_months']))
            ->assertOk()
            ->assertViewHas('period', '3_months')
            ->assertViewHas('totalSales', 800000.0)
            ->assertViewHas('totalCost', 430000.0)
            ->assertViewHas('totalProfit', 370000.0)
            ->assertViewHas('totalOrders', 4)
            ->assertViewHas('totalPreorderActive', 1)
            ->assertViewHas('newUsers', 2);

        // 1 Tahun (2025-07-01 .. 2026-06-15)
        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['period' => '1_year']))
            ->assertOk()
            ->assertViewHas('period', '1_year')
            ->assertViewHas('totalSales', 1200000.0)
            ->assertViewHas('totalCost', 630000.0)
            ->assertViewHas('totalProfit', 570000.0)
            ->assertViewHas('totalOrders', 5)
            ->assertViewHas('monthlySalesData', fn (array $data): bool => count($data['labels']) === 12);

        // Per Bulan (April 2026)
        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['period' => 'month', 'month' => 4, 'year' => 2026]))
            ->assertOk()
            ->assertViewHas('period', 'month')
            ->assertViewHas('periodLabel', 'April 2026')
            ->assertViewHas('totalSales', 300000.0)
            ->assertViewHas('totalCost', 150000.0)
            ->assertViewHas('totalProfit', 150000.0)
            ->assertViewHas('totalOrders', 1)
            ->assertViewHas('monthlySalesData', fn (array $data): bool => count($data['labels']) === 30)
            ->assertViewHas('productSalesData', fn (array $data): bool => $data['labels'] === ['Product April'] && $data['data'] === [1]);

        // Per Tahun (2025)
        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['period' => 'year', 'year' => 2025]))
            ->assertOk()
            ->assertViewHas('period', 'year')
            ->assertViewHas('periodLabel', 'Tahun 2025')
            ->assertViewHas('totalSales', 900000.0)
            ->assertViewHas('totalCost', 450000.0)
            ->assertViewHas('totalProfit', 450000.0)
            ->assertViewHas('totalOrders', 2)
            ->assertViewHas('newUsers', 1)
            ->assertViewHas('monthlySalesData', fn (array $data): bool => count($data['labels']) === 12 && $data['data'][2] === 500000 && $data['data'][9] === 400000)
            ->assertViewHas('combinedChartData', function (array $combined): bool {
                return count($combined['labels']) === 12
                    && $combined['sales'][2] === 500000
                    && $combined['cost'][2] === 250000
                    && $combined['profit'][2] === 250000
                    && $combined['orders'][2] === 1
                    && $combined['items'][2] === 1
                    && $combined['newUsers'][2] === 1
                    && $combined['sales'][9] === 400000
                    && $combined['cost'][9] === 200000
                    && $combined['profit'][9] === 200000;
            });

        // Default (7 Hari)
        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('period', '7_days')
            ->assertViewHas('totalSales', 500000.0)
            ->assertViewHas('totalOrders', 3);

        // Semua Waktu
        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['period' => 'all']))
            ->assertOk()
            ->assertViewHas('period', 'all')
            ->assertViewHas('totalSales', 1700000.0)
            ->assertViewHas('totalOrders', 6);
    }

    private function createAdminUser(): User
    {
        $admin = User::factory()->create([
            'created_at' => Carbon::create(2024, 1, 1, 0, 0, 0),
            'updated_at' => Carbon::create(2024, 1, 1, 0, 0, 0),
        ]);
        $admin->roles()->attach(Role::query()->create([
            'name' => 'admin',
            'display_name' => 'Admin',
        ]));

        return $admin;
    }

    private function createCustomerAt(Carbon $timestamp): User
    {
        $user = User::factory()->create();
        $user->forceFill([
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ])->save();

        return $user;
    }

    private function createProduct(Category $category, string $name, float $price, float $cost): Product
    {
        return Product::query()->create([
            'category_id' => $category->id,
            'name' => $name,
            'slug' => strtolower(str_replace(' ', '-', $name)) . '-' . uniqid(),
            'description' => 'Deskripsi produk',
            'price' => $price,
            'cost_price' => $cost,
            'stock' => 20,
            'is_active' => true,
        ]);
    }

    private function createOrderAt(User $customer, float $totalAmount, string $paymentStatus, Carbon $timestamp): Order
    {
        $order = Order::query()->create([
            'user_id' => $customer->id,
            'order_number' => 'JJY-DASH-' . uniqid(),
            'total_amount' => $totalAmount,
            'shipping_cost' => 0,
            'status' => $paymentStatus === 'paid' ? 'paid' : 'pending',
            'payment_status' => $paymentStatus,
            'payment_method' => 'midtrans',
            'shipping_address' => 'Jl. Test No. 1',
        ]);

        $order->forceFill([
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ])->save();

        return $order;
    }

    private function createPaidOrder(
        User $customer,
        Product $product,
        int $quantity,
        float $unitPrice,
        Carbon $timestamp,
        bool $isPreorder = false
    ): Order {
        $order = $this->createOrderAt($customer, $unitPrice * $quantity, 'paid', $timestamp);

        $item = OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $unitPrice,
            'is_preorder' => $isPreorder,
        ]);

        $item->forceFill([
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ])->save();

        return $order;
    }
}

