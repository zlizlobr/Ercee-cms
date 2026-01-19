<?php

namespace App\Domain\Form;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Form extends Model
{
    use HasFactory;

    public const FIELD_TYPES = ['text', 'email', 'textarea', 'select', 'checkbox'];

    protected $fillable = [
        'name',
        'schema',
        'active',
    ];

    protected $casts = [
        'schema' => 'array',
        'active' => 'boolean',
    ];

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function (Form $form) {
            $form->validateSchema();
        });
    }

    public function validateSchema(): void
    {
        $schema = $this->schema;

        if (!is_array($schema)) {
            throw ValidationException::withMessages([
                'schema' => ['Schema must be an array of fields.'],
            ]);
        }

        foreach ($schema as $index => $field) {
            $validator = Validator::make($field, [
                'name' => 'required|string',
                'type' => 'required|string|in:' . implode(',', self::FIELD_TYPES),
                'label' => 'required|string',
                'required' => 'boolean',
                'options' => 'array',
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages([
                    "schema.{$index}" => $validator->errors()->all(),
                ]);
            }

            if ($field['type'] === 'select' && empty($field['options'])) {
                throw ValidationException::withMessages([
                    "schema.{$index}.options" => ['Select field must have options.'],
                ]);
            }
        }
    }

    public function getValidationRules(): array
    {
        $rules = [];

        foreach ($this->schema as $field) {
            $fieldRules = [];

            if (!empty($field['required'])) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            switch ($field['type']) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'checkbox':
                    $fieldRules[] = 'boolean';
                    break;
                case 'select':
                    $options = array_column($field['options'], 'value');
                    $fieldRules[] = 'in:' . implode(',', $options);
                    break;
                case 'text':
                case 'textarea':
                    $fieldRules[] = 'string';
                    break;
            }

            $rules[$field['name']] = $fieldRules;
        }

        return $rules;
    }
}
