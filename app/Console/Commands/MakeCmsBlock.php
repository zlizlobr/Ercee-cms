<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

/**
 * Generate CMS block files for backend and frontend integration.
 */
class MakeCmsBlock extends Command
{
    protected $signature = 'make:cms-block
        {name : The name of the block (e.g. "Hero Banner" or "hero_banner")}
        {schema? : JSON schema string}
        {--schema-file= : Path to JSON schema file}
        {--force : Overwrite existing files}
        {--dry-run : Preview changes without writing files}';

    protected $description = 'Generate a CMS block with Filament form, Astro component, and all related files';

    /**
     * @var Filesystem Paths to generated files created by the command workflow.
     */
    protected Filesystem $files;

    /** @var array<string, mixed> */
    /**
     * @var array JSON schema definition used to validate generated block data.
     */
    protected array $schema;

    /**
     * @var string Human-readable block name provided as command input.
     */
    protected string $blockName;

    /**
     * @var string Normalized block type key stored in content payloads.
     */
    protected string $blockType;

    /**
     * @var string PHP class name generated for the new CMS block.
     */
    protected string $className;

    /**
     * @var string Frontend component name resolved for block rendering.
     */
    protected string $componentName;

    /**
     * @var string Filesystem path to the corresponding Astro component file.
     */
    protected string $astroPath;

    /** @var array<int, array{path: string, description: string}> */
    /**
     * @var array List of files newly created during command execution.
     */
    protected array $createdFiles = [];

    /** @var array<int, string> */
    /**
     * @var array List of existing files changed during command execution.
     */
    protected array $modifiedFiles = [];

    protected const FIELD_MAPPING = [
        'text' => 'TextInput',
        'textarea' => 'Textarea',
        'richtext' => 'RichEditor',
        'markdown' => 'MarkdownEditor',
        'select' => 'Select',
        'checkbox' => 'Checkbox',
        'toggle' => 'Toggle',
        'checkbox_list' => 'CheckboxList',
        'radio' => 'Radio',
        'datetime' => 'DateTimePicker',
        'file' => 'FileUpload',
        'repeater' => 'Repeater',
        'builder' => 'Builder',
        'tags' => 'TagsInput',
        'key_value' => 'KeyValue',
        'color' => 'ColorPicker',
        'toggle_buttons' => 'ToggleButtons',
        'hidden' => 'Hidden',
    ];

    /**
     * Create a new command instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute block scaffolding for CMS and Astro.
     *
     * @return int Exit code (`Command::SUCCESS` or `Command::FAILURE`).
     */
    public function handle(): int
    {
        $this->blockName = $this->argument('name');
        $this->normalizeBlockName();

        if (! $this->loadAndValidateSchema()) {
            return self::FAILURE;
        }

        $this->astroPath = $this->resolveAstroPath();
        if (! $this->astroPath) {
            $this->error('Astro frontend not found at ../ercee-frontend');
            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            $this->info('Dry run mode - no files will be written');
            $this->newLine();
        }

        $this->generateCmsBlock();
        $this->generateBlockPreview();
        $this->updatePageConstants();
        $this->updateLocalizations();
        $this->generateAstroComponent();
        $this->updateAstroTypes();
        $this->updateAstroRegistry();

        $this->printSummary();

        return self::SUCCESS;
    }

    /**
     * Normalize input block name into naming variants.
     */
    protected function normalizeBlockName(): void
    {
        $name = $this->blockName;

        // Convert to snake_case for block type
        $this->blockType = Str::snake(Str::replace(' ', '', $name));

        // Convert to PascalCase for class names
        $this->className = Str::studly(Str::replace(' ', '', $name)) . 'Block';

        // Convert to PascalCase for Astro component (without Block suffix)
        $this->componentName = Str::studly(Str::replace(' ', '', $name));
    }

    /**
     * Load and validate the input schema.
     *
     * @return bool True when schema is valid and loaded.
     */
    protected function loadAndValidateSchema(): bool
    {
        $schemaJson = $this->argument('schema');
        $schemaFile = $this->option('schema-file');

        if ($schemaFile) {
            if (! $this->files->exists($schemaFile)) {
                $this->error("Schema file not found: {$schemaFile}");
                return false;
            }
            $schemaJson = $this->files->get($schemaFile);
        }

        if (! $schemaJson) {
            $this->error('No schema provided. Use {schema} argument or --schema-file option.');
            return false;
        }

        $this->schema = json_decode($schemaJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON schema: ' . json_last_error_msg());
            return false;
        }

        if (! isset($this->schema['label'])) {
            $this->error('Schema must contain "label" key.');
            return false;
        }

        if (! isset($this->schema['fields']) || ! is_array($this->schema['fields'])) {
            $this->error('Schema must contain "fields" array.');
            return false;
        }

        foreach ($this->schema['fields'] as $index => $field) {
            if (! isset($field['type'])) {
                $this->error("Field at index {$index} missing 'type'.");
                return false;
            }
            if (! isset($field['name'])) {
                $this->error("Field at index {$index} missing 'name'.");
                return false;
            }
            if (! isset(self::FIELD_MAPPING[$field['type']])) {
                $this->error("Unsupported field type: {$field['type']}");
                return false;
            }
        }

        return true;
    }

