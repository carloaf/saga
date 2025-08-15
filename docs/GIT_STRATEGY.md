# SAGA - Estratégia de Commits e Branches
# Guia detalhado para organização do código

## 🌳 ESTRATÉGIA DE BRANCHES

### Estrutura Hierárquica
```
main (PRODUÇÃO)
├── release/v1.2.0 (preparação de release)
├── dev (STAGING/DESENVOLVIMENTO)
│   ├── feature/auth-oauth-improvements
│   ├── feature/dashboard-analytics  
│   ├── feature/mobile-responsive
│   ├── bugfix/session-timeout
│   ├── bugfix/booking-validation
│   └── hotfix/security-patch-critical
└── docs/update-readme (documentação)
```

### Descrição das Branches

#### 🏭 `main` - Branch de Produção
- **Status**: Sempre estável e deployável
- **Proteção**: Somente merge via Pull Request
- **CI/CD**: Deploy automático para produção
- **Tests**: Todos os testes devem passar
- **Review**: Code review obrigatório

#### 🔧 `dev` - Branch de Desenvolvimento/Staging  
- **Status**: Integração contínua das features
- **Deploy**: Automático para staging (localhost:8080)
- **Tests**: Bateria completa de testes
- **Purpose**: Validação antes da produção

#### ⭐ `feature/*` - Novas Funcionalidades
- **Nomenclatura**: `feature/descricao-da-funcionalidade`
- **Base**: Sempre criada a partir de `dev`
- **Desenvolvimento**: Ambiente local (localhost:8000)
- **Lifespan**: Apagada após merge para `dev`

#### 🐛 `bugfix/*` - Correções de Bugs
- **Nomenclatura**: `bugfix/descricao-do-problema`
- **Base**: Criada a partir de `dev`
- **Priority**: Merge prioritário
- **Testing**: Validação obrigatória em staging

#### 🚨 `hotfix/*` - Correções Críticas
- **Nomenclatura**: `hotfix/descricao-critica`
- **Base**: Criada a partir de `main`
- **Deploy**: Direto para produção após validação
- **Merge**: Para `main` E `dev` simultaneamente

#### 📦 `release/*` - Preparação de Releases
- **Nomenclatura**: `release/v1.2.0`
- **Base**: Criada a partir de `dev`
- **Purpose**: Stabilization e final testing
- **Merge**: Para `main` quando pronta

## 💬 ESTRATÉGIA DE COMMITS

