<?php

require_once 'vendor/autoload.php';

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Test the origin breakdown data
echo "=== TESTE: Origem dos Arranchados ===\n\n";

// Create date range for current month
$now = Carbon::now();
$dateRange = [
    'start' => $now->copy()->startOfMonth(),
    'end' => $now->copy()->endOfMonth()
];

echo "Período: " . $dateRange['start']->format('d/m/Y') . " até " . $dateRange['end']->format('d/m/Y') . "\n\n";

try {
    // Query with force categorization
    $data = Booking::whereBetween('booking_date', [
            $dateRange['start']->format('Y-m-d'), 
            $dateRange['end']->format('Y-m-d')
        ])
        ->join('users', 'bookings.user_id', '=', 'users.id')
        ->join('organizations', 'users.organization_id', '=', 'organizations.id')
        ->select(
            DB::raw('CASE 
                WHEN organizations.is_host = true THEN \'Própria OM\'
                WHEN users.armed_force = \'EB\' THEN \'Outras OM\'
                WHEN users.armed_force IN (\'MB\', \'FAB\') THEN \'Outras Forças\'
                ELSE \'Outras OM\'
            END as origin'),
            DB::raw('COUNT(*) as total'),
            DB::raw('users.armed_force'),
            DB::raw('organizations.name as org_name'),
            DB::raw('organizations.is_host')
        )
        ->groupBy(DB::raw('CASE 
            WHEN organizations.is_host = true THEN \'Própria OM\'
            WHEN users.armed_force = \'EB\' THEN \'Outras OM\'
            WHEN users.armed_force IN (\'MB\', \'FAB\') THEN \'Outras Forças\'
            ELSE \'Outras OM\'
        END'), 'users.armed_force', 'organizations.name', 'organizations.is_host')
        ->get();
        
    echo "=== RESULTADOS DETALHADOS ===\n";
    foreach ($data as $item) {
        echo sprintf(
            "Categoria: %-15s | Força: %-3s | Organização: %-30s | Host: %-3s | Total: %d\n",
            $item->origin,
            $item->armed_force ?: 'N/A',
            $item->org_name,
            $item->is_host ? 'Sim' : 'Não',
            $item->total
        );
    }
    
    // Aggregate by category
    $result = collect([
        'Própria OM' => 0,
        'Outras OM' => 0,
        'Outras Forças' => 0
    ]);
    
    foreach ($data as $item) {
        $result[$item->origin] += $item->total;
    }
    
    echo "\n=== RESUMO POR CATEGORIA ===\n";
    foreach ($result as $category => $total) {
        echo sprintf("%-15s: %d reservas\n", $category, $total);
    }
    
    // Test forces breakdown
    echo "\n=== BREAKDOWN POR FORÇA ===\n";
    $forcesData = Booking::whereBetween('booking_date', [
            $dateRange['start']->format('Y-m-d'), 
            $dateRange['end']->format('Y-m-d')
        ])
        ->join('users', 'bookings.user_id', '=', 'users.id')
        ->select(
            'users.armed_force',
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('users.armed_force')
        ->get();
    
    $forceNames = [
        'EB' => 'Exército Brasileiro',
        'MB' => 'Marinha do Brasil',
        'FAB' => 'Força Aérea Brasileira'
    ];
    
    foreach ($forcesData as $force) {
        $forceName = $forceNames[$force->armed_force] ?? $force->armed_force;
        echo sprintf("%-25s: %d reservas\n", $forceName, $force->total);
    }
    
    echo "\n=== DADOS PARA CHART.JS ===\n";
    echo "Labels: " . json_encode(array_keys($result->toArray())) . "\n";
    echo "Values: " . json_encode(array_values($result->toArray())) . "\n";
    echo "Colors: ['#10b981', '#3b82f6', '#f59e0b']\n";
    
} catch (\Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo "Stack: " . $e->getTraceAsString() . "\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";
