# 🚀 COMANDOS RÁPIDOS - SISTEMA SAGA

## Acesso Básico
```bash
# Login Teleport
tsh login --proxy=teleport.7cta.eb.mil.br --user=cleitonpaulo.martins@eb.mil.br

# SSH Servidor
tsh ssh suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO
```

## Desenvolvimento
```bash
# Iniciar desenvolvimento (primeira vez)
cd /home/augusto/workspace/remote/saga
./sync-saga.sh init

# Modo watch (sincronização automática)
./sync-saga.sh watch

# Abrir VSCode
code /home/augusto/workspace/remote/saga
```

## Docker Containers
```bash
# Status containers
tsh ssh suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO "docker ps"

# Logs aplicação
tsh ssh suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO "docker logs saga_app_port8080 -f"

# Reiniciar containers
tsh ssh suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO "cd /workspace/saga && docker-compose -f docker-compose-port8080.yml restart"
```

## Laravel Artisan
```bash
# Migrations
tsh ssh suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO "docker exec saga_app_port8080 php artisan migrate"

# Cache clear
tsh ssh suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO "docker exec saga_app_port8080 php artisan cache:clear"

# Console
tsh ssh suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO "docker exec -it saga_app_port8080 php artisan tinker"
```

## Banco de Dados
```bash
# Acessar PostgreSQL
tsh ssh suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO "docker exec -it saga_db_port8080 psql -U saga_user -d saga"
```

## URLs Importantes
- **Aplicação:** http://10.166.72.36:8080
- **Código:** /workspace/saga (servidor)
- **Local:** /home/augusto/workspace/remote/saga