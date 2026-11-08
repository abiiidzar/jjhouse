<?php

namespace Tests\Feature;

use App\Models\Order;
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
}
