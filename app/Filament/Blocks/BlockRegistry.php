<?php

namespace App\Filament\Blocks;

use App\Support\Module\ModuleManager;
use Filament\Forms\Components\Builder\Block;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use ReflectionClass;

/**
 * Registers and resolves available Filament builder blocks.
 */
class BlockRegistry
{
    public const CACHE_KEY = 'filament.blocks';

    /**
     * @var array Runtime map of block names to translated picker group labels.
     */
    protected static array $blockGroupMap = [];

    /**
     * Get all registered and enabled blocks.
     *
     * @return array<int, Block>
     */
    public static function all(): array
    {
        $blockClasses = Cache::rememberForever(self::CACHE_KEY, function () {
            return self::discoverBlocks();
        });

        // Guard against stale cache entries after blocks are deleted/renamed.
        if (collect($blockClasses)->contains(fn (string $class) => ! class_exists($class))) {
            self::clearCache();
            $blockClasses = self::discoverBlocks();
        }

        $moduleBlockClasses = self::getModuleBlockClasses();

        // Filter out core alias blocks whose parent is already registered via module
        $filteredCoreClasses = collect($blockClasses)
            ->reject(fn (string $class) => self::isAliasOfModuleBlock($class, $moduleBlockClasses))
            ->all();

        $coreBlocks = collect($filteredCoreClasses)
            ->map(fn (string $class) => self::makeWithGroup($class))
            ->all();

        $moduleBlocks = collect($moduleBlockClasses)
            ->filter(fn (string $class) => class_exists($class) && self::isValidBlockClass($class))
            ->sortBy(fn (string $class) => $class::$order)
            ->map(fn (string $class) => self::makeWithGroup($class))
            ->values()
            ->all();

        return array_merge($coreBlocks, $moduleBlocks);
    }

    /**
     * @var array Priority map that determines ordering of block groups.
     */
    protected static array $groupOrder = [
        'hero' => 1,
        'content' => 2,
        'cta' => 3,
        'features' => 4,
        'data' => 5,
        'layout' => 6,
    ];

    /**
     * Instantiate a block and register its translated group label.
     *
     * @param class-string<BaseBlock> $class
     */
    protected static function makeWithGroup(string $class): Block
    {
        $block = $class::make();
        $group = $class::$group;
        self::$blockGroupMap[$block->getName()] = __("admin.page.block_groups.{$group}");

        return $block;
    }

    /**
     * Resolve the configured group for a block class.
     * @param string $blockName
     */
    public static function getGroupForBlock(string $blockName): ?string
    {
        return self::$blockGroupMap[$blockName] ?? null;
    }

    /**
     * Build a deterministic sort key from block group and order.
     *
     * @param class-string<BaseBlock> $class
     */
    protected static function groupSortKey(string $class): string
    {
        $groupOrder = self::$groupOrder[$class::$group] ?? 99;

        return str_pad((string) $groupOrder, 3, '0', STR_PAD_LEFT)
            .'-'
            .str_pad((string) $class::$order, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get block classes registered by enabled modules.
     *
     * @return array<int, class-string<BaseBlock>>
     */
    protected static function getModuleBlockClasses(): array
    {
        try {
            return app(ModuleManager::class)->getModuleBlocks();
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Determine whether a core block class extends a module block class.
     *
     * @param class-string<BaseBlock> $class
     * @param array<int, class-string<BaseBlock>> $moduleBlockClasses
     */
    protected static function isAliasOfModuleBlock(string $class, array $moduleBlockClasses): bool
    {
        if (empty($moduleBlockClasses)) {
            return false;
        }

        $parent = get_parent_class($class);

        return $parent && in_array($parent, $moduleBlockClasses, true);
    }

    /**
     * Discover all block classes from the Blocks directory.
     *
     * @return array<int, class-string<BaseBlock>>
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
            ->sortBy(fn (string $class) => self::groupSortKey($class))
            ->values()
            ->all();
    }

    /**
     * Validate that a class is a valid block.
     *
     * @param class-string $class
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
     * @return array<int, class-string<BaseBlock>>
     */
    public static function getBlockClasses(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return self::discoverBlocks();
        });
    }
}

