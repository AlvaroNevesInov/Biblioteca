<?php

namespace Database\Factories;

use App\Models\Autor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Autor>
 */
class AutorFactory extends Factory
{
    protected $model = Autor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $autores = [
            'José Saramago',
            'Fernando Pessoa',
            'Eça de Queirós',
            'Luís de Camões',
            'Sophia de Mello Breyner',
            'António Lobo Antunes',
            'Agustina Bessa-Luís',
            'Miguel Torga',
            'Vergílio Ferreira',
            'Camilo Castelo Branco',
        ];

        return [
            'nome' => $this->faker->unique()->randomElement($autores),
            'foto' => null,
        ];
    }
}
