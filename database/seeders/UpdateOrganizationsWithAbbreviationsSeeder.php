<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organization;

class UpdateOrganizationsWithAbbreviationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizationAbbreviations = [
            // Depósitos e Esquadrões
            '11º Depósito de Suprimentos' => '11º D Sup',
            '11º Depósito de Suprimento' => '11º D Sup',
            '11º Depósito' => '11º D',
            'Décimo Primeiro Depósito' => '11º D',
            '1º Depósito de Suprimentos' => '1º D Sup',
            '2º Depósito de Suprimentos' => '2º D Sup',
            '3º Depósito de Suprimentos' => '3º D Sup',
            '1º Esquadrão do Primeiro Grupo de Aviação de Caça' => '1º/1º GAVCa',
            '2º Esquadrão do Primeiro Grupo de Aviação de Caça' => '2º/1º GAVCa',
            'Primeiro Grupo de Aviação de Caça' => '1º GAVCa',
            '1º GAVCa' => '1º GAVCa',
            
            // ELO (Esquadrão de Ligação e Observação)
            '1º Esquadrão de Ligação e Observação' => '1º ELO',
            '2º Esquadrão de Ligação e Observação' => '2º ELO',
            '3º Esquadrão de Ligação e Observação' => '3º ELO',
            'Primeiro ELO' => '1º ELO',
            'Segundo ELO' => '2º ELO',
            'Terceiro ELO' => '3º ELO',
            
            // Esquadrões de Transporte
            '1º Esquadrão de Transporte Aéreo' => '1º ETA',
            '2º Esquadrão de Transporte Aéreo' => '2º ETA',
            
            // Outras organizações comuns
            'Base Aérea de Anápolis' => 'BAAN',
            'Base Aérea de Brasília' => 'BABR',
            'Base Aérea de Campo Grande' => 'BACG',
            'Base Aérea de Canoas' => 'BACO',
            'Comando da Aeronáutica' => 'COMAER',
            'Estado-Maior da Aeronáutica' => 'EMAER',
            
            // Organizações do Exército
            '11ª Brigada de Infantaria Leve' => '11ª Bda Inf L',
            '1º Batalhão de Infantaria' => '1º BI',
            '2º Batalhão de Infantaria' => '2º BI',
            'Comando Militar do Planalto' => 'CMP',
            
            // Organizações da Marinha
            'Comando do 4º Distrito Naval' => 'Com4ºDN',
            'Base Naval de Brasília' => 'BNB',
            'Capitania dos Portos de Brasília' => 'CPBSB',
        ];

        foreach ($organizationAbbreviations as $orgName => $abbreviation) {
            $org = Organization::where('name', 'LIKE', "%{$orgName}%")->first();
            if ($org) {
                $org->update(['abbreviation' => $abbreviation]);
                $this->command->info("Atualizado: {$org->name} -> {$abbreviation}");
            }
        }

        // Para organizações que não foram encontradas acima, vamos tentar algumas inferências
        $organizations = Organization::whereNull('abbreviation')->get();
        foreach ($organizations as $org) {
            $name = $org->name;
            $abbreviation = null;

            // Padrões para depósitos
            if (preg_match('/(\d+)º\s*Depósito/i', $name, $matches)) {
                if (str_contains($name, 'Suprimento')) {
                    $abbreviation = $matches[1] . 'º D Sup';
                } else {
                    $abbreviation = $matches[1] . 'º D';
                }
            }
            // Padrões para esquadrões
            elseif (preg_match('/(\d+)º\s*Esquadrão/i', $name, $matches)) {
                if (str_contains($name, 'ELO') || str_contains($name, 'Ligação')) {
                    $abbreviation = $matches[1] . 'º ELO';
                } elseif (str_contains($name, 'Transporte')) {
                    $abbreviation = $matches[1] . 'º ETA';
                } else {
                    $abbreviation = $matches[1] . 'º Esqd';
                }
            }
            // Padrões para brigadas
            elseif (preg_match('/(\d+)ª\s*Brigada/i', $name, $matches)) {
                $abbreviation = $matches[1] . 'ª Bda';
            }
            // Padrões para batalhões
            elseif (preg_match('/(\d+)º\s*Batalhão/i', $name, $matches)) {
                if (str_contains($name, 'Infantaria')) {
                    $abbreviation = $matches[1] . 'º BI';
                } else {
                    $abbreviation = $matches[1] . 'º Btl';
                }
            }
            // Base Aérea
            elseif (str_contains($name, 'Base Aérea')) {
                $words = explode(' ', $name);
                if (count($words) >= 3) {
                    $city = end($words);
                    $abbreviation = 'BA' . strtoupper(substr($city, 0, 2));
                }
            }

            if ($abbreviation) {
                $org->update(['abbreviation' => $abbreviation]);
                $this->command->info("Inferido: {$org->name} -> {$abbreviation}");
            }
        }
    }
}
