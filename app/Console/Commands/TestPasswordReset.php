<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TestPasswordReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:password-reset {idt} {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a funcionalidade de reset de senha com IDT e Email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $idt = $this->argument('idt');
        $email = $this->argument('email');
        
        $this->info("Testando reset de senha para IDT: {$idt} e Email: {$email}");
        
        // Buscar usuário pelo IDT e email (ambos devem coincidir)
        $user = User::where('idt', $idt)
                   ->where('email', $email)
                   ->first();

        if (!$user) {
            $this->error('As informações fornecidas (IDT e email) não correspondem a nenhum usuário cadastrado.');
            return 1;
        }

        $this->info("Usuário encontrado: {$user->full_name} ({$user->email})");

        // Gerar token único
        $token = Str::random(64);

        // Remover tokens antigos para este email
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        // Criar novo token
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);

        $this->info('Token gerado com sucesso!');
        
        // Mostrar o link de reset
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);
        $this->info("Link para redefinir senha: {$resetUrl}");
        
        return 0;
    }
}
