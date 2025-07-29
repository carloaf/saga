<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display the admin users page with statistics cards
     */
    public function users()
    {
        // Verificação de acesso
        if (!Auth::user() || Auth::user()->role !== 'superuser') {
            abort(403, 'Acesso negado. Apenas superusuários podem acessar esta área.');
        }

        // Estatísticas dos usuários
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();
        $recentUsers = User::where('created_at', '>=', now()->subDays(30))->count();

        // Buscar usuários com paginação
        $users = User::with(['rank', 'organization'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'activeUsers', 
            'inactiveUsers',
            'recentUsers'
        ));
    }

    /**
     * Update a user
     */
    public function updateUser(Request $request, User $user)
    {
        // Verificação de acesso
        if (!Auth::user() || Auth::user()->role !== 'superuser') {
            return response()->json(['success' => false, 'message' => 'Acesso negado.'], 403);
        }

        try {
            $validatedData = $request->validate([
                'full_name' => 'required|string|max:255',
                'war_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'rank_id' => 'nullable|exists:ranks,id',
                'organization_id' => 'nullable|exists:organizations,id',
                'gender' => 'required|in:male,female',
                'ready_at_om_date' => 'required|date',
                'is_active' => 'required|boolean'
            ]);

            $user->update($validatedData);

            return response()->json(['success' => true, 'message' => 'Usuário atualizado com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Store a new user
     */
    public function storeUser(Request $request)
    {
        // Verificação de acesso
        if (!Auth::user() || Auth::user()->role !== 'superuser') {
            return response()->json(['success' => false, 'message' => 'Acesso negado.'], 403);
        }

        try {
            $validatedData = $request->validate([
                'full_name' => 'required|string|max:255',
                'war_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'rank_id' => 'nullable|exists:ranks,id',
                'organization_id' => 'nullable|exists:organizations,id',
                'gender' => 'required|in:male,female',
                'ready_at_om_date' => 'required|date',
                'is_active' => 'required|boolean'
            ]);

            // Adicionar campos padrão para usuário criado manualmente
            $validatedData['google_id'] = 'manual_' . time() . '_' . rand(1000, 9999);
            $validatedData['email_verified_at'] = now();
            $validatedData['role'] = 'user'; // Usuários criados pelo admin são 'user' por padrão

            $user = User::create($validatedData);

            return response()->json([
                'success' => true, 
                'message' => 'Usuário criado com sucesso!',
                'user' => $user->load(['rank', 'organization'])
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Toggle user status
     */
    public function toggleUserStatus(Request $request, User $user)
    {
        // Verificação de acesso
        if (!Auth::user() || Auth::user()->role !== 'superuser') {
            return response()->json(['success' => false, 'message' => 'Acesso negado.'], 403);
        }

        try {
            $user->update(['is_active' => !$user->is_active]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Status do usuário alterado com sucesso!',
                'is_active' => $user->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
