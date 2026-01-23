<?php

namespace App\Filament\Blocks;

use Filament\Forms\Components\Builder\Block;

abstract class BaseBlock
{
    /**
     * Block order in the UI (lower = first).
     */
    public static int $order = 100;

    /**
     * Whether the block is enabled.
     */
    public static bool $enabled = true;

    /**
     * Create the Filament Builder Block instance.
     */
    abstract public static function make(): Block;
}
