<?php

namespace App\Domain\Media;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class RichEditorMediaHandler
{
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
