# 🐳 Guia Prático: Trabalhando com Ambientes Docker no SAGA

## 📋 Visão Geral dos Ambientes

O projeto SAGA possui **três ambientes containerizados**:

1. **DEV** - Desenvolvimento (porta 8000)
2. **STAGING** - Homologação (porta 8080)  
3. **PRODUCTION** - Produção (porta 80)

---

## 🛠️ Ambiente de Desenvolvimento (DEV)

### ▶️ Como Iniciar:
```bash
# Método 1: Docker Compose padrão
cd /home/sonnote/Documents/saga
docker-compose up -d

# Método 2: Task do VS Code
# Use: Ctrl+Shift+P → "Tasks: Run Task" → "Start SAGA Development Server"

# Método 3: Script automatizado
./setup-saga.sh
```

### 📍 Acessos DEV:
- **Aplicação**: http://localhost:8000
- **Database**: localhost:5432
- **Redis**: localhost:6379

### 🔧 Comandos Úteis DEV:
```bash
# Ver logs
docker-compose logs -f

# Executar comandos Laravel
docker-compose exec app php artisan migrate
docker-compose exec app php artisan tinker

# Acessar container
docker-compose exec app bash

# Parar ambiente
docker-compose down

# Rebuild completo
docker-compose down && docker-compose up -d --build
```

---

## 🧪 Ambiente de Staging (STAGING)

### ▶️ Como Iniciar:
```bash
cd /home/sonnote/Documents/saga

# Iniciar staging
docker-compose -f deploy/staging/docker-compose.staging.yml up -d --build

# Com variáveis customizadas (para evitar conflitos de porta)
STAGING_PORT=8080 DB_PORT=5434 REDIS_PORT=6380 \
docker-compose -f deploy/staging/docker-compose.staging.yml up -d
```

### 📍 Acessos STAGING:
- **Aplicação**: http://localhost:8080
- **Database**: localhost:5434
- **Redis**: localhost:6380

### 🔧 Comandos Úteis STAGING:
```bash
# Ver logs staging
docker-compose -f deploy/staging/docker-compose.staging.yml logs -f

# Executar migrations
docker-compose -f deploy/staging/docker-compose.staging.yml exec app php artisan migrate --force

# Limpar caches
docker-compose -f deploy/staging/docker-compose.staging.yml exec app php artisan config:cache
docker-compose -f deploy/staging/docker-compose.staging.yml exec app php artisan route:cache

# Acessar container staging
docker-compose -f deploy/staging/docker-compose.staging.yml exec app bash

# Parar staging
docker-compose -f deploy/staging/docker-compose.staging.yml down
```

---

## 🚀 Workflow Prático de Desenvolvimento

### 1️⃣ **Desenvolvimento Normal (DEV)**
```bash
# 1. Iniciar ambiente dev
docker-compose up -d

# 2. Fazer mudanças no código
# (arquivos são montados como volume - mudanças são instantâneas)

# 3. Testar funcionalidades
docker-compose exec app php artisan test:password-reset PENDENTE_1 admin@saga.mil.br

# 4. Fazer commits
git add .
git commit -m "feat: nova funcionalidade"
git push origin dev
```

### 2️⃣ **Teste em Staging**
```bash
# 1. Sincronizar branches (opcional - se quiser testar main)
./scripts/development/sync-branches.sh

# 2. Iniciar staging
docker-compose -f deploy/staging/docker-compose.staging.yml up -d --build

# 3. Executar migrations se necessário
docker-compose -f deploy/staging/docker-compose.staging.yml exec app php artisan migrate --force

# 4. Testar em ambiente similar à produção
# Acessar: http://localhost:8080
```

### 3️⃣ **Execução Simultânea (DEV + STAGING)**
```bash
# Rodar os dois ambientes ao mesmo tempo
docker-compose up -d                                          # DEV na porta 8000
docker-compose -f deploy/staging/docker-compose.staging.yml up -d  # STAGING na porta 8080

# Verificar se ambos estão rodando
docker ps
```

