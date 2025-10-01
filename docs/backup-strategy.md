# SAGA - Backup Automatizado do Banco de Dados

## 📋 Visão Geral

Este documento descreve a estratégia de backup do banco de dados PostgreSQL do sistema SAGA.

## 🛠️ Scripts Disponíveis

### 1. `backup-database.sh`
Script principal para realizar backups do banco de dados.

**Localização:** `/home/sonnote/Documents/saga/scripts/backup-database.sh`

#### Modos de Uso:

```bash
# Modo interativo (menu)
./scripts/backup-database.sh

# Backup completo (linha de comando)
./scripts/backup-database.sh full

# Backup apenas dados
./scripts/backup-database.sh data

# Backup apenas schema
./scripts/backup-database.sh schema

# Backup tabelas críticas
./scripts/backup-database.sh critical
```

#### Tipos de Backup:

1. **Backup Completo (Schema + Dados)**
   - Inclui estrutura e dados
   - Recomendado para: Migração entre ambientes
   - Arquivo: `saga_<db>_full_<timestamp>.sql.gz`

2. **Backup Apenas Dados**
   - Somente os dados das tabelas
   - Recomendado para: Clonagem de dados
   - Arquivo: `saga_<db>_data_<timestamp>.sql.gz`

3. **Backup Apenas Schema**
   - Somente a estrutura do banco
   - Recomendado para: Documentação, comparação de schemas
   - Arquivo: `saga_<db>_schema_<timestamp>.sql.gz`

4. **Backup Tabelas Críticas**
   - Apenas: users, bookings, ranks, organizations
   - Recomendado para: Backup rápido de dados essenciais
   - Arquivo: `saga_<db>_critical_<timestamp>.sql.gz`

### 2. `restore-database.sh`
Script para restaurar backups.

**Localização:** `/home/sonnote/Documents/saga/scripts/restore-database.sh`

#### Modos de Uso:

```bash
# Modo interativo (menu)
./scripts/restore-database.sh

# Listar backups disponíveis
./scripts/restore-database.sh list

# Restaurar último backup
./scripts/restore-database.sh last

# Verificar integridade dos backups
./scripts/restore-database.sh verify

# Restaurar backup específico
./scripts/restore-database.sh /path/to/backup.sql.gz
```

## 📁 Estrutura de Diretórios

```
saga/
├── backups/                              # Diretório de backups
│   ├── saga_dev_full_20251001_081500.sql.gz
│   ├── saga_dev_critical_20251001_120000.sql.gz
│   ├── backup_report_20251001_081500.txt
│   └── safety_backup_20251001_140000.sql.gz
└── scripts/
    ├── backup-database.sh                # Script de backup
    └── restore-database.sh               # Script de restauração
```

## ⏰ Automatização com Cron

### Backup Diário (3h da manhã)
```bash
# Editar crontab
crontab -e

# Adicionar linha para backup diário completo às 3h
0 3 * * * /home/sonnote/Documents/saga/scripts/backup-database.sh full >> /home/sonnote/Documents/saga/backups/cron.log 2>&1
```

### Backup a cada 6 horas (tabelas críticas)
```bash
# Backup de tabelas críticas a cada 6 horas
0 */6 * * * /home/sonnote/Documents/saga/scripts/backup-database.sh critical >> /home/sonnote/Documents/saga/backups/cron.log 2>&1
```

### Exemplo de Crontab Completo
```bash
# SAGA - Backups Automatizados

# Backup completo diário às 3h da manhã
0 3 * * * /home/sonnote/Documents/saga/scripts/backup-database.sh full >> /home/sonnote/Documents/saga/backups/cron.log 2>&1

# Backup de tabelas críticas a cada 6 horas
0 */6 * * * /home/sonnote/Documents/saga/scripts/backup-database.sh critical >> /home/sonnote/Documents/saga/backups/cron.log 2>&1

# Verificação de integridade semanal (domingo às 4h)
0 4 * * 0 /home/sonnote/Documents/saga/scripts/restore-database.sh verify >> /home/sonnote/Documents/saga/backups/verify.log 2>&1
```

## 🔐 Melhores Práticas para Produção

### 1. **Backup Local + Remoto**
```bash
# Após backup local, copiar para servidor remoto
rsync -avz --progress \
  /home/sonnote/Documents/saga/backups/ \
  user@backup-server:/backups/saga/

# Ou usar rclone para cloud (AWS S3, Google Cloud Storage)
rclone copy /home/sonnote/Documents/saga/backups/ s3:saga-backups/
```

### 2. **Retenção de Backups**
- **Diários**: 7 dias (1 semana)
- **Semanais**: 4 semanas (1 mês)
- **Mensais**: 12 meses (1 ano)

```bash
# Configurar no script backup-database.sh
RETENTION_DAYS=30  # Ajustar conforme necessidade
```

### 3. **Monitoramento de Backups**
```bash
# Script para verificar se backup foi realizado hoje
#!/bin/bash
TODAY=$(date +%Y%m%d)
BACKUP_DIR="/home/sonnote/Documents/saga/backups"

if ls $BACKUP_DIR/*${TODAY}*.sql.gz 1> /dev/null 2>&1; then
    echo "✅ Backup de hoje encontrado"
    exit 0
else
    echo "❌ ALERTA: Nenhum backup encontrado para hoje!"
    # Enviar notificação (email, Slack, etc)
    exit 1
fi
```

