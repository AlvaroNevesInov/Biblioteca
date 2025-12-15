<?php

namespace App\Listeners;

use App\Services\LogService;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        if ($event->user) {
            LogService::log(
                modulo: 'Autenticação',
                acao: 'Logout',
                objectId: $event->user->id,
                descricao: "Utilizador '{$event->user->name}' fez logout do sistema",
                userId: $event->user->id
            );
        }
    }
}
