<?php

use Carbon\Carbon;

require_once 'vendor/autoload.php';

echo "=== TESTE DAS MENSAGENS MELHORADAS ===\n";

$now = Carbon::now();
echo "Agora: " . $now->format('d/m/Y H:i') . " (Hora: {$now->hour})\n\n";

// Simular as novas mensagens
function getImprovedErrorMessage($date, $mealType) {
    $date = Carbon::parse($date);
    
    if ($date->isTomorrow() && Carbon::now()->hour >= 13) {
        return "⏰ {$mealType} - " . $date->format('d/m/Y') . "\nPrazo encerrado às 13h de hoje";
    }
    
    return null;
}

// Teste para amanhã
$tomorrow = $now->copy()->addDay();
echo "=== MENSAGENS INDIVIDUAIS ===\n";
echo "Café da manhã:\n" . getImprovedErrorMessage($tomorrow, 'Café da manhã') . "\n\n";
echo "Almoço:\n" . getImprovedErrorMessage($tomorrow, 'Almoço') . "\n\n";

// Teste da mensagem final organizada
$errors = [
    getImprovedErrorMessage($tomorrow, 'Café da manhã'),
    getImprovedErrorMessage($tomorrow, 'Almoço')
];

echo "=== MENSAGEM FINAL ORGANIZADA ===\n";
$errorMessage = "❌ Nenhuma reserva de café foi possível:\n\n" . implode("\n\n", $errors) . "\n\n💡 Dica: Use o calendário para reservas individuais";
echo $errorMessage . "\n\n";

echo "=== MENSAGEM DE SUCESSO EXEMPLO ===\n";
$successMessage = "✅ 3 reserva(s) de café realizadas!\n\n⚠️ Avisos:\n" . implode("\n", [$errors[0]]);
echo $successMessage . "\n";
