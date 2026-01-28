<?php

declare(strict_types=1);

namespace App\Contracts\Module;

interface HasMigrationsInterface
{
    public function getMigrationsPath(): ?string;
}
