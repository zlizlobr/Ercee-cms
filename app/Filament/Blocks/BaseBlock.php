<?php

namespace App\Filament\Blocks;

use Filament\Forms\Components\Builder\Block;

abstract class BaseBlock
{
    public static int $order = 100;

    public static bool $enabled = true;

    public static string $group = 'content';

    abstract public static function make(): Block;
}
