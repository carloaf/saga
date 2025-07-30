# 🔄 Scripts de Automação SAGA

## Visão Geral

Este projeto contém scripts automatizados para facilitar o desenvolvimento e deploy do sistema SAGA.

## Scripts Disponíveis

### 🔄 `sync-branches.sh`
**Sincronização Automática de Branches**

Sincroniza automaticamente as branches `dev` e `main`, fazendo merge entre elas e enviando para o repositório remoto.

```bash
./sync-branches.sh
```

**O que faz:**
1. ✅ Verifica mudanças não commitadas
2. 🔄 Sincroniza branches dev ↔ main
3. 📤 Faz push de ambas as branches
4. 📊 Mostra status final

---

### 🚀 `quick-deploy.sh`
**Deploy Rápido Interativo**

Menu interativo para execução rápida de operações comuns.

```bash
./quick-deploy.sh
```

**Opções disponíveis:**
- 🔄 Sincronizar branches + deploy
- 🔄 Apenas sincronizar branches
- 🚀 Apenas fazer deploy
- ❌ Cancelar operação

---

### 🚀 `deploy.sh`
**Deploy para Produção**

Script principal de deploy para o servidor de produção.

```bash
./deploy.sh
```

**Funcionalidades:**
- 🐳 Gerenciamento Docker
- 🔧 Otimizações Laravel
- 📦 Cache e configurações
- 🔐 Permissões de segurança

---

### 🐛 `debug-login.sh`
**Debug do Sistema de Autenticação**

Ferramenta completa para debug de problemas de login.

```bash
./debug-login.sh
```

**Verificações:**
- 🌐 Conectividade URLs
- 🗄️ Status do banco de dados
- 👤 Criação de usuário de teste
- 📁 Permissões de arquivos
- 🔧 Configurações Laravel

## Workflow Recomendado

### Para Desenvolvimento
```bash
# 1. Trabalhar na branch dev
git checkout dev

# 2. Fazer suas alterações e commits
git add .
git commit -m "feat: nova funcionalidade"

# 3. Sincronizar com main e fazer push
./sync-branches.sh
```

### Para Deploy
```bash
# Deploy rápido com menu
./quick-deploy.sh

# Ou deploy direto
./deploy.sh
```

### Para Debug
```bash
# Se houver problemas de login
./debug-login.sh
```

## Estrutura de Branches

- **`main`**: Branch principal (produção)
- **`dev`**: Branch de desenvolvimento

> 📝 **Nota**: O script `sync-branches.sh` mantém ambas as branches sincronizadas automaticamente.

## Requisitos

- Git configurado
- Docker e Docker Compose
- Permissões de execução nos scripts
- Conectividade com repositório remoto

## Troubleshooting

### Script não executa
```bash
# Dar permissão de execução
chmod +x *.sh
```

### Problemas de conectividade
```bash
# Verificar remoto
git remote -v
git ls-remote origin
```

### Debug de autenticação
```bash
# Usar script de debug
./debug-login.sh
```

---

**Sistema de Agendamento e Gestão de Arranchamento (SAGA)**  
*Automatização para desenvolvimento eficiente* 🚀
