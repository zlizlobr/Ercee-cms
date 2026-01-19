<?php

namespace Tests\Feature;

use App\Domain\Commerce\Contracts\PaymentGatewayInterface;
use App\Domain\Commerce\Events\OrderPaid;
use App\Domain\Commerce\Order;
use App\Domain\Commerce\Payment;
use App\Domain\Commerce\PaymentResult;
use App\Domain\Commerce\Product;
use App\Domain\Subscriber\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

class WebhookProcessingTest extends TestCase
{
    use RefreshDatabase;

    public function test_processes_successful_payment_webhook(): void
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
            'transaction_id' => 'pi_test_123',
            'status' => Payment::STATUS_PENDING,
        ]);

        $mockGateway = Mockery::mock(PaymentGatewayInterface::class);
        $mockGateway->shouldReceive('handleWebhook')
            ->andReturn(PaymentResult::success('pi_test_123', ['event' => 'payment_intent.succeeded']));

        $this->app->instance(PaymentGatewayInterface::class, $mockGateway);

        $response = $this->postJson('/api/webhooks/stripe', [
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => ['id' => 'pi_test_123']],
        ]);

        $response->assertStatus(200)
            ->assertSee('OK');

        $order->refresh();
        $payment->refresh();

        $this->assertEquals(Order::STATUS_PAID, $order->status);
        $this->assertEquals(Payment::STATUS_PAID, $payment->status);

        Event::assertDispatched(OrderPaid::class);
    }

    public function test_processes_failed_payment_webhook(): void
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
            'transaction_id' => 'pi_test_failed',
            'status' => Payment::STATUS_PENDING,
        ]);

        $mockGateway = Mockery::mock(PaymentGatewayInterface::class);
        $mockGateway->shouldReceive('handleWebhook')
            ->andReturn(PaymentResult::failed('pi_test_failed', ['error' => 'card_declined']));

        $this->app->instance(PaymentGatewayInterface::class, $mockGateway);

        $response = $this->postJson('/api/webhooks/stripe', [
            'type' => 'payment_intent.payment_failed',
        ]);

        $response->assertStatus(200);

        $order->refresh();
        $payment->refresh();

        $this->assertEquals(Order::STATUS_FAILED, $order->status);
        $this->assertEquals(Payment::STATUS_FAILED, $payment->status);

        Event::assertNotDispatched(OrderPaid::class);
    }

    public function test_returns_400_for_invalid_signature(): void
    {
        $mockGateway = Mockery::mock(PaymentGatewayInterface::class);
        $mockGateway->shouldReceive('handleWebhook')
            ->andThrow(new \InvalidArgumentException('Invalid signature'));

        $this->app->instance(PaymentGatewayInterface::class, $mockGateway);

        $response = $this->postJson('/api/webhooks/stripe', []);

        $response->assertStatus(400)
            ->assertSee('Invalid signature');
    }

    public function test_returns_404_for_unknown_payment(): void
    {
        $mockGateway = Mockery::mock(PaymentGatewayInterface::class);
        $mockGateway->shouldReceive('handleWebhook')
            ->andReturn(PaymentResult::success('unknown_transaction', []));

        $this->app->instance(PaymentGatewayInterface::class, $mockGateway);

        $response = $this->postJson('/api/webhooks/stripe', []);

        $response->assertStatus(404)
            ->assertSee('Payment not found');
    }
}
