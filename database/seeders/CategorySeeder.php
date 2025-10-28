<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Core\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Egresos
            ['name' => 'Impuestos', 'type' => 'Egreso'],
            ['name' => 'Préstamos', 'type' => 'Egreso'],
            ['name' => 'Transferencia', 'type' => 'Egreso'],
            ['name' => 'Alimentación', 'type' => 'Egreso'],
            ['name' => 'Transporte', 'type' => 'Egreso'],
            ['name' => 'Vivienda', 'type' => 'Egreso'],
            ['name' => 'Ocio', 'type' => 'Egreso'],
            ['name' => 'Salud', 'type' => 'Egreso'],
            ['name' => 'Otros Egresos', 'type' => 'Egreso'],

            // Ingresos
            ['name' => 'Transferencia', 'type' => 'Ingreso'],
            ['name' => 'Salario', 'type' => 'Ingreso'],
            ['name' => 'Préstamos', 'type' => 'Ingreso'],
            ['name' => 'Inversiones', 'type' => 'Ingreso'],
            ['name' => 'Otros Ingresos', 'type' => 'Ingreso'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                [
                    'name' => $category['name'],
                    'type' => $category['type'],
                    'user_id' => null
                ]
            );
        }
    }
}
