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
        if (!User::where('email', 'admin@saga.mil.br')->exists()) {
            User::create([
                'full_name' => 'Administrador SAGA',
                'war_name' => 'ADMIN',
                'email' => 'admin@saga.mil.br',
                'google_id' => 'admin_saga_system',
                'rank_id' => 1, // Assumindo que existe um rank
                'organization_id' => 1, // Assumindo que existe uma organização
                'gender' => 'M',
                'ready_at_om_date' => now()->format('Y-m-d'),
                'role' => 'manager',
                'is_active' => true,
            ]);
        }
    }
}
