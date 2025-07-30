<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;

class OrganizationSeeder extends Seeder
{
    public function run()
    {
        $organizations = [
            ['name' => '1º Batalhão de Infantaria', 'is_host' => false],
            ['name' => '2º Batalhão de Infantaria', 'is_host' => false],
            ['name' => '3º Batalhão de Infantaria', 'is_host' => false],
            ['name' => '1º Regimento de Cavalaria', 'is_host' => false],
            ['name' => '2º Regimento de Cavalaria', 'is_host' => false],
            ['name' => '1º Grupo de Artilharia', 'is_host' => false],
            ['name' => '2º Grupo de Artilharia', 'is_host' => false],
            ['name' => 'Batalhão de Engenharia', 'is_host' => false],
            ['name' => 'Batalhão de Comunicações', 'is_host' => false],
            ['name' => 'Batalhão Logístico', 'is_host' => false],
            ['name' => 'Hospital Militar', 'is_host' => false],
            ['name' => 'Academia Militar', 'is_host' => false],
            ['name' => '11º Depósito de Suprimento', 'is_host' => true],
        ];

        foreach ($organizations as $organization) {
            Organization::create($organization);
        }
    }
}
