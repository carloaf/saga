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
        // PostgreSQL: Primeiro, adicionar o novo valor ao enum
        DB::statement("ALTER TYPE user_role ADD VALUE 'manager'");
        
        // Depois, atualizar todos os registros de 'superuser' para 'manager'
        DB::table('users')->where('role', 'superuser')->update(['role' => 'manager']);
        
        // Criar um novo tipo enum sem 'superuser'
        DB::statement("CREATE TYPE user_role_new AS ENUM('user', 'manager')");
        
        // Alterar a coluna para usar o novo tipo
        DB::statement("ALTER TABLE users ALTER COLUMN role TYPE user_role_new USING role::text::user_role_new");
        
        // Remover o tipo antigo e renomear o novo
        DB::statement("DROP TYPE user_role");
        DB::statement("ALTER TYPE user_role_new RENAME TO user_role");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter: criar tipo com 'superuser'
        DB::statement("CREATE TYPE user_role_new AS ENUM('user', 'superuser')");
        
        // Alterar 'manager' de volta para 'superuser'
        DB::table('users')->where('role', 'manager')->update(['role' => 'superuser']);
        
        // Alterar a coluna para usar o tipo antigo
        DB::statement("ALTER TABLE users ALTER COLUMN role TYPE user_role_new USING role::text::user_role_new");
        
        // Remover o tipo atual e renomear
        DB::statement("DROP TYPE user_role");
        DB::statement("ALTER TYPE user_role_new RENAME TO user_role");
    }
};
