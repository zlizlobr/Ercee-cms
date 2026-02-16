<?php

namespace Tests\Feature\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Modules\Commerce\Domain\Contracts\PaymentGatewayInterface;
use Modules\Commerce\Domain\PaymentResult;
use Modules\Commerce\Domain\Product;
use Modules\Forms\Domain\Form;
use Tests\TestCase;

class ModuleRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_commerce_products_index_returns_success_payload(): void
    {
        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => ['updated_at'],
            ]);
    }

    public function test_commerce_product_detail_returns_product_payload(): void
    {
        $product = Product::factory()->create(['active' => true]);

        $response = $this->getJson("/api/v1/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'type',
                    'price',
                    'stock' => ['manage_stock', 'quantity', 'status', 'in_stock'],
                ],
                'meta' => ['updated_at'],
            ]);
    }

    public function test_commerce_checkout_returns_validation_error_for_invalid_payload(): void
    {
        $response = $this->postJson('/api/v1/checkout', []);

        $response->assertStatus(422)
            ->assertJsonPath('error', 'Validation failed')
            ->assertJsonStructure(['errors' => ['product_id', 'email']]);
    }

    public function test_commerce_stripe_webhook_returns_bad_request_on_invalid_signature(): void
    {
        $this->app->instance(PaymentGatewayInterface::class, new class implements PaymentGatewayInterface
        {
            public function createPayment(\Modules\Commerce\Domain\Order $order): string
            {
                return 'https://example.test/payment';
            }

            public function handleWebhook(Request $request): PaymentResult
            {
                throw new \InvalidArgumentException('Invalid signature');
            }

            public function getGatewayName(): string
            {
                return 'test';
            }
        });

        $response = $this->postJson('/api/webhooks/stripe', []);

        $response->assertStatus(400);
    }

    public function test_forms_show_returns_form_payload(): void
    {
        $form = Form::factory()->create(['active' => true]);

        $response = $this->getJson("/api/v1/forms/{$form->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $form->id)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'schema',
                    'steps',
                    'data_options',
                    'submit_button_text',
                    'success_title',
                    'success_message',
                ],
            ]);
    }

    public function test_forms_submit_returns_validation_error_for_invalid_payload(): void
    {
        $form = Form::factory()->create(['active' => true]);

        $response = $this->postJson("/api/v1/forms/{$form->id}/submit", [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error', 'Validation failed')
            ->assertJsonStructure(['errors' => ['email']]);
    }

    public function test_core_health_route_returns_expected_payload(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'checks' => ['database', 'cache'],
                'modules',
                'php',
                'laravel',
            ]);
    }
}
