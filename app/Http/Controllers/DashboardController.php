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
        
        // Get quick stats for header cards
    $todayStats = $this->getTodayStats();
    $weekStats = $this->getWeekStats();
    $monthStats = $this->getMonthStats();
    $upcomingMeals = $this->getUpcomingMealsCount();
        
        // Get chart stats for dashboard
        $chartStats = $this->getChartStats();
        
        // Get origin breakdown data for cards
        $originData = $this->getOriginBreakdownData($dateRange);
        
        // Get chart data
        $chartData = [
            'dailyBookings' => $this->getDailyBookingsData($dateRange),
            'originBreakdown' => $originData,
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
        
        return view('dashboard.index', compact(
            'chartData', 
            'period', 
            'weeklyMenu', 
            'weekDates',
            'todayStats',
            'weekStats',
            'monthStats',
            'upcomingMeals',
            'chartStats',
            'originData'
        ));
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
            // Get data from the last 7 days for better visualization
            $endDate = Carbon::now();
            $startDate = $endDate->copy()->subDays(6); // Last 7 days including today
            
            $bookings = Booking::whereBetween('booking_date', [
                    $startDate->format('Y-m-d'), 
                    $endDate->format('Y-m-d')
                ])
                ->select(
                    DB::raw('booking_date as date'), 
                    'meal_type',
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('booking_date', 'meal_type')
                ->orderBy('booking_date')
                ->get();
            
            // Create arrays with all 7 days, filling missing days with 0
            $totalData = [];
            $breakfastData = [];
            $lunchData = [];
            
            for ($i = 6; $i >= 0; $i--) {
                $date = $endDate->copy()->subDays($i);
                $dateKey = $date->format('d/m');
                $dateFormatted = $date->format('Y-m-d');
                
                // Find bookings for this date
                $dayBookings = $bookings->where('date', $dateFormatted);
                $breakfastCount = $dayBookings->where('meal_type', 'breakfast')->sum('total');
                $lunchCount = $dayBookings->where('meal_type', 'lunch')->sum('total');
                $totalCount = $breakfastCount + $lunchCount;
                
                $totalData[$dateKey] = $totalCount;
                $breakfastData[$dateKey] = $breakfastCount;
                $lunchData[$dateKey] = $lunchCount;
            }
                
            return [
                'total' => $totalData,
                'breakfast' => $breakfastData,
                'lunch' => $lunchData
            ];
        } catch (\Exception $e) {
            // Return empty data with last 7 days if error
            $endDate = Carbon::now();
            $result = ['total' => [], 'breakfast' => [], 'lunch' => []];
            
            for ($i = 6; $i >= 0; $i--) {
                $date = $endDate->copy()->subDays($i);
                $dateKey = $date->format('d/m');
                $result['total'][$dateKey] = 0;
                $result['breakfast'][$dateKey] = 0;
                $result['lunch'][$dateKey] = 0;
            }
            
            return $result;
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
                ->leftJoin('organizations', 'users.organization_id', '=', 'organizations.id')
                ->select(
                    DB::raw('CASE 
                        WHEN organizations.name = \'11º D Sup\' THEN \'Própria OM\'
                        WHEN users.armed_force = \'EB\' AND (organizations.name != \'11º D Sup\' OR organizations.name IS NULL) THEN \'Outras OM\'
                        WHEN users.armed_force IN (\'MB\', \'FAB\') THEN \'Outras Forças\'
                        ELSE \'Outras OM\'
                    END as origin'),
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy(DB::raw('CASE 
                    WHEN organizations.name = \'11º D Sup\' THEN \'Própria OM\'
                    WHEN users.armed_force = \'EB\' AND (organizations.name != \'11º D Sup\' OR organizations.name IS NULL) THEN \'Outras OM\'
                    WHEN users.armed_force IN (\'MB\', \'FAB\') THEN \'Outras Forças\'
                    ELSE \'Outras OM\'
                END'))
                ->get();
                
            $result = collect([
                'Própria OM' => 0,
                'Outras OM' => 0,
                'Outras Forças' => 0
            ]);
            
            foreach ($data as $item) {
                $result[$item->origin] = $item->total;
            }
                
            return $result;
        } catch (\Exception $e) {
            // Return default data if error
            return collect([
                'Própria OM' => 0,
                'Outras OM' => 0,
                'Outras Forças' => 0
            ]);
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
    
    /**
     * Get today's booking statistics
     */
    private function getTodayStats()
    {
        try {
            $today = Carbon::now()->format('Y-m-d');
            
            $total = Booking::whereDate('booking_date', $today)->count();
            $breakfast = Booking::whereDate('booking_date', $today)
                ->where('meal_type', 'breakfast')
                ->count();
            $lunch = Booking::whereDate('booking_date', $today)
                ->where('meal_type', 'lunch')
                ->count();
            $dinner = Booking::whereDate('booking_date', $today)
                ->where('meal_type', 'dinner')
                ->count();
            
            return [
                'total' => $total,
                'breakfast' => $breakfast,
                'lunch' => $lunch,
                'dinner' => $dinner
            ];
        } catch (\Exception $e) {
            return ['total' => 0, 'breakfast' => 0, 'lunch' => 0, 'dinner' => 0];
        }
    }
    
    /**
     * Get this week's booking statistics
     */
    private function getWeekStats()
    {
        try {
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
            $endOfWeek = Carbon::now()->endOfWeek(Carbon::FRIDAY); // Only count weekdays
            
            $total = Booking::whereBetween('booking_date', [
                $startOfWeek->format('Y-m-d'),
                $endOfWeek->format('Y-m-d')
            ])->count();

            $breakfast = Booking::whereBetween('booking_date', [
                $startOfWeek->format('Y-m-d'),
                $endOfWeek->format('Y-m-d')
            ])->where('meal_type', 'breakfast')->count();

            $lunch = Booking::whereBetween('booking_date', [
                $startOfWeek->format('Y-m-d'),
                $endOfWeek->format('Y-m-d')
            ])->where('meal_type', 'lunch')->count();

            $dinner = Booking::whereBetween('booking_date', [
                $startOfWeek->format('Y-m-d'),
                $endOfWeek->format('Y-m-d')
            ])->where('meal_type', 'dinner')->count();
            
            return [
                'total' => $total,
                'breakfast' => $breakfast,
                'lunch' => $lunch,
                'dinner' => $dinner
            ];
        } catch (\Exception $e) {
            return ['total' => 0, 'breakfast' => 0, 'lunch' => 0, 'dinner' => 0];
        }
    }
    
    /**
     * Get this month's booking statistics
     */
    private function getMonthStats()
    {
        try {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            
            $total = Booking::whereBetween('booking_date', [
                $startOfMonth->format('Y-m-d'),
                $endOfMonth->format('Y-m-d')
            ])->count();

            $breakfast = Booking::whereBetween('booking_date', [
                $startOfMonth->format('Y-m-d'),
                $endOfMonth->format('Y-m-d')
            ])->where('meal_type', 'breakfast')->count();

            $lunch = Booking::whereBetween('booking_date', [
                $startOfMonth->format('Y-m-d'),
                $endOfMonth->format('Y-m-d')
            ])->where('meal_type', 'lunch')->count();

            $dinner = Booking::whereBetween('booking_date', [
                $startOfMonth->format('Y-m-d'),
                $endOfMonth->format('Y-m-d')
            ])->where('meal_type', 'dinner')->count();
            
            return [
                'total' => $total,
                'breakfast' => $breakfast,
                'lunch' => $lunch,
                'dinner' => $dinner
            ];
        } catch (\Exception $e) {
            return ['total' => 0, 'breakfast' => 0, 'lunch' => 0, 'dinner' => 0];
        }
    }
    
    /**
     * Get count of tomorrow's meals with breakfast and lunch breakdown
     */
    private function getUpcomingMealsCount()
    {
        try {
            $tomorrow = Carbon::now()->addDay()->format('Y-m-d');
            
            $totalCount = Booking::whereDate('booking_date', $tomorrow)->count();
            $breakfastCount = Booking::whereDate('booking_date', $tomorrow)
                ->where('meal_type', 'breakfast')
                ->count();
            $lunchCount = Booking::whereDate('booking_date', $tomorrow)
                ->where('meal_type', 'lunch')
                ->count();
            $dinnerCount = Booking::whereDate('booking_date', $tomorrow)
                ->where('meal_type', 'dinner')
                ->count();
            
            return [
                'total' => $totalCount,
                'breakfast' => $breakfastCount,
                'lunch' => $lunchCount,
                'dinner' => $dinnerCount
            ];
        } catch (\Exception $e) {
            return ['total' => 0, 'breakfast' => 0, 'lunch' => 0, 'dinner' => 0];
        }
    }
    
    /**
     * Get chart statistics for dashboard
     */
    private function getChartStats()
    {
        try {
            $today = Carbon::now()->format('Y-m-d');
            $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
            $weekEnd = Carbon::now()->endOfWeek(Carbon::FRIDAY)->format('Y-m-d');
            $monthStart = Carbon::now()->startOfMonth()->format('Y-m-d');
            $monthEnd = Carbon::now()->endOfMonth()->format('Y-m-d');
            
            // Today's bookings
            $todayCount = Booking::whereDate('booking_date', $today)->count();
            
            // Week average
            $weekBookings = Booking::whereBetween('booking_date', [$weekStart, $weekEnd])->count();
            $weekDays = Carbon::now()->startOfWeek(Carbon::MONDAY)->diffInDaysFiltered(function($date) {
                return $date->isWeekday() && $date->lte(Carbon::now());
            }, Carbon::now()->endOfWeek(Carbon::FRIDAY)) + 1;
            
            $weekAvg = $weekDays > 0 ? round($weekBookings / $weekDays, 1) : 0;
            
            // Forces breakdown for current month
            $forcesData = Booking::whereBetween('booking_date', [$monthStart, $monthEnd])
                ->join('users', 'bookings.user_id', '=', 'users.id')
                ->select(
                    'users.armed_force',
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('users.armed_force')
                ->get();
            
            $forcesCounts = [
                'EB' => 0,
                'MB' => 0,
                'FAB' => 0
            ];
            
            foreach ($forcesData as $force) {
                if (isset($forcesCounts[$force->armed_force])) {
                    $forcesCounts[$force->armed_force] = $force->total;
                }
            }
            
            // Find the force with most bookings
            $topForce = array_keys($forcesCounts, max($forcesCounts))[0] ?? 'EB';
            $forceNames = [
                'EB' => 'Exército',
                'MB' => 'Marinha', 
                'FAB' => 'Aeronáutica'
            ];
            
            // Meal comparison totals for current month
            $breakfastTotal = Booking::whereBetween('booking_date', [$monthStart, $monthEnd])
                ->where('meal_type', 'breakfast')
                ->count();
            
            $lunchTotal = Booking::whereBetween('booking_date', [$monthStart, $monthEnd])
                ->where('meal_type', 'lunch')
                ->count();
            
            // Origin breakdown totals for current month
            $propriaOmTotal = Booking::whereBetween('booking_date', [$monthStart, $monthEnd])
                ->join('users', 'bookings.user_id', '=', 'users.id')
                ->join('organizations', 'users.organization_id', '=', 'organizations.id')
                ->where('organizations.name', '11º D Sup')
                ->count();
            
            $outrasOmTotal = Booking::whereBetween('booking_date', [$monthStart, $monthEnd])
                ->join('users', 'bookings.user_id', '=', 'users.id')
                ->join('organizations', 'users.organization_id', '=', 'organizations.id')
                ->where('organizations.name', '!=', '11º D Sup')
                ->where('users.armed_force', 'EB')
                ->count();
            
            return [
                'today' => $todayCount,
                'week_avg' => $weekAvg,
                'total_forces' => count(array_filter($forcesCounts)),
                'top_force' => $forceNames[$topForce] ?? 'N/A',
                'forces_breakdown' => $forcesCounts,
                'breakfast_total' => $breakfastTotal,
                'lunch_total' => $lunchTotal,
                'propria_om_total' => $propriaOmTotal,
                'outras_om_total' => $outrasOmTotal
            ];
        } catch (\Exception $e) {
            return [
                'today' => 0, 
                'week_avg' => 0,
                'total_forces' => 0,
                'top_force' => 'N/A',
                'forces_breakdown' => ['EB' => 0, 'MB' => 0, 'FAB' => 0],
                'breakfast_total' => 0,
                'lunch_total' => 0
            ];
        }
    }
}
