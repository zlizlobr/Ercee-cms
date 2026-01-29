<?php

namespace Tests\Feature;

use Modules\Commerce\Domain\Contracts\PaymentGatewayInterface;
use Modules\Commerce\Domain\Order;
use Modules\Commerce\Domain\Product;
use App\Domain\Subscriber\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $mockGateway = Mockery::mock(PaymentGatewayInterface::class);
        $mockGateway->shouldReceive('createPayment')
            ->andReturn('https://payment.example.com/checkout/123');

        $this->app->instance(PaymentGatewayInterface::class, $mockGateway);
    }

    public function test_can_initiate_checkout_successfully(): void
    {
        $product = Product::factory()->create([
            'active' => true,
            'price' => 9900,
        ]);

        $response = $this->postJson('/api/v1/checkout', [
            'product_id' => $product->id,
            'email' => 'customer@example.com',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Checkout initiated',
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'order_id',
                    'redirect_url',
                ],
            ]);

        $this->assertDatabaseHas('subscribers', [
            'email' => 'customer@example.com',
        ]);

        $this->assertDatabaseHas('orders', [
            'product_id' => $product->id,
            'email' => 'customer@example.com',
            'price' => 9900,
            'status' => Order::STATUS_PENDING,
        ]);
    }

    public function test_returns_404_for_inactive_product(): void
    {
        $product = Product::factory()->create(['active' => false]);

        $response = $this->postJson('/api/v1/checkout', [
            'product_id' => $product->id,
            'email' => 'customer@example.com',
        ]);

        $response->assertStatus(404)
            ->assertJson(['error' => 'Product not found or inactive']);
    }

    public function test_returns_422_for_invalid_email(): void
    {
        $product = Product::factory()->create(['active' => true]);

        $response = $this->postJson('/api/v1/checkout', [
            'product_id' => $product->id,
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422)
            ->assertJson(['error' => 'Validation failed']);
    }

    public function test_returns_422_for_missing_product_id(): void
    {
        $response = $this->postJson('/api/v1/checkout', [
            'email' => 'customer@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJson(['error' => 'Validation failed']);
    }

    public function test_uses_existing_subscriber_if_email_exists(): void
    {
        $existingSubscriber = Subscriber::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $product = Product::factory()->create(['active' => true]);

        $response = $this->postJson('/api/v1/checkout', [
            'product_id' => $product->id,
            'email' => 'existing@example.com',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseCount('subscribers', 1);

        $order = Order::first();
        $this->assertEquals($existingSubscriber->id, $order->subscriber_id);
    }
}