    /**
     * Resolve the frontend Astro project path.
     *
     * @return string|null Absolute Astro path, or null when unavailable.
     */
    protected function resolveAstroPath(): ?string
    {
        $path = realpath(base_path('../ercee-frontend'));

        if (! $path || ! $this->files->isDirectory($path)) {
            return null;
        }

        // Safety check - must be within expected parent directory
        $basePath = realpath(base_path('..'));
        if (! str_starts_with($path, $basePath)) {
            return null;
        }

        return $path;
    }

    /**
     * Generate the Filament block class file.
     */
    protected function generateCmsBlock(): void
    {
        $path = app_path("Filament/Blocks/{$this->className}.php");

        if ($this->files->exists($path) && ! $this->option('force')) {
            $this->warn("Block class already exists: {$path}");
            return;
        }

        $stub = $this->getBlockClassStub();
        $content = $this->populateBlockStub($stub);

        $this->writeFile($path, $content, 'CMS Block class');
    }

    /**
     * Get the block class stub content.
     */
    protected function getBlockClassStub(): string
    {
        $stubPath = resource_path('stubs/blocks/block-class.stub');

        if ($this->files->exists($stubPath)) {
            return $this->files->get($stubPath);
        }

        return <<<'STUB'
<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class {{ className }} extends BaseBlock
{
    /**
     * @var int Display order used when registering generated blocks.
     */
    public static int $order = {{ order }};

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_{{ constantName }})
            ->label(__('admin.page.blocks.{{ blockType }}'))
            ->icon('{{ icon }}')
            ->columns(2)
            ->schema([
{{ schemaFields }}
            ]);
    }
}
STUB;
    }

    /**
     * Replace placeholders in the block class stub.
     *
     * @param string $stub Raw stub content.
     * @return string Stub content with generated values.
     */
    protected function populateBlockStub(string $stub): string
    {
        $schemaFields = $this->generateFilamentSchema();
        $order = $this->schema['order'] ?? 100;
        $icon = $this->schema['icon'] ?? 'heroicon-o-cube';
        $constantName = Str::upper($this->blockType);

        return str_replace(
            ['{{ className }}', '{{ order }}', '{{ constantName }}', '{{ blockType }}', '{{ icon }}', '{{ schemaFields }}'],
            [$this->className, $order, $constantName, $this->blockType, $icon, $schemaFields],
            $stub
        );
    }

    /**
     * Generate Filament schema code from schema fields.
     *
     * @return string PHP schema snippet for block form fields.
     */
    protected function generateFilamentSchema(): string
    {
        $lines = [];

        foreach ($this->schema['fields'] as $field) {
            $lines[] = $this->generateFieldCode($field);
        }

        return implode("\n", $lines);
    }

    /**
     * Generate Filament code for a single field definition.
     *
     * @param array<string, mixed> $field Schema field definition.
     * @param int $indent Base indentation level.
     * @return string Generated PHP code for a single field.
     */
    protected function generateFieldCode(array $field, int $indent = 4): string
    {
        $component = self::FIELD_MAPPING[$field['type']];
        $name = $field['name'];
        $spaces = str_repeat('    ', $indent);
        $chainSpaces = str_repeat('    ', $indent + 1);

        $code = "{$spaces}Forms\\Components\\{$component}::make('{$name}')";
        $code .= "\n{$chainSpaces}->label(__('admin.page.fields.{$name}'))";

        // Common validation
        if (! empty($field['required'])) {
            $code .= "\n{$chainSpaces}->required()";
        }

        // TextInput specific
        if ($field['type'] === 'text') {
            if (! empty($field['email'])) {
                $code .= "\n{$chainSpaces}->email()";
            }
            if (! empty($field['url'])) {
                $code .= "\n{$chainSpaces}->url()";
            }
            if (! empty($field['tel'])) {
                $code .= "\n{$chainSpaces}->tel()";
            }
            if (! empty($field['numeric'])) {
                $code .= "\n{$chainSpaces}->numeric()";
            }
            if (! empty($field['password'])) {
                $code .= "\n{$chainSpaces}->password()";
            }
            if (isset($field['minLength'])) {
                $code .= "\n{$chainSpaces}->minLength({$field['minLength']})";
            }
            if (isset($field['maxLength'])) {
                $code .= "\n{$chainSpaces}->maxLength({$field['maxLength']})";
            }
            if (isset($field['minValue'])) {
                $code .= "\n{$chainSpaces}->minValue({$field['minValue']})";
            }
            if (isset($field['maxValue'])) {
                $code .= "\n{$chainSpaces}->maxValue({$field['maxValue']})";
            }
            if (isset($field['step'])) {
                $code .= "\n{$chainSpaces}->step({$field['step']})";
            }
            if (isset($field['prefix'])) {
                $code .= "\n{$chainSpaces}->prefix('{$field['prefix']}')";
            }
            if (isset($field['suffix'])) {
                $code .= "\n{$chainSpaces}->suffix('{$field['suffix']}')";
            }
            if (isset($field['prefixIcon'])) {
                $code .= "\n{$chainSpaces}->prefixIcon('{$field['prefixIcon']}')";
            }
            if (isset($field['suffixIcon'])) {
                $code .= "\n{$chainSpaces}->suffixIcon('{$field['suffixIcon']}')";
            }
            if (isset($field['placeholder'])) {
                $code .= "\n{$chainSpaces}->placeholder('{$field['placeholder']}')";
            }
            if (isset($field['mask'])) {
                $code .= "\n{$chainSpaces}->mask('{$field['mask']}')";
            }
            if (isset($field['autocomplete'])) {
                $code .= "\n{$chainSpaces}->autocomplete('{$field['autocomplete']}')";
            }
            if (isset($field['datalist'])) {
                $datalistCode = $this->formatSimpleArray($field['datalist']);
                $code .= "\n{$chainSpaces}->datalist({$datalistCode})";
            }
        }

        // Textarea specific
        if ($field['type'] === 'textarea') {
            if (isset($field['rows'])) {
                $code .= "\n{$chainSpaces}->rows({$field['rows']})";
            }
            if (isset($field['cols'])) {
                $code .= "\n{$chainSpaces}->cols({$field['cols']})";
            }
            if (isset($field['minLength'])) {
                $code .= "\n{$chainSpaces}->minLength({$field['minLength']})";
            }
            if (isset($field['maxLength'])) {
                $code .= "\n{$chainSpaces}->maxLength({$field['maxLength']})";
            }
            if (isset($field['placeholder'])) {
                $code .= "\n{$chainSpaces}->placeholder('{$field['placeholder']}')";
            }
            if (! empty($field['autosize'])) {
                $code .= "\n{$chainSpaces}->autosize()";
            }
        }

        // Select specific
        if ($field['type'] === 'select') {
            if (isset($field['options'])) {
                $optionsCode = $this->formatOptionsArray($field['options']);
                $code .= "\n{$chainSpaces}->options({$optionsCode})";
            }
            if (! empty($field['multiple'])) {
                $code .= "\n{$chainSpaces}->multiple()";
            }
            if (! empty($field['searchable'])) {
                $code .= "\n{$chainSpaces}->searchable()";
            }
            if (! empty($field['preload'])) {
                $code .= "\n{$chainSpaces}->preload()";
            }
            if (isset($field['native']) && $field['native'] === false) {
                $code .= "\n{$chainSpaces}->native(false)";
            }
            if (isset($field['optionsLimit'])) {
                $code .= "\n{$chainSpaces}->optionsLimit({$field['optionsLimit']})";
            }
            if (isset($field['placeholder'])) {
                $code .= "\n{$chainSpaces}->placeholder('{$field['placeholder']}')";
            }
        }

        // Radio/CheckboxList/ToggleButtons specific
        if (in_array($field['type'], ['radio', 'checkbox_list', 'toggle_buttons'])) {
            if (isset($field['options'])) {
                $optionsCode = $this->formatOptionsArray($field['options']);
                $code .= "\n{$chainSpaces}->options({$optionsCode})";
            }
            if (! empty($field['inline'])) {
                $code .= "\n{$chainSpaces}->inline()";
            }
            if (isset($field['columns'])) {
                $code .= "\n{$chainSpaces}->columns({$field['columns']})";
            }
        }

        // FileUpload specific
        if ($field['type'] === 'file') {
            if (! empty($field['image'])) {
                $code .= "\n{$chainSpaces}->image()";
            }
            if (! empty($field['multiple'])) {
                $code .= "\n{$chainSpaces}->multiple()";
            }
            $directory = $field['directory'] ?? "blocks/{$this->blockType}";
            $code .= "\n{$chainSpaces}->directory('{$directory}')";
            if (isset($field['disk'])) {
                $code .= "\n{$chainSpaces}->disk('{$field['disk']}')";
            }
            if (isset($field['visibility'])) {
                $code .= "\n{$chainSpaces}->visibility('{$field['visibility']}')";
            }
            if (isset($field['acceptedFileTypes'])) {
                $typesCode = $this->formatSimpleArray($field['acceptedFileTypes']);
                $code .= "\n{$chainSpaces}->acceptedFileTypes({$typesCode})";
            }
            if (isset($field['minSize'])) {
                $code .= "\n{$chainSpaces}->minSize({$field['minSize']})";
            }
            if (isset($field['maxSize'])) {
                $code .= "\n{$chainSpaces}->maxSize({$field['maxSize']})";
            }
            if (isset($field['maxFiles'])) {
                $code .= "\n{$chainSpaces}->maxFiles({$field['maxFiles']})";
            }
            if (! empty($field['imageEditor'])) {
                $code .= "\n{$chainSpaces}->imageEditor()";
            }
            if (isset($field['imageCropAspectRatio'])) {
                $code .= "\n{$chainSpaces}->imageCropAspectRatio('{$field['imageCropAspectRatio']}')";
            }
            if (isset($field['imageResizeTargetWidth'])) {
                $code .= "\n{$chainSpaces}->imageResizeTargetWidth({$field['imageResizeTargetWidth']})";
            }
            if (isset($field['imageResizeTargetHeight'])) {
                $code .= "\n{$chainSpaces}->imageResizeTargetHeight({$field['imageResizeTargetHeight']})";
            }
            if (! empty($field['reorderable'])) {
                $code .= "\n{$chainSpaces}->reorderable()";
            }
            if (! empty($field['downloadable'])) {
                $code .= "\n{$chainSpaces}->downloadable()";
            }
            if (! empty($field['openable'])) {
                $code .= "\n{$chainSpaces}->openable()";
            }
            if (! empty($field['previewable']) || ! isset($field['previewable'])) {
                // previewable is true by default
            } elseif (isset($field['previewable']) && $field['previewable'] === false) {
                $code .= "\n{$chainSpaces}->previewable(false)";
            }
        }

        // DateTimePicker specific
        if ($field['type'] === 'datetime') {
            if (isset($field['format'])) {
                $code .= "\n{$chainSpaces}->format('{$field['format']}')";
            }
            if (isset($field['displayFormat'])) {
                $code .= "\n{$chainSpaces}->displayFormat('{$field['displayFormat']}')";
            }
            if (! empty($field['native'])) {
                $code .= "\n{$chainSpaces}->native()";
            }
            if (! empty($field['seconds'])) {
                $code .= "\n{$chainSpaces}->seconds()";
            }
            if (isset($field['minDate'])) {
                $code .= "\n{$chainSpaces}->minDate('{$field['minDate']}')";
            }
            if (isset($field['maxDate'])) {
                $code .= "\n{$chainSpaces}->maxDate('{$field['maxDate']}')";
            }
            if (isset($field['timezone'])) {
                $code .= "\n{$chainSpaces}->timezone('{$field['timezone']}')";
            }
        }

        // ColorPicker specific
        if ($field['type'] === 'color') {
            if (isset($field['format'])) {
                $format = $field['format'];
                $code .= "\n{$chainSpaces}->{$format}()";
            }
        }

        // Tags specific
        if ($field['type'] === 'tags') {
            if (isset($field['separator'])) {
                $code .= "\n{$chainSpaces}->separator('{$field['separator']}')";
            }
            if (isset($field['suggestions'])) {
                $suggestionsCode = $this->formatSimpleArray($field['suggestions']);
                $code .= "\n{$chainSpaces}->suggestions({$suggestionsCode})";
            }
        }

        // Repeater specific
        if ($field['type'] === 'repeater' && isset($field['schema'])) {
            $nestedSchema = $this->generateNestedSchema($field['schema'], $indent + 1);
            $code .= "\n{$chainSpaces}->schema([{$nestedSchema}\n{$chainSpaces}])";

            if (isset($field['columns'])) {
                $code .= "\n{$chainSpaces}->columns({$field['columns']})";
            }
            if (isset($field['defaultItems'])) {
                $code .= "\n{$chainSpaces}->defaultItems({$field['defaultItems']})";
            }
            if (isset($field['minItems'])) {
                $code .= "\n{$chainSpaces}->minItems({$field['minItems']})";
            }
            if (isset($field['maxItems'])) {
                $code .= "\n{$chainSpaces}->maxItems({$field['maxItems']})";
            }
            if (! empty($field['collapsible'])) {
                $code .= "\n{$chainSpaces}->collapsible()";
            }
            if (! empty($field['collapsed'])) {
                $code .= "\n{$chainSpaces}->collapsed()";
            }
            if (! empty($field['cloneable'])) {
                $code .= "\n{$chainSpaces}->cloneable()";
            }
            if (isset($field['reorderable']) && $field['reorderable'] === false) {
                $code .= "\n{$chainSpaces}->reorderable(false)";
            }
            if (isset($field['addable']) && $field['addable'] === false) {
                $code .= "\n{$chainSpaces}->addable(false)";
            }
            if (isset($field['deletable']) && $field['deletable'] === false) {
                $code .= "\n{$chainSpaces}->deletable(false)";
            }
            if (isset($field['grid'])) {
                $code .= "\n{$chainSpaces}->grid({$field['grid']})";
            }
            if (isset($field['itemLabel'])) {
                $code .= "\n{$chainSpaces}->itemLabel(fn (array \$state): ?string => \$state['{$field['itemLabel']}'] ?? null)";
            }
            if (! empty($field['simple'])) {
                $code .= "\n{$chainSpaces}->simple()";
            }
        }

        // Common options for all fields
        if (isset($field['default'])) {
            $defaultValue = is_string($field['default']) ? "'{$field['default']}'" : $field['default'];
            if (is_bool($field['default'])) {
                $defaultValue = $field['default'] ? 'true' : 'false';
            }
            $code .= "\n{$chainSpaces}->default({$defaultValue})";
        }

        if (! empty($field['disabled'])) {
            $code .= "\n{$chainSpaces}->disabled()";
        }

        if (! empty($field['hidden'])) {
            $code .= "\n{$chainSpaces}->hidden()";
        }

        if (isset($field['helperText'])) {
            $code .= "\n{$chainSpaces}->helperText('{$field['helperText']}')";
        }

        if (isset($field['hint'])) {
            $code .= "\n{$chainSpaces}->hint('{$field['hint']}')";
        }

        if (isset($field['hintIcon'])) {
            $code .= "\n{$chainSpaces}->hintIcon('{$field['hintIcon']}')";
        }

        if (! empty($field['columnSpanFull'])) {
            $code .= "\n{$chainSpaces}->columnSpanFull()";
        } elseif (isset($field['columnSpan'])) {
            $code .= "\n{$chainSpaces}->columnSpan({$field['columnSpan']})";
        }

        $code .= ',';

        return $code;
    }

    /**
     * Generate nested schema code for repeater fields.
     *
     * @param array<int, array<string, mixed>> $fields Nested repeater fields.
     * @param int $indent Base indentation level.
     * @return string Generated nested schema code.
     */
    protected function generateNestedSchema(array $fields, int $indent): string
    {
        $lines = [];

        foreach ($fields as $field) {
            $lines[] = "\n" . $this->generateFieldCode($field, $indent);
        }

        return implode('', $lines);
    }

    /**
     * Format a flat array into inline PHP code.
     *
     * @param array<int, scalar> $items List of scalar values.
     * @return string Inline PHP array expression.
     */
    protected function formatSimpleArray(array $items): string
    {
        $values = array_map(fn ($v) => "'{$v}'", $items);
        return '[' . implode(', ', $values) . ']';
    }

    /**
     * Format key-value options into inline PHP code.
     *
     * @param array<int|string, scalar> $options Options keyed by value or label.
     * @return string Inline PHP associative array expression.
     */
    protected function formatOptionsArray(array $options): string
    {
        $items = [];
        foreach ($options as $key => $value) {
            if (is_numeric($key)) {
                $items[] = "'{$value}' => '{$value}'";
            } else {
                $items[] = "'{$key}' => '{$value}'";
            }
        }
        return '[' . implode(', ', $items) . ']';
    }

    /**
     * Generate Blade preview for the block.
     */
    protected function generateBlockPreview(): void
    {
        $path = resource_path("views/components/blocks/{$this->blockType}.blade.php");

        if ($this->files->exists($path) && ! $this->option('force')) {
            $this->warn("Preview template already exists: {$path}");
            return;
        }

        $stub = $this->getBlockPreviewStub();
        $content = str_replace(
            ['{{ blockType }}', '{{ label }}'],
            [$this->blockType, $this->schema['label']],
            $stub
        );

        $this->ensureDirectory(dirname($path));
        $this->writeFile($path, $content, 'Block preview template');
    }

    /**
     * Get the block preview stub content.
     */
    protected function getBlockPreviewStub(): string
    {
        $stubPath = resource_path('stubs/blocks/block-preview.stub');

        if ($this->files->exists($stubPath)) {
            return $this->files->get($stubPath);
        }

        return <<<'STUB'
<div class="p-4 border rounded-lg bg-gray-50">
    <div class="text-sm font-medium text-gray-500 mb-2">{{ label }}</div>
    <div class="space-y-2">
        @foreach($data ?? [] as $key => $value)
            <div class="text-sm">
                <span class="font-medium">{{ $key }}:</span>
                <span>{{ is_array($value) ? json_encode($value) : $value }}</span>
            </div>
        @endforeach
    </div>
</div>
STUB;
    }

    /**
     * Add block constants to the page domain model.
     */
    protected function updatePageConstants(): void
    {
        $path = app_path('Domain/Content/Page.php');
        $content = $this->files->get($path);
        $constantName = Str::upper($this->blockType);

        // Check if constant already exists
        if (str_contains($content, "BLOCK_TYPE_{$constantName}")) {
            $this->warn("Constant BLOCK_TYPE_{$constantName} already exists in Page.php");
            return;
        }

        $newConstant = "public const BLOCK_TYPE_{$constantName} = '{$this->blockType}';";

        // Insert new constant after the last BLOCK_TYPE constant
        $pattern = '/(public const BLOCK_TYPE_IMAGE = \'image\';)/';
        $content = preg_replace(
            $pattern,
            "$1\n\n    {$newConstant}",
            $content
        );

        // Add to blockTypes() method - insert before closing bracket
        $newEntry = "            self::BLOCK_TYPE_{$constantName} => __('admin.page.blocks.{$this->blockType}'),";
        $pattern = '/(self::BLOCK_TYPE_IMAGE => __\(\'admin\.page\.blocks\.image\'\),)(\s*\];)/';
        $content = preg_replace(
            $pattern,
            "$1\n{$newEntry}$2",
            $content
        );

        $this->writeFile($path, $content, 'Page.php constants');
        $this->modifiedFiles[] = $path;
    }

    /**
     * Add block and field labels to localization files.
     */
    protected function updateLocalizations(): void
    {
        $locales = ['en', 'cs'];
        $translations = [
            'en' => [
                'block_label' => $this->schema['label'],
                'fields' => $this->getEnglishFieldLabels(),
            ],
            'cs' => [
                'block_label' => $this->schema['label_cs'] ?? $this->schema['label'],
                'fields' => $this->getCzechFieldLabels(),
            ],
        ];

        foreach ($locales as $locale) {
            $path = lang_path("{$locale}/admin.php");
            if (! $this->files->exists($path)) {
                continue;
            }

            $content = $this->files->get($path);

            // Add block label
            $blockLabel = $translations[$locale]['block_label'];
            $blockEntry = "            '{$this->blockType}' => '{$blockLabel}',";

            // Find the blocks array and insert
            $blocksPattern = "/('blocks' => \[)([^\]]*)(\'new\' => '[^']*',\s*\])/s";
            if (preg_match($blocksPattern, $content)) {
                $content = preg_replace(
                    $blocksPattern,
                    "$1$2{$blockEntry}\n            $3",
                    $content
                );
            }

            // Add field translations
            foreach ($translations[$locale]['fields'] as $fieldName => $fieldLabel) {
                if (str_contains($content, "'{$fieldName}' =>")) {
                    continue; // Field already exists
                }

                $fieldEntry = "            '{$fieldName}' => '{$fieldLabel}',";
                $fieldsPattern = "/('fields' => \[)([^\]]*?)(\s*\],)/s";
                if (preg_match($fieldsPattern, $content)) {
                    $content = preg_replace(
                        $fieldsPattern,
                        "$1$2{$fieldEntry}\n        $3",
                        $content
                    );
                }
            }

            $this->writeFile($path, $content, "Localization ({$locale})");
            $this->modifiedFiles[] = $path;
        }
    }

    /**
     * Build default English labels for schema fields.
     *
     * @return array<string, string> Field label map.
     */
    protected function getEnglishFieldLabels(): array
    {
        $labels = [];
        foreach ($this->schema['fields'] as $field) {
            $name = $field['name'];
            $labels[$name] = $field['label'] ?? Str::title(str_replace('_', ' ', $name));
        }
        return $labels;
    }

    /**
     * Build default Czech labels for schema fields.
     *
     * @return array<string, string> Field label map.
     */
    protected function getCzechFieldLabels(): array
    {
        $labels = [];
        foreach ($this->schema['fields'] as $field) {
            $name = $field['name'];
            $labels[$name] = $field['label_cs'] ?? $field['label'] ?? Str::title(str_replace('_', ' ', $name));
        }
        return $labels;
    }

    /**
     * Generate the Astro block component file.
     */
    protected function generateAstroComponent(): void
    {
        $path = "{$this->astroPath}/src/components/blocks/{$this->componentName}.astro";

        if ($this->files->exists($path) && ! $this->option('force')) {
            $this->warn("Astro component already exists: {$path}");
            return;
        }

        $stub = $this->getAstroComponentStub();
        $content = str_replace(
            ['{{ componentName }}', '{{ blockType }}', '{{ propsInterface }}', '{{ templateContent }}'],
            [$this->componentName, $this->blockType, $this->generateAstroPropsInterface(), $this->generateAstroTemplate()],
            $stub
        );

        $this->writeFile($path, $content, 'Astro component');
    }

    /**
     * Get the Astro component stub content.
     */
    protected function getAstroComponentStub(): string
    {
        $stubPath = resource_path('stubs/astro/BlockComponent.astro.stub');

        if ($this->files->exists($stubPath)) {
            return $this->files->get($stubPath);
        }

        return <<<'STUB'
---
import type { {{ componentName }}BlockData } from '../../lib/api';

interface Props {
  data: {{ componentName }}BlockData;
}

const { data } = Astro.props;
---

<section class="py-12 px-4">
  <div class="container mx-auto max-w-6xl">
{{ templateContent }}
  </div>
</section>
STUB;
    }

    /**
     * Generate Astro props interface type name.
     */
    protected function generateAstroPropsInterface(): string
    {
        return "{$this->componentName}BlockData";
    }

    /**
     * Generate Astro template markup for known field types.
     */
    protected function generateAstroTemplate(): string
    {
        $lines = [];

        foreach ($this->schema['fields'] as $field) {
            $name = $field['name'];
            $camelName = Str::camel($name);

            if ($field['type'] === 'file') {
                if (! empty($field['multiple'])) {
                    $lines[] = "    {data.{$camelName} && data.{$camelName}.length > 0 && (";
                    $lines[] = "      <div class=\"grid gap-4\">";
                    $lines[] = "        {data.{$camelName}.map((url) => (";
                    $lines[] = "          <img src={url} alt=\"\" class=\"rounded-lg\" />";
                    $lines[] = "        ))}";
                    $lines[] = "      </div>";
                    $lines[] = "    )}";
                } else {
                    $lines[] = "    {data.{$camelName} && (";
                    $lines[] = "      <img src={data.{$camelName}} alt=\"\" class=\"rounded-lg\" />";
                    $lines[] = "    )}";
                }
            } elseif ($field['type'] === 'richtext' || $field['type'] === 'markdown') {
                $lines[] = "    {data.{$camelName} && (";
                $lines[] = "      <div class=\"prose max-w-none\" set:html={data.{$camelName}} />";
                $lines[] = "    )}";
            } elseif ($field['type'] === 'repeater') {
                $lines[] = "    {data.{$camelName} && data.{$camelName}.length > 0 && (";
                $lines[] = "      <div class=\"space-y-4\">";
                $lines[] = "        {data.{$camelName}.map((item) => (";
                $lines[] = "          <div class=\"p-4 border rounded\">";
                $lines[] = "            {JSON.stringify(item)}";
                $lines[] = "          </div>";
                $lines[] = "        ))}";
                $lines[] = "      </div>";
                $lines[] = "    )}";
            } elseif (in_array($field['type'], ['text', 'textarea'])) {
                $tag = $name === 'title' || $name === 'heading' ? 'h2' : 'p';
                $class = $name === 'title' || $name === 'heading' ? 'text-3xl font-bold mb-4' : 'text-gray-600';
                $lines[] = "    {data.{$camelName} && (";
                $lines[] = "      <{$tag} class=\"{$class}\">{data.{$camelName}}</{$tag}>";
                $lines[] = "    )}";
            }
        }

        return implode("\n", $lines) ?: '    <p>Block content goes here</p>';
    }

    /**
     * Update shared Astro API types with the new block data type.
     */
    protected function updateAstroTypes(): void
    {
        $path = "{$this->astroPath}/src/lib/api/types.ts";

        if (! $this->files->exists($path)) {
            $this->warn("Astro types.ts not found at: {$path}");
            return;
        }

        $content = $this->files->get($path);

        // Check if type already exists
        if (str_contains($content, "{$this->componentName}BlockData")) {
            $this->warn("Type {$this->componentName}BlockData already exists in types.ts");
            return;
        }

        // Add block type to Block union
        $blockTypePattern = "/(type:\s*\n?\s*\|[^;]*?)(\s*;)/s";
        if (preg_match($blockTypePattern, $content)) {
            $content = preg_replace(
                $blockTypePattern,
                "$1\n    | '{$this->blockType}'$2",
                $content
            );
        }

        // Add to data union
        $dataPattern = "/(data:\s*\n?\s*\|[^;]*?)(\s*;)/s";
        if (preg_match($dataPattern, $content)) {
            $content = preg_replace(
                $dataPattern,
                "$1\n    | {$this->componentName}BlockData$2",
                $content
            );
        }

        // Generate and add interface
        $interface = $this->generateTypeScriptInterface();
        $content .= "\n{$interface}\n";

        $this->writeFile($path, $content, 'Astro types.ts');
        $this->modifiedFiles[] = $path;
    }

    /**
     * Generate the TypeScript interface for block data.
     *
     * @return string TypeScript interface code.
     */
    protected function generateTypeScriptInterface(): string
    {
        $lines = ["export interface {$this->componentName}BlockData {"];

        foreach ($this->schema['fields'] as $field) {
            $name = Str::camel($field['name']);
            $tsType = $this->mapFieldToTypeScript($field);
            $optional = empty($field['required']) ? '?' : '';
            $lines[] = "  {$name}{$optional}: {$tsType};";
        }

        $lines[] = '}';

        return implode("\n", $lines);
    }

    /**
     * Map one schema field into a TypeScript type.
     *
     * @param array<string, mixed> $field Schema field definition.
     * @return string TypeScript type expression.
     */
    protected function mapFieldToTypeScript(array $field): string
    {
        return match ($field['type']) {
            'text', 'textarea', 'richtext', 'markdown', 'color' => 'string',
            'checkbox', 'toggle' => 'boolean',
            'datetime' => 'string',
            'file' => ! empty($field['multiple']) ? 'string[]' : 'string',
            'select' => isset($field['options']) ? $this->generateUnionType($field['options']) : 'string',
            'checkbox_list', 'tags' => 'string[]',
            'repeater' => isset($field['schema']) ? $this->generateRepeaterType($field['schema']) : 'unknown[]',
            'key_value' => 'Record<string, string>',
            default => 'unknown',
        };
    }

    /**
     * Generate a TypeScript union from select options.
     *
     * @param array<int|string, scalar> $options Select options.
     * @return string TypeScript union type.
     */
    protected function generateUnionType(array $options): string
    {
        $values = array_map(fn ($v) => "'{$v}'", array_values($options));
        return implode(' | ', $values);
    }

    /**
     * Generate a TypeScript type for repeater items.
     *
     * @param array<int, array<string, mixed>> $schema Repeater field schema.
     * @return string TypeScript array item type expression.
     */
    protected function generateRepeaterType(array $schema): string
    {
        $props = [];
        foreach ($schema as $field) {
            $name = Str::camel($field['name']);
            $type = $this->mapFieldToTypeScript($field);
            $optional = empty($field['required']) ? '?' : '';
            $props[] = "{$name}{$optional}: {$type}";
        }
        return '{ ' . implode('; ', $props) . ' }[]';
    }

    /**
     * Register the new block component in the Astro registry.
     */
    protected function updateAstroRegistry(): void
    {
        $registryPath = "{$this->astroPath}/src/components/blocks/registry.ts";

        // Create registry if it doesn't exist
        if (! $this->files->exists($registryPath)) {
            $content = $this->createBlockRegistry();
            $this->writeFile($registryPath, $content, 'Astro block registry (new)');
            return;
        }

        $content = $this->files->get($registryPath);

        // Add import
        if (! str_contains($content, "import {$this->componentName}")) {
            $importLine = "import {$this->componentName} from './{$this->componentName}.astro';";
            $content = preg_replace(
                "/(import [^;]+;)\n/",
                "$1\n{$importLine}\n",
                $content,
                1
            );
        }

        // Add to registry object
        $registryPattern = '/(export const blockRegistry[^{]*\{)([^}]*)(}\s*;?\s*$)/s';
        if (preg_match($registryPattern, $content) && ! str_contains($content, "'{$this->blockType}'")) {
            $content = preg_replace(
                $registryPattern,
                "$1$2  '{$this->blockType}': {$this->componentName},\n$3",
                $content
            );
        }

        $this->writeFile($registryPath, $content, 'Astro block registry');
        $this->modifiedFiles[] = $registryPath;
    }

    /**
     * Create a new Astro registry file content.
     */
    protected function createBlockRegistry(): string
    {
        return <<<TS
import {$this->componentName} from './{$this->componentName}.astro';

export const blockRegistry = {
  '{$this->blockType}': {$this->componentName},
} as const;

export type BlockType = keyof typeof blockRegistry;
TS;
    }

    /**
     * Write a file unless dry-run mode is enabled.
     */
    protected function writeFile(string $path, string $content, string $description): void
    {
        if ($this->option('dry-run')) {
            $this->line("  [DRY RUN] Would write: {$path}");
            return;
        }

        $this->ensureDirectory(dirname($path));
        $this->files->put($path, $content);
        $this->createdFiles[] = ['path' => $path, 'description' => $description];
    }

    /**
     * Ensure the target directory exists.
     */
    protected function ensureDirectory(string $path): void
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true);
        }
    }

    /**
     * Print a summary of generated artifacts.
     */
    protected function printSummary(): void
    {
        $this->newLine();
        $this->info('Block generation complete!');
        $this->newLine();

        if (! empty($this->createdFiles)) {
            $this->line('<fg=green>Created/Modified files:</>');
            foreach ($this->createdFiles as $file) {
                $this->line("  ✓ {$file['description']}: {$file['path']}");
            }
        }

        $this->newLine();
        $this->line('<fg=yellow>Next steps:</>');
        $this->line('  1. Clear block cache: php artisan blocks:clear');
        $this->line('  2. Review generated files and customize as needed');
        $this->line('  3. Run frontend lint: cd ../ercee-frontend && pnpm lint');
    }
}

