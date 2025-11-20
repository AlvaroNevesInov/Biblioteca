<?php

namespace Tests\Unit;

use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste para verificar se utilizador é admin
     */
    public function test_verifica_se_utilizador_e_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $cidadao = User::factory()->cidadao()->create();

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($cidadao->isAdmin());
    }

    /**
     * Teste para verificar se utilizador é cidadão
     */
    public function test_verifica_se_utilizador_e_cidadao(): void
    {
        $admin = User::factory()->admin()->create();
        $cidadao = User::factory()->cidadao()->create();

        $this->assertFalse($admin->isCidadao());
        $this->assertTrue($cidadao->isCidadao());
    }

    /**
     * Teste para contar requisições ativas
     */
    public function test_contar_requisicoes_ativas(): void
    {
        $user = User::factory()->create();
        $livro1 = Livro::factory()->create();
        $livro2 = Livro::factory()->create();
        $livro3 = Livro::factory()->create();

        // Sem requisições
        $this->assertEquals(0, $user->contarRequisicoesAtivas());

        // Criar requisição pendente
        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro1->id,
            'estado' => 'pendente',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $this->assertEquals(1, $user->contarRequisicoesAtivas());

        // Criar requisição aprovada
        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro2->id,
            'estado' => 'aprovada',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $this->assertEquals(2, $user->contarRequisicoesAtivas());

        // Requisição devolvida não deve contar
        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro3->id,
            'estado' => 'devolvida',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
            'data_recepcao' => now(),
        ]);

        $this->assertEquals(2, $user->contarRequisicoesAtivas());
    }

    /**
     * Teste para verificar se utilizador pode requisitar (limite de 3 livros)
     */
    public function test_utilizador_pode_requisitar_ate_3_livros(): void
    {
        $user = User::factory()->create();

        // Sem requisições, pode requisitar
        $this->assertTrue($user->podeRequisitar());

        // Criar 1 requisição ativa
        $livro1 = Livro::factory()->create();
        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro1->id,
            'estado' => 'aprovada',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $this->assertTrue($user->podeRequisitar());

        // Criar 2ª requisição ativa
        $livro2 = Livro::factory()->create();
        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro2->id,
            'estado' => 'pendente',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $this->assertTrue($user->podeRequisitar());

        // Criar 3ª requisição ativa - atingiu o limite
        $livro3 = Livro::factory()->create();
        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro3->id,
            'estado' => 'aprovada',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $this->assertFalse($user->podeRequisitar());
    }

    /**
     * Teste para verificar que requisições rejeitadas não contam no limite
     */
    public function test_requisicoes_rejeitadas_nao_contam_no_limite(): void
    {
        $user = User::factory()->create();

        // Criar 3 requisições rejeitadas
        for ($i = 0; $i < 3; $i++) {
            $livro = Livro::factory()->create();
            Requisicao::create([
                'user_id' => $user->id,
                'livro_id' => $livro->id,
                'estado' => 'rejeitada',
                'data_requisicao' => now(),
                'data_prevista_devolucao' => now()->addDays(5),
            ]);
        }

        // Ainda pode requisitar
        $this->assertTrue($user->podeRequisitar());
        $this->assertEquals(0, $user->contarRequisicoesAtivas());
    }

    /**
     * Teste para verificar que requisições devolvidas não contam no limite
     */
    public function test_requisicoes_devolvidas_nao_contam_no_limite(): void
    {
        $user = User::factory()->create();

        // Criar 3 requisições devolvidas
        for ($i = 0; $i < 3; $i++) {
            $livro = Livro::factory()->create();
            Requisicao::create([
                'user_id' => $user->id,
                'livro_id' => $livro->id,
                'estado' => 'devolvida',
                'data_requisicao' => now()->subDays(10),
                'data_prevista_devolucao' => now()->subDays(5),
                'data_recepcao' => now()->subDays(3),
            ]);
        }

        // Ainda pode requisitar
        $this->assertTrue($user->podeRequisitar());
        $this->assertEquals(0, $user->contarRequisicoesAtivas());
    }

    /**
     * Teste para verificar que após devolução, utilizador pode requisitar novamente
     */
    public function test_apos_devolucao_utilizador_pode_requisitar_novamente(): void
    {
        $user = User::factory()->create();

        // Criar 3 requisições ativas (limite atingido)
        for ($i = 0; $i < 3; $i++) {
            $livro = Livro::factory()->create();
            Requisicao::create([
                'user_id' => $user->id,
                'livro_id' => $livro->id,
                'estado' => 'aprovada',
                'data_requisicao' => now(),
                'data_prevista_devolucao' => now()->addDays(5),
            ]);
        }

        $this->assertFalse($user->podeRequisitar());

        // Devolver uma requisição
        $requisicao = $user->requisicoes()->first();
        $requisicao->estado = 'devolvida';
        $requisicao->data_recepcao = now();
        $requisicao->save();

        // Agora pode requisitar novamente
        $this->assertTrue($user->podeRequisitar());
        $this->assertEquals(2, $user->contarRequisicoesAtivas());
    }

    /**
     * Teste para verificar relação com requisições
     */
    public function test_relacao_com_requisicoes(): void
    {
        $user = User::factory()->create();
        $livro = Livro::factory()->create();

        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'aprovada',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->requisicoes);
        $this->assertCount(1, $user->requisicoes);
    }
}
