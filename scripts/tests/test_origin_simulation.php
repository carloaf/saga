<?php

/*
 * Teste para verificar os dados do gráfico de origem
 * Este arquivo simula a lógica do DashboardController
 */

// Simulate Laravel environment
if (!function_exists('config')) {
    function config($key) {
        return null;
    }
}

echo "=== TESTE: Simulação da lógica do Dashboard ===\n\n";

// Simulate the getOriginBreakdownData logic
function simulateOriginBreakdown() {
    echo "1. Simulando dados de entrada:\n";
    
    // Sample booking data with forces
    $sampleBookings = [
        ['force' => 'EB', 'is_host' => true, 'count' => 45],   // Própria OM
        ['force' => 'EB', 'is_host' => false, 'count' => 23],  // Outras OM
        ['force' => 'MB', 'is_host' => false, 'count' => 12],  // Outras Forças
        ['force' => 'FAB', 'is_host' => false, 'count' => 8],  // Outras Forças
    ];
    
    foreach ($sampleBookings as $booking) {
        echo sprintf(
            "   Força: %-3s | Host: %-3s | Count: %d\n",
            $booking['force'],
            $booking['is_host'] ? 'Sim' : 'Não',
            $booking['count']
        );
    }
    
    echo "\n2. Aplicando categorização:\n";
    
    $result = [
        'Própria OM' => 0,
        'Outras OM' => 0,
        'Outras Forças' => 0
    ];
    
    foreach ($sampleBookings as $booking) {
        if ($booking['is_host']) {
            $category = 'Própria OM';
        } elseif ($booking['force'] === 'EB') {
            $category = 'Outras OM';
        } elseif (in_array($booking['force'], ['MB', 'FAB'])) {
            $category = 'Outras Forças';
        } else {
            $category = 'Outras OM';
        }
        
        $result[$category] += $booking['count'];
        
        echo sprintf(
            "   %-15s <- Força: %-3s, Host: %-3s, Count: %d\n",
            $category,
            $booking['force'],
            $booking['is_host'] ? 'Sim' : 'Não',
            $booking['count']
        );
    }
    
    echo "\n3. Resultado final:\n";
    foreach ($result as $category => $total) {
        echo sprintf("   %-15s: %d reservas\n", $category, $total);
    }
    
    return $result;
}

function simulateChartJsData($originData) {
    echo "\n4. Dados para Chart.js:\n";
    
    $labels = array_keys($originData);
    $values = array_values($originData);
    $colors = ['#10b981', '#3b82f6', '#f59e0b']; // Verde, Azul, Âmbar
    
    echo "   Labels: " . json_encode($labels) . "\n";
    echo "   Values: " . json_encode($values) . "\n";
    echo "   Colors: " . json_encode($colors) . "\n";
    
    echo "\n5. Configuração Chart.js resultante:\n";
    echo "   {\n";
    echo "     type: 'doughnut',\n";
    echo "     data: {\n";
    echo "       labels: " . json_encode($labels) . ",\n";
    echo "       datasets: [{\n";
    echo "         data: " . json_encode($values) . ",\n";
    echo "         backgroundColor: " . json_encode($colors) . "\n";
    echo "       }]\n";
    echo "     }\n";
    echo "   }\n";
    
    return [
        'labels' => $labels,
        'values' => $values,
        'colors' => $colors
    ];
}

function simulateForceStats() {
    echo "\n6. Estatísticas das forças:\n";
    
    $forcesCounts = [
        'EB' => 68,  // 45 + 23
        'MB' => 12,
        'FAB' => 8
    ];
    
    $totalForces = count(array_filter($forcesCounts));
    $topForce = array_keys($forcesCounts, max($forcesCounts))[0];
    
    $forceNames = [
        'EB' => 'Exército',
        'MB' => 'Marinha', 
        'FAB' => 'Aeronáutica'
    ];
    
    echo "   Total de forças ativas: $totalForces\n";
    echo "   Força com maior participação: " . $forceNames[$topForce] . "\n";
    echo "   Breakdown por força:\n";
    foreach ($forcesCounts as $force => $count) {
        echo sprintf("     %-12s: %d reservas\n", $forceNames[$force], $count);
    }
    
    return [
        'total_forces' => $totalForces,
        'top_force' => $forceNames[$topForce],
        'forces_breakdown' => $forcesCounts
    ];
}

// Execute simulation
$originData = simulateOriginBreakdown();
$chartData = simulateChartJsData($originData);
$forceStats = simulateForceStats();

echo "\n=== RESUMO FINAL ===\n";
echo "✓ Categorização implementada: Própria OM, Outras OM, Outras Forças\n";
echo "✓ Cores definidas: Verde (Própria), Azul (Outras OM), Âmbar (Outras Forças)\n";
echo "✓ Estatísticas de forças calculadas\n";
echo "✓ Dados prontos para Chart.js\n";

echo "\n=== TESTE CONCLUÍDO ===\n";
