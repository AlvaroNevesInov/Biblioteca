<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\EditoraController;

Route::get('/', function () {
    return view('home');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rotas de Livros
    Route::resource('livros', LivroController::class);

    // Rotas de Autores
    Route::resource('autores', AutorController::class);

    // Rotas de Editoras
    Route::resource('editoras', EditoraController::class);
});
