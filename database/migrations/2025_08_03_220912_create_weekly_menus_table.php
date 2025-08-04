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
        Schema::create('weekly_menus', function (Blueprint $table) {
            $table->id();
            $table->date('week_start'); // Data de início da semana (segunda-feira)
            $table->json('menu_data'); // Dados do cardápio da semana em JSON
            $table->boolean('is_active')->default(true); // Se o cardápio está ativo
            $table->foreignId('created_by')->constrained('users'); // Usuário que criou
            $table->foreignId('updated_by')->nullable()->constrained('users'); // Último usuário que editou
            $table->timestamps();
            
            // Índices para performance
            $table->index('week_start');
            $table->index(['week_start', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_menus');
    }
};
