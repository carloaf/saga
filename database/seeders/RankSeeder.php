<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rank;

class RankSeeder extends Seeder
{
    public function run()
    {
        $ranks = [
            ['name' => 'General de Exército', 'order' => 1],
            ['name' => 'General de Divisão', 'order' => 2],
            ['name' => 'General de Brigada', 'order' => 3],
            ['name' => 'Coronel', 'order' => 4],
            ['name' => 'Tenente-Coronel', 'order' => 5],
            ['name' => 'Major', 'order' => 6],
            ['name' => 'Capitão', 'order' => 7],
            ['name' => '1º Tenente', 'order' => 8],
            ['name' => '2º Tenente', 'order' => 9],
            ['name' => 'Aspirante a Oficial', 'order' => 10],
            ['name' => 'Subtenente', 'order' => 11],
            ['name' => '1º Sargento', 'order' => 12],
            ['name' => '2º Sargento', 'order' => 13],
            ['name' => '3º Sargento', 'order' => 14],
            ['name' => 'Cabo', 'order' => 15],
            ['name' => 'Soldado', 'order' => 16],
        ];

        foreach ($ranks as $rank) {
            Rank::create($rank);
        }
    }
}
