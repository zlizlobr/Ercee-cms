<?php

namespace Modules\Forms\Application\Results;

use App\Application\Contracts\ResultInterface;

final readonly class SubmitFormResult implements ResultInterface
{
    private function __construct(
        public bool $success,
        public ?int $contractId = null,
        public ?string $error = null,
        public array $validationErrors = [],
    ) {}

    public static function success(int $contractId): self
    {
        return new self(success: true, contractId: $contractId);
    }

    public static function formNotFound(): self
    {
        return new self(success: false, error: 'Form not found');
    }

    public static function honeypotTriggered(): self
    {
        return new self(success: true);
    }

    public static function validationFailed(array $errors): self
    {
        return new self(success: false, error: 'Validation failed', validationErrors: $errors);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function isHoneypot(): bool
    {
        return $this->success && $this->contractId === null;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'contract_id' => $this->contractId,
            'error' => $this->error,
            'validation_errors' => $this->validationErrors,
        ];
    }
}
