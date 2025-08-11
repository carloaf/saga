<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rank;
use App\Models\Organization;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Usuário não autenticado.');
        }
        
        // Get user's ranks and organizations for dropdowns
        $ranks = Rank::orderBy('order')->get();
        $organizations = Organization::orderBy('name')->get();
        
        // Se o usuário não tem organização definida, definir 11º Depósito de Suprimento como padrão
        if (!$user->organization_id) {
            $defaultOrganization = Organization::where('name', '11º Depósito de Suprimento')->first();
            if ($defaultOrganization) {
                $user->organization_id = $defaultOrganization->id;
                $user->save();
            }
        }
        
        // Get user's booking statistics
        $currentMonth = Carbon::now();
        $monthlyBookings = Booking::where('user_id', $user->id)
            ->whereYear('booking_date', $currentMonth->year)
            ->whereMonth('booking_date', $currentMonth->month)
            ->get();
        
        $totalBookings = Booking::where('user_id', $user->id)->count();
        
        $stats = [
            'breakfast_this_month' => $monthlyBookings->where('meal_type', 'breakfast')->count(),
            'lunch_this_month' => $monthlyBookings->where('meal_type', 'lunch')->count(),
            'total_this_month' => $monthlyBookings->count(),
            'total_all_time' => $totalBookings,
        ];
        
        // Get recent activity
        $recentActivity = Booking::where('user_id', $user->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($booking) {
                $type = $booking->meal_type === 'breakfast' ? 'café da manhã' : 'almoço';
                $action = $booking->status === 'cancelled' ? 'Cancelou' : 'Agendou';
                $color = $booking->status === 'cancelled' ? 'red' : ($booking->meal_type === 'breakfast' ? 'green' : 'blue');
                
                return [
                    'action' => "{$action} {$type}",
                    'date' => $booking->created_at,
                    'booking_date' => $booking->booking_date,
                    'color' => $color,
                ];
            });
        
        return view('profile.edit', compact('user', 'ranks', 'organizations', 'stats', 'recentActivity'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'war_name' => 'required|string|max:100',
            'rank_id' => 'required|exists:ranks,id',
            'organization_id' => 'nullable|exists:organizations,id',
            'subunit' => 'nullable|string|max:100',
            'armed_force' => 'required|in:EB,MB,FAB',
            'gender' => 'nullable|string|in:M,F',
            'ready_at_om_date' => 'nullable|date|before_or_equal:today',
        ]);

        // Validação condicional: organization_id é obrigatório apenas para EB
        if ($request->armed_force === 'EB' && !$request->organization_id) {
            return back()->withErrors([
                'organization_id' => 'Organização Militar é obrigatória para membros do Exército Brasileiro.'
            ])->withInput();
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Usuário não autenticado.');
        }
        
        $user->update([
            'full_name' => $request->full_name,
            'war_name' => $request->war_name,
            'rank_id' => $request->rank_id,
            'organization_id' => $request->organization_id,
            'subunit' => $request->subunit,
            'armed_force' => $request->armed_force,
            'gender' => $request->gender,
            'ready_at_om_date' => $request->ready_at_om_date ? Carbon::parse($request->ready_at_om_date) : null,
        ]);

        return redirect()->route('profile.edit')->with('success', 'Perfil atualizado com sucesso!');
    }

    /**
     * Update user preferences.
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'reminder_notifications' => 'boolean',
            'weekly_reports' => 'boolean',
        ]);

        $user = Auth::user();
        
        // For now, we'll store preferences as JSON in a preferences column
        // or create a separate user_preferences table later if needed
        $preferences = [
            'email_notifications' => $request->boolean('email_notifications'),
            'reminder_notifications' => $request->boolean('reminder_notifications'),
            'weekly_reports' => $request->boolean('weekly_reports'),
        ];

        // We'll add a preferences column to users table later
        // For now, just return success
        
        return response()->json([
            'success' => true,
            'message' => 'Preferências atualizadas com sucesso!'
        ]);
    }

    /**
     * Get user dashboard data for profile page.
     */
    public function getDashboardData()
    {
        $user = Auth::user();
        
        // Get upcoming bookings
        $upcomingBookings = Booking::where('user_id', $user->id)
            ->where('booking_date', '>=', Carbon::today())
            ->orderBy('booking_date')
            ->orderBy('meal_type')
            ->limit(5)
            ->get();
        
        // Get monthly chart data for last 6 months
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $bookings = Booking::where('user_id', $user->id)
                ->whereYear('booking_date', $month->year)
                ->whereMonth('booking_date', $month->month)
                ->get();
            
            $monthlyData[] = [
                'month' => $month->format('M/Y'),
                'breakfast' => $bookings->where('meal_type', 'breakfast')->count(),
                'lunch' => $bookings->where('meal_type', 'lunch')->count(),
                'total' => $bookings->count(),
            ];
        }
        
        return response()->json([
            'upcoming_bookings' => $upcomingBookings,
            'monthly_data' => $monthlyData,
        ]);
    }
}
