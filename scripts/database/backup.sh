#!/bin/bash
# scripts/database/backup.sh
# Script completo de backup para o sistema SAGA

set -e

# Configurações
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$(dirname "$SCRIPT_DIR")")"
BACKUP_DIR="$PROJECT_ROOT/backups"
DATE=$(date +%Y%m%d_%H%M%S)

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🗄️ SAGA - Sistema de Backup${NC}"
echo "=================================================="
echo "Data/Hora: $(date)"
echo "Diretório: $BACKUP_DIR"
echo "=================================================="

# Verificar se Docker está rodando
if ! docker info >/dev/null 2>&1; then
    echo -e "${RED}❌ Docker não está rodando!${NC}"
    exit 1
fi

# Criar diretório de backup
mkdir -p "$BACKUP_DIR"

# Função de backup por ambiente
backup_environment() {
    local env=$1
    local container=$2
    local database=$3
    
    echo -e "${YELLOW}📦 Fazendo backup do ambiente: $env${NC}"
    
    # Verificar se container existe e está rodando
    if ! docker ps | grep -q "$container"; then
        echo -e "${YELLOW}⚠️  Container $container não está rodando, pulando...${NC}"
        return 0
    fi
    
    # Backup completo (estrutura + dados)
    echo "  📋 Backup completo..."
    docker exec "$container" pg_dump -U saga_user -d "$database" \
        --clean --create --if-exists \
        > "$BACKUP_DIR/saga_${env}_complete_$DATE.sql"
    
    # Backup apenas dados
    echo "  📊 Backup apenas dados..."
    docker exec "$container" pg_dump -U saga_user -d "$database" \
        --data-only --inserts \
        > "$BACKUP_DIR/saga_${env}_data_$DATE.sql"
    
    # Backup por tabelas importantes
    echo "  👥 Backup tabela users..."
    docker exec "$container" pg_dump -U saga_user -d "$database" \
        --table=users --data-only --inserts \
        > "$BACKUP_DIR/saga_${env}_users_$DATE.sql"
    
    echo "  📅 Backup tabela bookings..."
    docker exec "$container" pg_dump -U saga_user -d "$database" \
        --table=bookings --data-only --inserts \
        > "$BACKUP_DIR/saga_${env}_bookings_$DATE.sql"
    
    # Compactar arquivos
    echo "  🗜️  Compactando arquivos..."
    gzip "$BACKUP_DIR/saga_${env}_complete_$DATE.sql"
    gzip "$BACKUP_DIR/saga_${env}_data_$DATE.sql"
    gzip "$BACKUP_DIR/saga_${env}_users_$DATE.sql"
    gzip "$BACKUP_DIR/saga_${env}_bookings_$DATE.sql"
    
    # Verificar tamanhos
    echo "  📏 Tamanhos dos arquivos:"
    ls -lh "$BACKUP_DIR"/*"${env}"*"$DATE"*.gz | awk '{print "    " $9 ": " $5}'
    
    echo -e "${GREEN}✅ Backup $env concluído${NC}"
}

# Executar backups para cada ambiente
echo -e "${YELLOW}🔍 Verificando ambientes disponíveis...${NC}"

if docker ps | grep -q saga_db; then
    backup_environment "dev" "saga_db" "saga"
fi

if docker ps | grep -q saga_db_staging; then
    backup_environment "staging" "saga_db_staging" "saga_staging"
fi

# Gerar relatório de backup
echo -e "${YELLOW}📊 Gerando relatório...${NC}"
REPORT_FILE="$BACKUP_DIR/backup_report_$DATE.txt"

cat > "$REPORT_FILE" << EOF
SAGA - Relatório de Backup
==========================
Data: $(date)
Diretório: $BACKUP_DIR

Arquivos gerados:
EOF

ls -la "$BACKUP_DIR"/*"$DATE"* >> "$REPORT_FILE"

echo "" >> "$REPORT_FILE"
echo "Espaço total utilizado:" >> "$REPORT_FILE"
du -sh "$BACKUP_DIR" >> "$REPORT_FILE"

# Limpeza de backups antigos - DESABILITADA
# echo -e "${YELLOW}🧹 Limpando backups antigos (>30 dias)...${NC}"
# DELETED_COUNT=$(find "$BACKUP_DIR" -name "*.gz" -mtime +30 -delete -print | wc -l)
# echo "  🗑️  Removidos: $DELETED_COUNT arquivos"

# Limpeza de relatórios antigos - DESABILITADA
# find "$BACKUP_DIR" -name "backup_report_*.txt" -mtime +7 -delete

echo -e "${BLUE}📦 Backups preservados indefinidamente${NC}"
echo "  💾 Para limpeza manual, use: find $BACKUP_DIR -name '*.gz' -mtime +30 -delete"

echo -e "${GREEN}🎉 Backup concluído com sucesso!${NC}"
echo "=================================================="
echo "📁 Arquivos salvos em: $BACKUP_DIR"
echo "📋 Relatório: $REPORT_FILE"
echo "=================================================="

# Mostrar resumo dos arquivos criados
echo -e "${BLUE}📦 Resumo dos backups criados:${NC}"
ls -la "$BACKUP_DIR"/*"$DATE"*
