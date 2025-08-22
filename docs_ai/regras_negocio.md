# Regras de Negócio do Projeto SAGA

Este documento consolida as regras de negócio já implementadas (ou em implementação avançada) no SAGA, servindo como referência única para produto, desenvolvimento, QA e operações.

> STATUS: Versão 0.2 (atualizado após inclusão jantar em relatórios, status Laranjeira e melhorias serviço sgtte). Atualize a cada nova regra implementada ou alteração relevante.

## Sumário
1. Identidade / Autenticação
2. Perfis e Acesso
3. Regras de Agendamento (Bookings)
4. Cardápio Semanal (WeeklyMenu)
5. Estrutura de Dados (Modelo Relacional)
6. Fluxo de Deploy e Ambientes
7. Validações de Staging / QA
8. Deadlines e Janelas de Validação
9. Versionamento e Estratégia de Branches
10. Backup & Restore (Regras Operacionais)
11. Multi-Arquitetura (x64 / ARM)
12. Padronização de Commits e Proteções
13. Checklist de Entrega
14. Próximos Passos / Gaps
15. Histórico de Evolução (Commits‑chave)

---
## 1. Identidade / Autenticação
- Autenticação centralizada via Laravel (guards padrão). 
- Sessão expira conforme configuração padrão de `config/session.php` (manter alinhado a requisitos de segurança).
- Acesso a rotas protegidas exige autenticação (middleware `auth`).
 - Campo de Identidade (IDT) obrigatório no cadastro de novos usuários (máx 30 chars) e único (enforcement fase 2). Validado em `AdminController@storeUser` (`required|unique:users,idt`).
 - IDT é imutável após criação: tentativas de alteração via edição administrativa são ignoradas (hard lock em `updateUser`); interface de Perfil exibe o valor em modo somente leitura.
 - IDT aceita somente números: validação frontend (pattern="[0-9]*", inputmode="numeric") e JavaScript remove caracteres não numéricos automaticamente.
 - Estratégia de migração em duas fases:
   1. Fase 1: adiciona coluna nullable (sem unique) para permitir backfill (`2025_08_21_120000_add_idt_to_users_table`).
   2. Fase 2: normaliza/backfill, torna NOT NULL + UNIQUE (`2025_08_21_130000_make_idt_unique_not_nullable_on_users_table`).
 - Backfill padrão: `UPDATE users SET idt = CONCAT('PENDENTE_', id)` para registros sem valor antes da fase 2; ajustar manualmente depois.

## 2. Perfis e Acesso
Perfis atuais (campo `users.role`, constraint CHECK): `user`, `manager`, `aprov`, `furriel`, `sgtte`.

Status Especial (campo `users.status`): atualmente suportado valor `Laranjeira`.
| Status | Objetivo | Efeito em Regras |
|--------|----------|------------------|
| Laranjeira | Identificar militar com direito ampliado de arranchamento | Pode auto‑reservar jantar e realizar reservas (café, almoço, jantar) também em fins de semana (exceção às demais restrições); demais usuários continuam limitados a dias úteis e sem jantar próprio |

| Role | Objetivo | Principais Permissões | Restrições |
|------|----------|-----------------------|-----------|
| user | Militar padrão | Criar/visualizar/cancelar (futuro) suas reservas; ver perfil; estatísticas pessoais | Não acessa administração, relatórios, cardápio |
| furriel | Apoio operacional (lançamento para terceiros) | Criar reservas em nome de Soldados EV (campo `created_by_furriel`), gerenciar arranchamento companhia (café/almoço) | Não edita cardápio; não gerencia usuários; sem jantar |
| sgtte | Sargenteante / Serviço | Arranchar qualquer militar da própria subunidade (café/almoço/jantar) sem restrição de horário; interface Serviço com busca | Não gerencia usuários; não edita cardápio; auditoria via `created_by_operator` |
| manager | Gestão administrativa | CRUD de usuários (`AdminController@users/storeUser/updateUser/toggleUserStatus`), acesso a relatórios (`reports`, `generateReport`) | Não edita cardápio semanal (restrito a aprov) |
| aprov | Acesso ampliado / curadoria | Visualizar reservas, estatísticas, relatórios; gerenciar cardápio semanal (`CardapioController`); (NÃO gerencia usuários) | Uso restrito; número limitado de contas |

