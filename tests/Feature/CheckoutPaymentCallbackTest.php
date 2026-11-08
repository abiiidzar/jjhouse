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
use Tests\TestCase;

class CheckoutPaymentCallbackTest extends TestCase
{
    use DatabaseMigrations;

    public function test_bri_settlement_without_fraud_status_marks_order_paid(): void
    {
        $order = $this->createUnpaidOrder();
        $this->mockNotification([
            'order_id' => $order->order_number,
            'transaction_status' => 'settlement',
            'fraud_status' => null,
            'payment_type' => 'bank_transfer',
            'gross_amount' => '165000.00',
            'status_code' => '200',
            'signature_key' => 'valid-signature',
        ]);

        $this->post('/payment-callback')
            ->assertOk()
            ->assertJson(['message' => 'OK']);

        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame('processing', $order->fresh()->status);
    }

    public function test_successful_payment_callback_does_not_decrease_reserved_stock_again(): void
    {
        $order = $this->createUnpaidOrder();
        $size = $this->addReservedStockItem($order);
        $this->mockNotification([
            'order_id' => $order->order_number,
            'transaction_status' => 'settlement',
            'fraud_status' => null,
            'payment_type' => 'bank_transfer',
            'gross_amount' => '165000.00',
            'status_code' => '200',
            'signature_key' => 'valid-signature',
        ]);

        $this->post('/payment-callback')->assertOk();

        $this->assertSame(9, $size->fresh()->stock);
        $this->assertTrue($order->fresh()->stock_reserved);
    }

    public function test_failed_payment_callback_restores_reserved_stock_only_once(): void
    {
        $order = $this->createUnpaidOrder();
        $size = $this->addReservedStockItem($order);
        $notification = [
            'order_id' => $order->order_number,
            'transaction_status' => 'expire',
            'fraud_status' => null,
            'payment_type' => 'bank_transfer',
            'gross_amount' => '165000.00',
            'status_code' => '200',
            'signature_key' => 'valid-signature',
        ];

        $this->mockNotification($notification);
        $this->post('/payment-callback')->assertOk();

        $this->mockNotification($notification);
        $this->post('/payment-callback')->assertOk();

        $this->assertSame(10, $size->fresh()->stock);
        $this->assertFalse($order->fresh()->stock_reserved);
    }

    public function test_capture_with_unaccepted_fraud_status_does_not_mark_order_paid(): void
    {
        $order = $this->createUnpaidOrder();
        $this->mockNotification([
            'order_id' => $order->order_number,
            'transaction_status' => 'capture',
            'fraud_status' => 'challenge',
            'payment_type' => 'credit_card',
            'gross_amount' => '165000.00',
            'status_code' => '200',
            'signature_key' => 'valid-signature',
        ]);

        $this->post('/payment-callback')
            ->assertOk()
            ->assertJson(['message' => 'OK']);

        $this->assertSame('unpaid', $order->fresh()->payment_status);
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_callback_for_replaced_payment_attempt_is_ignored(): void
    {
        $order = $this->createUnpaidOrder();
        $activeAttemptId = $order->order_number.'-PAY-newattempt';
        $order->update(['payment_attempt_id' => $activeAttemptId]);

        $this->mockNotification([
            'order_id' => $order->order_number,
            'transaction_status' => 'settlement',
            'fraud_status' => null,
            'payment_type' => 'qris',
            'gross_amount' => '165000.00',
            'status_code' => '200',
            'signature_key' => 'valid-signature',
        ]);

        $this->post('/payment-callback')
            ->assertOk()
            ->assertJson(['message' => 'OK']);

        $this->assertSame('unpaid', $order->fresh()->payment_status);
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_callback_for_active_payment_attempt_marks_order_paid(): void
    {
        $order = $this->createUnpaidOrder();
        $activeAttemptId = $order->order_number.'-PAY-active123';
        $order->update(['payment_attempt_id' => $activeAttemptId]);

        $this->mockNotification([
            'order_id' => $activeAttemptId,
            'transaction_status' => 'settlement',
            'fraud_status' => null,
            'payment_type' => 'qris',
            'gross_amount' => '165000.00',
            'status_code' => '200',
            'signature_key' => 'valid-signature',
        ]);

        $this->post('/payment-callback')
            ->assertOk()
            ->assertJson(['message' => 'OK']);

        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame('processing', $order->fresh()->status);
    }

    /**
     * @param  array<string, mixed>  $notification
     */
    private function mockNotification(array $notification): void
    {
        $this->mock(MidtransService::class, function (MockInterface $mock) use ($notification): void {
            $mock->shouldReceive('handleNotification')->once()->andReturn($notification);
            $mock->shouldReceive('verifySignature')->once()->andReturnTrue();
        });
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

    private function addReservedStockItem(Order $order): ProductSize
    {
        $category = Category::query()->create([
            'name' => 'Test Category',
            'slug' => 'test-category-'.uniqid(),
        ]);
        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => 'test-product-'.uniqid(),
            'description' => 'Test product description',
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

        return $size;
    }
}
