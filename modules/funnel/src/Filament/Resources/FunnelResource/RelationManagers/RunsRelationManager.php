<?php

namespace Modules\Funnel\Filament\Resources\FunnelResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Funnel\Domain\FunnelRun;
use Modules\Funnel\Filament\Resources\FunnelRunResource;

class RunsRelationManager extends RelationManager
{
    protected static string $relationship = 'runs';

    protected static ?string $title = 'Funnel Runs';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Run ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subscriber.email')
                    ->label('Subscriber')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        FunnelRun::STATUS_RUNNING => 'info',
                        FunnelRun::STATUS_COMPLETED => 'success',
                        FunnelRun::STATUS_FAILED => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('current_step')
                    ->label('Current Step'),
                Tables\Columns\TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        FunnelRun::STATUS_RUNNING => 'Running',
                        FunnelRun::STATUS_COMPLETED => 'Completed',
                        FunnelRun::STATUS_FAILED => 'Failed',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (FunnelRun $record): string => FunnelRunResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
