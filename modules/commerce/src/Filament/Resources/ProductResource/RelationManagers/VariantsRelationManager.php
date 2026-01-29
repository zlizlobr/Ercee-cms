<?php

namespace Modules\Commerce\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Commerce\Domain\Product;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    public function isReadOnly(): bool
    {
        return $this->getOwnerRecord()->type !== Product::TYPE_VARIABLE;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('sku')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->step(1 / (10 ** config('commerce.currency.decimals')))
                    ->default(0)
                    ->prefix(config('commerce.currency.code')),
                Forms\Components\TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Select::make('attributeValues')
                    ->relationship('attributeValues', 'value')
                    ->multiple()
                    ->preload()
                    ->label('Variant Attributes'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sku')
            ->columns([
                Tables\Columns\TextColumn::make('sku'),
                Tables\Columns\TextColumn::make('price')
                    ->money(config('commerce.currency.code')),
                Tables\Columns\TextColumn::make('stock')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('attributeValues.value')
                    ->badge()
                    ->separator(', '),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn () => $this->getOwnerRecord()->type === Product::TYPE_VARIABLE),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(
                fn () => $this->getOwnerRecord()->type === Product::TYPE_VARIABLE
                    ? 'No variants yet'
                    : 'Variants are only available for variable products'
            );
    }
}
