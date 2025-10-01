#!/bin/bash

###############################################################################
# SAGA - Script de Backup do Banco de Dados PostgreSQL
# Autor: Sistema SAGA
# Data: $(date +%Y-%m-%d)
# Descrição: Script para realizar backups automáticos do banco de dados
###############################################################################

set -e  # Parar em caso de erro

# ==============================================================================
# CONFIGURAÇÕES
# ==============================================================================

# Diretório de backups
BACKUP_DIR="/home/sonnote/Documents/saga/backups"
DATE_STAMP=$(date +%Y%m%d_%H%M%S)
DATE_ONLY=$(date +%Y%m%d)

# Configurações do banco
DB_HOST="localhost"
DB_PORT="5432"
DB_NAME="saga_dev"
DB_USER="saga_user"
DB_PASSWORD="saga_password"

# Retenção de backups (dias)
RETENTION_DAYS=30

# ==============================================================================
# CORES PARA OUTPUT
# ==============================================================================
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# ==============================================================================
# FUNÇÕES
# ==============================================================================

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Criar diretório de backups se não existir
create_backup_dir() {
    if [ ! -d "$BACKUP_DIR" ]; then
        mkdir -p "$BACKUP_DIR"
        log_info "Diretório de backups criado: $BACKUP_DIR"
    fi
}

# Backup completo (schema + dados)
backup_full() {
    local backup_file="${BACKUP_DIR}/saga_${DB_NAME}_full_${DATE_STAMP}.sql"
    local backup_file_gz="${backup_file}.gz"
    
    log_info "Iniciando backup completo..."
    
    PGPASSWORD=$DB_PASSWORD pg_dump \
        -h $DB_HOST \
        -p $DB_PORT \
        -U $DB_USER \
        -d $DB_NAME \
        --verbose \
        --format=plain \
        --no-owner \
        --no-privileges \
        -f "$backup_file" 2>&1 | grep -v "NOTICE"
    
    # Comprimir backup
    gzip "$backup_file"
    
    local file_size=$(du -h "$backup_file_gz" | cut -f1)
    log_success "Backup completo criado: $backup_file_gz ($file_size)"
    
    echo "$backup_file_gz"
}

# Backup apenas dos dados (sem schema)
backup_data_only() {
    local backup_file="${BACKUP_DIR}/saga_${DB_NAME}_data_${DATE_STAMP}.sql"
    local backup_file_gz="${backup_file}.gz"
    
    log_info "Iniciando backup de dados..."
    
    PGPASSWORD=$DB_PASSWORD pg_dump \
        -h $DB_HOST \
        -p $DB_PORT \
        -U $DB_USER \
        -d $DB_NAME \
        --verbose \
        --data-only \
        --no-owner \
        --no-privileges \
        -f "$backup_file" 2>&1 | grep -v "NOTICE"
    
    gzip "$backup_file"
    
    local file_size=$(du -h "$backup_file_gz" | cut -f1)
    log_success "Backup de dados criado: $backup_file_gz ($file_size)"
    
    echo "$backup_file_gz"
}

# Backup apenas do schema (estrutura)
backup_schema_only() {
    local backup_file="${BACKUP_DIR}/saga_${DB_NAME}_schema_${DATE_STAMP}.sql"
    local backup_file_gz="${backup_file}.gz"
    
    log_info "Iniciando backup de schema..."
    
    PGPASSWORD=$DB_PASSWORD pg_dump \
        -h $DB_HOST \
        -p $DB_PORT \
        -U $DB_USER \
        -d $DB_NAME \
        --verbose \
        --schema-only \
        --no-owner \
        --no-privileges \
        -f "$backup_file" 2>&1 | grep -v "NOTICE"
    
    gzip "$backup_file"
    
    local file_size=$(du -h "$backup_file_gz" | cut -f1)
    log_success "Backup de schema criado: $backup_file_gz ($file_size)"
    
    echo "$backup_file_gz"
}

# Backup de tabelas específicas (usuários e reservas)
backup_critical_tables() {
    local backup_file="${BACKUP_DIR}/saga_${DB_NAME}_critical_${DATE_STAMP}.sql"
    local backup_file_gz="${backup_file}.gz"
    
    log_info "Iniciando backup de tabelas críticas..."
    
    PGPASSWORD=$DB_PASSWORD pg_dump \
        -h $DB_HOST \
        -p $DB_PORT \
        -U $DB_USER \
        -d $DB_NAME \
        --verbose \
        --table=users \
        --table=bookings \
        --table=ranks \
        --table=organizations \
        --no-owner \
        --no-privileges \
        -f "$backup_file" 2>&1 | grep -v "NOTICE"
    
    gzip "$backup_file"
    
    local file_size=$(du -h "$backup_file_gz" | cut -f1)
    log_success "Backup de tabelas críticas criado: $backup_file_gz ($file_size)"
    
    echo "$backup_file_gz"
}

