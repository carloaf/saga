<?php

require_once 'vendor/autoload.php';

use Carbon\Carbon;

// Simular a lógica atual do BookingController
function hasBookingDeadlinePassed($bookingDate)
{
    $now = Carbon::now();
    $bookingDateCarbon = Carbon::parse($bookingDate);
    
    echo "Agora: " . $now->format('Y-m-d H:i:s') . " (hora: {$now->hour})\n";
    echo "Data da reserva: " . $bookingDateCarbon->format('Y-m-d H:i:s') . "\n";
    
    // If trying to book for today, always allow (handled by other logic)
    if ($bookingDateCarbon->isToday()) {
        echo "É hoje - sempre permitir\n";
        return false;
    }
    
    // If trying to book for tomorrow and it's past 13:00 today, block it
    if ($bookingDateCarbon->isTomorrow() && $now->hour >= 13) {
        echo "É amanhã e passou das 13h hoje - BLOQUEAR\n";
        return true;
    }
    
    echo "É amanhã? " . ($bookingDateCarbon->isTomorrow() ? 'SIM' : 'NÃO') . "\n";
    echo "Passou das 13h? " . ($now->hour >= 13 ? 'SIM' : 'NÃO') . "\n";
    
    // For dates further in the future, check if it's past 13:00 of the day before
    $deadlineDateTime = $bookingDateCarbon->copy()->subDay()->setTime(13, 0, 0);
    
    echo "Deadline: " . $deadlineDateTime->format('Y-m-d H:i:s') . "\n";
    echo "Agora >= Deadline? " . ($now->gte($deadlineDateTime) ? 'SIM' : 'NÃO') . "\n";
    
    return $now->gte($deadlineDateTime);
}

echo "=== TESTE DO DEADLINE ===\n\n";

// Teste para amanhã (05/08/2025)
$tomorrow = Carbon::tomorrow();
echo "Testando reserva para amanhã ({$tomorrow->format('d/m/Y')}):\n";
$result = hasBookingDeadlinePassed($tomorrow);
echo "Resultado: " . ($result ? 'BLOQUEADO' : 'PERMITIDO') . "\n\n";

// Teste para depois de amanhã
$dayAfterTomorrow = Carbon::tomorrow()->addDay();
echo "Testando reserva para depois de amanhã ({$dayAfterTomorrow->format('d/m/Y')}):\n";
$result2 = hasBookingDeadlinePassed($dayAfterTomorrow);
echo "Resultado: " . ($result2 ? 'BLOQUEADO' : 'PERMITIDO') . "\n\n";
