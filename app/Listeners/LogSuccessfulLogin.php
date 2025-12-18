<?php

namespace App\Listeners;

use App\Services\LogService;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        LogService::log(
            modulo: 'Autenticação',
            acao: 'Login',
            objectId: $event->user->id,
            descricao: "Utilizador '{$event->user->name}' fez login no sistema",
            userId: $event->user->id
        );
    }
}
