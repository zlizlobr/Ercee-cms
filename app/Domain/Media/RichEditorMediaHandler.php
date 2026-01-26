<?php

namespace App\Domain\Media;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Handle rich editor uploads and media URL resolution.
 */
class RichEditorMediaHandler
{
    /**
     * Store an uploaded file in MediaLibrary and return a placeholder URL.
     */
    public function handleUpload(TemporaryUploadedFile $file): string
    {
        $mediaItem = MediaLibrary::create([
            'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
        ]);

        $media = $mediaItem
            ->addMedia($file->getRealPath())
            ->usingFileName($file->getClientOriginalName())
            ->withCustomProperties(['source' => 'richtext'])
            ->toMediaCollection('default');

        return "/__media__/{$media->uuid}/original";
    }

    /**
     * Resolve a media UUID and optional variant to a manifest URL.
     */
    public function resolveUrl(string $uuid, ?string $variant = null): ?string
    {
        $manifestService = app(MediaManifestService::class);
        $entry = $manifestService->getByUuid($uuid);

        if (! $entry) {
            return null;
        }

        if ($variant && isset($entry['variants'][$variant])) {
            return $entry['variants'][$variant]['url'];
        }

        return $entry['original']['url'] ?? null;
    }
}
