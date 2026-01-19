<?php

namespace App\Domain\Subscriber;

class SubscriberService
{
    public function __construct(
        private SubscriberRepository $repository
    ) {}

    public function findOrCreateByEmail(string $email, ?string $source = null): Subscriber
    {
        $subscriber = $this->repository->findByEmail($email);

        if ($subscriber) {
            return $subscriber;
        }

        return $this->repository->create([
            'email' => $email,
            'status' => 'active',
            'source' => $source,
        ]);
    }
}
