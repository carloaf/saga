<?php

use Carbon\Carbon;

require_once 'vendor/autoload.php';

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

function getDeadlineMessage($date) {
    $bookingDate = Carbon::parse($date);
    
    if ($bookingDate->isTomorrow()) {
        return "Reservas para amanhã se encerram hoje às 13h";
    }
    
    $deadline = $bookingDate->copy()->subDay()->setTime(13, 0, 0);
    return "Reservas se encerram em " . $deadline->format('d/m/Y') . " às 13h";
}

// Testes
$now = Carbon::now();
echo "=== TESTE DA LÓGICA DE DEADLINE ===\n";
echo "Agora: " . $now->format('d/m/Y H:i') . " (Hora: {$now->hour})\n\n";

// Teste para amanhã
$tomorrow = $now->copy()->addDay();
echo "Testando para AMANHÃ ({$tomorrow->format('d/m/Y')}):\n";
echo "É amanhã? " . ($tomorrow->isTomorrow() ? 'SIM' : 'NÃO') . "\n";
echo "Passou das 13h hoje? " . ($now->hour >= 13 ? 'SIM' : 'NÃO') . "\n";
echo "Deadline passou? " . (hasBookingDeadlinePassed($tomorrow) ? 'SIM - BLOQUEAR' : 'NÃO - PERMITIR') . "\n";
echo "Mensagem: " . getDeadlineMessage($tomorrow) . "\n\n";

// Teste para depois de amanhã
$dayAfterTomorrow = $now->copy()->addDays(2);
echo "Testando para DEPOIS DE AMANHÃ ({$dayAfterTomorrow->format('d/m/Y')}):\n";
echo "Deadline passou? " . (hasBookingDeadlinePassed($dayAfterTomorrow) ? 'SIM - BLOQUEAR' : 'NÃO - PERMITIR') . "\n";
echo "Mensagem: " . getDeadlineMessage($dayAfterTomorrow) . "\n\n";

// Teste para hoje
$today = $now->copy();
echo "Testando para HOJE ({$today->format('d/m/Y')}):\n";
echo "Deadline passou? " . (hasBookingDeadlinePassed($today) ? 'SIM - BLOQUEAR' : 'NÃO - PERMITIR') . "\n\n";
