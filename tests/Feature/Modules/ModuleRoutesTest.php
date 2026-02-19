<?php

namespace Tests\Feature\Modules;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Modules\Commerce\Domain\Contracts\PaymentGatewayInterface;
use Modules\Commerce\Domain\PaymentResult;
use Modules\Commerce\Domain\Product;
use Modules\Forms\Domain\Form;
use Tests\TestCase;

/**
 * Feature-level contract tests for core module HTTP routes.
 *
 * These tests verify more than response shape:
 * - business filtering and deterministic ordering,
 * - negative branches for unavailable resources,
 * - validation guarantees,
 * - health semantics under dependency failures.
 */
class ModuleRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Configure public API auth so every request simulates a real public client.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.api.public_token', 'test-public-token');
        config()->set('session.driver', 'array');
        $this->withHeader('Authorization', 'Bearer test-public-token');
    }

    /**
     * Ensure products index returns only active products and deterministic ordering.
     *
     * Note:
     * This protects frontend lists from surfacing inactive catalog entries while
     * keeping ordering stable for snapshot-like consumers.
     */
    public function test_commerce_products_index_returns_success_payload(): void
    {
        $inactive = Product::factory()->create([
            'name' => 'Zulu product',
            'active' => false,
            'updated_at' => now()->addMinute(),
        ]);
        Product::factory()->create([
            'name' => 'Beta product',
            'active' => true,
            'updated_at' => now()->subMinute(),
        ]);
        Product::factory()->create([
            'name' => 'Alpha product',
            'active' => true,
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/products');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => ['updated_at'],
            ])
            ->assertJsonPath('data.0.name', 'Alpha product')
            ->assertJsonPath('data.1.name', 'Beta product');

        $this->assertCount(2, $response->json('data'));
        $this->assertNotContains($inactive->id, collect($response->json('data'))->pluck('id')->all());
        $this->assertNotFalse(strtotime((string) $response->json('meta.updated_at')));
    }

    /**
     * Ensure product detail endpoint returns a complete payload for active products.
     *
     * Note:
     * In addition to structure checks, this validates value semantics such as
     * non-negative price and boolean stock availability.
     */
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

        $this->assertIsInt($response->json('data.price'));
        $this->assertGreaterThanOrEqual(0, $response->json('data.price'));
        $this->assertIsBool($response->json('data.stock.in_stock'));
    }

    /**
     * Ensure product detail is not exposed for inactive or missing products.
     *
     * Note:
     * This verifies the public API behavior contract and prevents accidental
     * leakage of unpublished inventory.
     */
    public function test_commerce_product_detail_returns_404_for_missing_or_inactive_product(): void
    {
        $inactiveProduct = Product::factory()->create(['active' => false]);

        $this->getJson("/api/v1/products/{$inactiveProduct->id}")
            ->assertStatus(404)
            ->assertJsonPath('error', 'Product not found');

        $this->getJson('/api/v1/products/999999')
            ->assertStatus(404)
            ->assertJsonPath('error', 'Product not found');
    }

    /**
     * Ensure checkout endpoint enforces request validation.
     *
     * Note:
     * This guards against silent acceptance of malformed payloads on a
     * write-side public endpoint.
     */
    public function test_commerce_checkout_returns_validation_error_for_invalid_payload(): void
    {
        $response = $this->postJson('/api/v1/checkout', []);

        $response->assertStatus(422)
            ->assertJsonPath('error', 'Validation failed')
            ->assertJsonStructure(['errors' => ['product_id', 'email']]);
    }

    /**
     * Ensure Stripe webhook endpoint returns 400 when signature validation fails.
     *
     * Note:
     * A fake gateway throws InvalidArgumentException to simulate invalid signature
     * handling without depending on Stripe SDK internals.
     */
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

    /**
     * Ensure forms show endpoint returns schema and derived steps consistently.
     *
     * Note:
     * This test validates contract coherence between `schema` and generated
     * `steps`, not only raw JSON shape.
     */
    public function test_forms_show_returns_form_payload(): void
    {
        $form = Form::factory()->create([
            'active' => true,
            'schema' => [
                ['type' => 'section', 'label' => 'Step 1'],
                ['name' => 'full_name', 'type' => 'text', 'label' => 'Name', 'required' => true],
                ['type' => 'section', 'label' => 'Step 2'],
                ['name' => 'email', 'type' => 'email', 'label' => 'Email', 'required' => true],
            ],
            'data_options' => ['multi_step' => true],
        ]);

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

        $schemaTypes = collect($response->json('data.schema'))->pluck('type')->all();
        $this->assertEquals(['section', 'text', 'section', 'email'], $schemaTypes);
        $this->assertCount(2, $response->json('data.steps'));
        $this->assertEquals('full_name', $response->json('data.steps.0.fields.0.name'));
        $this->assertEquals('email', $response->json('data.steps.1.fields.0.name'));
    }

    /**
     * Ensure inactive forms are not publicly retrievable.
     */
    public function test_forms_show_returns_404_for_inactive_form(): void
    {
        $form = Form::factory()->create(['active' => false]);

        $this->getJson("/api/v1/forms/{$form->id}")
            ->assertStatus(404)
            ->assertJsonPath('error', 'Form not found');
    }

    /**
     * Ensure forms submit endpoint returns validation errors for invalid payloads.
     *
     * Note:
     * This confirms error contract for client-side form integrations.
     */
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

    /**
     * Ensure health endpoint status matches check outcomes.
     *
     * Note:
     * The endpoint must report `ok` only when all checks pass; otherwise it must
     * degrade status and HTTP code consistently.
     */
    public function test_core_health_route_returns_expected_payload(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertJsonStructure([
                'status',
                'checks' => ['database', 'cache'],
                'modules',
                'php',
                'laravel',
            ]);

        $checks = $response->json('checks');
        $allChecksOk = ! in_array(false, $checks, true);

        $this->assertSame($allChecksOk ? 'ok' : 'degraded', $response->json('status'));
        $response->assertStatus($allChecksOk ? 200 : 503);
    }

    /**
     * Ensure health endpoint reports degraded state when dependencies fail.
     *
     * Note:
     * DB and cache failures are simulated to validate failure semantics and
     * prevent false positive `ok` health results.
     */
    public function test_core_health_route_returns_degraded_status_when_checks_fail(): void
    {
        DB::shouldReceive('connection->getPdo')->once()->andThrow(new \RuntimeException('db-down'));
        Cache::shouldReceive('put')->once()->andThrow(new \RuntimeException('cache-down'));

        $response = $this->getJson('/api/health');

        $response->assertStatus(503)
            ->assertJsonPath('status', 'degraded')
            ->assertJsonPath('checks.database', false)
            ->assertJsonPath('checks.cache', false);
    }
}
