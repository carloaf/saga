<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestCompletePasswordReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:complete-password-reset {idt} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa o processo completo de reset de senha';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $idt = $this->argument('idt');
        $newPassword = $this->argument('password');
        
        $this->info("Testando processo completo de reset para IDT: {$idt}");
        
        // 1. Buscar usuário pelo IDT
        $user = User::where('idt', $idt)->first();

        if (!$user) {
            $this->error('Usuário não encontrado.');
            return 1;
        }

        $this->info("1. Usuário encontrado: {$user->full_name} ({$user->email})");
        $this->info("   Senha atual hash: " . substr($user->password, 0, 20) . "...");

        // 2. Simular processo de reset (como se fosse feito via formulário web)
        
        // Primeiro, gerar token
        $token = 'test-token-' . time();

        // Remover tokens antigos para este email
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        // Criar novo token
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);

        $this->info("2. Token criado: {$token}");

        // 3. Verificar se o token existe e não expirou
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->first();

        if (!$passwordReset) {
            $this->error('3. Token não encontrado.');
            return 1;
        }

        $this->info("3. Token verificado no banco de dados.");

        // 4. Verificar se o token está correto
        if (!Hash::check($token, $passwordReset->token)) {
            $this->error('4. Token inválido.');
            return 1;
        }

        $this->info("4. Token validado com sucesso.");

        // 5. Atualizar senha
        $user->password = $newPassword;
        $user->save();

        $this->info("5. Senha atualizada com sucesso!");
        $this->info("   Nova senha hash: " . substr($user->password, 0, 20) . "...");

        // 6. Remover token usado
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        $this->info("6. Token removido do banco de dados.");

        // 7. Verificar se consegue fazer login com a nova senha
        $loginAttempt = Hash::check($newPassword, $user->password);
        
        if ($loginAttempt) {
            $this->info("7. ✅ Teste de login com nova senha: SUCESSO");
        } else {
            $this->error("7. ❌ Teste de login com nova senha: FALHOU");
        }

        $this->info("\n🎉 Processo completo de reset de senha finalizado com sucesso!");
        
        return 0;
    }
}
