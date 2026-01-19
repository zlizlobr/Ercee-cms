<?php

namespace Tests\Unit\Application\Commerce;

use App\Application\Commerce\Commands\CreateOrderCommand;
use App\Application\Commerce\CreateOrderHandler;
use App\Domain\Commerce\Contracts\PaymentGatewayInterface;
use App\Domain\Commerce\Order;
use App\Domain\Commerce\Product;
use App\Domain\Subscriber\Subscriber;
use App\Domain\Subscriber\SubscriberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CreateOrderHandlerTest extends TestCase
{
    use RefreshDatabase;

    private CreateOrderHandler $handler;

    private SubscriberService $subscriberService;

    private PaymentGatewayInterface $paymentGateway;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subscriberService = Mockery::mock(SubscriberService::class);
        $this->paymentGateway = Mockery::mock(PaymentGatewayInterface::class);
        $this->handler = new CreateOrderHandler($this->subscriberService, $this->paymentGateway);
    }

    public function test_returns_product_not_found_when_product_does_not_exist(): void
    {
        $command = new CreateOrderCommand(
            productId: 999,
            email: 'test@example.com',
        );

        $result = $this->handler->handle($command);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Product not found or inactive', $result->error);
    }

    public function test_returns_product_not_found_when_product_is_inactive(): void
    {
        $product = Product::factory()->create(['active' => false]);

        $command = new CreateOrderCommand(
            productId: $product->id,
            email: 'test@example.com',
        );

        $result = $this->handler->handle($command);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Product not found or inactive', $result->error);
    }

    public function test_creates_order_and_initiates_payment_on_success(): void
    {
        $product = Product::factory()->create([
            'active' => true,
            'price' => 10000,
        ]);

        $subscriber = Subscriber::factory()->create();

        $this->subscriberService
            ->shouldReceive('findOrCreateByEmail')
            ->with('test@example.com', 'checkout:'.$product->id)
            ->once()
            ->andReturn($subscriber);

        $this->paymentGateway
            ->shouldReceive('createPayment')
            ->once()
            ->andReturn('https://payment.example.com/checkout/123');

        $command = new CreateOrderCommand(
            productId: $product->id,
            email: 'test@example.com',
        );

        $result = $this->handler->handle($command);

        $this->assertTrue($result->isSuccess());
        $this->assertNotNull($result->orderId);
        $this->assertEquals('https://payment.example.com/checkout/123', $result->redirectUrl);

        $order = Order::find($result->orderId);
        $this->assertNotNull($order);
        $this->assertEquals($subscriber->id, $order->subscriber_id);
        $this->assertEquals($product->id, $order->product_id);
        $this->assertEquals('test@example.com', $order->email);
        $this->assertEquals(10000, $order->price);
        $this->assertEquals(Order::STATUS_PENDING, $order->status);
    }
}
