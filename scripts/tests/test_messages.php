<?php

use Carbon\Carbon;

require_once 'vendor/autoload.php';

echo "=== TESTE DAS MENSAGENS DE RESERVA DA SEMANA ===\n";

$now = Carbon::now();
echo "Agora: " . $now->format('d/m/Y H:i') . " (Hora: {$now->hour})\n\n";

// Simular a lógica implementada no controller
function hasBookingDeadlinePassed($date) {
    $bookingDate = Carbon::parse($date);
    $now = Carbon::now();
    
    // Se é hoje ou passou, não pode reservar
    if ($bookingDate->isPast() || $bookingDate->isToday()) {
        return true;
    }
    
    // Se é amanhã e já passou das 13h hoje, bloquear
    if ($bookingDate->isTomorrow() && $now->hour >= 13) {
        return true;
    }
    
    return false;
}

function getErrorMessage($date, $mealType) {
    $date = Carbon::parse($date);
    
    if (hasBookingDeadlinePassed($date)) {
        if ($date->isTomorrow()) {
            return "⏰ {$mealType} para " . $date->format('d/m/Y') . " não pode ser reservado - prazo encerrado às 13h de hoje";
        } else {
            return "⏰ {$mealType} para " . $date->format('d/m/Y') . " - prazo para reserva já expirou";
        }
    }
    
    return null;
}

// Teste para amanhã (que deve estar bloqueado)
$tomorrow = $now->copy()->addDay();
echo "=== TESTE PARA AMANHÃ ({$tomorrow->format('d/m/Y')}) ===\n";
echo "Café da manhã: " . (getErrorMessage($tomorrow, 'Café da manhã') ?: 'Permitido') . "\n";
echo "Almoço: " . (getErrorMessage($tomorrow, 'Almoço') ?: 'Permitido') . "\n\n";

// Teste para depois de amanhã (que deve estar permitido)
$dayAfterTomorrow = $now->copy()->addDays(2);
echo "=== TESTE PARA DEPOIS DE AMANHÃ ({$dayAfterTomorrow->format('d/m/Y')}) ===\n";
echo "Café da manhã: " . (getErrorMessage($dayAfterTomorrow, 'Café da manhã') ?: 'Permitido') . "\n";
echo "Almoço: " . (getErrorMessage($dayAfterTomorrow, 'Almoço') ?: 'Permitido') . "\n\n";

// Teste de mensagem final
$errors = [];
if (hasBookingDeadlinePassed($tomorrow)) {
    $errors[] = getErrorMessage($tomorrow, 'Café da manhã');
}

if (count($errors) > 0) {
    echo "=== MENSAGEM FINAL ===\n";
    $errorMessage = "Nenhuma reserva de café foi possível:\n" . implode('; ', $errors) . "\n\nDica: Use o calendário para reservas individuais.";
    echo $errorMessage . "\n";
}
