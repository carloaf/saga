#!/bin/bash
# scripts/database/cleanup_old_backups.sh
# Script manual para limpeza de backups antigos

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$(dirname "$SCRIPT_DIR")")"
BACKUP_DIR="$PROJECT_ROOT/backups"

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🧹 SAGA - Limpeza Manual de Backups${NC}"
echo "=================================================="

# Verificar se diretório existe
if [ ! -d "$BACKUP_DIR" ]; then
    echo -e "${RED}❌ Diretório de backup não encontrado: $BACKUP_DIR${NC}"
    exit 1
fi

# Contar backups atuais
TOTAL_BACKUPS=$(find "$BACKUP_DIR" -name "*.gz" | wc -l)
OLD_BACKUPS=$(find "$BACKUP_DIR" -name "*.gz" -mtime +30 | wc -l)
OLD_REPORTS=$(find "$BACKUP_DIR" -name "backup_report_*.txt" -mtime +7 | wc -l)

echo "📊 Situação atual:"
echo "  📦 Total de backups: $TOTAL_BACKUPS"
echo "  🗓️  Backups >30 dias: $OLD_BACKUPS"
echo "  📋 Relatórios >7 dias: $OLD_REPORTS"
echo ""

if [ "$OLD_BACKUPS" -eq 0 ] && [ "$OLD_REPORTS" -eq 0 ]; then
    echo -e "${GREEN}✅ Nenhum arquivo antigo para remover!${NC}"
    exit 0
fi

# Mostrar arquivos que serão removidos
if [ "$OLD_BACKUPS" -gt 0 ]; then
    echo -e "${YELLOW}📦 Backups que serão removidos (>30 dias):${NC}"
    find "$BACKUP_DIR" -name "*.gz" -mtime +30 -ls
    echo ""
fi

if [ "$OLD_REPORTS" -gt 0 ]; then
    echo -e "${YELLOW}📋 Relatórios que serão removidos (>7 dias):${NC}"
    find "$BACKUP_DIR" -name "backup_report_*.txt" -mtime +7 -ls
    echo ""
fi

# Confirmação
echo -e "${RED}⚠️  ATENÇÃO: Esta operação é irreversível!${NC}"
read -p "Digite 'CONFIRMO LIMPEZA' para continuar: " confirmacao

if [ "$confirmacao" != "CONFIRMO LIMPEZA" ]; then
    echo -e "${YELLOW}⏹️  Operação cancelada pelo usuário.${NC}"
    exit 0
fi

# Executar limpeza
echo -e "${YELLOW}🗑️  Removendo backups antigos...${NC}"
if [ "$OLD_BACKUPS" -gt 0 ]; then
    DELETED_BACKUPS=$(find "$BACKUP_DIR" -name "*.gz" -mtime +30 -delete -print | wc -l)
    echo "  📦 Backups removidos: $DELETED_BACKUPS"
fi

if [ "$OLD_REPORTS" -gt 0 ]; then
    DELETED_REPORTS=$(find "$BACKUP_DIR" -name "backup_report_*.txt" -mtime +7 -delete -print | wc -l)
    echo "  📋 Relatórios removidos: $DELETED_REPORTS"
fi

# Resultado final
FINAL_BACKUPS=$(find "$BACKUP_DIR" -name "*.gz" | wc -l)
SPACE_SAVED=$(du -sh "$BACKUP_DIR" | cut -f1)

echo ""
echo -e "${GREEN}✅ Limpeza concluída!${NC}"
echo "📊 Situação final:"
echo "  📦 Backups restantes: $FINAL_BACKUPS"
echo "  💾 Espaço total: $SPACE_SAVED"
echo "=================================================="