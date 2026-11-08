<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery\MockInterface;
use RuntimeException;
use Tests\TestCase;

class ChangePaymentMethodTest extends TestCase
{
    use DatabaseMigrations;

    public function test_retry_payment_opens_payment_view_when_new_snap_token_is_created(): void
    {
        $order = $this->createUnpaidOrder();
        $this->actingAs($order->user);

        $this->mock(MidtransService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('cancelPendingTransaction')->once()->andReturnTrue();
            $mock->shouldReceive('createTransaction')
                ->once()
                ->with(
                    \Mockery::on(fn ($paymentOrder): bool => str_contains($paymentOrder->order_number, '-PAY-')),
                    \Mockery::type('array'),
                    \Mockery::type('array'),
                    'midtrans'
                )
                ->andReturn('retry-snap-token');
        });

        $this->post(route('order.retry-payment', $order))
            ->assertOk()
            ->assertViewIs('payment.index')
            ->assertViewHas('snapToken', 'retry-snap-token')
            ->assertViewHas('canChangePaymentMethod', true);

        $this->assertStringContainsString('-PAY-', $order->fresh()->payment_attempt_id);
    }

    public function test_user_can_cancel_pending_payment_and_open_midtrans_methods_again(): void
    {
        $order = $this->createUnpaidOrder();
        $activeAttemptId = $order->order_number;

        $this->actingAs($order->user);
        $this->mock(MidtransService::class, function (MockInterface $mock) use ($activeAttemptId): void {
            $mock->shouldReceive('cancelPendingTransaction')
                ->once()
                ->with($activeAttemptId)
                ->andReturnTrue();
            $mock->shouldReceive('createTransaction')
                ->once()
                ->with(
                    \Mockery::on(fn ($paymentOrder): bool => str_starts_with(
                        $paymentOrder->order_number,
                        $activeAttemptId.'-PAY-'
                    )),
                    \Mockery::type('array'),
                    \Mockery::type('array'),
                    'midtrans'
                )
                ->andReturn('new-snap-token');
        });

        $response = $this->post(route('order.change-payment-method', $order), [
            'payment_attempt_id' => $activeAttemptId,
        ]);

        $response->assertOk()
            ->assertViewIs('payment.index')
            ->assertViewHas('snapToken', 'new-snap-token')
            ->assertViewHas('canChangePaymentMethod', true);

        $this->assertNotSame($activeAttemptId, $order->fresh()->payment_attempt_id);
        $this->assertSame('unpaid', $order->fresh()->payment_status);
        $this->assertSame('midtrans', $order->fresh()->payment_method);
    }

    public function test_user_is_not_given_a_new_payment_if_existing_transaction_cannot_be_cancelled(): void
    {
        $order = $this->createUnpaidOrder();
        $activeAttemptId = $order->order_number;

        $this->actingAs($order->user);
        $this->mock(MidtransService::class, function (MockInterface $mock) use ($activeAttemptId): void {
            $mock->shouldReceive('cancelPendingTransaction')
                ->once()
                ->with($activeAttemptId)
                ->andReturnFalse();
            $mock->shouldNotReceive('createTransaction');
        });

        $this->post(route('order.change-payment-method', $order), [
            'payment_attempt_id' => $activeAttemptId,
        ])->assertRedirect(route('dashboard.order', $order))
            ->assertSessionHas('warning');

        $this->assertNull($order->fresh()->payment_attempt_id);
    }

    public function test_non_pending_status_warning_does_not_claim_payment_may_be_complete(): void
    {
        $order = $this->createUnpaidOrder();
        $this->actingAs($order->user);

        $this->mock(MidtransService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('cancelPendingTransaction')->once()->andReturnFalse();
            $mock->shouldNotReceive('createTransaction');
        });

        $this->followingRedirects()
            ->post(route('order.retry-payment', $order))
            ->assertOk()
            ->assertSee('Status transaksi sebelumnya di Midtrans bukan lagi menunggu pembayaran')
            ->assertDontSee('Pembayaran mungkin sudah selesai');
    }

    public function test_retry_failure_returns_to_order_details_with_visible_reason(): void
    {
        $order = $this->createUnpaidOrder();
        $this->actingAs($order->user);

        $this->mock(MidtransService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('cancelPendingTransaction')
                ->once()
                ->andThrow(new RuntimeException('Unable to verify current payment status.'));
            $mock->shouldNotReceive('createTransaction');
        });

        $this->followingRedirects()
            ->post(route('order.retry-payment', $order))
            ->assertOk()
            ->assertSee('Pembayaran sebelumnya belum dapat dipastikan batal');
    }

    public function test_cancelling_order_restores_reserved_stock_once(): void
    {
        $order = $this->createUnpaidOrder();
        $category = Category::query()->create([
            'name' => 'Cancellation Test',
            'slug' => 'cancellation-test-'.uniqid(),
        ]);
        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Cancellation Product',
            'slug' => 'cancellation-product-'.uniqid(),
            'description' => 'Cancellation test product',
            'price' => 150000,
            'stock' => 9,
        ]);
        $size = ProductSize::query()->create([
            'product_id' => $product->id,
            'size' => 'M',
            'stock' => 9,
        ]);
        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_size_id' => $size->id,
            'quantity' => 1,
            'price' => 150000,
            'is_preorder' => false,
        ]);
        $order->update(['stock_reserved' => true]);
        $this->actingAs($order->user);

        $this->put(route('order.cancel', $order))->assertSessionHas('success');
        $this->assertSame(10, $size->fresh()->stock);
        $this->assertSame('customer', $order->fresh()->cancellation_source);

        $this->put(route('order.cancel', $order))->assertSessionHas('error');
        $this->assertSame(10, $size->fresh()->stock);
        $this->assertFalse($order->fresh()->stock_reserved);
    }

    private function createUnpaidOrder(): Order
    {
        return Order::query()->create([
            'user_id' => User::factory()->create()->id,
            'order_number' => 'JJY-TEST-'.uniqid(),
            'total_amount' => 150000,
            'shipping_cost' => 15000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'midtrans',
            'shipping_address' => 'Jl. Contoh No. 1, Jakarta',
        ]);
    }
}
