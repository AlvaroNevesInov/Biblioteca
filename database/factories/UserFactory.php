<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'current_team_id' => null,
            'role' => 'cidadao', // role padrão
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user should have a personal team.
     */
    public function withPersonalTeam(?callable $callback = null): static
    {
        if (! Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(fn (array $attributes, User $user) => [
                    'name' => $user->name.'\'s Team',
                    'user_id' => $user->id,
                    'personal_team' => true,
                ])
                ->when(is_callable($callback), $callback),
            'ownedTeams'
        );
    }

    /**
     * Criar utilizador administrador
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Administrador',
            'email' => 'admin@biblioteca.pt',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);
    }

    /**
     * Criar utilizador cidadão
     */
    public function cidadao(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'cidadao',
        ]);
    }

    /**
     * Criar utilizador com nomes portugueses
     */
    public function portuguese(): static
    {
        return $this->state(function (array $attributes) {
            $primeiroNomes = [
                'João', 'Maria', 'José', 'Ana', 'António', 'Francisca',
                'Pedro', 'Inês', 'Manuel', 'Catarina', 'Rui', 'Sofia',
                'Luís', 'Beatriz', 'Carlos', 'Rita', 'Paulo', 'Diana',
                'Miguel', 'Mariana', 'Tiago', 'Carolina'
            ];

            $apelidos = [
                'Silva', 'Santos', 'Ferreira', 'Oliveira', 'Costa',
                'Rodrigues', 'Martins', 'Pereira', 'Carvalho', 'Almeida',
                'Sousa', 'Ribeiro', 'Lopes', 'Gomes', 'Marques',
                'Fernandes', 'Gonçalves', 'Pinto', 'Moreira'
            ];

            $nome = $primeiroNomes[array_rand($primeiroNomes)] . ' ' .
                    $apelidos[array_rand($apelidos)] . ' ' .
                    $apelidos[array_rand($apelidos)];

            return [
                'name' => $nome,
            ];
        });
    }

    /**
     * Criar utilizador verificado
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Criar utilizador de teste
     */
    public function testUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'cidadao',
        ]);
    }
}
