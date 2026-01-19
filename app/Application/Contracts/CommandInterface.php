<?php

namespace App\Application\Contracts;

interface CommandInterface
{
    public function toArray(): array;
}
