<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CustomerOrderStatusTabsTest extends TestCase
{
    use DatabaseMigrations;

    public function test_customer_can_filter_orders_by_status_and_see_tab_counts(): void
    {
        $customer = User::factory()->create();
        $anotherCustomer = User::factory()->create();

        $this->createOrder($customer, 'ORD-PAID', 'paid');
        $this->createOrder($customer, 'ORD-PROCESSING', 'processing');
        $this->createOrder($customer, 'ORD-READY', 'ready_to_ship');
        $this->createOrder($customer, 'ORD-SHIPPED', 'shipped');
        $this->createOrder($customer, 'ORD-DELIVERED', 'delivered');
        $this->createOrder($customer, 'ORD-COMPLETED', 'completed');
        $this->createOrder($anotherCustomer, 'ORD-OTHER', 'paid');

        $response = $this->actingAs($customer)->get(route('dashboard', ['status' => 'shipping']));

        $response->assertOk()
            ->assertSee('ORD-READY')
            ->assertSee('ORD-SHIPPED')
            ->assertSee('ORD-DELIVERED')
            ->assertDontSee('ORD-PAID')
            ->assertDontSee('ORD-COMPLETED')
            ->assertDontSee('ORD-OTHER')
            ->assertSee('aria-current="page"', false);

        $this->assertMatchesRegularExpression(
            '/>\\s*3\\s*<\\/span>/',
            $response->getContent()
        );
    }

    public function test_unknown_order_status_filter_falls_back_to_all_orders(): void
    {
        $customer = User::factory()->create();
        $this->createOrder($customer, 'ORD-PAID', 'paid');
        $this->createOrder($customer, 'ORD-CANCELLED', 'cancelled');

        $this->actingAs($customer)
            ->get(route('dashboard', ['status' => 'not-a-status']))
            ->assertOk()
            ->assertSee('ORD-PAID')
            ->assertSee('ORD-CANCELLED');
    }

    private function createOrder(User $customer, string $number, string $status): Order
    {
        return Order::query()->create([
            'user_id' => $customer->id,
            'order_number' => $number,
            'total_amount' => 100000,
            'shipping_cost' => 10000,
            'status' => $status,
            'payment_status' => $status === 'pending' ? 'unpaid' : 'paid',
            'shipping_address' => 'Test address',
        ]);
    }
}
