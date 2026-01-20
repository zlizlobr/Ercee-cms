<?php

namespace App\Filament\Resources\MenuResource\RelationManagers;

use App\Domain\Content\Navigation;
use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
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

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('parent_id')
                    ->label('Parent Item')
                    ->options(function (RelationManager $livewire) {
                        return Navigation::where('menu_id', $livewire->ownerRecord->id)
                            ->whereNull('parent_id')
                            ->pluck('title', 'id');
                    })
                    ->placeholder('None (Root Item)')
                    ->searchable(),

                Forms\Components\Select::make('link_type')
                    ->label('Link Type')
                    ->options([
                        'page' => 'Page',
                        'url' => 'Custom URL / Anchor',
                    ])
                    ->default('url')
                    ->live()
                    ->dehydrated(false),

                Forms\Components\Select::make('navigable_id')
                    ->label('Select Page')
                    ->options(fn () => Page::all()->mapWithKeys(fn ($page) => [$page->id => $page->getLocalizedTitle()]))
                    ->searchable()
                    ->visible(fn (Get $get) => $get('link_type') === 'page')
                    ->afterStateHydrated(function (Forms\Components\Select $component, $state, $record) {
                        if ($record && $record->navigable_type === Page::class) {
                            $component->state($record->navigable_id);
                        }
                    })
                    ->dehydrateStateUsing(fn ($state) => $state),

                Forms\Components\Hidden::make('navigable_type')
                    ->dehydrateStateUsing(fn (Get $get) => $get('link_type') === 'page' ? Page::class : null),

                Forms\Components\TextInput::make('url')
                    ->label('URL / Anchor')
                    ->placeholder('/page, #section, https://...')
                    ->helperText('Supports: /relative-path, #anchor, https://external.com')
                    ->visible(fn (Get $get) => $get('link_type') === 'url'),

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
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Set navigable fields based on link_type
                        if (($data['link_type'] ?? null) === 'page' && !empty($data['navigable_id'])) {
                            $data['navigable_type'] = Page::class;
                        } else {
                            $data['navigable_type'] = null;
                            $data['navigable_id'] = null;
                        }
                        unset($data['link_type']);
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, $record): array {
                        // Determine link_type based on existing data
                        if ($record->navigable_type === Page::class) {
                            $data['link_type'] = 'page';
                        } else {
                            $data['link_type'] = 'url';
                        }
                        return $data;
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        if (($data['link_type'] ?? null) === 'page' && !empty($data['navigable_id'])) {
                            $data['navigable_type'] = Page::class;
                            $data['url'] = null;
                        } else {
                            $data['navigable_type'] = null;
                            $data['navigable_id'] = null;
                        }
                        unset($data['link_type']);
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
