<?php

declare(strict_types=1);

namespace App\Support\Module;

use App\Contracts\Module\AdminExtensionInterface;
use App\Contracts\Module\HasEventsInterface;
use App\Contracts\Module\HasMigrationsInterface;
use App\Contracts\Module\HasPoliciesInterface;
use App\Contracts\Module\HasRoutesInterface;
use App\Contracts\Module\ModuleInterface;
use Illuminate\Support\ServiceProvider;

abstract class BaseModuleServiceProvider extends ServiceProvider implements
    ModuleInterface,
    AdminExtensionInterface,
    HasRoutesInterface,
    HasMigrationsInterface,
    HasEventsInterface,
    HasPoliciesInterface
{
    /**
     * @var string Module display name exposed in admin and diagnostics metadata.
     */
    protected string $name = '';
    /**
     * @var string Module version identifier used for compatibility tracking.
     */
    protected string $version = '1.0.0';
    /**
     * @var string Short module description shown in module listings.
     */
    protected string $description = '';
    /**
     * @var array Module dependency map required before module boot.
     */
    protected array $dependencies = [];
    /**
     * @var array Permission definitions registered by the module provider.
     */
    protected array $permissions = [];

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function isEnabled(): bool
    {
        return config("modules.modules.{$this->name}.enabled", false);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            $this->getModulePath('config/module.php'),
            "module.{$this->name}"
        );

        $this->registerBindings();
    }

    public function boot(): void
    {
        $this->loadViews();
        $this->loadTranslations();
        $this->publishAssets();
    }

    protected function registerBindings(): void
    {
        // Override in module to register DI bindings
    }

    protected function loadViews(): void
    {
        $viewsPath = $this->getModulePath('resources/views');

        if (is_dir($viewsPath)) {
            $this->loadViewsFrom($viewsPath, $this->name);
        }
    }

    protected function loadTranslations(): void
    {
        $langPath = $this->getModulePath('resources/lang');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->name);
        }
    }

    protected function publishAssets(): void
    {
        $assetsPath = $this->getModulePath('resources/assets');

        if (is_dir($assetsPath)) {
            $this->publishes([
                $assetsPath => public_path("vendor/{$this->name}"),
            ], "{$this->name}-assets");
        }
    }

    public function getWebRoutes(): ?string
    {
        $path = $this->getModulePath('routes/web.php');
        return file_exists($path) ? $path : null;
    }

    public function getApiRoutes(): ?string
    {
        $path = $this->getModulePath('routes/api.php');
        return file_exists($path) ? $path : null;
    }

    public function getMigrationsPath(): ?string
    {
        $path = $this->getModulePath('database/migrations');
        return is_dir($path) ? $path : null;
    }

    public function getEventListeners(): array
    {
        return [];
    }

    public function getEventSubscribers(): array
    {
        return [];
    }

    public function getPolicies(): array
    {
        return [];
    }

    public function getResources(): array
    {
        return [];
    }

    public function getPages(): array
    {
        return [];
    }

    public function getWidgets(): array
    {
        return [];
    }

    public function getNavigationItems(): array
    {
        return [];
    }

    public function getBlocks(): array
    {
        return [];
    }

    public function getRebuildRules(): array
    {
        return [];
    }

    abstract protected function getModulePath(string $path = ''): string;
}

