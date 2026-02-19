<?php

namespace Tests\Feature;

use App\Domain\Content\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Contract coverage for public page endpoints.
 *
 * Scope:
 * - published-only visibility,
 * - deterministic listing behavior,
 * - canonical payload integrity for detail endpoint,
 * - negative branches for missing or unpublished slugs.
 */
class PageEndpointContractTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure pages index exposes only published pages in stable slug order.
     *
     * Note:
     * Stable ordering reduces frontend rendering drift and improves cache hit
     * predictability for list consumers.
     */
    public function test_pages_index_returns_only_published_pages_in_deterministic_slug_order(): void
    {
        Page::factory()->create([
            'slug' => 'zeta-page',
            'status' => Page::STATUS_PUBLISHED,
            'updated_at' => now()->subMinute(),
        ]);

        Page::factory()->create([
            'slug' => 'alpha-draft',
            'status' => Page::STATUS_DRAFT,
        ]);

        Page::factory()->create([
            'slug' => 'beta-page',
            'status' => Page::STATUS_PUBLISHED,
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/pages');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.slug', 'beta-page')
            ->assertJsonPath('data.1.slug', 'zeta-page');

        $slugs = collect($response->json('data'))->pluck('slug')->all();
        $this->assertNotContains('alpha-draft', $slugs);
        $this->assertNotNull($response->json('meta.updated_at'));
    }

    /**
     * Ensure pages detail endpoint hides draft and missing resources.
     *
     * Note:
     * Both draft and nonexistent slugs must resolve to the same public-facing
     * not-found contract to avoid leaking publication state.
     */
    public function test_pages_show_returns_404_for_draft_or_missing_slug(): void
    {
        Page::factory()->create([
            'slug' => 'draft-only',
            'status' => Page::STATUS_DRAFT,
        ]);

        $this->getJson('/api/v1/pages/draft-only')
            ->assertStatus(404)
            ->assertJsonPath('error', 'Page not found');

        $this->getJson('/api/v1/pages/missing-page')
            ->assertStatus(404)
            ->assertJsonPath('error', 'Page not found');
    }

    /**
     * Ensure published page detail response is canonical and coherent.
     *
     * Note:
     * This validates identity (`id`, `slug`) and key content fragments (`seo`,
     * `blocks`) that frontend renderers depend on.
     */
    public function test_pages_show_returns_canonical_payload_for_published_page(): void
    {
        $page = Page::factory()->create([
            'slug' => 'public-page',
            'status' => Page::STATUS_PUBLISHED,
            'title' => ['cs' => 'Verejna stranka'],
            'content' => [
                [
                    'type' => 'hero',
                    'data' => ['title' => 'Hero'],
                ],
            ],
            'seo_meta' => ['title' => 'SEO title'],
            'published_at' => now()->subDay(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/pages/public-page');

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $page->id)
            ->assertJsonPath('data.slug', 'public-page')
            ->assertJsonPath('data.seo.title', 'SEO title')
            ->assertJsonPath('data.blocks.0.type', 'hero');
    }
}
