<?php

namespace App\Filament\Resources;

use App\Domain\Funnel\FunnelRun;
use App\Domain\Funnel\FunnelRunStep;
use App\Filament\Resources\FunnelRunResource\Pages;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FunnelRunResource extends Resource
{
    protected static ?string $model = FunnelRun::class;

    protected static ?string $navigationIcon = 'heroicon-o-play';

    protected static ?string $navigationGroup = 'Automation';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Funnel Runs';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Run Details')
                    ->schema([
                        Forms\Components\Select::make('funnel_id')
                            ->relationship('funnel', 'name')
                            ->disabled(),
                        Forms\Components\Select::make('subscriber_id')
                            ->relationship('subscriber', 'email')
                            ->disabled(),
                        Forms\Components\TextInput::make('status')
                            ->disabled(),
                        Forms\Components\TextInput::make('current_step')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('started_at')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Run Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('funnel.name')
                            ->label('Funnel'),
                        Infolists\Components\TextEntry::make('subscriber.email')
                            ->label('Subscriber'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                FunnelRun::STATUS_RUNNING => 'info',
                                FunnelRun::STATUS_COMPLETED => 'success',
                                FunnelRun::STATUS_FAILED => 'danger',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('current_step')
                            ->label('Current Step'),
                        Infolists\Components\TextEntry::make('started_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('completed_at')
                            ->dateTime(),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Step Execution Log')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('runSteps')
                            ->schema([
                                Infolists\Components\TextEntry::make('funnelStep.type')
                                    ->label('Type')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        FunnelRunStep::STATUS_PENDING => 'gray',
                                        FunnelRunStep::STATUS_SUCCESS => 'success',
                                        FunnelRunStep::STATUS_FAILED => 'danger',
                                        default => 'gray',
                                    }),
                                Infolists\Components\TextEntry::make('executed_at')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('error_message')
                                    ->label('Error')
                                    ->visible(fn ($state): bool => ! empty($state))
                                    ->color('danger'),
                                Infolists\Components\TextEntry::make('payload')
                                    ->label('Payload')
                                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state)
                                    ->visible(fn ($state): bool => ! empty($state)),
                            ])
                            ->columns(4),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Run ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('funnel.name')
                    ->label('Funnel')
                    ->searchable(),
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
                    ->label('Step'),
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
                Tables\Filters\SelectFilter::make('funnel_id')
                    ->relationship('funnel', 'name')
                    ->label('Funnel'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFunnelRuns::route('/'),
            'view' => Pages\ViewFunnelRun::route('/{record}'),
        ];
    }
}
