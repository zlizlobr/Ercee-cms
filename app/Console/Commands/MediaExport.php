<?php

namespace App\Console\Commands;

use App\Domain\Media\MediaLibrary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Export media library assets to the public directory and build a manifest.
 */
class MediaExport extends Command
{
    protected $signature = 'media:export
                            {--only-changed : Only export media that has changed since last export}
                            {--disk=media : Source disk for media files}
                            {--public-path=public/media : Target directory for exported files}';

    protected $description = 'Export media files and conversions to public directory and generate manifest';

    /** @var array<string, array<string, mixed>> */
    private array $manifest = [];
    private string $publicPath;
    private string $manifestPath;
    /** @var array<string, array<string, mixed>> */
    private array $previousManifest = [];

    /**
     * Export media items and generate a public manifest.
     */
    public function handle(): int
    {
        $this->publicPath = base_path($this->option('public-path'));
        $this->manifestPath = base_path('public/media-manifest.json');

        $this->loadPreviousManifest();
        $this->ensureDirectoryExists();

        $mediaItems = MediaLibrary::with('media')->get();

        $this->info("Processing {$mediaItems->count()} media library items...");

        $progressBar = $this->output->createProgressBar($mediaItems->count());
        $progressBar->start();

        foreach ($mediaItems as $item) {
            $this->processMediaItem($item);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->writeManifest();
        $this->cleanupOrphanedFiles();

        $this->info('Media export completed successfully.');
        $this->info("Manifest written to: {$this->manifestPath}");

        return self::SUCCESS;
    }

    /**
     * Load the existing manifest if it is available.
     */
    private function loadPreviousManifest(): void
    {
        if (File::exists($this->manifestPath)) {
            $this->previousManifest = json_decode(File::get($this->manifestPath), true) ?? [];
        }
    }

    /**
     * Ensure the export directory exists.
     */
    private function ensureDirectoryExists(): void
    {
        if (! File::isDirectory($this->publicPath)) {
            File::makeDirectory($this->publicPath, 0755, true);
        }
    }

    /**
     * Export a single MediaLibrary record if it has media.
     */
    private function processMediaItem(MediaLibrary $item): void
    {
        $media = $item->getFirstMedia('default');

        if (! $media) {
            return;
        }

        $uuid = $media->uuid;
        $checksum = $media->getCustomProperty('checksum') ?? md5_file($media->getPath());

        if ($this->option('only-changed') && $this->hasNotChanged($uuid, $checksum)) {
            $this->manifest[$uuid] = $this->previousManifest[$uuid];
            return;
        }

        $targetDir = "{$this->publicPath}/{$uuid}";
        if (! File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $this->copyOriginal($media, $targetDir);
        $this->copyConversions($media, $targetDir);

        $this->manifest[$uuid] = $this->buildManifestEntry($media, $item, $uuid, $checksum);
    }

    /**
     * Determine if a manifest entry has not changed.
     */
    private function hasNotChanged(string $uuid, string $checksum): bool
    {
        return isset($this->previousManifest[$uuid])
            && ($this->previousManifest[$uuid]['checksum'] ?? '') === $checksum;
    }

    /**
     * Copy the original media file to the public directory.
     */
    private function copyOriginal(Media $media, string $targetDir): void
    {
        $sourcePath = $media->getPath();
        $targetPath = "{$targetDir}/{$media->file_name}";

        if (File::exists($sourcePath)) {
            File::copy($sourcePath, $targetPath);
        }
    }

    /**
     * Copy generated conversions to the public directory.
     */
    private function copyConversions(Media $media, string $targetDir): void
    {
        $conversionsDir = "{$targetDir}/conversions";
        if (! File::isDirectory($conversionsDir)) {
            File::makeDirectory($conversionsDir, 0755, true);
        }

        foreach (['thumb', 'medium', 'large', 'webp'] as $conversion) {
            if ($media->hasGeneratedConversion($conversion)) {
                $sourcePath = $media->getPath($conversion);
                $fileName = $this->getConversionFileName($media, $conversion);
                $targetPath = "{$conversionsDir}/{$fileName}";

                if (File::exists($sourcePath)) {
                    File::copy($sourcePath, $targetPath);
                }
            }
        }
    }

    /**
     * Build a conversion file name for a specific conversion.
     */
    private function getConversionFileName(Media $media, string $conversion): string
    {
        $extension = $conversion === 'webp' ? 'webp' : pathinfo($media->file_name, PATHINFO_EXTENSION);
        $baseName = pathinfo($media->file_name, PATHINFO_FILENAME);

        return "{$baseName}-{$conversion}.{$extension}";
    }

    /**
     * Build a manifest entry for an exported media item.
     *
     * @return array<string, mixed>
     */
    private function buildManifestEntry(Media $media, MediaLibrary $item, string $uuid, string $checksum): array
    {
        $baseUrl = "/media/{$uuid}";

        $entry = [
            'id' => $item->id,
            'uuid' => $uuid,
            'original' => [
                'url' => "{$baseUrl}/{$media->file_name}",
                'width' => $media->getCustomProperty('width'),
                'height' => $media->getCustomProperty('height'),
                'size' => $media->size,
                'mime' => $media->mime_type,
            ],
            'variants' => [],
            'alt' => $item->alt_text,
            'title' => $item->title,
            'focal_point' => $item->focal_point,
            'tags' => $item->tags ?? [],
            'checksum' => $checksum,
            'exported_at' => now()->toIso8601String(),
        ];

        foreach (['thumb', 'medium', 'large', 'webp'] as $conversion) {
            if ($media->hasGeneratedConversion($conversion)) {
                $fileName = $this->getConversionFileName($media, $conversion);
                $conversionPath = $media->getPath($conversion);

                $dimensions = $this->getImageDimensions($conversionPath);

                $entry['variants'][$conversion] = [
                    'url' => "{$baseUrl}/conversions/{$fileName}",
                    'width' => $dimensions['width'],
                    'height' => $dimensions['height'],
                    'size' => File::exists($conversionPath) ? File::size($conversionPath) : null,
                ];
            }
        }

        return $entry;
    }

    /**
     * Read image dimensions from disk, if available.
     *
     * @return array{width: int|null, height: int|null}
     */
    private function getImageDimensions(string $path): array
    {
        if (! File::exists($path)) {
            return ['width' => null, 'height' => null];
        }

        $imageInfo = @getimagesize($path);

        return [
            'width' => $imageInfo[0] ?? null,
            'height' => $imageInfo[1] ?? null,
        ];
    }

    /**
     * Persist the manifest to disk using an atomic move.
     */
    private function writeManifest(): void
    {
        $tempPath = "{$this->manifestPath}.tmp";

        File::put($tempPath, json_encode($this->manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        File::move($tempPath, $this->manifestPath);
    }

    /**
     * Remove export directories that are no longer in the manifest.
     */
    private function cleanupOrphanedFiles(): void
    {
        $directories = File::directories($this->publicPath);

        foreach ($directories as $dir) {
            $uuid = basename($dir);

            if (! isset($this->manifest[$uuid])) {
                File::deleteDirectory($dir);
                $this->info("Removed orphaned directory: {$uuid}");
            }
        }
    }
}
