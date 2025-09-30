<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FurrielController extends Controller
{
    /**
     * Display the company meals management page
     */
    public function index(Request $request)
    {
        /** @var User|null $furriel */
        $furriel = Auth::user();

        // Verificar se o usuário é furriel
        if (!$furriel || !$furriel->isFurriel()) {
            abort(403, 'Acesso negado. Apenas furriéis podem acessar esta página.');
        }

        $requestedDate = $request->get('date');

        try {
            $selectedCarbon = $requestedDate ? Carbon::parse($requestedDate) : Carbon::now();
        } catch (\Throwable $e) {
            Log::warning('Data de arranchamento inválida recebida para furriel.', [
                'requested_date' => $requestedDate,
                'message' => $e->getMessage()
            ]);
            $selectedCarbon = Carbon::now();
        }

    $selectedDateIso = $selectedCarbon->format('Y-m-d');
    $selectedDateDisplay = $selectedCarbon->format('d/m/Y');
    $selectedDate = $selectedDateIso;

        $organization = $furriel->organization;
        $isHostOrganization = optional($organization)->is_host;

        $militaresQuery = User::query()
            ->where('organization_id', $furriel->organization_id)
            ->where('is_active', true);

        if ($isHostOrganization) {
            // Host (11º D Sup): restringir a Soldados EV da mesma subunidade
            $militaresQuery
                ->where('subunit', $furriel->subunit)
                ->whereHas('rank', function($query) {
                    $query->where('name', 'Soldado EV');
                });
        }

        $militares = $militaresQuery
            ->with(['rank', 'organization'])
            ->orderByRaw('COALESCE(war_name, full_name)')
            ->get();

        $audience = [
            'label_plural' => $isHostOrganization ? 'Soldados EV' : 'Militares',
            'label_singular' => $isHostOrganization ? 'Soldado' : 'Militar',
            'scope' => $isHostOrganization ? 'da Companhia' : 'da Organização',
            'description' => $isHostOrganization
                ? 'Gerenciamento das refeições dos Soldados EV da companhia'
                : 'Gerenciamento das refeições dos militares da organização'
        ];

        // Buscar reservas existentes para a data selecionada
        $existingBookings = Booking::whereDate('booking_date', $selectedDateIso)
            ->whereIn('user_id', $militares->pluck('id'))
            ->get()
            ->groupBy('user_id');

        // Se for uma requisição AJAX, retornar JSON
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            Log::info('Requisição AJAX detectada para furriel arranchamento', [
                'date' => $selectedDateIso,
                'soldados_count' => $militares->count(),
                'headers' => $request->headers->all()
            ]);
            
            $soldadosData = $militares->map(function($militar) use ($existingBookings) {
                return [
                    'id' => $militar->id,
                    'full_name' => $militar->full_name,
                    'war_name' => $militar->war_name,
                    'rank_abbreviation' => $militar->rank->abbreviation ?? null,
                    'existing_bookings' => $existingBookings->get($militar->id, collect())->map(function($booking) {
                        return [
                            'meal_type' => $booking->meal_type,
                            'id' => $booking->id
                        ];
                    })
                ];
            });

            $stats = [
                'totalTargets' => $militares->count(),
                'totalSoldadosEv' => $militares->count(),
                'reservasNaData' => $existingBookings->flatten()->count()
            ];

            return response()->json([
                'soldados' => $soldadosData,
                'stats' => $stats
            ]);
        }

        // Adicionar estatísticas para a view
        $stats = [
            'totalTargets' => $militares->count(),
            'totalSoldadosEv' => $militares->count(),
            'reservasNaData' => $existingBookings->flatten()->count()
        ];

        // Adicionar as reservas existentes aos soldados
        foreach ($militares as $militar) {
            $militar->existingBookings = $existingBookings->get($militar->id, collect());
        }

        Log::info('Retornando view normal (não AJAX) para furriel arranchamento', [
            'date' => $selectedDateIso,
            'soldados_count' => $militares->count()
        ]);

        return view('furriel.arranchamento-cia', compact(
            'militares',
            'existingBookings', 
            'selectedDate',
            'selectedDateIso',
            'selectedDateDisplay',
            'furriel',
            'stats',
            'audience',
            'isHostOrganization'
        ));
    }

    /**
     * Store bookings for eligible users managed by the furriel
     */
    public function store(Request $request)
    {
        /** @var User|null $furriel */
        $furriel = Auth::user();

        if (!$furriel || !$furriel->isFurriel()) {
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
        $isHostOrganization = optional($furriel->organization)->is_host;
        
        // Verificar se é dia útil
        $carbonDate = Carbon::parse($bookingDate);
        if ($carbonDate->isWeekend()) {
            return back()->withErrors(['booking_date' => 'Não é possível fazer reservas para fins de semana.']);
        }

        // Nova regra: Não permitir arranchamento para o mesmo dia útil
        if ($carbonDate->isToday()) {
            return back()->withErrors(['booking_date' => 'Não é permitido arranchar para o mesmo dia útil.']);
        }

        // Verificar deadline de 13h para o dia seguinte
        if ($carbonDate->isTomorrow() && Carbon::now()->hour >= 13) {
            return back()->withErrors(['booking_date' => 'Não é possível fazer reservas para amanhã após às 13h de hoje.']);
        }

        // Verificar se é sexta-feira e tentando agendar almoço
        if ($carbonDate->isFriday()) {
            foreach ($request->bookings as $booking) {
                if (isset($booking['meals']) && in_array('lunch', $booking['meals'])) {
                    return back()->withErrors(['booking_date' => 'Não é possível agendar almoço para sexta-feira.']);
                }
            }
        }

        DB::transaction(function() use ($request, $furriel, $bookingDate, $isHostOrganization) {
            foreach ($request->bookings as $booking) {
                $user = User::find($booking['user_id']);
                
                // Verificar se o usuário pertence à mesma organização do furriel
                if ($user->organization_id !== $furriel->organization_id) {
                    continue;
                }

                if ($isHostOrganization) {
                    // Host: restringir à mesma subunidade e Soldado EV
                    if ($user->subunit !== $furriel->subunit) {
                        continue;
                    }

                    if (!$user->rank || $user->rank->name !== 'Soldado EV') {
                        continue;
                    }
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
        /** @var User|null $furriel */
        $furriel = Auth::user();

        if (!$furriel || !$furriel->isFurriel()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $isHostOrganization = optional($furriel->organization)->is_host;
        $today = Carbon::now()->format('Y-m-d');
        $thisWeek = [
            Carbon::now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d'),
            Carbon::now()->endOfWeek(Carbon::FRIDAY)->format('Y-m-d')
        ];

        $eligibleUsersQuery = User::query()
            ->where('organization_id', $furriel->organization_id)
            ->where('is_active', true);

        if ($isHostOrganization) {
            $eligibleUsersQuery
                ->where('subunit', $furriel->subunit)
                ->whereHas('rank', function($query) {
                    $query->where('name', 'Soldado EV');
                });
        }

        $eligibleUserIds = $eligibleUsersQuery->pluck('id');

        // Contagem de militares elegíveis
        $totalMilitares = $eligibleUserIds->count();

        // Reservas de hoje
        $todayBookings = Booking::whereDate('booking_date', $today)
            ->whereIn('user_id', $eligibleUserIds)
            ->count();

        // Reservas da semana
        $weekBookings = Booking::whereBetween('booking_date', $thisWeek)
            ->whereIn('user_id', $eligibleUserIds)
            ->count();

        return response()->json([
            'total_militares' => $totalMilitares,
            'total_soldados_ev' => $totalMilitares,
            'today_bookings' => $todayBookings,
            'week_bookings' => $weekBookings
        ]);
    }
}
