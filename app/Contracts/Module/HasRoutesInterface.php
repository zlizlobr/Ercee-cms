<?php

declare(strict_types=1);

namespace App\Contracts\Module;

interface HasRoutesInterface
{
    public function getWebRoutes(): ?string;

    public function getApiRoutes(): ?string;
}

