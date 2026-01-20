<?php

namespace App\Domain\Form;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
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

    public function getValidationRules(): array
    {
        $rules = [];

        foreach ($this->schema ?? [] as $field) {
            $fieldRules = [];

            if ($field['required'] ?? false) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            $fieldRules[] = match ($field['type'] ?? 'text') {
                'email' => 'email',
                'number' => 'numeric',
                'url' => 'url',
                default => 'string',
            };

            $rules[$field['name']] = $fieldRules;
        }

        return $rules;
    }
}
