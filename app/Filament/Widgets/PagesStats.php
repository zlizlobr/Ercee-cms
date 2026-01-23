<?php

namespace App\Filament\Widgets;

use App\Domain\Content\Page;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PagesStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Stránek celkem', Page::query()->count()),
            Stat::make('Publikovaných', Page::query()->published()->count()),
            Stat::make('Draftů', Page::query()->where('status', Page::STATUS_DRAFT)->count()),
        ];
    }
}
