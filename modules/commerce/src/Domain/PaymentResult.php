<?php

namespace Modules\Commerce\Domain;

class PaymentResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $transactionId,
        public readonly string $status,
        public readonly ?array $payload = null,
    ) {}

    public static function success(string $transactionId, array $payload = []): self
    {
        return new self(
            success: true,
            transactionId: $transactionId,
            status: Payment::STATUS_PAID,
            payload: $payload,
        );
    }

    public static function failed(string $transactionId, array $payload = []): self
    {
        return new self(
            success: false,
            transactionId: $transactionId,
            status: Payment::STATUS_FAILED,
            payload: $payload,
        );
    }

    public static function pending(string $transactionId, array $payload = []): self
    {
        return new self(
            success: false,
            transactionId: $transactionId,
            status: Payment::STATUS_PENDING,
            payload: $payload,
        );
    }
}
