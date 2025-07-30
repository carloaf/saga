#!/bin/bash

# Script de Status SAGA - Visão Geral do Sistema
# Sistema de Agendamento e Gestão de Arranchamento

# Cores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m'

echo "📊 SAGA - Status do Sistema"
echo "==========================="
echo ""

# Verificar se estamos em repositório Git
if ! git rev-parse --git-dir > /dev/null 2>&1; then
    echo "❌ Não é um repositório Git!"
    exit 1
fi

# Status do Git
echo -e "${BLUE}🔄 Status Git${NC}"
echo "─────────────"
current_branch=$(git branch --show-current)
echo "📍 Branch atual: $current_branch"

# Status das branches
if git ls-remote origin > /dev/null 2>&1; then
    git fetch origin --quiet
    
    # Main branch
    main_commit=$(git log origin/main --oneline -1 2>/dev/null || echo "N/A")
    echo "🚀 main:  $main_commit"
    
    # Dev branch  
    dev_commit=$(git log origin/dev --oneline -1 2>/dev/null || echo "N/A")
    echo "🛠️  dev:   $dev_commit"
    
    # Verificar se branches estão sincronizadas
    if [ "$main_commit" = "$dev_commit" ] && [ "$main_commit" != "N/A" ]; then
        echo -e "${GREEN}✅ Branches sincronizadas${NC}"
    else
        echo -e "${YELLOW}⚠️  Branches podem estar dessincronizadas${NC}"
    fi
else
    echo "❌ Não foi possível conectar ao repositório remoto"
fi

# Verificar mudanças locais
if [[ -n $(git status --porcelain) ]]; then
    echo -e "${YELLOW}⚠️  Há mudanças não commitadas${NC}"
    git status --short | head -5
    if [[ $(git status --short | wc -l) -gt 5 ]]; then
        echo "   ... e mais $(($(git status --short | wc -l) - 5)) arquivo(s)"
    fi
else
    echo -e "${GREEN}✅ Working tree limpo${NC}"
fi

echo ""

# Status dos Scripts
echo -e "${PURPLE}🛠️  Scripts Disponíveis${NC}"
echo "──────────────────────"

scripts=("sync-branches.sh" "quick-deploy.sh" "deploy.sh" "debug-login.sh" "setup.sh")

for script in "${scripts[@]}"; do
    if [ -f "$script" ] && [ -x "$script" ]; then
        echo -e "${GREEN}✅${NC} $script"
    elif [ -f "$script" ]; then
        echo -e "${YELLOW}⚠️${NC}  $script (não executável)"
    else
        echo -e "❌ $script (não encontrado)"
    fi
done

echo ""

# Status do Docker
echo -e "${CYAN}🐳 Status Docker${NC}"
echo "────────────────"

if command -v docker-compose > /dev/null 2>&1; then
    if docker-compose ps > /dev/null 2>&1; then
        running_services=$(docker-compose ps --services --filter "status=running" 2>/dev/null | wc -l)
        total_services=$(docker-compose ps --services 2>/dev/null | wc -l)
        
        if [ "$running_services" -gt 0 ]; then
            echo -e "${GREEN}✅ Docker Compose: $running_services/$total_services serviços rodando${NC}"
            docker-compose ps --format "table {{.Service}}\t{{.State}}\t{{.Ports}}" 2>/dev/null | grep -v "^$"
        else
            echo -e "${YELLOW}⚠️  Docker Compose: Nenhum serviço rodando${NC}"
        fi
    else
        echo "❌ Erro ao verificar status do Docker Compose"
    fi
else
    echo "❌ Docker Compose não encontrado"
fi

echo ""

# Comandos úteis
echo -e "${BLUE}🚀 Comandos Rápidos${NC}"
echo "───────────────────"
echo "• ./sync-branches.sh     - Sincronizar branches"
echo "• ./quick-deploy.sh      - Menu de deploy"
echo "• ./deploy.sh           - Deploy completo"
echo "• ./debug-login.sh      - Debug de autenticação"
echo "• docker-compose up -d  - Iniciar serviços"
echo "• git checkout dev      - Mudar para desenvolvimento"

echo ""
echo -e "${PURPLE}📖 Documentação: AUTOMATION_README.md${NC}"
echo -e "${PURPLE}📋 Comandos: COMMANDS.md${NC}"
