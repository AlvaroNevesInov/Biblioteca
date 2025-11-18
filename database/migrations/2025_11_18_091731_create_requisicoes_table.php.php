<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisicoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('livro_id')->constrained('livros')->onDelete('cascade');
            $table->enum('estado', ['pendente', 'aprovada', 'rejeitada', 'devolvida'])->default('pendente');
            $table->date('data_requisicao');
            $table->date('data_prevista_devolucao')->nullable();
            $table->date('data_devolucao')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            // Índice para otimizar queries
            $table->index(['livro_id', 'estado']);
            $table->index(['user_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisicoes');
    }
};
