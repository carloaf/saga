<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Esta migration sincroniza os dados com o servidor de produção
     * Organizations: 4 organizações específicas
     * Ranks: Mantém hierarquia mas ajusta IDs
     * Users: Mantém estrutura mas permite dados de produção
     */
    public function up(): void
    {
        // 1. AJUSTAR ORGANIZATIONS para corresponder ao servidor de produção
        
        // Primeiro, verificar se existem usuários vinculados a organizações que serão removidas
        $usersWithOldOrgs = DB::table('users')
            ->whereNotIn('organization_id', [1, 2, 3, 4])
            ->where('organization_id', '>', 4)
            ->get();
            
        // Reatribuir usuários para organizações que permanecerão
        foreach ($usersWithOldOrgs as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update(['organization_id' => 1]); // 11º D Sup
        }
        
        // Limpar organizations extras (manter apenas as 4 primeiras e ajustar seus dados)
        DB::table('organizations')->where('id', '>', 4)->delete();
        
        // Atualizar organizations para corresponder à produção
        $productionOrgs = [
            ['id' => 1, 'name' => '11º D Sup', 'abbreviation' => '11DSUP', 'is_host' => true],
            ['id' => 2, 'name' => 'CITEx', 'abbreviation' => 'CITEX', 'is_host' => false],
            ['id' => 3, 'name' => 'PMB', 'abbreviation' => 'PMB', 'is_host' => false],
            ['id' => 4, 'name' => '7º CTA', 'abbreviation' => '7CTA', 'is_host' => false],
        ];
        
        foreach ($productionOrgs as $org) {
            DB::table('organizations')
                ->updateOrInsert(
                    ['id' => $org['id']],
                    [
                        'name' => $org['name'],
                        'abbreviation' => $org['abbreviation'],
                        'is_host' => $org['is_host'],
                        'updated_at' => now(),
                        'created_at' => DB::table('organizations')->where('id', $org['id'])->value('created_at') ?: now(),
                    ]
                );
        }
        
        // 2. AJUSTAR RANKS para corresponder ao servidor de produção
        // Manter a estrutura hierárquica mas garantir que existam todos os ranks necessários
        
        $productionRanks = [
            ['name' => 'General de Exército', 'abbreviation' => 'Gen Ex', 'order' => 1],
            ['name' => 'General de Divisão', 'abbreviation' => 'Gen Div', 'order' => 2],
            ['name' => 'General de Brigada', 'abbreviation' => 'Gen Bda', 'order' => 3],
            ['name' => 'Coronel', 'abbreviation' => 'Cel', 'order' => 4],
            ['name' => 'Tenente-Coronel', 'abbreviation' => 'TC', 'order' => 5],
            ['name' => 'Major', 'abbreviation' => 'Maj', 'order' => 6],
            ['name' => 'Capitão', 'abbreviation' => 'Cap', 'order' => 7],
            ['name' => '1º Tenente', 'abbreviation' => '1º Ten', 'order' => 8],
            ['name' => '2º Tenente', 'abbreviation' => '2º Ten', 'order' => 9],
            ['name' => 'Aspirante a Oficial', 'abbreviation' => 'Asp Of', 'order' => 10],
            ['name' => 'Subtenente', 'abbreviation' => 'ST', 'order' => 11],
            ['name' => '1º Sargento', 'abbreviation' => '1º Sgt', 'order' => 12],
            ['name' => '2º Sargento', 'abbreviation' => '2º Sgt', 'order' => 13],
            ['name' => '3º Sargento', 'abbreviation' => '3º Sgt', 'order' => 14],
            ['name' => 'Cabo', 'abbreviation' => 'Cb', 'order' => 15],
            ['name' => 'Soldado', 'abbreviation' => 'Sd', 'order' => 16],
            ['name' => 'Soldado EV', 'abbreviation' => 'Sd EV', 'order' => 17],
        ];
        
        foreach ($productionRanks as $rank) {
            // Verificar se o rank já existe
            $existingRank = DB::table('ranks')->where('name', $rank['name'])->first();
            
            if (!$existingRank) {
                // Criar novo rank se não existir
                DB::table('ranks')->insert([
                    'name' => $rank['name'],
                    'abbreviation' => $rank['abbreviation'],
                    'order' => $rank['order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // Atualizar rank existente
                DB::table('ranks')
                    ->where('id', $existingRank->id)
                    ->update([
                        'abbreviation' => $rank['abbreviation'],
                        'order' => $rank['order'],
                        'updated_at' => now(),
                    ]);
            }
        }
        
        // 3. GARANTIR DADOS BÁSICOS DE USUÁRIOS
        // Não remover usuários existentes, mas garantir que existe pelo menos um admin funcional
        
        $adminExists = DB::table('users')->where('role', 'manager')->exists();
        
        if (!$adminExists) {
            // Garantir que existe pelo menos um rank e uma organização
            $rankId = DB::table('ranks')->where('name', 'Capitão')->value('id') 
                     ?: DB::table('ranks')->orderBy('order')->value('id')
                     ?: 1;
            $orgId = DB::table('organizations')->where('name', '11º D Sup')->value('id') ?: 1;
            
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
                'password' => bcrypt('12345678'),
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
        // Para rollback, restaurar dados originais dos seeders
        // Esta operação é complexa, melhor fazer backup antes da migration
        
        // Recriar organizations originais
        $originalOrgs = [
            ['name' => 'Comando Geral', 'abbreviation' => 'CG', 'is_host' => false],
            ['name' => '1º Batalhão de Infantaria', 'abbreviation' => '1º BI', 'is_host' => false],
            ['name' => '2º Batalhão de Infantaria', 'abbreviation' => '2º BI', 'is_host' => false],
            ['name' => '3º Batalhão de Infantaria', 'abbreviation' => '3º BI', 'is_host' => false],
            ['name' => '1º Regimento de Cavalaria', 'abbreviation' => '1º RCC', 'is_host' => false],
            ['name' => '2º Regimento de Cavalaria', 'abbreviation' => '2º RCC', 'is_host' => false],
            ['name' => '1º Grupo de Artilharia', 'abbreviation' => '1º GA', 'is_host' => false],
            ['name' => '2º Grupo de Artilharia', 'abbreviation' => '2º GA', 'is_host' => false],
            ['name' => 'Batalhão de Engenharia', 'abbreviation' => 'B Eng', 'is_host' => false],
            ['name' => 'Batalhão de Comunicações', 'abbreviation' => 'B Com', 'is_host' => false],
            ['name' => 'Batalhão Logístico', 'abbreviation' => 'B Log', 'is_host' => false],
            ['name' => 'Hospital Militar', 'abbreviation' => 'HM', 'is_host' => false],
            ['name' => 'Academia Militar', 'abbreviation' => 'AM', 'is_host' => false],
            ['name' => '11º Depósito de Suprimento', 'abbreviation' => '11º DSUP', 'is_host' => true],
        ];
        
        // Limpar organizations atuais
        DB::table('organizations')->truncate();
        
        // Reinserir organizations originais
        foreach ($originalOrgs as $index => $org) {
            DB::table('organizations')->insert([
                'id' => $index + 1,
                'name' => $org['name'],
                'abbreviation' => $org['abbreviation'],
                'is_host' => $org['is_host'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};