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
        Schema::table('users', function (Blueprint $table) {
            // Adicionar campo de senha
            $table->string('password')->nullable()->after('email_verified_at');
            
            // Tornar google_id nullable para permitir cadastro tradicional
            $table->string('google_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remover campo de senha
            $table->dropColumn('password');
            
            // Voltar google_id para obrigatório
            $table->string('google_id')->nullable(false)->change();
        });
    }
};
