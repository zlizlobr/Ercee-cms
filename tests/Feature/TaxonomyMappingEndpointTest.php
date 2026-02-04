<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Commerce\Domain\Product;
use Modules\Commerce\Domain\Taxonomy;
use Tests\TestCase;

class TaxonomyMappingEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_empty_mapping_when_no_terms_exist(): void
    {
        $response = $this->getJson('/api/v1/taxonomies/mapping');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'products' => [
                        'category' => [],
                        'tag' => [],
                        'brand' => [],
                    ],
                ],
            ]);
    }

    public function test_returns_only_terms_linked_to_active_products_sorted_by_slug(): void
    {
        $activeProduct = Product::factory()->create(['active' => true]);
        $inactiveProduct = Product::factory()->create(['active' => false]);

        $categoryAlpha = Taxonomy::create([
            'type' => Taxonomy::TYPE_CATEGORY,
            'name' => 'Alpha',
            'slug' => 'alpha',
        ]);

        $categoryBeta = Taxonomy::create([
            'type' => Taxonomy::TYPE_CATEGORY,
            'name' => 'Beta',
            'slug' => 'beta',
        ]);

        $categoryZeta = Taxonomy::create([
            'type' => Taxonomy::TYPE_CATEGORY,
            'name' => 'Zeta',
            'slug' => 'zeta',
        ]);

        $tagSale = Taxonomy::create([
            'type' => Taxonomy::TYPE_TAG,
            'name' => 'Sale',
            'slug' => 'sale',
        ]);

        $brandAcme = Taxonomy::create([
            'type' => Taxonomy::TYPE_BRAND,
            'name' => 'Acme',
            'slug' => 'acme',
        ]);

        $activeProduct->taxonomies()->attach([$categoryAlpha->id, $categoryBeta->id, $tagSale->id, $brandAcme->id]);
        $inactiveProduct->taxonomies()->attach([$categoryZeta->id]);

        $response = $this->getJson('/api/v1/taxonomies/mapping');

        $response->assertStatus(200)
            ->assertJsonPath('data.products.category', ['alpha', 'beta'])
            ->assertJsonPath('data.products.tag', ['sale'])
            ->assertJsonPath('data.products.brand', ['acme']);
    }
}
