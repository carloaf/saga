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
     * Esta migration copia os usuários do servidor de produção para o ambiente local
     * Dados coletados do servidor VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO em 16/10/2025
     */
    public function up(): void
    {
        // Mapeamento de IDs de ranks (produção -> local)
        $rankMapping = [
            17 => 12, // Subtenente: produção ID 17 -> local ID 12
            15 => 10, // 2º Tenente: produção ID 15 -> local ID 10
        ];
        
        // Mapeamento de organizations (produção -> local)
        $orgMapping = [
            1 => 1, // 11º D Sup: mesmo ID em ambos ambientes
        ];
        
        // Dados dos usuários de produção
        $productionUsers = [
            [
                'id' => 1,
                'google_id' => null,
                'full_name' => 'Carlos Augusto Alves Fernandes',
                'war_name' => 'Augusto',
                'email' => 'carloafernandes@gmail.com',
                'email_verified_at' => null,
                'avatar_url' => null,
                'rank_id_prod' => 17, // Será mapeado para ID local
                'organization_id_prod' => 1, // Será mapeado para ID local
                'gender' => 'M',
                'ready_at_om_date' => '2025-04-28',
                'role' => 'manager',
                'is_active' => true,
                'remember_token' => null,
                'created_at' => '2025-10-08 16:03:20',
                'updated_at' => '2025-10-08 13:09:51',
                'password' => '$2y$12$3/LGTxetp4Jx/0I67y.K8.mUUmqi5JGiLax2860cLAUJms5fo5Tfe',
                'subunit' => '1ª Cia',
                'armed_force' => 'EB',
                'status' => 'active',
                'idt' => '0334361045'
            ],
            [
                'id' => 2,
                'google_id' => null,
                'full_name' => 'Cleiton Paulo Martins',
                'war_name' => 'Martins',
                'email' => 'cleitonpaulo.martins@eb.mil.br',
                'email_verified_at' => null,
                'avatar_url' => null,
                'rank_id_prod' => 15, // Será mapeado para ID local
                'organization_id_prod' => 1, // Será mapeado para ID local
                'gender' => 'M',
                'ready_at_om_date' => '2024-03-08',
                'role' => 'manager',
                'is_active' => true,
                'remember_token' => null,
                'created_at' => '2025-10-08 13:44:38',
                'updated_at' => '2025-10-08 14:28:39',
                'password' => '$2y$12$/F3z1Cdb.P0skchIBAAu0OA8k4SD9qrEfWef9UhThagTSvbwsrLUC',
                'subunit' => '1ª Cia',
                'armed_force' => 'EB',
                'status' => null,
                'idt' => '1119803177'
            ]
        ];
        
        // Remover usuários de desenvolvimento locais se existirem
        $devEmails = ['admin@saga.mil.br', 'furriel@saga.mil.br'];
        foreach ($devEmails as $email) {
            DB::table('users')->where('email', $email)->delete();
        }
        
        // Inserir usuários de produção
        foreach ($productionUsers as $userData) {
            // Verificar se usuário já existe
            $existingUser = DB::table('users')->where('email', $userData['email'])->first();
            
            if ($existingUser) {
                echo "Usuário já existe: " . $userData['email'] . " - Atualizando...\n";
                
                // Atualizar usuário existente
                DB::table('users')
                    ->where('email', $userData['email'])
                    ->update([
                        'google_id' => $userData['google_id'],
                        'full_name' => $userData['full_name'],
                        'war_name' => $userData['war_name'],
                        'email_verified_at' => $userData['email_verified_at'],
                        'avatar_url' => $userData['avatar_url'],
                        'rank_id' => $rankMapping[$userData['rank_id_prod']],
                        'organization_id' => $orgMapping[$userData['organization_id_prod']],
                        'gender' => $userData['gender'],
                        'ready_at_om_date' => $userData['ready_at_om_date'],
                        'role' => $userData['role'],
                        'is_active' => $userData['is_active'],
                        'remember_token' => $userData['remember_token'],
                        'password' => $userData['password'],
                        'subunit' => $userData['subunit'],
                        'armed_force' => $userData['armed_force'],
                        'status' => $userData['status'],
                        'idt' => $userData['idt'],
                        'updated_at' => now(),
                    ]);
            } else {
                echo "Inserindo novo usuário: " . $userData['email'] . "\n";
                
                // Inserir novo usuário
                DB::table('users')->insert([
                    'google_id' => $userData['google_id'],
                    'full_name' => $userData['full_name'],
                    'war_name' => $userData['war_name'],
                    'email' => $userData['email'],
                    'email_verified_at' => $userData['email_verified_at'],
                    'avatar_url' => $userData['avatar_url'],
                    'rank_id' => $rankMapping[$userData['rank_id_prod']],
                    'organization_id' => $orgMapping[$userData['organization_id_prod']],
                    'gender' => $userData['gender'],
                    'ready_at_om_date' => $userData['ready_at_om_date'],
                    'role' => $userData['role'],
                    'is_active' => $userData['is_active'],
                    'remember_token' => $userData['remember_token'],
                    'password' => $userData['password'],
                    'subunit' => $userData['subunit'],
                    'armed_force' => $userData['armed_force'],
                    'status' => $userData['status'],
                    'idt' => $userData['idt'],
                    'created_at' => $userData['created_at'],
                    'updated_at' => $userData['updated_at'],
                ]);
            }
        }
        
        echo "Usuários de produção copiados com sucesso!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover usuários de produção
        $productionEmails = [
            'carloafernandes@gmail.com',
            'cleitonpaulo.martins@eb.mil.br'
        ];
        
        foreach ($productionEmails as $email) {
            DB::table('users')->where('email', $email)->delete();
        }
        
        // Recriar usuários de desenvolvimento
        $devUsers = [
            [
                'email' => 'admin@saga.mil.br',
                'google_id' => 'admin_dev_' . time(),
                'full_name' => 'Admin Development',
                'war_name' => 'ADMIN',
                'idt' => 'ADM' . str_pad((string)random_int(10000, 99999), 5, '0', STR_PAD_LEFT),
                'password' => bcrypt('12345678'),
                'rank_id' => 8, // Capitão
                'organization_id' => 1, // 11º D Sup
                'armed_force' => 'EB',
                'gender' => 'M',
                'ready_at_om_date' => now()->toDateString(),
                'is_active' => true,
                'role' => 'manager',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'furriel@saga.mil.br',
                'google_id' => 'furriel_dev_' . time(),
                'full_name' => 'Furriel SAGA',
                'war_name' => 'FURRIEL',
                'idt' => 'FUR' . str_pad((string)random_int(10000, 99999), 5, '0', STR_PAD_LEFT),
                'password' => bcrypt('12345678'),
                'rank_id' => 12, // 1º Sargento
                'organization_id' => 1, // 11º D Sup
                'armed_force' => 'EB',
                'gender' => 'M',
                'ready_at_om_date' => now()->toDateString(),
                'is_active' => true,
                'role' => 'furriel',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        
        foreach ($devUsers as $user) {
            DB::table('users')->insert($user);
        }
        
        echo "Usuários de desenvolvimento restaurados!\n";
    }
};