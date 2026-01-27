<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class FormIconRegistry
{
    public static function options(): array
    {
        $path = resource_path('form-icons.json');

        if (! File::exists($path)) {
            return [];
        }

        $icons = File::json($path);

        return is_array($icons) ? $icons : [];
    }
}
