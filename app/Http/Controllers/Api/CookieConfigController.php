<?php

namespace App\Http\Controllers\Api;

use App\Domain\Content\CookieSetting;
use App\Domain\Content\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CookieConfigController extends ApiController
{
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

