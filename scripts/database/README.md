# 🗄️ Scripts de Database - SAGA

## 📁 Estrutura

```
scripts/database/
├── backup.sh          # Script completo de backup local
├── restore.sh         # Script de restore com validações
├── setup.sh           # Inicialização de novo ambiente
├── remote-backup.sh   # Backup de máquina remota (SSH)
└── examples.sh        # Exemplos de uso
```

## 🚀 Uso Rápido

### Backup Automático Local
```bash
# Backup de todos os ambientes disponíveis
./scripts/database/backup.sh
```

### Backup de Máquina Remota
```bash
# Importar dados via SSH
./scripts/database/remote-backup.sh usuario@servidor.com dev

# Exemplo real executado:
./scripts/database/remote-backup.sh sonnote@192.168.0.57 dev
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

### 🌐 remote-backup.sh
- ✅ Conectividade SSH automática
- ✅ Backup de segurança antes da importação
- ✅ Verificação de dados remotos
- ✅ Limpeza prévia para evitar conflitos
- ✅ Importação com validação
- ✅ Relatórios de migração

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

### Development (saga_db) - **COM DADOS REAIS**
```
✅ reservas (bookings): 424 registros (dados de produção)
✅ organizações (organizations): 14 registros (militares)
✅ usuários (users): 31 registros (produção)
✅ postos/graduações (ranks): 17 registros
✅ cardápios (weekly_menus): 3 registros
🏢 Principal organização: 11º Depósito de Suprimento (15 usuários)
📅 Período: agosto-setembro 2025 (dados reais)
```

### Staging (saga_db_staging) - **LIMPO PARA TESTES**
```
✅ estrutura: Completa e funcional
✅ organizações: 14 registros (base)
✅ postos/graduações: 17 registros (base)
⭕ usuários: 0 registros (vazio)
⭕ reservas: 0 registros (vazio)
⭕ cardápios: 0 registros (vazio)
```

## 🔐 Segurança

- ✅ Backup de segurança antes de restore
- ✅ Confirmação obrigatória para operações destrutivas
- ✅ Validação de containers antes de execução
- ✅ Logs detalhados de todas as operações
- ✅ Conectividade SSH verificada
- ✅ Verificação de integridade pós-migração

## 📊 Casos Reais de Uso

### 🎯 **Migração Realizada (15/08/2025)**
```bash
Origem: sonnote@192.168.0.57
Método: SSH + pg_dump --data-only
Dados: 424 reservas + 31 usuários + 14 organizações
Tempo: ~2 minutos (incluindo validações)
Status: ✅ SUCESSO COMPLETO
```

### � **Performance**
```bash
Backup local completo: ~30s (26K comprimido)
Backup remoto SSH: ~45s (73K dados)
Restore completo: ~60s (424 registros)
Inicialização ambiente: ~90s (com seeders)
```

## �📖 Documentação Completa

Para documentação detalhada, consulte:
- **docs/BACKUP_RESTORE.md** - Guia completo com casos reais
- **docs/COMMANDS.md** - Comandos manuais
- **scripts/database/examples.sh** - Exemplos práticos

## 🚨 Importante

⚠️ **Sempre teste scripts em ambiente de desenvolvimento antes de usar em produção!**

⚠️ **Faça backup de segurança antes de qualquer operação de restore!**

✅ **SSH sem senha configurado** para automação de backup remoto
