# Regras de Negócio do Projeto SAGA

Este documento consolida as regras de negócio já implementadas (ou em implementação avançada) no SAGA, servindo como referência única para produto, desenvolvimento, QA e operações.

> STATUS: Documento inicial (versão 0.1). Atualize a cada nova regra implementada ou alteração relevante.

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

## 2. Perfis e Acesso
Perfis atuais (campo `users.role`, constraint CHECK): `user`, `manager`, `superuser`, `furriel`, `sgtte`.

| Role | Objetivo | Principais Permissões | Restrições |
|------|----------|-----------------------|-----------|
| user | Militar padrão | Criar/visualizar/cancelar (futuro) suas reservas; ver perfil; estatísticas pessoais | Não acessa administração, relatórios, cardápio |
| furriel | Apoio operacional (lançamento para terceiros) | Criar reservas em nome de Soldados EV (campo `created_by_furriel`), gerenciar arranchamento companhia (café/almoço) | Não edita cardápio; não gerencia usuários; sem jantar |
| sgtte | Sargenteante / Serviço | Arranchar qualquer militar da própria subunidade (café/almoço/jantar) sem restrição de horário; interface Serviço com busca | Não gerencia usuários; não edita cardápio; auditoria via `created_by_operator` |
| manager | Gestão administrativa | CRUD de usuários (`AdminController@users/storeUser/updateUser/toggleUserStatus`), acesso a relatórios (`reports`, `generateReport`) | Não edita cardápio semanal (restrito a superuser) |
| superuser | Acesso ampliado / curadoria | Visualizar reservas, estatísticas, relatórios; gerenciar cardápio semanal (`CardapioController`); (NÃO gerencia usuários) | Uso restrito; número limitado de contas |

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
1. Estrutura: `user_id`, `booking_date` DATE, `meal_type` enum (breakfast|lunch|dinner), `status` enum (confirmed|cancelled|pending), `created_by_furriel` (nullable), timestamps. (Jantar introduzido para fluxo de serviço/sgtte.)
2. Restrição única `(user_id, booking_date, meal_type)` impede duplicidade por refeição/dia.
3. Tipos ativos: breakfast (Café da Manhã), lunch (Almoço) e dinner (Jantar – cadastrado via perfil `sgtte`).
4. Sexta-feira: somente breakfast (almoço indisponível) — coerente com `WeeklyMenu::getDefaultMenuStructure`; jantar (quando aplicável ao serviço) requer definição operacional se servido às sextas (REGRA A VALIDAR).
5. Dias permitidos: somente dias úteis (commit 59762a2 / sistema furriel) — finais de semana bloqueados.
6. Deadline diário (commit c824a4c): após 13h não é possível reservar para o dia SEGUINTE.
7. Bloqueio de mesmo dia útil (commit a44a25b): não é permitido criar reserva para o próprio dia (mesmo dentro do horário) — exceções operacionais devem ser avaliadas para futuro (possível override furriel?).
8. Janela futura alvo: até 30 dias corridos (regra de negócio declarada; validar enforcement atual e ajustar se necessário em Livewire/backend).
9. Estados de workflow:
  - confirmed (padrão)
  - cancelled (mantém histórico; requer endpoint/ação consistente de cancelamento) 
  - pending (placeholder para possível aprovação futura; hoje não utilizado — considerar remoção ou implementação)
10. Campo `created_by_furriel`: identifica reservas criadas por role `furriel` (ou superior) em nome do titular — base para auditoria e métricas operacionais.
11. Relatórios consolidados em AdminController (exportações PDF/Excel) — agora contemplam café, almoço e jantar.

Validações Implementadas / Esperadas:
- Datas passadas: bloqueio (frontend/backend — confirmar cobertura de testes).
- Fins de semana: bloqueio (commit 59762a2 menciona).
- Almoço nas sextas: bloqueio funcional (estrutura + validação UI; acrescentar backend se ausente).
- Mesma data (same-day): bloqueio (commit a44a25b).
- Deadline 13h para o dia seguinte: após 13:00 (timezone servidor) reservas para (D+1) são rejeitadas (commit c824a4c) — definir timezone padrão (ex: America/Sao_Paulo) para consistência.
- Janela máxima (30 dias): verificar enforcement; adicionar teste + constraint lógica se faltar.

Relatórios e Métricas:
- Estatísticas diárias/semanais/mensais (AdminController: métodos `getSummaryData`, `getOrganizationBreakdownData`, etc.).
- Exportações suportam formatos PDF e Excel (tipos: daily_meals, weekly_summary, monthly_summary, organization_breakdown, user_activity).