Regras Gerais:
- Qualquer rota sensível valida `Auth::user()->role` explicitamente.
- Elevação de privilégios somente via manager ou migração controlada.
- Perfis adicionais exigem atualização da constraint `users_role_check` + testes.
 - IMPORTANTE: Após introdução de novo perfil (ex: `sgtte`) aplicar a migration correspondente para atualizar a constraint `users_role_check`; caso contrário operações UPDATE/INSERT falharão com SQLSTATE 23514.

Critérios de Segurança:
- Ações de escrita exigem sessão autenticada e role apropriado (abort 403 em caso negativo).
- Futuro: Log de auditoria para criação/alteração de cardápios e reservas por terceiros (TODO).

Indicadores de Evolução Futuras (Gaps):
- Introduzir política granular baseada em Gates/Policies Laravel ao invés de condicionais diretos.
- Atributo `is_active=false` bloqueia login funcional (já usado em filtros administrativos).

## 3. Regras de Agendamento (Bookings)
Origem: `Booking` model + migrations `create_bookings_table`, `add_status_to_bookings_table`, `add_created_by_furriel_to_bookings_table`.

Regras Funcionais (conforme modelos + commits):
1. Estrutura: `user_id`, `booking_date` DATE, `meal_type` enum (breakfast|lunch|dinner), `status` enum (confirmed|cancelled|pending), `created_by_furriel` (nullable), `created_by_operator` (nullable – ações do sgtte), timestamps. Jantar integrado ao fluxo geral (não mais exclusivo do serviço) condicionado ao status/permissões.
2. Restrição única `(user_id, booking_date, meal_type)` impede duplicidade por refeição/dia.
3. Tipos ativos: breakfast (Café), lunch (Almoço) e dinner (Jantar). Usuários padrão só podem criar breakfast/lunch em dias úteis; jantar e fins de semana apenas via exceções (status Laranjeira ou lançamento por sgtte/furriel conforme política operacional).
4. Sexta-feira: somente breakfast (almoço indisponível) — coerente com `WeeklyMenu::getDefaultMenuStructure`. Jantar segue política normal (permitido para Laranjeira/serviço; almoço continua indisponível universalmente na sexta).
5. Dias permitidos:
  - Usuário com status Laranjeira: pode reservar café, almoço e jantar também em sábados e domingos.
  - Demais usuários: apenas dias úteis; fins de semana bloqueados (exceto se reserva inserida por papel operacional autorizado no futuro — gap a definir).
6. Deadline diário (commit c824a4c): após 13h não é possível reservar para o dia SEGUINTE (aplica-se a todos os tipos de refeição elegíveis do usuário).
7. Bloqueio de same-day (commit a44a25b): não é permitido criar reserva para o próprio dia (mesmo antes das 13h) — exceções operacionais futuras podem liberar para sgtte/furriel (gap).
8. Janela futura: até 30 dias corridos (confirmar enforcement programático completo; frontend já limita; adicionar validação backend se ausente).
9. Estados de workflow:
  - confirmed (padrão)
  - cancelled (mantém histórico; requer endpoint/ação consistente de cancelamento)
  - pending (placeholder não usado)
10. Campo `created_by_furriel`: identifica reservas criadas por furriel / superior; `created_by_operator`: ações via interface de serviço (sgtte).
11. Relatórios (AdminController) abrangem café, almoço e jantar (incluindo novas métricas incorporadas ao dashboard para jantar) — atualização commits 8748979 (exportações) e 407b83f (cards dashboard).

Validações Implementadas / Esperadas:
- Datas passadas: bloqueio.
- Fins de semana: bloqueio padrão exceto usuários status Laranjeira.
- Almoço nas sextas: bloqueio universal (todas as roles/status) – jantar permanece permitido para elegíveis.
- Mesma data (same-day): bloqueio universal.
- Deadline 13h D+1: após 13:00 reservas para o próximo dia (útil ou fim de semana se Laranjeira) são rejeitadas.
- Janela máxima (30 dias): conferir enforcement backend.
- Jantar: auto-reserva somente se (a) status Laranjeira ou (b) lançado por sgtte/furriel (política atual: sgtte pode para subunidade; furriel foco café/almoço – confirmar se furriel também insere jantar; se não, documentar como restrição futura).

