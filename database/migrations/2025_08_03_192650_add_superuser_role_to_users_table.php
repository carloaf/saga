<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remover constraint atual
        DB::statement('ALTER TABLE users DROP CONSTRAINT users_role_check');
        
        // Adicionar nova constraint que inclui 'superuser'
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('user', 'manager', 'superuser'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover constraint atual
        DB::statement('ALTER TABLE users DROP CONSTRAINT users_role_check');
        
        // Restaurar constraint anterior (apenas user e manager)
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('user', 'manager'))");
    }
};
