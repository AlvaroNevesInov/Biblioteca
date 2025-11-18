<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
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
    }
}
