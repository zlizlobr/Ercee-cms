<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Commerce\Domain\Attribute;
use Modules\Commerce\Domain\AttributeValue;
use Modules\Commerce\Domain\Product;
use Modules\Commerce\Domain\Taxonomy;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $count = 20;

        $attributes = $this->seedAttributes();
        $taxonomies = $this->seedTaxonomies();

        $attributeValueIds = AttributeValue::query()->pluck('id')->all();
        $categoryIds = $taxonomies['categories'];
        $tagIds = $taxonomies['tags'];
        $brandIds = $taxonomies['brands'];

        $faker = fake();

        for ($i = 0; $i < $count; $i++) {
            $name = ucfirst($faker->unique()->words(3, true));

            $product = Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'type' => Product::TYPE_SIMPLE,
                    'price' => $faker->numberBetween(1000, 100000),
                    'active' => true,
                    'attachment' => null,
                    'data' => [
                        'short_description' => $faker->sentence(12),
                        'description' => '<p>'.$faker->paragraph(3).'</p>',
                        'gallery' => [],
                        'seo' => [
                            'title' => Str::limit($name.' | Product', 60, ''),
                            'description' => Str::limit($faker->sentence(20), 160, ''),
                            'og_title' => $name,
                            'og_description' => Str::limit($faker->sentence(18), 200, ''),
                            'og_image' => null,
                        ],
                    ],
                ],
            );

            $product->attributeValues()->sync(
                $faker->randomElements($attributeValueIds, $faker->numberBetween(2, 4))
            );

            $product->taxonomies()->sync([
                ...$faker->randomElements($categoryIds, 1),
                ...$faker->randomElements($tagIds, 2),
                ...$faker->randomElements($brandIds, 1),
            ]);
        }
    }

    private function seedAttributes(): array
    {
        $attributes = [
            'material' => [
                'name' => 'Material',
                'values' => ['Ocel', 'Hlinik', 'Plast', 'Drevo'],
            ],
            'size' => [
                'name' => 'Velikost',
                'values' => ['S', 'M', 'L', 'XL'],
            ],
            'delivery' => [
                'name' => 'Dodani',
                'values' => ['Skladem', 'Do 3 dnu', 'Do 7 dnu', 'Na objednavku'],
            ],
        ];

        foreach ($attributes as $code => $data) {
            $attribute = Attribute::updateOrCreate(
                ['code' => $code],
                ['name' => $data['name'], 'is_filterable' => true]
            );

            foreach ($data['values'] as $value) {
                AttributeValue::updateOrCreate(
                    ['attribute_id' => $attribute->id, 'slug' => Str::slug($value)],
                    ['value' => $value]
                );
            }
        }

        return $attributes;
    }

    private function seedTaxonomies(): array
    {
        $categories = [
            'Webove sluzby',
            'E-shop',
            'Marketing',
            'Integrace',
        ];

        $tags = [
            'seo',
            'rychlost',
            'ux',
            'branding',
            'cms',
        ];

        $brands = [
            'Studio Nova',
            'PixelForge',
            'Brightdesk',
        ];

        $categoryIds = [];
        foreach ($categories as $name) {
            $categoryIds[] = Taxonomy::updateOrCreate(
                ['type' => Taxonomy::TYPE_CATEGORY, 'slug' => Str::slug($name)],
                ['name' => $name]
            )->id;
        }

        $tagIds = [];
        foreach ($tags as $name) {
            $tagIds[] = Taxonomy::updateOrCreate(
                ['type' => Taxonomy::TYPE_TAG, 'slug' => Str::slug($name)],
                ['name' => $name]
            )->id;
        }

        $brandIds = [];
        foreach ($brands as $name) {
            $brandIds[] = Taxonomy::updateOrCreate(
                ['type' => Taxonomy::TYPE_BRAND, 'slug' => Str::slug($name)],
                ['name' => $name]
            )->id;
        }

        return [
            'categories' => $categoryIds,
            'tags' => $tagIds,
            'brands' => $brandIds,
        ];
    }
}
