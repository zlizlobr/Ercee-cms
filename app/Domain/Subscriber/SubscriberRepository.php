<?php

namespace App\Domain\Subscriber;

class SubscriberRepository
{
    public function findByEmail(string $email): ?Subscriber
    {
        return Subscriber::where('email', $email)->first();
    }

    public function create(array $data): Subscriber
    {
        return Subscriber::create($data);
    }

    public function updateOrCreate(string $email, array $data): Subscriber
    {
        return Subscriber::updateOrCreate(
            ['email' => $email],
            $data
        );
    }
}
