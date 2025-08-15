<?php

use Carbon\Carbon;

require_once 'vendor/autoload.php';

echo "=== TESTE DA LÓGICA VISUAL DO CALENDÁRIO ===\n";

$now = Carbon::now();
$today = $now->copy();
$tomorrow = $now->copy()->addDay();
$dayAfterTomorrow = $now->copy()->addDays(2);

echo "Agora: " . $now->format('d/m/Y H:i') . " (Hora: {$now->hour})\n\n";

function testDayLogic($date, $now) {
    echo "Testando: " . $date->format('d/m/Y') . "\n";
    
    $isPast = $date->isPast();
    $isToday = $date->isToday();
    $isTomorrow = $date->isTomorrow();
    $isWeekend = $date->isWeekend();
    
    echo "  - É passado? " . ($isPast ? 'SIM' : 'NÃO') . "\n";
    echo "  - É hoje? " . ($isToday ? 'SIM' : 'NÃO') . "\n";
    echo "  - É amanhã? " . ($isTomorrow ? 'SIM' : 'NÃO') . "\n";
    echo "  - É fim de semana? " . ($isWeekend ? 'SIM' : 'NÃO') . "\n";
    
    // Lógica de deadline (mesma do view)
    $deadlinePassed = false;
    
    // Se é hoje ou passou, não pode reservar
    if ($date->isPast() || $date->isToday()) {
        $deadlinePassed = true;
        echo "  - Deadline passou? SIM (é hoje ou passado)\n";
    }
    // Se é amanhã e já passou das 13h hoje, bloquear
    else if ($date->isTomorrow() && $now->hour >= 13) {
        $deadlinePassed = true;
        echo "  - Deadline passou? SIM (é amanhã e passou das 13h hoje)\n";
    } else {
        echo "  - Deadline passou? NÃO\n";
    }
    
    $isBookable = !$isPast && !$isWeekend && !$deadlinePassed;
    echo "  - É reservável? " . ($isBookable ? 'SIM' : 'NÃO') . "\n";
    echo "  - Deve ter cursor-pointer? " . ($isBookable ? 'SIM' : 'NÃO') . "\n\n";
}

testDayLogic($today, $now);
testDayLogic($tomorrow, $now);
testDayLogic($dayAfterTomorrow, $now);
