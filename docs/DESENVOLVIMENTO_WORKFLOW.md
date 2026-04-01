# SAGA - Workflow de Desenvolvimento Multi-Arquitetura
# Data: 15 de Agosto de 2025
# Guia completo para desenvolvimento com estrutura profissional

## 📋 VISÃO GERAL

O projeto SAGA agora possui uma **estrutura multi-arquitetura profissional** que suporta desenvolvimento simultâneo em múltiplos ambientes com isolamento completo de configurações e dados.

### 🏗️ Arquitetura dos Ambientes

```
┌─────────────────┬──────────────────┬─────────────────┐
│   DEVELOPMENT   │     STAGING      │   PRODUCTION    │
├─────────────────┼──────────────────┼─────────────────┤
│ porta: 8000     │ porta: 8080      │ porta: 80       │
│ DB: saga        │ DB: saga_staging │ DB: saga_prod   │
│ Live reload     │ Debug ativado    │ Otimizado       │
│ Mount code      │ Mount + .env     │ Embedded code   │
│ Fast iteration  │ Testing/QA       │ High performance│
└─────────────────┴──────────────────┴─────────────────┘
```

## 🔧 CONFIGURAÇÃO DOS AMBIENTES

### Development (Desenvolvimento Local)
```yaml
# docker-compose.yml
- Volume: Mount direto do código fonte (./)
- Configuração: .env (credenciais de desenvolvimento)
- Banco: saga_db (porta 5432)
- Redis: saga_redis (porta 6379)
- Hot reload: Ativado automaticamente
- Debug: APP_DEBUG=true
```

### Staging (Homologação/QA)
```yaml
# deploy/staging/docker-compose.staging.yml
- Volume: Mount do código + .env.staging específico
- Configuração: .env.staging (credenciais de staging)
- Banco: saga_db_staging (porta 5433)
- Redis: saga_redis_staging (porta 6380)
- Testing: Ambiente isolado para QA
- Debug: APP_DEBUG=true (para troubleshooting)
```

### Production (Produção)
```yaml
# deploy/production/docker-compose.prod.yml
- Volume: Código embedado na imagem (sem mount)
- Configuração: .env.production
- Banco: saga_db_prod (porta padrão)
- Redis: saga_redis_prod (porta padrão)
- Performance: Otimizado, debug desabilitado
- SSL: Nginx com certificados
```

## 🚀 VANTAGENS DA NOVA ESTRUTURA

### 1. **Isolamento Completo**
- ✅ **Bancos independentes**: Cada ambiente tem seu próprio PostgreSQL
- ✅ **Configurações isoladas**: .env específico para cada ambiente
- ✅ **Portas diferentes**: Sem conflitos entre ambientes
- ✅ **Dados separados**: Desenvolvimento não afeta staging/produção

### 2. **Desenvolvimento Ágil**
- ✅ **Hot reload**: Mudanças no código refletem instantaneamente
- ✅ **Debug completo**: Logs detalhados e error reporting
- ✅ **Fast iteration**: Ciclo de desenvolvimento rápido
- ✅ **Multi-arquitetura**: Funciona em x64 e ARM64 nativamente

### 3. **Testing/QA Eficiente**
- ✅ **Ambiente dedicado**: Staging isolado para testes
- ✅ **Deploy realístico**: Processo similar à produção
- ✅ **Rollback fácil**: Scripts automatizados
- ✅ **Performance testing**: Ambiente próximo à produção

### 4. **Deploy Profissional**
- ✅ **Imagens otimizadas**: Multi-stage Dockerfile
- ✅ **Build multi-arch**: Suporte automático x64/ARM64
- ✅ **Automatização**: Scripts para build, deploy e rollback
- ✅ **Monitoramento**: Health checks e logs centralizados

## 🌳 ESTRATÉGIA DE BRANCHES

### Branch Structure
```
main (produção)
├── dev (desenvolvimento)
│   ├── feature/nova-funcionalidade
│   ├── feature/melhorias-ui
│   ├── bugfix/correcao-login
│   └── hotfix/security-patch
└── release/v1.2.0
```

### 1. **main** - Branch de Produção
- **Propósito**: Código estável em produção
- **Deploy**: Automático para ambiente de produção
- **Proteção**: Apenas merge via Pull Request
- **Testing**: Todos os testes devem passar

### 2. **dev** - Branch de Desenvolvimento
- **Propósito**: Integração contínua de features
- **Deploy**: Automático para ambiente de staging
- **Testing**: Ambiente de QA e validação
- **Merge**: Features e bugfixes

### 3. **feature/** - Novas Funcionalidades
- **Nomenclatura**: `feature/nome-da-funcionalidade`
- **Base**: Criada a partir de `dev`
- **Desenvolvimento**: Ambiente local (port 8000)
- **Merge**: Para `dev` via Pull Request

