<?php

declare(strict_types=1);

namespace App\Support\Module;

use App\Contracts\Module\AdminExtensionInterface;
use App\Contracts\Module\HasEventsInterface;
use App\Contracts\Module\HasMigrationsInterface;
use App\Contracts\Module\HasPoliciesInterface;
use App\Contracts\Module\HasRoutesInterface;
use App\Contracts\Module\ModuleInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

class ModuleManager
{
    protected array $modules = [];
    protected array $registered = [];
    protected array $booted = [];

    public function __construct(
        protected Application $app
    ) {}

    public function loadFromConfig(): void
    {
        $modules = config('modules.modules', []);

        foreach ($modules as $name => $config) {
            if (! ($config['enabled'] ?? false)) {
                continue;
            }

            $this->loadModule($name, $config);
        }
    }

    public function loadModule(string $name, array $config): void
    {
        $providerClass = $config['provider'] ?? null;

        if (! $providerClass || ! class_exists($providerClass)) {
            return;
        }

        $provider = new $providerClass($this->app);

        if (! $provider instanceof ModuleInterface) {
            return;
        }

        $this->modules[$name] = [
            'config' => $config,
            'provider' => $provider,
        ];
    }

    public function register(): void
    {
        foreach ($this->modules as $name => $module) {
            if (isset($this->registered[$name])) {
                continue;
            }

            $this->registerModule($name, $module);
            $this->registered[$name] = true;
        }
    }

    protected function registerModule(string $name, array $module): void
    {
        $provider = $module['provider'];

        if (! $this->checkDependencies($provider)) {
            return;
        }

        $provider->register();

        if ($provider instanceof HasRoutesInterface) {
            $this->registerRoutes($provider);
        }

        if ($provider instanceof HasMigrationsInterface) {
            $this->registerMigrations($provider);
        }
    }

    public function boot(): void
    {
        foreach ($this->modules as $name => $module) {
            if (isset($this->booted[$name])) {
                continue;
            }

            $this->bootModule($name, $module);
            $this->booted[$name] = true;
        }
    }

    protected function bootModule(string $name, array $module): void
    {
        $provider = $module['provider'];

        $provider->boot();

        if ($provider instanceof HasEventsInterface) {
            $this->registerEvents($provider);
        }

        if ($provider instanceof HasPoliciesInterface) {
            $this->registerPolicies($provider);
        }
    }

    protected function checkDependencies(ModuleInterface $module): bool
    {
        $dependencies = $module->getDependencies();

        foreach ($dependencies as $dependency => $version) {
            if (! $this->isModuleEnabled($dependency)) {
                return false;
            }
        }

        return true;
    }

    protected function registerRoutes(HasRoutesInterface $provider): void
    {
        if ($webRoutes = $provider->getWebRoutes()) {
            Route::middleware('web')->group($webRoutes);
        }

        if ($apiRoutes = $provider->getApiRoutes()) {
            Route::middleware('api')->prefix('api')->group($apiRoutes);
        }
    }

    protected function registerMigrations(HasMigrationsInterface $provider): void
    {
        if ($path = $provider->getMigrationsPath()) {
            $this->app->afterResolving('migrator', function ($migrator) use ($path) {
                $migrator->path($path);
            });
        }
    }

    protected function registerEvents(HasEventsInterface $provider): void
    {
        foreach ($provider->getEventListeners() as $event => $listeners) {
            foreach ((array) $listeners as $listener) {
                Event::listen($event, $listener);
            }
        }

        foreach ($provider->getEventSubscribers() as $subscriber) {
            Event::subscribe($subscriber);
        }
    }

    protected function registerPolicies(HasPoliciesInterface $provider): void
    {
        foreach ($provider->getPolicies() as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    public function isModuleEnabled(string $name): bool
    {
        return isset($this->modules[$name]);
    }

    public function getModule(string $name): ?ModuleInterface
    {
        return $this->modules[$name]['provider'] ?? null;
    }

    public function getModules(): array
    {
        return array_map(fn ($m) => $m['provider'], $this->modules);
    }

    public function getAdminExtensions(): array
    {
        return array_filter(
            $this->getModules(),
            fn ($m) => $m instanceof AdminExtensionInterface
        );
    }

    public function getModuleResources(): array
    {
        $resources = [];

        foreach ($this->getAdminExtensions() as $module) {
            $resources = array_merge($resources, $module->getResources());
        }

        return $resources;
    }

    public function getModulePages(): array
    {
        $pages = [];

        foreach ($this->getAdminExtensions() as $module) {
            $pages = array_merge($pages, $module->getPages());
        }

        return $pages;
    }

    public function getModuleWidgets(): array
    {
        $widgets = [];

        foreach ($this->getAdminExtensions() as $module) {
            $widgets = array_merge($widgets, $module->getWidgets());
        }

        return $widgets;
    }

    public function getModuleBlocks(): array
    {
        $blocks = [];

        foreach ($this->getAdminExtensions() as $module) {
            $blocks = array_merge($blocks, $module->getBlocks());
        }

        return $blocks;
    }

    public function getModuleNavigationItems(): array
    {
        $items = [];

        foreach ($this->getAdminExtensions() as $module) {
            $items = array_merge($items, $module->getNavigationItems());
        }

        return $items;
    }
}
