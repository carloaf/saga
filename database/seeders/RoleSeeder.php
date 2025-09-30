<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Este seeder agora apenas documenta os roles disponíveis
        // Os roles são gerenciados diretamente no campo 'role' da tabela users
        
        // Roles disponíveis:
        // 'user' - Usuário padrão (militares)
        // 'manager' - Administrador do sistema
        
        $defaultPassword = '12345678';

        // Criar usuário admin padrão se não existir
        $admin = User::where('email', 'admin@saga.mil.br')->first();

        if (!$admin) {
            $rankId = \App\Models\Rank::orderBy('id')->value('id') ?? 1;
            $orgId = \App\Models\Organization::orderBy('id')->value('id');
            $admin = User::create([
                'full_name' => 'Administrador SAGA',
                'war_name' => 'ADMIN',
                'email' => 'admin@saga.mil.br',
                'google_id' => 'admin_saga_system',
                'idt' => 'ADM'.str_pad((string)random_int(0,99999),5,'0',STR_PAD_LEFT),
                'rank_id' => $rankId,
                'organization_id' => $orgId,
                'gender' => 'M',
                'ready_at_om_date' => now()->format('Y-m-d'),
                'role' => 'manager',
                'is_active' => true,
                // Atributo password: cast hashed no model cuidará do hash
                'password' => $defaultPassword,
            ]);
            echo "Usuário admin criado: admin@saga.mil.br / {$defaultPassword}\n";
        } else {
            // Se já existe, garante que tenha senha definida para login tradicional
            $changed = false;
            if (empty($admin->password) || !Hash::check($defaultPassword, $admin->password)) {
                $admin->password = $defaultPassword;
                $changed = true;
            }
            if (empty($admin->idt)) {
                $admin->idt = 'ADM'.str_pad((string)random_int(0,99999),5,'0',STR_PAD_LEFT);
                $changed = true;
            }
            if (empty($admin->organization_id)) {
                $admin->organization_id = \App\Models\Organization::orderBy('id')->value('id');
                $changed = true;
            }
            if (empty($admin->rank_id)) {
                $admin->rank_id = \App\Models\Rank::orderBy('id')->value('id');
                $changed = true;
            }
            if ($changed) {
                $admin->save();
                echo "Admin atualizado (senha/idt).\n";
            } else {
                echo "Usuário admin já existe completo.\n";
            }
        }

        // Criar/atualizar usuário furriel padrão
        $furriel = User::where('email', 'furriel@saga.mil.br')->first();

        if (!$furriel) {
            $rankId = \App\Models\Rank::orderBy('id')->value('id') ?? 1;
            $orgId = \App\Models\Organization::orderBy('id')->value('id');

            User::create([
                'full_name' => 'Furriel SAGA',
                'war_name' => 'FURRIEL',
                'email' => 'furriel@saga.mil.br',
                'google_id' => 'furriel_saga_system',
                'idt' => 'FUR' . str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT),
                'rank_id' => $rankId,
                'organization_id' => $orgId,
                'gender' => 'M',
                'ready_at_om_date' => now()->format('Y-m-d'),
                'role' => 'furriel',
                'is_active' => true,
                'subunit' => '1ª Cia',
                'armed_force' => 'EB',
                'password' => $defaultPassword,
            ]);

            echo "Usuário furriel criado: furriel@saga.mil.br / {$defaultPassword}\n";
        } else {
            $changed = false;

            if (empty($furriel->password) || !Hash::check($defaultPassword, $furriel->password)) {
                $furriel->password = $defaultPassword;
                $changed = true;
            }

            if (empty($furriel->role) || $furriel->role !== 'furriel') {
                $furriel->role = 'furriel';
                $changed = true;
            }

            if (empty($furriel->organization_id)) {
                $furriel->organization_id = \App\Models\Organization::orderBy('id')->value('id');
                $changed = true;
            }

            if (empty($furriel->rank_id)) {
                $furriel->rank_id = \App\Models\Rank::orderBy('id')->value('id');
                $changed = true;
            }

            if (empty($furriel->ready_at_om_date)) {
                $furriel->ready_at_om_date = now()->format('Y-m-d');
                $changed = true;
            }

            if (empty($furriel->idt)) {
                $furriel->idt = 'FUR' . str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
                $changed = true;
            }

            if (empty($furriel->subunit)) {
                $furriel->subunit = '1ª Cia';
                $changed = true;
            }

            if (empty($furriel->armed_force)) {
                $furriel->armed_force = 'EB';
                $changed = true;
            }

            if ($changed) {
                $furriel->save();
                echo "Usuário furriel atualizado.\n";
            } else {
                echo "Usuário furriel já existe completo.\n";
            }
        }
    }
}
