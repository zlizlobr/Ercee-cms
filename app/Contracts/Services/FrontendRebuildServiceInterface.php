<?php

declare(strict_types=1);

namespace App\Contracts\Services;

interface FrontendRebuildServiceInterface
{
    public function trigger(string $reason): void;

    public function triggerForModel(object $model, string $action): void;
}
