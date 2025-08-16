#!/bin/bash
# scripts/database/restore.sh
# Script de restore para o sistema SAGA

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$(dirname "$SCRIPT_DIR")")"

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🔄 SAGA - Sistema de Restore${NC}"
echo "=================================================="

# Verificar parâmetros
if [ $# -lt 2 ]; then
    echo -e "${RED}❌ Parâmetros insuficientes!${NC}"
    echo ""
    echo "Uso: $0 <ambiente> <arquivo_backup> [tipo]"
    echo ""
    echo "Ambientes:"
    echo "  dev     - Ambiente de desenvolvimento"
    echo "  staging - Ambiente de staging"
    echo ""
    echo "Tipos (opcional):"
    echo "  complete - Restore completo (padrão)"
    echo "  data     - Apenas dados"
    echo "  users    - Apenas usuários"
    echo "  bookings - Apenas reservas"
    echo ""
    echo "Exemplos:"
    echo "  $0 dev backup_dev_complete_20250815_120000.sql.gz"
    echo "  $0 staging backup_staging_data_20250815_120000.sql.gz data"
    echo ""
    exit 1
fi

ENVIRONMENT=$1
BACKUP_FILE=$2
RESTORE_TYPE=${3:-complete}

# Validar ambiente
case $ENVIRONMENT in
    "dev")
        CONTAINER="saga_db"
        DATABASE="saga"
        APP_CONTAINER="saga_app_dev"
        PORT="8000"
        ;;
    "staging")
        CONTAINER="saga_db_staging"
        DATABASE="saga_staging"
        APP_CONTAINER="saga_app_staging"
        PORT="8080"
        ;;
    *)
        echo -e "${RED}❌ Ambiente inválido: $ENVIRONMENT${NC}"
        echo "Ambientes válidos: dev, staging"
        exit 1
        ;;
esac

# Verificar se arquivo existe
if [ ! -f "$BACKUP_FILE" ]; then
    echo -e "${RED}❌ Arquivo de backup não encontrado: $BACKUP_FILE${NC}"
    echo ""
    echo "Arquivos disponíveis em backups/:"
    ls -la "$PROJECT_ROOT/backups/"*.gz 2>/dev/null | tail -10 || echo "Nenhum backup encontrado"
    exit 1
fi

# Verificar se containers estão rodando
if ! docker ps | grep -q "$CONTAINER"; then
    echo -e "${RED}❌ Container $CONTAINER não está rodando!${NC}"
    echo "Execute: docker-compose up -d"
    exit 1
fi

# Mostrar informações do backup
echo -e "${YELLOW}📋 Informações do restore:${NC}"
echo "  Ambiente: $ENVIRONMENT"
echo "  Container: $CONTAINER"
echo "  Database: $DATABASE"
echo "  Arquivo: $BACKUP_FILE"
echo "  Tipo: $RESTORE_TYPE"
echo "  Tamanho: $(ls -lh "$BACKUP_FILE" | awk '{print $5}')"
echo ""

# Confirmação baseada no tipo de restore
case $RESTORE_TYPE in
    "complete")
        WARNING_MSG="⚠️  ATENÇÃO: Isto irá substituir TODA a estrutura e dados do ambiente $ENVIRONMENT!"
        CONFIRM_TEXT="CONFIRMO RESTORE COMPLETO"
        ;;
    "data")
        WARNING_MSG="⚠️  ATENÇÃO: Isto irá substituir TODOS os dados do ambiente $ENVIRONMENT!"
        CONFIRM_TEXT="CONFIRMO RESTORE DADOS"
        ;;
    "users"|"bookings")
        WARNING_MSG="⚠️  ATENÇÃO: Isto irá substituir os dados da tabela $RESTORE_TYPE no ambiente $ENVIRONMENT!"
        CONFIRM_TEXT="CONFIRMO RESTORE $RESTORE_TYPE"
        ;;
    *)
        echo -e "${RED}❌ Tipo de restore inválido: $RESTORE_TYPE${NC}"
        exit 1
        ;;
esac

echo -e "${RED}$WARNING_MSG${NC}"
echo ""
read -p "Digite '$CONFIRM_TEXT' para continuar: " confirmacao

if [ "$confirmacao" != "$CONFIRM_TEXT" ]; then
    echo -e "${YELLOW}⏹️  Operação cancelada pelo usuário.${NC}"
    exit 0
fi

# Criar backup de segurança antes do restore
echo -e "${YELLOW}💾 Criando backup de segurança...${NC}"
SAFETY_BACKUP="$PROJECT_ROOT/backups/safety_backup_${ENVIRONMENT}_$(date +%Y%m%d_%H%M%S).sql"
docker exec "$CONTAINER" pg_dump -U saga_user -d "$DATABASE" \
    --clean --create --if-exists > "$SAFETY_BACKUP"
gzip "$SAFETY_BACKUP"
echo "  ✅ Backup de segurança: ${SAFETY_BACKUP}.gz"

# Parar aplicação
echo -e "${YELLOW}🛑 Parando aplicação $APP_CONTAINER...${NC}"
docker-compose stop "$APP_CONTAINER" 2>/dev/null || true

# Aguardar alguns segundos
sleep 3

# Executar restore baseado no tipo
echo -e "${YELLOW}📥 Executando restore ($RESTORE_TYPE)...${NC}"

