<?php

namespace App\Filament\Blocks;

use Filament\Forms\Components\Builder\Block;

/**
 * Defines the shared contract for Filament builder blocks.
 */
abstract class BaseBlock
{
    /**
     * @var int Sort priority used to position the block in the builder picker.
     */
    public static int $order = 100;

    /**
     * @var bool Feature flag that controls whether the block is available for selection.
     */
    public static bool $enabled = true;

    /**
     * @var string Group key used to place the block into a picker section.
     */
    public static string $group = 'content';

    /**
     * Build the block schema.
     */
    abstract public static function make(): Block;
}