### 4. **bugfix/** - Correções
- **Nomenclatura**: `bugfix/descricao-do-bug`
- **Base**: Criada a partir de `dev`
- **Urgência**: Merge prioritário
- **Testing**: Validação em staging obrigatória

### 5. **hotfix/** - Correções Críticas
- **Nomenclatura**: `hotfix/descricao-critica`
- **Base**: Criada a partir de `main`
- **Deploy**: Direto para produção após validação
- **Merge**: Para `main` e `dev` simultaneamente

## 💻 FLUXO DE DESENVOLVIMENTO DIÁRIO

### 1. **Início do Desenvolvimento**
```bash
# 1. Atualizar repositório
git checkout dev
git pull origin dev

# 2. Criar branch de feature
git checkout -b feature/nova-funcionalidade

# 3. Iniciar ambiente de desenvolvimento
docker compose up -d

# 4. Verificar se está funcionando
curl http://localhost:8000
# Deve retornar HTTP 200
```

### 2. **Durante o Desenvolvimento**
```bash
# Trabalhar normalmente no código
# Hot reload está ativado automaticamente

# Verificar logs se necessário
docker logs saga_app_dev

# Executar migrations se necessário
docker exec saga_app_dev php artisan migrate

# Executar testes
docker exec saga_app_dev php artisan test
```

### 3. **Commit e Push**
```bash
# Commits frequentes com mensagens descritivas
git add .
git commit -m "feat: implementa autenticação OAuth Google"

# Push da branch
git push origin feature/nova-funcionalidade
```

### 4. **Testing em Staging**
```bash
# Fazer merge para dev (via PR)
git checkout dev
git merge feature/nova-funcionalidade

# Deploy automático para staging
git push origin dev

# Testar em staging
curl http://localhost:8080

# Validar funcionalidades
# - Login/logout
# - Fluxos principais
# - Performance
```

### 4.1 **Homologação no CTA (Teleport + bind mount)**
```bash
# 1. Autenticar no Teleport
tsh login --proxy=teleport.7cta.eb.mil.br --user=cleitonpaulo.martins@eb.mil.br

# 2. Validar acesso e diretório da aplicação
tsh ssh suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO "ls -la /workspace/saga/"

# 3. Fazer backup dos arquivos alterados antes da cópia
tsh ssh suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO "cp /workspace/saga/app/Models/User.php /workspace/saga/app/Models/User.php.bkp-$(date +%Y%m%d-%H%M%S)"

# 4. Copiar somente os arquivos modificados
tsh scp app/Models/User.php suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO:/workspace/saga/app/Models/User.php

# 5. Limpar cache e reiniciar a aplicação
tsh ssh suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO "docker exec saga_app_dev php artisan optimize:clear && docker restart saga_app_dev"

# 6. Verificar saúde do container
tsh ssh suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO "docker ps | grep saga_app_dev"
```

**Notas operacionais do CTA**
- O código do ambiente de homologação fica em `/workspace/saga` com bind mount no container `saga_app_dev`.
- Quando o `docker-compose` remoto não reconhecer o serviço `app`, prefira `docker exec` e `docker restart` diretamente no container `saga_app_dev`.
- Para mudanças pequenas, copie apenas os arquivos alterados e rode `php artisan optimize:clear` antes do restart.
- Valide a funcionalidade no navegador após o deploy, porque nem todo cenário de homologação está coberto por testes automatizados no host.

**Limitação conhecida de testes no servidor**
- O comando `php artisan test` no CTA depende de um banco de testes dedicado, como `saga_test`.
- Se esse banco não existir, faça a validação funcional manual em homologação ou provisione a base antes de executar a suíte.

### 5. **Deploy para Produção**
```bash
# Merge para main (via PR)
git checkout main
git merge dev

# Deploy para produção
cd deploy/production
./deploy-production.sh deploy

# Monitorar saúde
./deploy-production.sh status
./deploy-production.sh logs
```

## 🔄 COMANDOS ESSENCIAIS

### Desenvolvimento Local
```bash
# Iniciar ambiente
docker compose up -d

# Parar ambiente
docker compose down

# Rebuild após mudanças no Dockerfile
docker compose build --no-cache

# Logs em tempo real
docker compose logs -f app

# Executar comandos Laravel
docker exec saga_app_dev php artisan migrate
docker exec saga_app_dev php artisan tinker
```

### Staging
```bash
# Iniciar staging
cd deploy/staging
docker compose -f docker-compose.staging.yml up -d

# Parar staging
docker compose -f docker-compose.staging.yml down

# Logs do staging
docker logs saga_app_staging -f
```