case $RESTORE_TYPE in
    "complete")
        # Restore completo - derruba e recria tudo
        if [[ "$BACKUP_FILE" == *.gz ]]; then
            zcat "$BACKUP_FILE" | docker exec -i "$CONTAINER" psql -U saga_user -d "$DATABASE"
        else
            docker exec -i "$CONTAINER" psql -U saga_user -d "$DATABASE" < "$BACKUP_FILE"
        fi
        ;;
    "data")
        # Truncar tabelas e inserir dados
        echo "  🗑️  Limpando dados existentes..."
        docker exec "$CONTAINER" psql -U saga_user -d "$DATABASE" \
            -c "TRUNCATE TABLE bookings, weekly_menus RESTART IDENTITY CASCADE;"
        
        if [[ "$BACKUP_FILE" == *.gz ]]; then
            zcat "$BACKUP_FILE" | docker exec -i "$CONTAINER" psql -U saga_user -d "$DATABASE"
        else
            docker exec -i "$CONTAINER" psql -U saga_user -d "$DATABASE" < "$BACKUP_FILE"
        fi
        ;;
    "users"|"bookings")
        # Restore específico de tabela
        echo "  🗑️  Limpando tabela $RESTORE_TYPE..."
        docker exec "$CONTAINER" psql -U saga_user -d "$DATABASE" \
            -c "TRUNCATE TABLE $RESTORE_TYPE RESTART IDENTITY CASCADE;"
            
        if [[ "$BACKUP_FILE" == *.gz ]]; then
            zcat "$BACKUP_FILE" | docker exec -i "$CONTAINER" psql -U saga_user -d "$DATABASE"
        else
            docker exec -i "$CONTAINER" psql -U saga_user -d "$DATABASE" < "$BACKUP_FILE"
        fi
        ;;
esac

# Reiniciar aplicação
echo -e "${YELLOW}🚀 Reiniciando aplicação...${NC}"
docker-compose start "$APP_CONTAINER"

# Aguardar inicialização
echo "  ⏳ Aguardando inicialização..."
sleep 15

# Verificar se aplicação subiu
if docker ps | grep -q "$APP_CONTAINER"; then
    echo "  ✅ Container $APP_CONTAINER está rodando"
else
    echo -e "${RED}  ❌ Falha ao iniciar $APP_CONTAINER${NC}"
    echo "Verificando logs:"
    docker logs "$APP_CONTAINER" --tail 20
fi

# Limpar caches Laravel
echo -e "${YELLOW}🧹 Limpando caches do Laravel...${NC}"
docker exec "$APP_CONTAINER" php artisan cache:clear 2>/dev/null || echo "  ⚠️ Falha ao limpar cache"
docker exec "$APP_CONTAINER" php artisan view:clear 2>/dev/null || echo "  ⚠️ Falha ao limpar views"
docker exec "$APP_CONTAINER" php artisan config:cache 2>/dev/null || echo "  ⚠️ Falha ao cachear config"

# Verificar conectividade
echo -e "${YELLOW}🔍 Verificando conectividade...${NC}"
sleep 5

HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:$PORT" || echo "000")
if [ "$HTTP_STATUS" = "200" ]; then
    echo -e "${GREEN}  ✅ Aplicação respondendo HTTP 200 na porta $PORT${NC}"
else
    echo -e "${RED}  ❌ Aplicação não está respondendo (HTTP $HTTP_STATUS)${NC}"
    echo "  🔍 Verificando logs da aplicação:"
    docker logs "$APP_CONTAINER" --tail 10
fi

# Verificar dados no banco
echo -e "${YELLOW}📊 Verificando dados no banco...${NC}"
docker exec "$CONTAINER" psql -U saga_user -d "$DATABASE" -c "
SELECT 
    'organizations' as tabela, COUNT(*) as registros FROM organizations
UNION ALL
SELECT 'ranks' as tabela, COUNT(*) as registros FROM ranks  
UNION ALL
SELECT 'users' as tabela, COUNT(*) as registros FROM users
UNION ALL
SELECT 'bookings' as tabela, COUNT(*) as registros FROM bookings
UNION ALL 
SELECT 'weekly_menus' as tabela, COUNT(*) as registros FROM weekly_menus
ORDER BY tabela;" 2>/dev/null || echo "  ⚠️ Falha ao verificar dados"

# Gerar relatório de restore
REPORT_FILE="$PROJECT_ROOT/backups/restore_report_$(date +%Y%m%d_%H%M%S).txt"
cat > "$REPORT_FILE" << EOF
SAGA - Relatório de Restore
===========================
Data: $(date)
Ambiente: $ENVIRONMENT
Arquivo: $BACKUP_FILE
Tipo: $RESTORE_TYPE
Backup de segurança: ${SAFETY_BACKUP}.gz

Status:
- Container: $(docker ps | grep "$APP_CONTAINER" | awk '{print $7}' || echo "STOPPED")
- HTTP Status: $HTTP_STATUS
- Database: Conectado

EOF

echo -e "${GREEN}🎉 Restore concluído!${NC}"
echo "=================================================="
echo "🌐 Acesse: http://localhost:$PORT"
echo "📋 Relatório: $REPORT_FILE"
echo "💾 Backup segurança: ${SAFETY_BACKUP}.gz"
echo "=================================================="

# Dicas finais
echo -e "${BLUE}💡 Dicas:${NC}"
echo "  - Verifique a aplicação em http://localhost:$PORT"
echo "  - Em caso de problemas, use o backup de segurança para reverter"
echo "  - Logs da aplicação: docker logs $APP_CONTAINER"
