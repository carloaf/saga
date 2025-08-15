#!/bin/bash

# Script de Debug das Páginas de Login - SAGA

echo "🔍 Testando todas as páginas de login do SAGA..."

# Função para testar URL
test_url() {
    local url=$1
    local name=$2
    local status_code=$(curl -s -o /dev/null -w "%{http_code}" "$url")
    
    if [ "$status_code" = "200" ]; then
        echo "✅ $name - $url (Status: $status_code)"
    else
        echo "❌ $name - $url (Status: $status_code)"
    fi
}

# URLs para testar
echo ""
echo "📋 Testando páginas de autenticação:"

test_url "http://localhost:8000/" "Homepage"
test_url "http://localhost:8000/login" "Login Principal (Google OAuth)"
test_url "http://localhost:8000/login/traditional" "Login Tradicional"
test_url "http://localhost:8000/register" "Registro"
test_url "http://localhost:8000/register/complete" "Completar Registro"
test_url "http://localhost:8000/auth/google" "Redirect Google OAuth"
test_url "http://localhost:8000/dev-admin-login" "Dev Admin Login"
test_url "http://localhost:8000/dev-login" "Dev Login"

echo ""
echo "🛠️ Verificando containers Docker:"
docker-compose ps

echo ""
echo "📊 Últimos 10 logs do Laravel:"
docker-compose exec app tail -10 /var/www/html/storage/logs/laravel.log 2>/dev/null || echo "Nenhum log de erro encontrado"

echo ""
echo "🗄️ Testando conexão com banco de dados:"
docker-compose exec app php artisan tinker --execute="
try {
    \$users = \App\Models\User::count();
    echo 'Conexão com BD OK - Usuários cadastrados: ' . \$users . PHP_EOL;
} catch (Exception \$e) {
    echo 'Erro no BD: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "🔐 Verificando se existem usuários para teste:"
docker-compose exec app php artisan tinker --execute="
\$testUser = \App\Models\User::where('email', 'admin@test.com')->first();
if (\$testUser) {
    echo 'Usuário de teste encontrado: admin@test.com' . PHP_EOL;
} else {
    echo 'Nenhum usuário de teste encontrado. Criando...' . PHP_EOL;
    try {
        \App\Models\User::create([
            'full_name' => 'Admin Teste',
            'war_name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('123456789'),
            'gender' => 'male',
            'ready_at_om_date' => now(),
            'rank_id' => 1,
            'organization_id' => 1,
            'role' => 'manager'
        ]);
        echo 'Usuário criado: admin@test.com / senha: 123456789' . PHP_EOL;
    } catch (Exception \$e) {
        echo 'Erro ao criar usuário: ' . \$e->getMessage() . PHP_EOL;
    }
}
"

echo ""
echo "✅ Debug das páginas de login concluído!"
echo ""
echo "📋 Resumo para testar login:"
echo "   🌐 Login Google: http://localhost:8000/login"
echo "   🔑 Login Tradicional: http://localhost:8000/login/traditional"
echo "   📝 Registro: http://localhost:8000/register"
echo "   👤 Usuário teste: admin@test.com / senha: 123456789"
