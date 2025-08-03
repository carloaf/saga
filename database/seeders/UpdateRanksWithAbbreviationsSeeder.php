<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rank;

class UpdateRanksWithAbbreviationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rankAbbreviations = [
            // Oficiais
            'Tenente-Coronel' => 'Ten Cel',
            'Major' => 'Maj',
            'Capitão' => 'Cap',
            'Primeiro-Tenente' => '1º Ten',
            'Segundo-Tenente' => '2º Ten',
            
            // Praças
            'Primeiro-Sargento' => '1º Sgt',
            'Segundo-Sargento' => '2º Sgt',
            'Terceiro-Sargento' => '3º Sgt',
            'Cabo' => 'Cb',
            'Soldado' => 'Sd',
            
            // FAB específicos
            'Brigadeiro' => 'Brig',
            'Coronel' => 'Cel',
            'Suboficial' => 'SO',
            'Sargento' => 'Sgt',
            'Soldado de Primeira Classe' => 'Sd 1ª Cl',
            'Soldado de Segunda Classe' => 'Sd 2ª Cl',
            
            // MB específicos
            'Almirante' => 'Alte',
            'Capitão de Mar e Guerra' => 'CMG',
            'Capitão de Fragata' => 'CF',
            'Capitão de Corveta' => 'CC',
            'Capitão-Tenente' => 'CT',
            'Primeiro-Tenente' => '1º Ten',
            'Segundo-Tenente' => '2º Ten',
            'Suboficial' => 'SO',
            'Primeiro-Sargento' => '1º Sgt',
            'Segundo-Sargento' => '2º Sgt',
            'Terceiro-Sargento' => '3º Sgt',
            'Cabo' => 'Cb',
            'Marinheiro-Recruta' => 'MN-RC',
            'Marinheiro' => 'MN',
            
            // EB específicos
            'General' => 'Gen',
            'Coronel' => 'Cel',
            'Tenente-Coronel' => 'Ten Cel',
            'Major' => 'Maj',
            'Capitão' => 'Cap',
            'Primeiro-Tenente' => '1º Ten',
            'Segundo-Tenente' => '2º Ten',
            'Subtenente' => 'ST',
            'Primeiro-Sargento' => '1º Sgt',
            'Segundo-Sargento' => '2º Sgt',
            'Terceiro-Sargento' => '3º Sgt',
            'Cabo' => 'Cb',
            'Soldado' => 'Sd',
        ];

        foreach ($rankAbbreviations as $rankName => $abbreviation) {
            $rank = Rank::where('name', 'LIKE', "%{$rankName}%")->first();
            if ($rank) {
                $rank->update(['abbreviation' => $abbreviation]);
                $this->command->info("Atualizado: {$rank->name} -> {$abbreviation}");
            }
        }

        // Para ranks que não foram encontrados acima, vamos tentar algumas variações comuns
        $ranks = Rank::whereNull('abbreviation')->get();
        foreach ($ranks as $rank) {
            $name = $rank->name;
            $abbreviation = null;

            // Padrões específicos
            if (str_contains($name, 'Tenente') && str_contains($name, '1') || str_contains($name, 'Primeiro')) {
                $abbreviation = '1º Ten';
            } elseif (str_contains($name, 'Tenente') && str_contains($name, '2') || str_contains($name, 'Segundo')) {
                $abbreviation = '2º Ten';
            } elseif (str_contains($name, 'Capitão')) {
                $abbreviation = 'Cap';
            } elseif (str_contains($name, 'Major')) {
                $abbreviation = 'Maj';
            } elseif (str_contains($name, 'Coronel') && !str_contains($name, 'Tenente')) {
                $abbreviation = 'Cel';
            } elseif (str_contains($name, 'Tenente-Coronel') || str_contains($name, 'Tenente Coronel')) {
                $abbreviation = 'Ten Cel';
            } elseif (str_contains($name, 'Sargento') && (str_contains($name, '1') || str_contains($name, 'Primeiro'))) {
                $abbreviation = '1º Sgt';
            } elseif (str_contains($name, 'Sargento') && (str_contains($name, '2') || str_contains($name, 'Segundo'))) {
                $abbreviation = '2º Sgt';
            } elseif (str_contains($name, 'Sargento') && (str_contains($name, '3') || str_contains($name, 'Terceiro'))) {
                $abbreviation = '3º Sgt';
            } elseif (str_contains($name, 'Cabo')) {
                $abbreviation = 'Cb';
            } elseif (str_contains($name, 'Soldado')) {
                $abbreviation = 'Sd';
            }

            if ($abbreviation) {
                $rank->update(['abbreviation' => $abbreviation]);
                $this->command->info("Inferido: {$rank->name} -> {$abbreviation}");
            }
        }
    }
}
