<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface DomainEventInterface
{
    public function getEventName(): string;

    public function getPayload(): array;

    public function getOccurredAt(): \DateTimeImmutable;
}
