<?php

namespace App\Filament\Components;

use App\Domain\Content\Page;
use Closure;
use Filament\Forms;

class LinkPicker
{
    protected string $name;

    protected ?string $label = null;

    protected bool $withLinkType = false;

    protected bool $withAnchor = true;

    protected bool $withTarget = false;

    protected bool $isOverride = false;

    protected ?string $defaultUrl = null;

    protected ?Closure $pagesQuery = null;

    public function __construct(string $name = '')
    {
        $this->name = $name;
    }

    public static function make(string $name = ''): self
    {
        return new self($name);
    }

    public function label(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function withLinkType(bool $condition = true): self
    {
        $this->withLinkType = $condition;

        return $this;
    }

    public function withAnchor(bool $condition = true): self
    {
        $this->withAnchor = $condition;

        return $this;
    }

    public function withoutAnchor(): self
    {
        return $this->withAnchor(false);
    }

    public function withTarget(bool $condition = true): self
    {
        $this->withTarget = $condition;

        return $this;
    }

    public function isOverride(bool $condition = true): self
    {
        $this->isOverride = $condition;

        return $this;
    }

    public function defaultUrl(?string $url): self
    {
        $this->defaultUrl = $url;

        return $this;
    }

    public function pagesQuery(?Closure $query): self
    {
        $this->pagesQuery = $query;

        return $this;
    }

    protected function fieldName(string $field): string
    {
        return $this->name ? "{$this->name}.{$field}" : $field;
    }

    protected function fieldLabel(string $suffix): string
    {
        $prefix = $this->label ?? '';

        return $prefix ? "{$prefix} {$suffix}" : $suffix;
    }

    protected function getPageOptions(): Closure|array
    {
        if ($this->pagesQuery) {
            return $this->pagesQuery;
        }

        return fn () => Page::where('status', 'published')
            ->get()
            ->mapWithKeys(fn ($page) => [$page->id => $page->getLocalizedTitle()]);
    }

    public function fields(): array
    {
        $fields = [];

        if ($this->withLinkType) {
            $fields[] = $this->buildLinkTypeField();
        }

        $fields[] = $this->buildPageIdField();
        $fields[] = $this->buildUrlField();

        if ($this->withAnchor) {
            $fields[] = $this->buildAnchorField();
        }

        if ($this->withTarget) {
            $fields[] = $this->buildTargetField();
        }

        return $fields;
    }

    protected function buildLinkTypeField(): Forms\Components\Select
    {
        $linkTypeKey = $this->fieldName('link_type');

        $field = Forms\Components\Select::make($linkTypeKey)
            ->label($this->fieldLabel(__('admin.link_picker.link_type')))
            ->options([
                'url' => __('admin.link_picker.type_url'),
                'page' => __('admin.link_picker.type_page'),
            ])
            ->live();

        if ($this->isOverride) {
            $field->placeholder(__('admin.link_picker.use_global'));
        } else {
            $field->default('url');
        }

        return $field;
    }

    protected function buildPageIdField(): Forms\Components\Select
    {
        $field = Forms\Components\Select::make($this->fieldName('page_id'))
            ->label($this->fieldLabel(__('admin.link_picker.page')))
            ->options($this->getPageOptions())
            ->searchable()
            ->placeholder(__('admin.link_picker.page_placeholder'));

        if ($this->withLinkType) {
            $linkTypeKey = $this->fieldName('link_type');
            $field->visible(fn (Forms\Get $get) => $get($linkTypeKey) === 'page');
        } else {
            $field->helperText(__('admin.link_picker.page_helper'));
        }

        return $field;
    }

    protected function buildUrlField(): Forms\Components\TextInput
    {
        $field = Forms\Components\TextInput::make($this->fieldName('url'))
            ->label($this->fieldLabel(__('admin.link_picker.url')))
            ->placeholder(__('admin.link_picker.url_placeholder'))
            ->helperText(__('admin.link_picker.url_helper'));

        if ($this->defaultUrl !== null) {
            $field->default($this->defaultUrl);
        }

        if ($this->isOverride && ! $this->defaultUrl) {
            $field->placeholder(__('admin.link_picker.use_global'));
        }

        if ($this->withLinkType) {
            $linkTypeKey = $this->fieldName('link_type');
            $field->visible(fn (Forms\Get $get) => in_array($get($linkTypeKey), [null, 'url']));
        }

        return $field;
    }

    protected function buildAnchorField(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make($this->fieldName('anchor'))
            ->label($this->fieldLabel(__('admin.link_picker.anchor')))
            ->placeholder(__('admin.link_picker.anchor_placeholder'));
    }

    protected function buildTargetField(): Forms\Components\Select
    {
        return Forms\Components\Select::make($this->fieldName('target'))
            ->label($this->fieldLabel(__('admin.link_picker.target')))
            ->options([
                '_self' => __('admin.link_picker.target_self'),
                '_blank' => __('admin.link_picker.target_blank'),
            ])
            ->default('_self');
    }
}
