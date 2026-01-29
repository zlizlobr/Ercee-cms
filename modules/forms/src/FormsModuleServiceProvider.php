<?php

declare(strict_types=1);

namespace Modules\Forms;

use App\Support\Module\BaseModuleServiceProvider;

class FormsModuleServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'forms';
    protected string $version = '1.0.0';
    protected string $description = 'Form builder and contract management module';
    protected array $dependencies = [];
    protected array $permissions = [
        'view_forms',
        'create_forms',
        'update_forms',
        'delete_forms',
        'view_contracts',
        'update_contracts',
        'delete_contracts',
    ];

    protected function registerBindings(): void
    {
        // Register module-specific bindings
    }

    public function getEventListeners(): array
    {
        return [
            // Event listeners will be registered here after migration
        ];
    }

    public function getResources(): array
    {
        return [
            \Modules\Forms\Filament\Resources\FormResource::class,
            \Modules\Forms\Filament\Resources\ContractResource::class,
        ];
    }

    public function getBlocks(): array
    {
        return [
            \Modules\Forms\Filament\Blocks\FormEmbedBlock::class,
            \Modules\Forms\Filament\Blocks\ContactFormBlock::class,
            \Modules\Forms\Filament\Blocks\RFQFormSidebarBlock::class,
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
