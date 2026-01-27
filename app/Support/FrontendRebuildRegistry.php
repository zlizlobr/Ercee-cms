<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class FrontendRebuildRegistry
{
    protected static ?array $cache = null;

    public static function rules(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        return self::$cache = config('frontend-rebuild.resources', []);
    }

    /**
     * @return array<int, string>
     */
    public static function reasonsFor(Model $model, string $event): array
    {
        $modelClass = $model::class;
        $reasons = [];

        foreach (self::rules() as $resourceClass => $resourceRules) {
            $resourceModel = $resourceRules['model'] ?? null;

            if (! is_string($resourceModel) || $resourceModel !== $modelClass) {
                continue;
            }

            $eventRule = $resourceRules['events'][$event] ?? null;

            if (! is_array($eventRule)) {
                continue;
            }

            if (! self::passesCondition($model, $eventRule)) {
                continue;
            }

            $reason = self::resolveReason($model, $eventRule['reason'] ?? null);

            if ($reason) {
                $reasons[] = $reason;
            }
        }

        return $reasons;
    }

    protected static function passesCondition(Model $model, array $rule): bool
    {
        $condition = $rule['condition'] ?? null;

        if ($condition === null) {
            return true;
        }

        if (! is_array($condition)) {
            return (bool) $condition;
        }

        $method = $condition['method'] ?? null;

        if (! is_string($method) || ! method_exists($model, $method)) {
            return false;
        }

        $value = $model->{$method}();

        if (array_key_exists('equals', $condition)) {
            return $value === $condition['equals'];
        }

        return (bool) $value;
    }

    protected static function resolveReason(Model $model, ?string $reason): ?string
    {
        if (! $reason) {
            return null;
        }

        return preg_replace_callback(
            '/\{([A-Za-z0-9_.-]+)\}/',
            function (array $matches) use ($model): string {
                $value = data_get($model, $matches[1]);

                if ($value === null || ! is_scalar($value)) {
                    return $matches[0];
                }

                return (string) $value;
            },
            $reason
        );
    }
}
