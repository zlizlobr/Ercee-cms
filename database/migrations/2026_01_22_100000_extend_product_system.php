<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Extend products table with slug and type
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug')->unique()->after('name');
            $table->string('type')->default('simple')->after('slug'); // simple, virtual, variable
            $table->index('slug');
            $table->index('type');
        });

        // Taxonomies (categories, tags, brands, etc.)
        Schema::create('taxonomies', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // category, tag, brand
            $table->string('name');
            $table->string('slug');
            $table->timestamps();

            $table->unique(['type', 'slug']);
            $table->index('type');
            $table->index('slug');
        });

        // Polymorphic pivot for taxonomies
        Schema::create('taxables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taxonomy_id')->constrained()->cascadeOnDelete();
            $table->morphs('taxable');
            $table->timestamps();

            $table->unique(['taxonomy_id', 'taxable_id', 'taxable_type']);
        });

        // Attribute definitions
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // machine-readable, immutable
            $table->string('name'); // human-readable
            $table->boolean('is_filterable')->default(false);
            $table->timestamps();

            $table->index('code');
            $table->index('is_filterable');
        });

        // Attribute values
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained()->cascadeOnDelete();
            $table->string('value');
            $table->string('slug');
            $table->timestamps();

            $table->unique(['attribute_id', 'slug']);
            $table->index('slug');
        });

        // Product <-> AttributeValue pivot
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['product_id', 'attribute_value_id']);
            $table->index('product_id');
        });

        // Product variants (for variable products)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->unsignedInteger('price'); // in cents (haléře)
            $table->integer('stock')->default(0);
            $table->json('data')->nullable();
            $table->timestamps();

            $table->index('product_id');
            $table->index('sku');
        });

        // Variant <-> AttributeValue pivot
        Schema::create('variant_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['product_variant_id', 'attribute_value_id']);
        });

        // Product reviews
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscriber_id')->nullable()->constrained()->nullOnDelete();
            $table->string('author_name');
            $table->string('author_email');
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('content');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->timestamps();

            $table->index('product_id');
            $table->index('status');
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('variant_attribute_values');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('taxables');
        Schema::dropIfExists('taxonomies');

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['type']);
            $table->dropColumn(['slug', 'type']);
        });
    }
};
