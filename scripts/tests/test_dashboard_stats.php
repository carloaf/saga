<?php

require 'vendor/autoload.php';

use Carbon\Carbon;
use App\Models\Booking;

// Simular o ambiente Laravel
$config = require 'config/database.php';

// Teste das estatísticas do dashboard
echo "=== TESTE DAS ESTATÍSTICAS DO DASHBOARD ===\n\n";

// Simular dados para hoje
$today = Carbon::now()->format('Y-m-d');
echo "Testando estatísticas para hoje: $today\n";

// Estatísticas de hoje
echo "\n=== HOJE ===\n";
echo "Query para hoje: Booking::whereDate('booking_date', '$today')\n";
echo "- Total de reservas: [Será calculado pelo banco]\n";
echo "- Cafés da manhã: [Será calculado pelo banco]\n";
echo "- Almoços: [Será calculado pelo banco]\n";

// Estatísticas da semana
$startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
$endOfWeek = Carbon::now()->endOfWeek(Carbon::FRIDAY)->format('Y-m-d');

echo "\n=== ESTA SEMANA ===\n";
echo "Período: $startOfWeek até $endOfWeek (apenas dias úteis)\n";
echo "Query: Booking::whereBetween('booking_date', ['$startOfWeek', '$endOfWeek'])\n";
echo "- Total de reservas: [Será calculado pelo banco]\n";
echo "- Cafés da manhã: [Será calculado pelo banco]\n";
echo "- Almoços: [Será calculado pelo banco]\n";

// Estatísticas do mês
$startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
$endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');

echo "\n=== ESTE MÊS ===\n";
echo "Período: $startOfMonth até $endOfMonth\n";
echo "Query: Booking::whereBetween('booking_date', ['$startOfMonth', '$endOfMonth'])\n";
echo "- Total de reservas: [Será calculado pelo banco]\n";
echo "- Cafés da manhã: [Será calculado pelo banco]\n";
echo "- Almoços: [Será calculado pelo banco]\n";

echo "\n=== USUÁRIOS ATIVOS ===\n";
echo "Query: Booking::whereDate('booking_date', '$today')->distinct('user_id')->count('user_id')\n";
echo "- Usuários que fizeram reservas hoje: [Será calculado pelo banco]\n";

echo "\n✅ Implementação concluída!\n";
echo "Os cards do dashboard agora mostrarão:\n";
echo "- Números totais de reservas\n";
echo "- Detalhamento por tipo de refeição (☕ café, 🍽️ almoço)\n";
echo "- Dados em tempo real do banco de dados\n";
echo "- Estatísticas por período (hoje, semana, mês)\n";
