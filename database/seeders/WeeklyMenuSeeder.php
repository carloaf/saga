<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WeeklyMenu;
use App\Models\User;
use Carbon\Carbon;

class WeeklyMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar um superuser para criar os cardápios
        $superuser = User::where('role', 'superuser')->first();
        
        if (!$superuser) {
            echo "⚠️  Nenhum superuser encontrado. Execute primeiro o SuperuserSeeder.\n";
            return;
        }

        // Cardápio da semana atual
        $currentWeekStart = Carbon::now()->startOfWeek(Carbon::MONDAY);
        
        WeeklyMenu::updateOrCreate(
            ['week_start' => $currentWeekStart->toDateString(), 'is_active' => true],
            [
                'menu_data' => [
                    'segunda' => [
                        'cafe' => 'Café, Pão Francês, Manteiga, Leite, Fruta da Época',
                        'almoco' => 'Arroz Branco, Feijão Carioca, Carne Assada, Salada Verde, Suco Natural'
                    ],
                    'terca' => [
                        'cafe' => 'Café, Pão de Forma Integral, Requeijão, Leite, Banana',
                        'almoco' => 'Arroz Branco, Feijão Preto, Frango Grelhado, Legumes Refogados, Suco de Laranja'
                    ],
                    'quarta' => [
                        'cafe' => 'Café, Pão Francês, Presunto e Queijo, Leite, Mamão',
                        'almoco' => 'Arroz Branco, Feijão Carioca, Peixe Assado, Salada Mista, Refresco'
                    ],
                    'quinta' => [
                        'cafe' => 'Café, Pão de Forma, Manteiga e Mel, Leite, Maçã',
                        'almoco' => 'Arroz Branco, Feijão Preto, Carne de Porco, Purê de Batata, Suco de Uva'
                    ],
                    'sexta' => [
                        'cafe' => 'Café, Pão Francês, Geleia de Frutas, Leite, Fruta da Época'
                    ]
                ],
                'created_by' => $superuser->id,
                'updated_by' => $superuser->id
            ]
        );

        // Cardápio da próxima semana
        $nextWeekStart = Carbon::now()->addWeek()->startOfWeek(Carbon::MONDAY);
        
        WeeklyMenu::updateOrCreate(
            ['week_start' => $nextWeekStart->toDateString(), 'is_active' => true],
            [
                'menu_data' => [
                    'segunda' => [
                        'cafe' => 'Café, Pão Integral, Queijo Branco, Leite, Vitamina de Banana',
                        'almoco' => 'Arroz Integral, Feijão Fradinho, Bife Acebolado, Salada de Tomate, Suco de Maracujá'
                    ],
                    'terca' => [
                        'cafe' => 'Café, Pão Francês, Mortadela, Leite, Fruta Cítrica',
                        'almoco' => 'Arroz Branco, Feijão Carioca, Frango à Parmegiana, Abobrinha Refogada, Refresco'
                    ],
                    'quarta' => [
                        'cafe' => 'Café, Pão de Forma, Cream Cheese, Leite, Melancia',
                        'almoco' => 'Arroz Branco, Feijão Preto, Linguiça Acebolada, Batata Doce, Suco de Acerola'
                    ],
                    'quinta' => [
                        'cafe' => 'Café, Pão Francês, Manteiga e Açúcar, Leite, Pêra',
                        'almoco' => 'Arroz Branco, Feijão Carioca, Carne Moída, Macarrão, Suco de Caju'
                    ],
                    'sexta' => [
                        'cafe' => 'Café, Pão Integral, Doce de Leite, Leite, Abacaxi'
                    ]
                ],
                'created_by' => $superuser->id,
                'updated_by' => $superuser->id
            ]
        );

        echo "✅ Cardápios de exemplo criados com sucesso!\n";
        echo "📅 Semana atual: " . $currentWeekStart->format('d/m/Y') . "\n";
        echo "📅 Próxima semana: " . $nextWeekStart->format('d/m/Y') . "\n";
    }
}
