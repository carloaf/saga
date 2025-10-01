#!/bin/bash

###############################################################################
# SAGA - Script de Restauração do Banco de Dados PostgreSQL
# Autor: Sistema SAGA
# Data: $(date +%Y-%m-%d)
# Descrição: Script para restaurar backups do banco de dados
###############################################################################

set -e  # Parar em caso de erro

# ==============================================================================
# CONFIGURAÇÕES
# ==============================================================================

BACKUP_DIR="/home/sonnote/Documents/saga/backups"

# Configurações do banco
DB_HOST="localhost"
DB_PORT="5432"
DB_NAME="saga_dev"
DB_USER="saga_user"
DB_PASSWORD="saga_password"

# ==============================================================================
# CORES PARA OUTPUT
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

# Listar backups disponíveis
list_backups() {
    log_info "Backups disponíveis em $BACKUP_DIR:"
    echo ""
    
    local count=1
    while IFS= read -r backup; do
        local size=$(du -h "$backup" | cut -f1)
        local date=$(stat -c %y "$backup" | cut -d' ' -f1,2 | cut -d'.' -f1)
        echo "$count. $(basename "$backup") - $size - $date"
        count=$((count + 1))
    done < <(ls -t "$BACKUP_DIR"/*.sql.gz 2>/dev/null)
    
    if [ $count -eq 1 ]; then
        log_error "Nenhum backup encontrado!"
        return 1
    fi
    
    return 0
}

# Restaurar backup
restore_backup() {
    local backup_file=$1
    
    if [ ! -f "$backup_file" ]; then
        log_error "Arquivo de backup não encontrado: $backup_file"
        return 1
    fi
    
    log_warning "⚠️  ATENÇÃO: Esta operação irá SOBRESCREVER o banco de dados atual!"
    log_warning "Banco: $DB_NAME em $DB_HOST:$DB_PORT"
    echo ""
    read -p "Deseja continuar? (sim/não): " confirm
    
    if [ "$confirm" != "sim" ]; then
        log_info "Restauração cancelada pelo usuário"
        return 1
    fi
    
    log_info "Iniciando restauração do backup..."
    log_info "Arquivo: $(basename "$backup_file")"
    
    # Criar backup de segurança antes de restaurar
    log_info "Criando backup de segurança do banco atual..."
    local safety_backup="${BACKUP_DIR}/safety_backup_$(date +%Y%m%d_%H%M%S).sql.gz"
    PGPASSWORD=$DB_PASSWORD pg_dump \
        -h $DB_HOST \
        -p $DB_PORT \
        -U $DB_USER \
        -d $DB_NAME \
        --no-owner \
        --no-privileges \
        | gzip > "$safety_backup"
    log_success "Backup de segurança criado: $(basename "$safety_backup")"
    
    # Descompactar e restaurar
    log_info "Descompactando backup..."
    local temp_file="/tmp/saga_restore_$(date +%Y%m%d_%H%M%S).sql"
    gunzip -c "$backup_file" > "$temp_file"
    
    log_info "Restaurando banco de dados..."
    PGPASSWORD=$DB_PASSWORD psql \
        -h $DB_HOST \
        -p $DB_PORT \
        -U $DB_USER \
        -d $DB_NAME \
        -f "$temp_file" 2>&1 | grep -v "NOTICE" || true
    
    # Limpar arquivo temporário
    rm -f "$temp_file"
    
    log_success "Banco de dados restaurado com sucesso!"
    log_info "Backup de segurança mantido em: $(basename "$safety_backup")"
}

# Restaurar interativamente
restore_interactive() {
    if ! list_backups; then
        return 1
    fi
    
    echo ""
    read -p "Digite o número do backup para restaurar (0 para cancelar): " selection
    
    if [ "$selection" = "0" ]; then
        log_info "Operação cancelada"
        return 0
    fi
    
    local backup_file=$(ls -t "$BACKUP_DIR"/*.sql.gz 2>/dev/null | sed -n "${selection}p")
    
    if [ -z "$backup_file" ]; then
        log_error "Seleção inválida!"
        return 1
    fi
    
    restore_backup "$backup_file"
}

# Verificar integridade de todos os backups
verify_all_backups() {
    log_info "Verificando integridade de todos os backups..."
    echo ""
    
    local total=0
    local ok=0
    local failed=0
    
    while IFS= read -r backup; do
        total=$((total + 1))
        echo -n "Verificando $(basename "$backup")... "
        
        if gunzip -t "$backup" 2>/dev/null; then
            echo -e "${GREEN}OK${NC}"
            ok=$((ok + 1))
        else
            echo -e "${RED}FALHOU${NC}"
            failed=$((failed + 1))
        fi
    done < <(find "$BACKUP_DIR" -name "*.sql.gz" -type f)
    
    echo ""
    log_info "Total de backups: $total"
    log_success "Íntegros: $ok"
    if [ $failed -gt 0 ]; then
        log_error "Com problemas: $failed"
    fi
}

# ==============================================================================
# MENU PRINCIPAL
# ==============================================================================

show_menu() {
    echo ""
    echo "=========================================="
    echo "SAGA - Restauração de Backup"
    echo "=========================================="
    echo "1. Listar Backups Disponíveis"
    echo "2. Restaurar Backup (Interativo)"
    echo "3. Verificar Integridade dos Backups"
    echo "4. Restaurar Último Backup"
    echo "0. Sair"
    echo "=========================================="
    echo -n "Escolha uma opção: "
}

# ==============================================================================
# EXECUÇÃO PRINCIPAL
# ==============================================================================

main() {
    if [ ! -d "$BACKUP_DIR" ]; then
        log_error "Diretório de backups não encontrado: $BACKUP_DIR"
        exit 1
    fi
    
    # Modo com argumento
    if [ $# -gt 0 ]; then
        case $1 in
            list|--list)
                list_backups
                ;;
            last|--last)
                last_backup=$(ls -t "$BACKUP_DIR"/*.sql.gz 2>/dev/null | head -1)
                if [ -n "$last_backup" ]; then
                    restore_backup "$last_backup"
                else
                    log_error "Nenhum backup encontrado"
                    exit 1
                fi
                ;;
            verify|--verify)
                verify_all_backups
                ;;
            *)
                if [ -f "$1" ]; then
                    restore_backup "$1"
                else
                    log_error "Arquivo não encontrado: $1"
                    exit 1
                fi
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
                list_backups
                ;;
            2)
                restore_interactive
                ;;
            3)
                verify_all_backups
                ;;
            4)
                last_backup=$(ls -t "$BACKUP_DIR"/*.sql.gz 2>/dev/null | head -1)
                if [ -n "$last_backup" ]; then
                    log_info "Último backup: $(basename "$last_backup")"
                    restore_backup "$last_backup"
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
