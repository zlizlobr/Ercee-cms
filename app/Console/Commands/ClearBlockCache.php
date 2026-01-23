<?php

namespace App\Console\Commands;

use App\Filament\Blocks\BlockRegistry;
use Illuminate\Console\Command;

class ClearBlockCache extends Command
{
    protected $signature = 'blocks:clear';

    protected $description = 'Clear the Filament Builder blocks cache';

    public function handle(): int
    {
        BlockRegistry::clearCache();

        $this->info('Block cache cleared successfully.');

        return self::SUCCESS;
    }
}
