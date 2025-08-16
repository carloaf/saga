#!/bin/bash
# scripts/database/examples.sh
# Exemplos de uso dos scripts de backup e restore

# Cores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}📚 SAGA - Exemplos de Uso dos Scripts de Database${NC}"
echo "=================================================="

echo -e "${YELLOW}🗄️ BACKUP${NC}"
echo "# Backup completo de todos os ambientes disponíveis:"
echo "./scripts/database/backup.sh"
echo ""

echo -e "${YELLOW}🔄 RESTORE${NC}"
echo "# Restore completo no desenvolvimento:"
echo "./scripts/database/restore.sh dev backups/saga_dev_complete_20250815_120000.sql.gz"
echo ""
echo "# Restore apenas dados no staging:"
echo "./scripts/database/restore.sh staging backups/saga_staging_data_20250815_120000.sql.gz data"
echo ""
echo "# Restore apenas usuários:"
echo "./scripts/database/restore.sh dev backups/saga_dev_users_20250815_120000.sql.gz users"
echo ""

echo -e "${YELLOW}🌱 SETUP (Novo Ambiente)${NC}"
echo "# Inicializar ambiente de desenvolvimento:"
echo "./scripts/database/setup.sh dev"
echo ""
echo "# Inicializar ambiente de staging:"
echo "./scripts/database/setup.sh staging"
echo ""

echo -e "${YELLOW}🔧 COMANDOS MANUAIS${NC}"
echo "# Backup manual rápido:"
echo "docker exec saga_db pg_dump -U saga_user saga > backup_manual.sql"
echo ""
echo "# Restore manual:"
echo "docker exec -i saga_db psql -U saga_user saga < backup_manual.sql"
echo ""
echo "# Verificar dados:"
echo 'docker exec saga_db psql -U saga_user saga -c "SELECT COUNT(*) FROM users;"'
echo ""

echo -e "${YELLOW}⚙️ AUTOMAÇÃO${NC}"
echo "# Adicionar backup automático no crontab:"
echo "crontab -e"
echo "# Adicionar linha:"
echo "0 2 * * * /home/saga/scripts/database/backup.sh >> /var/log/saga-backup.log 2>&1"
echo ""

echo -e "${GREEN}✅ Para mais detalhes, consulte: docs/BACKUP_RESTORE.md${NC}"
