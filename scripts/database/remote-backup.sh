#!/bin/bash
# scripts/database/remote-backup.sh
# Script para fazer backup de máquina remota e importar localmente

set -e

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🌐 SAGA - Backup de Máquina Remota${NC}"
echo "=================================================="

# Verificar parâmetros
if [ $# -lt 2 ]; then
    echo -e "${RED}❌ Parâmetros insuficientes!${NC}"
    echo ""
    echo "Uso: $0 <usuario@host> <ambiente_destino> [container_remoto] [database_remoto]"
    echo ""
    echo "Exemplos:"
    echo "  $0 sonnote@192.168.0.57 dev"
    echo "  $0 sonnote@192.168.0.57 staging saga_db saga"
    echo "  $0 usuario@servidor.com dev postgres_container banco_producao"
    echo ""
    exit 1
fi

REMOTE_HOST=$1
LOCAL_ENV=$2
REMOTE_CONTAINER=${3:-saga_db}
REMOTE_DATABASE=${4:-saga}

# Configurar ambiente local
case $LOCAL_ENV in
    "dev")
        LOCAL_CONTAINER="saga_db"
        LOCAL_DATABASE="saga"
        LOCAL_APP_CONTAINER="saga_app_dev"
        LOCAL_PORT="8000"
        ;;
    "staging")
        LOCAL_CONTAINER="saga_db_staging"
        LOCAL_DATABASE="saga_staging"
        LOCAL_APP_CONTAINER="saga_app_staging"
        LOCAL_PORT="8080"
        ;;
    *)
        echo -e "${RED}❌ Ambiente local inválido: $LOCAL_ENV${NC}"
        echo "Ambientes válidos: dev, staging"
        exit 1
        ;;
esac

echo -e "${YELLOW}📋 Configuração do backup remoto:${NC}"
echo "  Host remoto: $REMOTE_HOST"
echo "  Container remoto: $REMOTE_CONTAINER"
echo "  Database remoto: $REMOTE_DATABASE"
echo "  Ambiente local: $LOCAL_ENV"
echo "  Container local: $LOCAL_CONTAINER"
echo "  Database local: $LOCAL_DATABASE"
echo ""

# Verificar conectividade SSH
echo -e "${YELLOW}🔍 Testando conectividade SSH...${NC}"
if ! ssh -o ConnectTimeout=5 -o BatchMode=yes "$REMOTE_HOST" "echo 'SSH OK'" 2>/dev/null; then
    echo -e "${RED}❌ Não foi possível conectar via SSH!${NC}"
    echo "Verifique:"
    echo "  - Conectividade de rede"
    echo "  - Chaves SSH configuradas"
    echo "  - Usuário e host corretos"
    echo ""
    echo "Para configurar chaves SSH:"
    echo "  ssh-copy-id $REMOTE_HOST"
    exit 1
fi
echo -e "${GREEN}✅ Conectividade SSH confirmada${NC}"

# Verificar se Docker está rodando na máquina remota
echo -e "${YELLOW}🐋 Verificando Docker remoto...${NC}"
if ! ssh "$REMOTE_HOST" "docker ps >/dev/null 2>&1"; then
    echo -e "${RED}❌ Docker não está rodando na máquina remota ou usuário sem permissão!${NC}"
    echo "Execute na máquina remota:"
    echo "  sudo usermod -aG docker \$USER"
    echo "  newgrp docker"
    exit 1
fi

# Verificar se container remoto existe
if ! ssh "$REMOTE_HOST" "docker ps -a | grep -q $REMOTE_CONTAINER"; then
    echo -e "${RED}❌ Container $REMOTE_CONTAINER não encontrado na máquina remota!${NC}"
    echo "Containers disponíveis:"
    ssh "$REMOTE_HOST" "docker ps -a --format 'table {{.Names}}\t{{.Image}}\t{{.Status}}'"
    exit 1
fi
echo -e "${GREEN}✅ Container $REMOTE_CONTAINER encontrado${NC}"

# Verificar containers locais
echo -e "${YELLOW}🏠 Verificando ambiente local...${NC}"
if ! docker ps | grep -q "$LOCAL_CONTAINER"; then
    echo -e "${RED}❌ Container local $LOCAL_CONTAINER não está rodando!${NC}"
    echo "Execute: docker-compose up -d"
    exit 1
fi
echo -e "${GREEN}✅ Ambiente local pronto${NC}"

# Confirmar operação
echo -e "${RED}⚠️  ATENÇÃO: Esta operação irá substituir TODOS os dados do ambiente $LOCAL_ENV!${NC}"
echo ""
read -p "Digite 'CONFIRMO BACKUP REMOTO' para continuar: " confirmacao

if [ "$confirmacao" != "CONFIRMO BACKUP REMOTO" ]; then
    echo -e "${YELLOW}⏹️  Operação cancelada pelo usuário.${NC}"
    exit 0
fi

