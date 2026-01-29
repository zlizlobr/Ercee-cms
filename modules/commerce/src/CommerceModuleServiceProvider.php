<?php

declare(strict_types=1);

namespace Modules\Commerce;

use App\Support\Module\BaseModuleServiceProvider;

class CommerceModuleServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'commerce';
    protected string $version = '1.0.0';
    protected string $description = 'E-commerce module with products, orders, and payments';
    protected array $dependencies = [];
    protected array $permissions = [
        'view_products',
        'create_products',
        'update_products',
        'delete_products',
        'view_orders',
        'update_orders',
        'view_payments',
        'view_attributes',
        'create_attributes',
        'update_attributes',
        'delete_attributes',
        'view_taxonomies',
        'create_taxonomies',
        'update_taxonomies',
        'delete_taxonomies',
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
            \Modules\Commerce\Filament\Resources\ProductResource::class,
            \Modules\Commerce\Filament\Resources\OrderResource::class,
            \Modules\Commerce\Filament\Resources\PaymentResource::class,
            \Modules\Commerce\Filament\Resources\AttributeResource::class,
            \Modules\Commerce\Filament\Resources\TaxonomyResource::class,
            \Modules\Commerce\Filament\Resources\ProductReviewResource::class,
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
