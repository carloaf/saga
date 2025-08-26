<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Mostrar formulário para solicitar reset de senha usando IDT
     */
    public function showRequestForm()
    {
        return view('auth.passwords.request');
    }

    /**
     * Processar solicitação de reset usando IDT + Email
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'idt' => 'required|string|max:30',
            'email' => 'required|email'
        ]);

        // Buscar usuário pelo IDT e email (ambos devem coincidir)
        $user = User::where('idt', $request->idt)
                   ->where('email', $request->email)
                   ->first();

        if (!$user) {
            return back()->withErrors([
                'credentials' => 'As informações fornecidas (IDT e email) não correspondem a nenhum usuário cadastrado.'
            ])->withInput();
        }

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

        // Redirecionar diretamente para página de redefinição de senha
        return redirect()->route('password.reset', [
            'token' => $token, 
            'email' => $user->email
        ])->with('success', 'Dados verificados com sucesso! Agora você pode definir sua nova senha.');
    }

    /**
     * Mostrar formulário de reset de senha
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Processar reset de senha
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        // Verificar se o token existe e não expirou (60 minutos)
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset) {
            return back()->withErrors(['email' => 'Token inválido ou expirado.']);
        }

        // Verificar se o token foi criado há menos de 60 minutos
        if (Carbon::parse($passwordReset->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Token expirado. Solicite um novo.']);
        }

        // Verificar se o token está correto
        if (!Hash::check($request->token, $passwordReset->token)) {
            return back()->withErrors(['email' => 'Token inválido.']);
        }

        // Buscar usuário
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Usuário não encontrado.']);
        }

        // Atualizar senha
        $user->password = $request->password;
        $user->save();

        // Remover token usado
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('auth.traditional-login')->with('success', 
            'Senha alterada com sucesso! Faça login com sua nova senha.'
        );
    }
}
