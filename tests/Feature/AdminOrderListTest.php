<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AdminOrderListTest extends TestCase
{
    use DatabaseMigrations;

    public function test_customer_cancelled_orders_are_hidden_but_other_orders_remain_visible(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::query()->create([
            'name' => 'admin',
            'display_name' => 'Admin',
        ]));

        $customer = User::factory()->create();
        $customerCancelledOrder = $this->createOrder($customer, [
            'status' => 'cancelled',
            'payment_status' => 'failed',
            'cancellation_source' => 'customer',
        ]);
        $adminCancelledOrder = $this->createOrder($customer, [
            'status' => 'cancelled',
            'payment_status' => 'failed',
            'cancellation_source' => 'admin',
        ]);
        $activeOrder = $this->createOrder($customer);

        $this->actingAs($admin)
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertViewHas('orders', function ($orders) use ($customerCancelledOrder, $adminCancelledOrder, $activeOrder): bool {
                $visibleIds = $orders->getCollection()->modelKeys();

                return !in_array($customerCancelledOrder->id, $visibleIds, true)
                    && in_array($adminCancelledOrder->id, $visibleIds, true)
                    && in_array($activeOrder->id, $visibleIds, true);
            });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createOrder(User $customer, array $attributes = []): Order
    {
        return Order::query()->create(array_merge([
            'user_id' => $customer->id,
            'order_number' => 'JJY-TEST-'.uniqid(),
            'total_amount' => 150000,
            'shipping_cost' => 15000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'midtrans',
            'shipping_address' => 'Jl. Contoh No. 1, Jakarta',
        ], $attributes));
    }
}
