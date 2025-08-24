#!/bin/bash

# =============================================================================
# SAGA Environment Manager
# Facilita o gerenciamento dos ambientes DEV, STAGING e PRODUCTION
# =============================================================================

set -e

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configurações
PROJECT_NAME="SAGA"
ENVIRONMENTS=("dev" "staging" "production")

# Função para mostrar ajuda
show_help() {
    echo -e "${BLUE}🚀 $PROJECT_NAME Environment Manager${NC}"
    echo "==============================================="
    echo ""
    echo "USAGE:"
    echo "  $0 <environment> <command> [options]"
    echo ""
    echo "ENVIRONMENTS:"
    echo "  dev         - Desenvolvimento (porta 8000)"
    echo "  staging     - Homologação (porta 8080)"  
    echo "  production  - Produção (porta 80)"
    echo ""
    echo "COMMANDS:"
    echo "  start       - Iniciar ambiente"
    echo "  stop        - Parar ambiente"
    echo "  restart     - Reiniciar ambiente"
    echo "  build       - Build e iniciar ambiente"
    echo "  logs        - Mostrar logs"
    echo "  shell       - Acessar shell do container"
    echo "  migrate     - Executar migrations"
    echo "  backup      - Fazer backup do banco"
    echo "  status      - Status dos containers"
    echo "  clean       - Limpar containers e volumes"
    echo ""
    echo "EXAMPLES:"
    echo "  $0 dev start                    # Iniciar desenvolvimento"
    echo "  $0 staging build                # Build e iniciar staging"
    echo "  $0 production migrate           # Executar migrations em produção"
    echo "  $0 dev logs -f                  # Logs em tempo real"
    echo "  $0 staging shell                # Shell do staging"
    echo ""
}

# Função para log colorido
log() {
    local level=$1
    local message=$2
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    
    case $level in
        "INFO")  echo -e "${GREEN}[INFO]${NC}  [$timestamp] $message" ;;
        "WARN")  echo -e "${YELLOW}[WARN]${NC}  [$timestamp] $message" ;;
        "ERROR") echo -e "${RED}[ERROR]${NC} [$timestamp] $message" ;;
        "DEBUG") echo -e "${BLUE}[DEBUG]${NC} [$timestamp] $message" ;;
    esac
}

# Função para validar ambiente
validate_environment() {
    local env=$1
    if [[ ! " ${ENVIRONMENTS[@]} " =~ " ${env} " ]]; then
        log "ERROR" "Ambiente inválido: $env"
        log "INFO" "Ambientes disponíveis: ${ENVIRONMENTS[*]}"
        exit 1
    fi
}

# Função para obter configuração do ambiente
get_env_config() {
    local env=$1
    
    case $env in
        "dev")
            COMPOSE_FILE="docker-compose.yml"
            COMPOSE_DIR="."
            APP_CONTAINER="saga_app_dev"
            DB_CONTAINER="saga_db"
            ;;
        "staging")
            COMPOSE_FILE="docker-compose.staging.yml"
            COMPOSE_DIR="deploy/staging"
            APP_CONTAINER="saga_app_staging"
            DB_CONTAINER="saga_db_staging"
            ;;
        "production")
            COMPOSE_FILE="docker-compose.prod.yml"
            COMPOSE_DIR="deploy/production"
            APP_CONTAINER="saga_app_prod"
            DB_CONTAINER="saga_db_prod"
            ;;
    esac
}

# Função para executar comando docker-compose
run_compose() {
    local env=$1
    shift
    local cmd="$@"
    
    get_env_config $env
    
    log "DEBUG" "Executando: docker-compose -f $COMPOSE_DIR/$COMPOSE_FILE $cmd"
    
    cd $COMPOSE_DIR
    docker-compose -f $COMPOSE_FILE $cmd
    cd - > /dev/null
}

# Comando: start
cmd_start() {
    local env=$1
    log "INFO" "Iniciando ambiente $env..."
    run_compose $env up -d
    log "INFO" "Ambiente $env iniciado com sucesso!"
    cmd_status $env
}

# Comando: stop
cmd_stop() {
    local env=$1
    log "INFO" "Parando ambiente $env..."
    run_compose $env down
    log "INFO" "Ambiente $env parado com sucesso!"
}

# Comando: restart
cmd_restart() {
    local env=$1
    log "INFO" "Reiniciando ambiente $env..."
    cmd_stop $env
    sleep 2
    cmd_start $env
}

# Comando: build
cmd_build() {
    local env=$1
    log "INFO" "Building e iniciando ambiente $env..."
    run_compose $env down
    run_compose $env up -d --build
    log "INFO" "Build do ambiente $env concluído!"
    cmd_status $env
}

