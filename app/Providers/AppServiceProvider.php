<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Laravel\Fortify\Fortify;
use Livewire\Livewire;
use Illuminate\Auth\Events\Login;
use App\Listeners\TriggerPopularBooksImport;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Registrar listener para importação de livros populares no login
        Event::listen(Login::class, TriggerPopularBooksImport::class);

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
    }
}
