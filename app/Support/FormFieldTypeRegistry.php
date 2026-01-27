<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FormFieldTypeRegistry
{
    public static function options(): array
    {
        $path = resource_path('form-field-types.json');

        if (! File::exists($path)) {
            return [];
        }

        $items = File::json($path);

        if (! is_array($items)) {
            return [];
        }

        $options = [];

        foreach ($items as $key => $value) {
            $type = is_int($key) ? $value : $key;

            if (! is_string($type) || $type === '') {
                continue;
            }

            $label = __("form-field-types.{$type}");

            $options[$type] = $label === "form-field-types.{$type}"
                ? Str::headline($type)
                : $label;
        }

        return $options;
    }
}
