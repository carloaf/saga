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
        
        // Calendar data
        $calendarMonth = request('month', Carbon::now()->format('Y-m'));
        $calendarDate = Carbon::createFromFormat('Y-m', $calendarMonth)->startOfMonth();
        
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
            $weekStart = $now->copy()->startOfWeek(Carbon::MONDAY);
            
            // If it's already past Monday morning, start from current day
            if ($now->isMonday() && $now->hour >= 12) {
                // If it's Monday afternoon, start from Tuesday
                $weekStart = $now->copy()->addDay()->startOfDay();
            } elseif ($now->dayOfWeek > Carbon::MONDAY) {
                // If it's Tuesday or later, start from current day
                $weekStart = $now->copy()->startOfDay();
            }
            
            $reservations = [];
            $errors = [];
            
            // Reserve for weekdays only (Monday to Friday)
            for ($i = 0; $i < 5; $i++) {
                $date = $weekStart->copy()->addDays($i);
                
                // Skip weekends
                if ($date->isWeekend()) {
                    continue;
                }
                
                // Skip if date is in the past
                if ($date->isPast() && !$date->isToday()) {
                    continue;
                }
                
                // Check if booking deadline has passed
                if ($this->hasBookingDeadlinePassed($date)) {
                    if ($date->isTomorrow()) {
                        $errors[] = "⏰ Café da manhã - " . $date->format('d/m/Y') . "\nPrazo encerrado às 13h de hoje";
                    } else {
                        $errors[] = "⏰ Café da manhã - " . $date->format('d/m/Y') . "\nPrazo para reserva expirou";
                    }
                    continue;
                }
                
                // Skip if it's today and already past breakfast time (assuming 12:00)
                if ($date->isToday() && $now->hour >= 12) {
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
                }
            }
            
            if (count($reservations) > 0) {
                $message = "✅ " . count($reservations) . " reserva(s) de café realizadas!";
                if (count($errors) > 0) {
                    $message .= "\n\n⚠️ Avisos:\n" . implode("\n", $errors);
                }
                return response()->json(['success' => true, 'message' => $message, 'bookings' => count($reservations), 'week_start' => $weekStart->format('d/m/Y')]);
            } else {
                return response()->json(['success' => false, 'message' => 'Nenhuma reserva realizada.', 'type' => 'warning']);
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
            $weekStart = $now->copy()->startOfWeek(Carbon::MONDAY);
            
            // If it's already past Monday afternoon, start from current day
            if ($now->isMonday() && $now->hour >= 15) {
                // If it's Monday late afternoon, start from Tuesday
                $weekStart = $now->copy()->addDay()->startOfDay();
            } elseif ($now->dayOfWeek > Carbon::MONDAY) {
                // If it's Tuesday or later, start from current day
                $weekStart = $now->copy()->startOfDay();
            }
            
            $reservations = [];
            $errors = [];
            
            // Reserve for weekdays only (Monday to Thursday - no lunch on Friday)
            for ($i = 0; $i < 4; $i++) {
                $date = $weekStart->copy()->addDays($i);
                
                // Skip weekends
                if ($date->isWeekend()) {
                    continue;
                }
                
                // Skip Friday (no lunch available)
                if ($date->isFriday()) {
                    continue;
                }
                
                // Skip if date is in the past
                if ($date->isPast() && !$date->isToday()) {
                    continue;
                }
                
                // Check if booking deadline has passed
                if ($this->hasBookingDeadlinePassed($date)) {
                    if ($date->isTomorrow()) {
                        $errors[] = "⏰ Almoço - " . $date->format('d/m/Y') . "\nPrazo encerrado às 13h de hoje";
                    } else {
                        $errors[] = "⏰ Almoço - " . $date->format('d/m/Y') . "\nPrazo para reserva expirou";
                    }
                    continue;
                }
                
                // Skip if it's today and already past lunch time (assuming 15:00)
                if ($date->isToday() && $now->hour >= 15) {
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
                }
            }
            
            if (count($reservations) > 0) {
                $message = "✅ " . count($reservations) . " reserva(s) de almoço realizadas!";
                if (count($errors) > 0) {
                    $message .= "\n\n⚠️ Avisos:\n" . implode("\n", $errors);
                }
                return response()->json(['success' => true, 'message' => $message, 'bookings' => count($reservations), 'week_start' => $weekStart->format('d/m/Y')]);
            } else {
                return response()->json(['success' => false, 'message' => 'Nenhuma reserva realizada.', 'type' => 'warning']);
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
            $request->validate([
                'date' => 'required|date|after_or_equal:today',
                'meal_type' => 'required|in:breakfast,lunch'
            ]);

            $user = Auth::user();
            $date = Carbon::parse($request->date);
            $now = Carbon::now();
            
            // Check if it's a weekday
            if ($date->isWeekend()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservas só podem ser feitas para dias úteis (segunda a sexta-feira).'
                ], 400);
            }
            
            // Check if booking deadline has passed
            if ($this->hasBookingDeadlinePassed($date)) {
                return response()->json([
                    'success' => false,
                    'message' => $this->getDeadlineMessage($date)
                ], 400);
            }
            
            // Check if it's Friday and trying to book lunch
            if ($date->isFriday() && $request->meal_type === 'lunch') {
                return response()->json([
                    'success' => false,
                    'message' => 'Almoço não está disponível nas sextas-feiras.'
                ], 400);
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
            
            $mealTypeName = $request->meal_type === 'breakfast' ? 'café da manhã' : 'almoço';
            
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
