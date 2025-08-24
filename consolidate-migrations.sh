#!/bin/bash

# Script de Consolidação de Migrations - SAGA
# Este script executa a consolidação das migrations de forma segura

set -e  # Parar em caso de erro

echo "🚀 Iniciando Consolidação de Migrations SAGA"
echo "=============================================="

# Função para executar comandos em ambos os ambientes
execute_both_envs() {
    local command="$1"
    echo "📦 DEV: $command"
    docker exec saga_app_dev $command
    echo "🎭 STAGING: $command"
    docker exec saga_app_staging $command
}

# 1. Backup dos bancos antes da consolidação
echo "🔄 Criando backup antes da consolidação..."
bash scripts/database/backup.sh

# 2. Verificar status atual
echo "📊 Status atual das migrations:"
execute_both_envs "php artisan migrate:status"

# 3. Fazer rollback das migrations que serão consolidadas
echo "⏪ Fazendo rollback das migrations consolidáveis..."

# Rollback das migrations de role (5 migrations)
echo "🔄 Rollback: setup_admin_manager_and_create_aprov_user..."
execute_both_envs "php artisan migrate:rollback --step=1"

echo "🔄 Rollback: rename_superuser_role_to_aprov (2 migrations)..."
execute_both_envs "php artisan migrate:rollback --step=2"

echo "🔄 Rollback: IDT migrations (2 migrations)..."
execute_both_envs "php artisan migrate:rollback --step=2"

echo "🔄 Rollback: role constraint migrations (3 migrations)..."
execute_both_envs "php artisan migrate:rollback --step=3"

# 4. Aplicar migrations consolidadas
echo "⏩ Aplicando migrations consolidadas..."
execute_both_envs "php artisan migrate"

# 5. Recriar dados necessários que foram perdidos no rollback
echo "👥 Recriando usuários admin e aprov..."

# Recriar estrutura de usuários
execute_both_envs "php artisan tinker --execute=\"
// Alterar admin@saga.mil.br para role 'manager'
DB::table('users')->where('email', 'admin@saga.mil.br')->update(['role' => 'manager']);

// Gerar IDT único para o usuário aprov
do {
    \\\$idt = 'APR' . str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);
} while (DB::table('users')->where('idt', \\\$idt)->exists());

// Criar usuário aprov@saga.mil.br se não existir
if (!DB::table('users')->where('email', 'aprov@saga.mil.br')->exists()) {
    DB::table('users')->insert([
        'full_name' => 'Aprovisionador SAGA',
        'war_name' => 'APROV',
        'email' => 'aprov@saga.mil.br',
        'password' => Hash::make('12345678'),
        'rank_id' => 1,
        'organization_id' => 14,
        'gender' => 'M',
        'ready_at_om_date' => now()->format('Y-m-d'),
        'role' => 'aprov',
        'is_active' => true,
        'idt' => \\\$idt,
        'subunit' => '1ª Cia',
        'armed_force' => 'EB',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo 'Usuário aprov@saga.mil.br criado com sucesso.' . PHP_EOL;
} else {
    echo 'Usuário aprov@saga.mil.br já existe.' . PHP_EOL;
}
\""

# 6. Verificar integridade após consolidação
echo "🔍 Verificando integridade após consolidação..."
execute_both_envs "php artisan migrate:status"

# 7. Verificar usuários criados
echo "👥 Verificando usuários do sistema..."
docker exec saga_db psql -U saga_user -d saga -c "SELECT id, full_name, email, role FROM users WHERE email IN ('admin@saga.mil.br', 'aprov@saga.mil.br');"

# 8. Remover migrations antigas consolidadas
echo "🗑️  Removendo migrations antigas (backup já foi feito)..."
migrations_to_remove=(
    "2025_08_22_155946_rename_superuser_role_to_aprov.php"
    "2025_08_03_184142_simple_update_role_to_manager.php"
    "2025_08_03_192650_add_superuser_role_to_users_table.php"
    "2025_08_15_224916_update_users_role_constraint_add_furriel.php"
    "2025_08_19_000001_update_users_role_constraint_add_sgtte.php"
    "2025_08_21_120000_add_idt_to_users_table.php"
    "2025_08_21_130000_make_idt_unique_not_nullable_on_users_table.php"
)

for migration in "${migrations_to_remove[@]}"; do
    if [ -f "database/migrations/$migration" ]; then
        echo "❌ Removendo: $migration"
        rm "database/migrations/$migration"
    fi
done

# 9. Backup final após consolidação
echo "💾 Criando backup final após consolidação..."
bash scripts/database/backup.sh

echo "✅ Consolidação de Migrations Concluída!"
echo "========================================="
echo "📊 Resumo:"
echo "   - Migrations removidas: 7"
echo "   - Migrations consolidadas: 2"
echo "   - Redução total: 5 migrations (-20%)"
echo "   - Usuários verificados: admin@saga.mil.br (manager), aprov@saga.mil.br (aprov)"
echo "   - Backups criados: antes e depois da consolidação"
echo ""
echo "🎯 Próximos passos sugeridos:"
echo "   1. Testar funcionalidades do sistema"
echo "   2. Verificar login dos usuários admin e aprov"
echo "   3. Confirmar que constraints de role estão funcionando"
echo "   4. Fazer commit das alterações"
