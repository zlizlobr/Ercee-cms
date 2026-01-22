<?php

namespace App\Filament\Resources;

use App\Domain\Form\Contract;
use App\Filament\Resources\ContractResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contract Details')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->options([
                                Contract::STATUS_NEW => 'New',
                                Contract::STATUS_QUALIFIED => 'Qualified',
                                Contract::STATUS_CONVERTED => 'Converted',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('source')
                            ->disabled(),
                        Forms\Components\Select::make('form_id')
                            ->relationship('form', 'name')
                            ->disabled(),
                        Forms\Components\Select::make('subscriber_id')
                            ->relationship('subscriber', 'email')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Submitted Data')
                    ->schema([
                        Forms\Components\KeyValue::make('data')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('form.name')
                    ->label('Form')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'qualified' => 'warning',
                        'converted' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('source')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        Contract::STATUS_NEW => 'New',
                        Contract::STATUS_QUALIFIED => 'Qualified',
                        Contract::STATUS_CONVERTED => 'Converted',
                    ]),
                Tables\Filters\SelectFilter::make('form_id')
                    ->relationship('form', 'name')
                    ->label('Form'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContracts::route('/'),
            'view' => Pages\ViewContract::route('/{record}'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
