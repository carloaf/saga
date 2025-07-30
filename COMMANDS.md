# SAGA - Comandos Úteis

## Scripts de Automação

### Sincronização de Branches
```bash
# Sincronizar dev ↔ main e fazer push
./sync-branches.sh

# Ver ajuda do script
./sync-branches.sh --help
```

### Deploy Rápido
```bash
# Menu interativo com opções:
# 1. Sincronizar + Deploy
# 2. Apenas sincronizar
# 3. Apenas deploy
./quick-deploy.sh
```

### Deploy para Produção
```bash
# Deploy completo para servidor
./deploy.sh
```

### Debug de Autenticação
```bash
# Testar sistema de login
./debug-login.sh
```

## Desenvolvimento

### Iniciar o projeto
```bash
./setup.sh
```

### Comandos Docker úteis
```bash
# Iniciar todos os serviços
docker-compose up -d

# Ver logs da aplicação
docker-compose logs -f app

# Parar todos os serviços
docker-compose down

# Reconstruir containers
docker-compose build --no-cache
```

### Comandos Laravel
```bash
# Executar migrations
docker-compose exec app php artisan migrate

# Executar seeders
docker-compose exec app php artisan db:seed

# Limpar cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear

# Gerar chave da aplicação
docker-compose exec app php artisan key:generate

# Acessar Tinker (console Laravel)
docker-compose exec app php artisan tinker
```

### Comandos Node.js/Frontend
```bash
# Instalar dependências
docker-compose exec app npm install

# Compilar assets para desenvolvimento
docker-compose exec app npm run dev

# Compilar assets para produção
docker-compose exec app npm run build

# Watch mode (desenvolvimento)
docker-compose exec app npm run dev -- --watch
```

## Configuração Inicial

### 1. Configurar Google OAuth
1. Acesse o [Google Cloud Console](https://console.cloud.google.com/)
2. Crie um novo projeto ou selecione um existente
3. Habilite a API do Google+ 
4. Configure a tela de consentimento OAuth
5. Crie credenciais OAuth 2.0:
   - Tipo: Aplicação Web
   - URIs de redirecionamento: `http://localhost:8000/auth/google/callback`
6. Copie o Client ID e Client Secret para o arquivo `.env`

### 2. Criar primeiro superusuário
```bash
docker-compose exec app php artisan tinker
```

No Tinker:
```php
$user = User::where('email', 'seu_email@gmail.com')->first();
$user->assignRole('superuser');
exit
```

## Backup e Restore

### Backup do banco
```bash
docker-compose exec database pg_dump -U saga_user saga > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restore do banco
```bash
docker-compose exec -T database psql -U saga_user saga < backup_file.sql
```

## Logs e Monitoramento

### Ver logs
```bash
# Logs da aplicação
docker-compose logs -f app

# Logs do banco
docker-compose logs -f database

# Logs do Redis
docker-compose logs -f redis

# Todos os logs
docker-compose logs -f
```

### Monitorar performance
```bash
# Status dos containers
docker-compose ps

# Uso de recursos
docker stats
```

## Troubleshooting

### Problemas comuns

1. **Erro de permissão no Laravel**
```bash
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 755 /var/www/html/storage
```

2. **Banco não conecta**
```bash
# Verificar se o banco está rodando
docker-compose ps database

# Resetar banco
docker-compose down
docker volume rm saga_postgres_data
docker-compose up -d database
```

3. **Assets não compilam**
```bash
# Limpar node_modules e reinstalar
docker-compose exec app rm -rf node_modules package-lock.json
docker-compose exec app npm install
```

4. **Erro de chave da aplicação**
```bash
docker-compose exec app php artisan key:generate
```

## URLs Importantes

- **Aplicação**: http://localhost:8000
- **Banco PostgreSQL**: localhost:5432
- **Redis**: localhost:6379

## Estrutura de Pastas

```
saga/
├── app/                 # Código da aplicação Laravel
├── database/           # Migrations, seeders, factories
├── docker/             # Configurações Docker
├── public/             # Assets públicos
├── resources/          # Views, CSS, JS
├── routes/             # Rotas da aplicação
├── storage/            # Logs, cache, uploads
└── tests/              # Testes automatizados
```
