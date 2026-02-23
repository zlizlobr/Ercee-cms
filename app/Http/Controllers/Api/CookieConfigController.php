<?php

namespace App\Http\Controllers\Api;

use App\Domain\Content\CookieSetting;
use App\Domain\Content\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

/**
 * Provide read-only cookie consent configuration for the frontend.
 */
class CookieConfigController extends ApiController
{
    /**
     * Return cookie settings, categories and policy links.
     *
     * @return JsonResponse JSON payload with cookie banner settings, categories, services and policy links.
     */
    public function index(): JsonResponse
    {
        return $this->safeGet(function () {
            $settings = CookieSetting::first() ?? new CookieSetting;
            $updatedAt = $settings->updated_at;
            $updatedAtTs = $this->normalizeTimestamp($updatedAt);
            $updatedAtIso = $this->normalizeIsoDate($updatedAt);
            $cacheKey = CookieSetting::CACHE_KEY.':'.($updatedAtTs ?? 'none');

            $data = Cache::remember($cacheKey, 3600, function () use ($settings) {
                return [
                    'banner' => $settings->getBanner(),
                    'categories' => $this->formatCategories($settings->getCategories()),
                    'services' => $settings->getServices(),
                    'policy_links' => $this->formatPolicyLinks($settings->getPolicyLinks()),
                ];
            });

            return response()->json([
                'data' => $data,
                'meta' => [
                    'updated_at' => $updatedAtIso,
                ],
            ]);
        });
    }

    /**
     * Normalize timestamp-like value to unix timestamp for cache keys.
     *
     * @param mixed $value Timestamp-like value from persistence layer (DateTime, unix timestamp, date string, or null).
     * @return int|null Unix timestamp used in cache key versioning, or null when value cannot be normalized.
     */
    private function normalizeTimestamp(mixed $value): ?int
    {
        if (! $value) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->getTimestamp();
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $parsed = strtotime((string) $value);

        return $parsed === false ? null : $parsed;
    }

    /**
     * Normalize timestamp-like value to ISO 8601 string for API metadata.
     *
     * @param mixed $value Timestamp-like value from persistence layer (DateTime, unix timestamp, date string, or null).
     * @return string|null ISO 8601 date string for API metadata, or null when value is empty.
     */
    private function normalizeIsoDate(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format(DATE_ATOM);
        }

        if (is_numeric($value)) {
            return date(DATE_ATOM, (int) $value);
        }

        $parsed = strtotime((string) $value);

        return $parsed === false ? (string) $value : date(DATE_ATOM, $parsed);
    }

    /**
     * Format cookie categories to stable frontend payload.
     *
     * @param array<string, array<string, mixed>> $categories
     * @return array<string, array{name: string, description: string, required: bool, default_enabled: bool}>
     */
    private function formatCategories(array $categories): array
    {
        $result = [];

        foreach ($categories as $key => $category) {
            $result[$key] = [
                'name' => $category['name'] ?? $key,
                'description' => $category['description'] ?? '',
                'required' => (bool) ($category['required'] ?? false),
                'default_enabled' => (bool) ($category['default_enabled'] ?? false),
            ];
        }

        return $result;
    }

    /**
     * Format policy links and resolve URLs.
     *
     * @param array<string, array<string, mixed>> $links
     * @return array<string, array{label: string, url: string|null}>
     */
    private function formatPolicyLinks(array $links): array
    {
        $result = [];

        foreach ($links as $key => $link) {
            $result[$key] = [
                'label' => $link['label'] ?? $key,
                'url' => $this->resolveLinkUrl($link),
            ];
        }

        return $result;
    }

    /**
     * Resolve policy link to URL from explicit URL or linked page.
     *
     * @param array<string, mixed> $link
     * @return string|null Resolved URL string, or null when no URL can be derived from the link payload.
     */
    private function resolveLinkUrl(array $link): ?string
    {
        $linkType = $link['link_type'] ?? 'url';

        if ($linkType === 'page' && ! empty($link['page_id'])) {
            $page = Page::find($link['page_id']);

            return $page ? '/'.$page->slug : null;
        }

        return $link['url'] ?? null;
    }
}
