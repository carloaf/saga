<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Booking;
use App\Models\Organization;
use App\Models\Rank;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OtherForcesBookingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primeiro, vamos criar algumas organizações de outras forças se não existirem
        $marinha = Organization::firstOrCreate([
            'name' => 'Comando do 5º Distrito Naval'
        ], [
            'is_host' => false
        ]);

        $aeronautica = Organization::firstOrCreate([
            'name' => '2ª Base Aérea'
        ], [
            'is_host' => false
        ]);

        // Buscar alguns ranks existentes
        $ranks = Rank::all();
        if ($ranks->isEmpty()) {
            // Se não há ranks, criar alguns básicos
            $soldadoRank = Rank::create(['name' => 'Soldado', 'abbreviation' => 'Sd']);
            $caboRank = Rank::create(['name' => 'Cabo', 'abbreviation' => 'Cb']);
            $ranks = [$soldadoRank, $caboRank];
        } else {
            $ranks = $ranks->toArray();
        }

        // Criar usuários da Marinha
        $marinhaUsers = [
            [
                'full_name' => 'João Silva Santos',
                'war_name' => 'SANTOS',
                'email' => 'santos.mb@marinha.mil.br',
                'armed_force' => 'MB'
            ],
            [
                'full_name' => 'Carlos Alberto Costa',
                'war_name' => 'COSTA',
                'email' => 'costa.mb@marinha.mil.br',
                'armed_force' => 'MB'
            ],
            [
                'full_name' => 'Pedro José Lima',
                'war_name' => 'LIMA',
                'email' => 'lima.mb@marinha.mil.br',
                'armed_force' => 'MB'
            ]
        ];

        // Criar usuários da Aeronáutica
        $fabUsers = [
            [
                'full_name' => 'Ana Maria Oliveira',
                'war_name' => 'OLIVEIRA',
                'email' => 'oliveira.fab@fab.mil.br',
                'armed_force' => 'FAB'
            ],
            [
                'full_name' => 'Roberto Souza Almeida',
                'war_name' => 'ALMEIDA',
                'email' => 'almeida.fab@fab.mil.br',
                'armed_force' => 'FAB'
            ]
        ];

        $createdUsers = [];

        // Criar usuários da Marinha
        foreach ($marinhaUsers as $userData) {
            $user = User::firstOrCreate([
                'email' => $userData['email']
            ], [
                'full_name' => $userData['full_name'],
                'war_name' => $userData['war_name'],
                'armed_force' => $userData['armed_force'],
                'organization_id' => $marinha->id,
                'rank_id' => $ranks[array_rand($ranks)]->id ?? $ranks[0]['id'],
                'role' => 'user',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'ready_at_om_date' => Carbon::now()->subDays(rand(30, 365))
            ]);
            $createdUsers[] = $user;
        }

        // Criar usuários da Aeronáutica
        foreach ($fabUsers as $userData) {
            $user = User::firstOrCreate([
                'email' => $userData['email']
            ], [
                'full_name' => $userData['full_name'],
                'war_name' => $userData['war_name'],
                'armed_force' => $userData['armed_force'],
                'organization_id' => $aeronautica->id,
                'rank_id' => $ranks[array_rand($ranks)]->id ?? $ranks[0]['id'],
                'role' => 'user',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'ready_at_om_date' => Carbon::now()->subDays(rand(30, 365))
            ]);
            $createdUsers[] = $user;
        }

        // Criar reservas para o mês atual
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        foreach ($createdUsers as $user) {
            // Criar algumas reservas aleatórias para cada usuário
            $bookingDates = [];
            
            // Gerar entre 8 a 15 dias de reservas por usuário
            $numBookings = rand(8, 15);
            
            for ($i = 0; $i < $numBookings; $i++) {
                $randomDate = Carbon::createFromTimestamp(
                    rand($startDate->timestamp, $endDate->timestamp)
                );
                
                // Só dias úteis (segunda a sexta)
                while ($randomDate->isWeekend()) {
                    $randomDate->addDay();
                    if ($randomDate->gt($endDate)) {
                        $randomDate = $startDate->copy()->addDays(rand(0, 20));
                    }
                }
                
                $dateStr = $randomDate->format('Y-m-d');
                
                // Evitar datas duplicadas para o mesmo usuário
                if (!in_array($dateStr, $bookingDates)) {
                    $bookingDates[] = $dateStr;
                    
                    // Café da manhã (sempre disponível)
                    Booking::firstOrCreate([
                        'user_id' => $user->id,
                        'booking_date' => $dateStr,
                        'meal_type' => 'breakfast'
                    ], [
                        'created_at' => $randomDate->copy()->subDays(rand(1, 7)),
                        'updated_at' => $randomDate->copy()->subDays(rand(1, 7))
                    ]);
                    
                    // Almoço (não disponível na sexta-feira)
                    if ($randomDate->dayOfWeek !== Carbon::FRIDAY && rand(0, 100) < 70) {
                        Booking::firstOrCreate([
                            'user_id' => $user->id,
                            'booking_date' => $dateStr,
                            'meal_type' => 'lunch'
                        ], [
                            'created_at' => $randomDate->copy()->subDays(rand(1, 7)),
                            'updated_at' => $randomDate->copy()->subDays(rand(1, 7))
                        ]);
                    }
                }
            }
        }

        $this->command->info('Usuários e reservas de outras forças criados com sucesso!');
        $this->command->info('Usuários da Marinha: ' . count($marinhaUsers));
        $this->command->info('Usuários da Aeronáutica: ' . count($fabUsers));
        $this->command->info('Total de usuários criados: ' . count($createdUsers));
    }
}
