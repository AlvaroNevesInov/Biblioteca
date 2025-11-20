<?php

namespace Tests\Unit;

use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequisicaoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste para verificar estados da requisição
     */
    public function test_estados_da_requisicao(): void
    {
        $user = User::factory()->create();
        $livro = Livro::factory()->create();

        // Teste estado pendente
        $requisicao = Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'pendente',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $this->assertTrue($requisicao->isPendente());
        $this->assertFalse($requisicao->isAprovada());
        $this->assertFalse($requisicao->isRejeitada());
        $this->assertFalse($requisicao->isDevolvida());
        $this->assertTrue($requisicao->isAtiva());

        // Teste estado aprovada
        $requisicao->estado = 'aprovada';
        $requisicao->save();

        $this->assertFalse($requisicao->isPendente());
        $this->assertTrue($requisicao->isAprovada());
        $this->assertTrue($requisicao->isAtiva());

        // Teste estado rejeitada
        $requisicao->estado = 'rejeitada';
        $requisicao->save();

        $this->assertTrue($requisicao->isRejeitada());
        $this->assertFalse($requisicao->isAtiva());

        // Teste estado devolvida
        $requisicao->estado = 'devolvida';
        $requisicao->save();

        $this->assertTrue($requisicao->isDevolvida());
        $this->assertFalse($requisicao->isAtiva());
    }

    /**
     * Teste para cálculo de dias decorridos
     */
    public function test_calculo_dias_decorridos(): void
    {
        $user = User::factory()->create();
        $livro = Livro::factory()->create();

        $requisicao = Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'aprovada',
            'data_requisicao' => now()->subDays(10),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        // Sem data de recepção, deve retornar null
        $this->assertNull($requisicao->diasDecorridos());

        // Com data de recepção
        $requisicao->data_recepcao = now()->subDays(3);
        $requisicao->save();

        $this->assertEquals(7, $requisicao->diasDecorridos());
    }

    /**
     * Teste para cálculo de dias de atraso
     */
    public function test_calculo_dias_atraso(): void
    {
        $user = User::factory()->create();
        $livro = Livro::factory()->create();

        // Requisição dentro do prazo
        $requisicao = Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'aprovada',
            'data_requisicao' => now()->subDays(3),
            'data_prevista_devolucao' => now()->addDays(2),
            'data_recepcao' => now(),
        ]);

        $this->assertEquals(0, $requisicao->diasAtraso());

        // Requisição atrasada
        $requisicaoAtrasada = Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'aprovada',
            'data_requisicao' => now()->subDays(10),
            'data_prevista_devolucao' => now()->subDays(3),
            'data_recepcao' => now(),
        ]);

        $this->assertEquals(3, $requisicaoAtrasada->diasAtraso());
    }

    /**
     * Teste para verificar se está recebido
     */
    public function test_verifica_recepcao(): void
    {
        $user = User::factory()->create();
        $livro = Livro::factory()->create();

        $requisicao = Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'aprovada',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $this->assertFalse($requisicao->isRecebido());

        $requisicao->data_recepcao = now();
        $requisicao->save();

        $this->assertTrue($requisicao->isRecebido());
    }

    /**
     * Teste para scope de requisições ativas
     */
    public function test_scope_requisicoes_ativas(): void
    {
        $user = User::factory()->create();
        $livro = Livro::factory()->create();

        // Criar requisições com diferentes estados
        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'pendente',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'aprovada',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'devolvida',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'rejeitada',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $requisioesAtivas = Requisicao::ativas()->get();

        $this->assertCount(2, $requisioesAtivas);
    }

    /**
     * Teste para scope de requisições passadas
     */
    public function test_scope_requisicoes_passadas(): void
    {
        $user = User::factory()->create();
        $livro = Livro::factory()->create();

        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'pendente',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'devolvida',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'rejeitada',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $requisicoesPassadas = Requisicao::passadas()->get();

        $this->assertCount(2, $requisicoesPassadas);
    }

    /**
     * Teste para relações do modelo
     */
    public function test_relacoes_do_modelo(): void
    {
        $user = User::factory()->create();
        $livro = Livro::factory()->create();
        $admin = User::factory()->admin()->create();

        $requisicao = Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'estado' => 'devolvida',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
            'data_recepcao' => now(),
            'recebido_por' => $admin->id,
        ]);

        $this->assertInstanceOf(User::class, $requisicao->user);
        $this->assertEquals($user->id, $requisicao->user->id);

        $this->assertInstanceOf(Livro::class, $requisicao->livro);
        $this->assertEquals($livro->id, $requisicao->livro->id);

        $this->assertInstanceOf(User::class, $requisicao->recebidoPor);
        $this->assertEquals($admin->id, $requisicao->recebidoPor->id);
    }
}
