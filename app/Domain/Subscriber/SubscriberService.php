<?php

declare(strict_types=1);

namespace App\Domain\Subscriber;

use App\Contracts\Services\SubscriberServiceInterface;

class SubscriberService implements SubscriberServiceInterface
{
    public function __construct(
        private SubscriberRepository $repository
    ) {}

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

    public function addTags(Subscriber $subscriber, array $tags): void
    {
        foreach ($tags as $tag) {
            $subscriber->addTag($tag);
        }
    }

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
