<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Este seeder agora apenas documenta os roles disponíveis
        // Os roles são gerenciados diretamente no campo 'role' da tabela users
        
        // Roles disponíveis:
        // 'user' - Usuário padrão (militares)
        // 'manager' - Administrador do sistema
        
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
                'password' => 'admin123',
            ]);
            echo "Usuário admin criado: admin@saga.mil.br / admin123\n";
        } else {
            // Se já existe, garante que tenha senha definida para login tradicional
            $changed = false;
            if (empty($admin->password)) {
                $admin->password = 'admin123';
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
    }
}
