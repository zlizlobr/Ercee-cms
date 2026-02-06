<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $pages = DB::table('pages')
            ->select('id', 'content')
            ->whereNotNull('content')
            ->get();

        foreach ($pages as $page) {
            $content = json_decode($page->content, true);

            if (!is_array($content)) {
                continue;
            }

            $changed = false;

            foreach ($content as $index => $block) {
                if (!is_array($block)) {
                    continue;
                }

                if (($block['type'] ?? null) === 'documentation_hero') {
                    $content[$index]['type'] = 'page_hero';
                    $changed = true;
                }
            }

            if ($changed) {
                DB::table('pages')
                    ->where('id', $page->id)
                    ->update([
                        'content' => json_encode($content, JSON_UNESCAPED_UNICODE),
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data migration is not safely reversible.
    }
};
