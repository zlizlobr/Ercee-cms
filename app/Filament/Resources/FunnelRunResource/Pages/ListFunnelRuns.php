<?php

namespace App\Filament\Resources\FunnelRunResource\Pages;

use App\Domain\Funnel\FunnelRun;
use App\Filament\Resources\FunnelRunResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListFunnelRuns extends ListRecords
{
    protected static string $resource = FunnelRunResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'running' => Tab::make('Running')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', FunnelRun::STATUS_RUNNING))
                ->badge(FunnelRun::running()->count())
                ->badgeColor('info'),
            'failed' => Tab::make('Failed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', FunnelRun::STATUS_FAILED))
                ->badge(FunnelRun::failed()->count())
                ->badgeColor('danger'),
            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', FunnelRun::STATUS_COMPLETED)),
        ];
    }
}