Relatórios e Métricas:
- Estatísticas diárias/semanais/mensais (AdminController: métodos `getSummaryData`, `getOrganizationBreakdownData`, etc.).
- Exportações suportam formatos PDF e Excel (tipos: daily_meals, weekly_summary, monthly_summary, organization_breakdown, user_activity).

Riscos / Gaps:
- Índice composto sugerido: (`booking_date`, `meal_type`).
- Política de cancelamento (cutoff) não definida.
- Estado `pending` ocioso.
- Auditoria ampliar: registrar também ações jantar via sgtte (`created_by_operator`).
- Timezone explícito para cálculo de 13h (America/Sao_Paulo) garantir consistência inclusive em horário de verão.
- Clarificar se furriel pode lançar jantar; hoje interface principal cobre café/almoço (gap de alinhamento operacional).

## 4. Cardápio Semanal (WeeklyMenu)
Origem: Model `WeeklyMenu` e `CardapioController`.

Regras (conforme commits 57fb347, 03defef, aea6282, ba372a2):
1. Visualização: Acesso universal via dashboard para todos os roles (commit ba372a2). 
2. Edição: Exclusiva de `aprov` (rotas protegidas abort 403 para demais).
3. Estrutura base: segunda–quinta `cafe` + `almoco`; sexta apenas `cafe` (sem almoço) — fonte de verdade para bloqueio de almoço em sextas.
4. Semana alvo: segunda corrente; se hoje for >= sexta, edição direciona para semana seguinte.
5. Sugestões: seletor de semanas (−4 passadas, +8 futuras) + carregamento de sugestões da semana anterior (commit aea6282) com opção de aplicar por dia ou toda semana.
6. Validação formulário: campos obrigatórios (max 500 chars) exceto almoço sexta.
7. Persistência: `updateOrCreate` mantém somente uma versão ativa (`is_active=true`) por `week_start`.
8. UX: feedback visual (toasts, combinações de confirmação) para evitar sobrescrever acidentalmente conteúdo (commit aea6282). 

Gaps / Extensões Futuras:
- Auditoria detalhada (snapshot diff por dia).
- Estados de publicação: rascunho vs publicado.
- Trava condicional de reservas se semana alvo sem cardápio aprovado.
- Histórico versionado para análise nutricional / retroalimentação.

## 5. Estrutura de Dados (Modelo Relacional)

Entidades Principais:
| Tabela | Campos Relevantes | Regras / Notas |
|--------|-------------------|----------------|
| users | google_id (unique), idt (unique, imutável pós-criação, somente números), full_name, war_name, email (unique), rank_id FK, organization_id FK (pode se tornar nullable), gender (M/F no controller; migration antiga male/female – ajustar convergência), ready_at_om_date, role (CHECK), is_active, status | Constraint dinâmica atualizada para incluir `furriel` e `sgtte`; divergência enum gender (corrigir); campo `status` (commit 407b83f); IDT obrigatório novos cadastros e exibido read-only no perfil, aceita apenas números |
| ranks | name (unique), abbreviation (nullable), order | Usado para ordenar exibição hierárquica |
| organizations | name (unique), abbreviation (nullable), is_host (bool) | Campo `is_host` identifica OM hospedeira |
| bookings | user_id FK, booking_date (DATE), meal_type enum, status enum, created_by_furriel (nullable FK), created_by_operator (nullable FK), unique(user_id, booking_date, meal_type) | Estados de workflow simples; sem soft deletes; `created_by_operator` genérico para ações de sgtte |
| weekly_menus | week_start (DATE), menu_data (JSON->array), is_active (bool), created_by, updated_by | Um ativo por semana; sem versionamento histórico detalhado |

Relacionamentos:
- User belongsTo Rank, Organization.
- Booking belongsTo User; createdByFurriel (User opcional).
- WeeklyMenu creator/updater -> User.

