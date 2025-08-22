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
            // Remover a constraint de check existente (não suportado no sqlite in-memory de testes)
            DB::statement('ALTER TABLE users DROP CONSTRAINT users_role_check');
        }
        
        // Atualizar todos os registros de 'superuser' para 'manager'
        DB::table('users')->where('role', 'superuser')->update(['role' => 'manager']);
        
        if (DB::getDriverName() !== 'sqlite') {
            // Adicionar nova constraint que aceita 'user' e 'manager'
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('user', 'manager'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // Remover a constraint atual
            DB::statement('ALTER TABLE users DROP CONSTRAINT users_role_check');
        }
        
        // Reverter: alterar 'manager' de volta para 'superuser'
        DB::table('users')->where('role', 'manager')->update(['role' => 'superuser']);
        
        if (DB::getDriverName() !== 'sqlite') {
            // Restaurar constraint original
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('user', 'superuser'))");
        }
    }
};