# Criar diretório de backup
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$(dirname "$SCRIPT_DIR")")"
BACKUP_DIR="$PROJECT_ROOT/backups"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$BACKUP_DIR/remote_backup_${REMOTE_HOST//[^a-zA-Z0-9]/_}_$DATE.sql"

mkdir -p "$BACKUP_DIR"

# Backup de segurança local
echo -e "${YELLOW}💾 Criando backup de segurança local...${NC}"
SAFETY_BACKUP="$BACKUP_DIR/safety_${LOCAL_ENV}_before_remote_$DATE.sql"
docker exec "$LOCAL_CONTAINER" pg_dump -U saga_user -d "$LOCAL_DATABASE" \
    --clean --create --if-exists > "$SAFETY_BACKUP"
gzip "$SAFETY_BACKUP"
echo "  ✅ Backup de segurança: ${SAFETY_BACKUP}.gz"

# Fazer backup da máquina remota
echo -e "${YELLOW}📥 Fazendo backup da máquina remota...${NC}"
echo "  🔍 Verificando dados remotos..."

# Verificar dados na máquina remota
ssh "$REMOTE_HOST" "docker exec $REMOTE_CONTAINER psql -U saga_user -d $REMOTE_DATABASE -c \"
SELECT 
    'users' as tabela, COUNT(*) as registros FROM users
UNION ALL
SELECT 'bookings' as tabela, COUNT(*) as registros FROM bookings
UNION ALL
SELECT 'organizations' as tabela, COUNT(*) as registros FROM organizations
ORDER BY tabela;\"" 2>/dev/null || echo "  ⚠️ Não foi possível verificar dados remotos"

echo "  📦 Executando backup remoto..."
ssh "$REMOTE_HOST" "docker exec $REMOTE_CONTAINER pg_dump -U saga_user -d $REMOTE_DATABASE \
    --clean --create --if-exists" > "$BACKUP_FILE"

if [ ! -s "$BACKUP_FILE" ]; then
    echo -e "${RED}❌ Backup remoto falhou ou está vazio!${NC}"
    exit 1
fi

echo "  ✅ Backup remoto concluído: $(ls -lh "$BACKUP_FILE" | awk '{print $5}')"

# Parar aplicação local
echo -e "${YELLOW}🛑 Parando aplicação local...${NC}"
docker-compose stop "$LOCAL_APP_CONTAINER" 2>/dev/null || true
sleep 3

# Importar backup
echo -e "${YELLOW}📥 Importando backup remoto...${NC}"
docker exec -i "$LOCAL_CONTAINER" psql -U saga_user -d "$LOCAL_DATABASE" < "$BACKUP_FILE"

# Reiniciar aplicação
echo -e "${YELLOW}🚀 Reiniciando aplicação...${NC}"
docker-compose start "$LOCAL_APP_CONTAINER"
sleep 15

# Limpar caches
echo -e "${YELLOW}🧹 Limpando caches...${NC}"
docker exec "$LOCAL_APP_CONTAINER" php artisan config:clear >/dev/null 2>&1 || true
docker exec "$LOCAL_APP_CONTAINER" php artisan view:clear >/dev/null 2>&1 || true
docker exec "$LOCAL_APP_CONTAINER" php artisan route:clear >/dev/null 2>&1 || true

# Verificar resultado
echo -e "${YELLOW}🔍 Verificando importação...${NC}"
sleep 5

# Testar HTTP
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:$LOCAL_PORT" || echo "000")
if [ "$HTTP_STATUS" = "200" ]; then
    echo -e "${GREEN}  ✅ Aplicação respondendo HTTP 200${NC}"
else
    echo -e "${RED}  ❌ Aplicação com problemas (HTTP $HTTP_STATUS)${NC}"
    echo "  🔍 Verificando logs:"
    docker logs "$LOCAL_APP_CONTAINER" --tail 10
fi

# Verificar dados importados
echo -e "${YELLOW}📊 Verificando dados importados...${NC}"
docker exec "$LOCAL_CONTAINER" psql -U saga_user -d "$LOCAL_DATABASE" -c "
SELECT 
    'users' as tabela, COUNT(*) as registros FROM users
UNION ALL
SELECT 'bookings' as tabela, COUNT(*) as registros FROM bookings
UNION ALL
SELECT 'organizations' as tabela, COUNT(*) as registros FROM organizations
UNION ALL
SELECT 'weekly_menus' as tabela, COUNT(*) as registros FROM weekly_menus
ORDER BY tabela;" 2>/dev/null || echo "  ⚠️ Erro ao verificar dados"

# Compactar backup
echo -e "${YELLOW}🗜️  Compactando backup...${NC}"
gzip "$BACKUP_FILE"

# Relatório final
REPORT_FILE="$BACKUP_DIR/remote_import_report_$DATE.txt"
cat > "$REPORT_FILE" << EOF
SAGA - Relatório de Importação Remota
=====================================
Data: $(date)
Host remoto: $REMOTE_HOST
Container remoto: $REMOTE_CONTAINER
Database remoto: $REMOTE_DATABASE
Ambiente local: $LOCAL_ENV
Container local: $LOCAL_CONTAINER
Database local: $LOCAL_DATABASE

Arquivos:
- Backup remoto: ${BACKUP_FILE}.gz
- Backup segurança: ${SAFETY_BACKUP}.gz

Status:
- HTTP Status: $HTTP_STATUS
- Container: $(docker ps | grep "$LOCAL_APP_CONTAINER" | awk '{print $7}' || echo "STOPPED")

EOF

echo -e "${GREEN}🎉 Importação de backup remoto concluída!${NC}"
echo "=================================================="
echo "🌐 Acesse: http://localhost:$LOCAL_PORT"
echo "📋 Relatório: $REPORT_FILE"
echo "💾 Backup remoto: ${BACKUP_FILE}.gz"
echo "💾 Backup segurança: ${SAFETY_BACKUP}.gz"
echo "=================================================="

# Dicas finais
echo -e "${BLUE}💡 Próximos passos:${NC}"
echo "  - Verificar a aplicação em http://localhost:$LOCAL_PORT"
echo "  - Conferir dados de usuários e reservas"
echo "  - Em caso de problemas, use: ./scripts/database/restore.sh $LOCAL_ENV ${SAFETY_BACKUP}.gz"
echo "  - Backup remoto salvo em: ${BACKUP_FILE}.gz"
