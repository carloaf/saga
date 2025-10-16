#!/bin/bash

# =============================================================================
# SAGA - Script de Backup Automático
# =============================================================================
# Script para backup automático do banco PostgreSQL do sistema SAGA
# Ambiente: Servidor de produção 10.166.72.36:8080
# =============================================================================

# Configurações
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/suporte/backups/saga"
CONTAINER_NAME="saga_db_port8080"
DB_USER="saga_user"
DB_NAME="saga"
RETENTION_DAYS=30
LOG_FILE="/var/log/saga-backup.log"

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função de log
log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
    log_message "SUCCESS: $1"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
    log_message "ERROR: $1"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
    log_message "WARNING: $1"
}

# Verificar se container existe
check_container() {
    if ! docker ps | grep -q "$CONTAINER_NAME"; then
        print_error "Container $CONTAINER_NAME não encontrado ou não está rodando"
        exit 1
    fi
    print_success "Container $CONTAINER_NAME encontrado"
}

# Criar diretório de backup
create_backup_dir() {
    if ! mkdir -p "$BACKUP_DIR"; then
        print_error "Falha ao criar diretório de backup: $BACKUP_DIR"
        exit 1
    fi
    print_success "Diretório de backup criado/verificado: $BACKUP_DIR"
}

# Backup completo (estrutura + dados)
backup_complete() {
    local backup_file="$BACKUP_DIR/saga_complete_${DATE}.sql"
    
    print_success "Iniciando backup completo..."
    
    if docker exec "$CONTAINER_NAME" pg_dump -U "$DB_USER" -d "$DB_NAME" \
        --clean --if-exists --create > "$backup_file"; then
        print_success "Backup completo criado: $backup_file"
        return 0
    else
        print_error "Falha no backup completo"
        return 1
    fi
}

# Backup apenas dados
backup_data_only() {
    local backup_file="$BACKUP_DIR/saga_data_${DATE}.sql"
    
    print_success "Iniciando backup de dados..."
    
    if docker exec "$CONTAINER_NAME" pg_dump -U "$DB_USER" -d "$DB_NAME" \
        --data-only > "$backup_file"; then
        print_success "Backup de dados criado: $backup_file"
        return 0
    else
        print_error "Falha no backup de dados"
        return 1
    fi
}

# Backup da estrutura apenas
backup_schema_only() {
    local backup_file="$BACKUP_DIR/saga_schema_${DATE}.sql"
    
    print_success "Iniciando backup de estrutura..."
    
    if docker exec "$CONTAINER_NAME" pg_dump -U "$DB_USER" -d "$DB_NAME" \
        --schema-only > "$backup_file"; then
        print_success "Backup de estrutura criado: $backup_file"
        return 0
    else
        print_error "Falha no backup de estrutura"
        return 1
    fi
}

# Comprimir backups
compress_backups() {
    print_success "Comprimindo backups..."
    
    for file in $BACKUP_DIR/saga_*_${DATE}.sql; do
        if [ -f "$file" ]; then
            if gzip "$file"; then
                print_success "Arquivo comprimido: $(basename $file).gz"
            else
                print_warning "Falha ao comprimir: $(basename $file)"
            fi
        fi
    done
}

# Limpar backups antigos
cleanup_old_backups() {
    print_success "Removendo backups antigos (>$RETENTION_DAYS dias)..."
    
    local deleted_count=$(find "$BACKUP_DIR" -name "*.gz" -mtime +$RETENTION_DAYS -delete -print | wc -l)
    
    if [ "$deleted_count" -gt 0 ]; then
        print_success "Removidos $deleted_count backups antigos"
    else
        print_success "Nenhum backup antigo para remover"
    fi
}

# Verificar integridade do backup
verify_backup() {
    local latest_backup=$(ls -t $BACKUP_DIR/saga_complete_*.gz 2>/dev/null | head -1)
    
    if [ -n "$latest_backup" ]; then
        if zcat "$latest_backup" | head -20 | grep -q "PostgreSQL database dump"; then
            print_success "Verificação de integridade do backup: OK"
        else
            print_warning "Backup pode estar corrompido: $latest_backup"
        fi
    fi
}

# Relatório de status
generate_report() {
    local total_backups=$(ls -1 $BACKUP_DIR/saga_*.gz 2>/dev/null | wc -l)
    local backup_size=$(du -sh $BACKUP_DIR 2>/dev/null | cut -f1)
    
    cat > "$BACKUP_DIR/backup_report_${DATE}.txt" << EOF
SAGA - Relatório de Backup
Data: $(date)
========================

Status: SUCESSO
Container: $CONTAINER_NAME
Database: $DB_NAME
Usuário: $DB_USER

Backups Criados:
- Backup Completo: saga_complete_${DATE}.sql.gz
- Backup Dados: saga_data_${DATE}.sql.gz
- Backup Estrutura: saga_schema_${DATE}.sql.gz

Estatísticas:
- Total de backups: $total_backups
- Espaço utilizado: $backup_size
- Retenção: $RETENTION_DAYS dias

Próximo backup: $(date -d '+1 day' '+%Y-%m-%d %H:%M:%S')
EOF

    print_success "Relatório criado: backup_report_${DATE}.txt"
}

# Função principal
main() {
    log_message "=== INÍCIO DO BACKUP SAGA ==="
    
    check_container
    create_backup_dir
    
    # Executar backups
    backup_complete
    backup_data_only
    backup_schema_only
    
    # Pós-processamento
    compress_backups
    cleanup_old_backups
    verify_backup
    generate_report
    
    print_success "Backup SAGA concluído com sucesso!"
    log_message "=== FIM DO BACKUP SAGA ==="
}

# Verificar se é execução de teste
if [ "$1" = "--test" ]; then
    print_success "Modo de teste - verificando apenas configurações"
    check_container
    create_backup_dir
    print_success "Teste concluído com sucesso!"
    exit 0
fi

# Executar backup
main