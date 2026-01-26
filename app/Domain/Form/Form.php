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
        'data_options',
    ];

    protected $casts = [
        'schema' => 'array',
        'active' => 'boolean',
        'data_options' => 'array',
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

    public function getSubmitButtonTextAttribute(): ?string
    {
        return $this->data_options['submit_button_text'] ?? null;
    }

    public function setSubmitButtonTextAttribute(?string $value): void
    {
        $this->setDataOption('submit_button_text', $value);
    }

    public function getSuccessTitleAttribute(): ?string
    {
        return $this->data_options['success_title'] ?? null;
    }

    public function setSuccessTitleAttribute(?string $value): void
    {
        $this->setDataOption('success_title', $value);
    }

    public function getSuccessMessageAttribute(): ?string
    {
        return $this->data_options['success_message'] ?? null;
    }

    public function setSuccessMessageAttribute(?string $value): void
    {
        $this->setDataOption('success_message', $value);
    }

    private function setDataOption(string $key, ?string $value): void
    {
        $options = $this->data_options ?? [];

        if ($value === null || $value === '') {
            unset($options[$key]);
        } else {
            $options[$key] = $value;
        }

        $this->attributes['data_options'] = empty($options) ? null : json_encode($options);
    }
}
