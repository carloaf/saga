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

### 4. Compilar Assets
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

### Ambiente de Produção
```bash
# Build da aplicação
docker-compose -f docker-compose.prod.yml build

# Deploy
docker-compose -f docker-compose.prod.yml up -d

# Otimizações
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## 📞 Suporte

Para dúvidas e suporte:
- 📧 Email: [seu-email@exemplo.com]
- 📖 Documentação: [link-documentacao]
- 🐛 Issues: [link-issues]

---

**SAGA v1.0** - Desenvolvido com ❤️ para as Forças Armadas
