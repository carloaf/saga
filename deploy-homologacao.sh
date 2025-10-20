#!/bin/bash

##############################################################################
# Script de Deploy - Feature Homologação de Usuários
# Servidor: 10.166.72.36:8080
# Data: 2025-10-19
##############################################################################

set -e  # Parar em caso de erro

echo "=============================================="
echo "  SAGA - Deploy Feature Homologação Status  "
echo "=============================================="
echo ""

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Função para imprimir com cores
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

# 1. Verificar se estamos no diretório correto
echo "1. Verificando diretório..."
if [ ! -f "docker-compose.yml" ]; then
    print_error "Arquivo docker-compose.yml não encontrado!"
    print_error "Execute este script no diretório raiz do projeto SAGA"
    exit 1
fi
print_success "Diretório correto"
echo ""

# 2. Fazer backup do banco de dados
echo "2. Fazendo backup do banco de dados..."
BACKUP_FILE="backups/backup_before_homologacao_$(date +%Y%m%d_%H%M%S).sql"
mkdir -p backups
docker-compose exec -T database pg_dump -U saga_user saga_db > "$BACKUP_FILE"
print_success "Backup salvo em: $BACKUP_FILE"
echo ""

# 3. Verificar status dos containers
echo "3. Verificando containers..."
if ! docker-compose ps | grep -q "Up"; then
    print_warning "Containers não estão rodando. Iniciando..."
    docker-compose up -d
    sleep 5
fi
print_success "Containers ativos"
echo ""

# 4. Git pull (já deve ter sido feito, mas confirma)
echo "4. Atualizando código..."
print_warning "Certifique-se de que já fez 'git pull' das alterações"
read -p "Pressione ENTER para continuar ou CTRL+C para cancelar..."
echo ""

# 5. Executar migration
echo "5. Executando migration..."
docker-compose exec app php artisan migrate --path=database/migrations/2025_10_19_212608_add_status_column_to_users_table.php --force
print_success "Migration executada"
echo ""

# 6. Verificar se a coluna foi criada
echo "6. Verificando estrutura do banco..."
COLUMN_EXISTS=$(docker-compose exec -T database psql -U saga_user -d saga_db -t -c "SELECT COUNT(*) FROM information_schema.columns WHERE table_name = 'users' AND column_name = 'status';")

if [ "$COLUMN_EXISTS" -gt 0 ]; then
    print_success "Coluna 'status' criada com sucesso"
else
    print_error "Coluna 'status' NÃO foi criada!"
    echo "Tentando criar manualmente..."
    docker-compose exec -T database psql -U saga_user -d saga_db -c "ALTER TABLE users ADD COLUMN status VARCHAR(255) DEFAULT 'H';"
    docker-compose exec -T database psql -U saga_user -d saga_db -c "ALTER TABLE users ADD CONSTRAINT users_status_check CHECK (status IN ('active', 'inactive', 'H'));"
    print_success "Coluna criada manualmente"
fi
echo ""

# 7. Atualizar usuários existentes
echo "7. Atualizando usuários existentes..."
docker-compose exec -T database psql -U saga_user -d saga_db -c "UPDATE users SET status = CASE WHEN is_active = true THEN 'active' ELSE 'inactive' END WHERE status = 'H';"
print_success "Usuários existentes atualizados"
echo ""

# 8. Limpar cache
echo "8. Limpando cache da aplicação..."
docker-compose exec app php artisan optimize:clear
print_success "Cache limpo"
echo ""

# 9. Reiniciar container da aplicação
echo "9. Reiniciando container da aplicação..."
docker-compose restart app
sleep 3
print_success "Container reiniciado"
echo ""

# 10. Verificação final
echo "10. Verificação final..."
echo ""
echo "Status das Migrations:"
docker-compose exec app php artisan migrate:status | grep "2025_10_19"
echo ""

echo "Usuários no sistema:"
docker-compose exec -T database psql -U saga_user -d saga_db -c "SELECT id, full_name, email, is_active, status FROM users ORDER BY id;"
echo ""

# 11. Testes de validação
echo "=============================================="
echo "  ✓ Deploy Concluído com Sucesso!           "
echo "=============================================="
echo ""
print_success "A feature de Homologação de Usuários está ativa!"
echo ""
echo "Próximos passos:"
echo "  1. Acesse: http://10.166.72.36:8080/admin/users"
echo "  2. Verifique o card 'Aguardando Homologação'"
echo "  3. Teste criar um novo usuário em /register"
echo "  4. Verifique que o login é bloqueado até aprovação"
echo "  5. Como admin, aprove o usuário mudando status para 'Ativo'"
echo ""
print_warning "Backup salvo em: $BACKUP_FILE"
echo ""

# Script de rollback
cat > backups/rollback_homologacao.sh << 'ROLLBACK_SCRIPT'
#!/bin/bash
# Script de Rollback - Feature Homologação

echo "⚠️  ROLLBACK - Revertendo Feature Homologação"
echo "Este script irá:"
echo "  1. Fazer rollback da migration"
echo "  2. Remover coluna 'status' da tabela users"
echo "  3. Restaurar backup (opcional)"
echo ""
read -p "Tem certeza? (sim/não): " confirm

if [ "$confirm" = "sim" ]; then
    docker-compose exec app php artisan migrate:rollback --step=1
    docker-compose exec app php artisan optimize:clear
    echo "✓ Rollback concluído"
    echo ""
    echo "Para restaurar backup completo:"
    echo "  cat backups/backup_before_homologacao_*.sql | docker-compose exec -T database psql -U saga_user saga_db"
else
    echo "Rollback cancelado"
fi
ROLLBACK_SCRIPT

chmod +x backups/rollback_homologacao.sh
print_success "Script de rollback criado: backups/rollback_homologacao.sh"
echo ""

echo "Deploy finalizado! 🚀"
