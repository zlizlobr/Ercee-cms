<?php

namespace App\Filament\Components;

use Closure;
use Filament\Forms;

class IconPicker
{
    protected string $name;

    protected ?string $label = null;

    protected ?string $placeholder = null;

    protected bool $searchable = true;

    protected bool $preload = true;

    public function __construct(string $name = 'icon_key')
    {
        $this->name = $name;
    }

    public static function make(string $name = 'icon_key'): self
    {
        return new self($name);
    }

    public function label(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function placeholder(?string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function searchable(bool $condition = true): self
    {
        $this->searchable = $condition;

        return $this;
    }

    public function preload(bool $condition = true): self
    {
        $this->preload = $condition;

        return $this;
    }

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
