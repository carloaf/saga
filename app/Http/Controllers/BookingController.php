<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Check if booking deadline has passed for a given date
     * Rule: After 13:00 of the current day, block bookings for the next day
     */
    private function hasBookingDeadlinePassed($bookingDate)
    {
        $now = Carbon::now();
        $bookingDateCarbon = Carbon::parse($bookingDate);
        
        // If trying to book for today, always allow (handled by other logic)
        if ($bookingDateCarbon->isToday()) {
            return false;
        }
        
        // If trying to book for tomorrow and it's past 13:00 today, block it
        if ($bookingDateCarbon->isTomorrow() && $now->hour >= 13) {
            return true;
        }
        
        // For dates further in the future, check if it's past 13:00 of the day before
        $deadlineDateTime = $bookingDateCarbon->copy()->subDay()->setTime(13, 0, 0);
        
        return $now->gte($deadlineDateTime);
    }

    /**
     * Get the deadline message for a given booking date
     */
    private function getDeadlineMessage($bookingDate)
    {
        $bookingDateCarbon = Carbon::parse($bookingDate);
        
        if ($bookingDateCarbon->isTomorrow()) {
            return 'Não é possível fazer reservas para amanhã após às 13h de hoje.';
        }
        
        $deadlineDateTime = $bookingDateCarbon->copy()->subDay()->setTime(13, 0, 0);
        return 'Prazo expirado para ' . $bookingDateCarbon->format('d/m/Y') . ' (limite: ' . $deadlineDateTime->format('d/m/Y \à\s H:i') . ')';
    }

    /**
     * Display the bookings index page
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get upcoming bookings
        $upcomingBookings = Booking::select('*')
            ->where('user_id', $user->id)
            ->where('booking_date', '>=', Carbon::today())
            ->orderBy('booking_date')
            ->orderBy('meal_type')
            ->get();
        
        // Get current month statistics
        $currentMonth = Carbon::now();
        $monthlyStats = Booking::where('user_id', $user->id)
            ->whereYear('booking_date', $currentMonth->year)
            ->whereMonth('booking_date', $currentMonth->month)
            ->get();
        
        $totalMeals = $monthlyStats->count();
        $breakfastCount = $monthlyStats->where('meal_type', 'breakfast')->count();
    $lunchCount = $monthlyStats->where('meal_type', 'lunch')->count();
    $dinnerCount = $monthlyStats->where('meal_type', 'dinner')->count();
    $dinnerCount = $monthlyStats->where('meal_type', 'dinner')->count();
        
        // Calendar data
        $calendarMonth = request('month', Carbon::now()->format('Y-m'));
        // Fix: Ensure we start from day 1 to avoid date overflow issues (e.g., Oct 31 -> Nov 31 = Dec 1)
        $calendarDate = Carbon::createFromFormat('Y-m-d', $calendarMonth . '-01')->startOfMonth();
        
        // Calculate calendar range (including previous/next month days that appear in calendar)
        $startOfCalendar = $calendarDate->copy()->startOfWeek(Carbon::SUNDAY);
        $endOfCalendar = $calendarDate->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);
        
        // Get bookings for the entire calendar period (not just the current month)
        $monthBookings = Booking::where('user_id', $user->id)
            ->whereBetween('booking_date', [
                $startOfCalendar->format('Y-m-d'),
                $endOfCalendar->format('Y-m-d')
            ])
            ->get()
            ->groupBy(function($booking) {
                return Carbon::parse($booking->booking_date)->format('Y-m-d');
            });
        
        return view('bookings.index', compact(
            'upcomingBookings', 
            'totalMeals', 
            'breakfastCount', 
            'lunchCount',
            'dinnerCount',
            'calendarDate',
            'monthBookings'
        ));
    }

    /**
     * Display booking history
     */
    public function history()
    {
        $user = Auth::user();
        
        $allBookings = Booking::where('user_id', $user->id)
            ->orderBy('booking_date', 'desc')
            ->orderBy('meal_type')
            ->paginate(20);
        
        // Get monthly statistics for the last 6 months
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthBookings = Booking::where('user_id', $user->id)
                ->whereYear('booking_date', $month->year)
                ->whereMonth('booking_date', $month->month)
                ->get();
            
            $monthlyStats[] = [
                'month' => $month->format('M/Y'),
                'month_name' => $month->translatedFormat('F Y'),
                'total' => $monthBookings->count(),
                'breakfast' => $monthBookings->where('meal_type', 'breakfast')->count(),
                'lunch' => $monthBookings->where('meal_type', 'lunch')->count(),
                'dinner' => $monthBookings->where('meal_type', 'dinner')->count(),
            ];
        }
        
        return view('bookings.history', compact('allBookings', 'monthlyStats'));
    }

    /**
     * Reserve breakfast for the week
     */
    public function reserveBreakfastWeek(Request $request)
    {
        try {
            $user = Auth::user();
            $now = Carbon::now();

            // Sempre reservar para a próxima semana (segunda a sexta)
            $nextWeekStart = $now->copy()->startOfWeek(Carbon::MONDAY)->addWeek();

            $reservations = [];
            $errors = [];

            // Reserve for weekdays only (Monday to Friday)
            for ($i = 0; $i < 5; $i++) {
                $date = $nextWeekStart->copy()->addDays($i);

                // Skip weekends (Saturday and Sunday)
                if ($date->isWeekend()) {
                    continue;
                }

                // Check if booking deadline has passed (13:00 of the day before)
                if ($this->hasBookingDeadlinePassed($date)) {
                    $errors[] = "⏰ Café da manhã - " . $date->format('d/m/Y') . "\nPrazo encerrado às 13h de " . $date->copy()->subDay()->format('d/m/Y');
                    continue;
                }

                // Skip if it's today (same day rule)
                if ($date->isToday()) {
                    $errors[] = "⏰ Café da manhã - " . $date->format('d/m/Y') . "\nNão é possível reservar para o mesmo dia";
                    continue;
                }

                // Check if booking already exists
                $existingBooking = Booking::where('user_id', $user->id)
                    ->where('booking_date', $date->format('Y-m-d'))
                    ->where('meal_type', 'breakfast')
                    ->first();

                if (!$existingBooking) {
                    try {
                        $booking = Booking::create([
                            'user_id' => $user->id,
                            'booking_date' => $date->format('Y-m-d'),
                            'meal_type' => 'breakfast',
                            'status' => 'confirmed'
                        ]);
                        $reservations[] = $booking;
                    } catch (\Exception $e) {
                        $errors[] = "Erro ao reservar café da manhã para " . $date->format('d/m/Y') . ": " . $e->getMessage();
                    }
                } else {
                    $errors[] = "⚠️ Café da manhã - " . $date->format('d/m/Y') . "\nJá possui reserva";
                }
            }

            if (count($reservations) > 0) {
                $message = "✅ " . count($reservations) . " reserva(s) de café realizadas!\n\n📅 Semana: " . $nextWeekStart->format('d/m/Y') . " - " . $nextWeekStart->copy()->endOfWeek(Carbon::FRIDAY)->format('d/m/Y');
                if (count($errors) > 0) {
                    $message .= "\n\n⚠️ Avisos:\n" . implode("\n", $errors);
                }
                return response()->json(['success' => true, 'message' => $message, 'bookings' => count($reservations), 'week_start' => $nextWeekStart->format('d/m/Y')]);
            } else {
                $errorMessage = "Nenhuma reserva realizada.";
                if (count($errors) > 0) {
                    $errorMessage .= "\n\n⚠️ Detalhes:\n" . implode("\n", $errors);
                }
                return response()->json(['success' => false, 'message' => $errorMessage, 'type' => 'warning']);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer reservas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reserve lunch for the week
     */
    public function reserveLunchWeek(Request $request)
    {
        try {
            $user = Auth::user();
            $now = Carbon::now();

            // Sempre reservar para a próxima semana (segunda a quinta - sem sexta)
            $nextWeekStart = $now->copy()->startOfWeek(Carbon::MONDAY)->addWeek();

            $reservations = [];
            $errors = [];

            // Reserve for weekdays only (Monday to Thursday - no lunch on Friday)
            for ($i = 0; $i < 4; $i++) {
                $date = $nextWeekStart->copy()->addDays($i);

                // Skip weekends
                if ($date->isWeekend()) {
                    continue;
                }

                // Skip Friday (no lunch available)
                if ($date->isFriday()) {
                    continue;
                }

                // Check if booking deadline has passed (13:00 of the day before)
                if ($this->hasBookingDeadlinePassed($date)) {
                    $errors[] = "⏰ Almoço - " . $date->format('d/m/Y') . "\nPrazo encerrado às 13h de " . $date->copy()->subDay()->format('d/m/Y');
                    continue;
                }

                // Skip if it's today (same day rule)
                if ($date->isToday()) {
                    $errors[] = "⏰ Almoço - " . $date->format('d/m/Y') . "\nNão é possível reservar para o mesmo dia";
                    continue;
                }

                // Check if booking already exists
                $existingBooking = Booking::where('user_id', $user->id)
                    ->where('booking_date', $date->format('Y-m-d'))
                    ->where('meal_type', 'lunch')
                    ->first();

                if (!$existingBooking) {
                    try {
                        $booking = Booking::create([
                            'user_id' => $user->id,
                            'booking_date' => $date->format('Y-m-d'),
                            'meal_type' => 'lunch',
                            'status' => 'confirmed'
                        ]);
                        $reservations[] = $booking;
                    } catch (\Exception $e) {
                        $errors[] = "Erro ao reservar almoço para " . $date->format('d/m/Y') . ": " . $e->getMessage();
                    }
                } else {
                    $errors[] = "⚠️ Almoço - " . $date->format('d/m/Y') . "\nJá possui reserva";
                }
            }

            if (count($reservations) > 0) {
                $message = "✅ " . count($reservations) . " reserva(s) de almoço realizadas!\n\n📅 Semana: " . $nextWeekStart->format('d/m/Y') . " - " . $nextWeekStart->copy()->endOfWeek(Carbon::THURSDAY)->format('d/m/Y');
                if (count($errors) > 0) {
                    $message .= "\n\n⚠️ Avisos:\n" . implode("\n", $errors);
                }
                return response()->json(['success' => true, 'message' => $message, 'bookings' => count($reservations), 'week_start' => $nextWeekStart->format('d/m/Y')]);
            } else {
                $errorMessage = "Nenhuma reserva realizada.";
                if (count($errors) > 0) {
                    $errorMessage .= "\n\n⚠️ Detalhes:\n" . implode("\n", $errors);
                }
                return response()->json(['success' => false, 'message' => $errorMessage, 'type' => 'warning']);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer reservas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Make a single booking
     */
    public function reserveSingle(Request $request)
    {
        try {
            $user = Auth::user();

            $allowedMeals = ['breakfast','lunch'];
            if ($user && method_exists($user,'isLaranjeira') && $user->isLaranjeira()) {
                $allowedMeals[] = 'dinner';
            }

            $request->validate([
                'date' => 'required|date|after_or_equal:today',
                'meal_type' => 'required|in:'.implode(',', $allowedMeals)
            ]);
            $date = Carbon::parse($request->date);
            $now = Carbon::now();
            
            // Weekend handling: only allow dinner for Laranjeira on weekends
            if ($date->isWeekend()) {
                if (!$user || !method_exists($user,'isLaranjeira') || !$user->isLaranjeira()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Reservas em fins de semana são exclusivas para usuários Laranjeira.'
                    ], 400);
                }
            }
            
            // Check if booking deadline has passed
            if ($this->hasBookingDeadlinePassed($date)) {
                return response()->json([
                    'success' => false,
                    'message' => $this->getDeadlineMessage($date)
                ], 400);
            }
            
            // Check if it's Friday and trying to book lunch (still forbidden) or dinner (rule? allow only if not Friday? keep dinner allowed all weekdays except maybe Friday per existing logic) - manter almoço proibido sexta
            if ($date->isFriday() && $request->meal_type === 'lunch') {
                return response()->json([
                    'success' => false,
                    'message' => 'Almoço não está disponível nas sextas-feiras.'
                ], 400);
            }

            // Dinner availability: permitir somente para usuários Laranjeira e dias úteis (segunda a quinta inicialmente)
            if ($request->meal_type === 'dinner') {
                if (!$user->isLaranjeira()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jantar disponível apenas para usuários com status Laranjeira.'
                    ], 400);
                }
                // Dinner allowed on weekend only for Laranjeira (already filtered above)
                // (Opcional) Bloquear sexta se regra exigir. Ajustar se necessário.
            }
            
            // Check if booking already exists
            $existingBooking = Booking::where('user_id', $user->id)
                ->where('booking_date', $date->format('Y-m-d'))
                ->where('meal_type', $request->meal_type)
                ->first();
            
            if ($existingBooking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você já possui uma reserva para esta refeição neste dia.'
                ], 400);
            }
            
            // Create booking
            $booking = Booking::create([
                'user_id' => $user->id,
                'booking_date' => $date->format('Y-m-d'),
                'meal_type' => $request->meal_type,
                'status' => 'confirmed'
            ]);
            
            $mealTypeName = match($request->meal_type) {
                'breakfast' => 'café da manhã',
                'lunch' => 'almoço',
                'dinner' => 'jantar',
                default => $request->meal_type
            };
            
            return response()->json([
                'success' => true,
                'message' => "Reserva de {$mealTypeName} para " . $date->format('d/m/Y') . " realizada com sucesso!",
                'booking' => $booking
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer reserva: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user bookings for AJAX calendar
     */
    public function getUserBookings(Request $request)
    {
        $user = Auth::user();
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        
        try {
            $calendarDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Exception $e) {
            $calendarDate = Carbon::now()->startOfMonth();
        }
        
        // Calculate calendar range (including previous/next month days that appear in calendar)
        $startOfCalendar = $calendarDate->copy()->startOfWeek(Carbon::SUNDAY);
        $endOfCalendar = $calendarDate->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);
        
        $bookings = Booking::where('user_id', $user->id)
            ->whereBetween('booking_date', [
                $startOfCalendar->format('Y-m-d'),
                $endOfCalendar->format('Y-m-d')
            ])
            ->get()
            ->groupBy(function($booking) {
                return Carbon::parse($booking->booking_date)->format('Y-m-d');
            });
        
        return response()->json([
            'success' => true,
            'bookings' => $bookings,
            'month' => $calendarDate->format('Y-m')
        ]);
    }

    /**
     * Cancel a booking
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $booking = Booking::where('id', $id)
                ->where('user_id', $user->id)
                ->first();
            
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reserva não encontrada.'
                ], 404);
            }
            
            $bookingDate = Carbon::parse($booking->booking_date);
            $now = Carbon::now();
            
            // Check if booking is for today or in the past
            if ($bookingDate->lte($now->copy()->startOfDay())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível cancelar reservas do dia atual ou de dias passados.'
                ], 400);
            }
            
            // Check if booking deadline has passed (same rule for cancellation)
            if ($this->hasBookingDeadlinePassed($bookingDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível cancelar reservas: ' . $this->getDeadlineMessage($bookingDate)
                ], 400);
            }
            
            $booking->delete();
            
            $mealTypeName = $booking->meal_type === 'breakfast' ? 'café da manhã' : 'almoço';
            
            return response()->json([
                'success' => true,
                'message' => "Reserva de {$mealTypeName} para " . $bookingDate->format('d/m/Y') . " cancelada com sucesso!"
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cancelar reserva: ' . $e->getMessage()
            ], 500);
        }
    }
}
