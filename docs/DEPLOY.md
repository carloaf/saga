# SAGA - Sistema de Agendamento e Gestão de Arranchamento

## 🚀 Deploy e Branches

> Nota: este documento resume o fluxo de deploy. Para o procedimento operacional detalhado de homologação no CTA, consulte `docs/DESENVOLVIMENTO_WORKFLOW.md`.

### Estrutura de Branches
O projeto utiliza duas branches principais:

- **`main`** - Branch principal de produção
  - Código estável e testado
  - Deploy automático para produção
  - Todas as features prontas para uso

- **`dev`** - Branch de desenvolvimento
  - Novas features em desenvolvimento
  - Testes e experimentos
  - Integração contínua

### Fluxo de Trabalho

1. **Desenvolvimento**: Trabalhe na branch `dev`
   ```bash
   git checkout dev
   git pull origin dev
   # Fazer alterações
   git add .
   git commit -m "feat: nova funcionalidade"
   git push origin dev
   ```

2. **Deploy para Produção**: Merge da `dev` para `main`
   ```bash
   git checkout main
   git merge dev
   git push origin main

  cd deploy/production
  ./deploy-production.sh deploy
   ```

3. **Homologação no CTA**: Publicação manual no host de homologação
  ```bash
  tsh login --proxy=teleport.7cta.eb.mil.br --user=cleitonpaulo.martins@eb.mil.br
  tsh ssh suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO "ls -la /workspace/saga/"

  # Copiar apenas os arquivos alterados
  tsh scp app/Models/User.php suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO:/workspace/saga/app/Models/User.php

  # Ativar no container com bind mount
  tsh ssh suporte@VM-7CTA-11DSUP-ARRANCHAMENTO-HOMOLOGACAO "docker exec saga_app_dev php artisan optimize:clear && docker restart saga_app_dev"
  ```

  Observações:
  - O código remoto de homologação fica em `/workspace/saga`.
  - Se o `docker-compose` remoto não reconhecer o serviço `app`, use `docker exec` e `docker restart saga_app_dev` diretamente.
  - A suíte `php artisan test` no host depende de um banco de testes dedicado.

### Deploy Rápido

Execute o script de deploy:
```bash
cd deploy/production
./deploy-production.sh deploy
```

Este script irá:
- Verificar se está na branch main
- Fazer pull das últimas mudanças
- Rebuildar containers Docker
- Executar migrations
- Limpar caches
- Verificar se aplicação está funcionando

### URLs do Sistema

- **Desenvolvimento**: http://localhost:8000
- **Homologação**: http://localhost:8080
- **Produção**: http://localhost
- **Login**: http://localhost/login
- **Registro**: http://localhost/register
- **Admin**: http://localhost/admin

### Comandos Docker Úteis

```bash
# Ver status dos containers
docker compose ps

# Ver logs
docker compose logs -f

# Parar containers
docker compose down

# Reiniciar containers
docker compose restart

# Rebuild completo
docker compose up -d --build
```

## 🛠️ Tecnologias

- Laravel 11
- PHP 8.4
- PostgreSQL
- Redis
- Docker
- Tailwind CSS
- Livewire
