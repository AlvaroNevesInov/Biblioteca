<?php

namespace App\Console\Commands;

use App\Jobs\SyncActiveBooksJob;
use Illuminate\Console\Command;

class SyncActiveBooksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:active-books
                            {--queue : Executar em background via queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza livros ativos (acessados nos últimos 30 dias) com a Google Books API';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔄 Iniciando sincronização de livros ativos...');

        if ($this->option('queue')) {
            SyncActiveBooksJob::dispatch();
            $this->info('✅ Job de sincronização enviado para a queue!');
            $this->info('💡 Use "php artisan queue:work" para processar');
        } else {
            $this->info('⚙️  Executando sincronização de forma síncrona...');
            $this->warn('⏱️  Isso pode demorar alguns minutos...');

            $job = new SyncActiveBooksJob();
            $job->handle(app(\App\Services\GoogleBooksService::class));

            $this->info('✅ Sincronização concluída!');
            $this->info('📋 Verifique os logs para detalhes: storage/logs/laravel.log');
        }

        return Command::SUCCESS;
    }
}
