<?php

namespace App\Domain\Media;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class MediaManifestService
{
    private const CACHE_KEY = 'media:manifest';
    private const CACHE_TTL = 3600;

    public function getManifest(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $manifestPath = public_path('media-manifest.json');

            if (! File::exists($manifestPath)) {
                return [];
            }

            return json_decode(File::get($manifestPath), true) ?? [];
        });
    }

    public function getByUuid(string $uuid): ?array
    {
        return $this->getManifest()[$uuid] ?? null;
    }

    public function getById(int $id): ?array
    {
        $manifest = $this->getManifest();

        foreach ($manifest as $entry) {
            if (($entry['id'] ?? null) === $id) {
                return $entry;
            }
        }

        return null;
    }

    public function getUrl(string $uuid, ?string $variant = null): ?string
    {
        $entry = $this->getByUuid($uuid);

        if (! $entry) {
            return null;
        }

        if ($variant && isset($entry['variants'][$variant])) {
            return $entry['variants'][$variant]['url'];
        }

        return $entry['original']['url'] ?? null;
    }

    public function resolveMediaIds(array $ids): array
    {
        $result = [];

        foreach ($ids as $id) {
            $entry = $this->getById($id);
            if ($entry) {
                $result[$id] = $entry;
            }
        }

        return $result;
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function toApiFormat(?array $entry): ?array
    {
        if (! $entry) {
            return null;
        }

        return [
            'uuid' => $entry['uuid'],
            'url' => $entry['original']['url'],
            'alt' => $entry['alt'],
            'title' => $entry['title'],
            'width' => $entry['original']['width'],
            'height' => $entry['original']['height'],
            'mime' => $entry['original']['mime'],
            'focal_point' => $entry['focal_point'],
            'variants' => collect($entry['variants'] ?? [])->map(fn ($v) => [
                'url' => $v['url'],
                'width' => $v['width'],
                'height' => $v['height'],
            ])->all(),
        ];
    }
}
