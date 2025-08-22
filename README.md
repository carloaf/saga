# SAGA - Sistema de Agendamento e Gestão de Arranchamento

Sistema digital para gestão completa do processo de arranchamento (agendamento de refeições) em Organizações Militares.

## 📋 Sobre o Projeto

O SAGA é uma plataforma digital robusta, intuitiva e segura desenvolvida para substituir processos manuais de agendamento de refeições, oferecendo:

- ✅ **Agendamento Digital**: Interface moderna para marcação de café da manhã e almoço
- 📊 **Dashboard Gerencial**: Relatórios e estatísticas em tempo real
- 🔐 **Autenticação Segura**: Login via Google OAuth
- 📱 **Design Responsivo**: Funciona em desktop, tablet e mobile
- 👥 **Gestão de Usuários**: Controle de acesso por níveis (usuário/superusuário)

## 🏗️ Arquitetura

### Stack Tecnológica
- **Backend**: Laravel 11 + PHP 8.4
- **Frontend**: Blade Templates + Laravel Livewire + Tailwind CSS
- **Database**: PostgreSQL 16
- **Infraestrutura**: Docker + Apache
- **Autenticação**: Laravel Socialite (Google)
- **Charts**: Chart.js

### Estrutura do Projeto
```
saga/
├── app/
│   ├── Http/Controllers/     # Controllers principais
│   ├── Livewire/            # Componentes interativos
│   └── Models/              # Models Eloquent
├── database/
│   └── migrations/          # Estrutura do banco
├── resources/
│   ├── views/               # Templates Blade
│   ├── css/                 # Estilos Tailwind
│   └── js/                  # JavaScript/Livewire
├── docker/                  # Configurações Docker
└── public/                  # Assets públicos
```

## 🚀 Instalação e Configuração

### Pré-requisitos
- Docker e Docker Compose
- Git

### 1. Clone o Repositório
```bash
git clone <repository-url>
cd saga
```

### 2. Configuração do Ambiente
```bash
# Copie o arquivo de configuração
cp .env.example .env

# Configure as variáveis do Google OAuth no .env
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
```

### 3. Inicialização com Docker
```bash
# Construir e iniciar os containers
docker-compose up -d

# Instalar dependências PHP
docker-compose exec app composer install

# Instalar dependências Node.js
docker-compose exec app npm install

# Gerar chave da aplicação
docker-compose exec app php artisan key:generate

# Executar migrations
docker-compose exec app php artisan migrate

# Executar seeders (opcional)
docker-compose exec app php artisan db:seed
```

### 3.1 Build Multi-Arquitetura (Profissional)

O SAGA inclui scripts profissionais para build e deployment multi-arquitetura:

#### Build Rápido com Script
```bash
# Build multi-arch e push para registry
./scripts/deployment/build-multiarch.sh --push --tag v1.0.0 --registry your-registry.com

# Build apenas local (arquitetura atual)
./scripts/deployment/build-multiarch.sh --load --tag dev

# Build com cache (GitHub Actions)
./scripts/deployment/build-multiarch.sh --cache --push --tag latest
```

#### Build Manual (Método Tradicional)
```bash
# Criar e usar builder (uma vez)
docker buildx create --name saga-builder --use

# Verificar suporte
docker buildx inspect --bootstrap

# Build e push multi-arch
docker buildx build \
  --platform linux/amd64,linux/arm64 \
  -t seuusuario/saga:latest \
  --push .
```

#### Forçar Arquitetura em Desenvolvimento
```bash
export APP_PLATFORM=linux/amd64
# Descomentar linha 'platform:' no docker-compose.yml
docker-compose up -d
```

### 4. Configuração Pós-Instalação

#### 4.1 Instalação de Dependências
```bash
# Instalar dependências PHP (obrigatório)
docker run --rm -v "$(pwd)":/app composer:latest install --ignore-platform-req=ext-gd --ignore-platform-req=php

# Ou usando o container do projeto
docker exec saga_app_dev composer install
```

#### 4.2 Criação de Diretórios Laravel
```bash
# Criar estrutura de storage (obrigatório)
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
```

#### 4.3 Configuração de Permissões
```bash
# Configurar permissões nos containers (obrigatório após subir containers)
docker exec saga_app_dev chown -R www-data:www-data /var/www/html/storage
docker exec saga_app_dev chmod -R 755 /var/www/html/storage
docker exec saga_app_dev chown -R www-data:www-data /var/www/html/bootstrap/cache
docker exec saga_app_dev chmod -R 755 /var/www/html/bootstrap/cache

# Para ambiente de staging
docker exec saga_app_staging chown -R www-data:www-data /var/www/html/storage
docker exec saga_app_staging chmod -R 755 /var/www/html/storage
docker exec saga_app_staging chown -R www-data:www-data /var/www/html/bootstrap/cache
docker exec saga_app_staging chmod -R 755 /var/www/html/bootstrap/cache
```

