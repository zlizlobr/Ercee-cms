<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FormFieldTypeRegistry
{
    protected static ?array $cache = null;

    public static function options(): array
    {
        $options = [];

        foreach (self::all() as $type => $meta) {
            $options[$type] = $meta['label'];
        }

        return $options;
    }

    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $path = resource_path('form-field-types.json');

        if (! File::exists($path)) {
            return self::$cache = [];
        }

        $items = File::json($path);

        if (! is_array($items)) {
            return self::$cache = [];
        }

        $normalized = [];

        foreach ($items as $key => $value) {
            $type = is_int($key) ? $value : $key;

            if (! is_string($type) || $type === '') {
                continue;
            }

            $meta = is_array($value) ? $value : [];
            $labelKey = $meta['label_key'] ?? $type;
            $descriptionKey = $meta['description_key'] ?? $type;

            $label = __("form-field-types.{$labelKey}");
            $description = __("form-field-types.{$descriptionKey}");

            $normalized[$type] = [
                'label' => $label === "form-field-types.{$labelKey}" ? Str::headline($type) : $label,
                'description' => $description === "form-field-types.{$descriptionKey}" ? null : $description,
                'category' => $meta['category'] ?? null,
                'supports' => array_values(array_filter($meta['supports'] ?? [])),
                'options' => is_array($meta['options'] ?? null) ? $meta['options'] : null,
                'defaults' => is_array($meta['defaults'] ?? null) ? $meta['defaults'] : [],
            ];
        }

        return self::$cache = $normalized;
    }

    public static function supports(?string $type, string $feature): bool
    {
        if (! $type) {
            return false;
        }

        $meta = self::all()[$type] ?? null;

        if (! $meta) {
            return false;
        }

        return in_array($feature, $meta['supports'], true);
    }

    public static function description(?string $type): ?string
    {
        if (! $type) {
            return null;
        }

        $meta = self::all()[$type] ?? null;

        return $meta['description'] ?? null;
    }
}
