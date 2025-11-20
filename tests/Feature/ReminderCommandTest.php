<?php

namespace Tests\Feature;

use App\Mail\ReminderDevolucao;
use App\Models\Requisicao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReminderCommandTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste: Comando envia reminders para requisições com devolução amanhã
     */
    public function test_comando_envia_reminders_para_requisicoes_com_devolucao_amanha(): void
    {
        Mail::fake();

        // Criar requisições aprovadas com devolução amanhã
        $requisicao1 = Requisicao::factory()->aprovada()->create([
            'data_prevista_devolucao' => now()->addDay(),
        ]);

        $requisicao2 = Requisicao::factory()->aprovada()->create([
            'data_prevista_devolucao' => now()->addDay(),
        ]);

        // Executar comando
        $exitCode = Artisan::call('reminders:devolucao');

        $this->assertEquals(0, $exitCode);

        // Verificar que 2 emails foram enviados em fila
        Mail::assertQueued(ReminderDevolucao::class, 2);
    }

    /**
     * Teste: Comando não envia reminders para requisições com devolução em outros dias
     */
    public function test_comando_nao_envia_reminders_para_outras_datas(): void
    {
        Mail::fake();

        // Requisição com devolução hoje
        Requisicao::factory()->aprovada()->create([
            'data_prevista_devolucao' => now(),
        ]);

        // Requisição com devolução em 2 dias
        Requisicao::factory()->aprovada()->create([
            'data_prevista_devolucao' => now()->addDays(2),
        ]);

        // Requisição com devolução há 1 dia (atrasada)
        Requisicao::factory()->aprovada()->create([
            'data_prevista_devolucao' => now()->subDay(),
        ]);

        // Executar comando
        Artisan::call('reminders:devolucao');

        // Nenhum email deve ser enviado
        Mail::assertNothingQueued();
    }

    /**
     * Teste: Comando não envia reminders para requisições não aprovadas
     */
    public function test_comando_nao_envia_reminders_para_requisicoes_nao_aprovadas(): void
    {
        Mail::fake();

        // Requisição pendente com devolução amanhã
        Requisicao::factory()->create([
            'estado' => 'pendente',
            'data_prevista_devolucao' => now()->addDay(),
        ]);

        // Requisição rejeitada com devolução amanhã
        Requisicao::factory()->rejeitada()->create([
            'data_prevista_devolucao' => now()->addDay(),
        ]);

        // Requisição devolvida com devolução amanhã
        Requisicao::factory()->devolvida()->create([
            'data_prevista_devolucao' => now()->addDay(),
        ]);

        // Executar comando
        Artisan::call('reminders:devolucao');

        // Nenhum email deve ser enviado
        Mail::assertNothingQueued();
    }

    /**
     * Teste: Comando retorna sucesso quando não há requisições
     */
    public function test_comando_retorna_sucesso_quando_nao_ha_requisicoes(): void
    {
        Mail::fake();

        // Executar comando sem requisições
        $exitCode = Artisan::call('reminders:devolucao');

        $this->assertEquals(0, $exitCode);

        // Verificar mensagem de saída
        $output = Artisan::output();
        $this->assertStringContainsString('Nenhuma requisição com devolução prevista para amanhã', $output);

        Mail::assertNothingQueued();
    }

    /**
     * Teste: Comando envia emails com delay escalonado
     */
    public function test_comando_envia_emails_com_delay_escalonado(): void
    {
        Mail::fake();

        // Criar 3 requisições aprovadas com devolução amanhã
        for ($i = 0; $i < 3; $i++) {
            Requisicao::factory()->aprovada()->create([
                'data_prevista_devolucao' => now()->addDay(),
            ]);
        }

        // Executar comando
        Artisan::call('reminders:devolucao');

        // Verificar que 3 emails foram enviados
        Mail::assertQueued(ReminderDevolucao::class, 3);

        // Verificar mensagem de sucesso no output
        $output = Artisan::output();
        $this->assertStringContainsString('Total de reminders agendados: 3', $output);
    }

    /**
     * Teste: Comando carrega relações necessárias (user e livro)
     */
    public function test_comando_carrega_relacoes_necessarias(): void
    {
        Mail::fake();

        $requisicao = Requisicao::factory()->aprovada()->create([
            'data_prevista_devolucao' => now()->addDay(),
        ]);

        // Executar comando
        Artisan::call('reminders:devolucao');

        // Verificar que email foi enviado com as relações carregadas
        Mail::assertQueued(ReminderDevolucao::class, function ($mail) use ($requisicao) {
            return $mail->hasTo($requisicao->user->email);
        });
    }

    /**
     * Teste: Comando exibe informações sobre cada reminder agendado
     */
    public function test_comando_exibe_informacoes_sobre_reminders(): void
    {
        Mail::fake();

        $requisicao = Requisicao::factory()->aprovada()->create([
            'data_prevista_devolucao' => now()->addDay(),
        ]);

        // Executar comando
        Artisan::call('reminders:devolucao');

        // Verificar output contém informações da requisição
        $output = Artisan::output();
        $this->assertStringContainsString($requisicao->user->name, $output);
        $this->assertStringContainsString($requisicao->livro->nome, $output);
        $this->assertStringContainsString('Reminder agendado', $output);
    }

    /**
     * Teste: Comando executa corretamente com múltiplas requisições do mesmo utilizador
     */
    public function test_comando_com_multiplas_requisicoes_mesmo_utilizador(): void
    {
        Mail::fake();

        $requisicao1 = Requisicao::factory()->aprovada()->create([
            'data_prevista_devolucao' => now()->addDay(),
        ]);

        // Segunda requisição do mesmo utilizador
        $requisicao2 = Requisicao::factory()->aprovada()->create([
            'user_id' => $requisicao1->user_id,
            'data_prevista_devolucao' => now()->addDay(),
        ]);

        // Executar comando
        Artisan::call('reminders:devolucao');

        // Verificar que 2 emails foram enviados
        Mail::assertQueued(ReminderDevolucao::class, 2);

        // Ambos para o mesmo utilizador
        Mail::assertQueued(ReminderDevolucao::class, function ($mail) use ($requisicao1) {
            return $mail->hasTo($requisicao1->user->email);
        });
    }
}
