# SAGA - Scripts de Backup

## 🎯 Guia Rápido

### Backup Completo
```bash
./scripts/backup-docker.sh full
```

### Listar Backups
```bash
ls -lh backups/*.gz
```

### Backup Automático (Cron)
```bash
# Adicionar ao crontab
crontab -e

# Backup diário às 3h
0 3 * * * /home/sonnote/Documents/saga/scripts/backup-docker.sh full >> /home/sonnote/Documents/saga/backups/cron.log 2>&1
```

## 📖 Documentação Completa
Ver: `docs/backup-strategy.md`

## 🔧 Scripts Disponíveis
- `backup-docker.sh` - Backup via Docker (recomendado)
- `backup-database.sh` - Backup direto PostgreSQL
- `restore-database.sh` - Restauração de backups

## ⚡ Uso Rápido

```bash
# Modo interativo (menu)
./scripts/backup-docker.sh

# Backup específico
./scripts/backup-docker.sh [full|data|schema|critical]
```
