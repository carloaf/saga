<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

class CreateSampleBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:create-samples';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create 26 sample bookings for other armed forces';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Criando 26 reservas para militares de outras forças armadas...');

        // Buscar usuários de outras forças armadas (não EB)
        $users = User::whereIn('armed_force', ['MB', 'FAB'])->take(5)->get();

        if ($users->count() === 0) {
            $this->error('Nenhum usuário de outras forças armadas encontrado. Criando usuários de exemplo...');
            
            // Criar usuários de exemplo se não existirem
            $this->createSampleUsers();
            $users = User::whereIn('armed_force', ['MB', 'FAB'])->take(5)->get();
        }

        $bookingsCreated = 0;
        $targetBookings = 26;

        // Datas para os próximos dias
        $dates = [
            '2025-08-19', // segunda
            '2025-08-20', // terça
            '2025-08-21', // quarta
            '2025-08-22', // quinta
            '2025-08-23', // sexta (só café)
        ];

        foreach ($dates as $date) {
            $isMonday = Carbon::parse($date)->dayOfWeek === Carbon::MONDAY;
            $isFriday = Carbon::parse($date)->dayOfWeek === Carbon::FRIDAY;

            foreach ($users as $user) {
                if ($bookingsCreated >= $targetBookings) break 2;

                // Café da manhã (todos os dias)
                if ($bookingsCreated < $targetBookings) {
                    $existingBreakfast = Booking::where([
                        'user_id' => $user->id,
                        'booking_date' => $date,
                        'meal_type' => 'breakfast'
                    ])->first();

                    if (!$existingBreakfast) {
                        Booking::create([
                            'user_id' => $user->id,
                            'booking_date' => $date,
                            'meal_type' => 'breakfast',
                            'status' => 'confirmed',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $bookingsCreated++;
                        $this->line("✅ Café da manhã - {$user->war_name} - {$date}");
                    } else {
                        $this->line("⚠️  Café da manhã já existe - {$user->war_name} - {$date}");
                    }
                }

                // Almoço (segunda a quinta)
                if (!$isFriday && $bookingsCreated < $targetBookings) {
                    $existingLunch = Booking::where([
                        'user_id' => $user->id,
                        'booking_date' => $date,
                        'meal_type' => 'lunch'
                    ])->first();

                    if (!$existingLunch) {
                        Booking::create([
                            'user_id' => $user->id,
                            'booking_date' => $date,
                            'meal_type' => 'lunch',
                            'status' => 'confirmed',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $bookingsCreated++;
                        $this->line("✅ Almoço - {$user->war_name} - {$date}");
                    } else {
                        $this->line("⚠️  Almoço já existe - {$user->war_name} - {$date}");
                    }
                }
            }
        }

        $this->info("✅ Total de {$bookingsCreated} reservas criadas com sucesso!");
        return 0;
    }

    private function createSampleUsers()
    {
        $this->info('Criando usuários de exemplo...');

        User::create([
            'full_name' => 'CF João Silva',
            'war_name' => 'Silva',
            'email' => 'silva@marinha.mil.br',
            'password' => bcrypt('password'),
            'armed_force' => 'MB',
            'gender' => 'M',
            'ready_at_om_date' => '2024-01-15',
            'is_active' => true,
        ]);

        User::create([
            'full_name' => 'SO Maria Santos',
            'war_name' => 'Santos',
            'email' => 'santos@fab.mil.br',
            'password' => bcrypt('password'),
            'armed_force' => 'FAB',
            'gender' => 'F',
            'ready_at_om_date' => '2024-02-10',
            'is_active' => true,
        ]);

        User::create([
            'full_name' => 'CB Pedro Costa',
            'war_name' => 'Costa',
            'email' => 'costa@marinha.mil.br',
            'password' => bcrypt('password'),
            'armed_force' => 'MB',
            'gender' => 'M',
            'ready_at_om_date' => '2024-03-05',
            'is_active' => true,
        ]);

        $this->line('✅ Usuários de exemplo criados');
    }
}
