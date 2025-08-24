<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Consolidação completa do sistema de roles do SAGA
     * 
     * Esta migration consolida as seguintes migrations:
     * - 2025_08_03_184142_simple_update_role_to_manager.php
     * - 2025_08_03_192650_add_superuser_role_to_users_table.php
     * - 2025_08_15_224916_update_users_role_constraint_add_furriel.php
     * - 2025_08_19_000001_update_users_role_constraint_add_sgtte.php
     * - 2025_08_22_155958_rename_superuser_role_to_aprov.php
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // Remover constraint existente se houver
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            
            // Aplicar a constraint final com todos os roles suportados
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('user','manager','aprov','furriel','sgtte'))");
        }
        
        // Nota: Os dados de usuários já estão corretos no estado atual
        // Esta migration apenas consolida as constraints de role em uma única operação
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // Remover constraint consolidada
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            
            // Restaurar constraint original com apenas user
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('user'))");
        }
    }
};
