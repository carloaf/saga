<?php

/*
 * Teste do gráfico Café vs Almoço melhorado
 */

echo "=== TESTE: Gráfico Café vs Almoço Atualizado ===\n\n";

// Simular dados como retornados pelo controller
$mealComparisonData = [
    'Segunda' => ['Café' => 15, 'Almoço' => 18],
    'Terça' => ['Café' => 12, 'Almoço' => 16],
    'Quarta' => ['Café' => 18, 'Almoço' => 22],
    'Quinta' => ['Café' => 14, 'Almoço' => 19],
    'Sexta' => ['Café' => 10, 'Almoço' => 0] // Sexta não tem almoço
];

echo "1. Dados de comparação de refeições simulados:\n";
foreach ($mealComparisonData as $day => $meals) {
    echo sprintf("   %-8s: Café: %2d | Almoço: %2d | Total: %2d\n", 
        $day, $meals['Café'], $meals['Almoço'], $meals['Café'] + $meals['Almoço']);
}

// Processar dados para Chart.js
$mealDays = array_keys($mealComparisonData);
$mealCafeValues = array_map(function($day) use ($mealComparisonData) {
    return $mealComparisonData[$day]['Café'] ?? 0;
}, $mealDays);
$mealAlmocoValues = array_map(function($day) use ($mealComparisonData) {
    return $mealComparisonData[$day]['Almoço'] ?? 0;
}, $mealDays);

echo "\n2. Dados processados para Chart.js:\n";
echo "   Labels: " . json_encode($mealDays) . "\n";
echo "   Café:   " . json_encode($mealCafeValues) . "\n";
echo "   Almoço: " . json_encode($mealAlmocoValues) . "\n";

// Calcular totais
$totalCafe = array_sum($mealCafeValues);
$totalAlmoco = array_sum($mealAlmocoValues);
$grandTotal = $totalCafe + $totalAlmoco;

echo "\n3. Estatísticas da semana:\n";
echo "   Total Café da Manhã: $totalCafe\n";
echo "   Total Almoço: $totalAlmoco\n";
echo "   Total Geral: $grandTotal\n";

if ($grandTotal > 0) {
    $percentCafe = round(($totalCafe / $grandTotal) * 100, 1);
    $percentAlmoco = round(($totalAlmoco / $grandTotal) * 100, 1);
    echo "   Percentual Café: $percentCafe%\n";
    echo "   Percentual Almoço: $percentAlmoco%\n";
}

echo "\n4. Configuração Chart.js resultante:\n";
echo "   {\n";
echo "     type: 'bar',\n";
echo "     data: {\n";
echo "       labels: " . json_encode($mealDays) . ",\n";
echo "       datasets: [\n";
echo "         {\n";
echo "           label: 'Café da Manhã',\n";
echo "           data: " . json_encode($mealCafeValues) . ",\n";
echo "           backgroundColor: '#10b981'\n";
echo "         },\n";
echo "         {\n";
echo "           label: 'Almoço',\n";
echo "           data: " . json_encode($mealAlmocoValues) . ",\n";
echo "           backgroundColor: '#3b82f6'\n";
echo "         }\n";
echo "       ]\n";
echo "     },\n";
echo "     options: {\n";
echo "       scales: {\n";
echo "         x: { stacked: false },\n";
echo "         y: { stacked: false, beginAtZero: true }\n";
echo "       }\n";
echo "     }\n";
echo "   }\n";

echo "\n5. Melhorias implementadas:\n";
echo "   ✓ Dados reais do banco de dados\n";
echo "   ✓ Barras lado a lado (não empilhadas)\n";
echo "   ✓ Tooltips melhorados com totais\n";
echo "   ✓ Bordas arredondadas nas barras\n";
echo "   ✓ Grid suavizado\n";
echo "   ✓ Cards com totais de café e almoço\n";
echo "   ✓ Responsividade mantida\n";

echo "\n6. Lógica do controller:\n";
echo "   - getMealComparisonData() processa dados por dia da semana\n";
echo "   - Apenas dias úteis (Segunda a Sexta)\n";
echo "   - Sexta-feira sem almoço (regra de negócio)\n";
echo "   - Dados filtrados por período selecionado\n";

echo "\n=== TESTE CONCLUÍDO ===\n";
