<?php

namespace Database\Seeders;

use App\Models\Categorias;
use App\Models\User;
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            // 'name' => 'Test User',
            'email' => 'test@gmail.com',
            // "apellido" => "Test apellido",
            "usuario" => 'Name User Test',
            "password" => '1234567890',
            // "rol_id" => 2,
            "activo" => 1,
            "ultimo_acceso" => Carbon::now(),
        ]);


        $categorias = [
            'Acción', 'Aventura', 'Rol (RPG)', 'Estrategia', 'Simulación',
            'Deportes', 'Carreras', 'Terror', 'Combate', 'Música y Ritmo'
        ];

        // Crear una categoría a la vez
        foreach ($categorias as $categoria) {
            Categorias::create([
                'categoria' => $categoria,
                'descripcion' => 'Descripción de ' . $categoria,
                'activo' => true, 
            ]);
        }

    }
}