Integridade:
- FKs padrão; `created_by_furriel` onDelete set null.
- Unique indexes garantem integridade de reservas e nomes.

Indices Potenciais (Avaliar):
- bookings(booking_date, meal_type) para relatórios.
- users(is_active, role) para dashboards.

## 6. Fluxo de Deploy e Ambientes
Fundamentado nos commits 0553028, d2efe59 (multi-arch / estrutura profissional).

Componentes:
- Ambientes separados: dev (8000), staging (8080), production (80/SSL).
- Docker multi-stage e multi-arch (linux/amd64, linux/arm64).
- Arquivos específicos em `deploy/production` e `deploy/staging`.
- Scripts de automação em `scripts/deployment` e `scripts/development`.

Regras:
- Todo merge em `dev` dispara ciclo de validação manual/automático antes de promoção.
- Promoção para produção somente com checklist (seção 13) concluído.
- Manter paridade de versões de imagem (tag semântica + hash curta opcional).
- Backups obrigatórios antes de migrações destrutivas em produção.

Gaps:
- Formalizar pipeline CI (lint, test, build multi-arch, scan de vulnerabilidades, push, deploy). 
- Assinatura de imagens (cosign) futura.

## 7. Validações de Staging / QA
- Testes unit/integration verdes.
- Verificação manual de regras críticas (deadline 13h, bloqueio sexta almoço, bloqueio same-day, bloqueio fins de semana).
- Exportações PDF/Excel gerando arquivos válidos (abrir e validar colunas / acentuação).
- Cardápio: fluxo de edição + sugestões multi-semana.
- Perfis: conferir restrição de acesso (aprov não gerencia usuários; manager não edita cardápio).
- Scripts de backup/restore executados com sucesso (dry-run + restore em staging). 

Resultado: marcação "validado" antes de criar release.

## 8. Deadlines e Janelas de Validação
Deadlines de agendamento (resumo operacional):
| Regra | Descrição | Fonte (Commit) | Observações |
|-------|-----------|----------------|-------------|
| Same-day bloqueado | Não permitir reserva para hoje | a44a25b | Possível override futuro para sgtte/furriel |
| Almoço sexta indisponível | Sexta só breakfast (almoço bloqueado) | Estrutura + 59762a2 | Jantar permitido se usuário elegível |
| Fim de semana bloqueado (padrão) | Sáb/Dom não permitidos para usuários padrão | 59762a2 | Exceção: status Laranjeira pode reservar |
| Deadline 13h D+1 | Após 13:00 não agenda próximo dia (útil ou fim de semana permitido) | c824a4c | Uniformizar timezone America/Sao_Paulo |
| Janela futura 30 dias | Limitar horizonte de reservas | Regra declarada | Validar backend enforcement |
| Jantar restrito | Auto-reserva somente Laranjeira; outros via serviço | Introdução status | Garantir mensagens claras na UI |

Validação de release: seguir `DEADLINE_VALIDACAO.md` para macro-janelas.

## 9. Versionamento e Estratégia de Branches
Ver `docs/GIT_STRATEGY.md`.

