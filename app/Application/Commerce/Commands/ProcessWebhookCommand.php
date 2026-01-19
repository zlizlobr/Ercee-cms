<?php

namespace App\Application\Commerce\Commands;

use App\Application\Contracts\CommandInterface;

final readonly class ProcessWebhookCommand implements CommandInterface
{
    public function __construct(
        public string $transactionId,
        public string $status,
        public bool $success,
        public ?array $payload = null,
    ) {}

    public function toArray(): array
    {
        return [
            'transaction_id' => $this->transactionId,
            'status' => $this->status,
            'success' => $this->success,
            'payload' => $this->payload,
        ];
    }
}
