<?php



namespace App\Console\Commands;



use App\Mail\ReminderDevolucao;

use App\Models\Requisicao;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Mail;



class TestarReminderDevolucao extends Command

{

    /**

     * The name and signature of the console command.

     *

     * @var string

     */

    protected $signature = 'reminders:test {requisicao_id?}';


    /**

     * The console command description.

     *

     * @var string

     */

    protected $description = 'Testa o envio de email de reminder para uma requisição (ignora validação de data)';


    /**

     * Execute the console command.

     */

    public function handle()

    {

        $requisicaoId = $this->argument('requisicao_id');


        // Se foi especificado um ID, usar essa requisição

        if ($requisicaoId) {

            $requisicao = Requisicao::with(['user', 'livro.autores'])->find($requisicaoId);



            if (!$requisicao) {

                $this->error("Requisição #{$requisicaoId} não encontrada.");

                return Command::FAILURE;

            }

        } else {

            // Caso contrário, buscar uma requisição aprovada qualquer

            $requisicao = Requisicao::where('estado', 'aprovada')

                ->with(['user', 'livro.autores'])

                ->first();



            if (!$requisicao) {

                $this->error('Nenhuma requisição aprovada encontrada no sistema.');

                $this->info('Dica: Crie uma requisição e aprove-a primeiro, ou especifique um ID: php artisan reminders:test [ID]');

                return Command::FAILURE;

            }

        }

        // Mostrar informações da requisição

        $this->info('═══════════════════════════════════════════════════════════');

        $this->info('📧  TESTE DE EMAIL DE REMINDER DE DEVOLUÇÃO');

        $this->info('═══════════════════════════════════════════════════════════');

        $this->line('');

        $this->info("Requisição: #{$requisicao->id}");

        $this->info("Cidadão: {$requisicao->user->name} ({$requisicao->user->email})");

        $this->info("Livro: {$requisicao->livro->nome}");

        $this->info("Estado: {$requisicao->estado}");

        $this->info("Data Requisição: {$requisicao->data_requisicao->format('d/m/Y')}");

        $this->info("Data Prev. Devolução: {$requisicao->data_prevista_devolucao->format('d/m/Y')}");

        $this->line('');

        if ($requisicao->estado !== 'aprovada') {

            $this->warn("⚠️  AVISO: Esta requisição está '{$requisicao->estado}', não 'aprovada'.");

            $this->warn('   Normalmente apenas requisições aprovadas recebem reminders.');

            $this->line('');

        }

        // Confirmar envio

        if (!$this->confirm('Deseja enviar o email de reminder de teste?', true)) {

            $this->info('Cancelado pelo utilizador.');

            return Command::SUCCESS;

        }

        try {

            // Enviar email com delay de 60 segundos para evitar rate limit

            $this->info('Enviando email (com delay de 60s para evitar rate limit)...');



            Mail::to($requisicao->user->email)

                ->later(now()->addSeconds(60), new ReminderDevolucao($requisicao));



            $this->line('');

            $this->info('✅ Email de reminder enfileirado com sucesso!');

            $this->line('');

            $this->info('📬 Próximos passos:');

            $this->info("   1. Execute: php artisan queue:work");

            $this->info("   2. Aguarde ~10 segundos para o email ser processado");

            $this->info("   3. Verifique o email: {$requisicao->user->email}");

            $this->info("   4. Se usar Mailtrap/MailHog, acesse a interface web");

            $this->info("   5. Verifique os logs: storage/logs/laravel.log");

            $this->line('');



            return Command::SUCCESS;

        } catch (\Exception $e) {

            $this->error('❌ Erro ao enviar email:');

            $this->error($e->getMessage());

            $this->line('');

            $this->info('Verifique:');

            $this->info('   - Configurações de email no .env');

            $this->info('   - Queue está rodando (se necessário): php artisan queue:work');

            $this->info('   - Logs em: storage/logs/laravel.log');

            return Command::FAILURE;

        }
    }
}
