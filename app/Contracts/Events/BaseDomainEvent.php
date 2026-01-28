<?php

declare(strict_types=1);

namespace App\Contracts\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class BaseDomainEvent implements DomainEventInterface
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    protected \DateTimeImmutable $occurredAt;

    public function __construct()
    {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getEventName(): string
    {
        return static::class;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
