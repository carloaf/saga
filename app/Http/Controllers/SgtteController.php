<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SgtteController extends Controller
{
    /**
     * Listagem de militares da companhia com seleção de refeições (café, almoço, jantar) para o dia seguinte.
     * Filtro por nome de guerra.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isSgtte()) {
            abort(403, 'Acesso negado. Apenas sargentos podem acessar esta página.');
        }

        $search = $request->get('q');
        $targetDate = $request->get('date');
        // Dia padrão: amanhã (mas permitir override)
        $targetDate = $targetDate ? Carbon::parse($targetDate) : Carbon::now()->addDay();

        // Buscar todos os militares ativos da mesma organização/subunidade
        $query = User::where('organization_id', $user->organization_id)
            ->where('subunit', $user->subunit)
            ->where('is_active', true)
            ->with(['rank','organization']);
        if ($search) {
            $query->where('war_name', 'ilike', "%$search%");
        }
        $militares = $query->orderBy('war_name')->get();

        // Reservas existentes na data alvo
        $existing = Booking::whereDate('booking_date', $targetDate->format('Y-m-d'))
            ->whereIn('user_id', $militares->pluck('id'))
            ->get()
            ->groupBy('user_id');

        $editable = $targetDate->isAfter(Carbon::today()); // Somente datas futuras (> hoje) podem ser editadas

        return view('sgtte.servico', [
            'militares' => $militares,
            'targetDate' => $targetDate->format('Y-m-d'),
            'existingBookings' => $existing,
            'search' => $search,
            'editable' => $editable,
        ]);
    }

    /**
     * Armazenar reservas selecionadas (sem restrição de horário, incluindo jantar)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isSgtte()) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            // Proibir salvar para hoje ou passado: somente futuro estrito
            'booking_date' => 'required|date|after:today',
            'bookings' => 'required|array',
            'bookings.*.user_id' => 'required|exists:users,id',
            'bookings.*.meals' => 'array',
            'bookings.*.meals.*' => 'in:breakfast,lunch,dinner'
        ]);

        $bookingDate = Carbon::parse($request->booking_date)->format('Y-m-d');

        DB::transaction(function() use ($request, $user, $bookingDate) {
            foreach ($request->bookings as $booking) {
                $targetUser = User::find($booking['user_id']);
                if (!$targetUser) continue;
                if ($targetUser->organization_id !== $user->organization_id || $targetUser->subunit !== $user->subunit) continue;

                // Remover reservas existentes para esta data (todas as refeições) para garantir substituição limpa
                Booking::where('user_id', $targetUser->id)
                    ->whereDate('booking_date', $bookingDate)
                    ->delete();

                if (!empty($booking['meals'])) {
                    foreach ($booking['meals'] as $mealType) {
                        Booking::create([
                            'user_id' => $targetUser->id,
                            'booking_date' => $bookingDate,
                            'meal_type' => $mealType,
                            'status' => 'confirmed',
                            'created_by_operator' => $user->id
                        ]);
                    }
                }
            }
        });

        return redirect()->route('sgtte.servico', ['date' => $bookingDate])
            ->with('success', 'Serviço atualizado com sucesso!');
    }

    /**
     * Retorna reservas existentes (por usuário) para uma data via JSON para atualização dinâmica da tabela.
     */
    public function getBookings(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isSgtte()) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'date' => 'required|date',
            'user_ids' => 'array'
        ]);

        $targetDate = Carbon::parse($request->get('date'))->format('Y-m-d');
        $userIds = collect($request->get('user_ids', []))->filter()->map(fn($id) => (int)$id)->values();

        if ($userIds->isEmpty()) {
            // Se não vier a lista, limitar aos da subunidade para evitar vazar dados de outros contextos
            $userIds = User::where('organization_id', $user->organization_id)
                ->where('subunit', $user->subunit)
                ->where('is_active', true)
                ->pluck('id');
        }

        // Garantir que todos os IDs pertencem à mesma organização/subunidade do sgtte
        $validIds = User::whereIn('id', $userIds)
            ->where('organization_id', $user->organization_id)
            ->where('subunit', $user->subunit)
            ->pluck('id');

        $bookings = Booking::whereDate('booking_date', $targetDate)
            ->whereIn('user_id', $validIds)
            ->get()
            ->groupBy('user_id')
            ->map(function($grp) {
                return $grp->pluck('meal_type')->values();
            });

        return response()->json([
            'date' => $targetDate,
            'bookings' => $bookings,
            'count_users' => $validIds->count()
        ]);
    }
}
