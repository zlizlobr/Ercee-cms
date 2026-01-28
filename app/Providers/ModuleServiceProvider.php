<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\Module\ModuleManager;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
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

    public function boot(): void
    {
        $manager = $this->app->make(ModuleManager::class);
        $manager->boot();
    }
}
