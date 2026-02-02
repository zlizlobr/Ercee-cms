<?php

namespace App\Filament\Resources\MenuResource\RelationManagers;

use App\Domain\Content\Navigation;
use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'allItems';

    protected static ?string $title = 'Navigation Items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),

                Forms\Components\Hidden::make('slug'),

                Forms\Components\TextInput::make('classes')
                    ->maxLength(255)
                    ->default('')
                    ->dehydrateStateUsing(fn ($state) => $state ?? ''),

                Forms\Components\Select::make('parent_id')
                    ->label('Parent Item')
                    ->options(function (RelationManager $livewire) {
                        return Navigation::where('menu_id', $livewire->ownerRecord->id)
                            ->whereNull('parent_id')
                            ->pluck('title', 'id');
                    })
                    ->placeholder('None (Root Item)')
                    ->searchable(),

                Forms\Components\Select::make('page_id')
                    ->label('Link to Page')
                    ->options(fn () => Page::all()->mapWithKeys(fn ($page) => [$page->id => $page->getLocalizedTitle()]))
                    ->searchable()
                    ->placeholder('Select a page...')
                    ->helperText('Or use custom URL below'),

                Forms\Components\TextInput::make('url')
                    ->label('Custom URL / Anchor')
                    ->placeholder('/page, #section, https://...')
                    ->helperText('Supports: /relative-path, #anchor, https://external.com'),

                Forms\Components\Select::make('target')
                    ->label('Open in')
                    ->options([
                        '_self' => 'Same window',
                        '_blank' => 'New window/tab',
                    ])
                    ->default('_self'),

                Forms\Components\TextInput::make('position')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.title')
                    ->label('Parent')
                    ->placeholder('Root'),
                Tables\Columns\TextColumn::make('page.slug')
                    ->label('Linked Page')
                    ->formatStateUsing(fn ($record) => $record->page?->getLocalizedTitle())
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->formatStateUsing(fn ($record) => $record->getUrl() ?? '-')
                    ->limit(30),
                Tables\Columns\TextColumn::make('position')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('position')
            ->reorderable('position')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
