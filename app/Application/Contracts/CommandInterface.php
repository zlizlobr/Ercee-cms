<?php

namespace App\Application\Contracts;

/**
 * Represents input data for an application use-case.
 */
interface CommandInterface
{
    /**
     * Convert command payload into a serializable structure.
     *
     * @return array<string, mixed> Scalar-friendly command payload.
     */
    public function toArray(): array;
}
