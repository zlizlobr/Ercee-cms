<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Convert existing title values to JSON format with default locale
        $defaultLocale = config('app.locale', 'cs');

        // First, get all existing pages
        $pages = DB::table('pages')->get();

        // Change column type to JSON
        Schema::table('pages', function (Blueprint $table) {
            $table->json('title_new')->nullable();
        });

        // Migrate existing data
        foreach ($pages as $page) {
            DB::table('pages')
                ->where('id', $page->id)
                ->update([
                    'title_new' => json_encode([
                        $defaultLocale => $page->title,
                    ]),
                ]);
        }

        // Drop old column and rename new one
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('title');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->renameColumn('title_new', 'title');
        });
    }

    public function down(): void
    {
        $defaultLocale = config('app.locale', 'cs');

        $pages = DB::table('pages')->get();

        Schema::table('pages', function (Blueprint $table) {
            $table->string('title_new')->nullable();
        });

        foreach ($pages as $page) {
            $titleData = json_decode($page->title, true);
            $title = $titleData[$defaultLocale] ?? array_values($titleData)[0] ?? '';

            DB::table('pages')
                ->where('id', $page->id)
                ->update(['title_new' => $title]);
        }

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('title');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->renameColumn('title_new', 'title');
        });
    }
};
