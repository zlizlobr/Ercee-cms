<?php

namespace App\Application\Content\Results;

use App\Application\Contracts\ResultInterface;
use DateTimeInterface;

final readonly class PublishPageResult implements ResultInterface
{
    private function __construct(
        public bool $success,
        public ?string $publishedAt = null,
        public ?string $error = null,
    ) {}

    public static function success(DateTimeInterface $publishedAt): self
    {
        return new self(
            success: true,
            publishedAt: $publishedAt->format('c')
        );
    }

    public static function pageNotFound(): self
    {
        return new self(success: false, error: 'Page not found');
    }

    public static function alreadyPublished(): self
    {
        return new self(success: false, error: 'Page is already published');
    }

    public static function validationFailed(string $reason): self
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
            'published_at' => $this->publishedAt,
            'error' => $this->error,
        ];
    }
}
