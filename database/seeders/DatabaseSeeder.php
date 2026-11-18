<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Equipo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@utt.edu.mx'],
            [
                'name' => 'Admin UTT',
                'password' => Hash::make('12345678'),
                'rol' => 'admin',
            ]
        );

        $categorias = ['Electrónica', 'Cómputo', 'Accesorios', 'Redes', 'Almacenamiento'];
        foreach ($categorias as $nombre) {
            Categoria::firstOrCreate(['nombre' => $nombre]);
        }

        for ($i = 1; $i <= 8; $i++) {
            Equipo::firstOrCreate(
                ['codigo' => 'PC-UTT-0' . $i],
                [
                    'descripcion' => 'Estación de Trabajo Core i7 - 16GB RAM - SSD 512GB',
                    'estado' => 'Disponible',
                ]
            );
        }
    }
}