---

## 🗂️ Estrutura de Arquivos Docker

```
saga/
├── docker-compose.yml                    # 🛠️ DEV
├── deploy/
│   ├── staging/
│   │   └── docker-compose.staging.yml    # 🧪 STAGING
│   └── production/
│       └── docker-compose.prod.yml       # 🚀 PRODUCTION
├── Dockerfile                            # Base image build
├── .env                                  # Config DEV
├── .env.staging                          # Config STAGING
└── .env.production                       # Config PRODUCTION
```

---

## ⚙️ Variáveis de Ambiente por Ambiente

### DEV (.env)
```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
DB_DATABASE=saga
```

### STAGING (.env.staging)
```env
APP_ENV=staging
APP_DEBUG=true
APP_URL=http://localhost:8080
DB_DATABASE=saga_staging
```

---

## 🔄 Scripts Utilitários

### **Sync de Branches**
```bash
# Sincronizar dev → main
./scripts/development/sync-branches.sh
```

### **Deploy Completo**
```bash
# Deploy para produção
./scripts/development/deploy.sh
```

### **Teste de Funcionalidades**
```bash
# Testar reset de senha (DEV)
docker-compose exec app php artisan test:password-reset PENDENTE_1 admin@saga.mil.br

# Testar reset de senha (STAGING)
docker-compose -f deploy/staging/docker-compose.staging.yml exec app php artisan test:password-reset PENDENTE_1 admin@saga.mil.br
```

---

## 🚨 Resolução de Problemas Comuns

### **Conflito de Portas**
```bash
# Se a porta 8080 estiver ocupada
STAGING_PORT=8081 docker-compose -f deploy/staging/docker-compose.staging.yml up -d

# Se database der conflito
DB_PORT=5435 docker-compose -f deploy/staging/docker-compose.staging.yml up -d
```

### **Limpeza Completa**
```bash
# Parar tudo e limpar
docker-compose down
docker-compose -f deploy/staging/docker-compose.staging.yml down
docker system prune -f
```

### **Rebuild Forçado**
```bash
# DEV
docker-compose down && docker-compose build --no-cache && docker-compose up -d

# STAGING  
docker-compose -f deploy/staging/docker-compose.staging.yml down
docker-compose -f deploy/staging/docker-compose.staging.yml build --no-cache
docker-compose -f deploy/staging/docker-compose.staging.yml up -d
```

---

## 📊 Monitoramento dos Ambientes

### **Verificar Status**
```bash
# Ver todos os containers
docker ps

# Ver apenas SAGA
docker ps | grep saga

# Status detalhado
docker-compose ps                                          # DEV
docker-compose -f deploy/staging/docker-compose.staging.yml ps  # STAGING
```

### **Logs em Tempo Real**
```bash
# DEV
docker-compose logs -f app

# STAGING
docker-compose -f deploy/staging/docker-compose.staging.yml logs -f app
```

---

## 🎯 Resumo dos Comandos Principais

| Ação | DEV | STAGING |
|------|-----|---------|
| **Iniciar** | `docker-compose up -d` | `docker-compose -f deploy/staging/docker-compose.staging.yml up -d` |
| **Parar** | `docker-compose down` | `docker-compose -f deploy/staging/docker-compose.staging.yml down` |
| **Logs** | `docker-compose logs -f` | `docker-compose -f deploy/staging/docker-compose.staging.yml logs -f` |
| **Bash** | `docker-compose exec app bash` | `docker-compose -f deploy/staging/docker-compose.staging.yml exec app bash` |
| **Artisan** | `docker-compose exec app php artisan` | `docker-compose -f deploy/staging/docker-compose.staging.yml exec app php artisan` |

---

**✅ Com este guia, você pode trabalhar eficientemente com ambos os ambientes Docker do SAGA!**
