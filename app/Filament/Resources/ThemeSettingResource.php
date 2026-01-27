<?php

namespace App\Filament\Resources;

use App\Domain\Content\ThemeSetting;
use App\Filament\Resources\ThemeSettingResource\Pages;
use Filament\Resources\Resource;

class ThemeSettingResource extends Resource
{
    protected static ?string $model = ThemeSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static ?string $navigationGroup = 'Thema';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Theme Settings';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageThemeSettings::route('/'),
        ];
    }
}
