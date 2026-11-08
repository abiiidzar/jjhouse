<?php

namespace Tests\Unit;

use App\Services\MidtransService;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use RuntimeException;
use Tests\TestCase;

class MidtransServiceTest extends TestCase
{
    public function test_default_checkout_selection_leaves_midtrans_payment_methods_unrestricted(): void
    {
        $service = new MidtransService();

        $this->assertNull($service->enabledPaymentsFor('midtrans'));
        $this->assertSame(['bank_transfer'], $service->enabledPaymentsFor('bank_transfer'));
        $this->assertSame(['gopay'], $service->enabledPaymentsFor('gopay'));
        $this->assertSame(['qris'], $service->enabledPaymentsFor('qris'));
    }

    public function test_unknown_payment_method_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new MidtransService())->enabledPaymentsFor('unknown');
    }

    public function test_pending_transaction_is_cancelled_before_payment_method_change(): void
    {
        config([
            'services.midtrans.server_key' => 'test-server-key',
            'services.midtrans.is_production' => false,
        ]);
        Http::fake([
            'api.sandbox.midtrans.com/v2/order-123/status' => Http::response([
                'transaction_status' => 'pending',
            ]),
            'api.sandbox.midtrans.com/v2/order-123/cancel' => Http::response([
                'status_code' => '200',
                'transaction_status' => 'cancel',
            ]),
        ]);

        $this->assertTrue((new MidtransService())->cancelPendingTransaction('order-123'));

        Http::assertSentCount(2);
    }

    public function test_missing_transaction_allows_a_new_payment_attempt(): void
    {
        config([
            'services.midtrans.server_key' => 'test-server-key',
            'services.midtrans.is_production' => false,
        ]);
        Http::fake([
            'api.sandbox.midtrans.com/v2/order-123/status' => Http::response([
                'status_code' => '404',
            ], 404),
        ]);

        $this->assertTrue((new MidtransService())->cancelPendingTransaction('order-123'));
        Http::assertSentCount(1);
    }

    public function test_paid_transaction_cannot_be_replaced(): void
    {
        config([
            'services.midtrans.server_key' => 'test-server-key',
            'services.midtrans.is_production' => false,
        ]);
        Http::fake([
            'api.sandbox.midtrans.com/v2/order-123/status' => Http::response([
                'transaction_status' => 'settlement',
            ]),
        ]);

        $this->assertFalse((new MidtransService())->cancelPendingTransaction('order-123'));
        Http::assertSentCount(1);
    }

    public function test_missing_transaction_status_is_not_reported_as_paid(): void
    {
        config([
            'services.midtrans.server_key' => 'test-server-key',
            'services.midtrans.is_production' => false,
        ]);
        Http::fake([
            'api.sandbox.midtrans.com/v2/order-123/status' => Http::response([
                'status_code' => '200',
                'status_message' => 'Success',
            ]),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('did not return a transaction status');

        (new MidtransService())->cancelPendingTransaction('order-123');
    }

    public function test_cancel_error_is_accepted_if_follow_up_status_confirms_cancellation(): void
    {
        config([
            'services.midtrans.server_key' => 'test-server-key',
            'services.midtrans.is_production' => false,
        ]);
        Http::fake([
            'api.sandbox.midtrans.com/v2/order-123/status' => Http::sequence()
                ->push(['transaction_status' => 'pending'])
                ->push(['transaction_status' => 'cancel']),
            'api.sandbox.midtrans.com/v2/order-123/cancel' => Http::response([
                'status_code' => '412',
                'status_message' => 'Transaction status has changed.',
            ], 412),
        ]);

        $this->assertTrue((new MidtransService())->cancelPendingTransaction('order-123'));
        Http::assertSentCount(3);
    }

    public function test_cancel_error_does_not_allow_new_payment_while_status_remains_pending(): void
    {
        config([
            'services.midtrans.server_key' => 'test-server-key',
            'services.midtrans.is_production' => false,
        ]);
        Http::fake([
            'api.sandbox.midtrans.com/v2/order-123/status' => Http::sequence()
                ->push(['transaction_status' => 'pending'])
                ->push(['transaction_status' => 'pending']),
            'api.sandbox.midtrans.com/v2/order-123/cancel' => Http::response([
                'status_code' => '412',
                'status_message' => 'Transaction status has changed.',
            ], 412),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Midtrans could not confirm cancellation');

        (new MidtransService())->cancelPendingTransaction('order-123');
    }
}
