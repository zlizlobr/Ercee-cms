<?php

namespace App\Filament\Resources;

use App\Domain\Content\ThemeSetting;
use App\Filament\Resources\ThemeSettingResource\Pages;
use Filament\Resources\Resource;

/**
 * Defines the Filament resource configuration for admin record management.
 */
class ThemeSettingResource extends Resource
{
    /**
     * @var ?string Eloquent model class managed by this Filament resource.
     */
    protected static ?string $model = ThemeSetting::class;

    /**
     * @var ?string Heroicon name shown for this resource in admin navigation.
     */
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    /**
     * @var ?string Navigation section label used for grouping this resource.
     */
    protected static ?string $navigationGroup = 'Thema';

    /**
     * @var ?int Numeric sort order for this resource inside navigation groups.
     */
    protected static ?int $navigationSort = 1;

    /**
     * @var ?string Custom navigation label displayed in the admin sidebar.
     */
    protected static ?string $navigationLabel = 'Theme Settings';

    /**
     * Define page routes for this Filament resource.
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageThemeSettings::route('/'),
        ];
    }
}


