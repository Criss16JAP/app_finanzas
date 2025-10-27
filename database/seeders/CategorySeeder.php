<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Core\Category; // Importa el modelo

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
            ['name' => 'Préstamos', 'type' => 'Egreso'], // Categoría para el Módulo 5
            ['name' => 'Alimentación', 'type' => 'Egreso'],
            ['name' => 'Transporte', 'type' => 'Egreso'],
            ['name' => 'Vivienda', 'type' => 'Egreso'],
            ['name' => 'Ocio', 'type' => 'Egreso'],
            ['name' => 'Salud', 'type' => 'Egreso'],
            ['name' => 'Otros Egresos', 'type' => 'Egreso'],

            // Ingresos
            ['name' => 'Salario', 'type' => 'Ingreso'],
            ['name' => 'Préstamos', 'type' => 'Ingreso'], // Categoría para el Módulo 5
            ['name' => 'Inversiones', 'type' => 'Ingreso'],
            ['name' => 'Otros Ingresos', 'type' => 'Ingreso'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                [
                    'name' => $category['name'],
                    'type' => $category['type'],
                    'user_id' => null // MUY IMPORTANTE: null = categoría por defecto
                ]
            );
        }
    }
}
