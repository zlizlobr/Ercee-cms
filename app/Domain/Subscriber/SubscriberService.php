<?php

declare(strict_types=1);

namespace App\Domain\Subscriber;

use App\Contracts\Services\SubscriberServiceInterface;

/**
 * Coordinates subscriber create/find and tag operations.
 */
class SubscriberService implements SubscriberServiceInterface
{
    /**
     * @param SubscriberRepository $repository Repository for subscriber persistence.
     */
    public function __construct(
        private SubscriberRepository $repository
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function findOrCreate(string $email, array $data = []): Subscriber
    {
        $subscriber = $this->repository->findByEmail($email);

        if ($subscriber) {
            return $subscriber;
        }

        return $this->repository->create(array_merge([
            'email' => $email,
            'status' => 'active',
        ], $data));
    }

    /**
     * @param array<int, string> $tags
     */
    public function addTags(Subscriber $subscriber, array $tags): void
    {
        foreach ($tags as $tag) {
            $subscriber->addTag($tag);
        }
    }

    /**
     * @param array<int, string> $tags
     */
    public function removeTags(Subscriber $subscriber, array $tags): void
    {
        foreach ($tags as $tag) {
            $subscriber->removeTag($tag);
        }
    }

    public function hasTag(Subscriber $subscriber, string $tag): bool
    {
        return $subscriber->hasTag($tag);
    }
}

