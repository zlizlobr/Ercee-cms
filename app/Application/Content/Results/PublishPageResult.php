<?php

namespace App\Application\Content\Results;

use App\Application\Contracts\ResultInterface;
use DateTimeInterface;

/**
 * Represents output of the page publishing use-case.
 */
final readonly class PublishPageResult implements ResultInterface
{
    /**
     * @param bool $success Indicates whether publishing succeeded.
     * @param string|null $publishedAt Publication timestamp in ISO 8601 format.
     * @param string|null $error Error description for failed operations.
     */
    private function __construct(
        public bool $success,
        public ?string $publishedAt = null,
        public ?string $error = null,
    ) {}

    /**
     * Create a successful publish result.
     *
     * @param DateTimeInterface $publishedAt Publication timestamp.
     * @return self Successful use-case result.
     */
    public static function success(DateTimeInterface $publishedAt): self
    {
        return new self(
            success: true,
            publishedAt: $publishedAt->format('c')
        );
    }

    /**
     * Create a failure result for a missing page.
     *
     * @return self Failed use-case result.
     */
    public static function pageNotFound(): self
    {
        return new self(success: false, error: 'Page not found');
    }

    /**
     * Create a failure result when page is already published.
     *
     * @return self Failed use-case result.
     */
    public static function alreadyPublished(): self
    {
        return new self(success: false, error: 'Page is already published');
    }

    /**
     * Create a validation failure result.
     *
     * @param string $reason Validation error description.
     * @return self Failed use-case result.
     */
    public static function validationFailed(string $reason): self
    {
        return new self(success: false, error: $reason);
    }

    /**
     * Indicate whether the publish use-case succeeded.
     *
     * @return bool True when page was published.
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return array{success: bool, published_at: string|null, error: string|null}
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'published_at' => $this->publishedAt,
            'error' => $this->error,
        ];
    }
}
