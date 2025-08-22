<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Rank;
use App\Models\Organization;

class EnsureAdminUser extends Command
{
    protected $signature = 'saga:ensure-admin {--email=admin@saga.mil.br} {--password=admin123}';
    protected $description = 'Garante que o usuário admin padrão exista com senha, idt, rank e organização válidos';

    public function handle(): int
    {
        $email = $this->option('email');
        $password = $this->option('password');

        $rankId = Rank::orderBy('id')->value('id');
        if (!$rankId) {
            $this->error('Nenhum rank encontrado. Execute: php artisan db:seed --class=RankSeeder');
            return 1;
        }

        $orgId = Organization::orderBy('id')->value('id');
        if (!$orgId) {
            $this->error('Nenhuma organização encontrada. Execute: php artisan db:seed --class=OrganizationSeeder');
            return 1;
        }

        $user = User::where('email', $email)->first();
        $created = false;
        if (!$user) {
            $user = User::create([
                'full_name' => 'Administrador SAGA',
                'war_name' => 'ADMIN',
                'email' => $email,
                'password' => $password,
                'google_id' => 'admin_saga_system',
                'idt' => $this->generateIdt(),
                'rank_id' => $rankId,
                'organization_id' => $orgId,
                'gender' => 'M',
                'ready_at_om_date' => now()->toDateString(),
                'role' => 'manager',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            $created = true;
        } else {
            $changed = false;
            if (empty($user->password)) {
                $user->password = $password; $changed = true;
            }
            if (empty($user->idt)) {
                $user->idt = $this->generateIdt(); $changed = true;
            }
            if (empty($user->rank_id)) { $user->rank_id = $rankId; $changed = true; }
            if (empty($user->organization_id)) { $user->organization_id = $orgId; $changed = true; }
            if ($user->role !== 'manager') { $user->role = 'manager'; $changed = true; }
            if ($changed) { $user->save(); }
        }

        if ($created) {
            $this->info("Usuário admin criado: {$email} / {$password}");
        } else {
            $this->info('Usuário admin verificado/atualizado com sucesso.');
        }
        $this->line('IDT: '.$user->idt);
        return 0;
    }

    private function generateIdt(): string
    {
        return 'ADM'.str_pad((string)random_int(0,99999),5,'0',STR_PAD_LEFT);
    }
}
