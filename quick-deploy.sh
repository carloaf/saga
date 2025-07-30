#!/bin/bash

# Script de Deploy Rápido SAGA
# Sincroniza branches e faz deploy em uma única operação

set -e

# Cores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

log_info() { echo -e "${BLUE}ℹ️  $1${NC}"; }
log_success() { echo -e "${GREEN}✅ $1${NC}"; }
log_warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }

echo "🚀 SAGA - Deploy Rápido"
echo "======================="
echo ""

# Verificar se estamos no diretório correto
if [ ! -f "sync-branches.sh" ]; then
    echo "❌ Erro: sync-branches.sh não encontrado!"
    echo "Execute este script a partir do diretório raiz do projeto SAGA."
    exit 1
fi

if [ ! -f "deploy.sh" ]; then
    echo "❌ Erro: deploy.sh não encontrado!"
    echo "Execute este script a partir do diretório raiz do projeto SAGA."
    exit 1
fi

# Mostrar opções
echo "Escolha uma opção:"
echo "1) 🔄 Sincronizar branches e fazer deploy"
echo "2) 🔄 Apenas sincronizar branches"
echo "3) 🚀 Apenas fazer deploy"
echo "4) ❌ Cancelar"
echo ""

read -p "Digite sua escolha (1-4): " choice

case $choice in
    1)
        log_info "Executando sincronização completa + deploy..."
        echo ""
        
        # Executar sincronização
        log_info "Passo 1/2: Sincronizando branches..."
        ./sync-branches.sh
        
        echo ""
        log_info "Passo 2/2: Executando deploy..."
        ./deploy.sh
        
        echo ""
        log_success "🎉 Sincronização e deploy concluídos com sucesso!"
        ;;
        
    2)
        log_info "Executando apenas sincronização..."
        ./sync-branches.sh
        ;;
        
    3)
        log_info "Executando apenas deploy..."
        ./deploy.sh
        ;;
        
    4)
        log_warning "Operação cancelada pelo usuário."
        exit 0
        ;;
        
    *)
        echo "❌ Opção inválida! Use 1, 2, 3 ou 4."
        exit 1
        ;;
esac

echo ""
log_info "Scripts disponíveis:"
echo "  • ./sync-branches.sh - Sincronizar branches"
echo "  • ./deploy.sh - Deploy para produção"
echo "  • ./debug-login.sh - Debug de autenticação"
echo "  • ./quick-deploy.sh - Este script (deploy rápido)"
