<?php

namespace Tests\Feature;

use App\Domain\Content\Menu;
use App\Domain\Content\Navigation;
use App\Domain\Content\ThemeSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ThemeEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_default_theme_settings_when_no_settings_exist(): void
    {
        $response = $this->getJson('/api/v1/theme');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'global' => [
                        'logo' => ['type', 'text', 'image_url', 'url'],
                        'cta' => ['label', 'url'],
                    ],
                    'header' => [
                        'logo' => ['type', 'text', 'image_url', 'url'],
                        'menu',
                        'cta' => ['label', 'url'],
                    ],
                    'footer' => [
                        'logo' => ['type', 'text', 'image_url'],
                        'company_text',
                        'menus' => ['quick_links', 'services', 'contact', 'legal'],
                        'cta' => ['label', 'url'],
                        'copyright_text',
                    ],
                ],
            ])
            ->assertJsonPath('data.global.logo.type', 'text')
            ->assertJsonPath('data.global.logo.text', 'Ercee')
            ->assertJsonPath('data.global.logo.url', '/')
            ->assertJsonPath('data.global.cta.label', 'Kontaktujte nás')
            ->assertJsonPath('data.global.cta.url', '/rfq');
    }

    public function test_returns_saved_theme_settings(): void
    {
        ThemeSetting::create([
            'global' => [
                'logo_type' => 'text',
                'logo_text' => 'Custom Logo',
                'logo_image' => null,
                'logo_url' => '/home',
                'cta_label' => 'Contact Us',
                'cta_url' => '/contact',
            ],
            'header' => [
                'cta_label' => 'Header CTA',
            ],
            'footer' => [
                'company_text' => 'Custom company text',
                'copyright_text' => '© {year} Custom Corp.',
            ],
        ]);

        $response = $this->getJson('/api/v1/theme');

        $response->assertStatus(200)
            ->assertJsonPath('data.global.logo.text', 'Custom Logo')
            ->assertJsonPath('data.global.logo.url', '/home')
            ->assertJsonPath('data.global.cta.label', 'Contact Us')
            ->assertJsonPath('data.header.cta.label', 'Header CTA')
            ->assertJsonPath('data.footer.company_text', 'Custom company text');

        // Check that copyright text has {year} replaced
        $this->assertStringContains(date('Y'), $response->json('data.footer.copyright_text'));
    }

    public function test_returns_resolved_menu_for_header(): void
    {
        $menu = Menu::firstOrCreate(
            ['slug' => 'main'],
            ['name' => 'Main Navigation']
        );

        Navigation::create([
            'menu_id' => $menu->id,
            'title' => 'Home',
            'url' => '/',
            'position' => 0,
            'is_active' => true,
            'classes' => 'nav-home',
            'slug' => 'home',
        ]);

        Navigation::create([
            'menu_id' => $menu->id,
            'title' => 'About',
            'url' => '/about',
            'position' => 1,
            'is_active' => true,
            'classes' => 'nav-about',
            'slug' => 'about',
        ]);

        ThemeSetting::create([
            'global' => ThemeSetting::defaultGlobal(),
            'header' => [
                'menu_id' => $menu->id,
            ],
            'footer' => ThemeSetting::defaultFooter(),
        ]);

        Cache::forget(ThemeSetting::CACHE_KEY);

        $response = $this->getJson('/api/v1/theme');

        $response->assertStatus(200)
            ->assertJsonPath('data.header.menu.id', $menu->id)
            ->assertJsonPath('data.header.menu.name', 'Main Navigation')
            ->assertJsonPath('data.header.menu.slug', 'main')
            ->assertJsonCount(2, 'data.header.menu.items');
    }

    public function test_header_settings_override_global(): void
    {
        ThemeSetting::create([
            'global' => [
                'logo_type' => 'text',
                'logo_text' => 'Global Logo',
                'logo_image' => null,
                'logo_url' => '/',
                'cta_label' => 'Global CTA',
                'cta_url' => '/global',
            ],
            'header' => [
                'logo_text' => 'Header Logo',
                'cta_label' => 'Header CTA',
            ],
            'footer' => [],
        ]);

        $response = $this->getJson('/api/v1/theme');

        $response->assertStatus(200)
            ->assertJsonPath('data.global.logo.text', 'Global Logo')
            ->assertJsonPath('data.header.logo.text', 'Header Logo')
            ->assertJsonPath('data.header.cta.label', 'Header CTA')
            // Footer should fallback to global
            ->assertJsonPath('data.footer.logo.text', 'Global Logo')
            ->assertJsonPath('data.footer.cta.label', 'Global CTA');
    }

    public function test_theme_response_is_cached(): void
    {
        ThemeSetting::create([
            'global' => ThemeSetting::defaultGlobal(),
            'header' => [],
            'footer' => [],
        ]);

        // First request - cache miss
        $this->getJson('/api/v1/theme')->assertStatus(200);

        // Verify cache was set
        $this->assertTrue(Cache::has(ThemeSetting::CACHE_KEY));

        // Update settings directly in DB without triggering model events
        ThemeSetting::query()->update([
            'global' => json_encode(['logo_text' => 'Updated']),
        ]);

        // Second request should return cached value
        $response = $this->getJson('/api/v1/theme');
        $response->assertJsonPath('data.global.logo.text', 'Ercee');

        // Clear cache and verify new value
        Cache::forget(ThemeSetting::CACHE_KEY);
        $response = $this->getJson('/api/v1/theme');
        $response->assertJsonPath('data.global.logo.text', 'Updated');
    }

    /**
     * Assert that a string contains a substring.
     */
    protected function assertStringContains(string $needle, string $haystack): void
    {
        $this->assertTrue(
            str_contains($haystack, $needle),
            "Failed asserting that '{$haystack}' contains '{$needle}'."
        );
    }
}
