<?php

require 'vendor/autoload.php';

use Carbon\Carbon;

function hasBookingDeadlinePassed($bookingDate)
{
    $now = Carbon::now();
    $bookingDateCarbon = Carbon::parse($bookingDate);
    
    echo "Tentando reservar para: " . $bookingDateCarbon->format('d/m/Y') . "\n";
    echo "Agora é: " . $now->format('d/m/Y H:i') . "\n";
    
    // If trying to book for today, always allow (handled by other logic)
    if ($bookingDateCarbon->isToday()) {
        echo "✅ É para hoje - permitido (outras regras se aplicam)\n";
        return false;
    }
    
    // If trying to book for tomorrow and it's past 13:00 today, block it
    if ($bookingDateCarbon->isTomorrow() && $now->hour >= 13) {
        echo "❌ É para amanhã e já passou das 13h hoje - BLOQUEADO\n";
        return true;
    }
    
    // For dates further in the future, check if it's past 13:00 of the day before
    $deadlineDateTime = $bookingDateCarbon->copy()->subDay()->setTime(13, 0, 0);
    
    echo "Deadline para esta data: " . $deadlineDateTime->format('d/m/Y H:i') . "\n";
    
    $result = $now->gte($deadlineDateTime);
    echo ($result ? "❌ BLOQUEADO" : "✅ PERMITIDO") . "\n";
    
    return $result;
}

function getDeadlineMessage($bookingDate)
{
    $bookingDateCarbon = Carbon::parse($bookingDate);
    
    if ($bookingDateCarbon->isTomorrow()) {
        return 'Não é possível fazer reservas para amanhã após às 13h de hoje.';
    }
    
    $deadlineDateTime = $bookingDateCarbon->copy()->subDay()->setTime(13, 0, 0);
    return 'Prazo expirado para ' . $bookingDateCarbon->format('d/m/Y') . ' (limite: ' . $deadlineDateTime->format('d/m/Y \à\s H:i') . ')';
}

echo "=== TESTE DA NOVA LÓGICA DE DEADLINE ===\n\n";

// Cenário 1: Hoje é segunda 12h, tentando reservar para terça
Carbon::setTestNow(Carbon::create(2025, 8, 4, 12, 0, 0)); // Segunda 12h
echo "CENÁRIO 1: Segunda 12h, reservando para terça\n";
hasBookingDeadlinePassed('2025-08-05');
echo "Mensagem: " . getDeadlineMessage('2025-08-05') . "\n\n";

// Cenário 2: Hoje é segunda 14h, tentando reservar para terça  
Carbon::setTestNow(Carbon::create(2025, 8, 4, 14, 0, 0)); // Segunda 14h
echo "CENÁRIO 2: Segunda 14h, reservando para terça\n";
hasBookingDeadlinePassed('2025-08-05');
echo "Mensagem: " . getDeadlineMessage('2025-08-05') . "\n\n";

// Cenário 3: Hoje é segunda 14h, tentando reservar para quarta
Carbon::setTestNow(Carbon::create(2025, 8, 4, 14, 0, 0)); // Segunda 14h
echo "CENÁRIO 3: Segunda 14h, reservando para quarta\n";
hasBookingDeadlinePassed('2025-08-06');
echo "Mensagem: " . getDeadlineMessage('2025-08-06') . "\n\n";

// Cenário 4: Hoje é terça 14h, tentando reservar para quarta
Carbon::setTestNow(Carbon::create(2025, 8, 5, 14, 0, 0)); // Terça 14h  
echo "CENÁRIO 4: Terça 14h, reservando para quarta\n";
hasBookingDeadlinePassed('2025-08-06');
echo "Mensagem: " . getDeadlineMessage('2025-08-06') . "\n\n";

Carbon::setTestNow(); // Reset
