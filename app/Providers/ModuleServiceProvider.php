<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\Module\ModuleManager;
use Illuminate\Support\ServiceProvider;

/**
 * Boots the module system and exposes the module manager in the container.
 */
class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register the module manager singleton and initialize module registration.
     */
    public function register(): void
    {
        $this->app->singleton(ModuleManager::class, function ($app) {
            return new ModuleManager($app);
        });

        $this->app->alias(ModuleManager::class, 'modules');

        $manager = $this->app->make(ModuleManager::class);
        $manager->loadFromConfig();
        $manager->register();
    }

    /**
     * Boot all registered modules after the application has been initialized.
     */
    public function boot(): void
    {
        $manager = $this->app->make(ModuleManager::class);
        $manager->boot();
    }
}
