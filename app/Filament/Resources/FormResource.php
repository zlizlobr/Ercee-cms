<?php

namespace App\Filament\Resources;

use App\Domain\Form\Form;
use App\Filament\Resources\FormResource\Pages;
use App\Support\FormIconRegistry;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FormResource extends Resource
{
    protected static ?string $model = Form::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 1;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Form Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Form Settings')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('submit_button_text')
                                    ->label('Submit button text')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('success_title')
                                    ->label('Success title')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('success_message')
                                    ->label('Success message')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                                Forms\Components\Toggle::make('active')
                                    ->default(true),
                            ])
                            ->columns(2),
                        Forms\Components\Tabs\Tab::make('Form Fields')
                            ->schema([
                                Forms\Components\Toggle::make('contact_form_preset')
                                    ->label('Apply contact form schema')
                                    ->helperText('Sets schema to name, email, phone, message fields.')
                                    ->dehydrated(false)
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, ?bool $state) {
                                        if (!$state) {
                                            return;
                                        }

                                        $set('schema', [
                                            [
                                                'name' => 'name',
                                                'label' => 'Name',
                                                'type' => 'text',
                                                'required' => true,
                                            ],
                                            [
                                                'name' => 'email',
                                                'label' => 'Email',
                                                'type' => 'email',
                                                'required' => true,
                                            ],
                                            [
                                                'name' => 'phone',
                                                'label' => 'Phone',
                                                'type' => 'text',
                                                'required' => true,
                                            ],
                                            [
                                                'name' => 'message',
                                                'label' => 'Message',
                                                'type' => 'textarea',
                                                'required' => true,
                                            ],
                                        ]);

                                        $set('contact_form_preset', false);
                                    }),
                                Forms\Components\Repeater::make('schema')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Field Name')
                                            ->required(fn (Get $get): bool => $get('type') !== 'section')
                                            ->alphaDash()
                                            ->visible(fn (Get $get): bool => $get('type') !== 'section')
                                            ->helperText('Use lowercase with underscores (e.g., first_name)'),

                                        Forms\Components\TextInput::make('label')
                                            ->label(fn (Get $get): string => $get('type') === 'section' ? 'Section title' : 'Field Label')
                                            ->required(),

                                        Forms\Components\Select::make('type')
                                            ->label('Field Type')
                                            ->options([
                                                'section' => 'Section',
                                                'text' => 'Text',
                                                'email' => 'Email',
                                                'tel' => 'Tel',
                                                'number' => 'Number',
                                                'password' => 'Password',
                                                'url' => 'URL',
                                                'date' => 'Date',
                                                'time' => 'Time',
                                                'datetime-local' => 'Datetime',
                                                'textarea' => 'Textarea',
                                                'select' => 'Select',
                                                'checkbox' => 'Checkbox',
                                                'radio' => 'Radio',
                                                'file' => 'File',
                                                'hidden' => 'Hidden',
                                            ])
                                            ->required()
                                            ->live(),

                                        Forms\Components\Toggle::make('required')
                                            ->label('Required')
                                            ->visible(fn (Get $get): bool => $get('type') !== 'section')
                                            ->default(false),

                                        Forms\Components\TextInput::make('placeholder')
                                            ->label('Placeholder')
                                            ->visible(fn (Get $get): bool => $get('type') !== 'section')
                                            ->maxLength(255),

                                        Forms\Components\Select::make('icon')
                                            ->label('Icon')
                                            ->options(FormIconRegistry::options())
                                            ->searchable()
                                            ->placeholder('Select icon...')
                                            ->visible(fn (Get $get): bool => $get('type') === 'section'),

                                        Forms\Components\Textarea::make('helper_text')
                                            ->label('Helper text')
                                            ->visible(fn (Get $get): bool => $get('type') !== 'section')
                                            ->rows(2)
                                            ->maxLength(255),

                                        Forms\Components\Repeater::make('options')
                                            ->label('Options')
                                            ->schema([
                                                Forms\Components\TextInput::make('label')
                                                    ->required(),
                                                Forms\Components\TextInput::make('value')
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(2)
                                            ->visible(fn (Get $get): bool => in_array($get('type'), ['select', 'radio'], true)),
                                    ])
                                    ->itemLabel(fn (array $state): ?string => ($state['label'] ?? 'New Field').' ('.($state['type'] ?? 'text').')')
                                    ->reorderable()
                                    ->reorderableWithButtons()
                                    ->collapsible()
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Field'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('schema')
                    ->label('Fields')
                    ->formatStateUsing(fn (mixed $state): string => is_array($state) ? count($state).' fields' : '0 fields'),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('contracts_count')
                    ->label('Submissions')
                    ->counts('contracts')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListForms::route('/'),
            'create' => Pages\CreateForm::route('/create'),
            'edit' => Pages\EditForm::route('/{record}/edit'),
        ];
    }
}
