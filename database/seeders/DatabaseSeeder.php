<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Cria o seu usuário Administrador pronto e real para testes
        User::updateOrCreate(
            ['email' => 'rafael@admin.com'], // Evita duplicar se rodar o comando mais de uma vez
            [
                'name' => 'Rafael Vaz',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
            ]
        );

        // 2. Chama o seeder que popula os cards de viagens (Paris, Roma, Rio)
        $this->call([
            DestinoSeeder::class,
        ]);
    }
}