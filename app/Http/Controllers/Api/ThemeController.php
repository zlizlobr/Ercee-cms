<?php

namespace App\Http\Controllers\Api;

use App\Domain\Content\Menu;
use App\Domain\Content\Page;
use App\Domain\Content\ThemeSetting;
use App\Domain\Media\ThemeMediaResolver;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

/**
 * Provide read-only access to theme settings and menus.
 */
class ThemeController extends ApiController
{
    public function __construct(
        private readonly ThemeMediaResolver $mediaResolver,
    ) {}

    /**
     * Return the aggregated theme configuration for the frontend.
     */
    public function index(): JsonResponse
    {
        return $this->safeGet(function () {
            $data = Cache::remember(ThemeSetting::CACHE_KEY, 3600, function () {
                $settings = ThemeSetting::first() ?? new ThemeSetting;

                return [
                    'global' => $this->formatGlobal($settings),
                    'header' => $this->formatHeader($settings),
                    'footer' => $this->formatFooter($settings),
                ];
            });

            return response()->json([
                'data' => $data,
            ]);
        });
    }

    /**
     * Format the global theme settings.
     */
    protected function formatGlobal(ThemeSetting $settings): array
    {
        $global = $settings->getGlobal();

        return [
            'logo' => $this->formatLogo($global, true),
            'cta' => $this->formatCta($global),
        ];
    }

    /**
     * Format the header theme settings.
     */
    protected function formatHeader(ThemeSetting $settings): array
    {
        $header = $settings->getHeader();

        return [
            'logo' => $this->formatLogo($header, true),
            'menu' => $this->resolveMenu($header['menu_id']),
            'cta' => $this->formatCta($header),
        ];
    }

    /**
     * Format the footer theme settings.
     */
    protected function formatFooter(ThemeSetting $settings): array
    {
        $footer = $settings->getFooter();
        $year = date('Y');
        $defaultCopyright = "© {$year} Ercee. Všechna práva vyhrazena.";

        return [
            'logo' => $this->formatLogo($footer, false),
            'company_text' => $footer['company_text'],
            'menus' => [
                'quick_links' => $this->resolveMenu($footer['quick_links_menu_id']),
                'services' => $this->resolveMenu($footer['services_menu_id']),
                'contact' => $this->resolveMenu($footer['contact_menu_id']),
                'legal' => $this->resolveMenu($footer['legal_menu_id']),
            ],
            'cta' => $this->formatCta($footer),
            'copyright_text' => $footer['copyright_text']
                ? str_replace('{year}', $year, $footer['copyright_text'])
                : $defaultCopyright,
        ];
    }

    /**
     * Resolve link data to a URL string.
     */
    protected function resolveLinkData(array $linkData): ?string
    {
        $linkType = $linkData['link_type'] ?? 'url';
        $url = $linkData['url'] ?? null;
        $pageId = $linkData['page_id'] ?? null;

        if ($linkType === 'page' && $pageId) {
            return $this->resolvePageUrl($pageId);
        }

        return $url;
    }

    /**
     * Format a call-to-action configuration.
     */
    protected function formatCta(array $settings): array
    {
        $ctaData = $settings['cta'] ?? [];

        return [
            'label' => $settings['cta_label'],
            'url' => $this->resolveLinkData($ctaData),
        ];
    }

    /**
     * Resolve a page URL by ID.
     */
    protected function resolvePageUrl(?int $pageId): ?string
    {
        if (! $pageId) {
            return null;
        }

        $page = Page::find($pageId);

        return $page ? '/'.$page->slug : null;
    }

    /**
     * Resolve a menu by ID and format it for the API response.
     */
    protected function resolveMenu(?int $menuId): ?array
    {
        if (! $menuId) {
            return null;
        }

        $menu = Menu::with(['items.children' => fn ($q) => $q->active()->ordered()])
            ->find($menuId);

        if (! $menu) {
            return null;
        }

        return [
            'id' => $menu->id,
            'name' => $menu->name,
            'slug' => $menu->slug,
            'items' => $menu->items
                ->filter(fn ($item) => $item->is_active)
                ->sortBy('position')
                ->values()
                ->map(fn ($item) => $item->toArray())
                ->toArray(),
        ];
    }

    /**
     * Format a logo configuration for API output.
     */
    protected function formatLogo(array $settings, bool $includeUrl): array
    {
        $logo = [
            'type' => $settings['logo_type'],
            'text' => $settings['logo_text'],
            'image_url' => $this->mediaResolver->resolveLogoImageUrl($settings),
        ];

        $media = $this->mediaResolver->resolveLogoMedia($settings);
        if ($media) {
            $logo['media'] = $media;
        }

        if ($includeUrl) {
            $logoLink = $settings['logo'] ?? [];
            $logo['url'] = $this->resolveLinkData($logoLink);
        }

        return $logo;
    }
}