#### 4.4 Configuração do Laravel
```bash
# Gerar chaves da aplicação
docker exec saga_app_dev php artisan key:generate
docker exec saga_app_staging php artisan key:generate

# Executar migrações
docker exec saga_app_dev php artisan migrate
docker exec saga_app_staging php artisan migrate

# Limpar caches
docker exec saga_app_dev php artisan view:clear
docker exec saga_app_dev php artisan config:cache
docker exec saga_app_staging php artisan view:clear
docker exec saga_app_staging php artisan config:cache
```

#### 4.5 Verificação de Funcionamento
#### 4.6 Usuário Admin Padrão
Para garantir a existência de um usuário administrador (login tradicional):
```bash
docker exec saga_app_dev php artisan saga:ensure-admin
# Credenciais padrão: admin@saga.mil.br / admin123 (altere após primeiro login)
```

Para alterar email ou senha:
```bash
docker exec saga_app_dev php artisan saga:ensure-admin --email=outro@saga.mil.br --password=NovaSenhaSegura123
```

Recomenda-se trocar a senha padrão imediatamente em produção.

```bash
# Testar ambientes
curl http://localhost:8000  # Development (deve retornar 200)
curl http://localhost:8080  # Staging (deve retornar 200)

# Verificar status dos containers
docker ps  # Todos devem estar "healthy"
```

### 5. Backup e Restore do Banco de Dados

#### 5.1 Inicialização com Dados Base
```bash
# Executar seeders (dados base: organizações, ranks)
./scripts/database/setup.sh dev     # Desenvolvimento
./scripts/database/setup.sh staging # Staging

# Verificar dados inseridos
docker exec saga_db psql -U saga_user -d saga -c "
SELECT 'organizations' as tabela, COUNT(*) as registros FROM organizations
UNION ALL SELECT 'ranks' as tabela, COUNT(*) as registros FROM ranks;"
```

#### 5.2 Backup Automático
```bash
# Backup completo de todos os ambientes
./scripts/database/backup.sh

# Arquivos gerados em backups/:
# - saga_dev_complete_YYYYMMDD_HHMMSS.sql.gz
# - saga_dev_data_YYYYMMDD_HHMMSS.sql.gz
# - saga_staging_complete_YYYYMMDD_HHMMSS.sql.gz
```

#### 5.3 Backup de Máquina Remota (SSH)
```bash
# Importar dados de produção via SSH
./scripts/database/remote-backup.sh usuario@servidor.com dev

# Exemplo real executado:
./scripts/database/remote-backup.sh sonnote@192.168.0.57 dev
# ✅ Importou 424 reservas + 31 usuários + 14 organizações
```

#### 5.4 Restore de Dados
```bash
# Restore completo (estrutura + dados)
./scripts/database/restore.sh dev backups/saga_dev_complete_20250815_210917.sql.gz

# Restore apenas dados
./scripts/database/restore.sh dev backups/saga_dev_data_20250815_210917.sql.gz data

# Restore específico (usuários ou reservas)
./scripts/database/restore.sh dev backups/saga_dev_users_20250815_210917.sql.gz users
```

#### 5.5 Status Atual dos Dados

**📊 Desenvolvimento (com dados reais de produção)**:
```bash
✅ Reservas: 424 registros (dados reais)
✅ Usuários: 31 registros (produção)  
✅ Organizações: 14 registros (militares)
✅ Cardápios: 3 registros (semanais)
🏢 Principal: 11º Depósito de Suprimento (15 usuários)
```

**📊 Staging (ambiente limpo para testes)**:
```bash
✅ Estrutura: Completa e funcional
⭕ Dados: Vazio (pronto para testes)
🌐 URL: http://localhost:8080
```

**📖 Documentação Completa**: [docs/BACKUP_RESTORE.md](docs/BACKUP_RESTORE.md)

### 6. Compilar Assets
```bash
# Para desenvolvimento
docker-compose exec app npm run dev

# Para produção
docker-compose exec app npm run build
```

## 🎯 Funcionalidades

### 👤 Usuário Padrão (Militar)
- Login via conta Google institucional
- Agendamento de refeições via calendário interativo
- Visualização do histórico pessoal
- Edição de perfil (dados não-críticos)

