<?php

namespace Database\Factories;

use App\Models\Editora;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Editora>
 */
class EditoraFactory extends Factory
{
    protected $model = Editora::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $editoras = [
            'Porto Editora',
            'Leya',
            'Bertrand',
            'Dom Quixote',
            'Editorial Presença',
            'Civilização Editora',
            'Almedina',
            'Gradiva',
            '20|20 Editora',
            'Penguin Random House',
        ];

        return [
            'nome' => $this->faker->unique()->randomElement($editoras),
            'logotipo' => null,
        ];
    }
}
