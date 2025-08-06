<?php

/*
 * Teste para verificar registros de arranchamentos por força
 */

echo "=== TESTE: Verificação de Registros de Arranchamentos ===\n\n";

// Simular conexão com Laravel
echo "1. Verificando usuários por força armada:\n";
echo "   Executando: App\\Models\\User::select('armed_force', DB::raw('COUNT(*) as total'))->groupBy('armed_force')->get()\n\n";

echo "2. Verificando arranchamentos existentes:\n";
echo "   Executando: App\\Models\\Booking::join('users', 'bookings.user_id', '=', 'users.id')\n";
echo "              ->select('users.armed_force', DB::raw('COUNT(*) as total'))\n";
echo "              ->groupBy('users.armed_force')->get()\n\n";

echo "3. Detalhamento por categoria (Origem dos Arranchados):\n";
echo "   Executando query do DashboardController::getOriginBreakdownData()\n\n";

echo "4. Comandos para teste manual via artisan tinker:\n\n";

echo "   # Verificar usuários por força\n";
echo "   docker-compose exec app php artisan tinker --execute=\"\n";
echo "   echo 'Usuários por força:' . PHP_EOL;\n";
echo "   App\\Models\\User::select('armed_force', DB::raw('COUNT(*) as total'))\n";
echo "       ->groupBy('armed_force')\n";
echo "       ->get()\n";
echo "       ->each(function(\$item) {\n";
echo "           echo 'Força: ' . (\$item->armed_force ?: 'N/A') . ' - Total: ' . \$item->total . PHP_EOL;\n";
echo "       });\n";
echo "   \"\n\n";

echo "   # Verificar arranchamentos por força\n";
echo "   docker-compose exec app php artisan tinker --execute=\"\n";
echo "   echo 'Arranchamentos por força:' . PHP_EOL;\n";
echo "   App\\Models\\Booking::join('users', 'bookings.user_id', '=', 'users.id')\n";
echo "       ->select('users.armed_force', DB::raw('COUNT(*) as total'))\n";
echo "       ->groupBy('users.armed_force')\n";
echo "       ->get()\n";
echo "       ->each(function(\$item) {\n";
echo "           echo 'Força: ' . \$item->armed_force . ' - Reservas: ' . \$item->total . PHP_EOL;\n";
echo "       });\n";
echo "   \"\n\n";

echo "   # Verificar dados de origem (como no dashboard)\n";
echo "   docker-compose exec app php artisan tinker --execute=\"\n";
echo "   \$now = Carbon\\Carbon::now();\n";
echo "   \$monthStart = \$now->copy()->startOfMonth()->format('Y-m-d');\n";
echo "   \$monthEnd = \$now->copy()->endOfMonth()->format('Y-m-d');\n";
echo "   \n";
echo "   \$data = App\\Models\\Booking::whereBetween('booking_date', [\$monthStart, \$monthEnd])\n";
echo "       ->join('users', 'bookings.user_id', '=', 'users.id')\n";
echo "       ->join('organizations', 'users.organization_id', '=', 'organizations.id')\n";
echo "       ->select(\n";
echo "           DB::raw('CASE \n";
echo "               WHEN organizations.is_host = true THEN \\\"Própria OM\\\"\n";
echo "               WHEN users.armed_force = \\\"EB\\\" THEN \\\"Outras OM\\\"\n";
echo "               WHEN users.armed_force IN (\\\"MB\\\", \\\"FAB\\\") THEN \\\"Outras Forças\\\"\n";
echo "               ELSE \\\"Outras OM\\\"\n";
echo "           END as origin'),\n";
echo "           DB::raw('COUNT(*) as total')\n";
echo "       )\n";
echo "       ->groupBy(DB::raw('CASE \n";
echo "           WHEN organizations.is_host = true THEN \\\"Própria OM\\\"\n";
echo "           WHEN users.armed_force = \\\"EB\\\" THEN \\\"Outras OM\\\"\n";
echo "           WHEN users.armed_force IN (\\\"MB\\\", \\\"FAB\\\") THEN \\\"Outras Forças\\\"\n";
echo "           ELSE \\\"Outras OM\\\"\n";
echo "       END'))\n";
echo "       ->get();\n";
echo "   \n";
echo "   echo 'Origem dos Arranchados (mês atual):' . PHP_EOL;\n";
echo "   foreach (\$data as \$item) {\n";
echo "       echo \$item->origin . ': ' . \$item->total . ' reservas' . PHP_EOL;\n";
echo "   }\n";
echo "   \"\n\n";

echo "   # Verificar usuários específicos de outras forças com reservas\n";
echo "   docker-compose exec app php artisan tinker --execute=\"\n";
echo "   \$users = App\\Models\\User::whereIn('armed_force', ['MB', 'FAB'])\n";
echo "       ->with(['bookings' => function(\$query) {\n";
echo "           \$query->whereBetween('booking_date', [\n";
echo "               Carbon\\Carbon::now()->startOfMonth()->format('Y-m-d'),\n";
echo "               Carbon\\Carbon::now()->endOfMonth()->format('Y-m-d')\n";
echo "           ]);\n";
echo "       }])\n";
echo "       ->get();\n";
echo "   \n";
echo "   echo 'Usuários de outras forças com reservas:' . PHP_EOL;\n";
echo "   foreach (\$users as \$user) {\n";
echo "       if (\$user->bookings->count() > 0) {\n";
echo "           echo 'Nome: ' . \$user->war_name . ' | Força: ' . \$user->armed_force . ' | Reservas: ' . \$user->bookings->count() . PHP_EOL;\n";
echo "       }\n";
echo "   }\n";
echo "   \"\n\n";

echo "5. Para criar dados de teste (se necessário):\n\n";
echo "   # Criar seeder\n";
echo "   docker-compose exec app php artisan make:seeder OtherForcesBookingsSeeder\n\n";
echo "   # Executar seeder\n";
echo "   docker-compose exec app php artisan db:seed --class=OtherForcesBookingsSeeder\n\n";

echo "6. Verificar dashboard em funcionamento:\n";
echo "   - Acesse: http://localhost:8000/dashboard\n";
echo "   - Observe o gráfico 'Origem dos Arranchados'\n";
echo "   - Verifique os cards: Própria OM, Outras OM, Outras Forças\n\n";

echo "=== COMANDOS PRONTOS PARA EXECUÇÃO ===\n";