# Comando: logs
cmd_logs() {
    local env=$1
    shift
    local options="$@"
    
    get_env_config $env
    log "INFO" "Mostrando logs do ambiente $env..."
    
    cd $COMPOSE_DIR
    docker-compose -f $COMPOSE_FILE logs $options
    cd - > /dev/null
}

# Comando: shell
cmd_shell() {
    local env=$1
    get_env_config $env
    
    log "INFO" "Acessando shell do container $APP_CONTAINER..."
    docker exec -it $APP_CONTAINER bash
}

# Comando: migrate
cmd_migrate() {
    local env=$1
    get_env_config $env
    
    log "INFO" "Executando migrations no ambiente $env..."
    
    if [ "$env" = "production" ]; then
        log "WARN" "ATENÇÃO: Executando migrations em PRODUÇÃO!"
        read -p "Tem certeza? (yes/no): " confirm
        if [ "$confirm" != "yes" ]; then
            log "INFO" "Operação cancelada."
            exit 0
        fi
        docker exec $APP_CONTAINER php artisan migrate --force
    else
        docker exec $APP_CONTAINER php artisan migrate
    fi
    
    log "INFO" "Migrations executadas com sucesso!"
}

# Comando: backup
cmd_backup() {
    local env=$1
    get_env_config $env
    
    log "INFO" "Fazendo backup do ambiente $env..."
    
    # Verificar se o script de backup existe
    if [ -f "scripts/database/backup.sh" ]; then
        bash scripts/database/backup.sh
    else
        # Backup simples via artisan
        docker exec $APP_CONTAINER php artisan db:backup
    fi
    
    log "INFO" "Backup concluído!"
}

# Comando: status
cmd_status() {
    local env=$1
    get_env_config $env
    
    log "INFO" "Status do ambiente $env:"
    echo ""
    
    # Status dos containers
    echo "📦 CONTAINERS:"
    docker ps --filter "name=saga_.*_$env" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
    echo ""
    
    # Uso de recursos
    echo "💾 RECURSOS:"
    docker stats --no-stream --format "table {{.Name}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.NetIO}}" $(docker ps --filter "name=saga_.*_$env" -q) 2>/dev/null || echo "Nenhum container ativo"
    echo ""
    
    # Health check
    echo "🔍 HEALTH CHECK:"
    case $env in
        "dev") curl -f http://localhost:8000 >/dev/null 2>&1 && echo "✅ DEV: http://localhost:8000 - OK" || echo "❌ DEV: http://localhost:8000 - ERRO" ;;
        "staging") curl -f http://localhost:8080 >/dev/null 2>&1 && echo "✅ STAGING: http://localhost:8080 - OK" || echo "❌ STAGING: http://localhost:8080 - ERRO" ;;
        "production") curl -f http://localhost >/dev/null 2>&1 && echo "✅ PRODUCTION: http://localhost - OK" || echo "❌ PRODUCTION: http://localhost - ERRO" ;;
    esac
}

# Comando: clean
cmd_clean() {
    local env=$1
    get_env_config $env
    
    log "WARN" "ATENÇÃO: Isso irá remover containers e volumes do ambiente $env!"
    read -p "Tem certeza? (yes/no): " confirm
    if [ "$confirm" != "yes" ]; then
        log "INFO" "Operação cancelada."
        exit 0
    fi
    
    log "INFO" "Limpando ambiente $env..."
    run_compose $env down -v --remove-orphans
    docker system prune -f
    log "INFO" "Limpeza concluída!"
}

# Script principal
main() {
    if [ $# -lt 2 ]; then
        show_help
        exit 1
    fi
    
    local env=$1
    local command=$2
    shift 2
    local options="$@"
    
    validate_environment $env
    
    case $command in
        "start")     cmd_start $env ;;
        "stop")      cmd_stop $env ;;
        "restart")   cmd_restart $env ;;
        "build")     cmd_build $env ;;
        "logs")      cmd_logs $env $options ;;
        "shell")     cmd_shell $env ;;
        "migrate")   cmd_migrate $env ;;
        "backup")    cmd_backup $env ;;
        "status")    cmd_status $env ;;
        "clean")     cmd_clean $env ;;
        *)
            log "ERROR" "Comando inválido: $command"
            show_help
            exit 1
            ;;
    esac
}

# Verificar se é uma chamada de ajuda
if [ $# -eq 0 ] || [ "$1" = "-h" ] || [ "$1" = "--help" ]; then
    show_help
    exit 0
fi

# Executar script principal
main "$@"
