<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Module Discovery Path
    |--------------------------------------------------------------------------
    |
    | Path where modules are located. Modules can be installed via Composer
    | or placed directly in this directory.
    |
    */
    'path' => base_path('modules'),

    /*
    |--------------------------------------------------------------------------
    | Module Migrations Loading
    |--------------------------------------------------------------------------
    |
    | Controls whether module migration paths should be registered dynamically.
    | You can optionally restrict migration registration to selected modules
    | via MODULE_MIGRATION_ALLOWLIST (comma-separated module names).
    |
    */
    'load_module_migrations' => env('MODULE_LOAD_MIGRATIONS', true),
    'module_migration_allowlist' => array_values(array_filter(array_map(
        static fn (string $value): string => trim($value),
        explode(',', (string) env('MODULE_MIGRATION_ALLOWLIST', ''))
    ))),

    /*
    |--------------------------------------------------------------------------
    | Registered Modules
    |--------------------------------------------------------------------------
    |
    | List of modules with their configuration. Each module can be enabled
    | or disabled independently. Modules are loaded in order of dependencies.
    |
    | Configuration options:
    | - enabled: bool - Whether the module is active
    | - provider: string - Module service provider class
    | - version: string - Module version constraint
    | - dependencies: array - Required modules with version constraints
    |
    */
    'modules' => [
        'analytics' => [
            'enabled' => true,
            'provider' => \Modules\Analytics\AnalyticsModuleServiceProvider::class,
            'version' => '1.0.0',
            'dependencies' => [],
        ],

        'forms' => [
            'enabled' => true,
            'provider' => \Modules\Forms\FormsModuleServiceProvider::class,
            'version' => '1.0.0',
            'dependencies' => [],
        ],

        'commerce' => [
            'enabled' => true,
            'provider' => \Modules\Commerce\CommerceModuleServiceProvider::class,
            'version' => '1.0.0',
            'dependencies' => [],
        ],

        'funnel' => [
            'enabled' => true,
            'provider' => \Modules\Funnel\FunnelModuleServiceProvider::class,
            'version' => '1.0.0',
            'dependencies' => [
                'forms' => '^1.0',
                'commerce' => '^1.0',
            ],
        ],

        'llm' => [
            'enabled' => true,
            'provider' => \Modules\Llm\LlmModuleServiceProvider::class,
            'version' => '1.0.0',
            'description' => 'Unified LLM Module (OpenAI, Claude, Gemini)',
            'dependencies' => [],
        ],

        'theme-builds' => [
            'enabled' => true,
            'provider' => \Modules\ThemeBuilds\ThemeBuildsModuleServiceProvider::class,
            'version' => '1.0.0',
            'dependencies' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Assets
    |--------------------------------------------------------------------------
    |
    | Configuration for module asset publishing.
    |
    */
    'assets' => [
        'publish_path' => public_path('vendor'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Cache
    |--------------------------------------------------------------------------
    |
    | Cache configuration for module discovery and registration.
    |
    */
    'cache' => [
        'enabled' => env('MODULE_CACHE_ENABLED', true),
        'key' => 'modules.registry',
        'ttl' => 3600,
    ],
];
