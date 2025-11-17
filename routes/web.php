<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\EditoraController;
use App\Http\Controllers\LivroExportController;

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

    // =========================================
    // ROTAS APENAS PARA ADMIN
    // =========================================
    Route::middleware(['admin'])->group(function () {
        // Rota de exportação ANTES do resource (importante!)
        Route::get('/livros/exportar-excel', [LivroExportController::class, 'exportarExcel'])
            ->name('livros.export.excel');

        // Rotas de Livros (criar, editar, eliminar)
        Route::resource('livros', LivroController::class)->except(['index', 'show']);

        // Rotas de Autores (criar, editar, eliminar)
        Route::resource('autores', AutorController::class)->except(['index', 'show']);

        // Rotas de Editoras (criar, editar, eliminar)
        Route::resource('editoras', EditoraController::class)->except(['index', 'show']);
    });

    // =========================================
    // ROTAS PARA TODOS OS UTILIZADORES AUTENTICADOS
    // =========================================

    // Listar livros (todos podem ver)
    Route::get('/livros', [LivroController::class, 'index'])->name('livros.index');
    Route::get('/livros/{livro}', [LivroController::class, 'show'])->name('livros.show');

    // Listar autores (todos podem ver)
    Route::get('/autores', [AutorController::class, 'index'])->name('autores.index');
    Route::get('/autores/{autor}', [AutorController::class, 'show'])->name('autores.show');

    // Listar editoras (todos podem ver)
    Route::get('/editoras', [EditoraController::class, 'index'])->name('editoras.index');
    Route::get('/editoras/{editora}', [EditoraController::class, 'show'])->name('editoras.show');
});
