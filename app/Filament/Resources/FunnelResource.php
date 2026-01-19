<?php

namespace App\Filament\Resources;

use App\Domain\Funnel\Funnel;
use App\Domain\Funnel\FunnelStep;
use App\Filament\Resources\FunnelResource\Pages;
use App\Filament\Resources\FunnelResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FunnelResource extends Resource
{
    protected static ?string $model = Funnel::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Automation';

    protected static ?int $navigationSort = 1;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Funnel Settings')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('trigger_type')
                            ->label('Trigger')
                            ->options([
                                Funnel::TRIGGER_CONTRACT_CREATED => 'Contract Created (Form Submission)',
                                Funnel::TRIGGER_ORDER_PAID => 'Order Paid',
                                Funnel::TRIGGER_MANUAL => 'Manual',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('active')
                            ->default(true),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Funnel Steps')
                    ->schema([
                        Forms\Components\Repeater::make('steps')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Step Type')
                                    ->options(FunnelStep::getTypes())
                                    ->required()
                                    ->live()
                                    ->columnSpan(1),

                                Forms\Components\Hidden::make('position'),

                                // Delay config
                                Forms\Components\TextInput::make('config.seconds')
                                    ->label('Delay (seconds)')
                                    ->numeric()
                                    ->default(3600)
                                    ->visible(fn (Get $get): bool => $get('type') === FunnelStep::TYPE_DELAY)
                                    ->columnSpan(2),

                                // Email config
                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('config.subject')
                                        ->label('Subject')
                                        ->required(fn (Get $get): bool => $get('type') === FunnelStep::TYPE_EMAIL),
                                    Forms\Components\Textarea::make('config.body')
                                        ->label('Email Body')
                                        ->rows(3)
                                        ->required(fn (Get $get): bool => $get('type') === FunnelStep::TYPE_EMAIL),
                                    Forms\Components\TextInput::make('config.template')
                                        ->label('Template (optional)')
                                        ->default('default'),
                                ])
                                    ->visible(fn (Get $get): bool => $get('type') === FunnelStep::TYPE_EMAIL)
                                    ->columnSpan(2),

                                // Webhook config
                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('config.url')
                                        ->label('Webhook URL')
                                        ->url()
                                        ->required(fn (Get $get): bool => $get('type') === FunnelStep::TYPE_WEBHOOK),
                                    Forms\Components\Select::make('config.method')
                                        ->label('Method')
                                        ->options([
                                            'POST' => 'POST',
                                            'GET' => 'GET',
                                            'PUT' => 'PUT',
                                            'PATCH' => 'PATCH',
                                        ])
                                        ->default('POST'),
                                    Forms\Components\KeyValue::make('config.headers')
                                        ->label('Headers')
                                        ->keyLabel('Header')
                                        ->valueLabel('Value')
                                        ->default([]),
                                    Forms\Components\Textarea::make('config.body')
                                        ->label('Body (JSON)')
                                        ->rows(3)
                                        ->helperText('Use placeholders: {{subscriber_id}}, {{subscriber_email}}, {{funnel_run_id}}, {{funnel_id}}')
                                        ->afterStateHydrated(function (Forms\Components\Textarea $component, $state) {
                                            if (is_array($state)) {
                                                $component->state(json_encode($state, JSON_PRETTY_PRINT));
                                            }
                                        })
                                        ->dehydrateStateUsing(fn ($state) => json_decode($state, true) ?? []),
                                ])
                                    ->visible(fn (Get $get): bool => $get('type') === FunnelStep::TYPE_WEBHOOK)
                                    ->columnSpan(2),

                                // Tag config
                                Forms\Components\TextInput::make('config.tag')
                                    ->label('Tag')
                                    ->required(fn (Get $get): bool => $get('type') === FunnelStep::TYPE_TAG)
                                    ->visible(fn (Get $get): bool => $get('type') === FunnelStep::TYPE_TAG)
                                    ->columnSpan(2),
                            ])
                            ->columns(3)
                            ->itemLabel(fn (array $state): ?string => self::getStepLabel($state))
                            ->reorderable()
                            ->reorderableWithButtons()
                            ->orderColumn('position')
                            ->collapsible()
                            ->defaultItems(0)
                            ->addActionLabel('Add Step'),
                    ]),
            ]);
    }

    protected static function getStepLabel(array $state): string
    {
        $type = $state['type'] ?? 'unknown';
        $types = FunnelStep::getTypes();
        $typeName = $types[$type] ?? 'Unknown';

        return match ($type) {
            FunnelStep::TYPE_DELAY => '⏱️ Delay: '.($state['config']['seconds'] ?? 0).'s',
            FunnelStep::TYPE_EMAIL => '✉️ Email: '.($state['config']['subject'] ?? 'No subject'),
            FunnelStep::TYPE_WEBHOOK => '🔗 Webhook: '.($state['config']['url'] ?? 'No URL'),
            FunnelStep::TYPE_TAG => '🏷️ Tag: '.($state['config']['tag'] ?? 'No tag'),
            default => $typeName,
        };
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('trigger_type')
                    ->label('Trigger')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Funnel::TRIGGER_CONTRACT_CREATED => 'success',
                        Funnel::TRIGGER_ORDER_PAID => 'info',
                        Funnel::TRIGGER_MANUAL => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('steps_count')
                    ->label('Steps')
                    ->counts('steps')
                    ->sortable(),
                Tables\Columns\TextColumn::make('runs_count')
                    ->label('Runs')
                    ->counts('runs')
                    ->sortable(),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active'),
                Tables\Filters\SelectFilter::make('trigger_type')
                    ->options([
                        Funnel::TRIGGER_CONTRACT_CREATED => 'Contract Created',
                        Funnel::TRIGGER_ORDER_PAID => 'Order Paid',
                        Funnel::TRIGGER_MANUAL => 'Manual',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RunsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFunnels::route('/'),
            'create' => Pages\CreateFunnel::route('/create'),
            'edit' => Pages\EditFunnel::route('/{record}/edit'),
        ];
    }
}
