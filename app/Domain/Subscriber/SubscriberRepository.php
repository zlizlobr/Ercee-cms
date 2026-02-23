<?php

namespace App\Domain\Subscriber;

/**
 * Provides persistence operations for subscriber entities.
 */
class SubscriberRepository
{
    /**
     * Finds subscriber by unique email.
     */
    public function findByEmail(string $email): ?Subscriber
    {
        return Subscriber::where('email', $email)->first();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Subscriber
    {
        return Subscriber::create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateOrCreate(string $email, array $data): Subscriber
    {
        return Subscriber::updateOrCreate(
            ['email' => $email],
            $data
        );
    }
}
