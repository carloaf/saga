<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rank;
use App\Models\Organization;
use Illuminate\Support\Str;

class SgtteSeeder extends Seeder
{
    public function run(): void
    {
        $rank = Rank::firstOrCreate(['name' => '3º Sargento'], ['order' => 50]);
        $org = Organization::firstOrCreate(['name' => 'Companhia Alpha'], ['is_host' => false]);

        User::firstOrCreate(
            ['email' => 'sgtte@saga.mil.br'],
            [
                'google_id' => 'sgtte_seed_'.time(),
                'full_name' => 'Sargenteante Companhia',
                'war_name' => 'SGTTE',
                'rank_id' => $rank->id,
                'organization_id' => $org->id,
                'subunit' => 'CIA ALPHA',
                'armed_force' => 'EB',
                'gender' => 'M',
                'ready_at_om_date' => now(),
                'is_active' => true,
                'role' => 'sgtte',
                'password' => bcrypt(Str::random(12))
            ]
        );
    }
}