### Build Multi-Arquitetura
```bash
# Build local
./scripts/deployment/build-multiarch.sh

# Build e push para registry
./scripts/deployment/build-multiarch.sh --push --tag v1.0.0

# Build apenas ARM64
./scripts/deployment/build-multiarch.sh --platforms linux/arm64
```

### Produção
```bash
# Deploy completo
cd deploy/production
./deploy-production.sh deploy

# Status dos serviços
./deploy-production.sh status

# Rollback se necessário
./deploy-production.sh rollback

# Logs de produção
./deploy-production.sh logs
```

## 📊 MONITORAMENTO E DEBUGGING

### Health Checks
```bash
# Verificar saúde dos containers
docker ps | grep saga

# Testes HTTP
curl -s -o /dev/null -w "%{http_code}" http://localhost:8000  # Dev
curl -s -o /dev/null -w "%{http_code}" http://localhost:8080  # Staging
```

### Logs Centralizados
```bash
# Logs do Laravel
docker exec saga_app_dev tail -f storage/logs/laravel.log

# Logs do Apache
docker logs saga_app_dev

# Logs do banco
docker logs saga_db
```

### Debugging
```bash
# Acessar container
docker exec -it saga_app_dev bash

# Debug SQL queries
docker exec saga_app_dev php artisan db:show

# Cache clear
docker exec saga_app_dev php artisan cache:clear
docker exec saga_app_dev php artisan config:clear
```

## 🛡️ BOAS PRÁTICAS

### 1. **Commits**
- ✅ **Mensagens claras**: Use conventional commits (feat:, fix:, docs:)
- ✅ **Commits pequenos**: Uma funcionalidade por commit
- ✅ **Testes**: Sempre executar testes antes do commit
- ✅ **Review**: Usar Pull Requests para code review

### 2. **Branches**
- ✅ **Nomenclatura**: Siga o padrão feature/bugfix/hotfix
- ✅ **Atualização**: Sempre atualizar base antes de criar branch
- ✅ **Cleanup**: Deletar branches após merge
- ✅ **Proteção**: main e dev devem ser protegidas

### 3. **Ambientes**
- ✅ **Isolamento**: Nunca misturar configurações de ambientes
- ✅ **Backup**: Fazer backup antes de deploy em produção
- ✅ **Monitoring**: Monitorar métricas pós-deploy
- ✅ **Rollback**: Ter plano de rollback pronto

### 4. **Segurança**
- ✅ **Credenciais**: Nunca commitar .env com dados sensíveis
- ✅ **SSL**: Usar HTTPS em produção
- ✅ **Updates**: Manter dependências atualizadas
- ✅ **Logs**: Não logar informações sensíveis

## 🎯 BENEFÍCIOS ALCANÇADOS

### Para Desenvolvedores
- 🚀 **Produtividade**: Hot reload e ambiente rápido
- 🔧 **Debugging**: Logs detalhados e fácil acesso
- 🔄 **Iteração**: Ciclo de desenvolvimento otimizado
- 🏗️ **Arquitetura**: Suporte nativo multi-arquitetura

### Para QA/Testing
- 🧪 **Ambiente isolado**: Staging dedicado para testes
- 📊 **Dados limpos**: Banco separado para cada ambiente
- 🔍 **Debugging**: Logs e métricas de qualidade
- 🚀 **Deploy**: Processo similar à produção

### Para DevOps/Produção
- 📦 **Containers**: Deployment consistente
- 🔄 **Automation**: Scripts para todas as operações
- 📈 **Monitoring**: Health checks e alertas
- 🛡️ **Segurança**: Configurações isoladas e SSL

### Para o Projeto
- 🏢 **Profissional**: Estrutura enterprise-ready
- 📚 **Documentação**: Processos bem documentados
- 🔧 **Manutenção**: Fácil manutenção e updates
- 📈 **Escalabilidade**: Preparado para crescimento

## ✅ CONCLUSÃO

A estrutura multi-arquitetura do SAGA proporciona um **ambiente de desenvolvimento profissional** com:

- **3 ambientes isolados** (dev/staging/prod)
- **Hot reload** para desenvolvimento ágil
- **Deploy automatizado** com scripts dedicados
- **Multi-arquitetura nativa** (x64/ARM64)
- **Monitoramento completo** com health checks
- **Segurança** com isolamento de configurações

Este workflow permite **desenvolvimento eficiente**, **testing rigoroso** e **deploys seguros**, seguindo as melhores práticas da indústria.

---

📝 **Próximos passos**: Continue desenvolvendo normalmente usando este workflow. Para dúvidas, consulte a documentação ou verifique os logs dos containers.
