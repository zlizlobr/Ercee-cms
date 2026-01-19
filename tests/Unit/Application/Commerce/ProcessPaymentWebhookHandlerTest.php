<?php

namespace Tests\Unit\Application\Commerce;

use App\Application\Commerce\Commands\ProcessWebhookCommand;
use App\Application\Commerce\ProcessPaymentWebhookHandler;
use App\Domain\Commerce\Events\OrderPaid;
use App\Domain\Commerce\Order;
use App\Domain\Commerce\Payment;
use App\Domain\Commerce\Product;
use App\Domain\Subscriber\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ProcessPaymentWebhookHandlerTest extends TestCase
{
    use RefreshDatabase;

    private ProcessPaymentWebhookHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new ProcessPaymentWebhookHandler;
    }

    public function test_returns_payment_not_found_when_transaction_does_not_exist(): void
    {
        $command = new ProcessWebhookCommand(
            transactionId: 'non-existent-transaction',
            status: Payment::STATUS_PAID,
            success: true,
        );

        $result = $this->handler->handle($command);

        $this->assertFalse($result->isSuccess());
        $this->assertStringContainsString('Payment not found', $result->error);
    }

    public function test_marks_order_as_paid_and_dispatches_event_on_successful_payment(): void
    {
        Event::fake([OrderPaid::class]);

        $subscriber = Subscriber::factory()->create();
        $product = Product::factory()->create();

        $order = Order::factory()->create([
            'subscriber_id' => $subscriber->id,
            'product_id' => $product->id,
            'status' => Order::STATUS_PENDING,
        ]);

        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'transaction_id' => 'txn_123',
            'status' => Payment::STATUS_PENDING,
        ]);

        $command = new ProcessWebhookCommand(
            transactionId: 'txn_123',
            status: Payment::STATUS_PAID,
            success: true,
            payload: ['event_id' => 'evt_123'],
        );

        $result = $this->handler->handle($command);

        $this->assertTrue($result->isSuccess());
        $this->assertEquals($order->id, $result->orderId);
        $this->assertEquals('Order paid successfully', $result->message);

        $order->refresh();
        $payment->refresh();

        $this->assertEquals(Order::STATUS_PAID, $order->status);
        $this->assertEquals(Payment::STATUS_PAID, $payment->status);
        $this->assertArrayHasKey('event_id', $payment->payload);

        Event::assertDispatched(OrderPaid::class);
    }

    public function test_marks_order_as_failed_on_failed_payment(): void
    {
        Event::fake([OrderPaid::class]);

        $subscriber = Subscriber::factory()->create();
        $product = Product::factory()->create();

        $order = Order::factory()->create([
            'subscriber_id' => $subscriber->id,
            'product_id' => $product->id,
            'status' => Order::STATUS_PENDING,
        ]);

        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'transaction_id' => 'txn_456',
            'status' => Payment::STATUS_PENDING,
        ]);

        $command = new ProcessWebhookCommand(
            transactionId: 'txn_456',
            status: Payment::STATUS_FAILED,
            success: false,
        );

        $result = $this->handler->handle($command);

        $this->assertTrue($result->isSuccess());
        $this->assertEquals('Order marked as failed', $result->message);

        $order->refresh();
        $payment->refresh();

        $this->assertEquals(Order::STATUS_FAILED, $order->status);
        $this->assertEquals(Payment::STATUS_FAILED, $payment->status);

        Event::assertNotDispatched(OrderPaid::class);
    }
}
