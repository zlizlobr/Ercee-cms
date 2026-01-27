<?php

namespace App\Filament\Resources;

use App\Domain\Form\Form;
use App\Filament\Resources\FormResource\Pages;
use App\Support\FormFieldTypeRegistry;
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
                                Forms\Components\TextInput::make('data_options.submit_button_text')
                                    ->label('Submit button text')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('data_options.success_title')
                                    ->label('Success title')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('data_options.success_message')
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
                                            ->required(fn (Get $get): bool => FormFieldTypeRegistry::supports($get('type'), 'name'))
                                            ->alphaDash()
                                            ->visible(fn (Get $get): bool => FormFieldTypeRegistry::supports($get('type'), 'name'))
                                            ->helperText('Use lowercase with underscores (e.g., first_name)'),

                                        Forms\Components\TextInput::make('label')
                                            ->label(fn (Get $get): string => $get('type') === 'section' ? 'Section title' : 'Field Label')
                                            ->required(),

                                        Forms\Components\Select::make('type')
                                            ->label('Field Type')
                                            ->options(FormFieldTypeRegistry::options())
                                            ->helperText(fn (Get $get): ?string => FormFieldTypeRegistry::description($get('type')))
                                            ->required()
                                            ->live(),

                                        Forms\Components\Toggle::make('required')
                                            ->label('Required')
                                            ->visible(fn (Get $get): bool => FormFieldTypeRegistry::supports($get('type'), 'required'))
                                            ->default(false),

                                        Forms\Components\TextInput::make('placeholder')
                                            ->label('Placeholder')
                                            ->visible(fn (Get $get): bool => FormFieldTypeRegistry::supports($get('type'), 'placeholder'))
                                            ->maxLength(255),

                                        Forms\Components\Select::make('icon')
                                            ->label('Icon')
                                            ->options(FormIconRegistry::options())
                                            ->searchable()
                                            ->placeholder('Select icon...')
                                            ->visible(fn (Get $get): bool => FormFieldTypeRegistry::supports($get('type'), 'icon')),

                                        Forms\Components\Textarea::make('helper_text')
                                            ->label('Helper text')
                                            ->visible(fn (Get $get): bool => FormFieldTypeRegistry::supports($get('type'), 'helper_text'))
                                            ->rows(2)
                                            ->maxLength(255),

                                        Forms\Components\Repeater::make('options')
                                            ->label('Options')
                                            ->schema([
                                                Forms\Components\TextInput::make('label')
                                                    ->required(),
                                                Forms\Components\TextInput::make('value')
                                                    ->required(),
                                                Forms\Components\Select::make('icon')
                                                    ->label('Icon')
                                                    ->options(FormIconRegistry::options())
                                                    ->searchable()
                                                    ->placeholder('Select icon...')
                                                    ->visible(fn (Get $get): bool => FormFieldTypeRegistry::supports($get('../../type'), 'options_icon')),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(2)
                                            ->visible(fn (Get $get): bool => FormFieldTypeRegistry::supports($get('type'), 'options')),
                                    ])
                                    ->itemLabel(fn (array $state): ?string => ($state['label'] ?? 'New Field').' ('.($state['type'] ?? 'text').')')
                                    ->reorderable()
                                    ->reorderableWithButtons()
                                    ->collapsible()
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Field'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Sidebar')
                            ->schema([
                                Forms\Components\Repeater::make('data_options.sidebar')
                                    ->label('Sidebar sections')
                                    ->schema([
                                        Forms\Components\Select::make('type')
                                            ->label('Section type')
                                            ->options([
                                                'contact_info' => 'Contact info',
                                                'steps' => 'Steps',
                                                'trust_indicators' => 'Trust indicators',
                                            ])
                                            ->required()
                                            ->live(),
                                        Forms\Components\TextInput::make('title')
                                            ->label('Section title')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Repeater::make('items')
                                            ->label('Items')
                                            ->schema([
                                                Forms\Components\TextInput::make('label')
                                                    ->label('Label')
                                                    ->required(fn (Get $get): bool => $get('../../type') === 'contact_info')
                                                    ->visible(fn (Get $get): bool => $get('../../type') === 'contact_info')
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('value')
                                                    ->label('Value')
                                                    ->required(fn (Get $get): bool => $get('../../type') === 'contact_info')
                                                    ->visible(fn (Get $get): bool => $get('../../type') === 'contact_info')
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('note')
                                                    ->label('Note')
                                                    ->visible(fn (Get $get): bool => $get('../../type') === 'contact_info')
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('title')
                                                    ->label('Step title')
                                                    ->required(fn (Get $get): bool => $get('../../type') === 'steps')
                                                    ->visible(fn (Get $get): bool => $get('../../type') === 'steps')
                                                    ->maxLength(255),
                                                Forms\Components\Textarea::make('description')
                                                    ->label('Step description')
                                                    ->required(fn (Get $get): bool => $get('../../type') === 'steps')
                                                    ->visible(fn (Get $get): bool => $get('../../type') === 'steps')
                                                    ->rows(2)
                                                    ->maxLength(500),
                                                Forms\Components\TextInput::make('number')
                                                    ->label('Step number')
                                                    ->visible(fn (Get $get): bool => $get('../../type') === 'steps')
                                                    ->maxLength(10),
                                                Forms\Components\TextInput::make('text')
                                                    ->label('Text')
                                                    ->required(fn (Get $get): bool => $get('../../type') === 'trust_indicators')
                                                    ->visible(fn (Get $get): bool => $get('../../type') === 'trust_indicators')
                                                    ->maxLength(255),
                                                Forms\Components\Select::make('icon')
                                                    ->label('Icon')
                                                    ->options(FormIconRegistry::options())
                                                    ->searchable()
                                                    ->placeholder('Select icon...'),
                                                Forms\Components\Select::make('tone')
                                                    ->label('Tone')
                                                    ->options([
                                                        'blue' => 'Blue',
                                                        'teal' => 'Teal',
                                                        'green' => 'Green',
                                                        'purple' => 'Purple',
                                                        'emerald' => 'Emerald',
                                                    ])
                                                    ->native(false),
                                            ])
                                            ->itemLabel(fn (array $state): ?string => $state['label']
                                                ?? $state['title']
                                                ?? $state['text']
                                                ?? 'Item')
                                            ->reorderable()
                                            ->reorderableWithButtons()
                                            ->collapsible()
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Item'),
                                    ])
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Section')
                                    ->reorderable()
                                    ->reorderableWithButtons()
                                    ->collapsible()
                                    ->default([
                                        [
                                            'type' => 'contact_info',
                                            'title' => 'Potřebujete pomoc?',
                                            'items' => [
                                                [
                                                    'label' => 'Telefon',
                                                    'value' => '+420 XXX XXX XXX',
                                                    'note' => 'Po-Pá, 8:00-17:00',
                                                    'icon' => 'phone',
                                                    'tone' => 'blue',
                                                ],
                                                [
                                                    'label' => 'E-mail',
                                                    'value' => 'info@ercee.cz',
                                                    'note' => 'Odpovíme do 24 hodin',
                                                    'icon' => 'mail',
                                                    'tone' => 'teal',
                                                ],
                                            ],
                                        ],
                                        [
                                            'type' => 'steps',
                                            'title' => 'Jak to funguje',
                                            'items' => [
                                                [
                                                    'title' => 'Odpověď',
                                                    'description' => 'Vaši poptávku zpracujeme do 24 hodin',
                                                    'number' => '1',
                                                    'tone' => 'blue',
                                                ],
                                                [
                                                    'title' => 'Konzultace',
                                                    'description' => 'Domluvíme si hovor pro upřesnění',
                                                    'number' => '2',
                                                    'tone' => 'teal',
                                                ],
                                                [
                                                    'title' => 'Nabídka',
                                                    'description' => 'Obdržíte detailní nabídku na míru',
                                                    'number' => '3',
                                                    'tone' => 'green',
                                                ],
                                                [
                                                    'title' => 'Spolupráce',
                                                    'description' => 'Začneme společnou cestu k úspěchu',
                                                    'number' => '4',
                                                    'tone' => 'purple',
                                                ],
                                            ],
                                        ],
                                        [
                                            'type' => 'trust_indicators',
                                            'title' => 'Proč my?',
                                            'items' => [
                                                [
                                                    'text' => '10+ let zkušeností v oboru',
                                                    'icon' => 'trend',
                                                    'tone' => 'green',
                                                ],
                                                [
                                                    'text' => '99% spokojenost klientů',
                                                    'icon' => 'check',
                                                    'tone' => 'blue',
                                                ],
                                                [
                                                    'text' => 'Certifikované procesy',
                                                    'icon' => 'shield',
                                                    'tone' => 'purple',
                                                ],
                                                [
                                                    'text' => 'Lokální tým v ČR',
                                                    'icon' => 'users',
                                                    'tone' => 'teal',
                                                ],
                                            ],
                                        ],
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Section')
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ])
            ->columns(1);
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
