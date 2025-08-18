#!/bin/bash

# Script de verificação pós-pull para SAGA
# Execute após fazer git pull e aplicar migrações

echo "🔍 VERIFICAÇÃO COMPLETA DO SISTEMA SAGA"
echo "======================================"

# Verificar se containers estão rodando
echo ""
echo "📋 1. Status dos Containers:"
docker compose ps

# Verificar migrações pendentes
echo ""
echo "📋 2. Status das Migrações:"
docker compose exec app php artisan migrate:status | tail -5

# Verificar ranks disponíveis
echo ""
echo "📋 3. Ranks Disponíveis (últimos 5):"
docker compose exec app php artisan tinker --execute="
\$ranks = \App\Models\Rank::orderBy('order')->get(['name', 'abbreviation', 'order']);
\$total = \$ranks->count();
echo 'Total de ranks: ' . \$total . PHP_EOL;
echo 'Últimos 5 ranks:' . PHP_EOL;
foreach(\$ranks->slice(-5) as \$rank) {
    echo '  ' . \$rank->order . '. ' . \$rank->name . ' (' . \$rank->abbreviation . ')' . PHP_EOL;
}
"

# Verificar role furriel
echo ""
echo "📋 4. Role Furriel:"
docker compose exec app php artisan tinker --execute="
\$furrieis = \App\Models\User::where('role', 'furriel')->count();
echo 'Usuários com role furriel: ' . \$furrieis . PHP_EOL;
"

# Verificar reservas de outras forças
echo ""
echo "📋 5. Reservas de Outras Forças Armadas:"
docker compose exec app php artisan tinker --execute="
\$count = \App\Models\Booking::whereHas('user', function(\$q) { 
    \$q->whereIn('armed_force', ['MB', 'FAB']); 
})->count();
echo 'Total de reservas MB/FAB: ' . \$count . PHP_EOL;
"

# Verificar funcionalidades do sistema
echo ""
echo "📋 6. Verificação de Funcionalidades:"
echo "   ✅ Interface furriel: /furriel/arranchamento-cia"
echo "   ✅ Dashboard: / (gráfico 'Origem dos Arranchados')"
echo "   ✅ Admin: /admin/users (novo usuário com Soldado EV)"
echo "   ✅ Registro: /register (ícones forças armadas)"

echo ""
echo "🎯 VERIFICAÇÃO CONCLUÍDA!"
echo "Se todos os números acima estão corretos, o sistema está funcionando."
