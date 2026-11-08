<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use App\Notifications\NewPaidOrderNotification;
use App\Notifications\OrderStatusChangedNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderNotificationObserverTest extends TestCase
{
    use DatabaseMigrations;

    public function test_payment_transition_to_paid_notifies_staff_and_customer(): void
    {
        Notification::fake();

        $customer = User::factory()->create();
        $admin = User::factory()->create();
        $employee = User::factory()->create();

        $adminRole = Role::query()->create(['name' => 'admin', 'display_name' => 'Admin']);
        $employeeRole = Role::query()->create(['name' => 'karyawan', 'display_name' => 'Karyawan']);
        $admin->roles()->attach($adminRole);
        $employee->roles()->attach($employeeRole);

        $order = $this->createOrder($customer);

        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        Notification::assertSentToTimes($admin, NewPaidOrderNotification::class);
        Notification::assertSentToTimes($employee, NewPaidOrderNotification::class);
        Notification::assertSentToTimes($customer, OrderStatusChangedNotification::class);

        Notification::assertSentTo(
            $customer,
            OrderStatusChangedNotification::class,
            function (OrderStatusChangedNotification $notification, array $channels, User $notifiable): bool {
                $data = $notification->toArray($notifiable);

                return $data['status'] === 'processing'
                    && $data['previous_status'] === 'pending'
                    && $data['type'] === 'order_status_changed';
            }
        );
    }

    public function test_shipping_update_notifies_customer_with_tracking_details(): void
    {
        Notification::fake();

        $customer = User::factory()->create();
        $order = $this->createOrder($customer, [
            'status' => 'processing',
            'payment_status' => 'paid',
        ]);

        $order->update([
            'status' => 'shipped',
            'tracking_number' => 'JNE-123456789',
            'shipping_note' => 'Paket sudah diserahkan ke kurir.',
        ]);

        Notification::assertSentToTimes($customer, OrderStatusChangedNotification::class);
        Notification::assertSentTo(
            $customer,
            OrderStatusChangedNotification::class,
            function (OrderStatusChangedNotification $notification, array $channels, User $notifiable): bool {
                $data = $notification->toArray($notifiable);

                return $data['status'] === 'shipped'
                    && $data['tracking_number'] === 'JNE-123456789'
                    && $data['shipping_note'] === 'Paket sudah diserahkan ke kurir.'
                    && $data['action_url'] === route('dashboard.order', $data['order_id']);
            }
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createOrder(User $customer, array $attributes = []): Order
    {
        return Order::query()->create(array_merge([
            'user_id' => $customer->id,
            'order_number' => 'JJY-TEST-'.Str::upper(Str::random(12)),
            'total_amount' => 150000,
            'shipping_cost' => 15000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'midtrans',
            'shipping_address' => 'Jl. Contoh No. 1, Jakarta',
        ], $attributes));
    }
}