Riscos / Gaps:
- Índice composto sugerido: (`booking_date`, `meal_type`) para relatórios (atual ausência pode gerar scans completos).
- Política de cancelamento não definida (cutoff antes da refeição) — especificar (ex: até 18h do dia anterior).
- Estado `pending` ocioso — decidir remover ou implementar fluxo de aprovação.
- Necessário log/auditoria para ações do furriel (`created_by_furriel`).
- Timezone explícito para cálculo de 13h; considerar armazenar deadlines calculados.

## 4. Cardápio Semanal (WeeklyMenu)
Origem: Model `WeeklyMenu` e `CardapioController`.

Regras (conforme commits 57fb347, 03defef, aea6282, ba372a2):
1. Visualização: Acesso universal via dashboard para todos os roles (commit ba372a2). 
2. Edição: Exclusiva de `superuser` (rotas protegidas abort 403 para demais).
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
| users | google_id (unique), full_name, war_name, email (unique), rank_id FK, organization_id FK (pode se tornar nullable), gender (M/F no controller; migration antiga male/female – ajustar convergência), ready_at_om_date, role (CHECK), is_active | Constraint dinâmica atualizada para incluir `furriel`; divergência enum gender (corrigir para consistência) |
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
- Perfis: conferir restrição de acesso (superuser não gerencia usuários; manager não edita cardápio).
- Scripts de backup/restore executados com sucesso (dry-run + restore em staging). 

Resultado: marcação "validado" antes de criar release.

## 8. Deadlines e Janelas de Validação
Deadlines de agendamento (resumo operacional):
| Regra | Descrição | Fonte (Commit) | Observações |
|-------|-----------|----------------|-------------|
| Same-day bloqueado | Não permitir reserva para hoje | a44a25b | Exceção futura via override operacional? |
| Almoço sexta indisponível | Sexta só breakfast | Estrutura + 59762a2 | Refletir em UI e backend |
| Fim de semana bloqueado | Sáb/Dom não permitidos | 59762a2 | Validar timezone |
| Deadline 13h D+1 | Após 13:00 não agenda próximo dia útil | c824a4c | Ajustar mensagens se fuso variar |
| Janela futura 30 dias | Limitar horizonte de reservas | Regra declarada | Confirmar enforcement |

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

## 14. Próximos Passos / Gaps
| Gap | Ação Proposta | Prioridade |
|-----|---------------|-----------|
| Definir matriz detalhada de perfis/permissões | Documentar roles e mapear rotas | Alta |
| Automatizar validação multi-arch em pipeline | Adicionar job CI | Média |
| Cobertura mínima de testes | Definir threshold e report | Média |
| Checklist PR formal | Template .github | Alta |
| Indexação bookings | Criar índice (booking_date, meal_type) | Média |
| Cancelamento com cutoff | Definir regra + implementar | Alta |
| Auditoria Furriel | Registrar logs de criação third-party | Alta |
| Estado pending | Decidir remover ou implementar fluxo aprovação | Média |
| Timezone unificado | Configurar e testar America/Sao_Paulo | Alta |
| Histórico cardápio | Versionamento e auditoria | Baixa |

## 15. Histórico de Evolução (Commits‑chave)
| Hash | Tema | Regra / Impacto Principal |
|------|------|---------------------------|
| a44a25b | Furriel / Same-day | Bloqueio de arranchamento no mesmo dia útil |
| c824a4c | Deadline reservas | Deadline 13h para bloquear D+1 |
| 59762a2 | Sistema furriel | Bloqueio fins de semana + sexta sem almoço + interface operacional |
| aea6282 | Cardápio | Seletor de semanas + sugestões semana anterior |
| ba372a2 | Cardápio | Acesso universal de visualização no dashboard |
| 03defef | Cardápio inicial | CRUD cardápio semanal + lógica próxima semana |
| 57fb347 | Perfil superuser | Introdução role superuser + acesso cardápio e relatórios (sem gestão usuários) |
| af8baa2 | Renomear role | Ajuste de nomenclatura superuser->manager (evolução posterior reintroduziu superuser) |
| 1cca710 | Relatórios | Exportações PDF/Excel abrangentes |
| 2eeca08 | Backup/Restore | Sistema completo backup & restore |
| 0553028 | Deploy | Estrutura profissional multi-arch |
| d2efe59 | Multi-arch merge | Consolidação final multi-ambiente |

Observação: reflita sempre alterações futuras com novos commits nesta tabela para rastreabilidade de decisões.

---
### Atualização
Sempre que uma nova regra de negócio for implementada ou alterada, atualizar esta lista mantendo histórico via controle de versão (diffs em PR).

