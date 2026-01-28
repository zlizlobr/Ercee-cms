<?php

declare(strict_types=1);

namespace App\Contracts\Module;

interface HasEventsInterface
{
    public function getEventListeners(): array;

    public function getEventSubscribers(): array;
}
