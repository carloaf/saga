<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rank;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('google_id', $googleUser->getId())->first();
            
            if ($user) {
                // Verificar se usuário está aguardando homologação
                if ($user->status === 'H') {
                    return redirect('/login')->withErrors([
                        'email' => 'Seu cadastro ainda está aguardando homologação pelo administrador.',
                    ]);
                }
                
                // Verificar se usuário está ativo
                if (!$user->is_active || $user->status === 'inactive') {
                    return redirect('/login')->withErrors([
                        'email' => 'Sua conta está inativa. Entre em contato com o administrador.',
                    ]);
                }
                
                // Update user info from Google
                $user->update([
                    'full_name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'avatar_url' => $googleUser->getAvatar(),
                ]);
                
                Auth::login($user);
                
                return redirect()->intended('/dashboard');
            } else {
                // Store Google user data in session for registration
                session([
                    'google_user' => [
                        'id' => $googleUser->getId(),
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'avatar' => $googleUser->getAvatar(),
                    ]
                ]);
                
                return redirect('/register/complete');
            }
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Erro na autenticação com Google');
        }
    }

    public function showCompleteRegistration()
    {
        $googleUser = session('google_user');
        
        if (!$googleUser) {
            return redirect('/login');
        }
        
        $ranks = Rank::ordered()->where('name', '!=', 'Usuário Externo')->get();
        $organizations = Organization::all();
        
        return view('auth.complete-registration', compact('googleUser', 'ranks', 'organizations'));
    }

    public function completeRegistration(Request $request)
    {
        $googleUser = session('google_user');
        
        if (!$googleUser) {
            return redirect('/login');
        }
        
        $validated = $request->validate([
            'war_name' => 'required|string|max:255',
            'rank_id' => 'required|exists:ranks,id',
            'organization_id' => 'nullable|exists:organizations,id',
            'armed_force' => 'required|in:EB,MB,FAB',
            'gender' => 'required|in:M,F',
            'ready_at_om_date' => 'required|date',
        ]);

        // Validação condicional: organization_id é obrigatório apenas para EB
        if ($request->armed_force === 'EB' && !$request->organization_id) {
            return back()->withErrors([
                'organization_id' => 'Organização Militar é obrigatória para membros do Exército Brasileiro.'
            ])->withInput();
        }
        
        $user = User::create([
            'google_id' => $googleUser['id'],
            'full_name' => $googleUser['name'],
            'war_name' => $validated['war_name'],
            'email' => $googleUser['email'],
            'avatar_url' => $googleUser['avatar'],
            'rank_id' => $validated['rank_id'],
            'organization_id' => $validated['organization_id'],
            'armed_force' => $validated['armed_force'],
            'gender' => $validated['gender'],
            'ready_at_om_date' => $validated['ready_at_om_date'],
        ]);
        
        // Assign default role
        $user->assignRole('user');
        
        // Clear session data
        session()->forget('google_user');
        
        Auth::login($user);
        
        return redirect('/dashboard')->with('success', 'Cadastro realizado com sucesso!');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    /**
     * Mostrar formulário de login tradicional
     */
    public function showLogin()
    {
        return view('auth.traditional-login');
    }

    /**
     * Processar login tradicional
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            // Verificar se usuário está aguardando homologação
            if ($user->status === 'H') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Seu cadastro ainda está aguardando homologação pelo administrador.',
                ])->onlyInput('email');
            }
            
            // Verificar se usuário está ativo
            if (!$user->is_active || $user->status === 'inactive') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Sua conta está inativa. Entre em contato com o administrador.',
                ])->onlyInput('email');
            }
            
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ])->onlyInput('email');
    }

    /**
     * Mostrar formulário de registro
     */
    public function showRegister()
    {
        $ranks = Rank::ordered()->where('name', '!=', 'Usuário Externo')->get();
        $organizations = Organization::all();
        
        return view('auth.register', compact('ranks', 'organizations'));
    }

    /**
     * Processar registro tradicional
     */
    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'war_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'idt' => 'required|string|max:30|unique:users,idt',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'rank_id' => 'required|exists:ranks,id',
            'organization_id' => 'nullable|exists:organizations,id',
            'subunit' => 'nullable|in:1ª Cia,2ª Cia,EM',
            'armed_force' => 'required|in:EB,MB,FAB',
            'gender' => 'required|in:M,F',
            'ready_at_om_date' => 'required|date',
        ]);

        // Validação condicional: organization_id é obrigatório apenas para EB
        if ($request->armed_force === 'EB' && !$request->organization_id) {
            return back()->withErrors([
                'organization_id' => 'Organização Militar é obrigatória para membros do Exército Brasileiro.'
            ])->withInput();
        }

        $user = User::create([
            'full_name' => $request->full_name,
            'war_name' => $request->war_name,
            'email' => $request->email,
            'idt' => $request->idt,
            'password' => $request->password, // Será hasheada automaticamente pelo cast
            'rank_id' => $request->rank_id,
            'organization_id' => $request->organization_id,
            'subunit' => $request->subunit, // Corrigido: usar subunit ao invés de section
            'armed_force' => $request->armed_force,
            'gender' => $request->gender,
            'ready_at_om_date' => $request->ready_at_om_date,
            'role' => 'user',
            'status' => 'H', // Novo usuário aguardando homologação
            'is_active' => false, // Inativo até ser homologado
            'email_verified_at' => now(), // Para simplificar, considera como verificado
        ]);

        // Não fazer login automático - usuário precisa ser homologado primeiro
        // Auth::login($user);

        return redirect('/login')->with('success', 'Cadastro realizado com sucesso! Aguarde a homologação do administrador para acessar o sistema.');
    }
}
