<?php

namespace App\Filament\Resources\ThemeSettingResource\Pages;

use App\Domain\Content\CookieSetting;
use App\Domain\Content\Menu;
use App\Domain\Content\ThemeSetting;
use App\Filament\Components\LinkPicker;
use App\Filament\Components\MediaPicker;
use App\Filament\Resources\ThemeSettingResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

/**
 * Provides the custom Filament page for theme settings management.
 */
class ManageThemeSettings extends Page
{
    /**
     * @var string Filament resource class associated with this page controller.
     */
    protected static string $resource = ThemeSettingResource::class;

    /**
     * @var string Blade view identifier used to render this Filament component.
     */
    protected static string $view = 'filament.pages.theme-settings';

    /**
     * @var ?string UI title displayed for this resource page or relation manager.
     */
    protected static ?string $title = 'Theme Settings';

    /**
     * @var ?string Custom navigation label displayed in the admin sidebar.
     */
    protected static ?string $navigationLabel = 'Theme Settings';

    /**
     * @var ?array Form state payload containing editable theme settings values.
     */
    public ?array $data = [];

    /**
     * Hydrate the form with persisted theme settings.
     */
    public function mount(): void
    {
        $settings = ThemeSetting::first();
        $cookieSettings = CookieSetting::first();

        $this->form->fill([
            'global' => $settings?->global ?? ThemeSetting::defaultGlobal(),
            'header' => $settings?->header ?? ThemeSetting::defaultHeader(),
            'footer' => $settings?->footer ?? ThemeSetting::defaultFooter(),
            'cookies' => [
                'banner' => $cookieSettings?->banner ?? CookieSetting::defaultBanner(),
                'categories' => $this->categoriesToRepeater(
                    $cookieSettings?->categories ?? CookieSetting::defaultCategories()
                ),
                'services' => $this->servicesToRepeater(
                    $cookieSettings?->services ?? CookieSetting::defaultServices()
                ),
                'policy_links' => $cookieSettings?->policy_links ?? CookieSetting::defaultPolicyLinks(),
            ],
        ]);
    }

