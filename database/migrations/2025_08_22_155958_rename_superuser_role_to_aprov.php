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
        if (DB::getDriverName() !== 'sqlite') {
            // Primeiro, atualizar todos os registros existentes de 'superuser' para 'aprov'
            DB::table('users')->where('role', 'superuser')->update(['role' => 'aprov']);
            
            // Remover constraint atual
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            
            // Adicionar nova constraint com 'aprov' ao invés de 'superuser'
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('user','manager','aprov','furriel','sgtte'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // Reverter registros de 'aprov' para 'superuser'
            DB::table('users')->where('role', 'aprov')->update(['role' => 'superuser']);
            
            // Remover constraint atual
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            
            // Restaurar constraint anterior com 'superuser'
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('user','manager','superuser','furriel','sgtte'))");
        }
    }
};