# Limpar backups antigos
cleanup_old_backups() {
    log_info "Limpando backups com mais de $RETENTION_DAYS dias..."
    
    local deleted_count=0
    while IFS= read -r old_backup; do
        rm -f "$old_backup"
        deleted_count=$((deleted_count + 1))
        log_warning "Removido: $(basename "$old_backup")"
    done < <(find "$BACKUP_DIR" -name "*.gz" -type f -mtime +$RETENTION_DAYS)
    
    if [ $deleted_count -eq 0 ]; then
        log_info "Nenhum backup antigo encontrado"
    else
        log_success "Removidos $deleted_count backup(s) antigo(s)"
    fi
}

# Gerar relatório de backup
generate_report() {
    local report_file="${BACKUP_DIR}/backup_report_${DATE_STAMP}.txt"
    
    {
        echo "=========================================="
        echo "SAGA - Relatório de Backup"
        echo "=========================================="
        echo "Data/Hora: $(date '+%Y-%m-%d %H:%M:%S')"
        echo "Banco de Dados: $DB_NAME"
        echo "Servidor: $DB_HOST:$DB_PORT"
        echo ""
        echo "Backups Criados:"
        echo "------------------------------------------"
        ls -lh "$BACKUP_DIR"/*_${DATE_STAMP}.sql.gz 2>/dev/null || echo "Nenhum backup criado"
        echo ""
        echo "Espaço em Disco:"
        echo "------------------------------------------"
        df -h "$BACKUP_DIR" | tail -1
        echo ""
        echo "Total de Backups no Diretório:"
        echo "------------------------------------------"
        echo "Arquivos: $(find "$BACKUP_DIR" -name "*.gz" -type f | wc -l)"
        echo "Tamanho total: $(du -sh "$BACKUP_DIR" | cut -f1)"
        echo ""
        echo "=========================================="
    } > "$report_file"
    
    log_success "Relatório gerado: $report_file"
    cat "$report_file"
}

# Testar backup (verificar integridade)
test_backup() {
    local backup_file=$1
    
    if [ ! -f "$backup_file" ]; then
        log_error "Arquivo de backup não encontrado: $backup_file"
        return 1
    fi
    
    log_info "Testando integridade do backup..."
    
    if gunzip -t "$backup_file" 2>/dev/null; then
        log_success "Backup íntegro: $(basename "$backup_file")"
        return 0
    else
        log_error "Backup corrompido: $(basename "$backup_file")"
        return 1
    fi
}

# ==============================================================================
# MENU PRINCIPAL
# ==============================================================================

show_menu() {
    echo ""
    echo "=========================================="
    echo "SAGA - Sistema de Backup PostgreSQL"
    echo "=========================================="
    echo "1. Backup Completo (Schema + Dados)"
    echo "2. Backup Apenas Dados"
    echo "3. Backup Apenas Schema"
    echo "4. Backup Tabelas Críticas"
    echo "5. Backup Completo com Limpeza"
    echo "6. Listar Backups Existentes"
    echo "7. Testar Último Backup"
    echo "0. Sair"
    echo "=========================================="
    echo -n "Escolha uma opção: "
}

# ==============================================================================
# EXECUÇÃO PRINCIPAL
# ==============================================================================

main() {
    create_backup_dir
    
    # Se foi passado argumento, executar modo automatizado
    if [ $# -gt 0 ]; then
        case $1 in
            full|--full)
                backup_full
                cleanup_old_backups
                generate_report
                ;;
            data|--data)
                backup_data_only
                ;;
            schema|--schema)
                backup_schema_only
                ;;
            critical|--critical)
                backup_critical_tables
                ;;
            *)
                log_error "Opção inválida: $1"
                echo "Uso: $0 [full|data|schema|critical]"
                exit 1
                ;;
        esac
        exit 0
    fi
    
    # Modo interativo
    while true; do
        show_menu
        read -r option
        
        case $option in
            1)
                backup_full
                generate_report
                ;;
            2)
                backup_data_only
                ;;
            3)
                backup_schema_only
                ;;
            4)
                backup_critical_tables
                ;;
            5)
                backup_full
                cleanup_old_backups
                generate_report
                ;;
            6)
                log_info "Backups existentes:"
                ls -lh "$BACKUP_DIR"/*.gz 2>/dev/null || echo "Nenhum backup encontrado"
                ;;
            7)
                last_backup=$(ls -t "$BACKUP_DIR"/*.gz 2>/dev/null | head -1)
                if [ -n "$last_backup" ]; then
                    test_backup "$last_backup"
                else
                    log_error "Nenhum backup encontrado"
                fi
                ;;
            0)
                log_info "Encerrando..."
                exit 0
                ;;
            *)
                log_error "Opção inválida!"
                ;;
        esac
        
        echo ""
        read -p "Pressione ENTER para continuar..."
    done
}

# Executar script
main "$@"
