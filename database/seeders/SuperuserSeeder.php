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
        // Criar usuário aprov exemplo
        User::create([
            'full_name' => 'João Silva Aprov',
            'war_name' => 'SILVA',
            'email' => 'aprov@saga.mil.br',
            'password' => bcrypt('123456'),
            'role' => 'aprov',
            'organization_id' => 1,
            'rank_id' => 1,
            'gender' => 'M',
            'ready_at_om_date' => now(),
            'is_active' => true,
            'subunit' => '1ª Cia',
            'armed_force' => 'EB',
        ]);

        echo "Usuário aprov criado: aprov@saga.mil.br / 123456\n";
    }
}
