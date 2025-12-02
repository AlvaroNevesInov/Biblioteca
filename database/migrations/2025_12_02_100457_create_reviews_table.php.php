<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('livro_id')->constrained('livros')->onDelete('cascade');
            $table->foreignId('requisicao_id')->constrained('requisicoes')->onDelete('cascade');
            $table->text('comentario');
            $table->enum('estado', ['suspenso', 'ativo', 'recusado'])->default('suspenso');
            $table->text('justificacao_recusa')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'livro_id', 'requisicao_id']);
            $table->index('livro_id');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
