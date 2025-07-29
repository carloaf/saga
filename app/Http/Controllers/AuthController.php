<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rank;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        
        $ranks = Rank::ordered()->get();
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
            'organization_id' => 'required|exists:organizations,id',
            'gender' => 'required|in:male,female',
            'ready_at_om_date' => 'required|date',
        ]);
        
        $user = User::create([
            'google_id' => $googleUser['id'],
            'full_name' => $googleUser['name'],
            'war_name' => $validated['war_name'],
            'email' => $googleUser['email'],
            'avatar_url' => $googleUser['avatar'],
            'rank_id' => $validated['rank_id'],
            'organization_id' => $validated['organization_id'],
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
}
