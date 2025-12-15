<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Fortify;
use Livewire\Livewire;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Listeners\TriggerPopularBooksImport;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use App\Models\Encomenda;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Registrar listener para importação de livros populares no login
        Event::listen(Login::class, TriggerPopularBooksImport::class);

        // Registrar listeners para logs de autenticação
        Event::listen(Login::class, LogSuccessfulLogin::class);
        Event::listen(Logout::class, LogSuccessfulLogout::class);

        // Compartilhar contagem de encomendas pendentes com todas as views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                /** @var \App\Models\User $user */
                $user = Auth::user();
                $encomendasPendentesCount = 0;

                if (!$user->isAdmin()) {
                    $encomendasPendentesCount = $user->encomendas()
                        ->where('estado', 'pendente')
                        ->count();
                }

                $view->with('encomendasPendentesCount', $encomendasPendentesCount);
            } else {
                $view->with('encomendasPendentesCount', 0);
            }
        });

        // Personalização das views do Fortify (opcional)
        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::registerView(function () {
            return view('auth.register');
        });

        // Registar componentes Livewire manualmente
        Livewire::component('editoras-table', \App\Http\Livewire\EditorasTable::class);
        Livewire::component('autores-table', \App\Http\Livewire\AutoresTable::class);
        Livewire::component('livros-table', \App\Http\Livewire\LivrosTable::class);
        Livewire::component('users-table', \App\Http\Livewire\UsersTable::class);
        Livewire::component('requisicoes-table', \App\Http\Livewire\RequisicoesTable::class);
        Livewire::component('cidadaos-table', \App\Http\Livewire\CidadaosTable::class);
        Livewire::component('reviews-table', \App\Http\Livewire\ReviewsTable::class);
        Livewire::component('logs-table', \App\Http\Livewire\LogsTable::class);
    }
}
