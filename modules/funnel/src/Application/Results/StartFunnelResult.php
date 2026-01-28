<?php

namespace Modules\Funnel\Application\Results;

use App\Application\Contracts\ResultInterface;

readonly class StartFunnelResult implements ResultInterface
{
    private function __construct(
        public bool $success,
        public array $startedRunIds = [],
        public ?string $error = null,
    ) {}

    public static function success(array $runIds): self
    {
        return new self(success: true, startedRunIds: $runIds);
    }

    public static function subscriberNotFound(): self
    {
        return new self(success: false, error: 'Subscriber not found');
    }

    public static function noFunnelsTriggered(): self
    {
        return new self(success: true, startedRunIds: []);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'started_run_ids' => $this->startedRunIds,
            'error' => $this->error,
        ];
    }
}
