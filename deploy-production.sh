#!/bin/bash

# Script de Deploy SAGA - Produção
# Sistema de Agendamento e Gestão de Arranchamento

echo "🚀 Iniciando Deploy do SAGA para Produção..."

# Verificar se estamos na branch production
CURRENT_BRANCH=$(git branch --show-current)
if [ "$CURRENT_BRANCH" != "production" ]; then
    echo "⚠️  Mudando para branch production..."
    git checkout production
fi

# Verificar status do git
echo "📋 Verificando status do repositório..."
git status

# Pull das últimas mudanças
echo "📥 Atualizando código da produção..."
git pull origin production

# Verificar se Docker está rodando
echo "🐳 Verificando Docker..."
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker não está rodando. Por favor, inicie o Docker primeiro."
    exit 1
fi

# Parar containers existentes
echo "🛑 Parando containers existentes..."
docker-compose down

# Rebuildar containers para produção
echo "🔨 Construindo containers para produção..."
docker-compose up -d --build

# Aguardar containers iniciarem
echo "⏳ Aguardando containers iniciarem..."
sleep 10

# Verificar se containers estão rodando
echo "✅ Verificando status dos containers..."
docker-compose ps

# Executar migrations se necessário
echo "🗄️  Executando migrations..."
docker-compose exec app php artisan migrate --force

# Limpar caches
echo "🧹 Limpando caches..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Verificar se aplicação está rodando
echo "🌐 Verificando aplicação..."
sleep 5
if curl -f http://localhost:8000 > /dev/null 2>&1; then
    echo "✅ SAGA está rodando em http://localhost:8000"
else
    echo "❌ Erro: Aplicação não está respondendo"
    echo "📋 Logs dos containers:"
    docker-compose logs --tail=50
    exit 1
fi

echo "🎉 Deploy concluído com sucesso!"
echo "📊 Sistema SAGA disponível em: http://localhost:8000"
echo "🔐 Página de login: http://localhost:8000/login"
echo "📝 Registro: http://localhost:8000/register"

# Mostrar informações úteis
echo ""
echo "📋 Comandos úteis:"
echo "  - Ver logs: docker-compose logs -f"
echo "  - Parar: docker-compose down"
echo "  - Reiniciar: docker-compose restart"
echo "  - Status: docker-compose ps"