### 👑 Superusuário (Gestor/Administrador)
- Todas as funcionalidades do usuário padrão
- Dashboard com 4 gráficos principais:
  - 📈 Arranchados por dia
  - 🥧 Origem dos arranchados (própria OM vs outras OMs)
  - 📊 Comparativo café vs almoço
  - 🏆 Top 5 postos/graduações
- Gestão completa de usuários
- Geração de relatórios (PDF/Excel)

### 📅 Regras de Negócio
- ✅ Agendamento apenas em dias úteis (segunda a sexta)
- ⛔ Sábados e domingos bloqueados
- 🍳 Sextas-feiras: apenas café da manhã
- ⏰ Janela de agendamento: próximos 30 dias
- 💾 Salvamento automático via AJAX/Livewire

## 🗄️ Banco de Dados

### Principais Tabelas
- **users**: Dados dos militares (perfil completo)
- **bookings**: Agendamentos de refeições
- **ranks**: Postos e graduações
- **organizations**: Organizações militares
- **roles**: Sistema de permissões

### Relacionamentos Chave
- User → Rank (N:1)
- User → Organization (N:1)
- User → Bookings (1:N)
- Índice único: (user_id, booking_date, meal_type)

## 🔒 Segurança

- 🛡️ Proteção contra OWASP Top 10
- 🔐 Autenticação OAuth2 (Google)
- 📝 Logs de auditoria
- 🚫 Acesso restrito à rede interna
- ✅ Validação rigorosa de dados

## 📊 Monitoramento

### Métricas Principais
- Uptime: 99.9%
- Tempo de resposta: < 2s (páginas)
- Interações: < 500ms (calendário)
- Usuários ativos diários/mensais

## 🚀 Deploy

### Estrutura de Deploy Profissional

```
deploy/
├── production/
│   ├── docker-compose.prod.yml    # Configuração produção
│   └── .env.production            # Variáveis ambiente prod
└── staging/
    ├── docker-compose.staging.yml # Configuração staging  
    └── .env.staging               # Variáveis ambiente staging

scripts/
└── deployment/
    ├── build-multiarch.sh         # Build multi-arquitetura
    ├── deploy-production.sh       # Deploy automatizado
    └── cleanup-project.sh         # Limpeza do projeto
```

### Deploy Automatizado

#### Produção
```bash
# Deploy completo (build + deploy)
./scripts/deployment/deploy-production.sh deploy

# Build multi-arch e deploy
MULTI_ARCH=true ./scripts/deployment/deploy-production.sh deploy

# Rollback para versão anterior
./scripts/deployment/deploy-production.sh rollback

# Monitoramento
./scripts/deployment/deploy-production.sh status
./scripts/deployment/deploy-production.sh logs
```

#### Staging
```bash
cd deploy/staging
docker-compose -f docker-compose.staging.yml up -d
```

### Deploy Manual - Produção
```bash
cd deploy/production

# Configurar environment
cp .env.production.example .env.production
# Editar .env.production com valores reais

# Build e deploy
docker-compose -f docker-compose.prod.yml up -d

# Setup inicial
docker-compose -f docker-compose.prod.yml exec app php artisan key:generate --force
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
```

### Limpeza do Projeto
```bash
# Dry run (visualizar o que seria removido)
./scripts/deployment/cleanup-project.sh

# Executar limpeza
./scripts/deployment/cleanup-project.sh --force

# Limpeza completa (inclui node_modules e vendor)
CLEAN_DEPS=true ./scripts/deployment/cleanup-project.sh --force
```

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

## 📄 Licença

## 📚 Documentação

### Guias de Desenvolvimento
- 📖 **[Workflow de Desenvolvimento](docs/DESENVOLVIMENTO_WORKFLOW.md)** - Guia completo para desenvolvimento com multi-arquitetura
- ⚡ **[Quick Reference](docs/QUICK_REFERENCE.md)** - Comandos e referência rápida para desenvolvedores
- 🏗️ **[Histórico de Implementação](docs/IMPLEMENTACAO_MULTI_ARCH_HISTORY.md)** - Detalhes da implementação multi-arquitetura

### Ambientes Disponíveis
```bash
DESENVOLVIMENTO: http://localhost:8000  # Hot reload ativo
STAGING:        http://localhost:8080   # Ambiente de QA/testes  
PRODUÇÃO:       porta 80               # Ambiente otimizado
```

### Estrutura de Branches
```
main (produção) ←── dev (staging) ←── feature/nova-funcionalidade
```

## 📝 Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## 📞 Suporte

Para dúvidas e suporte:
- 📧 Email: [seu-email@exemplo.com]
- 📖 Documentação: [docs/](docs/)
- 🐛 Issues: [link-issues]

---

**SAGA v1.0** - Desenvolvido com ❤️ para as Forças Armadas
