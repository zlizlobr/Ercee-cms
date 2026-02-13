<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeds the core application data.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seeders = [
            RolesAndPermissionsSeeder::class,
            AdminUserSeeder::class,
            NavigationSeeder::class,
            ProductsSeeder::class,
            TemplatePagesSeeder::class,
            HomePageSeeder::class,
        ];

        $formsSeeder = 'Modules\\Forms\\Database\\Seeders\\FormsSeeder';

        if (class_exists($formsSeeder)) {
            $seeders[] = $formsSeeder;
        }

        $this->call($seeders);
    }
}
