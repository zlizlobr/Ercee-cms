<?php

namespace App\Support\DevLayer;

/**
 * Resolves and exposes the shared Ercee dev-layer policy contract.
 */
class ErceeDevLayerPolicy
{
    public const PROFILE_DEV = 'dev';
    public const PROFILE_STAGING = 'staging';
    public const PROFILE_PROD = 'prod';

    /**
     * @var array<int, string>
     */
    private const ALLOWED_PROFILES = [
        self::PROFILE_DEV,
        self::PROFILE_STAGING,
        self::PROFILE_PROD,
    ];

    /**
     * @var array<int, string>
     */
    private const ALLOWED_LOG_LEVELS = [
        'debug',
        'info',
        'notice',
        'warning',
        'error',
        'critical',
        'alert',
        'emergency',
    ];

    public function __construct(
        private ?array $state = null
    ) {}

    /**
     * Resolve a normalized policy state from raw env-like input.
     *
     * @param array<string, mixed> $env
     * @return array<string, mixed>
     */
    public static function resolve(array $env): array
    {
        $invalid = [];

        $profile = self::resolveProfile($env, $invalid);
        $devLayerEnabled = self::toBool($env['ERCEE_DEV_LAYER'] ?? null, true, 'ERCEE_DEV_LAYER', $invalid);
        $requestedLevel = self::normalizeLogLevel($env['ERCEE_LOG_LEVEL'] ?? ($env['LOG_LEVEL'] ?? null), $invalid);

        $defaultLevel = match ($profile) {
            self::PROFILE_DEV => 'debug',
            self::PROFILE_STAGING => 'info',
            self::PROFILE_PROD => 'info',
        };

        $effectiveLevel = $requestedLevel ?? $defaultLevel;

        // Production must never emit debug logs from runtime policy.
        if ($profile === self::PROFILE_PROD && $effectiveLevel === 'debug') {
            $effectiveLevel = 'info';
        }

        // Disabled dev layer also strips debug level behavior.
        if (! $devLayerEnabled && $effectiveLevel === 'debug') {
            $effectiveLevel = 'info';
        }

        $publicDebugRequested = self::toBool(
            $env['ERCEE_PUBLIC_DEBUG'] ?? null,
            false,
            'ERCEE_PUBLIC_DEBUG',
            $invalid
        );

        $publicDebugEnabled = $devLayerEnabled
            && $profile === self::PROFILE_DEV
            && $publicDebugRequested;

        $canWriteDebugLogs = $devLayerEnabled
            && $profile === self::PROFILE_DEV
            && $effectiveLevel === 'debug';

        return [
            'runtime_profile' => $profile,
            'dev_layer_enabled' => $devLayerEnabled,
            'log_level_requested' => $requestedLevel,
            'log_level' => $effectiveLevel,
            'public_debug_requested' => $publicDebugRequested,
            'public_debug_enabled' => $publicDebugEnabled,
            'can_write_debug_logs' => $canWriteDebugLogs,
            'invalid_values' => $invalid,
            'contract' => [
                'variables' => [
                    'ERCEE_DEV_LAYER',
                    'ERCEE_LOG_LEVEL',
                    'ERCEE_PUBLIC_DEBUG',
                    'ERCEE_RUNTIME_PROFILE',
                ],
                'allowed_profiles' => self::ALLOWED_PROFILES,
                'allowed_log_levels' => self::ALLOWED_LOG_LEVELS,
            ],
            'behavior_matrix' => [
                self::PROFILE_DEV => [
                    'debug_logs' => 'ON',
                    'public_debug' => 'EXPLICIT_ONLY',
                    'astro_debug_plugins' => 'ON',
                    'npm_dev_only_scripts' => 'ON',
                ],
                self::PROFILE_STAGING => [
                    'debug_logs' => 'OFF',
                    'public_debug' => 'OFF',
                    'astro_debug_plugins' => 'OFF',
                    'npm_dev_only_scripts' => 'OFF',
                ],
                self::PROFILE_PROD => [
                    'debug_logs' => 'OFF',
                    'public_debug' => 'OFF',
                    'astro_debug_plugins' => 'OFF',
                    'npm_dev_only_scripts' => 'OFF',
                ],
            ],
        ];
    }

    public function isDevLayerEnabled(): bool
    {
        return (bool) ($this->state()['dev_layer_enabled'] ?? false);
    }

    public function canWriteDebugLogs(): bool
    {
        return (bool) ($this->state()['can_write_debug_logs'] ?? false);
    }

    public function isPublicDebugEnabled(): bool
    {
        return (bool) ($this->state()['public_debug_enabled'] ?? false);
    }

    public function runtimeProfile(): string
    {
        return (string) ($this->state()['runtime_profile'] ?? self::PROFILE_DEV);
    }

    public function logLevel(): string
    {
        return (string) ($this->state()['log_level'] ?? 'info');
    }

    /**
     * @return array<string, mixed>
     */
    private function state(): array
    {
        return $this->state ?? config('ercee_dev', []);
    }

    /**
     * @param array<string, mixed> $env
     * @param array<int, array{variable: string, value: string}> $invalid
     */
    private static function resolveProfile(array $env, array &$invalid): string
    {
        $raw = $env['ERCEE_RUNTIME_PROFILE'] ?? null;
        $profile = is_string($raw) ? strtolower(trim($raw)) : null;

        if ($profile !== null && $profile !== '' && in_array($profile, self::ALLOWED_PROFILES, true)) {
            return $profile;
        }

        if ($profile !== null && $profile !== '') {
            $invalid[] = ['variable' => 'ERCEE_RUNTIME_PROFILE', 'value' => $profile];
        }

        $appEnvRaw = $env['APP_ENV'] ?? null;
        $appEnv = is_string($appEnvRaw) ? strtolower(trim($appEnvRaw)) : '';

        return match ($appEnv) {
            'production', 'prod' => self::PROFILE_PROD,
            'stage', 'staging', 'preprod' => self::PROFILE_STAGING,
            default => self::PROFILE_DEV,
        };
    }

    /**
     * @param array<int, array{variable: string, value: string}> $invalid
     */
    private static function normalizeLogLevel(mixed $raw, array &$invalid): ?string
    {
        if (! is_string($raw)) {
            return null;
        }

        $level = strtolower(trim($raw));
        if ($level === '') {
            return null;
        }

        if (! in_array($level, self::ALLOWED_LOG_LEVELS, true)) {
            $invalid[] = ['variable' => 'ERCEE_LOG_LEVEL', 'value' => $level];

            return null;
        }

        return $level;
    }

    /**
     * @param array<int, array{variable: string, value: string}> $invalid
     */
    private static function toBool(
        mixed $raw,
        bool $default,
        string $variable,
        array &$invalid
    ): bool {
        if (is_bool($raw)) {
            return $raw;
        }

        if (is_int($raw)) {
            return $raw === 1;
        }

        if (! is_string($raw)) {
            return $default;
        }

        $value = strtolower(trim($raw));
        if ($value === '') {
            return $default;
        }

        if (in_array($value, ['1', 'true', 'yes', 'on'], true)) {
            return true;
        }

        if (in_array($value, ['0', 'false', 'no', 'off'], true)) {
            return false;
        }

        $invalid[] = ['variable' => $variable, 'value' => $value];

        return $default;
    }
}
