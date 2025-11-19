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
        Schema::table('requisicoes', function (Blueprint $table) {
            $table->date('data_recepcao')->nullable()->after('data_devolucao');

            $table->foreignId('recebido_por')->nullable()->after('data_recepcao')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requisicoes', function (Blueprint $table) {
             $table->dropForeign(['recebido_por']);

            $table->dropColumn(['data_recepcao', 'recebido_por']);
        });
    }
};
