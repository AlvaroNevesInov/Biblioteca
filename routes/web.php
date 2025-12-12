<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\EditoraController;
use App\Http\Controllers\LivroExportController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\RequisicaoController;
use App\Http\Controllers\CidadaoController;
use App\Http\Controllers\GoogleBooksController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AvailabilityAlertController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EncomendaController;

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

        // Rotas de Google Books API (pesquisa e importação) - apenas admin
        Route::prefix('google-books')->name('google-books.')->group(function () {
            Route::get('/', [GoogleBooksController::class, 'index'])->name('index');
            Route::get('/search', [GoogleBooksController::class, 'search'])->name('search');
            Route::get('/isbn', [GoogleBooksController::class, 'searchByIsbn'])->name('search.isbn');
            Route::get('/{volumeId}', [GoogleBooksController::class, 'show'])->name('show');
            Route::post('/import', [GoogleBooksController::class, 'import'])->name('import');
        });

        // API endpoints para Google Books (para uso em autocomplete, etc)
        Route::get('/api/google-books/search', [GoogleBooksController::class, 'apiSearch'])->name('api.google-books.search');

        // Rotas de Livros (criar, editar, eliminar)
        Route::resource('livros', LivroController::class)->except(['index', 'show']);

        // Rotas para criar autor e editora inline durante criação de livro

        Route::post('/livros/criar-editora', [LivroController::class, 'criarEditora'])->name('livros.criar-editora');

        Route::post('/livros/criar-autor', [LivroController::class, 'criarAutor'])->name('livros.criar-autor');

        // Rotas de Autores (criar, editar, eliminar)
        Route::resource('autores', AutorController::class)->except(['index', 'show']);

        // Rotas de Editoras (criar, editar, eliminar)
        Route::resource('editoras', EditoraController::class)->except(['index', 'show']);

        // Rotas de Gestão de Utilizadores (apenas para admins)
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('users', UserManagementController::class)->except(['show']);
        });

        // Rotas de Requisições - Ações Admin
        Route::patch('/requisicoes/{requisicao}/aprovar', [RequisicaoController::class, 'aprovar'])
            ->name('requisicoes.aprovar');
        Route::patch('/requisicoes/{requisicao}/rejeitar', [RequisicaoController::class, 'rejeitar'])
            ->name('requisicoes.rejeitar');
        Route::patch('/requisicoes/{requisicao}/devolver', [RequisicaoController::class, 'devolver'])
            ->name('requisicoes.devolver');
         Route::patch('/requisicoes/{requisicao}/confirmar-recepcao', [RequisicaoController::class, 'confirmarRecepcao'])
            ->name('requisicoes.confirmar-recepcao');
            // Rotas de Cidadãos (listagem e detalhes - apenas admin)

        Route::get('/cidadaos', [CidadaoController::class, 'index'])->name('cidadaos.index');
        Route::get('/cidadaos/{cidadao}', [CidadaoController::class, 'show'])->name('cidadaos.show');

        // Rotas de Reviews - Gestão Admin
        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::get('/reviews/{review}', [ReviewController::class, 'show'])->name('reviews.show');
        Route::post('/reviews/{review}/aprovar', [ReviewController::class, 'aprovar'])->name('reviews.aprovar');
        Route::post('/reviews/{review}/recusar', [ReviewController::class, 'recusar'])->name('reviews.recusar');
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
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

    // Importar e requisitar livro da Google Books (todos podem fazer)
    Route::post('/google-books/import-and-request', [GoogleBooksController::class, 'importAndRequest'])
        ->name('google-books.import-and-request');

    // Rotas de Requisições (todos podem ver as suas e criar novas)
    Route::resource('requisicoes', RequisicaoController::class)

        ->except(['show'])
        ->parameters(['requisicoes' => 'requisicao']);

    // Rotas de Reviews - Cidadãos podem criar reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Rotas de Alertas de Disponibilidade (todos podem criar e gerir os seus alertas)
    Route::prefix('availability-alerts')->name('availability-alerts.')->group(function () {
        Route::get('/', [AvailabilityAlertController::class, 'index'])->name('index');
        Route::post('/', [AvailabilityAlertController::class, 'store'])->name('store');
        Route::delete('/', [AvailabilityAlertController::class, 'destroy'])->name('destroy');
    });

    // =========================================
    // ROTAS DO CARRINHO DE COMPRAS
    // =========================================
    Route::prefix('carrinho')->name('carrinho.')->group(function () {
        Route::get('/', [CarrinhoController::class, 'index'])->name('index');
        Route::post('/', [CarrinhoController::class, 'store'])->name('store');
        Route::put('/{carrinhoItem}', [CarrinhoController::class, 'update'])->name('update');
        Route::delete('/{carrinhoItem}', [CarrinhoController::class, 'destroy'])->name('destroy');
        Route::delete('/', [CarrinhoController::class, 'clear'])->name('clear');
    });

    // =========================================
    // ROTAS DE CHECKOUT
    // =========================================
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/shipping', [CheckoutController::class, 'showShipping'])->name('shipping');
        Route::post('/shipping', [CheckoutController::class, 'processShipping'])->name('shipping.process');

        // Rotas de callback do Stripe
        Route::get('/stripe/success', [CheckoutController::class, 'stripeSuccess'])->name('stripe.success');
        Route::get('/stripe/cancel', [CheckoutController::class, 'stripeCancel'])->name('stripe.cancel');

        // Rotas antigas (manter para compatibilidade)
        Route::get('/payment', [CheckoutController::class, 'showPayment'])->name('payment');
        Route::post('/payment', [CheckoutController::class, 'processPayment'])->name('payment.process');

        Route::get('/success/{encomenda}', [CheckoutController::class, 'success'])->name('success');
    });

    // =========================================
    // ROTAS DE ENCOMENDAS
    // =========================================
    Route::prefix('encomendas')->name('encomendas.')->group(function () {
        Route::get('/', [EncomendaController::class, 'index'])->name('index');
        Route::get('/{encomenda}', [EncomendaController::class, 'show'])->name('show');

        // Apenas admin pode atualizar o estado
        Route::middleware(['admin'])->group(function () {
            Route::patch('/{encomenda}/status', [EncomendaController::class, 'updateStatus'])->name('updateStatus');
        });
    });
});
