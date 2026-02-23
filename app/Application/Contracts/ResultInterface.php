<?php

namespace App\Application\Contracts;

/**
 * Represents a normalized output of an application use-case.
 */
interface ResultInterface
{
    /**
     * Indicate whether the use-case execution succeeded.
     *
     * @return bool True when operation completed successfully.
     */
    public function isSuccess(): bool;

    /**
     * Convert result data into a serializable structure.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
