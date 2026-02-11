<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use Modules\Forms\Domain\Subscriber\Subscriber;

interface SubscriberServiceInterface
{
    public function findOrCreate(string $email, array $data = []): Subscriber;

    public function addTags(Subscriber $subscriber, array $tags): void;

    public function removeTags(Subscriber $subscriber, array $tags): void;

    public function hasTag(Subscriber $subscriber, string $tag): bool;
}