### 4. **Teste de Restauração**
```bash
# Testar restauração em ambiente de teste mensalmente
# 1. Criar banco de teste
createdb saga_test

# 2. Restaurar último backup
./scripts/restore-database.sh last

# 3. Validar dados
psql -U saga_user -d saga_test -c "SELECT COUNT(*) FROM users;"
psql -U saga_user -d saga_test -c "SELECT COUNT(*) FROM bookings;"
```

## 🚀 Migração para Servidor Definitivo

### Checklist Pré-Migração

- [ ] **Backup Completo do Ambiente Atual**
  ```bash
  ./scripts/backup-database.sh full
  ```

- [ ] **Verificar Integridade**
  ```bash
  ./scripts/restore-database.sh verify
  ```

- [ ] **Documentar Configurações**
  - Versão PostgreSQL
  - Extensões instaladas
  - Usuários e permissões
  - Configurações customizadas

- [ ] **Exportar Dados de Configuração**
  ```bash
  # Exportar roles e permissões
  pg_dumpall -U postgres --roles-only > roles.sql
  
  # Exportar configurações globais
  pg_dumpall -U postgres --globals-only > globals.sql
  ```

### Procedimento de Migração

1. **Preparar Servidor Novo**
   ```bash
   # Instalar PostgreSQL
   sudo apt update
   sudo apt install postgresql-16
   
   # Criar usuário e banco
   sudo -u postgres createuser saga_user
   sudo -u postgres createdb -O saga_user saga_production
   ```

2. **Transferir Backup**
   ```bash
   scp backups/saga_dev_full_*.sql.gz user@production:/tmp/
   ```

3. **Restaurar no Servidor Novo**
   ```bash
   # No servidor de produção
   gunzip -c /tmp/saga_dev_full_*.sql.gz | \
     psql -U saga_user -d saga_production
   ```

4. **Validar Dados**
   ```bash
   psql -U saga_user -d saga_production -c "\dt"
   psql -U saga_user -d saga_production -c "SELECT COUNT(*) FROM users;"
   psql -U saga_user -d saga_production -c "SELECT COUNT(*) FROM bookings;"
   ```

5. **Configurar Backups Automatizados**
   ```bash
   # Copiar scripts
   scp scripts/*.sh user@production:/opt/saga/scripts/
   
   # Configurar crontab
   ssh user@production
   crontab -e
   # Adicionar jobs de backup
   ```

## 📊 Monitoramento e Alertas

### Script de Monitoramento
```bash
#!/bin/bash
# /opt/saga/scripts/monitor-backups.sh

BACKUP_DIR="/opt/saga/backups"
ALERT_EMAIL="admin@example.com"
ALERT_SLACK_WEBHOOK="https://hooks.slack.com/services/YOUR/WEBHOOK"

# Verificar backup das últimas 24h
RECENT_BACKUP=$(find $BACKUP_DIR -name "*.sql.gz" -mtime -1 | wc -l)

if [ $RECENT_BACKUP -eq 0 ]; then
    # Enviar alerta
    echo "⚠️ ALERTA: Nenhum backup nas últimas 24h!" | \
      mail -s "SAGA Backup Alert" $ALERT_EMAIL
    
    # Slack notification
    curl -X POST $ALERT_SLACK_WEBHOOK \
      -H 'Content-Type: application/json' \
      -d '{"text":"⚠️ SAGA: Nenhum backup nas últimas 24h!"}'
fi
```

## 🔒 Segurança

### Permissões Recomendadas
```bash
# Diretório de backups
chmod 700 /home/sonnote/Documents/saga/backups

# Arquivos de backup
chmod 600 /home/sonnote/Documents/saga/backups/*.sql.gz

# Scripts
chmod 700 /home/sonnote/Documents/saga/scripts/*.sh
```

### Criptografia de Backups
```bash
# Backup com criptografia GPG
./scripts/backup-database.sh full
gpg --encrypt --recipient admin@example.com \
  backups/saga_dev_full_*.sql.gz

# Descriptografar quando necessário
gpg --decrypt backups/saga_dev_full_*.sql.gz.gpg > backup.sql.gz
```

## 📈 Estatísticas de Backup

### Verificar Tamanho dos Backups
```bash
du -sh backups/
ls -lh backups/*.sql.gz | tail -10
```

### Comparar Crescimento
```bash
# Ver crescimento do banco
psql -U saga_user -d saga_dev -c "
SELECT 
    pg_database.datname,
    pg_size_pretty(pg_database_size(pg_database.datname)) AS size
FROM pg_database
WHERE datname = 'saga_dev';"
```

## 🆘 Recuperação de Desastres

### Cenário 1: Perda de Dados Recente
```bash
# Restaurar último backup
./scripts/restore-database.sh last
```

### Cenário 2: Corrupção do Banco
```bash
# 1. Criar novo banco
createdb saga_recovery

# 2. Restaurar do backup
./scripts/restore-database.sh --database=saga_recovery

# 3. Validar e promover
# Após validação, renomear bancos
```

### Cenário 3: Migração Emergencial
```bash
# Backup imediato e transferência
./scripts/backup-database.sh full
scp backups/saga_*.sql.gz user@new-server:/tmp/
```

## 📞 Contatos de Emergência

- **Administrador do Sistema**: [Nome/Email]
- **DBA**: [Nome/Email]
- **Suporte PostgreSQL**: [Email/Telefone]

## 📝 Registro de Alterações

| Data | Versão | Alterações |
|------|--------|-----------|
| 2025-10-01 | 1.0 | Criação inicial da estratégia de backup |
