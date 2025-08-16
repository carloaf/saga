# 🗄️ Scripts de Database - SAGA

## 📁 Estrutura

```
scripts/database/
├── backup.sh      # Script completo de backup
├── restore.sh     # Script de restore com validações
├── setup.sh       # Inicialização de novo ambiente
└── examples.sh    # Exemplos de uso
```

## 🚀 Uso Rápido

### Backup Automático
```bash
# Backup de todos os ambientes disponíveis
./scripts/database/backup.sh
```

### Inicializar Ambiente
```bash
# Desenvolvimento
./scripts/database/setup.sh dev

# Staging  
./scripts/database/setup.sh staging
```

### Restore de Dados
```bash
# Restore completo
./scripts/database/restore.sh dev backups/saga_dev_complete_20250815_210917.sql.gz

# Apenas dados
./scripts/database/restore.sh dev backups/saga_dev_data_20250815_210917.sql.gz data
```

## 📋 Funcionalidades

### 🗄️ backup.sh
- ✅ Backup automático de todos os ambientes disponíveis
- ✅ Múltiplos tipos: completo, dados, por tabela
- ✅ Compressão automática (.gz)
- ✅ Limpeza de backups antigos (>30 dias)
- ✅ Relatórios detalhados
- ✅ Validação de containers

### 🔄 restore.sh
- ✅ Restore com backup de segurança automático
- ✅ Validações de segurança
- ✅ Múltiplos tipos de restore
- ✅ Verificação de integridade pós-restore
- ✅ Limpeza de caches Laravel
- ✅ Relatórios de status

### 🌱 setup.sh
- ✅ Inicialização completa de ambiente
- ✅ Verificação de migrations
- ✅ Execução de seeders base
- ✅ Validação de conectividade
- ✅ Limpeza de caches
- ✅ Relatório de status final

## 🎯 Status Atual dos Dados

### Development (saga_db)
```
organizations: 13 registros ✅
ranks: 16 registros ✅  
users: 1 registro ✅
bookings: 0 registros (vazio)
weekly_menus: 0 registros (vazio)
```

### Staging (saga_db_staging)
```
organizations: 13 registros ✅
ranks: 16 registros ✅
users: 0 registros (vazio)
bookings: 0 registros (vazio)  
weekly_menus: 0 registros (vazio)
```

## 🔐 Segurança

- ✅ Backup de segurança antes de restore
- ✅ Confirmação obrigatória para operações destrutivas
- ✅ Validação de containers antes de execução
- ✅ Logs detalhados de todas as operações

## 📖 Documentação Completa

Para documentação detalhada, consulte:
- **docs/BACKUP_RESTORE.md** - Guia completo
- **docs/COMMANDS.md** - Comandos manuais
- **scripts/database/examples.sh** - Exemplos práticos

## 🚨 Importante

⚠️ **Sempre teste scripts em ambiente de desenvolvimento antes de usar em produção!**

⚠️ **Faça backup de segurança antes de qualquer operação de restore!**
