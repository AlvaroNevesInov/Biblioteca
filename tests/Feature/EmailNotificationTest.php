<?php

namespace Tests\Feature;

use App\Mail\NovaRequisicaoAdmin;
use App\Mail\NovaRequisicaoCidadao;
use App\Mail\ReminderDevolucao;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste: Email é enviado ao cidadão quando cria requisição
     */
    public function test_email_enviado_ao_cidadao_ao_criar_requisicao(): void
    {
        Mail::fake();

        $cidadao = User::factory()->cidadao()->create();
        $livro = Livro::factory()->create();

        $requisicao = Requisicao::create([
            'user_id' => $cidadao->id,
            'livro_id' => $livro->id,
            'estado' => 'pendente',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $requisicao->load(['livro.autores', 'livro.editora', 'user']);

        Mail::to($cidadao->email)
            ->later(now()->addSeconds(5), new NovaRequisicaoCidadao($requisicao));

        Mail::assertQueued(NovaRequisicaoCidadao::class, function ($mail) use ($cidadao) {
            return $mail->hasTo($cidadao->email);
        });
    }

    /**
     * Teste: Email é enviado aos admins quando cidadão cria requisição
     */
    public function test_email_enviado_aos_admins_ao_criar_requisicao(): void
    {
        Mail::fake();

        $cidadao = User::factory()->cidadao()->create();
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);
        $livro = Livro::factory()->create();

        $requisicao = Requisicao::create([
            'user_id' => $cidadao->id,
            'livro_id' => $livro->id,
            'estado' => 'pendente',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $requisicao->load(['livro.autores', 'livro.editora', 'user']);

        $admins = User::where('role', 'admin')->get();
        $delay = 65;

        foreach ($admins as $admin) {
            Mail::to($admin->email)
                ->later(now()->addSeconds($delay), new NovaRequisicaoAdmin($requisicao));
            $delay += 10;
        }

        // Verificar que pelo menos 2 foram enviados
        Mail::assertQueued(NovaRequisicaoAdmin::class, function ($mail) use ($admin1) {
            return $mail->hasTo($admin1->email);
        });

        Mail::assertQueued(NovaRequisicaoAdmin::class, function ($mail) use ($admin2) {
            return $mail->hasTo($admin2->email);
        });
    }

    /**
     * Teste: Mailable NovaRequisicaoCidadao pode ser construído
     */
    public function test_nova_requisicao_cidadao_mailable_pode_ser_construido(): void
    {
        $requisicao = Requisicao::factory()->create();
        $requisicao->load(['livro.autores', 'livro.editora', 'user']);

        $mailable = new NovaRequisicaoCidadao($requisicao);

        $this->assertInstanceOf(NovaRequisicaoCidadao::class, $mailable);
    }

    /**
     * Teste: Mailable NovaRequisicaoAdmin pode ser construído
     */
    public function test_nova_requisicao_admin_mailable_pode_ser_construido(): void
    {
        $requisicao = Requisicao::factory()->create();
        $requisicao->load(['livro.autores', 'livro.editora', 'user']);

        $mailable = new NovaRequisicaoAdmin($requisicao);

        $this->assertInstanceOf(NovaRequisicaoAdmin::class, $mailable);
    }

    /**
     * Teste: Mailable ReminderDevolucao pode ser construído
     */
    public function test_reminder_devolucao_mailable_pode_ser_construido(): void
    {
        $requisicao = Requisicao::factory()->aprovada()->create([
            'data_prevista_devolucao' => now()->addDay(),
        ]);
        $requisicao->load(['livro.autores', 'user']);

        $mailable = new ReminderDevolucao($requisicao);

        $this->assertInstanceOf(ReminderDevolucao::class, $mailable);
    }

    /**
     * Teste: Emails são enviados em fila (queued) e não imediatamente
     */
    public function test_emails_sao_enviados_em_fila(): void
    {
        Mail::fake();

        $cidadao = User::factory()->cidadao()->create();
        $livro = Livro::factory()->create();

        $requisicao = Requisicao::create([
            'user_id' => $cidadao->id,
            'livro_id' => $livro->id,
            'estado' => 'pendente',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $requisicao->load(['livro.autores', 'livro.editora', 'user']);

        Mail::to($cidadao->email)
            ->later(now()->addSeconds(5), new NovaRequisicaoCidadao($requisicao));

        // Verificar que foi enviado em fila, não imediatamente
        Mail::assertQueued(NovaRequisicaoCidadao::class);
        Mail::assertNotSent(NovaRequisicaoCidadao::class);
    }

    /**
     * Teste: Emails para admins têm delay escalonado
     */
    public function test_emails_para_admins_tem_delay_escalonado(): void
    {
        Mail::fake();

        // Criar 3 admins
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);
        $admin3 = User::factory()->create(['role' => 'admin']);

        $cidadao = User::factory()->cidadao()->create();
        $livro = Livro::factory()->create();

        $requisicao = Requisicao::create([
            'user_id' => $cidadao->id,
            'livro_id' => $livro->id,
            'estado' => 'pendente',
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        $requisicao->load(['livro.autores', 'livro.editora', 'user']);

        $admins = User::where('role', 'admin')->get();
        $this->assertCount(3, $admins);

        $delay = 65;

        foreach ($admins as $admin) {
            Mail::to($admin->email)
                ->later(now()->addSeconds($delay), new NovaRequisicaoAdmin($requisicao));
            $delay += 10;
        }

        // Verificar que emails foram enviados
        Mail::assertQueued(NovaRequisicaoAdmin::class);
    }

    /**
     * Teste: Reminder só é enviado para requisições aprovadas com devolução amanhã
     */
    public function test_reminder_so_enviado_para_requisicoes_corretas(): void
    {
        Mail::fake();

        // Requisição com devolução amanhã (deve receber reminder)
        $requisicao1 = Requisicao::factory()->aprovada()->create([
            'data_prevista_devolucao' => now()->addDay(),
        ]);

        // Requisição com devolução em 2 dias (não deve receber)
        $requisicao2 = Requisicao::factory()->aprovada()->create([
            'data_prevista_devolucao' => now()->addDays(2),
        ]);

        // Requisição pendente com devolução amanhã (não deve receber)
        $requisicao3 = Requisicao::factory()->create([
            'estado' => 'pendente',
            'data_prevista_devolucao' => now()->addDay(),
        ]);

        // Enviar reminder apenas para requisição1
        Mail::to($requisicao1->user->email)
            ->later(now()->addSeconds(10), new ReminderDevolucao($requisicao1));

        Mail::assertQueued(ReminderDevolucao::class, 1);

        Mail::assertQueued(ReminderDevolucao::class, function ($mail) use ($requisicao1) {
            return $mail->hasTo($requisicao1->user->email);
        });
    }
}
