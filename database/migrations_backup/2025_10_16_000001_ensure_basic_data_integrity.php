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
        // Esta migration garante que sempre existam dados básicos necessários
        // para o funcionamento da aplicação, evitando erros de foreign key
        
        // Garantir que existe pelo menos uma organização
        if (DB::table('organizations')->count() === 0) {
            DB::table('organizations')->insert([
                [
                    'name' => 'Comando Geral',
                    'abbreviation' => 'CG',
                    'is_host' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => '11º Depósito de Suprimento',
                    'abbreviation' => '11DSUP',
                    'is_host' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }

        // Garantir que existe pelo menos um rank
        if (DB::table('ranks')->count() === 0) {
            DB::table('ranks')->insert([
                [
                    'name' => 'Soldado EV',
                    'abbreviation' => 'Sd EV',
                    'order' => 16,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Capitão',
                    'abbreviation' => 'Cap',
                    'order' => 7,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }

        // Garantir que existe um usuário admin básico
        if (DB::table('users')->where('email', 'admin@saga.mil.br')->doesntExist()) {
            $orgId = DB::table('organizations')->where('name', 'Comando Geral')->value('id');
            $rankId = DB::table('ranks')->where('name', 'Capitão')->value('id');
            
            // Se não encontrar os IDs específicos, pega o primeiro disponível
            if (!$orgId) $orgId = DB::table('organizations')->value('id');
            if (!$rankId) $rankId = DB::table('ranks')->value('id');

            // Gerar IDT único
            do {
                $idt = 'ADM' . str_pad((string)random_int(10000, 99999), 5, '0', STR_PAD_LEFT);
            } while (DB::table('users')->where('idt', $idt)->exists());

            DB::table('users')->insert([
                'email' => 'admin@saga.mil.br',
                'google_id' => 'admin_system_' . time(),
                'full_name' => 'Administrador Sistema',
                'war_name' => 'ADMIN',
                'idt' => $idt,
                'password' => bcrypt('12345678'), // Senha padrão para desenvolvimento
                'rank_id' => $rankId,
                'organization_id' => $orgId,
                'armed_force' => 'EB',
                'gender' => 'M',
                'ready_at_om_date' => now()->toDateString(),
                'is_active' => true,
                'role' => 'manager',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não remover dados básicos no rollback para manter integridade
        // Em caso de necessidade, fazer limpeza manual
    }
};