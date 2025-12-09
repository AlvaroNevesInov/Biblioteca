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
        Schema::create('encomendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('numero_encomenda')->unique();

            // Informações de entrega
            $table->string('nome_completo');
            $table->string('email');
            $table->string('telefone')->nullable();
            $table->text('morada');
            $table->string('cidade');
            $table->string('codigo_postal');
            $table->string('pais')->default('Portugal');

            // Informações financeiras
            $table->decimal('subtotal', 10, 2);
            $table->decimal('taxas', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Estado e pagamento
            $table->enum('estado', ['pendente', 'paga', 'processando', 'enviada', 'entregue', 'cancelada'])->default('pendente');
            $table->string('stripe_payment_intent_id')->nullable();

            // Notas adicionais
            $table->text('notas')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encomendas');
    }
};
