#!/bin/bash

# Script de Sincronização SAGA - Branches e Deploy
# Sistema de Agendamento e Gestão de Arranchamento

set -e  # Para se algum comando falhar

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Função para log colorido
log_info() { echo -e "${BLUE}ℹ️  $1${NC}"; }
log_success() { echo -e "${GREEN}✅ $1${NC}"; }
log_warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }
log_error() { echo -e "${RED}❌ $1${NC}"; }

# Função para verificar se há mudanças não commitadas
check_uncommitted_changes() {
    if [[ -n $(git status --porcelain) ]]; then
        log_warning "Há mudanças não commitadas!"
        echo ""
        git status --short
        echo ""
        read -p "Deseja fazer commit dessas mudanças? (y/n): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            read -p "📝 Digite a mensagem do commit: " commit_message
            if [[ -n "$commit_message" ]]; then
                git add .
                git commit -m "$commit_message"
                log_success "Commit realizado: $commit_message"
            else
                log_error "Mensagem de commit não pode estar vazia!"
                exit 1
            fi
        else
            log_error "Sincronização cancelada. Faça commit das mudanças primeiro."
            exit 1
        fi
    fi
}

# Função para sincronizar branches
sync_branches() {
    local current_branch=$(git branch --show-current)
    
    log_info "Branch atual: $current_branch"
    
    # Se estiver na dev, fazer merge para main
    if [ "$current_branch" = "dev" ]; then
        log_info "Sincronizando dev → main..."
        
        # Verificar se dev está atualizada com remoto
        git fetch origin dev
        if [[ $(git rev-parse HEAD) != $(git rev-parse origin/dev) ]]; then
            log_info "Fazendo pull da branch dev..."
            git pull origin dev
        fi
        
        # Mudar para main e fazer merge
        git checkout main
        log_info "Fazendo merge da dev para main..."
        git merge dev --no-ff -m "merge: Sincronização dev → main"
        
    # Se estiver na main, sincronizar com dev
    elif [ "$current_branch" = "main" ]; then
        log_info "Sincronizando main → dev..."
        
        # Verificar se main está atualizada com remoto
        git fetch origin main
        if [[ $(git rev-parse HEAD) != $(git rev-parse origin/main) ]]; then
            log_info "Fazendo pull da branch main..."
            git pull origin main
        fi
        
        # Mudar para dev e fazer merge
        git checkout dev
        log_info "Fazendo merge da main para dev..."
        git merge main --ff-only
        
        # Voltar para main
        git checkout main
        
    else
        log_error "Branch atual ($current_branch) não é dev nem main!"
        log_info "Mudando para branch main..."
        git checkout main
    fi
}

# Função para fazer push das duas branches
push_branches() {
    log_info "Enviando branches para servidor remoto..."
    
    # Push da main
    log_info "📤 Enviando branch main..."
    git push origin main
    log_success "Branch main enviada com sucesso!"
    
    # Push da dev
    log_info "📤 Enviando branch dev..."
    git checkout dev
    git push origin dev
    log_success "Branch dev enviada com sucesso!"
    
    # Voltar para main
    git checkout main
}

# Função para mostrar status final
show_final_status() {
    echo ""
    log_success "🎉 Sincronização concluída com sucesso!"
    echo ""
    echo "📊 Status final das branches:"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    
    # Status da main
    git checkout main > /dev/null 2>&1
    local main_commit=$(git log --oneline -1)
    echo "🚀 main:  $main_commit"
    
    # Status da dev
    git checkout dev > /dev/null 2>&1
    local dev_commit=$(git log --oneline -1)
    echo "🛠️  dev:   $dev_commit"
    
    git checkout main > /dev/null 2>&1
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    
    # Verificar se branches estão sincronizadas
    if [ "$main_commit" = "$dev_commit" ]; then
        log_success "Branches estão perfeitamente sincronizadas!"
    else
        log_warning "Branches têm commits diferentes (isso pode ser normal)"
    fi
    
    echo ""
    log_info "URLs do repositório:"
    echo "  🌐 GitHub: https://github.com/carloaf/saga"
    echo "  🔗 Clone:  git clone https://github.com/carloaf/saga.git"
    echo ""
    log_info "Próximos passos:"
    echo "  • Para desenvolvimento: git checkout dev"
    echo "  • Para deploy: ./deploy.sh"
    echo "  • Para debug: ./debug-login.sh"
}

# Função principal
main() {
    echo "🔄 SAGA - Script de Sincronização de Branches"
    echo "=============================================="
    echo ""
    
    # Verificar se estamos em um repositório git
    if ! git rev-parse --git-dir > /dev/null 2>&1; then
        log_error "Este não é um repositório Git!"
        exit 1
    fi
    
    # Configurar pull strategy se não estiver configurada
    if ! git config pull.rebase > /dev/null 2>&1; then
        log_info "Configurando estratégia de pull..."
        git config pull.rebase false
    fi
    
    # Verificar conectividade com remoto
    log_info "Verificando conectividade com repositório remoto..."
    if ! git ls-remote origin > /dev/null 2>&1; then
        log_error "Não foi possível conectar ao repositório remoto!"
        exit 1
    fi
    
    # Verificar mudanças não commitadas
    check_uncommitted_changes
    
    # Fazer fetch de todas as branches
    log_info "Atualizando referências remotas..."
    git fetch origin
    
    # Sincronizar branches
    sync_branches
    
    # Fazer push das branches
    push_branches
    
    # Mostrar status final
    show_final_status
}

# Verificar se o script foi chamado com parâmetros
if [ "$1" = "--help" ] || [ "$1" = "-h" ]; then
    echo "📖 SAGA - Script de Sincronização de Branches"
    echo ""
    echo "Este script sincroniza automaticamente as branches dev e main,"
    echo "fazendo merge entre elas e enviando para o repositório remoto."
    echo ""
    echo "Uso: ./sync-branches.sh"
    echo ""
    echo "O script fará:"
    echo "  1. Verificar mudanças não commitadas"
    echo "  2. Sincronizar branches dev ↔ main"
    echo "  3. Fazer push de ambas as branches"
    echo "  4. Mostrar status final"
    echo ""
    echo "Opções:"
    echo "  -h, --help    Mostra esta ajuda"
    echo ""
    exit 0
fi

# Executar função principal
main "$@"
