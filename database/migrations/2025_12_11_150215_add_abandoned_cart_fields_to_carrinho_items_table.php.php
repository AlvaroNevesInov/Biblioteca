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
        Schema::table('carrinho_items', function (Blueprint $table) {
            $table->timestamp('abandoned_cart_email_sent_at')->nullable()->after('quantidade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carrinho_items', function (Blueprint $table) {
            $table->dropColumn('abandoned_cart_email_sent_at');
        });
    }
};
