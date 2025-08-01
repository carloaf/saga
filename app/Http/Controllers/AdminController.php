<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Organization;
use App\Models\Rank;
use App\Exports\DailyMealsExport;
use App\Exports\SummaryExport;
use App\Exports\WeeklySummaryExport;
use App\Exports\OrganizationBreakdownExport;
use App\Exports\UserActivityExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Display the admin users page with statistics cards
     */
    public function users(Request $request)
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

        // Query base para usuários
        $query = User::with(['rank', 'organization']);

        // Aplicar filtros se fornecidos
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('war_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        // Buscar usuários com paginação
        $users = $query->orderBy('created_at', 'desc')
                       ->paginate(10)
                       ->appends($request->query());

        // Buscar dados necessários para os modais
        $ranks = Rank::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();

        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'activeUsers', 
            'inactiveUsers',
            'recentUsers',
            'ranks',
            'organizations'
        ));
    }

    /**
     * Update a user
     */
    public function updateUser(Request $request, User $user)
    {
        // Verificação de acesso
        if (!Auth::user() || Auth::user()->role !== 'superuser') {
            return response()->json(['success' => false, 'message' => 'Acesso negado. Apenas superusuários podem acessar esta área.'], 403);
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
                'is_active' => 'required|boolean',
                'role' => 'required|in:user,superuser'
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
                'role' => 'required|in:user,superuser',
                'is_active' => 'required|boolean'
            ]);

            // Adicionar campos padrão para usuário criado manualmente
            $validatedData['google_id'] = 'manual_' . time() . '_' . rand(1000, 9999);
            $validatedData['email_verified_at'] = now();

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

    /**
     * Display the reports page
     */
    public function reports()
    {
        // Verificação de acesso
        if (!Auth::user() || Auth::user()->role !== 'superuser') {
            abort(403, 'Acesso negado. Apenas superusuários podem acessar esta área.');
        }

        // Estatísticas do dia atual
        $today = Carbon::today();
        $todayBookings = Booking::whereDate('booking_date', $today)->count();
        $todayBreakfast = Booking::whereDate('booking_date', $today)
            ->where('meal_type', 'breakfast')->count();
        $todayLunch = Booking::whereDate('booking_date', $today)
            ->where('meal_type', 'lunch')->count();

        // Estatísticas da semana
        $weekStart = Carbon::today()->startOfWeek();
        $weekEnd = Carbon::today()->endOfWeek();
        $weekBookings = Booking::whereBetween('booking_date', [$weekStart, $weekEnd])->count();
        $avgDaily = $weekBookings > 0 ? round($weekBookings / 7, 1) : 0;

        // Estatísticas do mês
        $monthStart = Carbon::today()->startOfMonth();
        $monthEnd = Carbon::today()->endOfMonth();
        $monthlyBookings = Booking::whereBetween('booking_date', [$monthStart, $monthEnd])->count();

        // Top organizações
        $topOrgs = Booking::join('users', 'bookings.user_id', '=', 'users.id')
            ->join('organizations', 'users.organization_id', '=', 'organizations.id')
            ->select('organizations.name', DB::raw('COUNT(*) as total'))
            ->groupBy('organizations.id', 'organizations.name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        return view('admin.reports.index', compact(
            'todayBookings',
            'todayBreakfast', 
            'todayLunch',
            'weekBookings',
            'avgDaily',
            'monthlyBookings',
            'topOrgs'
        ));
    }

    /**
     * Generate and download reports
     */
    public function generateReport(Request $request)
    {
        // Verificação de acesso
        if (!Auth::user() || Auth::user()->role !== 'superuser') {
            abort(403, 'Acesso negado. Apenas superusuários podem acessar esta área.');
        }

        $request->validate([
            'report_type' => 'required|in:daily_meals,weekly_summary,monthly_summary,organization_breakdown,user_activity',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,excel'
        ]);

        $reportType = $request->report_type;
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $format = $request->format;

        $data = $this->getReportData($reportType, $startDate, $endDate);
        
        if ($format === 'pdf') {
            return $this->generatePdfReport($reportType, $data, $startDate, $endDate);
        } else {
            return $this->generateExcelReport($reportType, $data, $startDate, $endDate);
        }
    }

    /**
     * Get report data based on type
     */
    private function getReportData($reportType, $startDate, $endDate)
    {
        switch ($reportType) {
            case 'daily_meals':
                return $this->getDailyMealsData($startDate, $endDate);
            
            case 'weekly_summary':
            case 'monthly_summary':
                return $this->getSummaryData($startDate, $endDate);
            
            case 'organization_breakdown':
                return $this->getOrganizationBreakdownData($startDate, $endDate);
            
            case 'user_activity':
                return $this->getUserActivityData($startDate, $endDate);
            
            default:
                throw new \InvalidArgumentException('Tipo de relatório inválido');
        }
    }

    /**
     * Get daily meals data
     */
    private function getDailyMealsData($startDate, $endDate)
    {
        $bookings = Booking::with(['user.rank', 'user.organization'])
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->orderBy('booking_date')
            ->orderBy('meal_type')
            ->orderBy('users.full_name')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->select('bookings.*')
            ->get()
            ->groupBy('booking_date');

        return $bookings;
    }

    /**
     * Get summary data
     */
    private function getSummaryData($startDate, $endDate)
    {
        $totalBookings = Booking::whereBetween('booking_date', [$startDate, $endDate])->count();
        $breakfastCount = Booking::whereBetween('booking_date', [$startDate, $endDate])
            ->where('meal_type', 'breakfast')->count();
        $lunchCount = Booking::whereBetween('booking_date', [$startDate, $endDate])
            ->where('meal_type', 'lunch')->count();

        $dailyStats = Booking::whereBetween('booking_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(booking_date) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN meal_type = \'breakfast\' THEN 1 ELSE 0 END) as breakfast'),
                DB::raw('SUM(CASE WHEN meal_type = \'lunch\' THEN 1 ELSE 0 END) as lunch')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'total_bookings' => $totalBookings,
            'breakfast_count' => $breakfastCount,
            'lunch_count' => $lunchCount,
            'daily_stats' => $dailyStats,
            'period_days' => $startDate->diffInDays($endDate) + 1
        ];
    }

    /**
     * Get organization breakdown data
     */
    private function getOrganizationBreakdownData($startDate, $endDate)
    {
        return Booking::join('users', 'bookings.user_id', '=', 'users.id')
            ->join('organizations', 'users.organization_id', '=', 'organizations.id')
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->select(
                'organizations.name as organization_name',
                DB::raw('COUNT(*) as total_bookings'),
                DB::raw('SUM(CASE WHEN meal_type = \'breakfast\' THEN 1 ELSE 0 END) as breakfast_count'),
                DB::raw('SUM(CASE WHEN meal_type = \'lunch\' THEN 1 ELSE 0 END) as lunch_count'),
                DB::raw('COUNT(DISTINCT users.id) as unique_users')
            )
            ->groupBy('organizations.id', 'organizations.name')
            ->orderBy('total_bookings', 'desc')
            ->get();
    }

    /**
     * Get user activity data
     */
    private function getUserActivityData($startDate, $endDate)
    {
        return Booking::join('users', 'bookings.user_id', '=', 'users.id')
            ->leftJoin('organizations', 'users.organization_id', '=', 'organizations.id')
            ->leftJoin('ranks', 'users.rank_id', '=', 'ranks.id')
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->select(
                'users.full_name',
                'users.war_name',
                'ranks.name as rank_name',
                'organizations.name as organization_name',
                DB::raw('COUNT(*) as total_bookings'),
                DB::raw('SUM(CASE WHEN meal_type = \'breakfast\' THEN 1 ELSE 0 END) as breakfast_count'),
                DB::raw('SUM(CASE WHEN meal_type = \'lunch\' THEN 1 ELSE 0 END) as lunch_count')
            )
            ->groupBy('users.id', 'users.full_name', 'users.war_name', 'ranks.name', 'organizations.name')
            ->orderBy('total_bookings', 'desc')
            ->get();
    }

    /**
     * Generate PDF report
     */
    private function generatePdfReport($reportType, $data, $startDate, $endDate)
    {
        $viewName = 'admin.reports.pdf.' . str_replace('_', '-', $reportType);
        
        $pdf = Pdf::loadView($viewName, [
            'data' => $data,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => now()
        ]);

        $filename = sprintf('%s_%s_%s.pdf', 
            $reportType, 
            $startDate->format('Y-m-d'), 
            $endDate->format('Y-m-d')
        );

        return $pdf->download($filename);
    }

    /**
     * Generate Excel report
     */
    private function generateExcelReport($reportType, $data, $startDate, $endDate)
    {
        $filename = '';
        $export = null;

        switch ($reportType) {
            case 'daily_meals':
                $export = new DailyMealsExport($data, $startDate, $endDate);
                $filename = 'refeicoes_diarias_' . Carbon::parse($startDate)->format('Y-m-d') . '.xlsx';
                break;

            case 'weekly_summary':
                $export = new WeeklySummaryExport($data, $startDate, $endDate);
                $filename = 'resumo_semanal_' . Carbon::parse($startDate)->format('Y-m-d') . '_' . Carbon::parse($endDate)->format('Y-m-d') . '.xlsx';
                break;

            case 'monthly_summary':
                $export = new SummaryExport($data, $startDate, $endDate);
                $filename = 'resumo_mensal_' . Carbon::parse($startDate)->format('Y-m') . '.xlsx';
                break;

            case 'organization_breakdown':
                $export = new OrganizationBreakdownExport($data, $startDate, $endDate);
                $filename = 'relatorio_organizacoes_' . Carbon::parse($startDate)->format('Y-m-d') . '_' . Carbon::parse($endDate)->format('Y-m-d') . '.xlsx';
                break;

            case 'user_activity':
                $export = new UserActivityExport($data, $startDate, $endDate);
                $filename = 'atividade_usuarios_' . Carbon::parse($startDate)->format('Y-m-d') . '_' . Carbon::parse($endDate)->format('Y-m-d') . '.xlsx';
                break;

            default:
                return response()->json([
                    'error' => 'Tipo de relatório não reconhecido.'
                ], 400);
        }

        return Excel::download($export, $filename);
    }
}
