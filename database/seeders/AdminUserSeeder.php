<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Admin\Staff; // Importa el modelo Staff
use Illuminate\Support\Facades\Hash; // Importa Hash

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Staff::updateOrCreate(
            ['email' => 'cjap1607@gmail.com'], // <-- Tu nuevo email
            [
                'name' => 'Administrador',
                'password' => Hash::make('Admin16.'), // <-- Tu nueva contraseña
                'role' => 'Admin'
            ]
        );
    }
}
