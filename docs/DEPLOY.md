# SAGA - Sistema de Agendamento e Gestão de Arranchamento

## 🚀 Deploy e Branches

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
   ./deploy.sh
   ```

### Deploy Rápido

Execute o script de deploy:
```bash
./deploy.sh
```

Este script irá:
- Verificar se está na branch main
- Fazer pull das últimas mudanças
- Rebuildar containers Docker
- Executar migrations
- Limpar caches
- Verificar se aplicação está funcionando

### URLs do Sistema

- **Produção**: http://localhost:8000
- **Login**: http://localhost:8000/login
- **Registro**: http://localhost:8000/register
- **Admin**: http://localhost:8000/admin

### Comandos Docker Úteis

```bash
# Ver status dos containers
docker-compose ps

# Ver logs
docker-compose logs -f

# Parar containers
docker-compose down

# Reiniciar containers
docker-compose restart

# Rebuild completo
docker-compose up -d --build
```

## 🛠️ Tecnologias

- Laravel 11
- PHP 8.4
- PostgreSQL
- Redis
- Docker
- Tailwind CSS
- Livewire