### Conventional Commits
Seguimos o padrão [Conventional Commits](https://www.conventionalcommits.org/) para mensagens estruturadas:

```
<tipo>[escopo opcional]: <descrição>

[corpo opcional]

[rodapé opcional]
```

### Tipos de Commit

#### 🎉 `feat:` - Nova Funcionalidade
```bash
feat(auth): implementa autenticação OAuth com Google
feat(booking): adiciona validação de horários de refeição
feat(dashboard): cria gráficos de estatísticas mensais
```

#### 🐛 `fix:` - Correção de Bug
```bash
fix(login): corrige redirecionamento após logout
fix(booking): resolve problema de timezone
fix(database): corrige migration de users table
```

#### 📚 `docs:` - Documentação
```bash
docs: atualiza README com instruções de instalação
docs(api): adiciona documentação das rotas
docs: cria guia de desenvolvimento multi-arch
```

#### 🎨 `style:` - Formatação de Código
```bash
style: aplica formatação PSR-12 nos controllers
style(frontend): organiza imports CSS
style: remove trailing whitespaces
```

#### ♻️ `refactor:` - Refatoração
```bash
refactor(booking): simplifica lógica de validação
refactor: move helpers para classes utilitárias
refactor(database): otimiza queries do dashboard
```

#### ⚡ `perf:` - Melhoria de Performance
```bash
perf(database): adiciona índices nas tabelas principais
perf(frontend): implementa lazy loading de imagens
perf: otimiza cache do Redis
```

#### ✅ `test:` - Testes
```bash
test(booking): adiciona testes unitários para validação
test: implementa testes de integração do login
test(api): adiciona testes para endpoints REST
```

#### 🔧 `chore:` - Manutenção
```bash
chore: atualiza dependências do Laravel
chore(docker): otimiza Dockerfile
chore: adiciona script de build multi-arch
```

#### 🚀 `ci:` - CI/CD
```bash
ci: adiciona workflow do GitHub Actions
ci(docker): configura build automático
ci: implementa deploy automático para staging
```

### Escopo (Opcional)
Especifica a área afetada pela mudança:

- `auth` - Autenticação e autorização
- `booking` - Sistema de agendamento
- `dashboard` - Dashboard e relatórios
- `user` - Gestão de usuários
- `database` - Migrations e seeds
- `frontend` - Interface e componentes
- `docker` - Containers e deploy
- `api` - Endpoints e APIs

## 📝 EXEMPLOS PRÁTICOS

### Cenário 1: Nova Funcionalidade
```bash
# 1. Criar branch
git checkout dev
git pull origin dev
git checkout -b feature/booking-weekly-menu

# 2. Desenvolver com commits frequentes
git add .
git commit -m "feat(booking): cria modelo WeeklyMenu"

git add .
git commit -m "feat(booking): implementa CRUD de cardápio semanal"

git add .
git commit -m "feat(frontend): adiciona interface de gestão de cardápio"

git add .
git commit -m "test(booking): adiciona testes para WeeklyMenu"

# 3. Push e Pull Request
git push origin feature/booking-weekly-menu
# Criar Pull Request no GitHub/GitLab
```

### Cenário 2: Correção de Bug
```bash
# 1. Criar branch
git checkout dev
git pull origin dev
git checkout -b bugfix/session-timeout-redirect

# 2. Corrigir e testar
git add .
git commit -m "fix(auth): corrige redirecionamento após timeout de sessão

- Adiciona verificação de sessão expirada
- Redireciona para login com mensagem apropriada
- Testa cenário de timeout em diferentes browsers

Fixes #123"

# 3. Push e merge prioritário
git push origin bugfix/session-timeout-redirect
```

### Cenário 3: Hotfix Crítico
```bash
# 1. Criar branch a partir de main
git checkout main
git pull origin main
git checkout -b hotfix/security-sql-injection

# 2. Correção crítica
git add .
git commit -m "fix(security): previne SQL injection em booking queries

BREAKING CHANGE: atualiza validação de parâmetros de entrada

- Implementa prepared statements em todas as queries
- Adiciona sanitização de inputs do usuário
- Atualiza middleware de segurança

SECURITY: CVE-2025-001 - SQL Injection vulnerability"

# 3. Deploy imediato
git push origin hotfix/security-sql-injection
# Merge para main E dev
```

## 🔄 FLUXO DE INTEGRAÇÃO

### 1. Feature Development
```
feature/nova-funcionalidade
├── Commits frequentes e descritivos
├── Testes locais (localhost:8000)
├── Push para origin
└── Pull Request para dev
```

### 2. Code Review Process
```
Pull Request
├── Automated checks (CI/CD)
├── Code review por peer
├── Aprovação necessária
└── Merge para dev
```

### 3. Staging Validation
```
dev branch
├── Deploy automático para staging
├── Testes de QA (localhost:8080)
├── Validação de funcionalidades
└── Preparação para produção
```

### 4. Production Deploy
```
main branch
├── Merge de dev aprovado
├── Deploy para produção
├── Monitoramento ativo
└── Rollback disponível se necessário
```

## 🛡️ PROTEÇÕES E REGRAS

### Branch Protection Rules

#### main (Produção)
- ✅ Require pull request reviews
- ✅ Require status checks to pass
- ✅ Require branches to be up to date
- ✅ Restrict pushes that create new commits
- ✅ Require review from code owners

#### dev (Staging)
- ✅ Require pull request reviews
- ✅ Require status checks to pass
- ✅ Allow force pushes (com cuidado)

### Automated Checks
- ✅ **PHPStan**: Análise estática de código
- ✅ **PHP-CS-Fixer**: Formatação de código
- ✅ **PHPUnit**: Testes unitários e integração
- ✅ **Larastan**: Análise específica do Laravel
- ✅ **Security**: Verificação de vulnerabilidades

## 📊 FERRAMENTAS E MÉTRICAS

### Git Hooks
```bash
# Pre-commit: executa antes do commit
- Formatação de código (PHP-CS-Fixer)
- Análise estática (PHPStan)
- Testes unitários

# Pre-push: executa antes do push
- Testes de integração
- Build validation
- Security checks
```

### Métricas de Qualidade
- **Code Coverage**: Mínimo 80%
- **Complexity**: Máximo 10 por método
- **Duplication**: Máximo 3%
- **Security**: 0 vulnerabilidades críticas

## ✅ BOAS PRÁTICAS

### Commits
- ✅ **Atômicos**: Um commit = uma mudança lógica
- ✅ **Descritivos**: Mensagem clara do que foi feito
- ✅ **Frequentes**: Commits pequenos e regulares
- ✅ **Testados**: Sempre testar antes de commitar

### Branches
- ✅ **Curta duração**: Features não devem durar semanas
- ✅ **Atualizadas**: Sempre rebase/merge de dev antes de PR
- ✅ **Limpeza**: Deletar branches após merge
- ✅ **Nomenclatura**: Seguir padrão estabelecido

### Pull Requests
- ✅ **Descrição clara**: Template com checklist
- ✅ **Tamanho adequado**: Máximo 400 linhas alteradas
- ✅ **Self-review**: Revisar próprio código antes de PR
- ✅ **Screenshots**: Incluir prints de mudanças visuais

## 🎯 BENEFÍCIOS DA ESTRATÉGIA

### Para Desenvolvedores
- 🎯 **Clareza**: Histórico limpo e organizado
- 🔄 **Flexibilidade**: Múltiplas features em paralelo
- 🛡️ **Segurança**: Ambiente isolado para testes
- 📈 **Produtividade**: Workflow otimizado

### Para o Projeto
- 📊 **Rastreabilidade**: Histórico completo de mudanças
- 🚀 **Deploy seguro**: Validação em múltiplas etapas
- 🔧 **Manutenibilidade**: Código organizado e documentado
- 👥 **Colaboração**: Processo claro para toda equipe

---
📚 **Documentação relacionada**: 
- [Workflow de Desenvolvimento](DESENVOLVIMENTO_WORKFLOW.md)
- [Quick Reference](QUICK_REFERENCE.md)
