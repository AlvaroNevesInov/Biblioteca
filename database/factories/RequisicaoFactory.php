<?php

namespace Database\Factories;

use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Requisicao>
 */
class RequisicaoFactory extends Factory
{
    protected $model = Requisicao::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dataRequisicao = $this->faker->dateTimeBetween('-30 days', 'now');
        $dataPrevista = (clone $dataRequisicao)->modify('+5 days');

        return [
            'user_id' => User::factory(),
            'livro_id' => Livro::factory(),
            'foto_cidadao' => null,
            'estado' => 'pendente',
            'data_requisicao' => $dataRequisicao,
            'data_prevista_devolucao' => $dataPrevista,
            'data_devolucao' => null,
            'data_recepcao' => null,
            'recebido_por' => null,
            'observacoes' => null,
        ];
    }

    /**
     * Estado: Aprovada
     */
    public function aprovada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'aprovada',
        ]);
    }

    /**
     * Estado: Rejeitada
     */
    public function rejeitada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'rejeitada',
        ]);
    }

    /**
     * Estado: Devolvida
     */
    public function devolvida(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'devolvida',
            'data_recepcao' => $this->faker->dateTimeBetween($attributes['data_requisicao'], 'now'),
        ]);
    }

    /**
     * Requisição com atraso
     */
    public function atrasada(): static
    {
        return $this->state(function (array $attributes) {
            $dataRequisicao = now()->subDays(10);
            $dataPrevista = now()->subDays(3);

            return [
                'estado' => 'aprovada',
                'data_requisicao' => $dataRequisicao,
                'data_prevista_devolucao' => $dataPrevista,
            ];
        });
    }
}
