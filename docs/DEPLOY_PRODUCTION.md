# Guia de Deploy e Gerenciamento de Ambientes - SAGA

## 🏗️ Estratégia de Ambientes

### Ambientes Configurados:
1. **DEV** (Desenvolvimento) - `localhost:8000`
2. **STAGING** (Homologação) - `localhost:8080` 
3. **PRODUCTION** (Produção) - `porta 80`

---

## 🚀 Deploy para Produção

### Pré-requisitos
```bash
# 1. Servidor de produção com Docker e Docker Compose
# 2. Domínio configurado (ex: saga.mil.br)
# 3. SSL/TLS configurado
# 4. Backup strategy definida
```

### Passo a Passo do Deploy

#### 1. Preparação do Código
```bash
# No seu ambiente de desenvolvimento
git add .
git commit -m "feat: Sistema pronto para produção
- Migrations consolidadas
- Sistema de roles implementado
- Formulários validados
- Backups configurados"

git push origin main
```

#### 2. Criação das Imagens Docker

##### Opção A: Build Local e Push para Registry
```bash
# Build da imagem de produção
docker build -t saga/app:latest -t saga/app:v1.0.0 .

# Se usando Docker Hub ou registry privado:
docker tag saga/app:latest your-registry/saga:latest
docker push your-registry/saga:latest
```

##### Opção B: Build Direto no Servidor
```bash
# No servidor de produção
git clone https://github.com/carloaf/saga.git
cd saga
```

#### 3. Configuração do Ambiente de Produção

```bash
# Copiar configuração de produção
cp .env.example .env.production

# Editar variáveis de produção
nano .env.production
```

**Variáveis críticas para produção:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://saga.mil.br
DB_HOST=saga_db_prod
DB_DATABASE=saga_production
DB_USERNAME=saga_user
DB_PASSWORD=SENHA_FORTE_AQUI
GOOGLE_CLIENT_ID=your_production_client_id
GOOGLE_CLIENT_SECRET=your_production_secret
```

#### 4. Deploy da Aplicação

```bash
# Ir para diretório de produção
cd deploy/production

# Iniciar ambiente de produção
docker-compose -f docker-compose.prod.yml up -d --build

# Executar migrations
docker exec saga_app_prod php artisan migrate --force

# Criar usuários iniciais (se necessário)
docker exec saga_app_prod php artisan db:seed --force

# Otimizar cache
docker exec saga_app_prod php artisan config:cache
docker exec saga_app_prod php artisan route:cache
docker exec saga_app_prod php artisan view:cache
```

---

## 🔄 Gerenciamento de Ambientes

### Comandos por Ambiente

#### DEV (Desenvolvimento)
```bash
# Iniciar
docker-compose up -d

# Logs
docker-compose logs -f app

# Shell
docker exec -it saga_app_dev bash

# Migrations
docker exec saga_app_dev php artisan migrate

# Parar
docker-compose down
```

#### STAGING (Homologação)
```bash
# Iniciar
cd deploy/staging
docker-compose -f docker-compose.staging.yml up -d

# Logs
docker-compose -f docker-compose.staging.yml logs -f app

# Shell
docker exec -it saga_app_staging bash

# Sync com DEV
docker exec saga_app_staging php artisan migrate
docker exec saga_app_dev php artisan db:seed --class=TestDataSeeder

# Parar
docker-compose -f docker-compose.staging.yml down
```

#### PRODUCTION (Produção)
```bash
# Iniciar
cd deploy/production
docker-compose -f docker-compose.prod.yml up -d

# Logs (cuidado em produção)
docker-compose -f docker-compose.prod.yml logs --tail=100 app

# Shell (apenas emergências)
docker exec -it saga_app_prod bash

# Backup antes de mudanças
docker exec saga_app_prod php artisan db:backup

# Deploy de update
docker-compose -f docker-compose.prod.yml pull
docker-compose -f docker-compose.prod.yml up -d

# Parar (manutenção programada)
docker-compose -f docker-compose.prod.yml down
```

---

## 📊 Workflow de Deploy

### 1. Desenvolvimento → Staging
```bash
# 1. Commitar mudanças no DEV
git add .
git commit -m "feature: nova funcionalidade"
git push origin main

# 2. Atualizar STAGING
cd deploy/staging
git pull origin main
docker-compose -f docker-compose.staging.yml down
docker-compose -f docker-compose.staging.yml up -d --build

