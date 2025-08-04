<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Organization;
use App\Models\WeeklyMenu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'current_month');
        
        // Calculate date range based on period
        $dateRange = $this->getDateRange($period);
        
        // Get chart data
        $chartData = [
            'dailyBookings' => $this->getDailyBookingsData($dateRange),
            'originBreakdown' => $this->getOriginBreakdownData($dateRange),
            'mealComparison' => $this->getMealComparisonData($dateRange),
            'topRanks' => $this->getTopRanksData($dateRange),
        ];

        // Get weekly menu for all users
        $weeklyMenu = null;
        $weekDates = null;
        if (Auth::user()) {
            $currentWeekMenu = WeeklyMenu::getCurrentWeekMenu();
            $weeklyMenu = $currentWeekMenu ? $currentWeekMenu->menu_data : null;
            
            // Calculate week dates
            $now = Carbon::now();
            $weekStart = $now->copy()->startOfWeek(Carbon::MONDAY);
            
            // Se for sexta-feira, sábado ou domingo, pega a próxima semana
            if ($now->dayOfWeek >= Carbon::FRIDAY) {
                $weekStart->addWeek();
            }

            $weekDates = [
                'segunda' => $weekStart->copy(),
                'terca' => $weekStart->copy()->addDay(),
                'quarta' => $weekStart->copy()->addDays(2),
                'quinta' => $weekStart->copy()->addDays(3),
                'sexta' => $weekStart->copy()->addDays(4)
            ];
        }
        
        return view('dashboard.index', compact('chartData', 'period', 'weeklyMenu', 'weekDates'));
    }
    
    private function getDateRange($period)
    {
        $now = Carbon::now();
        
        switch($period) {
            case 'last_30_days':
                return [
                    'start' => $now->copy()->subDays(30),
                    'end' => $now
                ];
            case 'last_week':
                return [
                    'start' => $now->copy()->subWeek(),
                    'end' => $now
                ];
            case 'current_month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
            default:
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
        }
    }
    
    private function getDailyBookingsData($dateRange)
    {
        try {
            $bookings = Booking::whereBetween('booking_date', [
                    $dateRange['start']->format('Y-m-d'), 
                    $dateRange['end']->format('Y-m-d')
                ])
                ->select(DB::raw('booking_date as date, COUNT(*) as total'))
                ->groupBy('booking_date')
                ->orderBy('booking_date')
                ->get();
                
            return $bookings->mapWithKeys(function ($booking) {
                return [Carbon::parse($booking->date)->format('d/m') => $booking->total];
            });
        } catch (\Exception $e) {
            // Return empty data if error
            return collect([]);
        }
    }
    
    private function getOriginBreakdownData($dateRange)
    {
        try {
            $data = Booking::whereBetween('booking_date', [
                    $dateRange['start']->format('Y-m-d'), 
                    $dateRange['end']->format('Y-m-d')
                ])
                ->join('users', 'bookings.user_id', '=', 'users.id')
                ->join('organizations', 'users.organization_id', '=', 'organizations.id')
                ->select(
                    DB::raw('CASE WHEN organizations.is_host = true THEN \'Própria OM\' ELSE \'Outras OMs\' END as origin'),
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('organizations.is_host')
                ->get();
                
            return $data->mapWithKeys(function ($item) {
                return [$item->origin => $item->total];
            });
        } catch (\Exception $e) {
            // Return default data if error
            return collect(['Própria OM' => 0, 'Outras OMs' => 0]);
        }
    }
    
    private function getMealComparisonData($dateRange)
    {
        try {
            $data = Booking::whereBetween('booking_date', [
                    $dateRange['start']->format('Y-m-d'), 
                    $dateRange['end']->format('Y-m-d')
                ])
                ->select(
                    DB::raw('EXTRACT(dow FROM booking_date::date) as day_of_week'),
                    'meal_type',
                    DB::raw('COUNT(*) as total')
                )
                ->whereRaw('EXTRACT(dow FROM booking_date::date) BETWEEN 1 AND 5') // Monday to Friday
                ->groupBy(DB::raw('EXTRACT(dow FROM booking_date::date)'), 'meal_type')
                ->get();
                
            $dayNames = ['', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'];
            $result = [];
            
            foreach ($data as $booking) {
                $dayName = $dayNames[$booking->day_of_week] ?? '';
                $mealType = $booking->meal_type === 'breakfast' ? 'Café' : 'Almoço';
                
                if (!isset($result[$dayName])) {
                    $result[$dayName] = ['Café' => 0, 'Almoço' => 0];
                }
                
                $result[$dayName][$mealType] = $booking->total;
            }
            
            return $result;
        } catch (\Exception $e) {
            // Return default data if error
            return [
                'Segunda' => ['Café' => 0, 'Almoço' => 0],
                'Terça' => ['Café' => 0, 'Almoço' => 0],
                'Quarta' => ['Café' => 0, 'Almoço' => 0],
                'Quinta' => ['Café' => 0, 'Almoço' => 0],
                'Sexta' => ['Café' => 0, 'Almoço' => 0],
            ];
        }
    }
    
    private function getTopRanksData($dateRange)
    {
        try {
            return Booking::whereBetween('booking_date', [
                    $dateRange['start']->format('Y-m-d'), 
                    $dateRange['end']->format('Y-m-d')
                ])
                ->join('users', 'bookings.user_id', '=', 'users.id')
                ->join('ranks', 'users.rank_id', '=', 'ranks.id')
                ->select('ranks.name', DB::raw('COUNT(*) as total'))
                ->groupBy('ranks.id', 'ranks.name')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->name => $item->total];
                });
        } catch (\Exception $e) {
            // Return empty data if error
            return collect([]);
        }
    }
}
