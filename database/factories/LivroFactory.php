<?php

namespace Database\Factories;

use App\Models\Editora;
use App\Models\Livro;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Livro>
 */
class LivroFactory extends Factory
{
    protected $model = Livro::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'isbn' => $this->faker->unique()->isbn13(),
            'nome' => $this->faker->sentence(3),
            'editora_id' => Editora::factory(),
            'bibliografia' => $this->faker->paragraph(),
            'imagem_capa' => null,
            'preco' => $this->faker->randomFloat(2, 5, 50),
            'stock' => $this->faker->numberBetween(0, 20),
        ];
    }
}
