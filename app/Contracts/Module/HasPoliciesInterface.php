<?php

declare(strict_types=1);

namespace App\Contracts\Module;

interface HasPoliciesInterface
{
    public function getPolicies(): array;
}
