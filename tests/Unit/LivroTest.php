<?php

namespace Tests\Unit;

use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivroTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste para verificar se livro está disponível
     */
    public function test_livro_esta_disponivel_sem_requisicoes(): void
    {
        $livro = Livro::factory()->create();

        $this->assertTrue($livro->estaDisponivel());
    }

    /**
     * Teste para verificar se livro não está disponível com requisição pendente
     */
    public function test_livro_nao_esta_disponivel_com_requisicao_pendente(): void
    {
        $livro = Livro::factory()->create();
        $user = User::factory()->create();

        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'pendente',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $this->assertFalse($livro->estaDisponivel());
    }

    /**
     * Teste para verificar se livro não está disponível com requisição aprovada
     */
    public function test_livro_nao_esta_disponivel_com_requisicao_aprovada(): void
    {
        $livro = Livro::factory()->create();
        $user = User::factory()->create();

        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'aprovada',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $this->assertFalse($livro->estaDisponivel());
    }

    /**
     * Teste para verificar se livro está disponível após devolução
     */
    public function test_livro_esta_disponivel_apos_devolucao(): void
    {
        $livro = Livro::factory()->create();
        $user = User::factory()->create();

        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'devolvida',
            'data_requisicao' => now()->subDays(10),
            'data_prevista_devolucao' => now()->subDays(5),
            'data_recepcao' => now()->subDays(3),
        ]);

        $this->assertTrue($livro->estaDisponivel());
    }

    /**
     * Teste para verificar se livro está disponível após rejeição
     */
    public function test_livro_esta_disponivel_apos_rejeicao(): void
    {
        $livro = Livro::factory()->create();
        $user = User::factory()->create();

        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'rejeitada',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $this->assertTrue($livro->estaDisponivel());
    }

    /**
     * Teste para obter requisição ativa
     */
    public function test_obter_requisicao_ativa(): void
    {
        $livro = Livro::factory()->create();
        $user = User::factory()->create();

        $requisicao = Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'aprovada',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $requisicaoAtiva = $livro->requisicaoAtiva();

        $this->assertNotNull($requisicaoAtiva);
        $this->assertEquals($requisicao->id, $requisicaoAtiva->id);
        $this->assertEquals('aprovada', $requisicaoAtiva->estado);
    }

    /**
     * Teste para verificar que não há requisição ativa quando livro está livre
     */
    public function test_nao_ha_requisicao_ativa_quando_livro_livre(): void
    {
        $livro = Livro::factory()->create();

        $requisicaoAtiva = $livro->requisicaoAtiva();

        $this->assertNull($requisicaoAtiva);
    }

    /**
     * Teste para verificar relações do modelo
     */
    public function test_relacoes_do_livro(): void
    {
        $livro = Livro::factory()->create();
        $user = User::factory()->create();

        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'aprovada',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $livro->requisicoes);
        $this->assertCount(1, $livro->requisicoes);
        $this->assertNotNull($livro->editora);
    }

    /**
     * Teste para verificar múltiplas requisições históricas
     */
    public function test_livro_com_multiplas_requisicoes_historicas(): void
    {
        $livro = Livro::factory()->create();
        $user = User::factory()->create();

        // Criar várias requisições passadas
        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'devolvida',
            'data_requisicao' => now()->subDays(30),
            'data_prevista_devolucao' => now()->subDays(25),
            'data_recepcao' => now()->subDays(23),
        ]);

        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'devolvida',
            'data_requisicao' => now()->subDays(20),
            'data_prevista_devolucao' => now()->subDays(15),
            'data_recepcao' => now()->subDays(14),
        ]);

        // Livro deve estar disponível
        $this->assertTrue($livro->estaDisponivel());
        $this->assertCount(2, $livro->requisicoes);
    }
}
