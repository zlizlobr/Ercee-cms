<?php

declare(strict_types=1);

namespace Modules\Funnel;

use App\Support\Module\BaseModuleServiceProvider;

class FunnelModuleServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'funnel';
    protected string $version = '1.0.0';
    protected string $description = 'Marketing automation funnel module';
    protected array $dependencies = [
        'forms' => '^1.0',
        'commerce' => '^1.0',
    ];
    protected array $permissions = [
        'view_funnels',
        'create_funnels',
        'update_funnels',
        'delete_funnels',
        'view_funnel_runs',
    ];

    protected function registerBindings(): void
    {
        // Register module-specific bindings
    }

    public function getEventListeners(): array
    {
        return [
            \Modules\Forms\Domain\Events\ContractCreated::class => [
                \Modules\Funnel\Listeners\StartFunnelOnContractCreated::class,
            ],
            \Modules\Commerce\Domain\Events\OrderPaid::class => [
                \Modules\Funnel\Listeners\StartFunnelOnOrderPaid::class,
            ],
        ];
    }

    public function getResources(): array
    {
        return [
            \Modules\Funnel\Filament\Resources\FunnelResource::class,
            \Modules\Funnel\Filament\Resources\FunnelRunResource::class,
        ];
    }

    public function getNavigationItems(): array
    {
        return [
            // Navigation items for admin panel
        ];
    }

    protected function getModulePath(string $path = ''): string
    {
        return __DIR__ . '/../' . ltrim($path, '/');
    }
}
