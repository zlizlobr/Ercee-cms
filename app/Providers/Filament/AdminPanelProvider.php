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
use Filament\Widgets;
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
            ]);
    }
}
