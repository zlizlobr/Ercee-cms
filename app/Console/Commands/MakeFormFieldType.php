<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeFormFieldType extends Command
{
    protected $signature = 'make:form-field-type
        {type : The type key (e.g. "checkbox_cards" or "datetime-local")}
        {--category= : input|choice|section}
        {--renderer= : input|textarea|select|checkbox|radio|hidden|section|checkbox_cards}
        {--input-type= : HTML input type for renderer=input}
        {--supports= : Comma-separated supports list}
        {--options= : JSON string for options (e.g. {"required":true,"kind":"label_value"})}
        {--label-en= : English label}
        {--description-en= : English description}
        {--label-cs= : Czech label}
        {--description-cs= : Czech description}
        {--patch-frontend : Patch ContactForm.astro with a renderer skeleton}
        {--frontend-path= : Path to the frontend repo (defaults to ../ercee-frontend)}
        {--force : Overwrite existing type}
        {--dry-run : Preview changes without writing files}';

    protected $description = 'Add a new form field type to the JSON registry and translations';

    protected Filesystem $files;
    protected bool $optionsInvalid = false;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle(): int
    {
        $type = $this->normalizeType($this->argument('type'));
        if (! $this->isValidType($type)) {
            $this->error('Type key must contain only a-z, 0-9, "_" or "-".');
            return self::FAILURE;
        }

        $category = $this->resolveCategory();
        $renderer = $this->resolveRenderer();
        $inputType = $this->resolveInputType($renderer);
        $supports = $this->resolveSupports($renderer, $category);
        $options = $this->resolveOptions($supports);
        if ($this->optionsInvalid) {
            return self::FAILURE;
        }

        $labelEn = $this->resolveText('label-en', 'English label');
        $descriptionEn = $this->resolveText('description-en', 'English description');
        $labelCs = $this->resolveText('label-cs', 'Czech label');
        $descriptionCs = $this->resolveText('description-cs', 'Czech description');

        $entry = [
            'category' => $category,
            'label_key' => "{$type}.label",
            'description_key' => "{$type}.description",
            'renderer' => $renderer,
            'supports' => $supports,
            'defaults' => [],
        ];

        if ($renderer === 'input' && $inputType) {
            $entry['input_type'] = $inputType;
        }

        if (is_array($options)) {
            $entry['options'] = $options;
        }

        if (! $this->updateRegistry($type, $entry)) {
            return self::FAILURE;
        }

        if (! $this->updateTranslations('en', $type, $labelEn, $descriptionEn)) {
            return self::FAILURE;
        }

        if (! $this->updateTranslations('cs', $type, $labelCs, $descriptionCs)) {
            return self::FAILURE;
        }

        if ($this->option('patch-frontend')) {
            if (! $this->patchFrontendRenderer($renderer)) {
                return self::FAILURE;
            }
        }

        $this->info("Form field type '{$type}' created.");
        $this->line('Reminder: add/adjust Astro renderer if you introduced a new renderer.');

        return self::SUCCESS;
    }

    protected function normalizeType(string $type): string
    {
        $type = (string) Str::of($type)->trim()->lower();
        return str_replace(' ', '_', $type);
    }

    protected function isValidType(string $type): bool
    {
        return (bool) preg_match('/^[a-z0-9_-]+$/', $type);
    }

    protected function resolveCategory(): string
    {
        $category = $this->option('category');
        if ($category) {
            return $category;
        }

        return $this->choice('Category', ['input', 'choice', 'section'], 'input');
    }

    protected function resolveRenderer(): string
    {
        $renderer = $this->option('renderer');
        if ($renderer) {
            return $renderer;
        }

        return $this->choice(
            'Renderer',
            ['input', 'textarea', 'select', 'checkbox', 'radio', 'hidden', 'section', 'checkbox_cards'],
            'input'
        );
    }

    protected function resolveInputType(string $renderer): ?string
    {
        $inputType = $this->option('input-type');
        if ($renderer !== 'input') {
            return null;
        }

        if ($inputType) {
            return $inputType;
        }

        return $this->ask('Input type', 'text');
    }

    protected function resolveSupports(string $renderer, string $category): array
    {
        $supports = $this->parseSupports($this->option('supports'));
        if (! empty($supports)) {
            return $supports;
        }

        $defaults = $this->defaultSupports($renderer, $category);
        $input = $this->ask('Supports (comma-separated)', implode(',', $defaults));

        return $this->parseSupports($input) ?: $defaults;
    }

    protected function resolveOptions(array $supports): ?array
    {
        $raw = $this->option('options');
        if ($raw) {
            return $this->decodeOptions($raw);
        }

        if (! in_array('options', $supports, true)) {
            return null;
        }

        $input = $this->ask('Options JSON', '{"required": true, "kind": "label_value"}');

        return $this->decodeOptions($input);
    }

    protected function decodeOptions(string $raw): ?array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            $this->error('Options must be valid JSON object.');
            $this->optionsInvalid = true;
            return null;
        }

        return $decoded;
    }

    protected function resolveText(string $option, string $label): string
    {
        $value = $this->option($option);
        if ($value) {
            return $value;
        }

        return $this->ask($label);
    }

    protected function defaultSupports(string $renderer, string $category): array
    {
        $defaultsByRenderer = [
            'input' => ['name', 'required', 'placeholder', 'helper_text'],
            'textarea' => ['name', 'required', 'placeholder', 'helper_text'],
            'select' => ['name', 'required', 'helper_text', 'options'],
            'checkbox' => ['name', 'required', 'helper_text'],
            'radio' => ['name', 'required', 'helper_text', 'options'],
            'hidden' => ['name'],
            'section' => ['name', 'helper_text'],
            'checkbox_cards' => ['name', 'required', 'helper_text', 'options'],
        ];

        if (isset($defaultsByRenderer[$renderer])) {
            return $defaultsByRenderer[$renderer];
        }

        return $category === 'section'
            ? ['name', 'helper_text']
            : ['name', 'required', 'helper_text'];
    }

    protected function parseSupports(?string $supports): array
    {
        if (! $supports) {
            return [];
        }

        $items = array_map('trim', explode(',', $supports));

        return array_values(array_filter($items, static fn ($item) => $item !== ''));
    }

    protected function updateRegistry(string $type, array $entry): bool
    {
        $path = resource_path('form-field-types.json');
        $items = [];

        if ($this->files->exists($path)) {
            $items = $this->files->json($path);
            if (! is_array($items)) {
                $this->error('form-field-types.json is not a valid JSON object.');
                return false;
            }
        }

        if (array_key_exists($type, $items) && ! $this->option('force')) {
            $this->error("Field type '{$type}' already exists. Use --force to overwrite.");
            return false;
        }

        $items[$type] = $entry;

        $encoded = json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($encoded === false) {
            $this->error('Failed to encode form-field-types.json.');
            return false;
        }

        if ($this->option('dry-run')) {
            $this->info("Dry run: would write {$path}");
            return true;
        }

        $this->files->put($path, $encoded . PHP_EOL);

        return true;
    }

    protected function updateTranslations(string $locale, string $type, string $label, string $description): bool
    {
        $path = lang_path("{$locale}/form-field-types.php");
        $items = [];

        if ($this->files->exists($path)) {
            $items = include $path;
            if (! is_array($items)) {
                $this->error("Translation file {$path} is invalid.");
                return false;
            }
        }

        if (array_key_exists($type, $items) && ! $this->option('force')) {
            $this->error("Translation key '{$type}' already exists in {$locale}. Use --force to overwrite.");
            return false;
        }

        $items[$type] = [
            'label' => $label,
            'description' => $description,
        ];

        $content = $this->formatTranslationArray($items);

        if ($this->option('dry-run')) {
            $this->info("Dry run: would write {$path}");
            return true;
        }

        $this->files->put($path, $content);

        return true;
    }

    protected function patchFrontendRenderer(string $renderer): bool
    {
        $frontendPath = $this->resolveFrontendPath();
        if (! $frontendPath) {
            $this->error('Frontend not found. Use --frontend-path to set the repo location.');
            return false;
        }

        $astroPath = $frontendPath . '/src/components/blocks/ContactForm.astro';
        if (! $this->files->exists($astroPath)) {
            $this->error("ContactForm.astro not found at {$astroPath}");
            return false;
        }

        $content = $this->files->get($astroPath);
        if (str_contains($content, "renderer === '{$renderer}'")) {
            $this->info("Renderer '{$renderer}' already present in ContactForm.astro.");
            return true;
        }

        $rendererVar = 'is' . Str::studly($renderer);

        if (! $this->insertRendererGuard($content, $rendererVar, $renderer)) {
            $this->error('Failed to insert renderer guard.');
            return false;
        }

        if (! $this->insertRendererBranch($content, $rendererVar, $renderer)) {
            $this->error('Failed to insert renderer branch.');
            return false;
        }

        if ($this->option('dry-run')) {
            $this->info("Dry run: would write {$astroPath}");
            return true;
        }

        $this->files->put($astroPath, $content);
        $this->info("Patched {$astroPath} with renderer '{$renderer}'.");

        return true;
    }

    protected function resolveFrontendPath(): ?string
    {
        $override = $this->option('frontend-path');
        if ($override) {
            $path = realpath($override);
        } else {
            $path = realpath(base_path('../ercee-frontend'));
        }

        if (! $path || ! $this->files->isDirectory($path)) {
            return null;
        }

        $basePath = realpath(base_path('..'));
        if (! $basePath || ! str_starts_with($path, $basePath)) {
            return null;
        }

        return $path;
    }

    protected function insertRendererGuard(string &$content, string $rendererVar, string $renderer): bool
    {
        $pattern = '/^(\s*)const is[A-Za-z0-9]+ = renderer === \'.+\';/m';
        if (! preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            return false;
        }

        $lastIndex = count($matches[0]) - 1;
        $indent = $matches[1][$lastIndex][0] ?? '';
        $lastMatch = $matches[0][$lastIndex];
        $insertPos = $lastMatch[1] + strlen($lastMatch[0]);

        $line = "{$indent}const {$rendererVar} = renderer === '{$renderer}';";
        $content = substr_replace($content, PHP_EOL . $line, $insertPos, 0);

        return true;
    }

    protected function insertRendererBranch(string &$content, string $rendererVar, string $renderer): bool
    {
        if (! preg_match('/\n(\s*)\) : isRadio \? \(/', $content, $match, PREG_OFFSET_CAPTURE)) {
            return false;
        }

        $indent = $match[1][0];
        $insertPos = $match[0][1];
        $innerIndent = $indent . '  ';
        $blockIndent = $innerIndent . '  ';

        $block = [
            "{$indent}) : {$rendererVar} ? (",
            "{$innerIndent}<div class=\"p-4 border border-dashed border-gray-300 rounded-lg text-sm text-gray-600\">",
            "{$blockIndent}TODO: implement renderer \"{$renderer}\"",
            "{$innerIndent}</div>",
        ];

        $content = substr_replace(
            $content,
            implode(PHP_EOL, $block) . PHP_EOL,
            $insertPos,
            0
        );

        return true;
    }

    protected function formatTranslationArray(array $items): string
    {
        $lines = ['<?php', '', 'return ['];

        foreach ($items as $key => $value) {
            $label = $this->exportString($value['label'] ?? '');
            $description = $this->exportString($value['description'] ?? '');

            $lines[] = "    '{$key}' => [";
            $lines[] = "        'label' => {$label},";
            $lines[] = "        'description' => {$description},";
            $lines[] = '    ],';
        }

        $lines[] = '];';
        $lines[] = '';

        return implode(PHP_EOL, $lines);
    }

    protected function exportString(string $value): string
    {
        $escaped = str_replace(['\\', '\''], ['\\\\', '\\\''], $value);
        return "'{$escaped}'";
    }
}
