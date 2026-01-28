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
