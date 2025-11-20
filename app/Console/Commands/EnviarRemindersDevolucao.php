<?php

namespace App\Console\Commands;

use App\Mail\ReminderDevolucao;
use App\Models\Requisicao;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EnviarRemindersDevolucao extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:devolucao';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia emails de lembrete para cidadãos com devolução prevista para amanhã';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Data de amanhã
        $amanha = Carbon::tomorrow()->toDateString();

        // Buscar requisições aprovadas com data prevista de devolução amanhã
        $requisicoes = Requisicao::where('estado', 'aprovada')
            ->whereDate('data_prevista_devolucao', $amanha)
            ->with(['user', 'livro.autores'])
            ->get();

        if ($requisicoes->isEmpty()) {
            $this->info('Nenhuma requisição com devolução prevista para amanhã.');
            return Command::SUCCESS;
        }

        $contador = 0;

        $delay = 10; // Começar com 10 segundos de delay

        foreach ($requisicoes as $requisicao) {

            try {
                // Enviar email com delay escalonado para evitar rate limit
                Mail::to($requisicao->user->email)
                    ->later(now()->addSeconds($delay), new ReminderDevolucao($requisicao));

                $contador++;

                $this->info("Reminder agendado para: {$requisicao->user->name} - Livro: {$requisicao->livro->nome} (delay: {$delay}s)");

                // Incrementar delay em 10 segundos para cada email adicional

                $delay += 10;

            } catch (\Exception $e) {

                $this->error("Erro ao enviar reminder para {$requisicao->user->name}: {$e->getMessage()}");

            }

        }

        $this->info("Total de reminders agendados: {$contador}");

        return Command::SUCCESS;
    }
}
