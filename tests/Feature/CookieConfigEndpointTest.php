<?php

namespace Tests\Feature;

use App\Domain\Content\CookieSetting;
use App\Domain\Content\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CookieConfigEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget(CookieSetting::CACHE_KEY);
    }

    public function test_returns_default_cookie_config_when_no_settings_exist(): void
    {
        $response = $this->getJson('/api/v1/cookies/config');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'banner' => [
                        'enabled',
                        'title',
                        'description',
                        'accept_all_label',
                        'reject_all_label',
                        'customize_label',
                        'save_label',
                        'position',
                        'theme',
                    ],
                    'categories' => [
                        'necessary' => ['name', 'description', 'required', 'default_enabled'],
                        'analytics' => ['name', 'description', 'required', 'default_enabled'],
                        'marketing' => ['name', 'description', 'required', 'default_enabled'],
                        'preferences' => ['name', 'description', 'required', 'default_enabled'],
                    ],
                    'services',
                    'policy_links' => [
                        'privacy_policy' => ['label', 'url'],
                        'cookie_policy' => ['label', 'url'],
                    ],
                ],
                'meta' => ['updated_at'],
            ])
            ->assertJsonPath('data.banner.enabled', true)
            ->assertJsonPath('data.banner.title', 'Tato stránka používá cookies')
            ->assertJsonPath('data.banner.position', 'bottom')
            ->assertJsonPath('data.categories.necessary.required', true)
            ->assertJsonPath('data.categories.necessary.default_enabled', true)
            ->assertJsonPath('data.categories.analytics.required', false)
            ->assertJsonPath('data.categories.analytics.default_enabled', false)
            ->assertJsonPath('data.policy_links.privacy_policy.url', '/privacy-policy')
            ->assertJsonPath('data.policy_links.cookie_policy.url', '/cookie-policy');
    }

    public function test_returns_saved_cookie_settings(): void
    {
        CookieSetting::create([
            'banner' => [
                'enabled' => true,
                'title' => 'We use cookies',
                'description' => 'Custom description',
                'accept_all_label' => 'Accept',
                'reject_all_label' => 'Reject',
                'customize_label' => 'Settings',
                'save_label' => 'Save',
                'position' => 'center',
                'theme' => 'dark',
            ],
            'categories' => [
                'necessary' => [
                    'name' => 'Essential',
                    'description' => 'Required cookies.',
                    'required' => true,
                    'default_enabled' => true,
                ],
                'analytics' => [
                    'name' => 'Analytics',
                    'description' => 'Analytics cookies.',
                    'required' => false,
                    'default_enabled' => false,
                ],
            ],
            'services' => [
                'necessary' => [
                    ['name' => 'Session', 'description' => 'Session cookie.', 'cookie_pattern' => 'laravel_session'],
                ],
            ],
            'policy_links' => [
                'privacy_policy' => [
                    'label' => 'Privacy',
                    'link_type' => 'url',
                    'url' => '/privacy',
                    'page_id' => null,
                ],
                'cookie_policy' => [
                    'label' => 'Cookies',
                    'link_type' => 'url',
                    'url' => '/cookies',
                    'page_id' => null,
                ],
            ],
        ]);

        $response = $this->getJson('/api/v1/cookies/config');

        $response->assertStatus(200)
            ->assertJsonPath('data.banner.title', 'We use cookies')
            ->assertJsonPath('data.banner.position', 'center')
            ->assertJsonPath('data.banner.theme', 'dark')
            ->assertJsonPath('data.categories.necessary.name', 'Essential')
            ->assertJsonPath('data.categories.necessary.required', true)
            ->assertJsonPath('data.policy_links.privacy_policy.label', 'Privacy')
            ->assertJsonPath('data.policy_links.privacy_policy.url', '/privacy');
    }

    public function test_cookie_config_response_is_cached(): void
    {
        $banner = CookieSetting::defaultBanner();
        CookieSetting::create([
            'banner' => $banner,
            'categories' => CookieSetting::defaultCategories(),
            'services' => CookieSetting::defaultServices(),
            'policy_links' => CookieSetting::defaultPolicyLinks(),
        ]);

        $this->getJson('/api/v1/cookies/config')->assertStatus(200);

        $banner['title'] = 'Updated title';
        CookieSetting::query()->update([
            'banner' => json_encode($banner),
            'updated_at' => now()->addSecond(),
        ]);

        $response = $this->getJson('/api/v1/cookies/config');
        $response->assertJsonPath('data.banner.title', 'Updated title');
    }

    public function test_policy_links_resolve_page_urls(): void
    {
        $page = Page::create([
            'title' => ['cs' => 'Zásady ochrany'],
            'slug' => 'zasady-ochrany',
            'status' => 'published',
            'content' => [],
        ]);

        CookieSetting::create([
            'banner' => CookieSetting::defaultBanner(),
            'categories' => CookieSetting::defaultCategories(),
            'services' => CookieSetting::defaultServices(),
            'policy_links' => [
                'privacy_policy' => [
                    'label' => 'Zásady ochrany',
                    'link_type' => 'page',
                    'url' => null,
                    'page_id' => $page->id,
                ],
                'cookie_policy' => [
                    'label' => 'Zásady cookies',
                    'link_type' => 'url',
                    'url' => '/cookie-policy',
                    'page_id' => null,
                ],
            ],
        ]);

        $response = $this->getJson('/api/v1/cookies/config');

        $response->assertStatus(200)
            ->assertJsonPath('data.policy_links.privacy_policy.label', 'Zásady ochrany')
            ->assertJsonPath('data.policy_links.privacy_policy.url', '/zasady-ochrany')
            ->assertJsonPath('data.policy_links.cookie_policy.url', '/cookie-policy');
    }
}
