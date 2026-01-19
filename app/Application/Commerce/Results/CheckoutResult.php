<?php

namespace App\Application\Commerce\Results;

use App\Application\Contracts\ResultInterface;

final readonly class CheckoutResult implements ResultInterface
{
    private function __construct(
        public bool $success,
        public ?int $orderId = null,
        public ?string $redirectUrl = null,
        public ?string $error = null,
    ) {}

    public static function success(int $orderId, string $redirectUrl): self
    {
        return new self(
            success: true,
            orderId: $orderId,
            redirectUrl: $redirectUrl
        );
    }

    public static function productNotFound(): self
    {
        return new self(success: false, error: 'Product not found or inactive');
    }

    public static function paymentFailed(string $reason): self
    {
        return new self(success: false, error: $reason);
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
            'redirect_url' => $this->redirectUrl,
            'error' => $this->error,
        ];
    }
}