    /**
     * Build the form schema for this resource page.
     * @param Form $form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('ThemeSettingsTabs')
                    ->tabs([
                        $this->globalTab(),
                        $this->headerTab(),
                        $this->footerTab(),
                        $this->cookiesTab(),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function linkFields(string $name, string $label, ?string $defaultUrl = null, bool $isOverride = false): array
    {
        return LinkPicker::make($name)
            ->label($label)
            ->withLinkType()
            ->withoutAnchor()
            ->defaultUrl($defaultUrl)
            ->isOverride($isOverride)
            ->fields();
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
                        MediaPicker::make('global.logo_media_uuid')
                            ->label('Logo Image')
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
                        MediaPicker::make('header.logo_media_uuid')
                            ->label('Logo Image')
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
                        MediaPicker::make('footer.logo_media_uuid')
                            ->label('Logo Image')
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

    protected function cookiesTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('Cookies')
            ->icon('heroicon-o-shield-check')
            ->schema([
                Forms\Components\Section::make('Cookie Banner')
                    ->description('Nastavení consent banneru pro návštěvníky')
                    ->schema([
                        Forms\Components\Toggle::make('cookies.banner.enabled')
                            ->label('Enable Cookie Banner')
                            ->default(true),
                        Forms\Components\TextInput::make('cookies.banner.title')
                            ->label('Banner Title')
                            ->default('Tato stránka používá cookies'),
                        Forms\Components\Textarea::make('cookies.banner.description')
                            ->label('Banner Description')
                            ->rows(3)
                            ->default('Používáme cookies pro zlepšení vašeho zážitku na stránce, analýzu návštěvnosti a personalizaci obsahu.'),
                        Forms\Components\TextInput::make('cookies.banner.accept_all_label')
                            ->label('Accept All Button')
                            ->default('Přijmout vše'),
                        Forms\Components\TextInput::make('cookies.banner.reject_all_label')
                            ->label('Reject All Button')
                            ->default('Odmítnout vše'),
                        Forms\Components\TextInput::make('cookies.banner.customize_label')
                            ->label('Customize Button')
                            ->default('Nastavení'),
                        Forms\Components\TextInput::make('cookies.banner.save_label')
                            ->label('Save Button')
                            ->default('Uložit nastavení'),
                        Forms\Components\Select::make('cookies.banner.position')
                            ->label('Banner Position')
                            ->options([
                                'bottom' => 'Bottom',
                                'top' => 'Top',
                                'center' => 'Center (Modal)',
                            ])
                            ->default('bottom'),
                        Forms\Components\Select::make('cookies.banner.theme')
                            ->label('Banner Theme')
                            ->options([
                                'light' => 'Light',
                                'dark' => 'Dark',
                            ])
                            ->default('light'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Cookie Categories')
                    ->description('Definice kategorií cookies a jejich výchozího chování')
                    ->schema([
                        Forms\Components\Repeater::make('cookies.categories')
                            ->schema([
                                Forms\Components\TextInput::make('key')
                                    ->label('Category Key')
                                    ->required()
                                    ->helperText('e.g. necessary, analytics, marketing'),
                                Forms\Components\TextInput::make('name')
                                    ->label('Display Name')
                                    ->required(),
                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->rows(2),
                                Forms\Components\Toggle::make('required')
                                    ->label('Required (cannot be disabled)')
                                    ->default(false),
                                Forms\Components\Toggle::make('default_enabled')
                                    ->label('Enabled by Default')
                                    ->default(false),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ]),

                Forms\Components\Section::make('Third-Party Services')
                    ->description('Služby třetích stran přiřazené k jednotlivým kategoriím')
                    ->schema([
                        Forms\Components\Repeater::make('cookies.services')
                            ->schema([
                                Forms\Components\TextInput::make('category_key')
                                    ->label('Category')
                                    ->required()
                                    ->helperText('Must match a category key above'),
                                Forms\Components\TextInput::make('name')
                                    ->label('Service Name')
                                    ->required(),
                                Forms\Components\TextInput::make('description')
                                    ->label('Description'),
                                Forms\Components\TextInput::make('cookie_pattern')
                                    ->label('Cookie Pattern')
                                    ->helperText('e.g. _ga*, laravel_session'),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ]),

                Forms\Components\Section::make('Policy Links')
                    ->description('Odkazy na zásady ochrany osobních údajů a cookies')
                    ->schema([
                        Forms\Components\TextInput::make('cookies.policy_links.privacy_policy.label')
                            ->label('Privacy Policy Label')
                            ->default('Zásady ochrany osobních údajů'),
                        ...$this->linkFields('cookies.policy_links.privacy_policy', 'Privacy Policy', '/privacy-policy'),
                        Forms\Components\TextInput::make('cookies.policy_links.cookie_policy.label')
                            ->label('Cookie Policy Label')
                            ->default('Zásady cookies'),
                        ...$this->linkFields('cookies.policy_links.cookie_policy', 'Cookie Policy', '/cookie-policy'),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * Persist the submitted theme settings.
     */
    public function save(): void
    {
        $data = $this->form->getState();

        $settings = ThemeSetting::first() ?? new ThemeSetting;
        $settings->global = $data['global'] ?? [];
        $settings->header = $data['header'] ?? [];
        $settings->footer = $data['footer'] ?? [];
        $settings->save();

        $cookieData = $data['cookies'] ?? [];
        $cookieSettings = CookieSetting::first() ?? new CookieSetting;
        $cookieSettings->banner = $cookieData['banner'] ?? [];
        $cookieSettings->categories = $this->repeaterToCategories($cookieData['categories'] ?? []);
        $cookieSettings->services = $this->repeaterToServices($cookieData['services'] ?? []);
        $cookieSettings->policy_links = $cookieData['policy_links'] ?? [];
        $cookieSettings->save();

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }

    protected function categoriesToRepeater(array $categories): array
    {
        $items = [];

        foreach ($categories as $key => $category) {
            $items[] = array_merge(['key' => $key], $category);
        }

        return $items;
    }

    protected function repeaterToCategories(array $items): array
    {
        $categories = [];

        foreach ($items as $item) {
            $key = $item['key'] ?? null;
            if (! $key) {
                continue;
            }
            unset($item['key']);
            $categories[$key] = $item;
        }

        return $categories;
    }

    protected function servicesToRepeater(array $services): array
    {
        $items = [];

        foreach ($services as $categoryKey => $serviceList) {
            foreach ($serviceList as $service) {
                $items[] = array_merge(['category_key' => $categoryKey], $service);
            }
        }

        return $items;
    }

    protected function repeaterToServices(array $items): array
    {
        $services = [];

        foreach ($items as $item) {
            $categoryKey = $item['category_key'] ?? 'uncategorized';
            unset($item['category_key']);
            $services[$categoryKey][] = $item;
        }

        return $services;
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


