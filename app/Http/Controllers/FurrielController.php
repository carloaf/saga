<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Rank;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FurrielController extends Controller
{
    /**
     * Display the company meals management page
     */
    public function index(Request $request)
    {
        // Verificar se o usuário é furriel
        if (!Auth::user()->isFurriel()) {
            abort(403, 'Acesso negado. Apenas furriéis podem acessar esta página.');
        }

        $furriel = Auth::user();
        $selectedDate = $request->get('date', Carbon::now()->format('Y-m-d'));
        
        // Buscar todos os Soldados EV da mesma organização/subunidade do furriel
        $soldadosEv = User::whereHas('rank', function($query) {
                $query->where('name', 'Soldado EV');
            })
            ->where('organization_id', $furriel->organization_id)
            ->where('subunit', $furriel->subunit)
            ->where('is_active', true)
            ->with(['rank', 'organization'])
            ->orderBy('war_name')
            ->get();

        // Buscar reservas existentes para a data selecionada
        $existingBookings = Booking::whereDate('booking_date', $selectedDate)
            ->whereIn('user_id', $soldadosEv->pluck('id'))
            ->get()
            ->groupBy('user_id');

        // Se for uma requisição AJAX, retornar JSON
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            \Log::info('Requisição AJAX detectada para furriel arranchamento', [
                'date' => $selectedDate,
                'soldados_count' => $soldadosEv->count(),
                'headers' => $request->headers->all()
            ]);
            
            $soldadosData = $soldadosEv->map(function($soldado) use ($existingBookings) {
                return [
                    'id' => $soldado->id,
                    'full_name' => $soldado->full_name,
                    'war_name' => $soldado->war_name,
                    'rank_abbreviation' => $soldado->rank->abbreviation ?? null,
                    'existing_bookings' => $existingBookings->get($soldado->id, collect())->map(function($booking) {
                        return [
                            'meal_type' => $booking->meal_type,
                            'id' => $booking->id
                        ];
                    })
                ];
            });

            $stats = [
                'totalSoldadosEv' => $soldadosEv->count(),
                'reservasNaData' => $existingBookings->flatten()->count()
            ];

            return response()->json([
                'soldados' => $soldadosData,
                'stats' => $stats
            ]);
        }

        // Adicionar estatísticas para a view
        $stats = [
            'totalSoldadosEv' => $soldadosEv->count(),
            'reservasNaData' => $existingBookings->flatten()->count()
        ];

        // Adicionar as reservas existentes aos soldados
        foreach ($soldadosEv as $soldado) {
            $soldado->existingBookings = $existingBookings->get($soldado->id, collect());
        }

        \Log::info('Retornando view normal (não AJAX) para furriel arranchamento', [
            'date' => $selectedDate,
            'soldados_count' => $soldadosEv->count()
        ]);

        return view('furriel.arranchamento-cia', compact(
            'soldadosEv',
            'existingBookings', 
            'selectedDate',
            'furriel',
            'stats'
        ));
    }

    /**
     * Store bookings for Soldados EV
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isFurriel()) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'bookings' => 'required|array',
            'bookings.*.user_id' => 'required|exists:users,id',
            'bookings.*.meals' => 'array',
            'bookings.*.meals.*' => 'in:breakfast,lunch'
        ]);

        $bookingDate = $request->booking_date;
        $furriel = Auth::user();
        
        // Verificar se é dia útil
        $carbonDate = Carbon::parse($bookingDate);
        if ($carbonDate->isWeekend()) {
            return back()->withErrors(['booking_date' => 'Não é possível fazer reservas para fins de semana.']);
        }

        // Verificar se é sexta-feira e tentando agendar almoço
        if ($carbonDate->isFriday()) {
            foreach ($request->bookings as $booking) {
                if (isset($booking['meals']) && in_array('lunch', $booking['meals'])) {
                    return back()->withErrors(['booking_date' => 'Não é possível agendar almoço para sexta-feira.']);
                }
            }
        }

        DB::transaction(function() use ($request, $furriel, $bookingDate) {
            foreach ($request->bookings as $booking) {
                $user = User::find($booking['user_id']);
                
                // Verificar se o usuário pertence à mesma organização/subunidade do furriel
                if ($user->organization_id !== $furriel->organization_id || 
                    $user->subunit !== $furriel->subunit) {
                    continue;
                }

                // Verificar se é Soldado EV
                if (!$user->rank || $user->rank->name !== 'Soldado EV') {
                    continue;
                }

                // Remover reservas existentes para esta data
                Booking::where('user_id', $user->id)
                    ->whereDate('booking_date', $bookingDate)
                    ->delete();

                // Criar novas reservas se houver refeições selecionadas
                if (isset($booking['meals']) && is_array($booking['meals'])) {
                    foreach ($booking['meals'] as $mealType) {
                        Booking::create([
                            'user_id' => $user->id,
                            'booking_date' => $bookingDate,
                            'meal_type' => $mealType,
                            'status' => 'confirmed',
                            'created_by_furriel' => $furriel->id
                        ]);
                    }
                }
            }
        });

        return back()->with('success', 'Arranchamento da companhia atualizado com sucesso!');
    }

    /**
     * Get statistics for the furriel dashboard
     */
    public function getStats()
    {
        if (!Auth::user()->isFurriel()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $furriel = Auth::user();
        $today = Carbon::now()->format('Y-m-d');
        $thisWeek = [
            Carbon::now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d'),
            Carbon::now()->endOfWeek(Carbon::FRIDAY)->format('Y-m-d')
        ];

        // Contar Soldados EV da companhia
        $totalSoldadosEv = User::whereHas('rank', function($query) {
                $query->where('name', 'Soldado EV');
            })
            ->where('organization_id', $furriel->organization_id)
            ->where('subunit', $furriel->subunit)
            ->where('is_active', true)
            ->count();

        // Reservas de hoje
        $todayBookings = Booking::whereDate('booking_date', $today)
            ->whereHas('user', function($query) use ($furriel) {
                $query->where('organization_id', $furriel->organization_id)
                      ->where('subunit', $furriel->subunit)
                      ->whereHas('rank', function($rankQuery) {
                          $rankQuery->where('name', 'Soldado EV');
                      });
            })
            ->count();

        // Reservas da semana
        $weekBookings = Booking::whereBetween('booking_date', $thisWeek)
            ->whereHas('user', function($query) use ($furriel) {
                $query->where('organization_id', $furriel->organization_id)
                      ->where('subunit', $furriel->subunit)
                      ->whereHas('rank', function($rankQuery) {
                          $rankQuery->where('name', 'Soldado EV');
                      });
            })
            ->count();

        return response()->json([
            'total_soldados_ev' => $totalSoldadosEv,
            'today_bookings' => $todayBookings,
            'week_bookings' => $weekBookings
        ]);
    }
}