Fluxo atual:
- feature/* -> PR -> dev
- (opcional) release/* -> estabilização -> main
- hotfix/* (a partir de main) -> main -> back-merge dev

Commits: Conventional Commits + escopo (ex: feat(cardapio): ...). BREAKING CHANGE exige nota descrevendo migração.

## 10. Backup & Restore (Regras Operacionais)
Baseado no commit 2eeca08.

Backup:
- Tipos: completo / dados / por tabela.
- Retenção: >30 dias são limpos.
- Compressão padrão (.gz) + naming padronizado com timestamp.

Restore:
- Backup de segurança antes de aplicar.
- Confirmação interativa para operações destrutivas.
- Validação de integridade pós-restore.
- Execução de migrações pendentes se necessário.

Procedimento Pré-Deploy:
- Executar backup completo atualizado <24h.
- Validar restauração em staging (amostra) periodicamente.

## 11. Multi-Arquitetura (x64 / ARM)
Commits: 0553028, d2efe59, 03defef (ajustes estruturais indiretos).

Regras:
- Build multi-arch obrigatório para imagens de release.
- Teste mínimo x64 + ARM (script / guia docs/IMPLEMENTACAO_MULTI_ARCH_HISTORY.md).
- Evitar dependências nativas não multi-arch sem fallback.
- Documentar anomalias específicas de arquitetura (ex: performance de I/O) se surgirem.

## 12. Padronização de Commits e Proteções
- Conventional Commits + escopo.
- Hooks (futuro): lint, testes rápidos antes de push.
- Proteções PR: revisão obrigatória + checagens CI verdes.
- Sem commits diretamente em `main` (exceto hotfix emergencial documentado).

## 13. Checklist de Entrega
Antes de promover para produção:
- [ ] Testes unitários verdes
- [ ] Build Docker multi-arch bem-sucedido
- [ ] Backup recente validado (< 24h)
- [ ] Variáveis de ambiente revisadas
- [ ] Migrações aplicadas em staging
- [ ] Logs sem erros críticos (janela definida, ex: últimos 30m)
- [ ] Documentação atualizada (incluindo este arquivo)
- [ ] Acessos / roles revisados
- [ ] Exportações PDF/Excel validadas
- [ ] Auditoria de regras de agendamento (deadline / same-day) manual OK
- [ ] Aprovação de revisão de código

## 15. Próximos Passos / Gaps
| Gap | Ação Proposta | Prioridade |
|-----|---------------|-----------|
| Definir matriz detalhada de perfis/permissões | Documentar roles e mapear rotas | Alta |
| Automatizar validação multi-arch em pipeline | Adicionar job CI | Média |
| Cobertura mínima de testes | Definir threshold e report | Média |
| Checklist PR formal | Template .github | Alta |
| Indexação bookings | Criar índice (booking_date, meal_type) | Média |
| Cancelamento com cutoff | Definir regra + implementar | Alta |
| Auditoria Furriel/Sgtte | Registrar logs (created_by_furriel/operator) centralizados | Alta |
| Estado pending | Decidir remover ou implementar fluxo aprovação | Média |
| Timezone unificado | Configurar e testar America/Sao_Paulo | Alta |
| Histórico cardápio | Versionamento e auditoria | Baixa |
| Clarificar escopo furriel x jantar | Decidir política e ajustar documentação/UI | Média |
| Testes jantar + fim de semana Laranjeira | Criar casos de teste (unit + feature) | Alta |
| Atualizar dados legados subunit | Migrar valores '1', '2', '3' para '1ª Cia', '2ª Cia', 'EM' | Média |

## 15. Histórico de Evolução (Commits‑chave)
| Hash | Tema | Regra / Impacto Principal |
|------|------|---------------------------|
| a44a25b | Furriel / Same-day | Bloqueio de arranchamento no mesmo dia útil |
| c824a4c | Deadline reservas | Deadline 13h para bloquear D+1 |
| 59762a2 | Sistema furriel | Bloqueio fins de semana + sexta sem almoço + interface operacional |
| aea6282 | Cardápio | Seletor de semanas + sugestões semana anterior |
| ba372a2 | Cardápio | Acesso universal de visualização no dashboard |
| 03defef | Cardápio inicial | CRUD cardápio semanal + lógica próxima semana |
| 57fb347 | Perfil aprov | Introdução role aprov + acesso cardápio e relatórios (sem gestão usuários) |
| af8baa2 | Renomear role | Ajuste de nomenclatura aprov->manager (evolução posterior reintroduziu aprov) |
| 1cca710 | Relatórios | Exportações PDF/Excel abrangentes |
| 2eeca08 | Backup/Restore | Sistema completo backup & restore |
| 0553028 | Deploy | Estrutura profissional multi-arch |
| d2efe59 | Multi-arch merge | Consolidação final multi-ambiente |
| 199e86b | Perfil sgtte + Jantar | Introdução role sgtte, refeição jantar e auditoria operador |
| d578db5 | Serviço sgtte | Carregamento dinâmico por data, bloqueio edição passado e preservação parâmetro dia |
| 1a09082 | Documentação | Criação inicial deste documento de regras de negócio |
| 407b83f | Jantar + Status | Status Laranjeira, auto-reserva jantar, atualização cards dashboard |
| 8748979 | Relatórios jantar | Inclusão jantar nos relatórios exportados e melhorias de layout |
| 14c3597 | Correção perfil | Ajuste campo Data Pronto OM (exibição de calendário) |
| 13bb827 | IDT validação + UX + Org | Validação IDT numérica, melhorias UX calendário, padronização organizações e subunidades |
| 22c4b15 | Renomeação perfil | Alteração role 'superuser' para 'aprov' em todo o sistema |
| 33d5e26 | Usuários e badges | Separação admin/aprov, badges perfil completos, ícone chef para aprov |

Observação: reflita sempre alterações futuras com novos commits nesta tabela para rastreabilidade de decisões.

---
### Atualização
Sempre que uma nova regra de negócio for implementada ou alterada, atualizar esta lista mantendo histórico via controle de versão (diffs em PR).

---

## 13. Melhorias Recentes

**13.1. Comando Administrativo**: Criado comando `saga:ensure-admin` para configuração automatizada do usuário administrador em produção com geração dinâmica de IDT.

**13.2. Organizações Padronizadas**: Sistema possui apenas 4 organizações militares válidas: 11º Depósito de Suprimento, AC Defesa, PMB, 7º CTA. Organizações removidas por não serem relevantes para o escopo operacional.

**13.3. Subunidades do 11º Depósito**: Para usuários do 11º Depósito de Suprimento, disponibilizar opções de subunidade: 1ª Cia, 2ª Cia, EM. Padronização aplicada em formulários de cadastro e administração.

**13.4. Aprimoramentos UX**: Melhorada responsividade do calendário Data Pronto OM para abertura ao clicar no ícone. Ajustes CSS de z-index e pointer-events para comportamento mais intuitivo.

**13.5. Perfis no Profile**: Badges de role no perfil do usuário agora refletem fielmente todos os tipos de perfil (user, manager, aprov, furriel, sgtte) com cores e ícones distintos. Perfil Aprov utiliza ícone de chapéu de mestre cuca.

**13.6. Gestão de Usuários**: Criado sistema de usuários separados - admin@saga.mil.br (role: manager) para gestão administrativa e aprov@saga.mil.br (role: aprov, senha: 12345678) para gestão de cardápio.

**13.7. Permissões de Migration**: IMPORTANTE - Sempre corrigir permissões após criar migrations via Docker, pois são criadas como root. Executar: `sudo chown sonnote:sonnote /path/migration.php && sudo chmod 664 /path/migration.php`.

---

## 14. Boas Práticas de Desenvolvimento

### 14.1. Migrations e Permissões
- **Problema**: Migrations criadas via `docker exec` são criadas como usuário root
- **Solução**: Sempre executar após criação de migration:
  ```bash
  sudo chown sonnote:sonnote database/migrations/YYYY_MM_DD_*.php
  sudo chmod 664 database/migrations/YYYY_MM_DD_*.php
  sudo chown sonnote:sonnote database/migrations/  # diretório também
  sudo chmod 775 database/migrations/
  ```

### 14.2. Renomeação de Roles
- **Processo**: Ao alterar roles (ex: superuser → aprov):
  1. Criar migration para UPDATE nos registros existentes
  2. Atualizar constraint CHECK na tabela users
  3. Atualizar todos os controllers com validações
  4. Atualizar views com labels e condicionais
  5. Atualizar models com novos métodos (ex: isAprov())
  6. Testar em ambientes DEV e STAGING

### 14.3. Backup Antes de Mudanças Críticas
- Sempre executar backup antes de migrations que alteram dados:
  ```bash
  bash scripts/database/backup.sh
  ```
- Verificar integridade dos dados após mudanças críticas

### 14.4. Ambientes Separados
- **DEV**: Dados reais para desenvolvimento e testes
- **STAGING**: Dados limpos para validação de features
- Manter migrations sincronizadas entre ambientes
- Verificar constraints e foreign keys em ambos ambientes

