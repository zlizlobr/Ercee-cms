<?php

namespace Modules\Commerce\Application\Commands;

use App\Application\Contracts\CommandInterface;

readonly class ProcessWebhookCommand implements CommandInterface
{
    public function __construct(
        public string $transactionId,
        public string $status,
        public bool $success,
        public bool $signatureVerified = false,
        public ?array $payload = null,
    ) {}

    public function toArray(): array
    {
        return [
            'transaction_id' => $this->transactionId,
            'status' => $this->status,
            'success' => $this->success,
            'signature_verified' => $this->signatureVerified,
            'payload' => $this->payload,
        ];
    }
}
