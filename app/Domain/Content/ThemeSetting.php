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
            'logo_link_type' => 'url',
            'logo_url' => '/',
            'logo_page_id' => null,
            'cta_label' => 'Kontaktujte nás',
            'cta_link_type' => 'url',
            'cta_url' => '/rfq',
            'cta_page_id' => null,
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
            'logo_link_type' => null,
            'logo_url' => null,
            'logo_page_id' => null,
            'menu_id' => null,
            'cta_label' => null,
            'cta_link_type' => null,
            'cta_url' => null,
            'cta_page_id' => null,
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
            'company_text' => 'Poskytujeme komplexní řešení pro vaše projekty.',
            'quick_links_menu_id' => null,
            'services_menu_id' => null,
            'contact_menu_id' => null,
            'legal_menu_id' => null,
            'cta_label' => null,
            'cta_link_type' => null,
            'cta_url' => null,
            'cta_page_id' => null,
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

        return [
            'logo_type' => $header['logo_type'] ?? $global['logo_type'],
            'logo_text' => $header['logo_text'] ?? $global['logo_text'],
            'logo_image' => $header['logo_image'] ?? $global['logo_image'],
            'logo_link_type' => $header['logo_link_type'] ?? $global['logo_link_type'],
            'logo_url' => $header['logo_url'] ?? $global['logo_url'],
            'logo_page_id' => $header['logo_page_id'] ?? $global['logo_page_id'],
            'menu_id' => $header['menu_id'] ?? null,
            'cta_label' => $header['cta_label'] ?? $global['cta_label'],
            'cta_link_type' => $header['cta_link_type'] ?? $global['cta_link_type'],
            'cta_url' => $header['cta_url'] ?? $global['cta_url'],
            'cta_page_id' => $header['cta_page_id'] ?? $global['cta_page_id'],
        ];
    }

    /**
     * Get footer settings with global fallback.
     */
    public function getFooter(): array
    {
        $global = $this->getGlobal();
        $footer = $this->footer ?? self::defaultFooter();

        return [
            'logo_type' => $footer['logo_type'] ?? $global['logo_type'],
            'logo_text' => $footer['logo_text'] ?? $global['logo_text'],
            'logo_image' => $footer['logo_image'] ?? $global['logo_image'],
            'company_text' => $footer['company_text'] ?? self::defaultFooter()['company_text'],
            'quick_links_menu_id' => $footer['quick_links_menu_id'] ?? null,
            'services_menu_id' => $footer['services_menu_id'] ?? null,
            'contact_menu_id' => $footer['contact_menu_id'] ?? null,
            'legal_menu_id' => $footer['legal_menu_id'] ?? null,
            'cta_label' => $footer['cta_label'] ?? $global['cta_label'],
            'cta_link_type' => $footer['cta_link_type'] ?? $global['cta_link_type'],
            'cta_url' => $footer['cta_url'] ?? $global['cta_url'],
            'cta_page_id' => $footer['cta_page_id'] ?? $global['cta_page_id'],
            'copyright_text' => $footer['copyright_text'] ?? null,
        ];
    }

    /**
     * Get cached theme settings instance.
     */
    public static function getCached(): self
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            return self::first() ?? new self();
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
