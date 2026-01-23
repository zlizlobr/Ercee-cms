<?php

namespace App\Filament\Pages;

use App\Domain\Content\Menu;
use App\Domain\Content\Page as ContentPage;
use App\Domain\Content\ThemeSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class ThemeSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static ?string $navigationGroup = 'Thema';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.theme-settings';

    protected static ?string $title = 'Theme Settings';

    protected static ?string $navigationLabel = 'Theme Settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = ThemeSetting::first();

        $this->form->fill([
            'global' => $settings?->global ?? ThemeSetting::defaultGlobal(),
            'header' => $settings?->header ?? ThemeSetting::defaultHeader(),
            'footer' => $settings?->footer ?? ThemeSetting::defaultFooter(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('ThemeSettingsTabs')
                    ->tabs([
                        $this->globalTab(),
                        $this->headerTab(),
                        $this->footerTab(),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    /**
     * Create link fields group (link_type, url, page_id).
     */
    protected function linkFields(string $prefix, string $label, ?string $defaultUrl = null, bool $isOverride = false): array
    {
        $linkTypeKey = "{$prefix}_link_type";
        $urlKey = "{$prefix}_url";
        $pageIdKey = "{$prefix}_page_id";

        return [
            Forms\Components\Select::make($linkTypeKey)
                ->label("{$label} Link Type")
                ->options([
                    'url' => 'Custom URL',
                    'page' => 'Page',
                ])
                ->default($isOverride ? null : 'url')
                ->placeholder($isOverride ? 'Use global setting' : null)
                ->live(),
            Forms\Components\TextInput::make($urlKey)
                ->label("{$label} URL")
                ->default($defaultUrl)
                ->placeholder($isOverride ? 'Use global setting' : null)
                ->visible(fn (Forms\Get $get) => in_array($get($linkTypeKey), [null, 'url'])),
            Forms\Components\Select::make($pageIdKey)
                ->label("{$label} Page")
                ->options(ContentPage::where('status', 'published')->pluck('title', 'id'))
                ->searchable()
                ->placeholder('Select a page')
                ->visible(fn (Forms\Get $get) => $get($linkTypeKey) === 'page'),
        ];
    }

    protected function globalTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('Global')
            ->icon('heroicon-o-globe-alt')
            ->schema([
                Forms\Components\Section::make('Logo Settings')
                    ->schema([
                        Forms\Components\Select::make('global.logo_type')
                            ->label('Logo Type')
                            ->options([
                                'text' => 'Text',
                                'image' => 'Image',
                            ])
                            ->default('text')
                            ->live(),
                        Forms\Components\TextInput::make('global.logo_text')
                            ->label('Logo Text')
                            ->default('Ercee')
                            ->visible(fn (Forms\Get $get) => $get('global.logo_type') === 'text'),
                        Forms\Components\FileUpload::make('global.logo_image')
                            ->label('Logo Image')
                            ->disk('public')
                            ->directory('theme/logos')
                            ->image()
                            ->imageEditor()
                            ->visible(fn (Forms\Get $get) => $get('global.logo_type') === 'image'),
                        ...$this->linkFields('global.logo', 'Logo', '/'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('CTA Settings')
                    ->description('Default CTA button settings for header and footer')
                    ->schema([
                        Forms\Components\TextInput::make('global.cta_label')
                            ->label('CTA Label')
                            ->default('Kontaktujte nás'),
                        ...$this->linkFields('global.cta', 'CTA', '/rfq'),
                    ])
                    ->columns(2),
            ]);
    }

    protected function headerTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('Header')
            ->icon('heroicon-o-bars-3')
            ->schema([
                Forms\Components\Section::make('Header Logo (Override)')
                    ->description('Leave empty to use global settings')
                    ->schema([
                        Forms\Components\Select::make('header.logo_type')
                            ->label('Logo Type')
                            ->options([
                                'text' => 'Text',
                                'image' => 'Image',
                            ])
                            ->placeholder('Use global setting')
                            ->live(),
                        Forms\Components\TextInput::make('header.logo_text')
                            ->label('Logo Text')
                            ->placeholder('Use global setting')
                            ->visible(fn (Forms\Get $get) => $get('header.logo_type') === 'text'),
                        Forms\Components\FileUpload::make('header.logo_image')
                            ->label('Logo Image')
                            ->disk('public')
                            ->directory('theme/logos')
                            ->image()
                            ->imageEditor()
                            ->visible(fn (Forms\Get $get) => $get('header.logo_type') === 'image'),
                        ...$this->linkFields('header.logo', 'Logo', null, true),
                    ])
                    ->columns(2)
                    ->collapsed(),

                Forms\Components\Section::make('Header Navigation')
                    ->schema([
                        Forms\Components\Select::make('header.menu_id')
                            ->label('Navigation Menu')
                            ->options(Menu::pluck('name', 'id'))
                            ->placeholder('Select a menu')
                            ->searchable(),
                    ]),

                Forms\Components\Section::make('Header CTA (Override)')
                    ->description('Leave empty to use global settings')
                    ->schema([
                        Forms\Components\TextInput::make('header.cta_label')
                            ->label('CTA Label')
                            ->placeholder('Use global setting'),
                        ...$this->linkFields('header.cta', 'CTA', null, true),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    protected function footerTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('Footer')
            ->icon('heroicon-o-bars-3-bottom-left')
            ->schema([
                Forms\Components\Section::make('Footer Logo (Override)')
                    ->description('Leave empty to use global settings')
                    ->schema([
                        Forms\Components\Select::make('footer.logo_type')
                            ->label('Logo Type')
                            ->options([
                                'text' => 'Text',
                                'image' => 'Image',
                            ])
                            ->placeholder('Use global setting')
                            ->live(),
                        Forms\Components\TextInput::make('footer.logo_text')
                            ->label('Logo Text')
                            ->placeholder('Use global setting')
                            ->visible(fn (Forms\Get $get) => $get('footer.logo_type') === 'text'),
                        Forms\Components\FileUpload::make('footer.logo_image')
                            ->label('Logo Image')
                            ->disk('public')
                            ->directory('theme/logos')
                            ->image()
                            ->imageEditor()
                            ->visible(fn (Forms\Get $get) => $get('footer.logo_type') === 'image'),
                    ])
                    ->columns(2)
                    ->collapsed(),

                Forms\Components\Section::make('Company Info')
                    ->schema([
                        Forms\Components\Textarea::make('footer.company_text')
                            ->label('Company Description')
                            ->rows(3)
                            ->placeholder('Poskytujeme komplexní řešení pro vaše projekty.'),
                        Forms\Components\TextInput::make('footer.copyright_text')
                            ->label('Copyright Text')
                            ->placeholder('Leave empty for default: © {year} Ercee. Všechna práva vyhrazena.'),
                    ]),

                Forms\Components\Section::make('Footer Menus')
                    ->schema([
                        Forms\Components\Select::make('footer.quick_links_menu_id')
                            ->label('Quick Links Menu')
                            ->options(Menu::pluck('name', 'id'))
                            ->placeholder('Select a menu')
                            ->searchable(),
                        Forms\Components\Select::make('footer.services_menu_id')
                            ->label('Services Menu')
                            ->options(Menu::pluck('name', 'id'))
                            ->placeholder('Select a menu')
                            ->searchable(),
                        Forms\Components\Select::make('footer.contact_menu_id')
                            ->label('Contact Menu')
                            ->options(Menu::pluck('name', 'id'))
                            ->placeholder('Select a menu')
                            ->searchable(),
                        Forms\Components\Select::make('footer.legal_menu_id')
                            ->label('Legal Menu')
                            ->options(Menu::pluck('name', 'id'))
                            ->placeholder('Select a menu')
                            ->searchable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Footer CTA (Override)')
                    ->description('Leave empty to use global settings')
                    ->schema([
                        Forms\Components\TextInput::make('footer.cta_label')
                            ->label('CTA Label')
                            ->placeholder('Use global setting'),
                        ...$this->linkFields('footer.cta', 'CTA', null, true),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = ThemeSetting::first() ?? new ThemeSetting();
        $settings->global = $data['global'] ?? [];
        $settings->header = $data['header'] ?? [];
        $settings->footer = $data['footer'] ?? [];
        $settings->save();

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }
}
