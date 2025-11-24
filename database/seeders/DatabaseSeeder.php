<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Editora;
use App\Models\Autor;
use App\Models\Livro;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar 1 administrador
        User::factory()->admin()->verified()->create();

        // Criar 15 utilizadores portugueses (cidadãos)
        User::factory()
            ->count(15)
            ->portuguese()
            ->cidadao()
            ->verified()
            ->create();

        $this->command->info('👥 1 Admin + 15 Cidadãos');
    }
}
