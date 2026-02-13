<?php

namespace App\Domain\Content;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CookieSetting extends Model
{
    protected $fillable = [
        'banner',
        'categories',
        'services',
        'policy_links',
    ];

    protected $casts = [
        'banner' => 'array',
        'categories' => 'array',
        'services' => 'array',
        'policy_links' => 'array',
    ];

    public const CACHE_KEY = 'cookie-settings';

    public static function defaultBanner(): array
    {
        return [
            'enabled' => true,
            'title' => 'Tato stránka používá cookies',
            'description' => 'Používáme cookies pro zlepšení vašeho zážitku na stránce, analýzu návštěvnosti a personalizaci obsahu.',
            'accept_all_label' => 'Přijmout vše',
            'reject_all_label' => 'Odmítnout vše',
            'customize_label' => 'Nastavení',
            'save_label' => 'Uložit nastavení',
            'position' => 'bottom',
            'theme' => 'light',
        ];
    }

    public static function defaultCategories(): array
    {
        return [
            'necessary' => [
                'name' => 'Nezbytné',
                'description' => 'Nezbytné cookies pro správné fungování webu. Nelze je vypnout.',
                'required' => true,
                'default_enabled' => true,
            ],
            'analytics' => [
                'name' => 'Analytické',
                'description' => 'Cookies pro analýzu návštěvnosti a chování uživatelů.',
                'required' => false,
                'default_enabled' => false,
            ],
            'marketing' => [
                'name' => 'Marketingové',
                'description' => 'Cookies pro personalizaci reklam a marketingový obsah.',
                'required' => false,
                'default_enabled' => false,
            ],
            'preferences' => [
                'name' => 'Preferenční',
                'description' => 'Cookies pro zapamatování uživatelských preferencí.',
                'required' => false,
                'default_enabled' => false,
            ],
        ];
    }

    public static function defaultServices(): array
    {
        return [
            'necessary' => [
                ['name' => 'Session', 'description' => 'Session cookie pro správu přihlášení.', 'cookie_pattern' => 'laravel_session'],
            ],
            'analytics' => [
                ['name' => 'Google Analytics', 'description' => 'Sledování návštěvnosti webu.', 'cookie_pattern' => '_ga*'],
            ],
            'marketing' => [],
            'preferences' => [],
        ];
    }

    public static function defaultPolicyLinks(): array
    {
        return [
            'privacy_policy' => [
                'label' => 'Zásady ochrany osobních údajů',
                'link_type' => 'url',
                'url' => '/privacy-policy',
                'page_id' => null,
            ],
            'cookie_policy' => [
                'label' => 'Zásady cookies',
                'link_type' => 'url',
                'url' => '/cookie-policy',
                'page_id' => null,
            ],
        ];
    }

    public function getBanner(): array
    {
        return array_merge(self::defaultBanner(), $this->banner ?? []);
    }

    public function getCategories(): array
    {
        $defaults = self::defaultCategories();
        $saved = $this->categories ?? [];
        $result = [];

        foreach ($defaults as $key => $defaultCategory) {
            $result[$key] = array_merge($defaultCategory, $saved[$key] ?? []);
        }

        foreach ($saved as $key => $category) {
            if (! isset($result[$key])) {
                $result[$key] = $category;
            }
        }

        return $result;
    }

    public function getServices(): array
    {
        return array_merge(self::defaultServices(), $this->services ?? []);
    }

    public function getPolicyLinks(): array
    {
        $defaults = self::defaultPolicyLinks();
        $saved = $this->policy_links ?? [];
        $result = [];

        foreach ($defaults as $key => $defaultLink) {
            $result[$key] = array_merge($defaultLink, $saved[$key] ?? []);
        }

        foreach ($saved as $key => $link) {
            if (! isset($result[$key])) {
                $result[$key] = $link;
            }
        }

        return $result;
    }

    public static function getCached(): self
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            return self::first() ?? new self;
        });
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
