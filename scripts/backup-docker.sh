#!/bin/bash

###############################################################################
# SAGA - Script de Backup do Banco de Dados PostgreSQL (Docker)
# Autor: Sistema SAGA
# Data: $(date +%Y-%m-%d)
# Descrição: Script para realizar backups automáticos via Docker
###############################################################################

set -e

# ==============================================================================
# CONFIGURAÇÕES
# ==============================================================================

BACKUP_DIR="/home/sonnote/Documents/saga/backups"
DATE_STAMP=$(date +%Y%m%d_%H%M%S)
DATE_ONLY=$(date +%Y%m%d)

# Configurações Docker
DOCKER_CONTAINER="saga_db"
DB_NAME="saga"
DB_USER="saga_user"
DB_PASSWORD="saga_password"

# Retenção de backups (dias)
RETENTION_DAYS=30

# ==============================================================================
# CORES
# ==============================================================================
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

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

create_backup_dir() {
    if [ ! -d "$BACKUP_DIR" ]; then
        mkdir -p "$BACKUP_DIR"
        log_info "Diretório de backups criado: $BACKUP_DIR"
    fi
}

# Verificar se container está rodando
check_docker() {
    if ! docker ps | grep -q "$DOCKER_CONTAINER"; then
        log_error "Container $DOCKER_CONTAINER não está rodando!"
        exit 1
    fi
    log_info "Container $DOCKER_CONTAINER está ativo"
}

# Backup completo
backup_full() {
    local backup_file="${BACKUP_DIR}/saga_${DB_NAME}_full_${DATE_STAMP}.sql.gz"
    
    log_info "Iniciando backup completo..."
    
    docker exec $DOCKER_CONTAINER pg_dump \
        -U $DB_USER \
        -d $DB_NAME \
        --no-owner \
        --no-privileges \
        | gzip > "$backup_file"
    
    local file_size=$(du -h "$backup_file" | cut -f1)
    log_success "Backup completo criado: $(basename "$backup_file") ($file_size)"
    
    echo "$backup_file"
}

# Backup apenas dados
backup_data_only() {
    local backup_file="${BACKUP_DIR}/saga_${DB_NAME}_data_${DATE_STAMP}.sql.gz"
    
    log_info "Iniciando backup de dados..."
    
    docker exec $DOCKER_CONTAINER pg_dump \
        -U $DB_USER \
        -d $DB_NAME \
        --data-only \
        --no-owner \
        --no-privileges \
        | gzip > "$backup_file"
    
    local file_size=$(du -h "$backup_file" | cut -f1)
    log_success "Backup de dados criado: $(basename "$backup_file") ($file_size)"
    
    echo "$backup_file"
}

# Backup apenas schema
backup_schema_only() {
    local backup_file="${BACKUP_DIR}/saga_${DB_NAME}_schema_${DATE_STAMP}.sql.gz"
    
    log_info "Iniciando backup de schema..."
    
    docker exec $DOCKER_CONTAINER pg_dump \
        -U $DB_USER \
        -d $DB_NAME \
        --schema-only \
        --no-owner \
        --no-privileges \
        | gzip > "$backup_file"
    
    local file_size=$(du -h "$backup_file" | cut -f1)
    log_success "Backup de schema criado: $(basename "$backup_file") ($file_size)"
    
    echo "$backup_file"
}

# Backup tabelas críticas
backup_critical_tables() {
    local backup_file="${BACKUP_DIR}/saga_${DB_NAME}_critical_${DATE_STAMP}.sql.gz"
    
    log_info "Iniciando backup de tabelas críticas..."
    
    docker exec $DOCKER_CONTAINER pg_dump \
        -U $DB_USER \
        -d $DB_NAME \
        --table=users \
        --table=bookings \
        --table=ranks \
        --table=organizations \
        --no-owner \
        --no-privileges \
        | gzip > "$backup_file"
    
    local file_size=$(du -h "$backup_file" | cut -f1)
    log_success "Backup de tabelas críticas criado: $(basename "$backup_file") ($file_size)"
    
    echo "$backup_file"
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

# Gerar relatório
generate_report() {
    local report_file="${BACKUP_DIR}/backup_report_${DATE_STAMP}.txt"
    
    {
        echo "=========================================="
        echo "SAGA - Relatório de Backup"
        echo "=========================================="
        echo "Data/Hora: $(date '+%Y-%m-%d %H:%M:%S')"
        echo "Banco de Dados: $DB_NAME"
        echo "Container: $DOCKER_CONTAINER"
        echo ""
        echo "Backups Criados:"
        echo "------------------------------------------"
        ls -lh "$BACKUP_DIR"/*_${DATE_STAMP}.sql.gz 2>/dev/null || echo "Nenhum backup criado"
        echo ""
        echo "Espaço em Disco:"
        echo "------------------------------------------"
        df -h "$BACKUP_DIR" | tail -1
        echo ""
        echo "Total de Backups:"
        echo "------------------------------------------"
        echo "Arquivos: $(find "$BACKUP_DIR" -name "*.gz" -type f | wc -l)"
        echo "Tamanho total: $(du -sh "$BACKUP_DIR" | cut -f1)"
        echo ""
        echo "=========================================="
    } > "$report_file"
    
    log_success "Relatório gerado: $report_file"
    cat "$report_file"
}

# Testar backup
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
# MENU
# ==============================================================================

show_menu() {
    echo ""
    echo "=========================================="
    echo "SAGA - Sistema de Backup (Docker)"
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
# MAIN
# ==============================================================================

main() {
    create_backup_dir
    check_docker
    
    # Modo automatizado
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

main "$@"
