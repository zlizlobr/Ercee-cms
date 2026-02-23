<?php

namespace App\Console\Commands;

use App\Filament\Blocks\BlockRegistry;
use Illuminate\Console\Command;

/**
 * Clear cached Filament block registry data.
 */
class ClearBlockCache extends Command
{
    protected $signature = 'blocks:clear';

    protected $description = 'Clear the Filament Builder blocks cache';

    /**
     * Execute the cache clear command.
     *
     * @return int Exit code (`Command::SUCCESS` on success).
     */
    public function handle(): int
    {
        BlockRegistry::clearCache();

        $this->info('Block cache cleared successfully.');

        return self::SUCCESS;
    }
}

