<?php

namespace App\Domain\Media;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Renames media files and conversion variants to SEO-friendly names.
 */
class MediaRenameService
{
    /**
     * @param SeoSlugGenerator $slugGenerator Slug strategy for generated file names.
     */
    public function __construct(
        private SeoSlugGenerator $slugGenerator
    ) {}

    /**
     * Renames item media to SEO format when source metadata is available.
     */
    public function renameToSeo(MediaLibrary $item): ?string
    {
        $media = $item->getFirstMedia('default');

        if (! $media) {
            return null;
        }

        $newFileName = $this->slugGenerator->generateFileName(
            $item->title,
            $item->alt_text,
            $media->uuid,
            pathinfo($media->file_name, PATHINFO_EXTENSION)
        );

        if (empty($newFileName)) {
            Log::debug('SEO rename skipped - no title/alt', [
                'media_id' => $media->id,
                'uuid' => $media->uuid,
            ]);

            return null;
        }

        if ($media->file_name === $newFileName) {
            return $newFileName;
        }

        return $this->performRename($media, $newFileName);
    }

    /**
     * Reverts media file name to original preserved name.
     */
    public function revertToOriginal(MediaLibrary $item): ?string
    {
        $media = $item->getFirstMedia('default');

        if (! $media) {
            return null;
        }

        $originalName = $media->getCustomProperty('original_file_name');

        if (! $originalName || $media->file_name === $originalName) {
            return null;
        }

        return $this->performRename($media, $originalName);
    }

    /**
     * Applies physical file rename and updates persisted media metadata.
     */
    private function performRename(Media $media, string $newFileName): ?string
    {
        $disk = Storage::disk($media->disk);
        $directory = $media->uuid;
        $oldFileName = $media->file_name;
        $oldPath = "{$directory}/{$oldFileName}";
        $newPath = "{$directory}/{$newFileName}";

        if (! $disk->exists($oldPath)) {
            Log::error('Media file not found for rename', [
                'media_id' => $media->id,
                'path' => $oldPath,
            ]);

            return null;
        }

        if (! $media->getCustomProperty('original_file_name')) {
            $media->setCustomProperty('original_file_name', $oldFileName);
        }

        try {
            $disk->move($oldPath, $newPath);

            $this->renameConversions($media, $oldFileName, $newFileName);

            $media->file_name = $newFileName;
            $media->save();

            Log::info('Media renamed for SEO', [
                'media_id' => $media->id,
                'uuid' => $media->uuid,
                'old_name' => $oldFileName,
                'new_name' => $newFileName,
            ]);

            return $newFileName;
        } catch (\Exception $e) {
            Log::error('Media rename failed', [
                'media_id' => $media->id,
                'error' => $e->getMessage(),
            ]);

            if ($disk->exists($newPath) && ! $disk->exists($oldPath)) {
                $disk->move($newPath, $oldPath);
            }

            return null;
        }
    }

    /**
     * Renames known conversion files to keep names aligned with original.
     */
    private function renameConversions(Media $media, string $oldBaseName, string $newBaseName): void
    {
        $disk = Storage::disk($media->disk);
        $directory = $media->uuid;
        $conversionsDir = "{$directory}/conversions";

        if (! $disk->exists($conversionsDir)) {
            return;
        }

        $oldBase = pathinfo($oldBaseName, PATHINFO_FILENAME);
        $newBase = pathinfo($newBaseName, PATHINFO_FILENAME);

        $conversions = ['thumb', 'medium', 'large', 'webp'];

        foreach ($conversions as $conversion) {
            $extension = $conversion === 'webp' ? 'webp' : pathinfo($oldBaseName, PATHINFO_EXTENSION);

            $oldConversionPath = "{$conversionsDir}/{$oldBase}-{$conversion}.{$extension}";
            $newConversionPath = "{$conversionsDir}/{$newBase}-{$conversion}.{$extension}";

            if ($disk->exists($oldConversionPath)) {
                $disk->move($oldConversionPath, $newConversionPath);
            }
        }
    }

    /**
     * Indicates whether current media file name differs from expected SEO name.
     */
    public function shouldRename(MediaLibrary $item): bool
    {
        $media = $item->getFirstMedia('default');

        if (! $media) {
            return false;
        }

        $newFileName = $this->slugGenerator->generateFileName(
            $item->title,
            $item->alt_text,
            $media->uuid,
            pathinfo($media->file_name, PATHINFO_EXTENSION)
        );

        return ! empty($newFileName) && $media->file_name !== $newFileName;
    }

    /**
     * Returns expected SEO file name without mutating media.
     */
    public function getExpectedFileName(MediaLibrary $item): ?string
    {
        $media = $item->getFirstMedia('default');

        if (! $media) {
            return null;
        }

        return $this->slugGenerator->generateFileName(
            $item->title,
            $item->alt_text,
            $media->uuid,
            pathinfo($media->file_name, PATHINFO_EXTENSION)
        );
    }
}
