<?php

namespace App\Listeners;

use App\Jobs\ImportPopularBooksJob;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TriggerPopularBooksImport
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // Verificar e disparar importação de livros populares
        $alreadyImported = Cache::get('popular_books_imported', false);
        $isImporting = Cache::get('popular_books_importing', false);

        if (!$alreadyImported && !$isImporting) {
             // Marcar como "importing" ANTES de disparar o job para prevenir race condition

            Cache::put('popular_books_importing', true, now()->addHour());
            Log::info('Disparando importação de livros populares após login', [
                'user_id' => $event->user->id,
                'user_email' => $event->user->email,
            ]);

            ImportPopularBooksJob::dispatch();
        }
    }
}
