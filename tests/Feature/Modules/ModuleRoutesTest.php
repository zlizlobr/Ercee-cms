<?php

namespace Tests\Feature\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Commerce\Domain\Product;
use Modules\Forms\Domain\Form;
use Tests\TestCase;

class ModuleRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_commerce_products_route_is_registered(): void
    {
        $response = $this->getJson('/api/v1/products');

        $this->assertNotEquals(404, $response->status());
    }

    public function test_commerce_product_detail_route_is_registered(): void
    {
        $product = Product::factory()->create(['active' => true]);

        $response = $this->getJson("/api/v1/products/{$product->id}");

        $this->assertNotEquals(404, $response->status());
    }

    public function test_commerce_checkout_route_is_registered(): void
    {
        $response = $this->postJson('/api/v1/checkout', []);

        // 422 = validation error, not 404 = route exists
        $this->assertNotEquals(404, $response->status());
    }

    public function test_commerce_stripe_webhook_route_is_registered(): void
    {
        $response = $this->postJson('/api/webhooks/stripe', []);

        // May return 400/403 due to webhook.whitelist middleware, but not 404
        $this->assertNotEquals(404, $response->status());
    }

    public function test_forms_show_route_is_registered(): void
    {
        $form = Form::factory()->create(['active' => true]);

        $response = $this->getJson("/api/v1/forms/{$form->id}");

        $this->assertNotEquals(404, $response->status());
    }

    public function test_forms_submit_route_is_registered(): void
    {
        $form = Form::factory()->create(['active' => true]);

        $response = $this->postJson("/api/v1/forms/{$form->id}/submit", [
            'email' => 'test@example.com',
        ]);

        $this->assertNotEquals(404, $response->status());
    }

    public function test_core_routes_still_work(): void
    {
        $response = $this->getJson('/api/health');

        $this->assertNotEquals(404, $response->status());
    }
}
