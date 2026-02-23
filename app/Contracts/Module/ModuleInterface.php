<?php

declare(strict_types=1);

namespace App\Contracts\Module;

interface ModuleInterface
{
    public function getName(): string;

    public function getVersion(): string;

    public function getDescription(): string;

    public function getDependencies(): array;

    public function getPermissions(): array;

    public function isEnabled(): bool;

    public function register(): void;

    public function boot(): void;
}

