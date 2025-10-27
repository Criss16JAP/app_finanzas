<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llamamos a los seeders en orden
        $this->call([
            PlanSeeder::class,
            CategorySeeder::class,
            AdminUserSeeder::class,
        ]);

    }
}
