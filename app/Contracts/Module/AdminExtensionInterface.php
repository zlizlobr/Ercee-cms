<?php

declare(strict_types=1);

namespace App\Contracts\Module;

interface AdminExtensionInterface
{
    public function getResources(): array;

    public function getPages(): array;

    public function getWidgets(): array;

    public function getNavigationItems(): array;

    public function getBlocks(): array;
}
