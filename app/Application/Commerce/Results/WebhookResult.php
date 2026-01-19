<?php

namespace App\Application\Commerce\Results;

use App\Application\Contracts\ResultInterface;

final readonly class WebhookResult implements ResultInterface
{
    private function __construct(
        public bool $success,
        public ?int $orderId = null,
        public ?string $message = null,
        public ?string $error = null,
    ) {}

    public static function success(int $orderId, string $message): self
    {
        return new self(
            success: true,
            orderId: $orderId,
            message: $message
        );
    }

    public static function paymentNotFound(string $transactionId): self
    {
        return new self(
            success: false,
            error: "Payment not found for transaction: {$transactionId}"
        );
    }

    public static function processingError(string $error): self
    {
        return new self(success: false, error: $error);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'order_id' => $this->orderId,
            'message' => $this->message,
            'error' => $this->error,
        ];
    }
}
