<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Users\Plan; // Asegúrate de importar el modelo

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Plan Gratuito
        Plan::updateOrCreate(
            ['name' => 'Gratuito'],
            [
                'price_monthly' => 0,
                'price_semester' => 0,
                'price_yearly' => 0,
            ]
        );

        // Plan Premium
        Plan::updateOrCreate(
            ['name' => 'Premium'],
            [
                'price_monthly' => 20000,  // Precio de ejemplo
                'price_semester' => 100000,
                'price_yearly' => 180000,
            ]
        );

        // Plan Freelancer
        Plan::updateOrCreate(
            ['name' => 'Freelancer'],
            [
                'price_monthly' => 35000,
                'price_semester' => 180000,
                'price_yearly' => 320000,
            ]
        );
    }
}
