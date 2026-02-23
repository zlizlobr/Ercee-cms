<?php

namespace App\Filament\Components;

use Closure;
use Filament\Forms;

/**
 * Builds a reusable icon select field for Filament forms.
 */
class IconPicker
{
    /**
     * @var string State key used to bind the icon select value in form payloads.
     */
    protected string $name;

    /**
     * @var ?string Translated label rendered for the generated field inputs.
     */
    protected ?string $label = null;

    /**
     * @var ?string Placeholder text shown when no value is selected.
     */
    protected ?string $placeholder = null;

    /**
     * @var bool Flag that controls whether users can search available options.
     */
    protected bool $searchable = true;

    /**
     * @var bool Flag that controls whether options are preloaded on render.
     */
    protected bool $preload = true;

    /**
     * Create a new instance of the component.
     * @param string $name
     */
    public function __construct(string $name = 'icon_key')
    {
        $this->name = $name;
    }

    /**
     * Instantiate the picker with the given state key.
     * @param string $name
     */
    public static function make(string $name = 'icon_key'): self
    {
        return new self($name);
    }

    /**
     * Configure the translated label displayed above the picker.
     * @param ?string $label
     */
    public function label(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set the field placeholder.
     * @param ?string $placeholder
     */
    public function placeholder(?string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Enable or disable icon searching in the select dropdown.
     * @param bool $condition
     */
    public function searchable(bool $condition = true): self
    {
        $this->searchable = $condition;

        return $this;
    }

    /**
     * Enable or disable eager loading of icon options in the UI.
     * @param bool $condition
     */
    public function preload(bool $condition = true): self
    {
        $this->preload = $condition;

        return $this;
    }

    /**
     * Get available icon options.
     * @return array<string, string>
     */
    public static function iconOptions(): array
    {
        return [
            'default' => 'Default',
            'check' => 'Check',
            'star' => 'Star',
            'shield' => 'Shield',
            'user' => 'User',
            'mail' => 'Mail',
            'phone' => 'Phone',
            'building' => 'Building',
            'briefcase' => 'Briefcase',
            'calendar' => 'Calendar',
            'file-text' => 'File text',
            'message-square' => 'Message',
            'globe' => 'Globe',
            'map-pin' => 'Map pin',
            'info' => 'Info',
            'check-circle' => 'Check circle',
            'chat' => 'Chat',
            'cog' => 'Settings',
            'support' => 'Support',
            'academic' => 'Academic cap',
        ];
    }

    /**
     * Build the Filament select field instance.
     */
    public function field(): Forms\Components\Select
    {
        $field = Forms\Components\Select::make($this->name)
            ->label($this->label ?? __('admin.page.fields.icon'))
            ->options(static::iconOptions())
            ->placeholder($this->placeholder ?? __('admin.page.fields.icon_placeholder'));

        if ($this->searchable) {
            $field->searchable();
        }

        if ($this->preload) {
            $field->preload();
        }

        return $field;
    }
}

