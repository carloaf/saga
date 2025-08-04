<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperuserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário superuser exemplo
        User::create([
            'full_name' => 'João Silva Superuser',
            'war_name' => 'SILVA',
            'email' => 'superuser@saga.mil.br',
            'password' => bcrypt('123456'),
            'role' => 'superuser',
            'organization_id' => 1,
            'rank_id' => 1,
            'gender' => 'male',
            'ready_at_om_date' => now(),
            'is_active' => true,
            'subunit' => '1ª Cia',
            'armed_force' => 'EB',
        ]);

        echo "Usuário superuser criado: superuser@saga.mil.br / 123456\n";
    }
}
