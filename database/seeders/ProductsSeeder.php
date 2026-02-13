<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Concerns\ReadsJsonSeedData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Commerce\Domain\Attribute;
use Modules\Commerce\Domain\AttributeValue;
use Modules\Commerce\Domain\Product;
use Modules\Commerce\Domain\Taxonomy;

class ProductsSeeder extends Seeder
{
    use ReadsJsonSeedData;

    public function run(): void
    {
        $payload = $this->readSeedJson('products.json');
        if (! is_array($payload)) {
            $this->warn('Skipping ProductsSeeder: invalid payload.');

            return;
        }

        $this->seedAttributes(is_array($payload['attributes'] ?? null) ? $payload['attributes'] : []);
        $this->seedTaxonomies(is_array($payload['taxonomies'] ?? null) ? $payload['taxonomies'] : []);
        $this->seedProducts(is_array($payload['products'] ?? null) ? $payload['products'] : []);
    }

    private function seedAttributes(array $attributes): void
    {
        foreach ($attributes as $attributeData) {
            if (! is_array($attributeData) || ! isset($attributeData['code'], $attributeData['name'])) {
                continue;
            }

            $attribute = Attribute::updateOrCreate(
                ['code' => (string) $attributeData['code']],
                [
                    'name' => (string) $attributeData['name'],
                    'is_filterable' => (bool) ($attributeData['is_filterable'] ?? true),
                ]
            );

            $values = is_array($attributeData['values'] ?? null) ? $attributeData['values'] : [];
            foreach ($values as $valueData) {
                $value = is_array($valueData)
                    ? (string) ($valueData['value'] ?? '')
                    : (string) $valueData;

                if ($value === '') {
                    continue;
                }

                $slug = is_array($valueData)
                    ? (string) ($valueData['slug'] ?? Str::slug($value))
                    : Str::slug($value);

                AttributeValue::updateOrCreate(
                    ['attribute_id' => $attribute->id, 'slug' => $slug],
                    ['value' => $value]
                );
            }
        }
    }

    private function seedTaxonomies(array $taxonomies): void
    {
        $this->seedTaxonomyType(Taxonomy::TYPE_CATEGORY, is_array($taxonomies['categories'] ?? null) ? $taxonomies['categories'] : []);
        $this->seedTaxonomyType(Taxonomy::TYPE_TAG, is_array($taxonomies['tags'] ?? null) ? $taxonomies['tags'] : []);
        $this->seedTaxonomyType(Taxonomy::TYPE_BRAND, is_array($taxonomies['brands'] ?? null) ? $taxonomies['brands'] : []);
    }

    private function seedTaxonomyType(string $type, array $items): void
    {
        foreach ($items as $item) {
            $name = is_array($item) ? (string) ($item['name'] ?? '') : (string) $item;
            if ($name === '') {
                continue;
            }

            $slug = is_array($item) ? (string) ($item['slug'] ?? Str::slug($name)) : Str::slug($name);

            Taxonomy::updateOrCreate(
                ['type' => $type, 'slug' => $slug],
                ['name' => $name]
            );
        }
    }

    private function seedProducts(array $products): void
    {
        foreach ($products as $productData) {
            if (! is_array($productData) || ! isset($productData['name'])) {
                continue;
            }

            $name = (string) $productData['name'];
            $slug = (string) ($productData['slug'] ?? Str::slug($name));

            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'type' => (string) ($productData['type'] ?? Product::TYPE_SIMPLE),
                    'price' => (int) ($productData['price'] ?? 0),
                    'active' => (bool) ($productData['active'] ?? true),
                    'attachment' => $productData['attachment'] ?? null,
                    'data' => is_array($productData['data'] ?? null) ? $productData['data'] : [],
                ]
            );

            $attributeValueIds = [];
            $attributeValues = is_array($productData['attribute_values'] ?? null) ? $productData['attribute_values'] : [];
            foreach ($attributeValues as $attributeValueData) {
                if (! is_array($attributeValueData)) {
                    continue;
                }

                $attributeCode = (string) ($attributeValueData['attribute_code'] ?? '');
                $valueSlug = (string) ($attributeValueData['value_slug'] ?? '');
                $valueText = (string) ($attributeValueData['value'] ?? '');

                if ($attributeCode === '') {
                    continue;
                }

                $attribute = Attribute::query()->where('code', $attributeCode)->first();
                if (! $attribute) {
                    continue;
                }

                $query = AttributeValue::query()->where('attribute_id', $attribute->id);
                if ($valueSlug !== '') {
                    $query->where('slug', $valueSlug);
                } elseif ($valueText !== '') {
                    $query->where('value', $valueText);
                } else {
                    continue;
                }

                $value = $query->first();
                if ($value) {
                    $attributeValueIds[] = $value->id;
                }
            }

            $product->attributeValues()->sync($attributeValueIds);

            $productTaxonomies = is_array($productData['taxonomies'] ?? null) ? $productData['taxonomies'] : [];
            $taxonomyIds = array_merge(
                $this->taxonomyIdsByType(Taxonomy::TYPE_CATEGORY, is_array($productTaxonomies['categories'] ?? null) ? $productTaxonomies['categories'] : []),
                $this->taxonomyIdsByType(Taxonomy::TYPE_TAG, is_array($productTaxonomies['tags'] ?? null) ? $productTaxonomies['tags'] : []),
                $this->taxonomyIdsByType(Taxonomy::TYPE_BRAND, is_array($productTaxonomies['brands'] ?? null) ? $productTaxonomies['brands'] : []),
            );

            $product->taxonomies()->sync(array_values(array_unique($taxonomyIds)));
        }
    }

    private function taxonomyIdsByType(string $type, array $slugs): array
    {
        $cleanSlugs = array_values(array_filter(array_map(static fn ($slug) => is_string($slug) ? trim($slug) : '', $slugs)));
        if ($cleanSlugs === []) {
            return [];
        }

        return Taxonomy::query()
            ->where('type', $type)
            ->whereIn('slug', $cleanSlugs)
            ->pluck('id')
            ->all();
    }
}
