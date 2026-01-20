<?php

use App\Domain\Content\Menu;
use App\Domain\Content\Navigation;
use App\Domain\Content\Page;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create default 'main' menu
        $menu = Menu::firstOrCreate(
            ['slug' => 'main'],
            ['name' => 'Main Navigation']
        );

        // Assign all existing navigations to this menu
        Navigation::whereNull('menu_id')->update(['menu_id' => $menu->id]);

        // Backfill navigable_type/id for existing page_id relations
        Navigation::whereNotNull('page_id')
            ->whereNull('navigable_type')
            ->each(function (Navigation $nav) {
                $nav->update([
                    'navigable_type' => Page::class,
                    'navigable_id' => $nav->page_id,
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear menu assignments
        Navigation::whereNotNull('menu_id')->update(['menu_id' => null]);

        // Clear polymorphic backfill (keep page_id for legacy)
        Navigation::whereNotNull('navigable_type')->update([
            'navigable_type' => null,
            'navigable_id' => null,
        ]);

        // Optionally delete default menu
        Menu::where('slug', 'main')->delete();
    }
};
