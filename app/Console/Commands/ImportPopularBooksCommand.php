<?php

namespace App\Console\Commands;

use App\Jobs\ImportPopularBooksJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ImportPopularBooksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:import-popular {--force : Force reimport even if already done}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import ~200 popular books from Google Books API';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $alreadyImported = Cache::get('popular_books_imported', false);
        $isImporting = Cache::get('popular_books_importing', false);

        if ($this->option('force')) {
            $this->warn('Forçando reimportação...');
            Cache::forget('popular_books_imported');
            Cache::forget('popular_books_importing');
        } elseif ($alreadyImported) {
            $this->info('Os livros populares já foram importados.');
            $this->info('Use --force para forçar uma nova importação.');
            return self::SUCCESS;
        } elseif ($isImporting) {
            $this->info('A importação já está em progresso...');
            return self::SUCCESS;
        }

        $this->info('Disparando job de importação de livros populares...');
        $this->info('Isso será executado em background.');
        $this->info('Acompanhe o progresso nos logs: storage/logs/laravel.log');

        ImportPopularBooksJob::dispatch();

        $this->info('✓ Job disparado com sucesso!');
        $this->newLine();
        $this->info('Dica: Execute "php artisan queue:work" para processar o job imediatamente.');

        return self::SUCCESS;
    }
}
