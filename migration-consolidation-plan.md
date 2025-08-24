# Plano de Consolidação de Migrations - SAGA

## Análise Atual (25 migrations)

### Problemas Identificados:

1. **Migration Duplicada Vazia**: `2025_08_22_155946_rename_superuser_role_to_aprov.php` está vazia
2. **Múltiplas Alterações de Role Constraint**: 6 migrations diferentes alterando a mesma constraint
3. **Divisão Desnecessária**: Campo IDT dividido em 2 migrations quando poderia ser 1

### Migrations Que Podem Ser Consolidadas:

#### Grupo 1: Role Constraints (6 migrations → 1)
- `2025_08_03_184142_simple_update_role_to_manager.php`
- `2025_08_03_192650_add_superuser_role_to_users_table.php`
- `2025_08_15_224916_update_users_role_constraint_add_furriel.php`
- `2025_08_19_000001_update_users_role_constraint_add_sgtte.php`
- `2025_08_22_155958_rename_superuser_role_to_aprov.php`

#### Grupo 2: Campo IDT (2 migrations → 1)
- `2025_08_21_120000_add_idt_to_users_table.php`
- `2025_08_21_130000_make_idt_unique_not_nullable_on_users_table.php`

#### Grupo 3: Migration Vazia (remover)
- `2025_08_22_155946_rename_superuser_role_to_aprov.php`

## Plano de Consolidação

### Etapa 1: Backup e Verificação
- [x] Backup do banco de dados atual
- [x] Verificação do estado atual das migrations

### Etapa 2: Criar Migrations Consolidadas

#### 2.1 Nova Migration: Role System Consolidation
**Nome**: `2025_08_03_000000_consolidate_role_system.php`
**Função**: Consolidar todas as alterações de role em uma única migration
**Benefícios**: 
- Remove 5 migrations redundantes
- Lógica mais clara e linear
- Easier rollback

#### 2.2 Nova Migration: IDT Field Complete
**Nome**: `2025_08_21_000000_add_idt_field_complete.php`
**Função**: Adicionar campo IDT com todas as constraints em uma única operação
**Benefícios**:
- Remove 1 migration redundante
- Operação atômica

### Etapa 3: Procedimento de Migração
1. Criar ambiente de teste
2. Fazer rollback das migrations consolidáveis
3. Aplicar novas migrations consolidadas
4. Verificar integridade dos dados
5. Aplicar em produção

## Impacto da Consolidação

### Antes: 25 migrations
### Depois: 19 migrations (-6 migrations)

### Benefícios:
- ✅ Redução de 24% no número de migrations
- ✅ Lógica mais clara e organizada
- ✅ Rollbacks mais seguros
- ✅ Facilita setup em novos ambientes
- ✅ Remove migration vazia

### Riscos:
- ⚠️ Precisa rollback em produção (se aplicável)
- ⚠️ Requer testes extensivos
- ⚠️ Pode afetar deploys em andamento

## Status: RECOMENDAÇÃO

**Recomendo PROCEDER com a consolidação** pelas seguintes razões:

1. **Múltiplas alterações na mesma constraint**: 6 migrations alterando `users_role_check` é excessivo
2. **Migration vazia**: Não agrega valor e polui o histórico
3. **Melhor manutenibilidade**: Código mais limpo para novos desenvolvedores
4. **Ambiente controlado**: Sistema ainda em desenvolvimento, riscos menores

## Próximos Passos Sugeridos:

1. Verificar se existem outros ambientes (staging/produção) que precisam ser considerados
2. Criar as novas migrations consolidadas
3. Testar em ambiente isolado
4. Aplicar a consolidação
