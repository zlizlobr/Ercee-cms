<?php

namespace App\Providers\Filament;

use App\Filament\Resources\MediaResource;
use App\Filament\Resources\MenuResource;
use App\Filament\Resources\NavigationResource;
use App\Filament\Resources\PageResource;
use App\Filament\Resources\SubscriberResource;
use App\Filament\Resources\ThemeSettingResource;
use App\Filament\Widgets\PagesStats;
use App\Support\Module\ModuleManager;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Support\HtmlString;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $moduleManager = app(ModuleManager::class);

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->navigationItems([
                NavigationItem::make('Homepage')
                    ->url(fn () => config('app.frontend_url', '/'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->sort(-1)
                    ->openUrlInNewTab(),
                ...$moduleManager->getModuleNavigationItems(),
            ])
            ->resources([
                // Core resources
                PageResource::class,
                NavigationResource::class,
                MenuResource::class,
                MediaResource::class,
                SubscriberResource::class,
                ThemeSettingResource::class,
                // Module resources
                ...$moduleManager->getModuleResources(),
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                ...$moduleManager->getModulePages(),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                PagesStats::class,
                ...$moduleManager->getModuleWidgets(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => new HtmlString('
                    <style>
                        .fi-fo-builder-block-picker .fi-dropdown-list-item-icon { width: 1.25rem; height: 1.25rem; }
                        .fi-fo-builder-block-picker .fi-dropdown-list-item { padding-block: 0.5rem; }
                        .fi-fo-builder-block-picker-modal { z-index: 210; }
                        @media (max-width: 768px) {
                            .fi-fo-builder-block-picker-modal {
                                width: 96vw !important;
                                max-height: 72vh !important;
                            }
                        }
                        .fi-fo-builder-block-picker .fi-dropdown-header {
                            font-weight: 600; font-size: 0.7rem; text-transform: uppercase;
                            letter-spacing: 0.05em; border-bottom: 1px solid rgb(229 231 235);
                            margin-bottom: 0.25rem; padding-block: 0.25rem;
                        }
                    </style>
                '),
            );
    }
}
