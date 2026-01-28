<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'currency' => env('STRIPE_CURRENCY', 'czk'),
        'success_url' => env('STRIPE_SUCCESS_URL', env('APP_URL').'/payment/return'),
        'cancel_url' => env('STRIPE_CANCEL_URL', env('APP_URL').'/products'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook IP Whitelist
    |--------------------------------------------------------------------------
    |
    | IP addresses or CIDR ranges allowed to access webhook endpoints.
    | Stripe webhook IPs: https://stripe.com/docs/ips
    | Leave empty to allow all IPs (not recommended for production).
    |
    */
    'webhook_whitelist' => array_filter(explode(',', env('WEBHOOK_IP_WHITELIST', ''))),

    /*
    |--------------------------------------------------------------------------
    | GitHub Integration
    |--------------------------------------------------------------------------
    |
    | Configuration for GitHub API integration used to trigger frontend
    | rebuilds via repository_dispatch events.
    |
    */
    'github' => [
        'token' => env('GITHUB_TOKEN'),
        'frontend_repository' => env('GITHUB_FRONTEND_REPOSITORY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Frontend Rebuild
    |--------------------------------------------------------------------------
    |
    | Configuration for frontend rebuild behavior.
    |
    | rebuild_enabled: Whether to trigger rebuilds at all
    | rebuild_mode: 'github' (production), 'local' (dev), 'disabled'
    | rebuild_token: Secret token for rebuild endpoint authentication
    | local_frontend_path: Path to frontend project (for local mode)
    |
    */
    'frontend' => [
        'rebuild_enabled' => env('FRONTEND_REBUILD_ENABLED', true),
        'rebuild_mode' => env('FRONTEND_REBUILD_MODE', 'github'),
        'rebuild_token' => env('FRONTEND_REBUILD_TOKEN'),
        'local_frontend_path' => env('FRONTEND_LOCAL_PATH'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Media
    |--------------------------------------------------------------------------
    |
    | Configuration for media handling and SEO optimization.
    |
    | seo_rename_enabled: Auto-rename files to SEO-friendly names on save
    |
    */
    'media' => [
        'seo_rename_enabled' => env('MEDIA_SEO_RENAME_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Authentication
    |--------------------------------------------------------------------------
    |
    | Configuration for internal API authentication using bearer tokens.
    | The internal_token is used for /internal endpoints.
    | Token abilities define what actions a token can perform.
    |
    */
    'api' => [
        'internal_token' => env('API_INTERNAL_TOKEN'),
        'token_abilities' => explode(',', env('API_TOKEN_ABILITIES', 'internal:rebuild')),
    ],

];
