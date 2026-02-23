<?php

namespace App\Domain\Content;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Theme settings model for storing global, header, and footer configurations.
 *
 * @property int $id
 * @property array|null $global
 * @property array|null $header
 * @property array|null $footer
 */
class ThemeSetting extends Model
{
    protected $fillable = [
        'global',
        'header',
        'footer',
    ];

    protected $casts = [
        'global' => 'array',
        'header' => 'array',
        'footer' => 'array',
    ];

    /**
     * Cache key for theme settings.
     */
    public const CACHE_KEY = 'theme-settings';

    /**
     * Default global settings.
     */
    public static function defaultGlobal(): array
    {
        return [
            'logo_type' => 'text',
            'logo_text' => 'Ercee',
            'logo_image' => null,
            'logo_media_uuid' => null,
            'logo' => [
                'link_type' => 'url',
                'url' => '/',
                'page_id' => null,
            ],
            'cta_label' => 'Kontaktujte nás',
            'cta' => [
                'link_type' => 'url',
                'url' => '/rfq',
                'page_id' => null,
            ],
        ];
    }

    /**
     * Default header settings.
     */
    public static function defaultHeader(): array
    {
        return [
            'logo_type' => null,
            'logo_text' => null,
            'logo_image' => null,
            'logo_media_uuid' => null,
            'logo' => [
                'link_type' => null,
                'url' => null,
                'page_id' => null,
            ],
            'menu_id' => null,
            'cta_label' => null,
            'cta' => [
                'link_type' => null,
                'url' => null,
                'page_id' => null,
            ],
        ];
    }

    /**
     * Default footer settings.
     */
    public static function defaultFooter(): array
    {
        return [
            'logo_type' => null,
            'logo_text' => null,
            'logo_image' => null,
            'logo_media_uuid' => null,
            'company_text' => 'Poskytujeme komplexní řešení pro vaše projekty.',
            'quick_links_menu_id' => null,
            'services_menu_id' => null,
            'contact_menu_id' => null,
            'legal_menu_id' => null,
            'cta_label' => null,
            'cta' => [
                'link_type' => null,
                'url' => null,
                'page_id' => null,
            ],
            'copyright_text' => null,
        ];
    }

    /**
     * Get global settings with defaults.
     */
    public function getGlobal(): array
    {
        return array_merge(self::defaultGlobal(), $this->global ?? []);
    }

    /**
     * Get header settings with global fallback.
     */
    public function getHeader(): array
    {
        $global = $this->getGlobal();
        $header = $this->header ?? [];
        $headerLogo = $header['logo'] ?? [];
        $globalLogo = $global['logo'] ?? [];
        $headerCta = $header['cta'] ?? [];
        $globalCta = $global['cta'] ?? [];

        return [
            'logo_type' => $header['logo_type'] ?? $global['logo_type'],
            'logo_text' => $header['logo_text'] ?? $global['logo_text'],
            'logo_image' => $header['logo_image'] ?? $global['logo_image'],
            'logo_media_uuid' => $header['logo_media_uuid'] ?? $global['logo_media_uuid'],
            'logo' => [
                'link_type' => $headerLogo['link_type'] ?? $globalLogo['link_type'] ?? 'url',
                'url' => $headerLogo['url'] ?? $globalLogo['url'] ?? '/',
                'page_id' => $headerLogo['page_id'] ?? $globalLogo['page_id'] ?? null,
            ],
            'menu_id' => $header['menu_id'] ?? null,
            'cta_label' => $header['cta_label'] ?? $global['cta_label'],
            'cta' => [
                'link_type' => $headerCta['link_type'] ?? $globalCta['link_type'] ?? 'url',
                'url' => $headerCta['url'] ?? $globalCta['url'] ?? null,
                'page_id' => $headerCta['page_id'] ?? $globalCta['page_id'] ?? null,
            ],
        ];
    }

    /**
     * Get footer settings with global fallback.
     */
    public function getFooter(): array
    {
        $global = $this->getGlobal();
        $footer = $this->footer ?? self::defaultFooter();
        $footerCta = $footer['cta'] ?? [];
        $globalCta = $global['cta'] ?? [];

        return [
            'logo_type' => $footer['logo_type'] ?? $global['logo_type'],
            'logo_text' => $footer['logo_text'] ?? $global['logo_text'],
            'logo_image' => $footer['logo_image'] ?? $global['logo_image'],
            'logo_media_uuid' => $footer['logo_media_uuid'] ?? $global['logo_media_uuid'],
            'company_text' => $footer['company_text'] ?? self::defaultFooter()['company_text'],
            'quick_links_menu_id' => $footer['quick_links_menu_id'] ?? null,
            'services_menu_id' => $footer['services_menu_id'] ?? null,
            'contact_menu_id' => $footer['contact_menu_id'] ?? null,
            'legal_menu_id' => $footer['legal_menu_id'] ?? null,
            'cta_label' => $footer['cta_label'] ?? $global['cta_label'],
            'cta' => [
                'link_type' => $footerCta['link_type'] ?? $globalCta['link_type'] ?? 'url',
                'url' => $footerCta['url'] ?? $globalCta['url'] ?? null,
                'page_id' => $footerCta['page_id'] ?? $globalCta['page_id'] ?? null,
            ],
            'copyright_text' => $footer['copyright_text'] ?? null,
        ];
    }

    /**
     * Get cached theme settings instance.
     */
    public static function getCached(): self
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            return self::first() ?? new self;
        });
    }

    /**
     * Clear the theme settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}