# 3. Testar em STAGING
# - Testes funcionais
# - Testes de performance
# - Validação com usuários
```

### 2. Staging → Production
```bash
# 1. Validação final em STAGING
# 2. Criar tag de release
git tag -a v1.0.0 -m "Release v1.0.0: Sistema completo"
git push origin v1.0.0

# 3. Deploy em PRODUCTION
cd deploy/production
git pull origin main
git checkout v1.0.0  # Deploy da versão específica

# 4. Backup de produção
docker exec saga_app_prod php artisan db:backup

# 5. Deploy
docker-compose -f docker-compose.prod.yml down
docker-compose -f docker-compose.prod.yml up -d --build

# 6. Migrations (se necessário)
docker exec saga_app_prod php artisan migrate --force

# 7. Otimizar cache
docker exec saga_app_prod php artisan optimize
```

---

## 🗂️ Gerenciamento de Imagens

### Estratégia de Tags

```bash
# Desenvolvimento
saga/app:dev          # Sempre latest do branch main

# Staging  
saga/app:staging      # Release candidate

# Produção
saga/app:latest       # Produção atual
saga/app:v1.0.0       # Versão específica
saga/app:stable       # Última versão estável
```

### Build Multi-Ambiente
```bash
# Build todas as imagens de uma vez
docker build -t saga/app:dev --target runtime .
docker build -t saga/app:staging --target runtime .
docker build -t saga/app:latest --target runtime .

# Build com tag específica para produção
docker build -t saga/app:v1.0.0 --target runtime .
docker tag saga/app:v1.0.0 saga/app:latest
```

---

## 📋 Checklist de Deploy

### Pré-Deploy
- [ ] Código commitado e testado
- [ ] Migrations testadas em STAGING
- [ ] Backup do banco de produção
- [ ] Variáveis de ambiente configuradas
- [ ] SSL/TLS configurado
- [ ] Monitoramento configurado

### Durante Deploy
- [ ] Aplicação rodando em STAGING
- [ ] Build da imagem successful
- [ ] Containers iniciados corretamente
- [ ] Migrations executadas
- [ ] Cache otimizado
- [ ] Health check passou

### Pós-Deploy
- [ ] Aplicação acessível
- [ ] Login funcionando
- [ ] Funcionalidades críticas testadas
- [ ] Logs sem erros críticos
- [ ] Performance normal
- [ ] Backup pós-deploy

---

## 🛡️ Segurança e Backup

### Backup Automático
```bash
# Criar script de backup para produção
#!/bin/bash
# /usr/local/bin/saga-backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/saga"

# Backup do banco
docker exec saga_app_prod php artisan db:backup

# Backup dos arquivos
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/saga/storage

# Limpar backups antigos (>30 dias)
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete
```

### Crontab para Backup
```bash
# Backup diário às 2h da manhã
0 2 * * * /usr/local/bin/saga-backup.sh

# Backup antes de deploy
0 * * * * if [ -f /tmp/deploy.lock ]; then /usr/local/bin/saga-backup.sh; fi
```

---

## 🎯 Próximos Passos Recomendados

1. **Configurar Domínio**:
   - Apontar DNS para servidor
   - Configurar SSL/TLS
   - Testar acesso externo

2. **Configurar Monitoramento**:
   - Logs centralizados
   - Alertas de erro
   - Métricas de performance

3. **Setup CI/CD**:
   - Pipeline automatizado
   - Deploy automático para STAGING
   - Deploy manual para PRODUCTION

4. **Documentar Procedures**:
   - Runbook de operação
   - Procedimentos de emergency
   - Contatos e escalação

---

## 🆘 Comandos de Emergência

### Rollback Rápido
```bash
# Voltar para versão anterior
docker-compose -f docker-compose.prod.yml down
docker run -d --name saga_app_prod_temp saga/app:v0.9.0
# Ou restaurar backup
```

### Debug em Produção
```bash
# Logs em tempo real
docker logs -f saga_app_prod

# Status dos containers
docker ps | grep saga

# Uso de recursos
docker stats saga_app_prod
```

### Manutenção
```bash
# Modo manutenção
docker exec saga_app_prod php artisan down

# Sair do modo manutenção
docker exec saga_app_prod php artisan up
```
