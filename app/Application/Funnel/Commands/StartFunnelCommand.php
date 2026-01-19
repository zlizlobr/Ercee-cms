<?php

namespace App\Application\Funnel\Commands;

use App\Application\Contracts\CommandInterface;

final readonly class StartFunnelCommand implements CommandInterface
{
    public function __construct(
        public string $trigger,
        public int $subscriberId,
    ) {}

    public function toArray(): array
    {
        return [
            'trigger' => $this->trigger,
            'subscriber_id' => $this->subscriberId,
        ];
    }
}
