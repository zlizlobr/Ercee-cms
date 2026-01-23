<?php

namespace App\Filament\Blocks;

use Filament\Forms\Components\Builder\Block;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class BlockRegistry
{
    public const CACHE_KEY = 'filament.blocks';

    /**
     * Get all registered and enabled blocks.
     *
     * @return array<Block>
     */
    public static function all(): array
    {
        $blockClasses = Cache::rememberForever(self::CACHE_KEY, function () {
            return self::discoverBlocks();
        });

        return collect($blockClasses)
            ->map(fn (string $class) => $class::make())
            ->all();
    }

    /**
     * Discover all block classes from the Blocks directory.
     *
     * @return array<string>
     */
    protected static function discoverBlocks(): array
    {
        $blocksPath = app_path('Filament/Blocks');
        $namespace = 'App\\Filament\\Blocks\\';

        if (! File::isDirectory($blocksPath)) {
            return [];
        }

        return collect(File::files($blocksPath))
            ->map(fn ($file) => $file->getFilenameWithoutExtension())
            ->filter(fn (string $filename) => ! in_array($filename, ['BaseBlock', 'BlockRegistry']))
            ->map(fn (string $filename) => $namespace.$filename)
            ->filter(fn (string $class) => self::isValidBlockClass($class))
            ->sortBy(fn (string $class) => $class::$order)
            ->values()
            ->all();
    }

    /**
     * Validate that a class is a valid block.
     */
    protected static function isValidBlockClass(string $class): bool
    {
        if (! class_exists($class)) {
            return false;
        }

        $reflection = new ReflectionClass($class);

        if ($reflection->isAbstract()) {
            return false;
        }

        if (! $reflection->isSubclassOf(BaseBlock::class)) {
            return false;
        }

        if (! $class::$enabled) {
            return false;
        }

        return true;
    }

    /**
     * Clear the block cache.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get block class names without instantiating them.
     *
     * @return array<string>
     */
    public static function getBlockClasses(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return self::discoverBlocks();
        });
    }
}
