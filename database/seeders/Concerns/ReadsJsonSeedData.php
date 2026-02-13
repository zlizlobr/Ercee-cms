<?php

declare(strict_types=1);

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\File;
use JsonException;

trait ReadsJsonSeedData
{
    protected function readSeedJson(string $fileName): mixed
    {
        $path = storage_path('app/seed-data/'.ltrim($fileName, '/'));

        if (! File::exists($path)) {
            $this->warn("Seed file not found: {$path}");

            return null;
        }

        try {
            return json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $this->warn("Invalid JSON in {$path}: {$exception->getMessage()}");

            return null;
        }
    }

    protected function warn(string $message): void
    {
        if ($this->command !== null) {
            $this->command->warn($message);
        }
    }

    protected function info(string $message): void
    {
        if ($this->command !== null) {
            $this->command->info($message);
        }
    }
}
