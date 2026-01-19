<?php

namespace App\Application\Contracts;

interface ResultInterface
{
    public function isSuccess(): bool;

    public function toArray(): array;
}
